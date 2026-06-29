<template>
    <FormModal
        v-model:open="open"
        :titre="title"
        @close="closeDocModal"
        @submit="saveDocument"
        size="md"
        :show_champ_obligatoir="false"
    >
        <div class="space-y-6 mt-4">
            <div
                class="flex items-center space-x-3 mb-6 pb-3 border-b border-gray-100"
            >
                <font-awesome-icon
                    :icon="['fas', 'edit']"
                    class="text-primary text-lg"
                />
                <h4 class="text-lg font-semibold text-gray-800">
                    Informations du document
                </h4>
            </div>
            <a-row :gutter="[16, 0]">
                <a-col :xs="24" :lg="12">
                    <FormItem
                        required
                        label="Nom du Document"
                        :help="formErrors.nom_document"
                    >
                        <a-input
                            v-model:value="localDocument.nom_document"
                            placeholder="Nom du document"
                            size="large"
                        />
                    </FormItem>
                </a-col>

                <a-col :xs="24" :lg="12">
                    <FormItem
                        label="Date d'expiration"
                        :help="formErrors.date_expiration"
                    >
                        <a-date-picker
                            v-model:value="localDocument.date_expiration"
                            format="DD/MM/YYYY"
                            size="large"
                            class="w-full"
                            :value-format="'YYYY-MM-DD'"
                            placeholder="Sélectionner une date"
                        />
                    </FormItem>
                </a-col>

                <a-col :xs="24">
                    <FormItem
                        label="Description"
                        :help="formErrors.description"
                    >
                        <a-textarea
                            v-model:value="localDocument.description"
                            placeholder="Description du document"
                            :auto-size="{ minRows: 3, maxRows: 5 }"
                        />
                    </FormItem>
                </a-col>

                <a-col :xs="24">
                    <div class="flex items-center space-x-3 mb-4">
                        <font-awesome-icon
                            :icon="['fas', 'file']"
                            class="text-primary text-lg"
                        />
                        <h4 class="text-lg font-semibold text-gray-800">
                            Fichiers joints
                        </h4>
                        <span class="text-red-500 font-medium">*</span>
                    </div>

                    <div
                        class="bg-gray-50 border-2 border-dashed border-gray-300 rounded-lg p-6 hover:border-sky-500 hover:bg-sky-50 transition-all duration-300"
                    >
                        <UploadMultipleFileAndImage
                            :initial-files="
                                mapExistingImages(localDocument.fichier_jointe)
                            "
                            @updateFiles="
                                (files) =>
                                    handleFilesUpdate(localDocument, files)
                            "
                            :key="uploadComponentKey"
                            accept="image/*,.pdf,.doc,.docx"
                            class="h-48 overflow-y-auto"
                        >
                            <template #tips>
                                <div
                                    class="mt-4 p-3 bg-primary/5 rounded-lg border border-blue-200"
                                >
                                    <p class="text-xs text-primary font-medium">
                                        <font-awesome-icon
                                            icon="fa-solid fa-info-circle"
                                            class="mr-2"
                                        />
                                        Formats acceptés: Images, PDF, DOC
                                        <span class="ml-1 font-semibold"
                                            >. (max 5MB)</span
                                        >
                                    </p>
                                </div>
                            </template>
                        </UploadMultipleFileAndImage>
                    </div>
                </a-col>
            </a-row>
        </div>
    </FormModal>
</template>

<script setup>
import FormItem from "@/Components/FormItem.vue";
import FormModal from "@/Components/FormModal.vue";
import UploadMultipleFileAndImage from "@/Components/UploadFile/UploadMultipleFileAndImage.vue";
import { router } from "@inertiajs/vue3";
import dayjs from "dayjs";
import customParseFormat from "dayjs/plugin/customParseFormat";
import { ref } from "vue";
dayjs.extend(customParseFormat);

// Form for a single document
const localDocument = ref({
    id: null,
    chauffeur_id: null,
    nom_document: null,
    description: null,
    date_expiration: null,
    fichier_jointe: [],
});

const open = ref(false);
const title = ref("");
const currentChauffeurId = ref(null);
const uploadComponentKey = ref(0);
const loading = ref(false);
const formErrors = ref({});

// Reverting mapExistingImages to closely match FormulaireDocumentChauffeur.vue
const mapExistingImages = (listePj) => {
    const imageExtensions = ["jpg", "jpeg", "png", "gif", "bmp", "webp"];

    let processedListePj = listePj;
    if (typeof listePj === "string") {
        try {
            processedListePj = JSON.parse(listePj);
        } catch (e) {
            console.error("Failed to parse fichier_jointe JSON string:", e);
            processedListePj = [];
        }
    }

    if (!processedListePj || !Array.isArray(processedListePj)) {
        return [];
    }

    return processedListePj
        .map((item) => {
            const originalPath =
                typeof item === "string"
                    ? item
                    : item.src || item.url || item.path;
            if (!originalPath) return null;

            const extension =
                originalPath.split(".").pop()?.toLowerCase() || "";
            const isImage = imageExtensions.includes(extension);

            return {
                url: `../../${originalPath}`,
                originalPath: originalPath,
                isExisting: true,
                type: isImage ? "image/jpeg" : extension,
                name: item.name || originalPath.split("/").pop(),
                size: item.size || 0,
                ...(typeof item === "object" ? item : {}),
            };
        })
        .filter(Boolean);
};

