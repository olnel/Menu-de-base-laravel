<script setup>
import SousMenuPrincipale from "@/Pages/Menu/SousMenuPrincipale.vue";
import DataTable from "@/Components/DataTable.vue";
import ButtonIcon from "@/Components/ButtonIcon.vue";
import {computed, ref} from "vue";
import {confirm_delete} from "../../../Utils/confirmation_modal.js";
import {router} from "@inertiajs/vue3";
import FormulaireVehicule from "@/Pages/Vehicule/Formulaire/FormulaireVehicule.vue";
import FormulairePhotoVehicule from "@/Pages/Vehicule/Formulaire/FormulairePhotoVehicule.vue";
import Information from "@/Pages/Vehicule/Formulaire/Information.vue";
import FilterBase from "@/Components/Filter/FilterBase.vue";
import FormModal from "@/Pages/Vehicule/VehiculeForm/FormModal.vue";
import {createSearchFilter, gotoSearch} from "../../../Utils/FiltreUtils.js";
import usePermissions from "@/UserPermissions/usePermissions.js";
import Notification from "@/Components/Notification.vue";
import {mapExistingImages} from "../../../Utils/fileUtil.js";
import FilePreviewList from "@/Components/FilePreviewList.vue";
import VehiculeDocumentForm from "@/Pages/VehiculeDocument/Form/VehiculeDocumentForm.vue";
import {DownOutlined} from "@ant-design/icons-vue";
import {Space} from "ant-design-vue";
import ExcelImportBaseStandard from "@/Components/ExcelImportBaseStandard.vue";
import ExportData from "@/Components/ExportData/ExportData.vue";

const {can} = usePermissions()


const props = defineProps({
    data: {
        type: Object,
        default: () => ({})
    },
    vehicules: {
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
    },
    notification_data: {
        type: Array,
        default: () => ([])
    },
    errors: Object
});

const refFormModal = ref();
const formPhotoModal = ref();
const dropdownVisible = ref(false);

const filter = ref({
    search: null,
    search_document: null,
    vehicule_id: null,
    start_date_document: null,
    end_date_document: null
})

const title = computed(() => `Liste des véhicules (${props.data?.total ?? 0})`);
const dateFormat = 'DD/MM/YYYY';
const columns = [
    {key: "date_expiration", title: "Date", dataIndex: "date_expiration",customHeaderCell:()=>({class:'!text-center'}),customCell:()=>({class:'!text-center'})},
    {key: "immatriculation", title: "Immatriculation", dataIndex: "immatriculation",customHeaderCell:()=>({class:'!text-center'}),customCell:()=>({class:'!text-center'})},
    {key: "nom_document", title: "Nom du Document", dataIndex: "nom_document"},
    {key: "description", title: "Description", dataIndex: "description"},

];

const handleDelete = (record) => {
    confirm_delete(() => {
        router.delete(route('vehiculedocument.destroy', record.id), {
            preserveScroll: true,
        });
    });
};


const actions = [
    {
        text: "Voir les Informations",
        // action: (record) => router.visit(route('vehiculedocument.info', {vehicule: record.id})),
        action: (record) => refFormModal.value.update(record),
        icon: 'fa-edit',
        privilege: 'vehiculedocument.update'
    },
    {
        text: "Supprimer",
        action: handleDelete,
        class: "!text-red-600",
        icon: 'fa-trash',
        privilege: 'vehiculedocument.destroy',
        disabled: (record) => record.is_you || props.data.total < 1,
    },
];

const applyFilters = (data) => {
    filter.value = data;
    filter.value.search_document = data.search;
    const url = route('vehiculedocument.index');
    gotoSearch(filter.value, url)
};

const resetFilters = () => {
    filter.value = {
        search: null,
        search_document: null,
        vehicule_id: null,
        start_date_document: null,
        end_date_document: null
    };
    const url = route('vehiculedocument.index');
    gotoSearch(filter.value, url)
};

const addNewdocument = () => {
    refFormModal.value.openModal(true)
}
const closeDropdown = () => {
    dropdownVisible.value = false;
}
</script>

<template>

    <SousMenuPrincipale :title="title" selectedMenu="vehiculedocument">
        <template #top>
            <FilterBase v-model="filter"
                        @search="applyFilters"
                        @reset="resetFilters"
        >
            <template #import v-if="can('export.vehiculedocument.export')">
                <ExportData :show_import="false" :title="'Export data'" :columns="columns" :filter="filter" :url="route('vehiculedocument.export')" >
                    <template #import >
                        <excel-import-base-standard model="test"
                            :columns="columns"/>
                    </template>
                </ExportData>
            </template>

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
                                v-model:value="filter.start_date_document"
                                :format="dateFormat"
                                size="large"
                                class="w-full text-center"
                                placeholder="Du"
                                :value-format="'DD-MM-YYYY'"
                            />
                            <a-date-picker
                                v-model:value="filter.end_date_document"
                                :format="dateFormat"
                                size="large"
                                class="w-full text-center"
                                placeholder="Au"
                                :value-format="'DD-MM-YYYY'"
                            />
                            <a-select
                                v-model:value="filter.vehicule_id"
                                placeholder="Véhicule"
                                class="w-full"
                                size="large"
                                allow-clear
                            >
                                <a-select-option
                                    v-for="type in props.vehicules"
                                    :key="type.value"
                                    :value="type.value"
                                >
                                    {{ type.label }}
                                </a-select-option>
                            </a-select>

                            <div class="flex space-x-2 !mt-6">
                                <a-button block type="primary" size="middle" @click="applyFilters(filter)">Appliquer
                                </a-button>
                                <a-button block type="default" size="middle" @click="closeDropdown">Fermer</a-button>
                            </div>
                        </div>
                    </template>

                    <a-button size="large" type="default" class="!rounded-none border-r-0 focus:z-10">
                        <Space>
                            <font-awesome-icon class="text-[15px]" icon="fa-filter"/>
                            Filtres
                            <DownOutlined/>
                        </Space>
                    </a-button>
                </a-popover>

            </template>

            <template #add>

                <ButtonIcon v-if="can('vehiculedocument.store')" @click="addNewdocument" type="primary"
                            text="Nouveau Document" icon="fa-plus"
                            class="!rounded-md hover:!text-white hover:!bg-sky-800 transition-all duration-300"/>
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
            <template #expandedRowRender="{ record }">

                <div class="p-4">
                    <FilePreviewList
                        :initial-files="
                            mapExistingImages(record.fichier_jointe)
                        "
                        :disabled="true"
                        :preview-only="true"
                    />
                </div>
            </template>

            <template #emptyText>
                <a-empty description="Aucun document associé" size="small"/>
            </template>

        </DataTable>

        <VehiculeDocumentForm
            ref="refFormModal"
            :vehiuclesListe="vehicules"
            :vehiculeDocuments="{}"
            :vehicule_id="1"
        />

    </SousMenuPrincipale>
</template>

<style scoped>

</style>
