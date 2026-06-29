<script>
const dateFormat = 'YYYY/MM/DD';
const STATUT_FACTURE = [
    {label: 'Brouillon', value: 'Brouillon'},
    {label: 'envoyée', value: 'envoyée'},
    {label: 'Partiellement payée', value: 'Partiellement payée'},
    {label: 'Payée', value: 'Payée'}
]
</script>

<script setup>
import { computed, ref } from "vue";
import { router } from "@inertiajs/vue3";


import SousMenuPrincipale from "@/Pages/Menu/SousMenuPrincipale.vue";
import DataTable from "@/Components/DataTable.vue";
import FilterBase from "@/Components/Filter/FilterBase.vue";
import GenerationFacture from "@/Pages/Voyage/Formulaire/GenerationFacture.vue";
import ReglementFacture from "@/Pages/FactureClient/Reglement/ReglementFacture.vue";

import usePermissions from "@/UserPermissions/usePermissions.js";
import { confirm_delete } from "@/../Utils/confirmation_modal.js";
import { createSearchFilter, gotoSearch } from "../../../Utils/FiltreUtils.js";

import { DownOutlined } from "@ant-design/icons-vue";
import {formatDate, formatDateTime} from "../../../Utils/functions.js";
import HistoriqueReglement from "@/Pages/FactureClient/HistoriqueReglement/HistoriqueReglement.vue";

import ExportData from "@/Components/ExportData/ExportData.vue";
import ExcelImportBaseStandard from "@/Components/ExcelImportBaseStandard.vue";

const { can } = usePermissions();
const voyageFormModal = ref(null);
const selectedRowKeys = ref([]);
const refFacture = ref(null);
const reglementFacture = ref(null);
const historiquereglement = ref(null);

const title = computed(
    () => `Facture Client (${props.data?.total ?? 0})`
);

const props = defineProps({
    data: { type: Object, default: () => ({}) },
    users: { type: Array, default: () => [] },
    clients: { type: Array, default: () => [] },
    filters: { type: Object, default: () => ({}) },
    flash: { type: Object, default: () => ({}) },
    tresoreries: { type: Array, default: () => [] },
});

const dropdownVisible = ref(false);
const filter = ref({
    ...createSearchFilter(),
    client_id: props.filters.client_id || null,
    statut_facture: props.filters.statut_facture || null,
    start_date: null,
    end_data: null,
});

const applyFilters = () => {
    const url = route("factureclient.index");
    gotoSearch(filter.value, url);
    closeDropdown();
};

const resetFilters = () => {
    filter.value.search = "";
    filter.value.client_id = null;
    filter.value.statut_facture = null;
    filter.value.start_date = null;
    filter.value.end_data = null;
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
}));


const handleDelete = (record) => {
    confirm_delete(() => {
        router.delete(route("voyages.destroy", record.id), {
            preserveScroll: true,
        });
    });
};

const columns = ref([
    {key: "date_facture", title: "date ", dataIndex: "date_facture", width: 100,customCell : ()=>({class:"text-center"}),customHeaderCell : ()=>({class:"!text-center"})},
    {key: "numero_facture", title: "N° Facture",dataIndex: "numero_facture",  width: 100,customCell : ()=>({class:"text-center"}),customHeaderCell : ()=>({class:"!text-center"})},
    {key: "nom_client", title: "Client", dataIndex: "nom_client"},
    {key: "statut_facture", title: "Etat Facture", dataIndex: 'statut_facture', width: 150, customCell: () => ({ class: 'text-center' }),customHeaderCell : ()=>({class:"!text-center"})},
    {key: "montant_ht", title: "Montant HT", dataIndex: 'montant_ht', width: 150, customCell: () => ({ class: 'text-right' }),customHeaderCell : ()=>({class:"!text-right"})},
    {key: "montant_tva", title: "Montant TVA", dataIndex: 'montant_tva', width: 150, customCell: () => ({ class: 'text-right' }),customHeaderCell : ()=>({class:"!text-right"})},
    {key: "montant_ttc", title: "Montant TTC", dataIndex: 'montant_ttc', width: 150, customCell: () => ({ class: 'text-right' }),customHeaderCell : ()=>({class:"!text-right"})},
    {key: "montant_payer", title: "Montant Payer", dataIndex: 'montant_payer', width: 150, customCell: () => ({ class: 'text-right' }),customHeaderCell : ()=>({class:"!text-right"})},
    {key: "montant_reste_a_payer", title: "Reste à Payer", dataIndex: 'montant_reste_a_payer', width: 200, customCell: () => ({ class: 'text-right' }),customHeaderCell : ()=>({class:"!text-right"})},
    {key: "nom_user", title: "Utilisateur", dataIndex: 'nom_user', width: 200, customCell: () => ({ class: 'text-left' }),customHeaderCell : ()=>({class:"!text-left"})},
    {key: "created_at", title: "Date Enregistrement", dataIndex: 'created_at', width: 200,customCell : ()=>({class:"text-center"}),customHeaderCell : ()=>({class:"!text-center"})},

]);

