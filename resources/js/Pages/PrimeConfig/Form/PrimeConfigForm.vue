<script setup>
import FormItem from "@/Components/FormItem.vue";
import FormModal from "@/Components/FormModal.vue";
import { useForm } from "@inertiajs/vue3";
import { ref, watch } from "vue";

const props = defineProps({
    types_salarie: { type: Array, default: () => [] },
});

const form = useForm({
    id: null,
    libelle: null,
    montant: 0,
    type_salarie_id: null,
    is_global: false,
    is_per_voyage: false,
    is_actif: true,
});

const open = ref(false);
const title = ref("");

const close = () => {
    open.value = false;
    form.reset();
    form.clearErrors();
};

const add = () => {
    title.value = "Nouvelle Configuration de Prime";
    open.value = true;
};

const update = (rowData) => {
    Object.keys(rowData).forEach((key) => {
        if (key in form) {
            form[key] = rowData[key];
        }
    });
    // Forcer le format boolean pour les switchs
    form.is_global = !!rowData.is_global;
    form.is_per_voyage = !!rowData.is_per_voyage;
    form.is_actif = !!rowData.is_actif;

    title.value = "Modifier la Prime";
    open.value = true;
};

const submit = () => {
    form.clearErrors();
    const url = form.id
        ? route("prime_config.update", form.id)
        : route("prime_config.store");

    const method = form.id ? "put" : "post";

    form.submit(method, url, {
        onSuccess: () => close(),
    });
};

// Si Global est coché, désactiver la sélection par type
watch(() => form.is_global, (val) => {
    if (val) {
        form.type_salarie_id = null;
    }
});

defineExpose({ add, update, close });
</script>

<template>
    <FormModal
        v-model:open="open"
        :loading="form.processing"
        @close="close"
        @submit="submit"
        :titre="title"
    >
        <div class="grid grid-cols-1 gap-y-4">
            <form-item required label="Libellé de la prime" :help="form.errors.libelle">
                <a-input v-model:value="form.libelle" placeholder="Ex: Prime de voyage, Indemnité..." size="large" />
            </form-item>

            <form-item required label="Montant" :help="form.errors.montant">
                <a-input-number
                    v-model:value="form.montant"
                    placeholder="0.00"
                    size="large"
                    class="w-full"
                    prefix="Ar"
                    :min="0"
                    :precision="2"
                    :formatter="value => `${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ' ')"
                    :parser="value => value.replace(/\s?|(\s*)/g, '')"
                />
            </form-item>

            <div class="flex gap-8 p-4 bg-gray-50 rounded-xl border border-gray-100">
                <form-item label="Prime Globale" :help="form.errors.is_global">
                    <a-switch v-model:checked="form.is_global" />
                </form-item>

                <form-item label="Par Voyage" :help="form.errors.is_per_voyage">
                    <a-switch v-model:checked="form.is_per_voyage" />
                </form-item>

                <form-item label="Actif" :help="form.errors.is_actif">
                    <a-switch v-model:checked="form.is_actif" />
                </form-item>
            </div>

            <form-item v-if="!form.is_global" label="Type de Salarié concerné" :help="form.errors.type_salarie_id">
                <a-select
                    v-model:value="form.type_salarie_id"
                    placeholder="Sélectionner un type"
                    size="large"
                    :options="types_salarie"
                    :field-names="{ label: 'label', value: 'value' }"
                    class="w-full"
                />
                <p class="text-[10px] text-gray-400 mt-1 italic">La prime ne s'appliquera qu'aux salariés de ce type.</p>
            </form-item>
        </div>
    </FormModal>
</template>

<style scoped></style>
