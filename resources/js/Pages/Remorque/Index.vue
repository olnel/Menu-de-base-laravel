<script setup>
import ButtonIcon from "@/Components/ButtonIcon.vue";
import DataTable from "@/Components/DataTable.vue";
import DynamicDocumentManager from "@/Components/DynamicDocumentManager.vue";
import FilterBase from "@/Components/Filter/FilterBase.vue";
import SousMenuPrincipale from "@/Pages/Menu/SousMenuPrincipale.vue";
import ExcelImportBaseStandard from "@/Components/ExcelImportBaseStandard.vue";
import ExportData from "@/Components/ExportData/ExportData.vue";
import FormulaireRemorque from "@/Pages/Remorque/Formulaire/FormulaireRemorque.vue";
import FormulairePhotoVehicule from "@/Pages/Vehicule/Formulaire/FormulairePhotoVehicule.vue";
import usePermissions from "@/UserPermissions/usePermissions.js";
import { router } from "@inertiajs/vue3";
import { computed, ref } from "vue";
import { confirm_delete } from "../../../Utils/confirmation_modal.js";
import { createSearchFilter, gotoSearch } from "../../../Utils/FiltreUtils.js";

const { can } = usePermissions();

const props = defineProps({
    data: {
        type: Object,
        default: () => ({})
    },
    liste_couleur: {
        type: Array,
        default: () => []
    },
    liste_modele: {
        type: Array,
        default: () => []
    },
    liste_marque: {
        type: Array,
        default: () => []
    },
    filters: {
        type: Object,
        default: () => ({}),
    },
    required_documents: {
        type: Array,
        default: () => [],
    },
    flash: {
        type: Object,
        default: () => ({}),
    },
    notification_data: {
        type: Array,
        default: () => ([])
    },
    errors: Object
});

const refFormModal = ref();
const formPhotoModal = ref();
const docModalOpen = ref(false);
const selectedRemorque = ref(null);

const filter = ref(createSearchFilter());

const title = computed(() => `Liste des Rémorques (${props.data?.total ?? 0})`);

const getMissingDocs = (record) => {
    if (!props.required_documents || !record.documents) return [];
    return props.required_documents.filter(reqDoc => {
        const exists = record.documents.some(doc => doc.document_type_id === reqDoc.document_type_id);
        return reqDoc.obligatoire && !exists;
    });
};

const modelPrint = 'App\\Models\\Remorque'
const columnsPrint = ['numero_remorque', 'modele_remorque', 'marque_remorque']

const columns = [
    { key: "numero_remorque", title: "N° Remorque", dataIndex: "numero_remorque", width: 150, customCell: () => ({ class: 'text-center' }),customHeaderCell:()=>({class:'!text-center'})  },
    { key: "marque_remorque", title: "marque_remorque", dataIndex: "marque_remorque", width: 120 },
    { key: "modele_remorque", title: "Modèle", dataIndex: "modele_remorque", width: 120 },
    {
        key: "docs_manquants",
        title: "Docs. Obligatoires",
        align: "center",
        width: 150,
    },
];


const handleDelete = (record) => {
    confirm_delete(() => {
        router.delete(route('remorque.destroy', record.id), {
            preserveScroll: true,
        });
    });
};

const openDocModal = (record) => {
    selectedRemorque.value = record;
    docModalOpen.value = true;
};

const actions = [
    {
        text: "Documents",
        action: openDocModal,
        icon: "fa-folder-open",
        class: "!text-amber-500",
        privilege: "remorque.update",
        disabled: (record) => !props.required_documents?.length && !record.documents?.length,
    },
    { text: "Voir les Informations",
        action: (record) => router.visit(route('remorque.info', {remorque: record.id})),
        icon: 'fa-eye',
        privilege: 'remorque.show'
    },
    { text: "Supprimer",
        action: handleDelete,
        class:"!text-red-600",
        icon: 'fa-trash',
        privilege: 'remorque.destroy',
        disabled: (record) => record.is_you || props.data.total < 1,
    },
];

const applyFilters = (data) => {
    filter.value = data;
    const url = route('remorque.index');
    gotoSearch(filter.value, url)
};

const resetFilters = () => {
    filter.value.search = ""
    applyFilters();
};


</script>

<template>
    <SousMenuPrincipale :title="title" selectedMenu="remorque">
        <template #top>
            <FilterBase v-model="props.filters"
                        @search="applyFilters"
                        @reset="resetFilters"
        >
            <!-- <template #import>
                <ExcelImportBaseStandard
                v-if="can('remorque.import')"
                    :model=modelPrint
                    :columns="columnsPrint"
                    buttonText="Importer"
                />
            </template> -->
            <template #import v-if="can('export_remorque.export')">
                <ExportData :show_import="true" :title="'Export data'" :columns="columns" :filter="filter" :url="route('remorque.export')" >
                    <template #import >
                        <excel-import-base-standard v-if="can('remorque.import')" :model=modelPrint
                            :columns="columnsPrint"/>
                    </template>
                </ExportData>
            </template>

            <template #add>
                <ButtonIcon v-if="can('remorque.store')" @click="() => refFormModal.add()" type="primary" text="Nouveau Remorque" icon="fa-plus" class="!rounded-md hover:!text-white hover:!bg-sky-800 transition-all duration-300"/>
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
                <template v-if="column.key === 'valeur_initial'">
                    {{ new Intl.NumberFormat().format(record.valeur_initial) }}
                </template>
                <template v-else-if="column.key === 'docs_manquants'">
                    <div class="flex items-center justify-center">
                        <a-popover v-if="getMissingDocs(record).length > 0" title="Documents Manquants" placement="left">
                            <template #content>
                                <ul class="list-disc pl-4 space-y-1">
                                    <li v-for="doc in getMissingDocs(record)" :key="doc.id" class="text-xs text-red-500 font-medium">
                                        {{ doc.document_type.nom }}
                                    </li>
                                </ul>
                            </template>
                            <a-badge :count="getMissingDocs(record).length" :offset="[5, 0]">
                                <div class="w-8 h-8 rounded-full bg-red-50 flex items-center justify-center text-red-500 border border-red-100 cursor-help hover:bg-red-100 transition-colors">
                                    <font-awesome-icon icon="fa-solid fa-file-circle-exclamation" />
                                </div>
                            </a-badge>
                        </a-popover>
                        <div v-else class="w-8 h-8 rounded-full bg-green-50 flex items-center justify-center text-green-500 border border-green-100">
                            <font-awesome-icon icon="fa-solid fa-check-double" />
                        </div>
                    </div>
                </template>
            </template>
        </DataTable>

        <!-- Modal pour la gestion des documents -->
        <a-modal
            v-model:open="docModalOpen"
            :title="`Documents : ${selectedRemorque?.numero_remorque}`"
            width="1000px"
            :footer="null"
            centered
            destroyOnClose
        >
            <div class="py-4">
                <DynamicDocumentManager 
                    v-if="selectedRemorque"
                    modelClass="App\Models\Remorque" 
                    :modelId="selectedRemorque.id" 
                />
            </div>
        </a-modal>


        <FormulaireRemorque ref="refFormModal"
                            :LIST_ELEMENT_VEHICULE="props.flash.element_vehicule"
                            :LIST_modele_remorque_VEHICULE="props.liste_modele"
                            :LIST_marque_remorque_VEHICULE="props.liste_marque"
                            :required_documents="props.required_documents"
                            :flash="props.flash"/>
        <FormulairePhotoVehicule ref="formPhotoModal" />


    </SousMenuPrincipale>
</template>

<style scoped>

</style>