// Reverting handleFilesUpdate to closely match FormulaireDocumentChauffeur.vue
const handleFilesUpdate = (
    documentInstance,
    { existing = [], newFiles = [] } = {}
) => {
    console.log("Existing files:", existing);
    console.log("New files:", newFiles);

    // Préserver les fichiers existants
    const existingFiles = existing
        .filter((file) => file.isExisting)
        .map((file) => ({
            src: file.url.replace("../../", ""),
            isExisting: true,
        }));

    // Combiner les fichiers existants avec les nouveaux
    documentInstance.fichier_jointe = [...existingFiles, ...newFiles];
};

const openDocModal = (chauffeur_obj, document_to_edit = null) => {
    uploadComponentKey.value++;
    currentChauffeurId.value = chauffeur_obj.id;
    formErrors.value = {};

    if (document_to_edit) {
        localDocument.value.id = document_to_edit.id;
        localDocument.value.chauffeur_id = document_to_edit.chauffeur_id;
        localDocument.value.nom_document = document_to_edit.nom_document;
        localDocument.value.description = document_to_edit.description;
        localDocument.value.date_expiration = document_to_edit.date_expiration
            ? dayjs(document_to_edit.date_expiration, "DD-MM-YYYY")
            : null;

        // S'assurer que les fichiers existants sont correctement chargés
        const existingFiles = mapExistingImages(
            document_to_edit.fichier_jointe
        );
        localDocument.value.fichier_jointe = existingFiles.map((file) => ({
            src: file.url.replace("../../", ""),
            isExisting: true,
        }));

        title.value = `Modifier le document: ${
            document_to_edit.nom_document || "Sans nom"
        }`;
    } else {
        localDocument.value = {
            id: null,
            chauffeur_id: chauffeur_obj.id,
            nom_document: null,
            description: null,
            date_expiration: null,
            fichier_jointe: [],
        };
        title.value = "Ajouter un nouveau document";
    }
    open.value = true;
};

const closeDocModal = () => {
    open.value = false;
    localDocument.value = {
        id: null,
        chauffeur_id: null,
        nom_document: null,
        description: null,
        date_expiration: null,
        fichier_jointe: [],
    };
    formErrors.value = {};
    currentChauffeurId.value = null;
    uploadComponentKey.value++;
};

const saveDocument = () => {
    loading.value = true;
    formErrors.value = {};

    const formData = new FormData();

    if (localDocument.value.id) {
        formData.append("_method", "PUT");
    }

    formData.append("chauffeur_id", localDocument.value.chauffeur_id);
    formData.append("nom_document", localDocument.value.nom_document || "");
    formData.append("description", localDocument.value.description || "");
    formData.append(
        "date_expiration",
        localDocument.value.date_expiration
            ? dayjs(localDocument.value.date_expiration).format("YYYY-MM-DD")
            : ""
    );

    // Traitement des fichiers
    const existingFiles = [];
    const newFiles = [];

    if (localDocument.value.fichier_jointe?.length) {
        localDocument.value.fichier_jointe.forEach((item) => {
            if (item instanceof File) {
                newFiles.push(item);
            } else if (item.src) {
                existingFiles.push(item.src);
            }
        });
    }

    // Ajouter les nouveaux fichiers
    newFiles.forEach((file, index) => {
        formData.append(`fichier_jointe[${index}]`, file);
    });

    // Toujours envoyer les fichiers existants
    formData.append("existing_files", JSON.stringify(existingFiles));

    console.log("Sending existing files:", existingFiles);
    console.log("Sending new files:", newFiles);

    const url = localDocument.value.id
        ? route("chauffeur_documents.update", localDocument.value.id)
        : route("chauffeur_documents.store");

    router.post(
        url,
        formData,
        {
            onSuccess: () => {
                closeDocModal();
            },
            onError: (errors) => {
                formErrors.value = errors;
            },
            forceFormData: true,
            headers: {
                "Content-Type": "multipart/form-data",
            },
            onFinish: () => {
                loading.value = false;
            },
        },
        { immediate: true, deep: true }
    );
};
defineExpose({ openDocModal });
</script>

<style scoped>
.ant-upload-hint {
    font-size: 12px;
    color: #888;
}
</style>
