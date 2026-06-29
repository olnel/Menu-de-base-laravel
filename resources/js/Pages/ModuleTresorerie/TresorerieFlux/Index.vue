<script setup>
import SousMenuPrincipale from "@/Pages/Menu/SousMenuPrincipale.vue";
import DataTable from "@/Components/DataTable.vue";
import ButtonIcon from "@/Components/ButtonIcon.vue";
import { computed, reactive, ref } from "vue";
import { confirm_delete } from "../../../../Utils/confirmation_modal.js";
import { router } from "@inertiajs/vue3";
import FilterBase from "@/Components/Filter/FilterBase.vue";
import { createSearchFilter, gotoSearch } from "../../../../Utils/FiltreUtils.js";
import usePermissions from "@/UserPermissions/usePermissions.js";
import { DownOutlined } from "@ant-design/icons-vue";
import { Space } from "ant-design-vue";


import {formatDate, formatDateTime} from "../../../../Utils/functions.js";
import ExportData from "@/Components/ExportData/ExportData.vue";
import ExcelImportBaseStandard from "@/Components/ExcelImportBaseStandard.vue";

const { can } = usePermissions();
const dropdownVisible = ref(false);
const dateFormat = 'DD/MM/YYYY'; // Format plus standard pour la France
const dateTimeFormat = 'DD/MM/YYYY HH:mm';

const props = defineProps({
    data: {
        type: Object,
        default: () => ({})
    },
    filters: {
        type: Object,
        default: () => ({})
    },
    flash: {
        type: Object,
        default: () => ({})
    },
    tresoreries: {
        type: Array,
        default: () => ([])
    },
    errors: Object
});

const refFormModal = ref();

const filter = ref({ ...createSearchFilter(), start_date: null, end_date: null, tresorerie_id: null });

const title = computed(() => `Trésorerie Flux (${props.data?.total ?? 0})`);

const columns = [
    {
        key: "date_mvt",
        title: "Date",
        dataIndex: "date_mvt",
        width: 100,
        customCell: () => ({ class: 'text-center' })
    },
    {
        key: "libelle_mvt",
        title: "motif",
        dataIndex: "libelle_mvt",
        width: 100,

    },
    {
        key: "poste_depense",
        title: "Poste de dépense",
        dataIndex: "poste_depense",
        width: 150,
        customCell: () => ({ class: 'text-left' })
    },
    {
        key: "nom_tresorerie",
        title: "Trésorerie",
        dataIndex: "nom_tresorerie",
        width: 120,
        customHeaderCell: () => ({ class: "!text-center" }),
        customCell: () => ({ class: 'text-center' })
    },
    {
        key: "nom_tresorerie_cible",
        title: "Trésorerie Cible",
        dataIndex: "nom_tresorerie_cible",
        width: 120,
        customHeaderCell: () => ({ class: "!text-center" }),
        customCell: () => ({ class: 'text-center' })
    },
    {
        key: "type_mvt",
        title: "Opération",
        dataIndex: "type_mvt",
        width: 120,
        customHeaderCell: () => ({ class: "!text-center" }),
        customCell: () => ({ class: 'text-center' })
    },
    {
        key: "solde_avant",
        title: "Solde Avant",
        dataIndex: "solde_avant",
        width: 100,
        customCell: () => ({ class: 'text-right' })
    },
    {
        key: "montant",
        title: "Montant",
        dataIndex: "montant",
        width: 100,
        customHeaderCell: () => ({ class: "!text-right" }),
        customCell: () => ({ class: 'text-right' })
    },
    {
        key: "solde_final",
        title: "Solde Après",
        dataIndex: "solde_final",
        width: 100,
        customCell: () => ({ class: 'text-right' })
    },
    {
        key: "commentaire",
        title: "Commenaire",
        dataIndex: "commentaire",
        width: 100
    },

    {
        key: "nom_user",
        title: "Utilisateur",
        dataIndex: "nom_user",
        width: 100,
        customHeaderCell: () => ({ class: "!text-center" }),
        customCell: () => ({ class: 'text-center' })
    },
    {
        key: "created_at",
        title: "Date création",
        dataIndex: "created_at",
        width: 120,
        customHeaderCell: () => ({ class: "!text-center" }),
        customCell: () => ({ class: 'text-center' })
    },
];



