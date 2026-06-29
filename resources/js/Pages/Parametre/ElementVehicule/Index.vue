<script setup>
import SousMenuPrincipale from "@/Pages/Menu/SousMenuPrincipale.vue";
import DataTable from "@/Components/DataTable.vue";
import ButtonIcon from "@/Components/ButtonIcon.vue";
import {computed, ref} from "vue";
import {confirm_delete} from "../../../../Utils/confirmation_modal.js";
import {router} from "@inertiajs/vue3";
import FormulaireElementVehicule from "@/Pages/Parametre/ElementVehicule/Formulaire/FormulaireElementVehicule.vue";
import usePermissions from "@/UserPermissions/usePermissions.js";
import FilterBase from "@/Components/Filter/FilterBase.vue";
import ExcelImportBaseStandard from "@/Components/ExcelImportBaseStandard.vue";
import ExportData from "@/Components/ExportData/ExportData.vue";
import {createSearchFilter, gotoSearch} from "../../../../Utils/FiltreUtils.js";

const {can} = usePermissions()
const filter = ref(createSearchFilter())


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
const title = computed(() => `Element Véhicule (${props.data?.total ?? 0})`);

const columns = [
    {key: "type_model", title: "Modèle", dataIndex: "type_model"},
];

const handleDelete = (record) => {
    confirm_delete(() => {
        router.delete(route('paramelement.destroy', record.id), {
            preserveScroll: true,
        });
    });
};

const actions = [
    {
        text: "Modifier",
        action: (record) => formModal.value.update(record),
        icon: 'fa-pen-to-square',
        privilege: 'paramelement.update'
    },
    {
        text: "Supprimer",
        action: handleDelete,
        icon: 'fa-trash',
        class:"!text-red-600",
        disabled: (record) => record.is_you || props.data.total < 1,
        privilege: 'paramelement.destroy'
    },
];
const applyFilters = (data) => {
    filter.value = data;
    const url = route('paramelement.index');
    gotoSearch(filter.value, url)
};

const resetFilters = () => {
    filter.value.search = ""
    applyFilters();
};
</script>

<template>
    <SousMenuPrincipale :title="title" selectedMenu="vehicle-elements">
        <template #top>
            <FilterBase v-model="props.filters"
                        @search="applyFilters"
                        @reset="resetFilters"
        >
            <template #add>
                <ButtonIcon v-if="can('paramelement.store')" @click="() => formModal.add()" type="primary"
                            text="Nouveau Elément" icon="fa-plus"/>
            </template>
            <template #import v-if="can('export_paramelement.export')">
                <ExportData :show_import="false" :title="'Export data'" :columns="columns" :filter="filter" :url="route('paramelement.export')" >
                    <template #import >
                        <excel-import-base-standard model="test"
                            :columns="columns"/>
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
        />

        <FormulaireElementVehicule ref="formModal" :flash="props.flash"/>
    </SousMenuPrincipale>
</template>

<style scoped>
/* Styles spécifiques si nécessaires */
</style>
