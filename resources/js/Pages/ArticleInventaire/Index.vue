<script setup>
import SousMenuPrincipale from "@/Pages/Menu/SousMenuPrincipale.vue";
import DataTable from "@/Components/DataTable.vue";
import ButtonIcon from "@/Components/ButtonIcon.vue";
import ExcelImportBaseStandard from "@/Components/ExcelImportBaseStandard.vue";
import ExportData from "@/Components/ExportData/ExportData.vue";
import { computed, ref } from "vue";
import { confirm_delete } from "../../../Utils/confirmation_modal.js";
import { router } from "@inertiajs/vue3";
import ArticleInventaireFormulaire from '@/Pages/ArticleInventaire/Formulaire/ArticleInventaireFormulaire.vue'
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

const refFormModal = ref();
const formPhotoModal = ref();

const filter = ref({...createSearchFilter(), start_date: null, end_date: null, article_famille_id: null, magasin_id: null})

const title = computed(() => `Historique des Inventaires (${props.data?.total ?? 0})`);

const columns = [
    { key: "date_inventaire", title: "date", dataIndex: "date_inventaire", width: 150, customCell: () => ({ class: 'text-center' }),customHeaderCell:()=>({class:'!text-center'})  },
    { key: "nom_magasin", title: "Magasin", dataIndex: "nom_magasin", width: 120 },
    { key: "nom_famille_article", title: "Famille Article", dataIndex: "nom_famille_article", width: 120 },
    { key: "designation", title: "Désignation", dataIndex: "designation", width: 120 },
    { key: "reference", title: "Référence", dataIndex: "reference", width: 120 },
    { key: "stock_theorique", title: "S.Théorique", dataIndex: "stock_theorique", width: 100, customCell: () => ({ class: 'text-center' }),customHeaderCell:()=>({class:'!text-center'}) },
    { key: "stock_reel", title: "S. Réel", dataIndex: "stock_reel", width: 160, customCell: () => ({ class: 'text-center' }) ,customHeaderCell:()=>({class:'!text-center'})},
    { key: "ecart_stock", title: "Ecart", dataIndex: "ecart_stock", width: 160, customCell: () => ({ class: 'text-center' }) ,customHeaderCell:()=>({class:'!text-center'})},
    { key: "remarque", title: "Remarque", dataIndex: "remarque", width: 100, customCell: () => ({ class: 'text-left' }) },
    { key: "nom_user", title: "Utilisateur", dataIndex: "nom_user", width: 100, customCell: () => ({ class: 'text-left' }) },
    { key: "date_heure_enregistrment", title: "Date.Enr", dataIndex: "date_heure_enregistrment", width: 100, },
];



const applyFilters = (data) => {
    filter.value = data;
    const url = route('article_inventaire.index');
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
    const url = route('article_inventaire.index');
    gotoSearch(filter.value, url)
};


</script>

<template>
    <SousMenuPrincipale :title="title" selectedMenu="article_inventaire">
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

            <template #import v-if="can('export_article_inventaire.export')">
                <ExportData :show_import="false" :title="'Export data'" :columns="columns" :filter="filter" :url="route('article_inventaire.export')">
                    <template #import>
                        <excel-import-base-standard :columns="columns" model="test"/>
                    </template>
                </ExportData>
            </template>

            <template #add>
                <ButtonIcon v-if="can('article_inventaire.store')" @click="() => refFormModal.add()" type="primary" text="Nouveau Inventaire" icon="fa-plus" class="!rounded-md hover:!text-white hover:!bg-sky-800 transition-all duration-300"/>
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
                <template v-if="column.key === 'date_inventaire'">
                    {{ record.date_inventaire ? dayjs(record.date_inventaire).format('DD/MM/YYYY') : ''}}
                </template>
                <template v-if="column.key === 'date_heure_enregistrment'">
                    {{ record.date_heure_enregistrment ? dayjs(record.date_heure_enregistrment).format('DD/MM/YYYY HH:mm:ss') : ''}}
                </template>
            </template>
        </DataTable>

        <ArticleInventaireFormulaire
            :magasins="magasins"
            ref="refFormModal"
        />

    </SousMenuPrincipale>
</template>

<style scoped>

</style>
