<script setup>
import { ref, computed } from "vue";
import { useForm } from "@inertiajs/vue3";
import FormModal from "@/Components/FormModal.vue";
import FormItem from "@/Components/FormItem.vue";
import axios from "axios";
import { confirm_delete } from "../../../../Utils/confirmation_modal.js";
import dayjs from "dayjs";
import { message } from "ant-design-vue";

const props = defineProps({
    modelClass: String,
    requiredModels: Array,
});

const open = ref(false);
const loading = ref(false);
const entity = ref(null);
const existingDocuments = ref([]);

const form = useForm({
    documentable_type: "",
    documentable_id: null,
    document_type_id: null,
    fichier: null,
    date_expiration: null,
    observation: "",
    type: "fichier",
    parent_id: null,
});

const detailConfig = ref({
    open: false,
    model: null,
    documents: [],
});

const fileList = ref([]);

const openModal = async (record) => {
    entity.value = record;
    form.documentable_type = props.modelClass;
    form.documentable_id = record.id;
    await fetchDocuments();
    open.value = true;
};

const fetchDocuments = async () => {
    if (!entity.value) return;
    loading.value = true;
    try {
        const response = await axios.get(route('dynamic.documents.entity.documents'), {
            params: {
                model_class: props.modelClass,
                model_id: entity.value.id
            }
        });
        existingDocuments.value = response.data;

        // Si la modale de détail est ouverte, on actualise sa liste
        if (detailConfig.value.open && detailConfig.value.model) {
            detailConfig.value.documents = getDocsForType(detailConfig.value.model.document_type_id);
        }
    } catch (error) {
        console.error("Error fetching documents", error);
    } finally {
        loading.value = false;
    }
};

const handleManageClick = (model) => {
    detailConfig.value.model = model;
    detailConfig.value.documents = getDocsForType(model.document_type_id);

    // Reset form pour le nouvel upload
    form.document_type_id = model.document_type_id;
    form.date_expiration = null;
    form.observation = model.document_type.nom; // Remplissage automatique avec le nom du type (ex: CIN)
    form.fichier = null;
    fileList.value = [];

    detailConfig.value.open = true;
};

const submit = () => {
    form.post(route('dynamic.documents.upload'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset('fichier', 'date_expiration', 'observation');
            fileList.value = [];
            fetchDocuments();
        },
    });
};

const handleDelete = (doc) => {
    confirm_delete(() => {
        axios.delete(route('dynamic.documents.delete', { document: doc.id }))
            .then((response) => {
                message.success("Document supprimé avec succès");
                fetchDocuments();
            })
            .catch(err => {
                message.error("Erreur lors de la suppression");
                console.error("Erreur lors de la suppression", err);
            });
    });
};

const getDocsForType = (typeId) => {
    return existingDocuments.value
        .filter(d => d.document_type_id === typeId)
        .sort((a, b) => dayjs(b.created_at).unix() - dayjs(a.created_at).unix());
};

const getStatus = (model) => {
    const docs = getDocsForType(model.document_type_id);
    if (docs.length === 0) return { label: 'Manquant', color: 'default', icon: 'fa-circle-xmark' };

    const latest = docs[0];
    if (model.expiration_required && latest.date_expiration) {
        const exp = dayjs(latest.date_expiration);
        const today = dayjs();
        const alertDate = exp.subtract(model.alert_days, 'day');

        if (today.isAfter(exp)) return { label: 'Expiré', color: 'error', icon: 'fa-triangle-exclamation' };
        if (today.isAfter(alertDate)) return { label: 'Expire bientôt', color: 'warning', icon: 'fa-clock' };
    }

    return { label: 'Valide', color: 'success', icon: 'fa-circle-check' };
};

defineExpose({ openModal });
</script>

