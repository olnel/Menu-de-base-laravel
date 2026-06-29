<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { computed, ref } from "vue";
import { confirm_delete } from "../../../../Utils/confirmation_modal.js";
import { router } from "@inertiajs/vue3";
import ButtonIcon from "@/Components/ButtonIcon.vue";
import PartenaireForm from "@/Pages/Accueil/Partenaire/PartenaireForm.vue";
import CardListe from "@/Components/CardListe.vue";

const props = defineProps({
    data: {
        type: Object,
        default: () => ({})
    },
    filters: {
        type: Object,
        default: () => ({})
    },
});

const formModal = ref();
const title = computed(() => `Partenaires (${props.data?.total ?? 0})`);

const handleDelete = (record) => {
    confirm_delete(() => {
        router.delete(route('partener.destroy', record.id), {
            preserveScroll: true,
        })
    });
}

const actions = [
    {
        text: "Modifier",
        action: (record) => formModal.value.update(record),
        icon: 'fa-pen-to-square',
        danger: false
    },
    {
        text: "Supprimer",
        action: handleDelete,
        icon: 'fa-trash',
        danger: true,
        disabled: (record) => record.is_you || props.data.total < 1
    },
];
</script>

<template>
    <AuthenticatedLayout :title="title" selected-menu="partenaire">
        <template #actions>
            <ButtonIcon
                @click="() => formModal.add()"
                type="primary"
                text="Nouveau Partenaire"
                icon="fa-plus"
            />
        </template>

        <CardListe
            :partners="data.data"
            :actions="actions"
            @edit="(record) => formModal.update(record)"
            @delete="handleDelete"
        />


    </AuthenticatedLayout>

    <PartenaireForm ref="formModal" />
</template>

<style scoped>

</style>
