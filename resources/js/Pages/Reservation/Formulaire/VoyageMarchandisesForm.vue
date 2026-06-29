<template>
    <div class="p-4">
        <a-table
            :columns="marchandiseColumns"
            :data-source="form.voyage_marchandises"
            :pagination="false"
            size="small"
            bordered
        >
            <template #title>
                <a-button
                    type="dashed"
                    size="large"
                    class="w-full"
                    @click="addMarchandise"
                >
                    <PlusOutlined />
                    Ajouter
                </a-button>
            </template>

            <template #bodyCell="{ column, record, index }">
                <template v-if="column.key === 'marchandise_item'">
                    <VoyageMarchandiseItem
                        :item="record"
                        :status="getMarchandiseItemStatus(index)"
                        @remove="removeMarchandise(index)"
                    />
                </template>
            </template>
        </a-table>
        <div class="flex justify-end mt-4">
            <a-button
                type="primary"
                size="large"
                :loading="form.processing"
                @click="submitMarchandises"
            >
                Enregistrer les Marchandises
            </a-button>
        </div>
    </div>
</template>

<script setup>
import VoyageMarchandiseItem from "@/Components/VoyageMarchandiseItem.vue";
import { PlusOutlined } from "@ant-design/icons-vue";
import { useForm } from "@inertiajs/vue3";
import { watch } from "vue";

const props = defineProps({
    voyageId: {
        type: Number,
        required: true,
    },
    initialMarchandises: {
        type: Array,
        default: () => [],
    },
    refreshVoyageDetails: {
        type: Function,
        required: true,
    },
});

const form = useForm({
    voyage_marchandises: [],
});

watch(
    () => props.initialMarchandises,
    (newVal) => {
        form.voyage_marchandises = newVal || [];
    },
    { immediate: true, deep: true }
);

const marchandiseColumns = [
    {
        title: "Marchandise",
        key: "marchandise_item",
    },
];

const getMarchandiseItemStatus = (index) => {
    return {
        designation: form.errors[`voyage_marchandises.${index}.designation`],
    };
};

const addMarchandise = () => {
    form.voyage_marchandises.push({ designation: "" });
};

const removeMarchandise = (index) => {
    form.voyage_marchandises.splice(index, 1);
    form.isDirty = true;
};

const submitMarchandises = () => {
    if (!props.voyageId) return;

    form.put(
        route("voyages.marchandises.sync", {
            voyage: props.voyageId,
        }),
        {
            onSuccess: () => {
                props.refreshVoyageDetails(props.voyageId);
            },
            onError: (errors) => {
                console.error("Erreur:", errors);
            },
        }
    );
};
</script>

<style scoped>
/* Styles spécifiques au composant Marchandises */
</style>
