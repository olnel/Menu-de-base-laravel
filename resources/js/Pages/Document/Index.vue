<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import DataTable from "@/Components/DataTable.vue";
import FilterBase from "@/Components/Filter/FilterBase.vue";
import ButtonIcon from "@/Components/ButtonIcon.vue";
import { computed, ref } from "vue";
import { router } from "@inertiajs/vue3";
import { createSearchFilter, gotoSearch } from "../../../Utils/FiltreUtils.js";
import usePermissions from "@/UserPermissions/usePermissions.js";
import DocumentManagerModal from "./Form/DocumentManagerModal.vue";

const { can } = usePermissions();

const props = defineProps({
    entities: { type: Object, default: () => ({}) },
    requiredModels: { type: Array, default: () => [] },
    currentType: { type: String, default: "Personne" },
    modelClass: { type: String, default: "" },
    documentableTypes: { type: Array, default: () => [] },
    filters: { type: Object, default: () => ({}) },
});

const managerModal = ref();
const title = computed(() => `Documents - ${props.currentType}`);
const filter = ref(createSearchFilter());

// On construit dynamiquement les colonnes basées sur les modèles requis
const columns = computed(() => {
    const baseColumns = [
        {
            key: "entity_name",
            title: props.currentType === 'Personne' ? 'Nom complet' : 'Désignation / Matricule',
            customRender: ({ record }) => {
                if (props.currentType === 'Personne') {
                    return `${record.nom} ${record.prenom ?? ''}`;
                }
                return record.immatriculation || record.numero_remorque || record.matricule || record.nom;
            }
        }
    ];

    // Colonnes pour chaque document requis
    props.requiredModels.forEach(model => {
        baseColumns.push({
            key: `doc_${model.document_type_id}`,
            title: model.document_type.nom,
            align: 'center',
        });
    });

    return baseColumns;
});

const applyFilters = (data) => {
    filter.value = data;
    const url = route("dynamic.documents.index");
    gotoSearch(filter.value, url);
};

const resetFilters = () => {
    filter.value.search = "";
    filter.value.type = props.currentType;
    applyFilters(filter.value);
};

const handleTypeChange = (type) => {
    router.get(route("dynamic.documents.index"), { type }, { preserveScroll: true });
};

// Vérifie si un document est présent pour un type donné
const getDocumentStatus = (entity, documentTypeId) => {
    return entity.documents.find(d => d.document_type_id === documentTypeId);
};

const actions = [
    {
        text: "Gérer les documents",
        action: (record) => managerModal.value.openModal(record),
        icon: "fa-folder-open",
        privilege: "dynamic.documents.upload",
    },
];
</script>

<template>
    <AuthenticatedLayout :title="title" selected-menu="documents_administratifs">
        <div class="mb-6">
            <a-radio-group v-model:value="props.currentType" button-style="solid" size="large" @change="e => handleTypeChange(e.target.value)">
                <a-radio-button v-for="type in documentableTypes" :key="type.id" :value="type.nom">
                    {{ type.nom }}
                </a-radio-button>
            </a-radio-group>
        </div>

        <FilterBase
            v-model="props.filters"
            @search="applyFilters"
            @reset="resetFilters"
        >
        </FilterBase>

        <DataTable
            :columns="columns"
            :data="entities"
            :actions="actions"
            :filters="props.filters"
            class="main-shadow"
            :show-index="true"
            :btn_action="false"
        >
            <template #bodyCell="{ column, record }">
                <template v-if="column.key.startsWith('doc_')">
                    <div class="flex justify-center">
                        <template v-if="getDocumentStatus(record, parseInt(column.key.split('_')[1]))">
                            <a-tag color="success">
                                <font-awesome-icon icon="fa-solid fa-check" class="mr-1" /> Présent
                            </a-tag>
                        </template>
                        <template v-else>
                            <a-tag color="error">
                                <font-awesome-icon icon="fa-solid fa-xmark" class="mr-1" /> Manquant
                            </a-tag>
                        </template>
                    </div>
                </template>
            </template>
        </DataTable>

        <DocumentManagerModal 
            ref="managerModal" 
            :model-class="modelClass" 
            :required-models="requiredModels" 
        />
    </AuthenticatedLayout>
</template>
