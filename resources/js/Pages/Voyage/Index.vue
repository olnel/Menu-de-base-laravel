<script>
const dateFormat = "YYYY/MM/DD";
</script>

<template>

    <SousMenuPrincipale :title="title" selectedMenu="voyage_list">
        <template #top>
            <FilterBase
            v-model="filter"
            @search="applyFilters"
            @reset="resetFilters"
        >
            <template #import v-if="can('export_voyage.export')">
                <ExportData :show_import="false" :title="'Export data'" :columns="columns" :filter="filter" :url="route('voyage.export')" >
                    <template #import >
                        <excel-import-base-standard :columns="columns" model="test"/>
                    </template>
                </ExportData>
            </template>
            <template #add>
                <div class="flex gap-2 items-center justify-center">
                    <ButtonIcon
                        v-if="
                            selectedRowKeys.length > 0 && can('voyages.facture')
                        "
                        @click="handleAction"
                        type="primary"
                        text="Générer Facture"
                        icon="fa-check"
                        :disabled="!selectedClient"
                    />
                </div>
            </template>
            <template #otherFilter>
                <a-popover
                    placement="bottomRight"
                    trigger="click"
                    :visible="dropdownVisible"
                    @visibleChange="(val) => (dropdownVisible = val)"
                >
                    <template #content>
                        <div class="bg-white p-4 w-96 space-y-2 rounded-md">
                            <a-date-picker
                                v-model:value="filter.start_date"
                                :format="dateFormat"
                                size="large"
                                class="w-full text-center"
                                placeholder="Du"
                                :value-format="'DD-MM-YYYY'"
                            />
                            <a-date-picker
                                v-model:value="filter.end_date"
                                :format="dateFormat"
                                size="large"
                                class="w-full text-center"
                                placeholder="Au"
                                :value-format="'DD-MM-YYYY'"
                            />
                            <a-select
                                v-model:value="filter.client_id"
                                placeholder="Client"
                                class="w-full"
                                size="large"
                                allow-clear
                                show-search
                                :filter-option="
                                    (input, option) =>
                                        option.label
                                            .toLowerCase()
                                            .includes(input.toLowerCase())
                                "
                                option-filter-prop="label"
                            >
                                <a-select-option
                                    v-for="client in clients"
                                    :key="client.value"
                                    :value="client.value"
                                    :label="client.label"
                                >
                                    {{ client.label }}
                                </a-select-option>
                            </a-select>
                            <a-select
                                v-model:value="filter.vehicule_id"
                                placeholder="Vehicule"
                                class="w-full"
                                size="large"
                                allow-clear
                                show-search
                                :filter-option="
                                    (input, option) =>
                                        option.label
                                            .toLowerCase()
                                            .includes(input.toLowerCase())
                                "
                                option-filter-prop="label"
                            >
                                <a-select-option
                                    v-for="vehicule in vehicules"
                                    :key="vehicule.value"
                                    :value="vehicule.value"
                                    :label="vehicule.label"
                                >
                                    {{ vehicule.label }}
                                </a-select-option>
                            </a-select>
                            <a-select
                                v-model:value="filter.chauffeur_id"
                                placeholder="Chauffeur"
                                class="w-full"
                                size="large"
                                allow-clear
                                show-search
                                :filter-option="
                                    (input, option) =>
                                        option.label
                                            .toLowerCase()
                                            .includes(input.toLowerCase())
                                "
                                option-filter-prop="label"
                            >
                                <a-select-option
                                    v-for="chauffeur in chauffeurs"
                                    :key="chauffeur.value"
                                    :value="chauffeur.value"
                                    :label="chauffeur.label"
                                >
                                    {{ chauffeur.label }}
                                </a-select-option>
                            </a-select>
                            <a-select
                                v-model:value="filter.statut"
                                placeholder="Statut expédition"
                                class="w-full"
                                size="large"
                                allow-clear
                            >
                                <a-select-option v-for="(s, key) in STATUT_VOYAGE" :key="key" :value="key">
                                    {{ s.label }}
                                </a-select-option>
                            </a-select>
                            <div class="flex space-x-2 !mt-6">
                                <a-button
                                    block
                                    type="primary"
                                    size="middle"
                                    @click="applyFilters()"
                                    >Appliquer</a-button
                                >
                                <a-button
                                    block
                                    type="default"
                                    size="middle"
                                    @click="closeDropdown"
                                    >Fermer</a-button
                                >
                            </div>
                        </div>
                    </template>
                    <a-button
                        size="large"
                        type="default"
                        class="!rounded-none border-r-0 focus:z-10"
                    >
                        <a-space>
                            <font-awesome-icon
                                class="text-[15px]"
                                icon="fa-filter"
                            />
                            Filtres
                            <DownOutlined />
                        </a-space>
                    </a-button>
                </a-popover>
            </template>
        </FilterBase>
        </template>

        <DataTable
            :columns="columns"
            :data="props.data"
            class="main-shadow"
            :actions="action"
            :show-index="true"
            :btn_action="true"
            :rowSelection="rowSelection"
            rowKey="id"
        >
            <template #bodyCell="{ column, record }">
                <template v-if="column.key==='depart'">
                    {{ record.depart??"-" }}
                </template>
                <template v-if="column.key === 'destination'">
                    {{ record.destination ?? "-" }}
                </template>
                <template v-if="column.key === 'numero_voyage'">
                    {{ record.numero_voyage ?? "-" }}
                </template>
                <template v-if="column.key === 'numero_reservation'">
                    {{ record.reservation?.numero_reservation ?? "-" }}
                </template>
                <template v-if="column.key === 'client'">
                    {{ record.reservation?.client?.nom_client ?? "-" }}
                </template>
                <template v-if="column.key === 'immatriculation_vehicule'">
                    <div>{{ record.matricule_vehicule ?? "-" }}</div>
                    <div v-if="record.numero_remorque" class="text-xs text-gray-400 mt-0.5">
                        Remorque : {{ record.numero_remorque }}
                    </div>
                </template>
                <template v-if="column.key === 'chauffeur'">
                    <div>{{ record.nom_chauffeur ?? "-" }}</div>
                    <div v-if="record.nom_aide_chauffeur" class="text-xs text-gray-400 mt-0.5">
                        Aide : {{ record.nom_aide_chauffeur }}
                    </div>
                </template>
                <template v-if="column.key === 'date_voyage'">
                    {{ dayjs(record.date_voyage).format("DD/MM/YYYY") ?? "-" }}
                </template>
                <template v-else-if="column.key === 'statut'">
                    <span
                        v-if="record.statut"
                        class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold border"
                        :style="{ backgroundColor: STATUT_VOYAGE[record.statut]?.bg, color: STATUT_VOYAGE[record.statut]?.hex, borderColor: STATUT_VOYAGE[record.statut]?.hex + '55' }"
                    >{{ STATUT_VOYAGE[record.statut]?.label ?? record.statut }}</span>
                    <span v-else class="text-gray-300 text-xs">—</span>
                </template>
            </template>
        </DataTable>
        <VoyageFormulaire
            ref="voyageFormModal"
            :vehicules="vehicules"
            :chauffeurs="chauffeurs"
            :clients="clients"
            :tariff-options="tariff_options"
            :carburant-cards="carburantCards"
        />
    </SousMenuPrincipale>

    <GenerationFacture ref="refFacture" />
