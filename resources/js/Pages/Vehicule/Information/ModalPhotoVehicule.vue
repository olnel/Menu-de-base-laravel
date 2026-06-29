<script setup>
import {ref, watch} from "vue";
import FormItem from "@/Components/FormItem.vue";
import {PictureOutlined, DeleteOutlined} from "@ant-design/icons-vue";
import UploadMultipleComponent from "@/Components/UploadFile/UploadMultipleComponent.vue";
import {message} from "ant-design-vue";
import {router} from "@inertiajs/vue3";
import {confirm_delete} from "../../../../Utils/confirmation_modal.js";
import FormModal from "@/Components/FormModal.vue";

const TYPE = [
    {value: 'Photo origine', label: 'Photo origine'},
    {value: 'Constat', label: 'Constat'},
    {value: 'Controle Technique', label: 'Controle Technique'},
    {value: 'Entretien', label: 'Entretien'},
    {value: 'Sinistre', label: 'Sinistre'},
];

const uploadKey = ref(0)

const props = defineProps({
    vehicule_id: {
        type: String,
        required: true,
    },
    vehiculePhotos: {
        type: Object,
        required: false,
        default: () => ({})
    },
    errors: Object
});

const localPhotoVehicule = ref({...props.vehiculePhotos});
const dateFormat = 'YYYY/MM/DD';
const showModal = ref(false);
const titre = ref();
const localVehicule_id = ref(null);
const element = ref();
const openModal = (vehicule_id) => {
    showModal.value = true;
    uploadKey.value++; // Force le re-rendu du composant Upload
    titre.value = "Nouveau Images";
    localVehicule_id.value = vehicule_id;
    localPhotoVehicule.value = {}; // Réinitialiser les données
};

const close = () => {
    showModal.value = false;
    localPhotoVehicule.value = {}; // Réinitialiser les données
    uploadKey.value++; // Force le re-rendu du composant Upload
};

const update = (rowData) => {
    router.visit(route('vehiculephoto.show', {vehiculephoto: rowData.id}), {
        preserveState: true,
        preserveScroll: true,
        onSuccess: (reponse) => {
            // console.log(reponse.props.flash.data);
            element.value = {...reponse.props.flash.data};
            console.log(element.value)
            showModal.value = true;
            localVehicule_id.value = rowData.vehicule_id;
            uploadKey.value++; // Force le re-rendu du composant Upload
        }
    });
}

const mapExistingImages = (listeImage) => {
    if (!listeImage || !Array.isArray(listeImage)) return [];
    return listeImage.map((img, index) => ({
        uid: `existing-${index}`,
        name: img.name || `image-${index}.jpg`,
        status: 'done',
        url: img.main?.startsWith('/') ? img.main : `/${img.main}`,
        isExisting: true
    }));
};

const savePhoto = () => {
    const photo = localPhotoVehicule.value;
    if (!photo) return;

    photo.loading = true;
    const formData = new FormData();

    formData.append('vehicule_id', localVehicule_id.value);
    formData.append('date_prise_photo', photo.date_prise_photo || '');
    formData.append('type_element', photo.type_element || '');
    formData.append('etat_vehicule', photo.etat_vehicule || '');

    if (photo.liste_image?.length) {
        photo.liste_image.forEach((item) => {
            if (item instanceof File) {
                formData.append('liste_image[]', item);
            } else if (item.main) {
                formData.append('existing_images[]', item.main);
            }
        });
    }

    const url = photo.id
        ? route('vehiculephoto.update', photo.id)
        : route('vehiculephoto.store');

    if (photo.id) {
        formData.append('_method', 'PUT');
    }

    router.post(url, formData, {
        onSuccess: (respons) => {
            console.log(respons);
            close();
        },
        onError: (reponse) => {
            console.log(reponse);
        },
        forceFormData: true,

    });
};