const applyFilters = (data) => {
    filter.value = data;
    const url = route('tresorerieflux.index');
    gotoSearch(filter.value, url);
};

const resetFilters = () => {
    filter.value = { ...createSearchFilter(), start_date: null, end_date: null, tresorerie_id: null };
    const url = route('tresorerieflux.index');
    gotoSearch(filter.value, url);
};

</script>

<template>
    <SousMenuPrincipale :title="title" selectedMenu="tresorerieflux">
        <template #top>
            <FilterBase
            v-model="filter"
            @search="applyFilters"
            @reset="resetFilters"
        >
            <template #otherFilter>
                <a-popover
                    placement="bottomRight"
                    trigger="click"
                    :visible="dropdownVisible"
                    @visibleChange="val => dropdownVisible = val"
                >
                    <template #content>
                        <div class="bg-white p-4 w-96 space-y-2 rounded-md">
                            <a-date-picker
                                v-model:value="filter.start_date"
                                :format="dateFormat"
                                size="large"
                                class="w-full"
                                placeholder="Du"
                                :value-format="'DD-MM-YYYY'"
                            />
                            <a-date-picker
                                v-model:value="filter.end_date"
                                :format="dateFormat"
                                size="large"
                                class="w-full"
                                placeholder="Au"
                                :value-format="'DD-MM-YYYY'"
                            />
                            <a-select
                                v-model:value="filter.tresorerie_id"
                                placeholder="Trésorerie"
                                :options="props.tresoreries"
                                class="w-full"
                                size="large"
                                allow-clear
                            />
                            <div class="flex space-x-2 !mt-6">
                                <a-button block type="primary" size="middle" @click="applyFilters(filter)">Appliquer</a-button>
                                <a-button block type="default" size="middle" @click="dropdownVisible = false">Fermer</a-button>
                            </div>
                        </div>
                    </template>
                    <a-button size="large" type="default" class="!rounded-none border-r-0 focus:z-10">
                        <Space>
                            <font-awesome-icon class="text-[15px]" icon="fa-filter" />
                            Filtres
                            <DownOutlined />
                        </Space>
                    </a-button>
                </a-popover>
            </template>

            <template #import v-if="can('export_tresorerie_flux.export')">
                <ExportData :show_import="false" :title="'Export data'" :columns="columns" :filter="filter" :url="route('tresorerie_flux.export')" >
                    <template #import >
                        <excel-import-base-standard :columns="columns" model="App\Model\Tresorerie"></excel-import-base-standard>
                    </template>
                </ExportData>
            </template>
        </FilterBase>
        </template>

        <DataTable
            :columns="columns"
            :data="data"
            class="main-shadow"
            :show-index="true"
            :btn_action="false"
        >
            <template #bodyCell="{ column, record }">
                <!-- Formatage des dates -->
                <template v-if="column.key === 'date_mvt'">
                    {{ formatDate(record.date_mvt) }}
                </template>

                <template v-if="column.key === 'created_at'">
                    {{ formatDateTime(record.created_at) }}
                </template>

                <!-- Formatage des montants -->
                <template v-if="column.key === 'montant'">
                    {{ new Intl.NumberFormat('fr-FR').format(record.montant) }}
                </template>
                <template v-if="column.key === 'solde_avant'">
                    {{ new Intl.NumberFormat('fr-FR').format(record.solde_avant) }}
                </template>
                <template v-if="column.key === 'solde_final'">
                    {{ new Intl.NumberFormat('fr-FR').format(record.solde_final) }}
                </template>
            </template>
        </DataTable>


    </SousMenuPrincipale>
</template>

<style scoped>
/* Styles spécifiques si nécessaire */
</style>
