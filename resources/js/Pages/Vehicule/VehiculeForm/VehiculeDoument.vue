<script setup>
import { ref, watch } from "vue";
import ButtonIcon from "@/Components/ButtonIcon.vue";
import FormItem from "@/Components/FormItem.vue";
import { DeleteOutlined, PictureOutlined, SaveOutlined, EyeOutlined, DownloadOutlined } from "@ant-design/icons-vue";
import UploadMultipleFileAndImage from "@/Components/UploadFile/UploadMultipleFileAndImage.vue";
import { message } from "ant-design-vue";
import { router } from "@inertiajs/vue3";
import {confirm_delete} from "../../../../Utils/confirmation_modal.js";

const props = defineProps({
    vehicule_id: {
        type: String,
        required: true,
    },
    documents: {
        type: Array,
        required: false,
        default: () => [],
    }
});

const localDocuments = ref([...props.documents]);
const dateFormat = 'YYYY/MM/DD';

watch(() => props.documents, (newVal) => {
    localDocuments.value = [...newVal];
}, { deep: true });

const newPJ = () => {
    /*localDocuments.value.push({
        vehicule_id: props.vehicule_id,
        nom_document: null,
        description: null,
        date_expiration: null,
        fichier_jointe: []
    });*/
    localDocuments.value.unshift({
        vehicule_id: props.vehicule_id,
        nom_document: null,
        description: null,
        date_expiration: null,
        fichier_jointe: []
    });
}

const mapExistingImages = (listePj) => {
    const imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'];

    if (!listePj || !Array.isArray(listePj)) {
        return [];
    }

    return listePj.map(item => {
        // Si l'item est déjà une string (chemin direct)
        const path = typeof item === 'string' ? item : item.path || item.src || item.url;

        if (!path) return null;

        const extension = path.split('.').pop()?.toLowerCase() || '';

        const isImage = imageExtensions.includes(extension);
        console.log('--------')
        console.log(isImage)
        console.log('--------')
        return {
            path: path,
            isExisting: true,
            type: isImage ? 'image/jpeg' : extension,
            name: item.name || path.split('/').pop(),
            size: item.size || 0,
            ...(typeof item === 'object' ? item : {})
        };
    }).filter(Boolean);
};

const handleFilesUpdate = (photo, { existing = [], newFiles = [] } = {}) => {
    photo.fichier_jointe = [
        ...existing.map(img => ({ src: img.url || img.path })),
        ...newFiles
    ];
};


const removedocument = (index, document) => {
    confirm_delete(() => {
        if (document.id > 0) {
            router.delete(route('vehiculedocument.destroy', document.id), {
                preserveScroll: true,
                onSuccess: () => {
                    localDocuments.value.splice(index, 1);
                },
            });
        }else{
            localDocuments.value.splice(index, 1);
        }

    });
}

const savedocument = (document) => {
    document.loading = true;

    const formData = new FormData();
    formData.append('vehicule_id', props.vehicule_id);
    formData.append('nom_document', document.nom_document || '');
    formData.append('description', document.description || '');
    formData.append('date_expiration', document.date_expiration || '');

    // Traitement des fichiers joints
    if (document.fichier_jointe && document.fichier_jointe.length > 0) {
        // Réinitialiser le tableau pour éviter les doublons
        const existingFiles = [];

        document.fichier_jointe.forEach((item, index) => {
            if (item instanceof File) {
                // Nouveau fichier - l'ajouter directement
                formData.append(`fichier_jointe[${index}]`, item);
            } else  {
                existingFiles.push(item);
            }
        });

        // Ajouter les fichiers existants sous forme de tableau JSON
        if (existingFiles.length > 0) {
            formData.append('existing_files', JSON.stringify(existingFiles));
        }
    }

    const url = document.id
        ? route('vehiculedocument.update', document.id)
        : route('vehiculedocument.store');

    if (document.id) {
        formData.append('_method', 'PUT');
    }

    router.post(url, formData, {
        onSuccess: () => close(),
        forceFormData: true,
        headers: {
            'Content-Type': 'multipart/form-data'
        }
    });
}
</script>


<template>
    <div class="space-y-6">
        <ButtonIcon
            type="primary"
            text="Ajouter Nouveau Pièce Jointe"
            icon="fa-camera"
            @click="newPJ"
        />

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            <div v-for="(document, index) in localDocuments" :key="index"
                 class="border rounded-none border-primary/15 rounded-lg p-4 shadow-sm">

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">

                    <FormItem required label="Nom du Pièce Jointe">
                        <a-input v-model:value="document.nom_document" size="large" />
                    </FormItem>

                    <FormItem label="Date Expiration">

                        <a-date-picker
                            v-model:value="document.date_expiration"
                            :format="dateFormat"
                            size="large"
                            class="w-full"
                            :value-format="'DD-MM-YYYY'"
                        />
                    </FormItem>

                </div>

                <FormItem required label="Remarque" class="mb-4">
                    <a-textarea
                        v-model:value="document.description"
                        placeholder="Remarque"
                        :auto-size="{ minRows: 3, maxRows: 5 }"
                    />
                </FormItem>

                <div class="border-t pt-4">
                    <div class="flex items-center justify-between mb-2">
                        <h4 class="font-medium flex items-center">
                            <PictureOutlined class="mr-2"/>
                            <span class="text-red-500"> * </span> DOCUMENTS
                        </h4>
                    </div>


                    <UploadMultipleFileAndImage
                        :initial-files="mapExistingImages(document.fichier_jointe)"
                        @updateFiles="(files) => handleFilesUpdate(document, files)"
                        :accept="'image/*,.pdf,.doc'"
                        :key="document.id || index"
                    >
                        <template #tips>
                            <p class="text-xs text-gray-500 mt-2">
                                Formats acceptés: Image.*, document.* (max 5MB)
                            </p>
                        </template>
                    </UploadMultipleFileAndImage>
                </div>

                <div class="flex justify-end gap-2 mt-4 pt-3 border-t">
                    <a-button
                        danger
                        class="w-full"
                        @click="removedocument(index, document)"
                    >
                        <template #icon>
                            <DeleteOutlined/>
                        </template>
                        Supprimer
                    </a-button>

                    <a-button
                        type="primary"
                        class="w-full"
                        @click="savedocument(document)"

                    >
                        <template #icon>
                            <SaveOutlined/>
                        </template>
                        Enregistrer
                    </a-button>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>

</style>
