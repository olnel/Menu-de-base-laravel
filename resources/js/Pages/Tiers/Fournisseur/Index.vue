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
import FournisseurFormulaire from "@/Pages/Tiers/Fournisseur/Formulaire/FournisseurFormulaire.vue";
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
const title = computed(() => `Liste des Fournisseurs (${props.data?.total ?? 0})`);
const filter = ref(createSearchFilter())

const columns = [
    {key: "nom_fournisseur", title: "Nom", dataIndex: "nom_fournisseur"},
    {key: "adresse_fournisseur", title: "Adresse", dataIndex: "adresse_fournisseur"},
    {key: "mail_fournisseur", title: "E-Mail", dataIndex: "mail_fournisseur"},
    {key: "tel_fournisseur", title: "Contact", dataIndex: "tel_fournisseur"},
    {key: "nif_fournisseur", title: "NIF", dataIndex: "nif_fournisseur"},
    {key: "stat_fournisseur", title: "STAT", dataIndex: "stat_fournisseur"},
    {key: "rcs_fournisseur", title: "RCS", dataIndex: "rcs_fournisseur"},
];

const handleDelete = (record) => {
    confirm_delete(() => {
        router.delete(route('fournisseur.destroy', record.id), {
            preserveScroll: true,
        });
    });
};

const actions = [
    {
        text: "Modifier",
        action: (record) => formModal.value.update(record),
        icon: 'fa-pen-to-square',
        privilege: 'fournisseur.update'
    },
    {
        text: "Supprimer",
        action: handleDelete,
        icon: 'fa-trash',
        class:"!text-red-600",
        disabled: (record) => record.is_you || props.data.total < 1,
        privilege: 'fournisseur.destroy'
    },
];
const applyFilters = (data) => {
    filter.value = data;
    const url = route('fournisseur.index');
    gotoSearch(filter.value, url)
};

const resetFilters = () => {
    filter.value.search = ""
    applyFilters();
};


</script>

<template>
    <SousMenuPrincipale :title="title" selectedMenu="fournisseur">
        <template #top>
            <FilterBase v-model="props.filters"
                        @search="applyFilters"
                        @reset="resetFilters"
            >
                <template #add>
                    <ButtonIcon v-if="can('fournisseur.store')" @click="() => formModal.add()" type="primary"
                                text="Nouveau Fournisseur" icon="fa-plus"/>
                </template>

                <template #import v-if="can('export_fournisseur.export')">
                    <ExportData :show_import="false" :title="'Export data'" :columns="columns" :filter="filter" :url="route('fournisseur.export')" >
                        <template #import >
                            <excel-import-base-standard :columns="columns" model="test"></excel-import-base-standard>
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


        </DataTable>
        <FournisseurFormulaire ref="formModal"

        />
    </SousMenuPrincipale>
</template>

<style scoped>
/* Styles spécifiques si nécessaires */
</style>
