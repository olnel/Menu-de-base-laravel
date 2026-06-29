<template>
    <AuthenticatedLayout title="Détails du Chauffeur">
        <div class="pt-6">
            <a-card class="main-shadow rounded-lg overflow-hidden">
                <!-- Conteneur principal avec padding intérieur -->
                <div class="p-6 space-y-8">
                    <!-- Première Ligne: Image et Informations Générales -->
                    <div
                        class="grid grid-cols-1 xl:grid-cols-3 gap-6 content-center"
                    >
                        <!-- Colonne Image + Nom (Combiné) -->
                        <div
                            class="xl:col-span-1 flex flex-col items-center justify-center space-y-4"
                        >
                            <div class="relative overflow-hidden w-40 h-40">
                                <img
                                    v-if="chauffeur && chauffeur.img"
                                    :src="`../${chauffeur.img}`"
                                    class="rounded-lg h-full w-full object-cover"
                                />
                            </div>
                        </div>
                        <!-- Colonne Informations Générales -->
                        <div class="xl:col-span-2 mt-0">
                            <a-descriptions
                                :column="{
                                    xxl: 2,
                                    xl: 2,
                                    lg: 1,
                                    md: 1,
                                    sm: 1,
                                    xs: 1,
                                }"
                                bordered
                            >
                                <a-descriptions-item label="Nom Complet">{{
                                    chauffeur.nom || chauffeur.prenom
                                        ? `${
                                              chauffeur.nom.toUpperCase() ?? ""
                                          } ${chauffeur.prenom ?? ""}`.trim()
                                        : "N/A"
                                }}</a-descriptions-item>
                                <a-descriptions-item label="Matricule">{{
                                    chauffeur.matricule ?? "N/A"
                                }}</a-descriptions-item>
                                <a-descriptions-item
                                    label="Date de naissance"
                                    >{{
                                        chauffeur.date_naissance ?? "N/A"
                                    }}</a-descriptions-item
                                >
                                <a-descriptions-item label="N° CIN">{{
                                    chauffeur.CIN ?? "N/A"
                                }}</a-descriptions-item>
                                <a-descriptions-item label="Téléphone">{{
                                    chauffeur.telephone ?? "N/A"
                                }}</a-descriptions-item>
                                <a-descriptions-item label="Adresse">{{
                                    chauffeur.adresse ?? "N/A"
                                }}</a-descriptions-item>
                            </a-descriptions>
                        </div>
                    </div>

                    <!-- Deuxième Ligne: Documents et Véhicules -->
                    <div class="grid grid-cols-1 gap-6">
                        <!-- Colonne Documents  -->
                        <a-card
                            title="Documents du Chauffeur"
                            size="small"
                            class="rounded-lg  col-span-1"
                        >
                            <a-table
                                :columns="documentColumns"
                                :data-source="chauffeur.documents"
                                :pagination="false"
                                :row-key="'id'"
                                size="small"
                                :expandable="{
                                    expandedRowRender: (record) => true,
                                    rowExpandable: (record) =>
                                        record.fichier_jointe !== null &&
                                        record.fichier_jointe.length > 0,
                                }"
                                :expand-icon-column-index="4"
                                :expand-column-width="50"
                                class="custom-table overflow-x-auto overflow-y-hidden"

                            >
                                <template #expandedRowRender="{ record }">
                                    <div class="p-4">
                                        <FilePreviewList
                                            :initial-files="
                                                mapExistingImages(
                                                    record.fichier_jointe
                                                )
                                            "
                                            :disabled="true"
                                            :preview-only="true"
                                        />
                                    </div>
                                </template>

                                <template #bodyCell="{ column }">
                                    <template v-if="column.key === 'action'">
                                        <!-- Content is now in expandedRowRender -->
                                    </template>
                                </template>
                                <template #emptyText>
                                    <a-empty
                                        description="Aucun document associé"
                                        size="small"
                                    />
                                </template>
                            </a-table>
                        </a-card>

                        <!-- Colonne Véhicules Associés  -->
                        <a-card
                            title="Véhicules Associés"
                            size="small"
                            class="rounded-lg overflow-hidden col-span-1"
                        >
                            <div
                                v-if="
                                    chauffeur.vehicules &&
                                    chauffeur.vehicules.length > 0
                                "
                                class="flex flex-col gap-4 p-4"
                            >
                                <div
                                    v-for="vehicule in chauffeur.vehicules"
                                    :key="vehicule.id"
                                    class="bg-gray-50 border border-gray-200 rounded-lg px-6 py-4 flex flex-col gap-4 shadow-sm"
                                >
                                    <div class="flex items-center gap-3">
                                        <font-awesome-icon
                                            icon="fa fa-car"
                                            class="text-primary text-2xl"
                                        ></font-awesome-icon>
                                        <span
                                            class="text-2xl text-primary tracking-wide font-semibold"
                                        >
                                            {{ vehicule.immatriculation }}
                                        </span>
                                    </div>
                                    <div
                                        class="grid grid-cols-1 md:grid-cols-3 gap-4 text-gray-700 text-sm"
                                    >
                                        <span>
                                            <b class="font-medium">Marque :</b>
                                            {{ vehicule.marque || "N/A" }}
                                        </span>
                                        <span>
                                            <b class="font-medium">Modèle :</b>
                                            {{ vehicule.modele || "N/A" }}
                                        </span>
                                        <span>
                                            <b class="font-medium">Couleur :</b>
                                            {{ vehicule.couleur || "N/A" }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <a-empty
                                v-else
                                description="Aucun véhicule associé"
                                size="small"
                                class="p-4"
                            />
                        </a-card>
                    </div>
                </div>
            </a-card>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import FilePreviewList from "@/Components/FilePreviewList.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { defineProps, h } from "vue";

const props = defineProps({
    chauffeur: {
        type: Object,
        required: true,
    },
});

// Définition des colonnes des documents
const documentColumns = [
    {
        title: "Nom du Document",
        customRender: ({ record }) =>
            h(
                "div",
                h(
                    "p",
                    {
                        class: "text-gray-500 font-semibold text-base uppercase",
                    },
                    record.type
                )
            ),
        key: "type",
    },
    {
        title: "Date d'Expiration",
        dataIndex: "date_expiration",
        customRender: ({ record }) => {
            return record.date_expiration || "N/A";
        },
    },
    {
        title: "Description",
        dataIndex: "description",
        customRender: ({ record }) => {
            return record.description || "N/A";
        },
    },
    {
        title: "Pièce(s) Jointe(s)",
        key: "fichier_jointe",
        customRender: ({ record }) => {
            const files = Array.isArray(record.fichier_jointe)
                ? record.fichier_jointe
                : typeof record.fichier_jointe === "string"
                ? JSON.parse(record.fichier_jointe)
                : [];
            return files.length;
        },
    },
    {
        key: "action",
        fixed: "right",
    },
];

const mapExistingImages = (listePj) => {
    const imageExtensions = ["jpg", "jpeg", "png", "gif", "bmp", "webp"];

    let processedListePj = listePj;
    // Check le type de listePj fichier_jointe
    if (typeof listePj === "string") {
        try {
            processedListePj = JSON.parse(processedListePj);
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

console.log(
    "Data passed to UploadMultipleFileAndImage (initial-files):",
    mapExistingImages(props.chauffeur.documents[0]?.fichier_jointe)
);
</script>

<style scoped>
/* Add striped rows */
.custom-table
    :deep(tbody > tr.ant-table-row.ant-table-row-level-0:nth-child(even)) {
    @apply !bg-[#fafafa];
}

/* Existing styles */
.custom-table :deep(th.ant-table-cell::before) {
    @apply !hidden;
}
.custom-table :deep(.ant-table-thead > tr > th) {
    @apply bg-primary text-white font-medium  whitespace-nowrap px-4 py-2.5;
}
.custom-table :deep(.ant-table-cell .p-4) {
    @apply !p-0;
}
:deep(.ant-table-wrapper .ant-table-row-expand-icon:hover) {
    @apply !text-primary !border-primary;
}
</style>
