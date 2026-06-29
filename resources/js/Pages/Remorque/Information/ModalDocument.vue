<script setup>
import {nextTick, ref, watch} from "vue";
import FormItem from "@/Components/FormItem.vue";
import {DeleteOutlined, PictureOutlined, SaveOutlined} from "@ant-design/icons-vue";
import UploadMultipleFileAndImage from "@/Components/UploadFile/UploadMultipleFileAndImage.vue";
import {message} from "ant-design-vue";
import {router} from "@inertiajs/vue3";
import {confirm_delete} from "../../../../Utils/confirmation_modal.js";
import FormModal from "@/Components/FormModal.vue";

const props = defineProps({
    remorque_id: {
        type: String,
        required: true,
    },
    vehiculeDocuments: {
        type: Object,
        required: false,
        default: () => ({})
    },
    errors: Object
});

const uploadKey = ref(0);
const showModal = ref(false);
const titre = ref();
const localremorque_id = ref(null);
const element = ref();
const localDocument = ref({...props.vehiculeDocuments});
const dateFormat = 'YYYY/MM/DD';

const openModal = (remorque_id, isNew = true) => {
    showModal.value = true;
    uploadKey.value++;
    titre.value = isNew ? "Nouveau Document" : "Modifier Document";
    localremorque_id.value = remorque_id;
    if (isNew) {
        localDocument.value = {
            remorque_id: remorque_id,
            nom_document: null,
            description: null,
            date_expiration: null,
            fichier_jointe: []
        };
    }
};

const update = (rowData) => {
    console.log(rowData.remorque_id)
    router.visit(route('remorquedocument.show', {remorquedocument: rowData.id}), {
        preserveState: true,
        preserveScroll: true,
        onSuccess: (reponse) => {
            // Réinitialisation avant la mise à jour
            localDocument.value = {};
            uploadKey.value++;

            // Mise à jour des données
            element.value = {...reponse.props.flash.data};
            showModal.value = true;
            localremorque_id.value = rowData.remorque_id;

            // Forcer le recalcul en incrémentant uploadKey après la mise à jour
            nextTick(() => uploadKey.value++);
        }
    });
};

const close = () => {
    showModal.value = false;
    localDocument.value = {};
    uploadKey.value++;
};

const mapExistingFiles = (listeFichiers) => {
    if (!listeFichiers) return [];

    return listeFichiers.map(item => {
        const path = item.src || item.path || item.url || item;
        if (!path) return null;

        return {
            uid: `existing-${Date.now()}-${Math.random().toString(36).substr(2, 9)}`,
            name: item.name || (typeof path === 'string' ? path.split('/').pop() : 'file'),
            status: 'done',
            url: typeof path === 'string' ? (path.startsWith('http') || path.startsWith('/') ? path : `/${path}`) : '',
            isExisting: true
        };
    }).filter(Boolean);
};

const handleFilesUpdate = ({existing = [], newFiles = []} = {}) => {
    localDocument.value.fichier_jointe = [
        ...existing.filter(f => f.isExisting).map(f => ({src: f.url})),
        ...newFiles
    ];
};

const removeDocument = async () => {
    if (!localDocument.value) return;

    if (localDocument.value.id) {
        confirm_delete(() => {
            router.delete(route('remorquedocument.destroy', localDocument.value.id), {
                preserveScroll: true,
                onSuccess: () => {
                    localDocument.value = {};
                    message.success('Document supprimé');
                    close();
                },
                onError: () => message.error('Erreur lors de la suppression')
            });
        });
    } else {
        localDocument.value = {};
        close();
    }
};

const saveDocument = () => {
    const document = localDocument.value;
    if (!document) return;

    document.loading = true;
    const formData = new FormData();

    formData.append('remorque_id', localremorque_id.value);
    formData.append('nom_document', document.nom_document || '');
    formData.append('description', document.description || '');
    formData.append('date_expiration', document.date_expiration || '');

    if (document.fichier_jointe?.length) {
        document.fichier_jointe.forEach((item, index) => {
            if (item instanceof File) {
                formData.append(`fichier_jointe[${index}]`, item);
            } else if (item.src || item.url || item.path) {
                formData.append('existing_files[]', item.src || item.url || item.path);
            }
        });
    }

    const url = document.id
        ? route('remorquedocument.update', document.id)
        : route('remorquedocument.store');

    if (document.id) {
        formData.append('_method', 'PUT');
    }

    router.post(url, formData, {
        onSuccess: () => {
            // message.success('Document enregistré');
            close();
        },
        onError: (errors) => {
            message.error('Erreur lors de l\'enregistrement');
            console.error(errors);
        },
        forceFormData: true,
    });
};

