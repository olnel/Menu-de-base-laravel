<template>
    <SousMenuPrincipale :title="title" selectedMenu="client">
        <template #top>
            <FilterBase
                v-model="props.filters"
                @search="applyFilters"
                @reset="resetFilters"
            >

                <template #import v-if="can('export_client.export')">
                    <ExportData :show_import="true" :title="'Export data'" :columns="columns" :filter="filter" :url="route('client.export')" >
                        <template #import >
                            <excel-import-base-standard :columns="columnsPrint" :model="modelPrint"></excel-import-base-standard>
                        </template>
                    </ExportData>
                </template>

                <template #add>
                    <ButtonIcon
                        v-if="can('client.store')"
                        @click="() => formModal.add()"
                        type="primary"
                        text="Nouveau Client"
                        icon="fa-plus"
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
            :btn_action="true"
        >
        </DataTable>
        <ClientFormulaire ref="formModal" />
    </SousMenuPrincipale>
</template>
<script setup>
import ButtonIcon from "@/Components/ButtonIcon.vue";
import DataTable from "@/Components/DataTable.vue";
import FilterBase from "@/Components/Filter/FilterBase.vue";
import SousMenuPrincipale from "@/Pages/Menu/SousMenuPrincipale.vue";
import ClientFormulaire from "@/Pages/Tiers/Client/Formulaire/ClientFormulaire.vue";
import usePermissions from "@/UserPermissions/usePermissions.js";
import { router } from "@inertiajs/vue3";
import { computed, ref } from "vue";
import { confirm_delete, confirm_save } from "../../../../Utils/confirmation_modal.js";
import {
    createSearchFilter,
    gotoSearch,
} from "../../../../Utils/FiltreUtils.js";
import ExportData from "@/Components/ExportData/ExportData.vue";
import ExcelImportBaseStandard from "@/Components/ExcelImportBaseStandard.vue";

const { can } = usePermissions();

const props = defineProps({
    data: {
        type: Object,
        default: () => ({}),
    },
    filters: {
        type: Object,
        default: () => ({}),
    },
    flash: {
        type: Object,
        default: () => ({}),
    },
});

const formModal = ref();
const title = computed(() => `Liste des Clients (${props.data?.total ?? 0})`);
const filter = ref(createSearchFilter());

const modelPrint = 'App\\Models\\Client'
const columnsPrint = ['numero', 'nom_client', 'adresse_client', 'mail_client', 'tel_client', 'nif_client', 'stat_client', 'rcs_client'];

const columns = [
    { key: "numero", title: "Numero", dataIndex: "numero", width: 100 },
    { key: "nom_client", title: "Nom", dataIndex: "nom_client" },
    { key: "login", title: "Login", dataIndex: "login", width: 140 },
    { key: "adresse_client", title: "Adresse", dataIndex: "adresse_client" },
    { key: "mail_client", title: "E-Mail", dataIndex: "mail_client" },
    { key: "tel_client", title: "Contact", dataIndex: "tel_client", width: 100 },
    { key: "nif_client", title: "NIF", dataIndex: "nif_client", width: 100 },
    { key: "stat_client", title: "STAT", dataIndex: "stat_client", width: 100 },
    { key: "rcs_client", title: "RCS", dataIndex: "rcs_client", width: 100 },
];

const handleDelete = (record) => {
    confirm_delete(() => {
        router.delete(route("client.destroy", record.id), {
            preserveScroll: true,
        });
    });
};

const handleResetPassword = (record) => {
    confirm_save(
        () => {
            router.post(
                route("client.reset-password", record.id),
                {},
                { preserveScroll: true }
            );
        },
        "Réinitialiser le mot de passe ?",
        "Le mot de passe sera remis à « password ».",
        "RÉINITIALISER"
    );
};

const actions = [
    {
        text: "Modifier",
        action: (record) => formModal.value.update(record),
        icon: "fa-pen-to-square",
        privilege: "client.update",
    },
    {
        text: "Réinitialiser mot de passe",
        action: handleResetPassword,
        icon: "fa-key",
        disabled: (record) => !record.login,
        privilege: "client.update",
    },
    {
        text: "Supprimer",
        action: handleDelete,
        icon: "fa-trash",
        class: "!text-red-600",
        disabled: (record) => record.is_you || props.data.total < 1,
        privilege: "client.destroy",
    },
];
const applyFilters = (data) => {
    filter.value = data;
    const url = route("client.index");
    gotoSearch(filter.value, url);
};

const resetFilters = () => {
    filter.value.search = "";
    applyFilters();
};
</script>

<style scoped>
/* Styles spécifiques si nécessaires */
</style>
