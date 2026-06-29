<script setup>
import SousMenuPrincipale from "@/Pages/Menu/SousMenuPrincipale.vue";
import DataTable from "@/Components/DataTable.vue";
import ButtonIcon from "@/Components/ButtonIcon.vue";
import {computed, ref} from "vue";
import {confirm_delete} from "../../../../Utils/confirmation_modal.js";
import {router} from "@inertiajs/vue3";
import FilterBase from "@/Components/Filter/FilterBase.vue";
import {createSearchFilter, gotoSearch} from "../../../../Utils/FiltreUtils.js";
import usePermissions from "@/UserPermissions/usePermissions.js";
import TresorerieFormulaire from "@/Pages/ModuleTresorerie/Tresorerie/form/TresorerieFormulaire.vue";
import {formatDate, formatDateTime} from "../../../../Utils/functions.js";
import ExportData from "@/Components/ExportData/ExportData.vue";
import ExcelImportBaseStandard from "@/Components/ExcelImportBaseStandard.vue";

const {can} = usePermissions()

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
    }
});

const formModal = ref();
const title = computed(() => `Liste des Trésoreries (${props.data?.total ?? 0})`);
const filter = ref(createSearchFilter())

const columns = [
    {key: "nom_tresorerie", title: "Nom de la Trésorerie", dataIndex: "nom_tresorerie"},
    {key: "type_tresorerie", title: "Type", dataIndex: "type_tresorerie", width: 150},
    {key: "numero_telephone", title: "N° du Téléphone", dataIndex: "numero_telephone", width: 200},
    {key: "titulaire_compte", title: "Titulaire de compte", dataIndex: "titulaire_compte", width: 200},
    {key: "banque_correspondante", title: "Banque Correspondante", dataIndex: "banque_correspondante", width: 300},
    {key: "bic", title: "BIC", dataIndex: "bic"},
    {key: "iban", title: "IBAN", dataIndexd: "iban"},
    {key: "code_swift", title: "SWIFT", dataIndex: "code_swift"},
    {key: "solde", title: "SOLDE", dataIndex: "solde",  customHeaderCell: () => ({ class: "!text-right" }),  customCell: () => ({ class: 'text-right' })},
];

const handleDelete = (record) => {
    confirm_delete(() => {
        router.delete(route('tresorerie.destroy', record.id), {
            preserveScroll: true,
        });
    });
};

const toRootMouvement = (record) => {

    router.get(route('tresorerie_mouvement.index', {tresorerie_id: record.id}), {
        preserveScroll: true,
    });
};

const toRootFlux = (record) => {
    router.get(route('tresorerieflux.index', {tresorerie_id: record.id}), {
        preserveScroll: true,
    });
};

const actions = [
    {
        text: "Modifier",
        action: (record) => formModal.value.update(record),
        icon: 'fa-pen-to-square',
        privilege: 'tresorerie.update'
    },
    {
        text: "Voir le mouvement",
        action: (record) => toRootMouvement(record),
        icon: 'fa-right-left',
        privilege: 'tresorerie.mouvement'
    },
    {
        text: "Voir l'Historique ",
        action: (record) => toRootFlux(record),
        icon: 'fa-clock-rotate-left',
        privilege: 'tresorerie.historique'
    },
    {
        text: "Supprimer",
        action: handleDelete,
        icon: 'fa-trash',
        class:"!text-red-600",
        disabled: (record) => record.is_you || props.data.total < 1,
        privilege: 'tresorerie.destroy'
    },
];
const applyFilters = (data) => {
    filter.value = data;
    const url = route('tresorerie.index');
    gotoSearch(filter.value, url)
};

const resetFilters = () => {
    filter.value.search = ""
    applyFilters();
};


</script>

<template>
    <SousMenuPrincipale :title="title" selectedMenu="tresorerie">
        <template #top>
            <FilterBase v-model="props.filters"
                        @search="applyFilters"
                        @reset="resetFilters"
        >
            <template #add>
                <ButtonIcon v-if="can('tresorerie.store')" @click="() => formModal.add()" type="primary"
                            text="Nouvelle Trésorerie" icon="fa-plus"/>
            </template>
            <template #import v-if="can('export_tresorerie.export')">
                <ExportData :show_import="false" :title="'Export data'" :columns="columns" :filter="filter" :url="route('tresorerie.export')" >
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
            :actions="actions"
            class="main-shadow"
            :show-index="true"
            :btn_action="true"
        >
            <template #bodyCell="{ column, record }">
                <template v-if="column.key === 'solde'">
                    {{ new Intl.NumberFormat('fr-FR').format(record.solde) }}
                </template>
            </template>

        </DataTable>
        <TresorerieFormulaire ref="formModal"

        />
    </SousMenuPrincipale>
</template>

<style scoped>
/* Styles spécifiques si nécessaires */
</style>