const printFacture = (record) => {
    const link = document.createElement('a');
    link.href = route('factureclient.print', { factureclient: record.id });
    link.target = '_blank';
    link.download = `factureclient-${record.numero_facture}.pdf`;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);

    // router.visit(`${route("factureclient.print", { factureclient: record.id })}`, {
    //     preserveState: true,
    //     preserveScroll: true,
    //     only: ["selectedgroup"],
    //     onSuccess: () => {
    //         Object.assign(form, props.selectedgroup);
    //     },
    // });
};

const sendMail = record => {
    const url = route('factureclient.sendMail', { factureclient: record.id });

    router.visit(url, {
        preserveState: true,
        preserveScroll: true,
        onSuccess: (response) => {
            console.log(response)
        },
    });
}

const action = [
    {
        text: "Modifier",
        action: (record) => refFacture.value.update(record),
        icon: "fa-edit",
        privilege: "factureclient.update",
    },
    {
        text: "Générer Facture",
        action: (record) => printFacture(record),
        icon: "fa-print",
        privilege: "factureclient.generer_facture",
    },
    {
        text: "Envoi mail ",
        action: (record) => sendMail(record),
        icon: 'fa-paper-plane',
        privilege: "factureclient.envoi_email",
    },
    {
        text: "Regler la Facture ",
        action: (record) => reglementFacture.value.reglement(record),
        icon: 'fa-credit-card',
        privilege: "factureclient.regler_facture",
        disabled: (record) => record.statut_facture ==='Brouillon' || record.statut_facture === 'Payée',
    },
    {
        text: "Voir Historique de Réglement",
        action: (record) => historiquereglement.value.voirReglement(record),
        icon: 'fa-file-invoice',
        privilege: "factureclient.historique_reglement",
        disabled: (record) => parseFloat(record.montant_payer) <= 0,
    },
    {
        text: "Supprimer",
        action: handleDelete,
        icon: "fa-trash",
        class: "!text-red-600",
        disabled: (record) => parseFloat(record.montant_payer) > 0,
        privilege: "factureclient.destroy",
    },
];
</script>