</template>

<script setup>
import ButtonIcon from "@/Components/ButtonIcon.vue";
import DataTable from "@/Components/DataTable.vue";
import FilterBase from "@/Components/Filter/FilterBase.vue";
import SousMenuPrincipale from "@/Pages/Menu/SousMenuPrincipale.vue";
import GenerationFacture from "@/Pages/Voyage/Formulaire/GenerationFacture.vue";
import VoyageFormulaire from "@/Pages/Voyage/Formulaire/VoyageFormulaire.vue";
import { router } from "@inertiajs/vue3";
import { message } from "ant-design-vue";
import dayjs from "dayjs";
import { computed, ref } from "vue";

import { confirm_delete } from "@/../Utils/confirmation_modal.js";
import usePermissions from "@/UserPermissions/usePermissions.js";
import { createSearchFilter, gotoSearch } from "../../../Utils/FiltreUtils.js";
import { DownOutlined } from "@ant-design/icons-vue";
import ExportData from "@/Components/ExportData/ExportData.vue";
import ExcelImportBaseStandard from "@/Components/ExcelImportBaseStandard.vue";

const { can } = usePermissions();
const voyageFormModal = ref(null);
const selectedRowKeys = ref([]);
const refFacture = ref(null);

const title = computed(
    () => `Historique des voyages (${props.data?.total ?? 0})`
);

