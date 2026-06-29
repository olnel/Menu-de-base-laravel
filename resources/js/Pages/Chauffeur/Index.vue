<script setup>
import ButtonIcon from "@/Components/ButtonIcon.vue";
import BaseDataTable from "@/Components/DataTable.vue";
import FilterBase from "@/Components/Filter/FilterBase.vue";
import ImagePreview from "@/Components/ImagePreview.vue";
import SousMenuPrincipale from "@/Pages/Menu/SousMenuPrincipale.vue";
import usePermissions from "@/UserPermissions/usePermissions.js";
import { Link, router } from "@inertiajs/vue3";
import { computed, h, ref } from "vue";
import { confirm_delete } from "../../../Utils/confirmation_modal.js";
import { createSearchFilter, gotoSearch } from "../../../Utils/FiltreUtils.js";
import { usePrivileges } from "../../../Utils/usePrivileges";
import ChauffeurForm from "./Formulaire/FormulaireChauffeur.vue";
import ExportData from "@/Components/ExportData/ExportData.vue";
import ExcelImportBaseStandard from "@/Components/ExcelImportBaseStandard.vue";

const { can } = usePermissions();

const props = defineProps({
    data: {
        type: Object,
        default: () => ({}),
    },
    vehicules: {
        type: Array,
        default: () => [],
    },
    filters: {
        type: Object,
        default: () => ({}),
    },
});

const title = computed(() => `Liste Des Chauffeurs (${props.data?.total ?? 0})`);
const formModal = ref();
const filter = ref(createSearchFilter());

const applyFilters = (data) => {
    filter.value = data;
    const url = route("chauffeur.index");
    gotoSearch(filter.value, url);
};

const resetFilters = () => {
    props.filters.search = "";
    filter.value.search = "";
    applyFilters();
};

const modelPrint = 'App\\Models\\Chauffeur'
const columnsPrint = [
    'numero_camion',
    'matricule',
    'nom',
    'prenom',
    'date_naissance',
    'CIN',
    'telephone',
    'adresse',
]

const columns = [
    {
        key: "photo",
        title: "photo",
        width: 60,
    },
    {
        key: "matricule",
        title: "Matricule",
        width: 120,
        class: "text-center",
        customCell: () => ({ class: "!text-center" }),
    },
    {
        key: "nom_complet",
        title: "Nom Complet",
        width: 200,
        customRender: ({ record }) =>
            h(
                Link,
                {
                    class: "!text-primary",
                    href: route("chauffeur.informations", record),
                },
                [
                    h(
                        "span",
                        {
                            class: "font-medium uppercase",
                        },
                        record.nom
                    ),
                    " ",
                    h("span", { class: "capitalize" }, record.prenom),
                ]
            ),
    },
    {
        key: "cin",
        title: "CIN",
        width: 120,
        class:"text-center",
        customCell: () => ({ class: "!text-center" }),
    },
    {
        key: "date_naissance",
        title: "date.naiss",
        customRender: ({ record }) => record.date_naissance ?? "-",

        width: 120,
        customCell: () => ({ class: "!text-center" }),
        customHeaderCell: () => ({ class: "!text-center whitespace-nowrap" }),
    },
    {
        key: "telephone",
        title: "Telephone",
        customRender: ({ record }) => record.telephone ?? "-",
        width: 120,
        customCell: () => ({ class: "" }),
        customHeaderCell: () => ({ class: " text-left" }),
    },
    {
        key: "adresse",
        title: "Adresse",
        customRender: ({ record }) => record.adresse ?? "-",

        width: 200,
    },
];

const handleDelete = (record) => {
    confirm_delete(() => {
        router.delete(route("chauffeur.destroy", record.id), {
            preserveScroll: true,
        });
    });
};

const actions = [
    { text: "Voir les informations",icon: "fa-eye",action: (record) => { router.get(route("chauffeur.informations", record))},privilege: "chauffeur.informations"},
    { text: "Supprimer",action: handleDelete,class:"!text-red-600",icon: "fa-trash", disabled: (record) => record.is_you || props.data.total < 1,privilege: "chauffeur.destroy"},
];
</script>
<template>
    <SousMenuPrincipale :title="title" selectedMenu="chauffeur">
        <template #top>
            <FilterBase v-model="props.filters" @search="applyFilters" @reset="resetFilters">
                <template #import v-if="can('export_chauffeur.export')">
                    <ExportData :show_import="true" :title="'Export data'" :columns="columns" :filter="filter" :url="route('chauffeur.export')" >
                        <template #import >
                            <excel-import-base-standard  v-if="can('chauffeur.import')" :columns="columnsPrint" :model="modelPrint" routeName="import.chauffeur.standard"/>
                        </template>
                    </ExportData>
                </template>

                <template #add>
                    <ButtonIcon
                        v-if="can('chauffeur.store')"
                        @click="() => formModal.add()"
                        type="primary"
                        text="Nouveau Chauffeur"
                        icon="fa-plus"
                        class="!rounded-md hover:!text-white hover:!bg-sky-800 transition-all duration-300"
                    />
                </template>
            </FilterBase>
        </template>

        <BaseDataTable
            :columns="columns"
            :data="data"
            :actions="actions"
            class="main-shadow"
            :btn_action="false"
        >
            <template #bodyCell="{ column, record }">
                <template v-if="column.key === 'photo'">
                        <ImagePreview v-if="record.thumb_img" :src="record.thumb_img" :img="record.img" alt="Chauffeur Photo"/>
                        <span v-else>-</span>
                </template>

                <template v-else-if="column.key === 'matricule'">
                    <span
                        class="text-primary cursor-pointer"
                        title="Double-cliquez pour voir les informations"
                        @dblclick="() => router.get( route('chauffeur.informations', record) )"
                    >
                        {{ record?.matricule || "-" }}
                    </span>
                </template>

                <template v-else-if="column.key === 'nom_complet'">
                    <span
                        class="text-primary cursor-pointer"
                        title="Double-cliquez pour voir les informations"
                        @dblclick="() => router.get( route('chauffeur.informations', record) )"
                    >
                        <span class="font-medium uppercase"
                            >{{ record.nom }}
                            <span class="capitalize">
                                {{ record.prenom }}</span
                            ></span
                        >
                    </span>
                </template>

                <template v-else-if="column.key === 'cin'">
                    <span
                        class="text-primary cursor-pointer"
                        title="Double-cliquez pour voir les informations"
                        @dblclick="() => router.get( route('chauffeur.informations', record) )"
                    >
                        {{ record?.CIN || "-" }}
                    </span>
                </template>
            </template>
        </BaseDataTable>

        <ChauffeurForm ref="formModal" :vehicules="vehicules" />
    </SousMenuPrincipale>
</template>

<style scoped></style>
