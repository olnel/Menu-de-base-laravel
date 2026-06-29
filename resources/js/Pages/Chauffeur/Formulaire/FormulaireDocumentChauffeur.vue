<template>
    <FormModal
        v-model:open="open"
        :titre="title"
        size="full_screen"
        :show_champ_obligatoir="false"
        :show-footer="false"
    >
        <!-- Header avec titre et statistiques -->
        <div class="mb-8  -mx-6 -mt-6 px-6 pt-6 pb-4 border-b border-blue-100">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">{{ title }}</h2>
                        <p class="text-sm text-gray-600 mt-1">
                            Gérez vos documents en toute simplicité
                        </p>
                    </div>
                </div>

                <!-- Statistiques -->
                <div class="flex items-center space-x-2">
                    <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                    <span class="capitalize text-sm font-medium text-gray-700">
                                {{ localDocuments.length }} document{{ localDocuments.length > 1 ? 's' : '' }}
                            </span>
                </div>
            </div>
        </div>

        <!-- Zone de contenu principal -->
        <div class="relative">
            <!-- État vide avec design ultra-moderne -->
            <div
                v-if="localDocuments.length === 0"
                class="relative overflow-hidden"
            >
                <div class="relative z-10 text-center py-24 px-8">
                    <!-- Illustration moderne avec animation -->
                    <div class="mx-auto mb-8 relative">
                        <!-- Conteneur principal avec effet glassmorphism -->
                        <div
                            class="mx-auto w-40 h-40 bg-white/20 backdrop-blur-sm rounded-3xl flex items-center justify-center mb-6 border border-white/30 relative overflow-hidden group">
                            <!-- Effet de brillance -->
                            <div
                                class="absolute inset-0 bg-gradient-to-r from-transparent via-white/10 to-transparent -skew-x-12 transform -translate-x-full group-hover:translate-x-full transition-transform duration-1000"></div>

                            <!-- Icône principale -->
                            <div
                                class="relative z-10 w-20 h-20 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl flex items-center justify-center shadow-lg transform group-hover:scale-110 transition-transform duration-300">
                                <FileTextOutlined class="text-3xl text-white"/>
                            </div>

                            <!-- Éléments décoratifs flottants -->
                            <div class="absolute top-4 right-4 w-3 h-3 bg-blue-400 rounded-full animate-bounce"></div>
                            <div
                                class="absolute bottom-4 left-4 w-2 h-2 bg-purple-400 rounded-full animate-bounce animation-delay-500"></div>
                            <div
                                class="absolute top-1/2 right-2 w-1.5 h-1.5 bg-indigo-400 rounded-full animate-bounce animation-delay-1000"></div>
                        </div>

                        <!-- Éléments décoratifs autour -->
                        <div class="absolute top-8 left-1/2 transform -translate-x-1/2 -translate-y-4">
                            <div class="flex space-x-2">
                                <div
                                    class="w-2 h-8 bg-gradient-to-b from-blue-400 to-transparent rounded-full opacity-60 animate-pulse"></div>
                                <div
                                    class="w-2 h-6 bg-gradient-to-b from-indigo-400 to-transparent rounded-full opacity-60 animate-pulse animation-delay-300"></div>
                                <div
                                    class="w-2 h-10 bg-gradient-to-b from-purple-400 to-transparent rounded-full opacity-60 animate-pulse animation-delay-600"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Texte avec typographie moderne -->
                    <div class="space-y-6 max-w-2xl mx-auto">
                        <h3 class="text-4xl font-bold bg-gradient-to-r from-gray-900 via-blue-900 to-indigo-900 bg-clip-text text-transparent mb-4">
                            Espace Documents Vide
                        </h3>

                        <div class="flex items-center justify-center space-x-3">
                            <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                                <span class="text-white font-bold text-sm">💡</span>
                            </div>
                            <p class="text-lg text-gray-600 leading-relaxed">
                                <span class="font-bold">Astuce :</span> Vous pouvez aussi utiliser le bouton
                                flottant à droite pour ajouter rapidement un document !
                            </p>
                        </div>

                        <!-- Boutons d'action -->
                        <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                            <ButtonIcon
                                type="primary"
                                text="Créer mon premier document"
                                icon="fa-file"
                                @click="newPJ"
                            />
                        </div>
                    </div>
                </div>
            </div>
            <!-- Iterate over chauffeur.documents if chauffeur prop exists, otherwise empty array -->
            <div v-else class="grid grid-cols-1 xl:grid-cols-2 2xl:grid-cols-3 gap-4">

                <div
                    v-for="(document, index) in localDocuments"
                    :key="index"
                    class="border border-primary/15 rounded-lg p-4 shadow-sm"
                >
                    <!-- Contenu du document -->
                    <div class="">
                        <!-- En-tête avec icône -->
                        <div class="flex items-start justify-between mb-6">
                            <div class="flex items-center space-x-4">
                                <div
                                    class="w-12 h-12 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                                    <FileTextOutlined class="text-white text-lg"/>
                                </div>
                                <div>
                                    <h4 class="text-lg font-bold text-gray-900 group-hover:text-blue-600 transition-colors">
                                        {{ document.type || 'Document sans nom' }}
                                    </h4>
                                    <p class="text-sm text-gray-500 mt-1">
                                        Document #{{ String(index + 1).padStart(2, '0') }}
                                    </p>
                                </div>
                            </div>

                            <!-- Badge de statut -->
                            <div class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-xs font-medium">
                                Actif
                            </div>
                        </div>

                        <!-- Formulaire -->
                        <a-row :gutter="[16, 0]">
                            <a-col :xs="24" :lg="12">
                                <FormItem required label="Nom du document">
                                    <a-input
                                        v-model:value="document.type"
                                        size="large"
                                        placeholder="Saisissez le nom du document"
                                        class="rounded-lg"
                                    />
                                </FormItem>
                            </a-col>

                            <a-col :xs="24" :lg="12">
                                <FormItem label="Date d'expiration">
                                    <a-date-picker
                                        v-model:value="document.date_expiration"
                                        :format="dateFormat"
                                        size="large"
                                        class="w-full rounded-lg"
                                        :value-format="'DD-MM-YYYY'"
                                        placeholder="Sélectionner une date"
                                    />
                                </FormItem>
                            </a-col>

                            <a-col :xs="24">
                                <FormItem required label="Remarque">
                                    <a-textarea
                                        v-model:value="document.description"
                                        placeholder="Remarque"
                                        :auto-size="{ minRows: 3, maxRows: 5 }"
                                    />
                                </FormItem>
                            </a-col>
                        </a-row>
                        <!-- Section upload -->
                        <div class="border-t border-gray-100 pt-4">
                            <div class="flex items-center mb-4">
                                <CloudUploadOutlined class="text-gray-600 mr-2"/>
                                <h5 class="font-medium text-gray-900">Fichiers joints</h5>
                                <span class="text-red-500 ml-1">*</span>
                            </div>

                            <div class="h-28 overflow-y-auto">
                                <UploadMultipleFileAndImage
                                    :initial-files="mapExistingImages(document.fichier_jointe)"
                                    @updateFiles="(files) => handleFilesUpdate(document, files)"
                                    :accept="'image/*,.pdf,.doc,.docx'"
                                    class="rounded-lg"
                                >
                                    <template #tips>
                                        <div
                                            class="flex items-center text-xs text-gray-500 mt-3 p-3 bg-gray-50 rounded-lg">
                                            <InfoCircleOutlined class="mr-2 text-blue-500"/>
                                            <span>Formats acceptés: Images, PDF, DOC (max 5MB par fichier)</span>
                                        </div>
                                    </template>
                                </UploadMultipleFileAndImage>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex items-center gap-3 pt-4 border-t border-gray-100">
                            <a-button
                                danger
                                class="flex-1 h-10 rounded-lg font-medium hover:shadow-md transition-shadow"
                                @click="removedocument(index, document)"
                            >
                                <template #icon>
                                    <font-awesome-icon icon="fa-solid fa-trash" class="mr-2"/>
                                </template>
                                Supprimer
                            </a-button>

                            <a-button
                                type="primary"
                                class="flex-1 h-10 rounded-lg font-medium bg-gradient-to-r from-green-500 to-green-600 border-0 hover:shadow-md transition-shadow"
                                @click="savedocument(document)"
                                :loading="document.loading"
                            >
                                <template #icon>
                                    <font-awesome-icon icon="fa-solid fa-save" class="mr-2"/>
                                </template>
                                Enregistrer
                            </a-button>
                        </div>
                    </div>

                    <!-- Effet de survol -->
                    <div
                        class="absolute inset-0 rounded-2xl ring-2 ring-blue-500 ring-opacity-0 group-hover:ring-opacity-20 transition-all duration-300 pointer-events-none"></div>
                </div>
            </div>
        </div>

        <!-- Bouton flottant d'ajout rapide (positionné comme demandé) -->
        <div class="fixed right-6 top-1/2 transform translate-x-0 -translate-y-1/2 z-50">
            <a-button
                type="primary"
                shape="circle"
                size="middle"
                class="!w-12 !h-12 flex items-center justify-center hover:shadow-3xl transition-all duration-300 hover:scale-110 !bg-primary border-0"
                @click="newPJ"
            >
                <PlusOutlined class="text-xl !me-0"/>
            </a-button>
        </div>
    </FormModal>
</template>

<script setup>
import ButtonIcon from "@/Components/ButtonIcon.vue";
import FormItem from "@/Components/FormItem.vue";
import FormModal from "@/Components/FormModal.vue";
import UploadMultipleFileAndImage from "@/Components/UploadFile/UploadMultipleFileAndImage.vue";
import {router} from "@inertiajs/vue3";
import {ref, watch} from "vue";
import {confirm_delete} from "../../../../Utils/confirmation_modal.js";
import {
    CloudUploadOutlined,
    FileTextOutlined, InfoCircleOutlined,
    PlusOutlined
} from "@ant-design/icons-vue";

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
    router.visit(route("chauffeur.index", {chauffeur_id: id}), {
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

const handleFilesUpdate = (document, {existing = [], newFiles = []} = {}) => {
    document.fichier_jointe = [
        ...existing.map((img) => ({src: img.url || img.path})),
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
    console.log(document.id)
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
defineExpose({openDocModal});
</script>

<style></style>
