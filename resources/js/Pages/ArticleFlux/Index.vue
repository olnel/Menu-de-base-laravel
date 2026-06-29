<script setup>
import SousMenuPrincipale from "@/Pages/Menu/SousMenuPrincipale.vue";
import DataTable from "@/Components/DataTable.vue";
import ExcelImportBaseStandard from "@/Components/ExcelImportBaseStandard.vue";
import ExportData from "@/Components/ExportData/ExportData.vue";
import { computed, ref } from "vue";
import FilterBase from "@/Components/Filter/FilterBase.vue";
import {createSearchFilter, gotoSearch} from "../../../Utils/FiltreUtils.js";
import usePermissions from "@/UserPermissions/usePermissions.js";
import {DownOutlined} from "@ant-design/icons-vue";
import {Space} from "ant-design-vue";
import dayjs from "dayjs";
const { can } = usePermissions()
const dropdownVisible = ref(false);
const dateFormat = 'YYYY/MM/DD';

const props = defineProps({
    data: {
        type: Object,
        default: () => ({})
    },
    magasins: {
        type: Array,
        default: []
    },
    filters: {
        type: Object,
        default: () => ({})
    },
    flash: {
        type: Object,
        default: () => ({})
    },
    famille_articles: {
        type: Array,
        default: () => ({})
    },
    errors: Object
});




const filter = ref({...createSearchFilter(), start_date: null, end_date: null, article_famille_id: null, magasin_id: null})

const title = computed(() => `Flux Articles (${props.data?.total ?? 0})`);

const columns = [
    { key: "date_mvt", title: "date", dataIndex: "date_mvt", width: 150, customCell: () => ({ class: 'text-center' }),customHeaderCell:()=>({class:'!text-center'})  },
    { key: "nom_magasin", title: "Magasin", dataIndex: "nom_magasin", width: 120 },
    { key: "nom_famille_article", title: "Famille Article", dataIndex: "nom_famille_article", width: 120 },
    { key: "designation", title: "Désignation", dataIndex: "designation", width: 120 },
    { key: "reference", title: "Référence", dataIndex: "reference", width: 120 },
    { key: "operation_mvt", title: "Opération", dataIndex: "operation_mvt", width: 120,customCell: () => ({ class: 'text-center' }),customHeaderCell:()=>({class:'!text-center'}) },
    { key: "reference_mvt", title: "Reférence Mouvement", dataIndex: "reference_mvt", width: 200},
    { key: "qte_initial", title: "Qte.Init", dataIndex: "qte_initial", width: 100, customCell: () => ({ class: 'text-center' }),customHeaderCell:()=>({class:'!text-center'}) },
    { key: "qte_mvt", title: "Qte.mvt", dataIndex: "qte_mvt", width: 160, customCell: () => ({ class: 'text-center' }),customHeaderCell:()=>({class:'!text-center'}) },
    { key: "qte_final", title: "Qte.Final", dataIndex: "qte_final", width: 160, customCell: () => ({ class: 'text-center' }),customHeaderCell:()=>({class:'!text-center'}) },
    { key: "commentaire_mvt", title: "Commentaire", dataIndex: "commentaire_mvt", width: 200 },
    { key: "nom_user", title: "Utilisateur", dataIndex: "nom_user", width: 100, customCell: () => ({ class: 'text-left' }) },
    { key: "date_heure_enregistrement", title: "Date Enregistrement", dataIndex: "date_heure_enregistrement", width: 150, customCell: () => ({ class: 'text-center' }),customHeaderCell:()=>({class:'!text-center'}) },
];



const applyFilters = (data) => {
    filter.value = data;
    const url = route('article_flux.index');
    gotoSearch(filter.value, url)
};

const closeDropdown = ()=> {
    dropdownVisible.value = false;
}

const resetFilters = () => {
    filter.value.search = ""
    filter.value.start_date = null;
    filter.value.end_date = null;
    filter.value.article_famille_id = null;
    filter.value.magasin_id = null;
    const url = route('article_flux.index');
    gotoSearch(filter.value, url)
};


</script>

<template>
    <SousMenuPrincipale :title="title" selectedMenu="article_flux">
        <template #top>
            <FilterBase v-model="filter"
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
                                v-model:value="filter.article_famille_id"
                                placeholder="Famille d'article"
                                class="w-full"
                                size="large"
                                allow-clear
                            >
                                <a-select-option
                                    v-for="type in props.famille_articles"
                                    :key="type.value"
                                    :value="type.value"
                                >
                                    {{ type.label }}
                                </a-select-option>
                            </a-select>

                            <a-select
                                v-model:value="filter.magasin_id"
                                placeholder="Liste Magasin"
                                class="w-full"
                                size="large"
                                allow-clear
                            >
                                <a-select-option
                                    v-for="type in props.magasins"
                                    :key="type.value"
                                    :value="type.value"
                                >
                                    {{ type.label }}
                                </a-select-option>
                            </a-select>
                            <div class="flex space-x-2 !mt-6">
                                <a-button block type="primary" size="middle" @click="applyFilters(filter)">Appliquer</a-button>
                                <a-button block type="default" size="middle" @click="closeDropdown">Fermer</a-button>
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

            <template #import v-if="can('export_article_flux.export')">
                <ExportData :show_import="false" :title="'Export data'" :columns="columns" :filter="filter" :url="route('article_flux.export')">
                    <template #import>
                        <excel-import-base-standard :columns="columns" model="test"/>
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
            <template #bodyCell="{ column, record}">
                <template v-if="column.key === 'date_mvt'">
                    {{ record.date_mvt ? dayjs(record.date_mvt).format('DD/MM/YYYY') : ''}}
                </template>
                <template v-if="column.key === 'date_heure_enregistrement'">
                    {{ record.date_heure_enregistrement ? dayjs(record.date_heure_enregistrement).format('DD/MM/YYYY HH:mm:ss') : ''}}
                </template>
            </template>


        </DataTable>


    </SousMenuPrincipale>
</template>

<style scoped>

</style>
