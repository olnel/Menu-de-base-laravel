<script setup>
import SousMenuPrincipale from "@/Pages/Menu/SousMenuPrincipale.vue";
import DataTable from "@/Components/DataTable.vue";
import ButtonIcon from "@/Components/ButtonIcon.vue";
import { computed, ref } from "vue";
import { confirm_delete } from "../../../Utils/confirmation_modal.js";
import { router } from "@inertiajs/vue3";
import FilterBase from "@/Components/Filter/FilterBase.vue";
import {createSearchFilter, gotoSearch} from "../../../Utils/FiltreUtils.js";
import usePermissions from "@/UserPermissions/usePermissions.js";
import {DownOutlined} from "@ant-design/icons-vue";
import {Space} from "ant-design-vue";
import ArticleApprovisionnemntFormulaire
    from "@/Pages/ArticleApprovisionnement/Formulaire/ArticleApprovisionnemntFormulaire.vue";
import dayjs from "dayjs";

import ExportData from "@/Components/ExportData/ExportData.vue";
import ExcelImportBaseStandard from "@/Components/ExcelImportBaseStandard.vue";

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

    fournisseurs: {
        type: Array,
        default: () => ({})
    },
    errors: Object
});

const refFormModal = ref();
const formPhotoModal = ref();

const filter = ref({...createSearchFilter(), start_date: null, end_date: null, magasin_id: null})

const title = computed(() => `Historique des Approvisionnements (${props.data?.total ?? 0})`);

const columns = [
    { key: "date_appro", title: "date", dataIndex: "date_appro", width: 150, customCell: () => ({ class: 'text-center' })  },
    { key: "nom_magasin", title: "Magasin", dataIndex: "nom_magasin", width: 120 },
    { key: "nom_fournisseur", title: "Fournisseur", dataIndex: "nom_fournisseur", width: 120 },
    { key: "numero_bon_livraison", title: "N° Bon Livraison", dataIndex: "numero_bon_livraison", width: 140 },
    { key: "numero_bon_commande", title: "N° Bon Commande", dataIndex: "numero_bon_commande", width: 140 },
    { key: "montant_ht_appro", title: "Montant HT", dataIndex: "montant_ht_appro", width: 120, customCell: () => ({ class: 'text-right' }) },
    { key: "remise_general_ariary", title: "Remise", dataIndex: "remise_general_ariary", width: 120, customCell: () => ({ class: 'text-right' }) },
    { key: "montant_tva_appro", title: "Montant TVA", dataIndex: "montant_tva_appro", width: 120, customCell: () => ({ class: 'text-right' }) },
    { key: "montant_ttc_appro", title: "Montant TTC", dataIndex: "montant_ttc_appro", width: 120, customCell: () => ({ class: 'text-right' }) },
    { key: "nom_user", title: "Utilisateur", dataIndex: "nom_user", width: 100, customCell: () => ({ class: 'text-left' }) },
    { key: "date_heure_enregistrement", title: "date.Enr", dataIndex: "date_heure_enregistrement", width: 150, customCell: () => ({ class: 'text-center' })  },
];

const handleDelete = (record)=> {
    confirm_delete(() => {
        router.delete(route('article_approvisionnement.destroy', record.id), {
            preserveScroll: true,
        });
    });
}

const actions = [
    {
        text: "Modifier",
        action: (record) => refFormModal.value.update(record),
        icon: 'fa-pen-to-square',
        privilege: 'article_approvisionnement.update'
    },
    {
        text: "Supprimer",
        action: handleDelete,
        icon: 'fa-trash',
        class:"!text-red-600",
        disabled: (record) => record.is_you || props.data.total < 1,
        privilege: 'article_approvisionnement.destroy'
    },
];


const applyFilters = (data) => {
    filter.value = data;
    const url = route('article_approvisionnement.index');
    gotoSearch(filter.value, url)
};

const closeDropdown = ()=> {
    dropdownVisible.value = false;
}

const resetFilters = () => {
    filter.value.search = ""
    filter.value.start_date = null;
    filter.value.end_date = null;
    filter.value.magasin_id = null;
    const url = route('article_approvisionnement.index');
    gotoSearch(filter.value, url)
};


</script>

<template>
    <SousMenuPrincipale :title="title" selectedMenu="article_approvisionnement">
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

            <template #add>
                <ButtonIcon v-if="can('article_approvisionnement.store')" @click="() => refFormModal.add()" type="primary" text="Nouveau Appro" icon="fa-plus" class="!rounded-md hover:!text-white hover:!bg-sky-800 transition-all duration-300"/>
            </template>

            <template # v-if="can('export_article_approvisionnement.export')">
                <ExportData :show_import="false" :title="'Export data'" :columns="columns" :filter="filter" :url="route('article_approvisionnement.export')" >
                        <template #import >
                            <excel-import-base-standard :columns="columns" model="App\Model\Article"></excel-import-base-standard>
                        </template>
                </ExportData>
            </template>
        </FilterBase>
        </template>

        <DataTable
            :columns="columns"
            :data="data"
            :actions="actions"
            class="main-shadow"
            :show-index="true"
            :btn_action="false"
        >
            <template #bodyCell="{ column, record}">
                <template v-if="column.key === 'remise_general_ariary'">
                    {{ new Intl.NumberFormat().format(record.remise_general_ariary) }}
                </template>

                <template v-if="column.key === 'montant_ht_appro'">
                    {{ new Intl.NumberFormat().format(record.montant_ht_appro) }}
                </template>
                <template v-if="column.key === 'montant_tva_appro'">
                    {{ new Intl.NumberFormat().format(record.montant_tva_appro) }}
                </template>
                <template v-if="column.key === 'montant_ttc_appro'">
                    {{ new Intl.NumberFormat().format(record.montant_ttc_appro) }}
                </template>
                <template v-if="column.key === 'date_heure_enregistrement'">
                    {{ record.date_heure_enregistrement ? dayjs(record.date_heure_enregistrement).format('DD/MM/YYYY HH:mm:ss') : ''}}

                </template>
                <template v-if="column.key === 'date_appro'">
                    {{ record.date_appro ? dayjs(record.date_appro).format('DD/MM/YYYY') : ''}}
                </template>
            </template>


        </DataTable>

        <ArticleApprovisionnemntFormulaire
            :magasins="magasins"
            :fournisseurs = "fournisseurs"
            ref="refFormModal"
        />

    </SousMenuPrincipale>
</template>

<style scoped>

</style>
