<script setup>
import FormItem from "@/Components/FormItem.vue";
import FormModal from "@/Components/FormModal.vue";
import { router, useForm } from "@inertiajs/vue3";
import { ref } from "vue";

const props = defineProps({
    flash: Object,
});

const form = useForm({
    id: null,
    libelle: null,
    description: null,
});

const open = ref(false);
const title = ref("");

const close = () => {
    open.value = false;
    form.reset();
    form.clearErrors();
};

const add = () => {
    title.value = "Nouveau Type de Salarié";
    open.value = true;
};

const update = (rowData) => {
    router.visit(
        `${route("type_salarie.show", { type_salarie: rowData.id })}`,
        {
            preserveState: true,
            preserveScroll: true,
            only: ["flash"],
            onSuccess: (reponse) => {
                const response = reponse.props.flash.data;
                Object.keys(response).forEach((key) => {
                    form[key] = response[key];
                });
                title.value = "Modifier le Type de Salarié";
                open.value = true;
            },
        },
    );
};

const submit = () => {
    form.clearErrors();
    const method = form.id ? "put" : "post";
    const url = form.id
        ? route("type_salarie.update", form.id)
        : route("type_salarie.store");
    form.transform((data) => ({
        ...data,
        _method: method.toUpperCase(),
    })).post(url, {
        onSuccess: () => close(),
        forceFormData: true,
    });
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
        :show_champ_obligatoir="false"
    >
        <div class="grid grid-cols-1 gap-4">
            <form-item
                required
                has-feedback
                label="Libellé"
                :help="form.errors.libelle"
            >
                <a-input
                    v-model:value="form.libelle"
                    placeholder="Ex: Chauffeur, Caissier(e), Chef..."
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
                    placeholder="Description du type de salarié"
                    :rows="4"
                />
            </form-item>
        </div>
    </FormModal>
</template>

<style scoped></style>
