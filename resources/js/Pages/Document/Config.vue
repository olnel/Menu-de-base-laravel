<script setup>
import SousMenuPrincipale from "@/Pages/Menu/SousMenuPrincipale.vue";
import DataTable from "@/Components/DataTable.vue";
import ButtonIcon from "@/Components/ButtonIcon.vue";
import FilterBase from "@/Components/Filter/FilterBase.vue";
import { computed, ref } from "vue";
import { router } from "@inertiajs/vue3";
import { confirm_delete } from "../../../Utils/confirmation_modal.js";
import { createSearchFilter, gotoSearch } from "../../../Utils/FiltreUtils.js";
import usePermissions from "@/UserPermissions/usePermissions.js";
import DocumentModelForm from "./Form/DocumentModelForm.vue";

const { can } = usePermissions();

const props = defineProps({
    data: { type: Object, default: () => ({}) },
    documentableTypes: { type: Array, default: () => [] },
    documentTypes: { type: Array, default: () => [] },
    filters: { type: Object, default: () => ({}) },
});

const formModal = ref();
const title = computed(() => `Modèles de Documents (${props.data?.total ?? 0})`);
const filter = ref(createSearchFilter());

const columns = [
    {
        key: "nom",
        title: "Type d'Entité",
        dataIndex: "nom",
    },
    // {
    //     key: "model_class",
    //     title: "Classe Associée",
    //     dataIndex: "model_class",
    // },
    {
        key: "created_at",
        title: "Date de Création",
        dataIndex: "created_at",
    },
    {
        key: "updated_at",
        title: "Date de Modification",
        dataIndex: "updated_at",
    },
];

const innerColumns = [
    { title: 'Type de Document', dataIndex: ['document_type', 'nom'], key: 'doc_name' },
    { title: 'Obligatoire', dataIndex: 'obligatoire', key: 'obligatoire' },
    { title: 'Expiration', dataIndex: 'expiration_required', key: 'expiration' },
    { title: 'Alerte (jours)', dataIndex: 'alert_days', key: 'alert' },
    { title: 'Statut', dataIndex: 'actif', key: 'statut' },
];

const handleDelete = (record) => {
    confirm_delete(() => {
        // Here record is DocumentableType, but we might want to delete all its models
        // Or keep delete for individual models in the expanded view if needed.
        // For now, let's keep it simple.
        router.delete(route("config.destroy", record.id), {
            preserveScroll: true,
        });
    });
};

const actions = [
    {
        text: "Modifier",
        action: (record) => formModal.value.update(record),
        icon: "fa-pen-to-square",
        privilege: "dynamic.documents.config.store",
    },
    {
        text: "Supprimer",
        action: handleDelete,
        icon: "fa-trash",
        class: "!text-red-600",
        privilege: "dynamic.documents.config.destroy",
    },
];

const applyFilters = (data) => {
    filter.value = data;
    const url = route("config");
    gotoSearch(filter.value, url);
};

const resetFilters = () => {
    filter.value.search = "";
    applyFilters();
};
</script>

<template>
    <SousMenuPrincipale :title="title" selectedMenu="document_config">
        <template #top>
            <FilterBase
            v-model="props.filters"
            @search="applyFilters"
            @reset="resetFilters"
        >
            <template #add>
                <ButtonIcon
                    v-if="can('dynamic.documents.config.store')"
                    icon="fa-solid fa-plus"
                    @click="formModal.add()"
                >
                    Nouveau Modèle
                </ButtonIcon>
            </template>
        </FilterBase>
        </template>

        <DataTable
            :columns="columns"
            :data="data"
            :actions="actions"
            :filters="props.filters"
            class="main-shadow"
            :show-index="true"
            :btn_action="false"
        >
            <template #bodyCell="{ column, text }">
                <template v-if="column.key === 'created_at' || column.key === 'updated_at'">
                    {{ text ? new Date(text).toLocaleString() : '-' }}
                </template>
            </template>

            <template #expandedRowRender="{ record }">
                <a-table
                    :columns="innerColumns"
                    :data-source="record.models"
                    :pagination="false"
                    size="small"
                >
                    <template #bodyCell="{ column, record: innerRecord }">
                        <template v-if="column.key === 'obligatoire'">
                            <a-tag :color="innerRecord.obligatoire ? 'blue' : 'gray'">
                                {{ innerRecord.obligatoire ? 'Oui' : 'Non' }}
                            </a-tag>
                        </template>
                        <template v-if="column.key === 'expiration'">
                            <a-tag :color="innerRecord.expiration_required ? 'orange' : 'gray'">
                                {{ innerRecord.expiration_required ? 'Oui' : 'Non' }}
                            </a-tag>
                        </template>
                        <template v-if="column.key === 'statut'">
                            <a-badge :status="innerRecord.actif ? 'success' : 'error'" :text="innerRecord.actif ? 'Actif' : 'Inactif'" />
                        </template>
                    </template>
                </a-table>
            </template>
        </DataTable>

        <DocumentModelForm
            ref="formModal"
            :documentable-types="documentableTypes"
            :document-types="documentTypes"
        />
    </SousMenuPrincipale>
</template>
