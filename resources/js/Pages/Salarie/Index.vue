<script setup>
import ButtonIcon from "@/Components/ButtonIcon.vue";
import DataTable from "@/Components/DataTable.vue";
import DynamicDocumentManager from "@/Components/DynamicDocumentManager.vue";
import FilterBase from "@/Components/Filter/FilterBase.vue";
import SousMenuPrincipale from "@/Pages/Menu/SousMenuPrincipale.vue";
import SalarieForm from "@/Pages/Salarie/Form/SalarieForm.vue";
import usePermissions from "@/UserPermissions/usePermissions.js";
import { router } from "@inertiajs/vue3";
import { computed, ref } from "vue";
import { confirm_delete } from "../../../Utils/confirmation_modal.js";
import { createSearchFilter, gotoSearch } from "../../../Utils/FiltreUtils.js";
import {text} from "@fortawesome/fontawesome-svg-core";

const { can } = usePermissions();

const props = defineProps({
    data: { type: Object, default: () => ({}) },
    types_salarie: { type: Array, default: () => [] },
    required_documents: { type: Array, default: () => [] },
    vehicules: { type: Array, default: () => [] },
    chauffeurs_list: { type: Array, default: () => [] },
    filters: { type: Object, default: () => ({}) },
    flash: { type: Object, default: () => ({}) },
});

const formModal = ref();
const docModalOpen = ref(false);
const selectedSalarie = ref(null);

const title = computed(() => `Salariés (${props.data?.total ?? 0})`);

const filter = ref(createSearchFilter());

const getMissingDocs = (record) => {
    if (!props.required_documents || !record.documents) return [];
    return props.required_documents.filter(reqDoc => {
        // Un document est considéré manquant s'il est obligatoire ET qu'aucun document de ce type n'existe
        const exists = record.documents.some(doc => doc.document_type_id === reqDoc.document_type_id);
        return reqDoc.obligatoire && !exists;
    });
};

const columns = [
    {
        key: "matricule",
        title: "Matricule",
        dataIndex: "matricule",
        customCell: () => ({ class: "!font-bold !text-primary" }),
        width:"100px"

    },
    {
        key: "nom",
        title: "Nom & Prénom",
        dataIndex: "nom",
        customRender: ({ record }) => `${record.nom} ${record.prenom ?? ''}`,
        width:"400px"

    },
    {
        key: "type_salarie",
        title: "Type",
        dataIndex: ["type_salarie", "libelle"],
        width:"150px"
    },
    {
        key: "docs_manquants",
        title: "Docs. Obligatoires",
        align: "center",
        width:"200px"
    },
    {
        key: "salaire",
        title: "Salaire",
        dataIndex: "salaire",
        align: "right",
        customRender: ({ text }) => text ? new Intl.NumberFormat('fr-FR', { minimumFractionDigits: 2 }).format(text) : '-',
        width:"200px"
    },
    {
        key: "email",
        title: "Email",
        dataIndex: "email",
        width:"300px"
    },
    {
        key: "telephone",
        title: "Téléphone",
        dataIndex: "telephone",
        width:"300px"
    },
];

const handleDelete = (record) => {
    confirm_delete(() => {
        router.delete(route("salarie.destroy", record.id), {
            preserveScroll: true,
        });
    });
};

const openDocModal = (record) => {
    selectedSalarie.value = record;
    docModalOpen.value = true;
};

const actions = [
    {
        text: "Documents",
        action: openDocModal,
        icon: "fa-folder-open",
        class: "!text-amber-500",
        privilege: "salarie.update",
    },
    {
        text: "Modifier",
        action: (record) => formModal.value.update(record),
        icon: "fa-pen-to-square",
        privilege: "salarie.update",
    },
    {
        text: "Supprimer",
        action: handleDelete,
        icon: "fa-trash",
        class: "!text-red-600",
        disabled: (record) => props.data.total < 1,
        privilege: "salarie.destroy",
    },
];

const applyFilters = (data) => {
    filter.value = data;
    const url = route("salarie.index");
    gotoSearch(filter.value, url);
};

const resetFilters = () => {
    filter.value.search = "";
    applyFilters();
};
</script>

<template>
    <SousMenuPrincipale :title="title" selectedMenu="salarie">
        <template #top>
            <FilterBase
                v-model="props.filters"
                @search="applyFilters"
                @reset="resetFilters"
            >
                <template #add>
                    <ButtonIcon
                        v-if="can('salarie.store')"
                        @click="() => formModal.add()"
                        type="primary"
                        text="Nouveau Salarié"
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
            :btn_action="false"
        >
            <template #bodyCell="{ column, record, text }">
                <template v-if="column.key === 'docs_manquants'">
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
                <template v-if="column.key === 'salaire'">
                    {{text}} Ar
                </template>
            </template>
        </DataTable>

        <!-- Modal pour la gestion des documents -->
        <a-modal
            v-model:open="docModalOpen"
            :title="`Documents : ${selectedSalarie?.nom} ${selectedSalarie?.prenom ?? ''}`"
            width="1000px"
            :footer="null"
            centered
            destroyOnClose
        >
            <div class="py-4">
                <DynamicDocumentManager
                    v-if="selectedSalarie"
                    modelClass="App\Models\Salarie"
                    :modelId="selectedSalarie.id"
                />
            </div>
        </a-modal>

        <SalarieForm
            ref="formModal"
            :types_salarie="types_salarie"
            :required_documents="required_documents"
            :vehicules="vehicules"
            :chauffeurs_list="chauffeurs_list"
        />
    </SousMenuPrincipale>
</template>

<style scoped></style>
