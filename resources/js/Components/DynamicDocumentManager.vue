<script setup>
import { ref, onMounted } from 'vue';
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import axios from 'axios';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
    modelClass: { type: String, required: true },
    modelId: { type: [Number, String], required: true },
});

const loading = ref(true);
const requiredDocs = ref([]);
const existingDocs = ref({});
const showUploadModal = ref(false);
const selectedDocType = ref(null);
const fileList = ref([]);

const uploadForm = useForm({
    documentable_type: props.modelClass,
    documentable_id: props.modelId,
    document_type_id: null,
    fichier: null,
    date_expiration: null,
    observation: null,
});

const fetchData = async () => {
    loading.value = true;
    try {
        const response = await axios.get(route('dynamic.documents.required'), {
            params: {
                model_class: props.modelClass,
                model_id: props.modelId
            }
        });
        requiredDocs.value = response.data.required;
        existingDocs.value = response.data.existing;
    } catch (error) {
        console.error("Erreur lors de la récupération des documents", error);
    } finally {
        loading.value = false;
    }
};

const openUpload = (docModel) => {
    selectedDocType.value = docModel.document_type;
    uploadForm.document_type_id = docModel.document_type_id;
    uploadForm.date_expiration = null;
    uploadForm.observation = null;
    uploadForm.fichier = null;
    fileList.value = [];
    showUploadModal.value = true;
};

const beforeUpload = (file) => {
    fileList.value = [file];
    uploadForm.fichier = file;
    return false;
};

const handleRemove = () => {
    fileList.value = [];
    uploadForm.fichier = null;
};

const handleUpload = () => {
    uploadForm.post(route('dynamic.documents.upload'), {
        onSuccess: () => {
            showUploadModal.value = false;
            fetchData();
        },
        forceFormData: true,
    });
};

onMounted(fetchData);

const getStatus = (docTypeId) => {
    const docs = existingDocs.value[docTypeId];
    if (docs && docs.length > 0) return 'uploaded';
    return 'missing';
};

</script>

<template>
    <div class="mt-4">
        <a-skeleton :loading="loading" active>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div v-for="docModel in requiredDocs" :key="docModel.id"
                    class="border rounded-lg p-4 flex flex-col justify-between hover:shadow-md transition-shadow"
                    :class="getStatus(docModel.document_type_id) === 'uploaded' ? 'border-green-200 bg-green-50' : 'border-gray-200 bg-white'">

                    <div class="flex justify-between items-start mb-2">
                        <div>
                            <h4 class="font-semibold text-gray-800">{{ docModel.document_type.nom }}</h4>
                            <p class="text-xs text-gray-500" v-if="docModel.obligatoire">Obligatoire</p>
                        </div>
                        <a-tag :color="getStatus(docModel.document_type_id) === 'uploaded' ? 'success' : 'warning'">
                            {{ getStatus(docModel.document_type_id) === 'uploaded' ? 'Présent' : 'Manquant' }}
                        </a-tag>
                    </div>

                    <div v-if="existingDocs[docModel.document_type_id]" class="mb-3">
                        <div v-for="doc in existingDocs[docModel.document_type_id]" :key="doc.id" class="text-sm py-1 flex items-center justify-between border-b border-green-100 last:border-0">
                            <span class="truncate max-w-[150px]">
                                <FontAwesomeIcon icon="fa-solid fa-file" class="mr-2 text-green-600" />
                                <a :href="'/' + doc.fichier" target="_blank" class="text-blue-600 hover:underline">Voir le fichier</a>
                            </span>
                            <span v-if="doc.date_expiration" class="text-xs text-gray-500">
                                Exp: {{ doc.date_expiration }}
                            </span>
                        </div>
                    </div>

                    <a-button type="primary" size="small" @click="openUpload(docModel)" ghost block class="mt-2">
                        <template #icon><FontAwesomeIcon icon="fa-solid fa-upload" class="mr-2" /></template>
                        {{ getStatus(docModel.document_type_id) === 'uploaded' ? 'Remplacer' : 'Uploader' }}
                    </a-button>
                </div>
            </div>
        </a-skeleton>

        <a-modal cancel-Text="Fermer" v-model:open="showUploadModal" :title="'Uploader : ' + (selectedDocType?.nom || '')" @ok="handleUpload" :confirmLoading="uploadForm.processing">
            <a-form layout="vertical">
                <a-form-item label="Fichier" required :help="uploadForm.errors.fichier" :validateStatus="uploadForm.errors.fichier ? 'error' : ''">
                    <a-upload
                        :file-list="fileList"
                        :before-upload="beforeUpload"
                        @remove="handleRemove"
                        :max-count="1"
                    >
                        <a-button block>
                            <template #icon><FontAwesomeIcon icon="fa-solid fa-cloud-upload-alt" class="mr-2" /></template>
                            Sélectionner le document
                        </a-button>
                    </a-upload>
                </a-form-item>

                <a-form-item label="Date d'expiration" v-if="requiredDocs.find(d => d.document_type_id === uploadForm.document_type_id)?.expiration_required">
                    <a-date-picker
                        v-model:value="uploadForm.date_expiration"
                        class="w-full"
                        value-format="YYYY-MM-DD"
                        format="DD/MM/YYYY"
                        placeholder="Sélectionner la date d'expiration"
                    />
                </a-form-item>

                <a-form-item label="Observation">
                    <a-textarea v-model:value="uploadForm.observation" :rows="3" placeholder="Notes éventuelles..." />
                </a-form-item>
            </a-form>
        </a-modal>
    </div>
</template>