const removePhoto = async () => {
    const photo = localPhotoVehicule.value;
    if (!photo) return;

    if (photo.id) {
        confirm_delete(() => {
            router.delete(route('vehiculephoto.destroy', photo.id), {
                preserveScroll: true,
                onSuccess: () => {
                    localPhotoVehicule.value = {};
                    message.success('Photo supprimée');
                },
                onError: () => message.error('Erreur lors de la suppression')
            });
        });
    } else {
        localPhotoVehicule.value = {};
    }
};

const handleFilesUpdate = ({existing = [], newFiles = []} = {}) => {
    if (!localPhotoVehicule.value) return;

    localPhotoVehicule.value.liste_image = [
        ...existing.filter(img => img.isExisting).map(img => ({main: img.url})),
        ...newFiles
    ];
};

watch(() => element.value, (newVal) => {
    localPhotoVehicule.value = null;
    localPhotoVehicule.value = {...newVal};
}, {deep: true});

defineExpose({openModal, update});
</script>

<template>
    <FormModal
        v-model:open="showModal"
        :titre="titre"
        @close="close"
        @submit="savePhoto"
        size="md"
        :show_champ_obligatoir="false"
    >
        <div class="space-y-6 mt-4">
            <div class="flex items-center space-x-3 mb-6 pb-3 border-b border-gray-100">
                <font-awesome-icon :icon="['fas', 'edit']" class="text-primary text-lg" />
                <h4 class="text-lg font-semibold text-gray-800">Informations de la photo</h4>
            </div>
            <a-row :gutter="[16, 0]">
                <a-col :xs="24" :lg="12">
                    <FormItem required label="Date Prise Photo">
                        <a-date-picker
                            v-model:value="localPhotoVehicule.date_prise_photo"
                            :format="dateFormat"
                            size="large"
                            class="w-full"
                            :value-format="'DD-MM-YYYY'"
                        />
                    </FormItem>
                </a-col>

                <a-col :xs="24" :lg="12">
                    <FormItem required label="Type">
                        <a-select
                            v-model:value="localPhotoVehicule.type_element"
                            placeholder="Type de photo"
                            class="w-full"
                            size="large"
                        >
                            <a-select-option
                                v-for="type in TYPE"
                                :key="type.value"
                                :value="type.value"
                            >
                                {{ type.label }}
                            </a-select-option>
                        </a-select>
                    </FormItem>
                </a-col>

                <a-col :xs="24">
                    <FormItem required label="Etat Véhicule" class="mb-4">
                        <a-textarea
                            v-model:value="localPhotoVehicule.etat_vehicule"
                            placeholder="Description de l'état"
                            :auto-size="{ minRows: 3, maxRows: 5 }"
                        />
                    </FormItem>
                </a-col>

                <a-col :xs="24">
                    <div class="flex items-center space-x-3 mb-4">
                        <font-awesome-icon :icon="['fas', 'images']" class="text-primary text-lg" />
                        <h4 class="text-lg font-semibold text-gray-800">Galerie d'Images</h4>
                        <span class="text-red-500 font-medium">*</span>
                    </div>

                    <div class="bg-gray-50 border-2 border-dashed border-gray-300 rounded-lg p-6 hover:border-sky-500 hover:bg-sky-50 transition-all duration-300">
                        <UploadMultipleComponent
                            :initial-files="mapExistingImages(localPhotoVehicule.liste_image)"
                            @updateFiles="handleFilesUpdate"
                            :key="uploadKey"
                            class="h-48 overflow-y-auto"
                        >
                            <template #tips>
                                <div class="mt-4 p-3 bg-primary/5 rounded-lg border border-blue-200">
                                    <p class="text-xs text-primary font-medium">
                                        <font-awesome-icon icon="fa-solid fa-info-circle" class="mr-2" />
                                        Formats acceptés : JPG, PNG <span class="ml-1 font-semibold">. Max: 5MB</span>
                                    </p>
                                </div>
                            </template>
                        </UploadMultipleComponent>
                    </div>
                </a-col>
            </a-row>
        </div>
    </FormModal>

</template>

<style scoped>
.ant-upload-hint {
    font-size: 12px;
    color: #888;
}
</style>
