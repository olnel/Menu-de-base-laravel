<template>
    <div class="p-4">
        <div class="flex justify-end mb-4">
            <ButtonIcon
                type="primary"
                text="Ajouter un document"
                icon="fa-plus"
                @click="openAddDocumentModal"
                v-if="hasPrivilege('chauffeurdocument.store')"
            />
        </div>

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
            :expand-icon-column-index="0"
            :expand-column-width="50"
            class="custom-table whitespace-nowrap main-rounded overflow-hidden"
            :row-class-name="
                (_record, index) => (index % 2 === 1 ? 'table-striped' : null)
            "
        >
            <template #expandedRowRender="{ record }">
                <div class="p-4">
                    <FilePreviewList
                        :initial-files="
                            mapExistingImages(record.fichier_jointe)
                        "
                        :disabled="true"
                        :preview-only="true"
                    />
                </div>
            </template>

            <template #bodyCell="{ column, record }">
                <!-- Action column -->
                <template v-if="column.key === 'action'">
                    <div class="flex items-center gap-1">
                        <a-button
                            v-if="hasPrivilege('chauffeurdocument.update')"
                            type="text"
                            class="!p-1"
                            @click="openEditDocumentModal(record)"
                        >
                            <font-awesome-icon
                                icon="fa-solid fa-edit"
                                class=""
                            />
                        </a-button>
                        <a-button
                            v-if="hasPrivilege('chauffeurdocument.destroy')"
                            type="text"
                            class="!p-1"
                            @click="confirmDeleteDocument(record.id)"
                        >
                            <font-awesome-icon
                                icon="fa-solid fa-trash"
                                class="text-red-600"
                            />
                        </a-button>
                    </div>
                </template>
            </template>
            <template #emptyText>
                <a-empty description="Aucun document associé" size="small" />
            </template>
        </a-table>
    </div>

    <!-- Modal-->
    <ModalDocument ref="documentModalRef" />
</template>

<script setup>
import ButtonIcon from "@/Components/ButtonIcon.vue";
import FilePreviewList from "@/Components/FilePreviewList.vue";
import ModalDocument from "@/Pages/Chauffeur/Informations/ModalDocument.vue";
import { router } from "@inertiajs/vue3";
import dayjs from "dayjs";
import { h, ref } from "vue";
import { confirm_delete } from "../../../../Utils/confirmation_modal.js";
import { usePrivileges } from "../../../../Utils/usePrivileges";

const props = defineProps({
    chauffeur: {
        type: Object,
        required: true,
    },
});

const documentModalRef = ref(null); // Ref pour le documentModal
const { hasPrivilege } = usePrivileges();

// Fonction pour ouvrir le modal pour ajouter un nouveau document
const openAddDocumentModal = () => {
    if (documentModalRef.value) {
        documentModalRef.value.openDocModal(props.chauffeur); // Passer chauffeur object
    }
};

// Fonction pour ouvrir le modal pour modifier un document existant
const openEditDocumentModal = (documentToEdit) => {
    if (documentModalRef.value) {
        documentModalRef.value.openDocModal(props.chauffeur, documentToEdit); // Pass chauffeur and document to edit
    }
};

// Fonction pour confirmer et supprimer un document
const confirmDeleteDocument = (documentId) => {
    confirm_delete(() => {
        router.delete(route("chauffeur_documents.destroy", documentId), {
            preserveScroll: true,
            preserveState: true,
        });
    });
};

// --- Définition des colonnes du tableau des documents ---
const documentColumns = [
    {
        title: "NOM DU DOCUMENT",
        key: "nom_document",
        customRender: ({ record }) =>
            h(
                "div",
                h(
                    "p",
                    {
                        class: "text-gray-500 font-medium  uppercase",
                    },
                    record.nom_document
                )
            ),
    },
    {
        title: "DATE D'EXPIRATION",
        key: "date_expiration",

        customCell: () => ({ class: "text-center" }),
        customHeaderCell: () => ({ class: "!text-center" }),
        customRender: ({ record }) => {
            console.log("date_expiration:", record.date_expiration);
            return record.date_expiration
                ? dayjs(record.date_expiration, "DD-MM-YYYY").format(
                      "DD/MM/YYYY"
                  )
                : "-";
        },
    },
    {
        title: "DESCRIPTION",
        key: "description",
        customRender: ({ record }) => {
            return record.description || "-";
        },
    },
    {
        title: "PIÈCE(S) JOINTE(S)",
        key: "fichier_jointe",
        customHeaderCell: () => ({ class: "!text-center" }),
        customCell: () => ({ class: "text-center" }),
        customRender: ({ record }) => {
            let files = [];
            if (Array.isArray(record.fichier_jointe)) {
                files = record.fichier_jointe;
            } else if (typeof record.fichier_jointe === "string") {
                try {
                    files = JSON.parse(record.fichier_jointe);
                } catch (e) {
                    console.error(
                        "Failed to parse fichier_jointe JSON string:",
                        e
                    );
                    files = [];
                }
            }
            return files.length;
        },
    },
    {
        key: "action",
        fixed: "right",
        width: 100,
    },
];

const mapExistingImages = (listePj) => {
    const imageExtensions = ["jpg", "jpeg", "png", "gif", "bmp", "webp"];

    let processedListePj = listePj;
    if (typeof listePj === "string") {
        try {
            processedListePj = JSON.parse(processedListePj);
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
            const path =
                typeof item === "string"
                    ? item
                    : item.path || item.src || item.url;

            if (!path) return null;

            const extension = path.split(".").pop()?.toLowerCase() || "";

            const isImage = imageExtensions.includes(extension);
            return {
                path: `../${path}`,
                isExisting: true,
                type: isImage ? "image/jpeg" : extension,
                name: item.name || path.split("/").pop(),
                size: item.size || 0,
                ...(typeof item === "object" ? item : {}),
            };
        })
        .filter(Boolean);
};
</script>

<style scoped>
/* Styles copied from DataTable.vue for consistent look and feel */
.fixed-column {
    position: sticky !important;
    right: 0 !important;
    background: white !important;
}
.custom-table :deep(.ant-table-cell .p-4) {
    @apply !p-0;
}

.custom-table
    :deep(tbody > tr.ant-table-row.ant-table-row-level-0:nth-child(even)) {
    @apply !bg-[#fafafa];
}
.custom-table :deep(th.ant-table-cell::before) {
    @apply !hidden;
}
.custom-table :deep(.ant-table-thead > tr > th) {
    @apply bg-primary  px-4 py-2.5;
}
:deep(.ant-table-wrapper .ant-table-row-expand-icon:hover) {
    @apply !text-primary !border-primary;
}
</style>
