<script setup>
import SousMenuPrincipale from "@/Pages/Menu/SousMenuPrincipale.vue";
import DataTable from "@/Components/DataTable.vue";
import ButtonIcon from "@/Components/ButtonIcon.vue";
import {computed, ref} from "vue";
import {confirm_delete} from "../../../Utils/confirmation_modal.js";
import {router} from "@inertiajs/vue3";
import FilterBase from "@/Components/Filter/FilterBase.vue";
import {createSearchFilter, gotoSearch} from "../../../Utils/FiltreUtils.js";
import usePermissions from "@/UserPermissions/usePermissions.js";
import MagasinForm from "@/Pages/Magasin/Form/MagasinForm.vue";
import ExcelImportBaseStandard from "@/Components/ExcelImportBaseStandard.vue";
import ExportData from "@/Components/ExportData/ExportData.vue";

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
const title = computed(() => `Liste des Magasins (${props.data?.total ?? 0})`);
const filter = ref(createSearchFilter())

const columns = [
    {key: "nom_magasin", title: "Magasin", dataIndex: "nom_magasin"},
];

const handleDelete = (record) => {
    confirm_delete(() => {
        router.delete(route('magasin.destroy', record.id), {
            preserveScroll: true,
        });
    });
};

const actions = [
    {
        text: "Modifier",
        action: (record) => formModal.value.update(record),
        icon: 'fa-pen-to-square',
        privilege: 'magasin.update'
    },
    {
        text: "Supprimer",
        action: handleDelete,
        icon: 'fa-trash',
        class:"!text-red-600",
        disabled: (record) => record.is_you || props.data.total < 1,
        privilege: 'magasin.destroy'
    },
];
const applyFilters = (data) => {
    filter.value = data;
    const url = route('magasin.index');
    gotoSearch(filter.value, url)
};

const resetFilters = () => {
    filter.value.search = ""
    applyFilters();
};


</script>

<template>
    <SousMenuPrincipale :title="title" selectedMenu="magasin">
        <template #top>
            <FilterBase v-model="props.filters"
                        @search="applyFilters"
                        @reset="resetFilters"
        >
            <template #add>
                <ButtonIcon v-if="can('magasin.store')" @click="() => formModal.add()" type="primary"
                            text="Nouveau Magasin" icon="fa-plus"/>
            </template>
            <template #import v-if="can('export_magasin.export')">
                <ExportData :show_import="false" :title="'Export data'" :columns="columns" :filter="filter" :url="route('magasin.export')" >
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
        >


        </DataTable>
        <MagasinForm ref="formModal"

        />
    </SousMenuPrincipale>
</template>

<style scoped>
/* Styles spécifiques si nécessaires */
</style>