watch(() => element.value, (newVal) => {
    localDocument.value = {...newVal};
    // Appel explicite à handleFilesUpdate avec les fichiers existants
    if (newVal.fichier_jointe) {
        handleFilesUpdate({
            existing: mapExistingFiles(newVal.fichier_jointe),
            newFiles: []
        });
    }
}, {deep: true});

defineExpose({openModal, update});
</script>

<template>
    <FormModal
        v-model:open="showModal"
        :titre="titre"
        @close="close"
        @submit="saveDocument"
        size="md"
        :show_champ_obligatoir="false"

    >
        <div class="space-y-6 mt-4">
            <div class="flex items-center space-x-3 mb-6 pb-3 border-b border-gray-100">
                <font-awesome-icon :icon="['fas', 'edit']" class="text-primary text-lg"/>
                <h4 class="text-lg font-semibold text-gray-800">Informations de la document</h4>
            </div>
            <a-row :gutter="[16, 0]">
                <a-col :xs="24" :lg="12">
                    <FormItem required label="Nom du Document">
                        <a-input
                            v-model:value="localDocument.nom_document"
                            placeholder="Nom du document"
                            size="large"
                        />
                    </FormItem>
                </a-col>

                <a-col :xs="24" :lg="12">
                    <FormItem label="Date d'expiration">
                        <a-date-picker
                            v-model:value="localDocument.date_expiration"
                            :format="dateFormat"
                            size="large"
                            class="w-full"
                            :value-format="'DD-MM-YYYY'"
                        />
                    </FormItem>
                </a-col>

                <a-col :xs="24">
                    <FormItem label="Description">
                        <a-textarea
                            v-model:value="localDocument.description"
                            placeholder="Description du document"
                            :auto-size="{ minRows: 3, maxRows: 5 }"
                        />
                    </FormItem>
                </a-col>

                <a-col :xs="24">
                    <div class="flex items-center space-x-3 mb-4">
                        <font-awesome-icon :icon="['fas', 'file']" class="text-primary text-lg"/>
                        <h4 class="text-lg font-semibold text-gray-800">Fichiers joints</h4>
                        <span class="text-red-500 font-medium">*</span>
                    </div>

                    <div
                        class="bg-gray-50 border-2 border-dashed border-gray-300 rounded-lg p-6 hover:border-sky-500 hover:bg-sky-50 transition-all duration-300">
                        <UploadMultipleFileAndImage
                            :initial-files="mapExistingFiles(localDocument.fichier_jointe)"
                            @updateFiles="handleFilesUpdate"
                            :key="uploadKey"
                            accept="image/*,.pdf,.doc,.docx,.xls,.xlsx"
                            class="h-48 overflow-y-auto"
                        >
                            <template #tips>
                                <div class="mt-4 p-3 bg-primary/5 rounded-lg border border-blue-200">
                                    <p class="text-xs text-primary font-medium">
                                        <font-awesome-icon icon="fa-solid fa-info-circle" class="mr-2"/>
                                        Formats acceptés: Images, PDF, Word, Excel <span class="ml-1 font-semibold">. (max 5MB)</span>
                                    </p>
                                </div>
                            </template>
                        </UploadMultipleFileAndImage>
                    </div>
                </a-col>
            </a-row>
        </div>

        <!--        <div class="space-y-6">-->
        <!--            -->

        <!--                <UploadMultipleFileAndImage-->
        <!--                    :initial-files="mapExistingFiles(localDocument.fichier_jointe)"-->
        <!--                    @updateFiles="handleFilesUpdate"-->
        <!--                    :key="uploadKey"-->
        <!--                    accept="image/*,.pdf,.doc,.docx,.xls,.xlsx"-->
        <!--                >-->
        <!--                    <template #tips>-->
        <!--                        <p class="text-xs text-gray-500 mt-2">-->
        <!--                            Formats acceptés: Images, PDF, Word, Excel (max 5MB)-->
        <!--                        </p>-->
        <!--                    </template>-->
        <!--                </UploadMultipleFileAndImage>-->
        <!--            </div>-->

        <!--        </div>-->
    </FormModal>
</template>

<style scoped>
.ant-upload-hint {
    font-size: 12px;
    color: #888;
}
</style>
