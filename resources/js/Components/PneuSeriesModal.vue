<template>
    <FormModal
        v-model:open="visible"
        :titre="`Séries de pneus - ${article?.reference}`"
        size="md"
        :showFooter="false"
        :show_champ_obligatoir="false"
    >
        <div v-if="loading" class="text-center py-4">
            <a-spin />
        </div>

        <div v-else-if="seriesData?.length > 0">
            <a-table
                :dataSource="seriesData"
                :pagination="false"
                :scroll="{ x: 'max-content' }"
                size="small"

            >
                <a-table-column
                    title="Date"
                    dataIndex="date_appro"
                    key="date_appro"
                >
                    <template #default="{ record }">
                        {{ formatDate(record.date_appro) }}
                    </template>
                </a-table-column>
                <a-table-column
                    title="N° Série"
                    dataIndex="numero_serie"
                    key="numero_serie"
                />
                <a-table-column title="État" dataIndex="etat" key="etat">
                    <template #default="{ record }">
                        {{ record.etat || "Non défini" }}
                    </template>
                </a-table-column>
                <a-table-column
                    title="Magasin"
                    dataIndex="nom_magasin"
                    key="nom_magasin"
                />
                <a-table-column
                    title="Fournisseur"
                    dataIndex="nom_fournisseur"
                    key="nom_fournisseur"
                >
                    <template #default="{ record }">
                        {{ record.nom_fournisseur || "-" }}
                    </template>
                </a-table-column>
            </a-table>
        </div>

        <div v-else-if="!loading" class="text-center py-8 text-gray-500">
            <p>Aucune série trouvée</p>
        </div>
    </FormModal>
</template>

<script setup>
import { router } from "@inertiajs/vue3";
import { ref, watch } from "vue";
import FormModal from "./FormModal.vue";

const visible = ref(false);
const loading = ref(false);
const seriesData = ref([]);
const article = ref(null);

const props = defineProps({ pneuSeriesData: { type: Object, default: null } });

// Watcher pour récupérer les données quand elles arrivent via Inertia
watch(
    () => props.pneuSeriesData,
    (newData) => {
        if (newData && visible.value) {
            seriesData.value = newData.series || [];
            loading.value = false;
        }
    },
    { immediate: true }
);

const openModal = (articleData) => {
    article.value = articleData;
    visible.value = true;
    loading.value = true;
    seriesData.value = [];

    // Utiliser Inertia pour récupérer les données
    router.get(
        route("article.pneu_series", articleData.id),
        {},
        {
            preserveState: true,
            preserveScroll: true,
            only: ["flash"],
            onSuccess: () => {
                loading.value = false;
            },
            onError: () => {
                loading.value = false;
                console.error("Erreur lors du chargement des séries");
            },
        }
    );
};
const formatDate = (dateString) => {
    if (!dateString) return "-";
    return new Date(dateString).toLocaleDateString("fr-FR");
};

defineExpose({
    openModal,
});
</script>

<style scoped>
/* Reduce padding for table cells inside this modal only */
.ant-table-wrapper :deep(.ant-table-cell) {
    padding-top: 6px;
    padding-bottom: 6px;
    padding-left: 8px;
    padding-right: 8px;
}

.ant-table-wrapper :deep(.ant-table-thead > tr > th) {
    padding-top: 6px;
    padding-bottom: 6px;
}

.ant-table-wrapper :deep(.ant-table-tbody > tr > td) {
    padding-top: 6px;
    padding-bottom: 6px;
}
</style>