<template>
    <SousMenuPrincipale :title="title" selectedMenu="factureclient">
        <template #top>
            <FilterBase v-model="filter" @search="applyFilters" @reset="resetFilters">
            <template #otherFilter>
                <a-popover placement="bottomRight" trigger="click" :visible="dropdownVisible" @visibleChange="(val) => (dropdownVisible = val)">
                    <template #content>
                        <div class="bg-white p-4 w-96 space-y-2 rounded-md">
                            <a-date-picker v-model:value="filter.start_date" :format="dateFormat" size="large" class="w-full text-center" placeholder="Du" :value-format="'DD-MM-YYYY'" />
                            <a-date-picker v-model:value="filter.end_date" :format="dateFormat" size="large" class="w-full text-center" placeholder="Au" :value-format="'DD-MM-YYYY'" />
                            <a-select v-model:value="filter.client_id" placeholder="Client" class="w-full" size="large" allow-clear show-search
                                      :filter-option="(input, option) => option.label.toLowerCase().includes(input.toLowerCase())" option-filter-prop="label">
                                <a-select-option v-for="client in clients" :key="client.value" :value="client.value" :label="client.label">
                                    {{ client.label }}
                                </a-select-option>
                            </a-select>
                            <a-select v-model:value="filter.statut_facture" placeholder="Statut Facture" class="w-full" size="large" allow-clear show-search
                                      :filter-option="(input, option) => option.label.toLowerCase().includes(input.toLowerCase())" option-filter-prop="label">
                                <a-select-option v-for="statut in STATUT_FACTURE" :key="statut.value" :value="statut.value" :label="statut.label">
                                    {{ statut.label }}
                                </a-select-option>
                            </a-select>
                            <div class="flex space-x-2 !mt-6">
                                <a-button block type="primary" size="middle" @click="applyFilters()">Appliquer</a-button>
                                <a-button block type="default" size="middle" @click="closeDropdown">Fermer</a-button>
                            </div>
                        </div>
                    </template>
                    <a-button size="large" type="default" class="!rounded-none border-r-0 focus:z-10">
                        <a-space>
                            <font-awesome-icon class="text-[15px]" icon="fa-filter" />
                            Filtres
                            <DownOutlined />
                        </a-space>
                    </a-button>
                </a-popover>
            </template>

            <template #import v-if="can('export_facture_client.export')">
                <ExportData :show_import="false" :title="'Export data'" :columns="columns" :filter="filter" :url="route('facture_client.export')" >
                        <template #import >
                            <excel-import-base-standard :columns="columns" model="App\Model\FactureClient"></excel-import-base-standard>
                        </template>
                </ExportData>
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
            rowKey="id"
        >
            <template #expandedRowRender="{ record }">
                <div class="bg-gray-50 !py-3 !px-3 rounded-lg shadow-inner">
                    <h4 class="text-md font-bold text-gray-800 mb-4">Détails des voyages associés</h4>

                    <div class="overflow-x-auto rounded-md border border-gray-200">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-100">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Numéro
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Date Voyage
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Camion
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Remorque
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Chauffeur
                                </th>
                            </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                            <tr
                                v-for="(v, index) in record.voyages"
                                :key="index"
                                class="hover:bg-gray-50 transition-colors duration-150"
                            >
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ v.numero_voyage ?? "-" }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ v.date_voyage ?? "-" }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ v.matricule_vehicule ?? "-" }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ v.numero_remorque ?? "-" }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ v.nom_chauffeur ?? "-" }}
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </template>

            <template #bodyCell="{ column, record }">
                <template v-if="column.key === 'date_facture'">
                    {{ formatDate(record.date_facture) }}
                </template>
                <template v-if="column.key === 'montant_ht'">
                    {{ new Intl.NumberFormat().format(record.montant_ht) }}
                </template>
                <template v-if="column.key === 'montant_tva'">
                    {{ new Intl.NumberFormat().format(record.montant_tva) }}
                </template>
                <template v-if="column.key === 'montant_ttc'">
                    {{ new Intl.NumberFormat().format(record.montant_ttc) }}
                </template>
                <template v-if="column.key === 'montant_payer'">
                    {{ new Intl.NumberFormat().format(record.montant_payer) }}
                </template>
                <template v-if="column.key === 'montant_reste_a_payer'">
                    {{ new Intl.NumberFormat().format(record.montant_reste_a_payer) }}
                </template>
                <template v-if="column.key === 'statut_facture'">
                    <span :class="{
                        'text-green-600': record.statut_facture === 'Payée',
                        'text-red-600': record.statut_facture === 'Brouillon',
                        'text-yellow-600': record.statut_facture === 'En attente',
                    }">
                        {{ record.statut_facture }}
                    </span>
                </template>

                <template v-if="column.key === 'created_at'">
                    {{ formatDateTime(record.created_at) }}
                </template>
            </template>
        </DataTable>
    </SousMenuPrincipale>

    <GenerationFacture ref="refFacture" />

    <ReglementFacture
        ref="reglementFacture"
        :tresoreries="props.tresoreries"
    />

    <HistoriqueReglement ref="historiquereglement" />
</template>


<style>
.ant-table-wrapper .ant-table-tbody > tr.ant-table-row-selected > td {
    background-color: #d1e4ffe7 !important;
}

.custom-checkbox-table .ant-checkbox-inner {
    width: 14px;
    height: 14px;
}
</style>
