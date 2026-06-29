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
    nom_famille_article: null,
});

const open = ref(false);
const title = ref("");

const close = () => {
    open.value = false;
    form.reset();
    form.clearErrors();
};

const add = () => {
    title.value = "Nouvelle Famille";
    open.value = true;
};

const update = (rowData) => {
    router.visit(
        `${route("article_famille.show", { article_famille: rowData.id })}`,
        {
            preserveState: true,
            preserveScroll: true,
            only: ["flash"],
            onSuccess: (reponse) => {
                const response = reponse.props.flash.data;
                Object.keys(response).forEach((key) => {
                    form[key] = response[key];
                });
                title.value = "Modifier";
                open.value = true;
            },
        }
    );
};

const submit = () => {
    form.clearErrors();
    const method = form.id ? "put" : "post";
    const url = form.id
        ? route("article_famille.update", form.id)
        : route("article_famille.store");
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
        size="sm"
        :show_champ_obligatoir="false"
    >
        <div class="">
            <form-item
                required
                has-feedback
                label="Libellé"
                :help="form.errors.nom_famille_article"
            >
                <a-input
                    v-model:value="form.nom_famille_article"
                    placeholder=""
                    size="large"
                />
            </form-item>
        </div>
    </FormModal>
</template>

<style scoped></style>