const props = defineProps({
    data: { type: Object, default: () => ({}) },
    users: { type: Array, default: () => [] },
    clients: { type: Array, default: () => [] },
    vehicules: { type: Array, default: () => [] },
    chauffeurs: { type: Array, default: () => [] },
    filters: { type: Object, default: () => ({}) },
    flash: { type: Object, default: () => ({}) },
    tariff_options: { type: Array, default: () => [] },
    carburantCards: { type: Array, default: () => [] },
});

const dropdownVisible = ref(false);
const filter = ref({
    ...createSearchFilter(),
    client_id: props.filters.client_id || null,
    vehicule_id: props.filters.vehicule_id || null,
    chauffeur_id: props.filters.chauffeur_id || null,
    statut: props.filters.statut || null,
    start_date: null,
    end_date: null,
});

const applyFilters = () => {
    const url = route("voyages.index");
    gotoSearch(filter.value, url);
    closeDropdown();
};

const resetFilters = () => {
    filter.value.search = "";
    filter.value.client_id = null;
    filter.value.vehicule_id = null;
    filter.value.chauffeur_id = null;
    filter.value.statut = null;
    filter.value.start_date = null;
    filter.value.end_date = null;
    applyFilters();
};

const closeDropdown = () => {
    dropdownVisible.value = false;
};

const onSelectChange = (keys) => {
    selectedRowKeys.value = keys;
};

const rowSelection = computed(() => ({
    selectedRowKeys: selectedRowKeys.value,
    onChange: onSelectChange,
    getCheckboxProps: (record) => ({
        disabled: parseInt(record.facture_client_id) > 0,
    }),
}));

const totalMontant = computed(() => {
    return props.data.data
        .filter((voyage) => selectedRowKeys.value.includes(voyage.id))
        .reduce((sum, voyage) => sum + parseFloat(voyage.montant || 0), 0);
})

// Propriétés calculées pour les totaux
const totalHT = computed(() => {
    return props.data.data
        .filter((voyage) => selectedRowKeys.value.includes(voyage.id))
        .reduce((sum, voyage) => sum + parseFloat(voyage.tarif_ht || 0), 0);
});

const totalTTC = computed(() => {
    return props.data.data
        .filter((voyage) => selectedRowKeys.value.includes(voyage.id))
        .reduce((sum, voyage) => sum + parseFloat(voyage.tarif_ttc || 0), 0);
});

// Propriété calculée pour le client unique sélectionné
const selectedClient = computed(() => {
    const selectedVoyages = props.data.data.filter((voyage) =>
        selectedRowKeys.value.includes(voyage.id)
    );
    if (selectedVoyages.length === 0) {
        return null;
    }
    const firstClient = selectedVoyages[0].reservation?.client;
    const allSameClient = selectedVoyages.every(
        (voyage) => voyage.reservation?.client?.id === firstClient?.id
    );

    return allSameClient
        ? {
              id_client: firstClient?.id,
              nom_client: firstClient?.nom_client,
          }
        : null;
});

