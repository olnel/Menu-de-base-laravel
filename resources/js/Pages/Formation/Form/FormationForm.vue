<script setup>
import FormItem from "@/Components/FormItem.vue";
import FormModal from "@/Components/FormModal.vue";
import { router, useForm } from "@inertiajs/vue3";
import { ref } from "vue";

const props = defineProps({
    formations: { type: Array, default: () => [] },
});

const form = useForm({
    id: null,
    nom: null,
    description: null,
    periode_renouvellement_mois: 12,
    alerte_avant_jours: 20,
    formation_suivante_id: null,
});

const open = ref(false);
const title = ref("");

const close = () => {
    open.value = false;
    form.reset();
    form.clearErrors();
};

const add = () => {
    title.value = "Nouvelle Formation";
    form.periode_renouvellement_mois = 12;
    form.alerte_avant_jours = 20;
    form.formation_suivante_id = null;
    open.value = true;
};

const update = (rowData) => {
    router.visit(`${route("formations.show", { formation: rowData.id })}`, {
        preserveState: true,
        preserveScroll: true,
        only: ["flash"],
        onSuccess: (reponse) => {
            const response = reponse.props.flash.data;
            Object.keys(response).forEach((key) => {
                if (key in form) {
                    form[key] = response[key];
                }
            });
            form.formation_suivante_id = response.formation_suivante?.id ?? null;
            title.value = "Modifier la Formation";
            open.value = true;
        },
    });
};

const submit = () => {
    form.clearErrors();
    const method = form.id ? "put" : "post";
    const url = form.id
        ? route("formations.update", form.id)
        : route("formations.store");

    form.transform((data) => ({
        ...data,
        _method: method.toUpperCase(),
    })).post(url, {
        onSuccess: () => close(),
    });
};

const periodeOptions = [
    { label: "1 mois", value: 1 },
    { label: "3 mois", value: 3 },
    { label: "6 mois", value: 6 },
    { label: "1 an", value: 12 },
    { label: "2 ans", value: 24 },
    { label: "3 ans", value: 36 },
    { label: "4 ans", value: 48 },
];

const alerteOptions = [
    { label: "7 jours", value: 7 },
    { label: "15 jours", value: 15 },
    { label: "20 jours", value: 20 },
    { label: "30 jours", value: 30 },
    { label: "45 jours", value: 45 },
    { label: "60 jours", value: 60 },
];

const filteredFormations = () => {
    if (!form.id) return props.formations;
    return props.formations.filter((f) => f.id !== form.id);
};

defineExpose({ add, update, close });
</script>

<template>
    <FormModal
        v-model:open="open"
        :loading="form.processing"
        @close="close"
        @submit="submit"
        :titre="title"
        size="md"
    >
        <div class="space-y-4 px-2">
            <div class="grid grid-cols-1 gap-4">
                <form-item
                    required
                    has-feedback
                    label="Nom de la Formation"
                    :help="form.errors.nom"
                >
                    <a-input
                        v-model:value="form.nom"
                        placeholder="Ex: Sécurité Routière, HSE, Premiers Secours..."
                        size="large"
                    />
                </form-item>

                <form-item
                    has-feedback
                    label="Description"
                    :help="form.errors.description"
                >
                    <a-textarea
                        v-model:value="form.description"
                        placeholder="Description de la formation"
                        :rows="3"
                    />
                </form-item>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <form-item
                    required
                    has-feedback
                    label="Période de Renouvellement"
                    :help="form.errors.periode_renouvellement_mois"
                >
                    <a-select
                        v-model:value="form.periode_renouvellement_mois"
                        placeholder="Sélectionner la période"
                        size="large"
                        :options="periodeOptions"
                        class="w-full"
                    />
                </form-item>

                <form-item
                    required
                    has-feedback
                    label="Alerte Avant Échéance"
                    :help="form.errors.alerte_avant_jours"
                >
                    <a-select
                        v-model:value="form.alerte_avant_jours"
                        placeholder="Nombre de jours avant"
                        size="large"
                        :options="alerteOptions"
                        class="w-full"
                    />
                </form-item>
            </div>

            <form-item
                has-feedback
                label="Formation Suivante (chaînage A → B)"
                :help="form.errors.formation_suivante_id"
            >
                <a-select
                    v-model:value="form.formation_suivante_id"
                    placeholder="Optionnelle : sélectionner la formation suivante"
                    size="large"
                    allow-clear
                    class="w-full"
                >
                    <a-select-option
                        v-for="f in filteredFormations()"
                        :key="f.id"
                        :value="f.id"
                    >
                        {{ f.nom }}
                    </a-select-option>
                </a-select>
                <p class="text-xs text-gray-400 mt-1">
                    Quand cette formation est terminée, une notification proposera de planifier la formation suivante.
                </p>
            </form-item>
        </div>
    </FormModal>
</template>
