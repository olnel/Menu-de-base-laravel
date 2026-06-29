<script setup>
import ButtonIcon from "@/Components/ButtonIcon.vue";
import DataTable from "@/Components/DataTable.vue";
import DynamicDocumentManager from "@/Components/DynamicDocumentManager.vue";
import FilterBase from "@/Components/Filter/FilterBase.vue";
import SousMenuPrincipale from "@/Pages/Menu/SousMenuPrincipale.vue";
import FormulairePhotoVehicule from "@/Pages/Vehicule/Formulaire/FormulairePhotoVehicule.vue";
import FormulaireVehicule from "@/Pages/Vehicule/Formulaire/FormulaireVehicule.vue";
import usePermissions from "@/UserPermissions/usePermissions.js";
import { router } from "@inertiajs/vue3";
import { computed, ref } from "vue";
import { confirm_delete } from "../../../Utils/confirmation_modal.js";
import { createSearchFilter, gotoSearch } from "../../../Utils/FiltreUtils.js";
import ExportData from "@/Components/ExportData/ExportData.vue";
import ExcelImportBaseStandard from "@/Components/ExcelImportBaseStandard.vue";

const { can } = usePermissions();

const props = defineProps({
    data: {
        type: Object,
        default: () => ({}),
    },
    liste_couleur: {
        type: Array,
        default: () => [],
    },
    liste_modele: {
        type: Array,
        default: () => [],
    },
    liste_remorque: {
        type: Array,
        default: () => [],
    },
    liste_marque: {
        type: Array,
        default: () => [],
    },
    filters: {
        type: Object,
        default: () => ({}),
    },
    flash: {
        type: Object,
        default: () => ({}),
    },
    notification_data: {
        type: Array,
        default: () => [],
    },
    required_documents: {
        type: Array,
        default: () => [],
    },
    errors: Object,
});

const refFormModal = ref();
const formPhotoModal = ref();
const docModalOpen = ref(false);
const selectedVehicule = ref(null);

const filter = ref(createSearchFilter());

const title = computed(() => `Liste des véhicules (${props.data?.total ?? 0})`);

const getMissingDocs = (record) => {
    if (!props.required_documents || !record.documents) return [];
    return props.required_documents.filter(reqDoc => {
        const exists = record.documents.some(doc => doc.document_type_id === reqDoc.document_type_id);
        return reqDoc.obligatoire && !exists;
    });
};

const modelPrint = 'App\\Models\\Vehicule'
const columnsPrint = [
    'immatriculation',
    'numero_remorque',
    'marque',
    'modele',
    'couleur',
    'num_chassis',
    'num_carte_grise',
    'nbre_porte',
    'valeur_initial',
    'description',
    'remorque_id'
]

const columns = [
    {
        key: "immatriculation",
        title: "Immatriculation",
        dataIndex: "immatriculation",
        width: 150,
        customHeaderCell: () => ({ class: "!text-center whitespace-nowrap" }),
        customCell: () => ({ class: "text-center" }),
    },
    { key: "marque", title: "Marque", dataIndex: "marque", width: 120 },
    { key: "modele", title: "Modèle", dataIndex: "modele", width: 120 },
    { key: "couleur", title: "Couleur", dataIndex: "couleur", width: 100 },
    {
        key: "num_chassis",
        title: "N° Châssis",
        dataIndex: "num_chassis",
        width: 160,
    },
    {
        key: "num_carte_grise",
        title: "N° Carte Grise",
        dataIndex: "num_carte_grise",
        width: 160,
    },
    {
        key: "docs_manquants",
        title: "Docs. Obligatoires",
        align: "center",
        width: 150,
    },
    {
        key: "numero_remorque",
        title: "N° Remorque",
        dataIndex: "numero_remorque",
        width: 160,
    },
    {
        key: "nbre_porte",
        title: "Nbre Porte",
        dataIndex: "nbre_porte",
        width: 100,
        customHeaderCell: () => ({ class: "!text-center whitespace-nowrap" }),
        customCell: () => ({ class: "text-center" }),
    },
    {
        key: "valeur_initial",
        title: "Valeur Initial",
        dataIndex: "valeur_initial",
        width: 130,
        class:"text-right",
        customHeaderCell: () => ({ class: "!text-right whitespace-nowrap" }),
        customCell: () => ({ class: "text-right" }),
    },
];

const handleDelete = (record) => {
    confirm_delete(() => {
        router.delete(route("vehicule.destroy", record.id), {
            preserveScroll: true,
        });
    });
};

const openDocModal = (record) => {
    selectedVehicule.value = record;
    docModalOpen.value = true;
};

const actions = [
    {
        text: "Documents",
        action: openDocModal,
        icon: "fa-folder-open",
        class: "!text-amber-500",
        privilege: "vehicule.update",
        disabled: (record) => !props.required_documents?.length && !record.documents?.length,
    },
    {
        text: "Voir les Informations",
        action: (record) =>
            router.visit(route("vehicule.info", { vehicule: record.id })),
        icon: "fa-eye",
        privilege: "vehicule.show",
    },
    {
        text: "Supprimer",
        action: handleDelete,
        class: "!text-red-600",
        icon: "fa-trash",
        privilege: "vehicule.destroy",
        disabled: (record) => record.is_you || props.data.total < 1,
    },
];

const applyFilters = (data) => {
    filter.value = data;
    const url = route("vehicule.index");
    gotoSearch(filter.value, url);
};

const resetFilters = () => {
    filter.value.search = "";
    applyFilters();
};
</script>

<template>
    <SousMenuPrincipale :title="title" selectedMenu="vehicule">
        <template #top>
            <FilterBase
            v-model="props.filters"
            @search="applyFilters"
            @reset="resetFilters"
        >

            <template #import v-if="can('export_vehicule.export')">
                <ExportData :show_import="true" :title="'Export data'" :columns="columns" :filter="filter" :url="route('vehicule.export')" >
                    <template #import >
                        <excel-import-base-standard v-if="can('vehicule.import')" :model=modelPrint
                            :columns="columnsPrint" routeName="import.vehicule.standard"/>
                    </template>
                </ExportData>
            </template>

            <template #add>
                <ButtonIcon
                    v-if="can('vehicule.store')"
                    @click="() => refFormModal.add()"
                    type="primary"
                    text="Nouveau Véhicule"
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
            :title="`Documents : ${selectedVehicule?.immatriculation}`"
            width="1000px"
            :footer="null"
            centered
            destroyOnClose
        >
            <div class="py-4">
                <DynamicDocumentManager 
                    v-if="selectedVehicule"
                    modelClass="App\Models\Vehicule" 
                    :modelId="selectedVehicule.id" 
                />
            </div>
        </a-modal>

        <FormulaireVehicule
            ref="refFormModal"
            :LIST_ELEMENT_VEHICULE="props.flash.element_vehicule"
            :LIST_COULEUR_VEHICULE="props.liste_couleur"
            :LIST_MODELE_VEHICULE="props.liste_modele"
            :LIST_MARQUE_VEHICULE="props.liste_marque"
            :LIST_REMORQUE="props.liste_remorque"
            :required_documents="props.required_documents"
            :flash="props.flash"
        />
        <FormulairePhotoVehicule ref="formPhotoModal" />
    </SousMenuPrincipale>
</template>

<style scoped></style>
