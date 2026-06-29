<script setup>
import SousMenuPrincipale from "@/Pages/Menu/SousMenuPrincipale.vue";
import DataTable from "@/Components/DataTable.vue";
import ButtonIcon from "@/Components/ButtonIcon.vue";
import {computed, ref} from "vue";
import {confirm_delete} from "../../../../Utils/confirmation_modal.js";
import {router} from "@inertiajs/vue3";
import FormulaireElementVehicule from "@/Pages/Parametre/ElementVehicule/Formulaire/FormulaireElementVehicule.vue";
import LibelleMaintenanceFormulaire
    from "@/Pages/Parametre/LibelleMaintenance/Formulaire/LibelleMaintenanceFormulaire.vue";
import FilterBase from "@/Components/Filter/FilterBase.vue";
import {createSearchFilter, gotoSearch} from "../../../../Utils/FiltreUtils.js";
import usePermissions from "@/UserPermissions/usePermissions.js";
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
const title = computed(() => `Libellé Maintenance Préventif (${props.data?.total ?? 0})`);
const filter = ref(createSearchFilter())

const columns = [
    {key: "libelle", title: "Libellé", dataIndex: "libelle"},
    {key: "background", title: "background", dataIndex: "background"},
    {key: "text_color", title: "Texte Color", dataIndex: "text_color"},
    {key: "notification", title: "Notification", dataIndex: "notification"},
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
        privilege: 'libelle_maintenance.update'
    },
    {
        text: "Supprimer",
        action: handleDelete,
        icon: 'fa-trash',
        class:"!text-red-600",
        disabled: (record) => record.is_you || props.data.total < 1,
        privilege: 'libelle_maintenance.destroy'
    },
];
const applyFilters = (data) => {
    filter.value = data;
    const url = route('libelle_maintenance.index');
    gotoSearch(filter.value, url)
};

const resetFilters = () => {
    filter.value.search = ""
    applyFilters();
};


</script>

<template>
    <SousMenuPrincipale :title="title" selectedMenu="maintenances">
        <template #top>
            <FilterBase v-model="props.filters"
                        @search="applyFilters"
                        @reset="resetFilters"
        >
            <template #add>
                <ButtonIcon v-if="can('libelle_maintenance.store')" @click="() => formModal.add()" type="primary"
                            text="Nouveau Libellé" icon="fa-plus"/>
            </template>
            <template #import v-if="can('export_libelle_maintenance.export')">
                <ExportData :show_import="false" :title="'Export data'" :columns="columns" :filter="filter" :url="route('libelle_maintenance.export')" >
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
            <template #bodyCell="{ column, record }">
                <!-- Aperçu de la couleur de fond -->
                <template v-if="column.key === 'background'">
                    <div class="flex items-center space-x-2">
                        <div
                            class="w-6 h-6 rounded shadow border"
                            :style="{ backgroundColor: record.background }"
                        ></div>
                        <span>{{ record.background }}</span>
                    </div>
                </template>

                <!-- Aperçu de la couleur du texte -->
                <template v-if="column.key === 'text_color'">
                    <div class="flex items-center space-x-2">
                        <div
                            class="w-6 h-6 rounded shadow border"
                            :style="{ backgroundColor: record.text_color }"
                        ></div>
                        <span>{{ record.text_color }}</span>
                    </div>
                </template>
            </template>

        </DataTable>
        <LibelleMaintenanceFormulaire ref="formModal"

        />
    </SousMenuPrincipale>
</template>

<style scoped>
/* Styles spécifiques si nécessaires */
</style>