const handleAction = () => {
    if (selectedRowKeys.value.length > 0) {
        if (selectedClient.value) {
            const factureData = {
                voyage_ids: selectedRowKeys.value,
                total_ht: parseFloat(totalHT.value),
                montant_payer: parseFloat(totalMontant.value),
                total_ttc: parseFloat(totalTTC.value),
                client_id: selectedClient.value.id_client,
                client_nom: selectedClient.value.nom_client,
            };
            console.log('----------')
            console.log(factureData);
            console.log('----------')

            refFacture.value.add(factureData);
        } else {
            message.error(
                "Veuillez sélectionner des voyages pour un seul et unique client."
            );
        }
    }
};

const handleDelete = (record) => {
    confirm_delete(() => {
        router.delete(route("voyages.destroy", record.id), {
            preserveScroll: true,
        });
    });
};

const STATUT_VOYAGE = {
    planifie:  { label: 'Planifié',  hex: '#6366F1', bg: '#EEF2FF' },
    en_route:  { label: 'En route',  hex: '#3B82F6', bg: '#EFF6FF' },
    arrive:    { label: 'Arrivé',    hex: '#F59E0B', bg: '#FFFBEB' },
    livre:     { label: 'Livré',     hex: '#10B981', bg: '#ECFDF5' },
    annule:    { label: 'Annulé',    hex: '#EF4444', bg: '#FEF2F2' },
};

const columns = ref([
    {
        key: "date_voyage",
        title: "Date Voyage",
        width: 100,
    },
    {
        key:"depart",
        title:"Départ",
        width:100,
    },
    {
        key: "destination",
        title: "Destination",
        width: 100,
    },
    {
        key: "numero_reservation",
        title: "N° Réservation",
        customHeaderCell: () => ({ class: "!text-center whitespace-nowrap" }),
        customCell : ()=>({class:"text-center"}),
        width: 110,
    },
    {
        key: "numero_voyage",
        title: "N° Voyage",
        width: 100,
        customHeaderCell: () => ({ class: "!text-center whitespace-nowrap" }),
        customCell : ()=>({class:"text-center"})
    },
    {
        key: "statut",
        title: "Statut",
        width: 110,
        customHeaderCell: () => ({ class: "!text-center" }),
        customCell: () => ({ class: "text-center" }),
    },
    {
        key: "client",
        title: "Client",
        width: 100,
    },
    {
        key: "immatriculation_vehicule",
        title: "Véhicule / Remorque",
        width: 160,
    },
    {
        key: "chauffeur",
        title: "Chauffeur / Aide",
        width: 150,
    },
]);

const action = [
    {
        text: "Modifier",
        action: (record) => {
            //voyageFormModal.value.open(record);
            try {
                if (record.facture_client_id) {
                    throw new Error("VEUILLEZ ANNULER LA FACTURE AVANT DE POUVOIR MODIFIER CE VOYAGE.");
                }
                voyageFormModal.value.open(record);
            } catch (e) {
                message.error(e.message || "ERREUR INCONNUE");
            }
        },
        icon: "fa-edit",
        privilege: "voyages.update",

    },
    {
        text: "Générer Facture",
        // Envoie maintenant un objet standardisé, comme pour les sélections multiples
        action: (record) => {
            const factureDataSingle = {
                voyage_ids: [record.id],
                total_ht: parseFloat(record.tarif_ht),
                total_ttc: parseFloat(record.tarif_ttc),
                client_id: record.reservation?.client?.id,
                client_nom: record.reservation?.client?.nom_client,
                montant_payer: parseFloat(record.montant)
            };
            refFacture.value.add(factureDataSingle);
        },
        icon: "fa-print",
        privilege: "voyages.facture",
        disabled: (record) => parseInt(record.facture_client_id) > 0,
    },
    {
        text: "Supprimer",
        action: handleDelete,
        icon: "fa-trash",
        class: "!text-red-600",
        disabled: (record) => props.data.total < 1,
        privilege: "voyages.destroy",
    },
];
</script>

<style>
.ant-table-wrapper .ant-table-tbody > tr.ant-table-row-selected > td {
    background-color: #d1e4ffe7 !important;
}

.custom-checkbox-table .ant-checkbox-inner {
    width: 14px;
    height: 14px;
}
</style>
