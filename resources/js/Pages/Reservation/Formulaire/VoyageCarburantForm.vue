<template>
    <div class="p-4">
        <div class="mb-4 flex justify-end">
            <ButtonIcon
                text="Ajouter un achat de carburant"
                type="primary"
                icon="fa-plus"
                @click="addCarburantTransaction"
            />
        </div>
        <DataTable
            :columns="carburantColumns"
            :data="carburantTableData"
            :actions="carburantActions"
            :btn_action="false"
            :expandable="{
                rowExpandable: (record) =>
                    record.piece_jointe != null &&
                    record.piece_jointe.length > 0,
            }"
        >
            <template #expandedRowRender="{ record }">
                <div class="p-4">
                    <FilePreviewList
                        :initial-files="mapExistingImages(record.piece_jointe)"
                        :disabled="true"
                        :preview-only="true"
                    />
                </div>
            </template>
            <template #bodyCell="{ column, record }">
                <template v-if="column.key === 'date_transaction'">
                    {{ record.date_transaction ?? "-" }}
                </template>
                <template v-if="column.key === 'type'">
                    {{ record.type ?? "-" }}
                </template>
                <template v-if="column.key === 'carburant_litre'">
                    <span class="text-center block">
                        {{
                            record.carburant_litre
                                ? new Intl.NumberFormat().format(
                                      record.carburant_litre
                                  )
                                : "-"
                        }}
                    </span>
                </template>
                <template v-if="column.key === 'prix_unitaire'">
                    <span class="">
                        {{
                            record.prix_unitaire
                                ? new Intl.NumberFormat().format(
                                      record.prix_unitaire
                                  )
                                : "-"
                        }}
                    </span>
                </template>
                <template v-if="column.key === 'montant'">
                    <span class="text-right block">
                        {{
                            record.montant
                                ? new Intl.NumberFormat().format(record.montant)
                                : "-"
                        }}
                    </span>
                </template>
            </template>
        </DataTable>
    </div>
    <VoyageCarburantFormulaire
        ref="carburantFormModal"
        @transaction-saved="() => refreshVoyageDetails(voyageId)"
        :vehicules="vehicules"
        :chauffeurs="chauffeurs"
        :carburant-cards="carburantCards"
    />
</template>

<script setup>
import { confirm_delete } from "@/../Utils/confirmation_modal.js";
import ButtonIcon from "@/Components/ButtonIcon.vue";
import DataTable from "@/Components/DataTable.vue";
import FilePreviewList from "@/Components/FilePreviewList.vue";
import { router } from "@inertiajs/vue3";
import { computed, ref } from "vue";
import VoyageCarburantFormulaire from "./VoyageCarburantFormulaire.vue";

const props = defineProps({
    voyageId: {
        type: Number,
        required: true,
    },
    initialCarburants: {
        type: Array,
        default: () => [],
    },
    refreshVoyageDetails: {
        type: Function,
        required: true,
    },
    vehicules: {
        type: Array,
        default: () => [],
    },
    chauffeurs: {
        type: Array,
        default: () => [],
    },
    carburantCards: {
        type: Array,
        default: () => [],
    },
    vehiculeId: {
        type: Number,
        default: null,
    },
    chauffeurId: {
        type: Number,
        default: null,
    },
    remorqueId: {
        type: Number,
        default: null,
    },
});

const carburantFormModal = ref();

const carburantColumns = [
    {
        key: "date_transaction",
        title: "Date",
        width: 100,
    },
    { key: "type", title: "Type", width: 100 },
    {
        key: "carburant_litre",
        title: "Litres",
        customCell: () => ({ class: "!text-center" }),
        customHeaderCell: () => ({ class: "!text-center whitespace-nowrap" }),
        width: 100,
    },
    {
        key: "prix_unitaire",
        title: "P.U.",
        customCell: () => ({ class: "!text-right" }),
        customHeaderCell: () => ({ class: "!text-right" }),
        width: 100,
    },
    {
        key: "montant",
        title: "Montant",
        customCell: () => ({ class: "!text-right" }),
        customHeaderCell: () => ({ class: "!text-right" }),
        width: 100,
    },
];

const carburantTableData = computed(() => ({
    data: props.initialCarburants || [],
}));

const carburantActions = [
    {
        text: "Supprimer",
        action: (record) => deleteCarburantTransaction(record),
        icon: "fa-trash",
        class: "!text-red-600",
        privilege : "carburant_transactions.destroy"
    },
];

const addCarburantTransaction = () => {
    carburantFormModal.value.add({
        voyage_id: props.voyageId,
        vehicule_id: props.vehiculeId,
        chauffeur_id: props.chauffeurId,
        remorque_id: props.remorqueId,
    });
};

const deleteCarburantTransaction = (record) => {
    confirm_delete(() => {
        router.delete(route("carburant_transactions.destroy", record.id), {
            preserveScroll: true,
            onSuccess: () => {
                props.refreshVoyageDetails(props.voyageId);
            },
        });
    });
};

const mapExistingImages = (listePj) => {
    const imageExtensions = ["jpg", "jpeg", "png", "gif", "bmp", "webp"];
    let processedListePj = listePj;
    if (typeof listePj === "string") {
        try {
            processedListePj = JSON.parse(listePj);
        } catch (e) {
            console.error("Failed to parse piece_jointe JSON string:", e);
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
