<template>
    <FormModal
        v-model:open="open"
        :titre="title"
        size="full_screen"
        :show_champ_obligatoir="false"
        :show-footer="false"
    >
        <div class="space-y-6">
            <ButtonIcon
                type="primary"
                text="Ajouter un Nouveau Documents"
                icon="fa-file"
                @click="newPJ"
            />

            <!-- Iterate over chauffeur.documents if chauffeur prop exists, otherwise empty array -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div
                    v-for="(document, index) in localDocuments"
                    :key="index"
                    class="border border-primary/15 rounded-lg p-4 shadow-sm"
                >
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                        <FormItem required label="Nom du Pièce Jointe">
                            <a-input
                                v-model:value="document.type"
                                size="large"
                            />
                        </FormItem>

                        <FormItem label="Date expiration du document">
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
                                <PictureOutlined class="mr-2" />
                                <span class="text-red-500"> * </span> DOCUMENTS
                            </h4>
                        </div>

                        <UploadMultipleFileAndImage
                            :initial-files="
                                mapExistingImages(document.fichier_jointe)
                            "
                            @updateFiles="
                                (files) => handleFilesUpdate(document, files)
                            "
                            :accept="'image/*,.pdf,.doc'"
                        >
                            <template #tips>
                                <p class="text-xs text-gray-500 mt-2">
                                    Formats acceptés: Image.*, document.* (max
                                    5MB)
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
                                <DeleteOutlined />
                            </template>
                            Supprimer
                        </a-button>

                        <a-button
                            type="primary"
                            class="w-full"
                            @click="savedocument(document)"
                        >
                            <template #icon>
                                <SaveOutlined />
                            </template>
                            Enregistrer
                        </a-button>
                    </div>
                </div>
            </div>
        </div>
    </FormModal>
</template>

<script setup>
import ButtonIcon from "@/Components/ButtonIcon.vue";
import FormItem from "@/Components/FormItem.vue";
import FormModal from "@/Components/FormModal.vue";
import UploadMultipleFileAndImage from "@/Components/UploadFile/UploadMultipleFileAndImage.vue";
import { router } from "@inertiajs/vue3";
import { ref, watch } from "vue";
import { confirm_delete } from "../../../../Utils/confirmation_modal.js";

const props = defineProps({
    documents: {
        type: Array,
        required: false,
        default: () => [],
    },
});

const title = ref("");
const open = ref(false);
const dateFormat = "YYYY/MM/DD";
const currentChauffeurId = ref(null);
const localDocuments = ref([]);

watch();

const newPJ = () => {
    localDocuments.value.push({
        chauffeur_id: currentChauffeurId.value,
        type: null,
        description: null,
        date_expiration: null,
        fichier_jointe: [],
    });
};

const fetchChauffeurDocuments = (id) => {
    router.visit(route("chauffeur.index", { chauffeur_id: id }), {
        only: ["documents"],
        preserveState: true,
        preserveScroll: true,
        onSuccess: (reponse) => {
            console.log(reponse.props.documents);
            localDocuments.value = reponse.props.documents;
        },
    });
};
const openDocModal = (chauffeur) => {
    currentChauffeurId.value = chauffeur.id;
    fetchChauffeurDocuments(chauffeur.id);
    title.value = `Liste des documents de ${chauffeur.nom.toUpperCase()} ${
        chauffeur.prenom
    }`;
    open.value = true;
};

const closeDocModal = () => {
    open.value = false;
    documents.value = []; // Clear documents
    currentChauffeurId.value = null;
};

const mapExistingImages = (listePj) => {
    const imageExtensions = ["jpg", "jpeg", "png", "gif", "bmp", "webp"];

    let processedListePj = listePj;
    // Check le type de listePj fichier_jointe
    if (typeof listePj === "string") {
        try {
            processedListePj = JSON.parse(listePj);
        } catch (e) {
            console.error("Failed to parse fichier_jointe JSON string:", e);
            processedListePj = []; // Set to empty array if parsing fails
        }
    }

    if (!processedListePj || !Array.isArray(processedListePj)) {
        return [];
    }

    return processedListePj
        .map((item) => {
            // Si l'item est déjà une string (chemin direct)
            const path =
                typeof item === "string"
                    ? item
                    : item.path || item.src || item.url;

            if (!path) return null;

            const extension = path.split(".").pop()?.toLowerCase() || "";

            const isImage = imageExtensions.includes(extension);
            console.log("--------");
            console.log(isImage);
            console.log("--------");
            return {
                path: path,
                isExisting: true,
                type: isImage ? "image/jpeg" : extension,
                name: item.name || path.split("/").pop(),
                size: item.size || 0,
                ...(typeof item === "object" ? item : {}),
            };
        })
        .filter(Boolean);
};

const handleFilesUpdate = (document, { existing = [], newFiles = [] } = {}) => {
    document.fichier_jointe = [
        ...existing.map((img) => ({ src: img.url || img.path })),
        ...newFiles,
    ];
};

const removedocument = (index, document) => {
    console.log(document.id);
    confirm_delete(() => {
        if (document.id > 0) {
            router.delete(route("chauffeur_documents.destroy", document.id), {
                preserveScroll: true,
            });
        }
        localDocuments.value.splice(index, 1);
    });
};

const savedocument = (document) => {
    document.loading = true;

    const formData = new FormData();
    formData.append("chauffeur_id", currentChauffeurId.value);
    formData.append("type", document.type || "");
    formData.append("description", document.description || "");
    formData.append("date_expiration", document.date_expiration || "");
    // Traitement des fichiers joints
    if (document.fichier_jointe && document.fichier_jointe.length > 0) {
        // Réinitialiser le tableau pour éviter les doublons
        const existingFiles = [];
        document.fichier_jointe.forEach((item, index) => {
            if (item instanceof File) {
                // Nouveau fichier - l'ajouter directement
                formData.append(`fichier_jointe[${index}]`, item);
            } else {
                existingFiles.push(item);
            }
        });
        // Ajouter les fichiers existants sous forme de tableau JSON
        if (existingFiles.length > 0) {
            formData.append("existing_files", JSON.stringify(existingFiles));
        }
    }

    const url = document.id
        ? route("chauffeur_documents.update", document.id)
        : route("chauffeur_documents.store");

    if (document.id) {
        formData.append("_method", "PUT");
    }

    router.post(url, formData, {
        onSuccess: () => close(),
        forceFormData: true,
        headers: {
            "Content-Type": "multipart/form-data",
        },
    });
};

// Expose openDocModal for parent to call via ref
defineExpose({ openDocModal });
</script>

<style></style>
