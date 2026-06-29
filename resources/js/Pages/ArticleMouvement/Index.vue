<script setup>
import SousMenuPrincipale from "@/Pages/Menu/SousMenuPrincipale.vue";
import DataTable from "@/Components/DataTable.vue";
import ButtonIcon from "@/Components/ButtonIcon.vue";
import ExcelImportBaseStandard from "@/Components/ExcelImportBaseStandard.vue";
import ExportData from "@/Components/ExportData/ExportData.vue";
import { computed, ref } from "vue";
import { confirm_delete } from "../../../Utils/confirmation_modal.js";
import { router } from "@inertiajs/vue3";
import FilterBase from "@/Components/Filter/FilterBase.vue";
import { createSearchFilter, gotoSearch } from "../../../Utils/FiltreUtils.js";
import usePermissions from "@/UserPermissions/usePermissions.js";
import { DownOutlined } from "@ant-design/icons-vue";
import { Space } from "ant-design-vue";
import dayjs from "dayjs";
import ArticleMouvementFormulaire from "@/Pages/ArticleMouvement/Formulaire/ArticleMouvementFormulaire.vue";

const { can } = usePermissions();
const dropdownVisible = ref(false);
const dateFormat = 'YYYY/MM/DD';

const props = defineProps({
    data: {
        type: Object,
        default: () => ({})
    },
    magasins: {
        type: Array,
        default: () => []
    },
    vehicules: {
        type: Array,
        default: () => []
    },
    filters: {
        type: Object,
        default: () => ({})
    },
    flash: {
        type: Object,
        default: () => ({})
    },
    errors: Object
});

const refFormModal = ref();

const filter = ref({ ...createSearchFilter(), start_date: null, end_date: null, magasin_id: null });

const title = computed(() => `Liste Article Mouvements (${props.data?.total ?? 0})`);

const columns = [
    { key: "date_transaction",        title: "Date",               dataIndex: "date_transaction",        width: 120, customCell: () => ({ class: 'text-center' }), customHeaderCell: () => ({ class: '!text-center' }) },
    { key: "reference_mouvement",     title: "Référence",          dataIndex: "reference_mouvement",     width: 120, customCell: () => ({ class: '!text-center' }), customHeaderCell: () => ({ class: '!text-center' }) },
    { key: "nom_magasin",             title: "Magasin",            dataIndex: "nom_magasin",             width: 120 },
    { key: "immatriculation",         title: "N° Immatriculation", dataIndex: "immatriculation",         width: 160, customCell: () => ({ class: 'text-center' }), customHeaderCell: () => ({ class: '!text-center' }) },
    { key: "nom_magasin_cible",       title: "Magasin Cible",      dataIndex: "nom_magasin_cible",       width: 120 },
    { key: "nom_user",                title: "Utilisateur",        dataIndex: "nom_user",                width: 100 },
    { key: "date_heure_enregistrement", title: "Date.Enr",         dataIndex: "date_heure_enregistrement", width: 150 },
];

const handleDelete = (record) => {
    confirm_delete(() => {
        router.delete(route('article_mouvement.destroy', record.id), {
            preserveScroll: true,
        });
    });
};

const actions = [
    {
        text: "Modifier",
        action: (record) => refFormModal.value.update(record),
        icon: 'fa-pen-to-square',
        privilege: 'article_mouvement.update'
    },
    {
        text: "Supprimer",
        action: handleDelete,
        icon: 'fa-trash',
        class: "!text-red-600",
        disabled: (record) => record.is_you || props.data.total < 1,
        privilege: 'article_mouvement.destroy'
    },
];

const applyFilters = (data) => {
    filter.value = data;
    closeDropdown();
    const url = route('article_mouvement.index');
    gotoSearch(filter.value, url);
};

const closeDropdown = () => {
    dropdownVisible.value = false;
};

const resetFilters = () => {
    filter.value.search = "";
    filter.value.start_date = null;
    filter.value.end_date = null;
    filter.value.magasin_id = null;
    const url = route('article_mouvement.index');
    gotoSearch(filter.value, url);
};
</script>

<template>
    <SousMenuPrincipale :title="title" selectedMenu="article_mouvement">
        <template #top>
            <FilterBase v-model="filter" @search="applyFilters" @reset="resetFilters">
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
                                v-model:value="filter.magasin_id"
                                placeholder="Magasin"
                                :options="props.magasins"
                                class="w-full"
                                size="large"
                                allow-clear
                            />
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

            <template #import v-if="can('export_article_mouvement.export')">
                <ExportData :show_import="false" :title="'Export data'" :columns="columns" :filter="filter" :url="route('article_mouvement.export')">
                    <template #import>
                        <excel-import-base-standard :columns="columns" model="test"/>
                    </template>
                </ExportData>
            </template>

            <template #add>
                <ButtonIcon
                    v-if="can('article_mouvement.store')"
                    @click="() => refFormModal.add()"
                    type="primary"
                    text="Nouveau Mouvement"
                    icon="fa-plus"
                    class="!rounded-md hover:!text-white hover:!bg-sky-800 transition-all duration-300"
                />
            </template>
        </FilterBase>
        </template>

        <DataTable
            :columns="columns"
            :data="data"
            :actions="actions"
            class="main-shadow"
            :show-index="true"
            :btn_action="true"
        >
            <template #bodyCell="{ column, record }">
                <template v-if="column.key === 'date_transaction'">
                    {{ record.date_transaction ? dayjs(record.date_transaction).format('DD/MM/YYYY') : '' }}
                </template>
                <template v-else-if="column.key === 'date_heure_enregistrement'">
                    {{ record.date_heure_enregistrement ? dayjs(record.date_heure_enregistrement).format('DD/MM/YYYY HH:mm:ss') : '' }}
                </template>
            </template>
        </DataTable>

        <ArticleMouvementFormulaire
            :magasins="magasins"
            :vehicules="vehicules"
            ref="refFormModal"
        />

    </SousMenuPrincipale>
</template>

<style scoped></style>