<template>
    <FormModal
        v-model:open="open"
        :titre="`Dossier Documentaire : ${entity?.nom || entity?.immatriculation || entity?.numero_remorque || ''}`"
        size="lg"
        @close="open = false"
        :show-footer="false"
    >
        <div class="py-2">
            <div class="grid grid-cols-1 gap-4">
                <div
                    v-for="model in requiredModels"
                    :key="model.id"
                    class="flex items-center justify-between p-4 bg-white border rounded-xl hover:shadow-md transition-shadow duration-300"
                >
                    <div class="flex items-center space-x-4">
                        <div
                            class="w-12 h-12 rounded-full flex items-center justify-center"
                            :class="{
                                'bg-green-50 text-green-600': getStatus(model).color === 'success',
                                'bg-red-50 text-red-600': getStatus(model).color === 'error',
                                'bg-orange-50 text-orange-600': getStatus(model).color === 'warning',
                                'bg-gray-50 text-gray-400': getStatus(model).color === 'default',
                            }"
                        >
                            <font-awesome-icon :icon="getStatus(model).icon" class="text-xl" />
                        </div>
                        <div>
                            <div class="font-bold text-gray-800 flex items-center">
                                {{ model.document_type.nom }}
                                <span v-if="model.obligatoire" class="text-red-500 ml-1" title="Obligatoire">*</span>
                            </div>
                            <div class="flex items-center mt-1">
                                <a-tag :color="getStatus(model).color" class="rounded-full px-3 text-[10px] uppercase font-bold border-none">
                                    {{ getStatus(model).label }}
                                </a-tag>
                                <span v-if="getDocsForType(model.document_type_id)[0]?.date_expiration" class="text-xs text-gray-500 ml-3">
                                    <font-awesome-icon icon="fa-solid fa-calendar-alt" class="mr-1 opacity-60" />
                                    Exp : {{ dayjs(getDocsForType(model.document_type_id)[0].date_expiration).format('DD/MM/YYYY') }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <a-button type="primary" ghost class="rounded-lg border-primary/30 hover:bg-primary/5" @click="handleManageClick(model)">
                        <font-awesome-icon icon="fa-solid fa-folder-open" class="mr-2" />
                        Gérer
                    </a-button>
                </div>
            </div>
        </div>

        <!-- Deuxième Modale : Gestion détaillée d'un type de document -->
        <a-modal
            v-model:open="detailConfig.open"
            :title="`Gestion : ${detailConfig.model?.document_type.nom}`"
            width="800px"
            :footer="null"
            centered
        >
            <div class="space-y-6 pt-4">
                <!-- Section Upload / Renouvellement -->
                <div class="bg-gray-50 p-5 rounded-xl border border-gray-100">
                    <h4 class="text-sm font-bold text-gray-700 mb-4 flex items-center">
                        <font-awesome-icon icon="fa-solid fa-cloud-upload" class="mr-2 text-primary" />
                        {{ detailConfig.documents.length > 0 ? 'Renouveler ou Ajouter une version' : 'Ajouter le document' }}
                    </h4>

                    <a-form layout="vertical">
                        <div class="grid grid-cols-1 gap-2">
                            <form-item label="Libellé / Note" :help="form.errors.observation">
                                <a-input v-model:value="form.observation" disabled placeholder="Ex: Version 2024, Copie certifiée..." />
                            </form-item>

                            <form-item v-if="detailConfig.model?.expiration_required" label="Date d'expiration" :help="form.errors.date_expiration">
                                <a-date-picker v-model:value="form.date_expiration" class="w-full" value-format="YYYY-MM-DD" format="DD/MM/YYYY" />
                            </form-item>
                        </div>

                        <div class="mt-4 flex items-end space-x-4">
                            <div class="flex-1">
                                <a-upload
                                    v-model:file-list="fileList"
                                    :before-upload="file => {
                                        form.fichier = file;
                                        fileList = [file];
                                        return false;
                                    }"
                                    :max-count="1"
                                    @remove="form.fichier = null"
                                >
                                    <a-button block class="h-10 border-dashed">
                                        <font-awesome-icon icon="fa-solid fa-paperclip" class="mr-2" />
                                        {{ form.fichier ? 'Fichier sélectionné' : 'Choisir le fichier' }}
                                    </a-button>
                                </a-upload>
                            </div>
                            <a-button type="primary" class="h-10 px-8" :loading="form.processing" @click="submit" :disabled="!form.fichier">
                                Enregistrer
                            </a-button>
                        </div>
                        <div v-if="form.errors.fichier" class="text-red-500 text-xs mt-1">{{ form.errors.fichier }}</div>
                    </a-form>
                </div>

                <!-- Section Historique -->
                <div>
                    <h4 class="text-sm font-bold text-gray-700 mb-3 flex items-center px-1">
                        <font-awesome-icon icon="fa-solid fa-history" class="mr-2 text-gray-400" />
                        Historique des fichiers
                    </h4>

                    <div class="space-y-2 max-h-[300px] overflow-y-auto pr-1">
                        <div
                            v-for="(doc, index) in detailConfig.documents"
                            :key="doc.id"
                            class="flex items-center justify-between p-3 bg-white border rounded-lg hover:border-primary/30 transition-colors"
                            :class="{'border-l-4 border-l-green-500': index === 0 && dayjs(doc.date_expiration).isAfter(dayjs())}"
                        >
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 rounded bg-blue-50 text-blue-500 flex items-center justify-center">
                                    <font-awesome-icon icon="fa-solid fa-file-pdf" v-if="doc.fichier?.endsWith('.pdf')" />
                                    <font-awesome-icon icon="fa-solid fa-file-image" v-else />
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-gray-800">
                                        {{ doc.observation || 'Sans libellé' }}
                                        <a-tag v-if="index === 0" color="blue" class="ml-2 scale-75 origin-left">Actuel</a-tag>
                                    </div>
                                    <div class="text-[11px] text-gray-500 flex space-x-3">
                                        <span v-if="doc.date_expiration">Expire le : {{ dayjs(doc.date_expiration).format('DD/MM/YYYY') }}</span>
                                        <span>Ajouté le : {{ dayjs(doc.created_at).format('DD/MM/YYYY') }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center space-x-1">
                                <a :href="`/storage/${doc.fichier}`" target="_blank" class="w-8 h-8 flex items-center justify-center rounded-md hover:bg-gray-100 text-gray-600 transition-colors" title="Voir">
                                    <font-awesome-icon icon="fa-solid fa-external-link" />
                                </a>
                                <button @click="handleDelete(doc)" class="w-8 h-8 flex items-center justify-center rounded-md hover:bg-red-50 text-red-500 transition-colors" title="Supprimer">
                                    <font-awesome-icon icon="fa-solid fa-trash-can" />
                                </button>
                            </div>
                        </div>

                        <div v-if="detailConfig.documents.length === 0" class="text-center py-8 text-gray-400 italic text-sm border-2 border-dashed rounded-xl">
                            Aucun historique disponible
                        </div>
                    </div>
                </div>
            </div>
        </a-modal>
    </FormModal>
</template>

<style scoped>
:deep(.ant-modal-content) {
    @apply rounded-2xl overflow-hidden;
}
:deep(.ant-modal-header) {
    @apply border-b-0 pb-0;
}
</style>

