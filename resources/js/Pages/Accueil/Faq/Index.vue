<script setup>

import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import DataTable from "@/Components/DataTable.vue";
import {computed, ref} from "vue";

import {confirm_delete} from "../../../../Utils/confirmation_modal.js";
import {router} from "@inertiajs/vue3";
import ButtonIcon from "@/Components/ButtonIcon.vue";
import EvenementForm from "@/Pages/Accueil/Evenement/EvenementForm.vue";
import AproposForm from "@/Pages/Accueil/Apropos/AproposForm.vue";
import FaqForm from "@/Pages/Accueil/Faq/FaqForm.vue";
import FooterPage from "@/Pages/Public/Components/Layouts/Partials/FooterPage.vue";


const props = defineProps({
    data: {
        type: Object,
        default: () => {
        }
    },
    filters: {
        type: Object,
        default: () => {
        }
    },

})

const formModal = ref()
const title = computed(() => `FAQ (${props.data?.total ?? 0})`)

const columns = [
    {title: "Question", dataIndex: "question", key: "Question", },
    {title: "Réponse", dataIndex: "reponse", key: "reponse", },
]

const handleDelete = (record) => {
    confirm_delete(() => {
        router.delete(route('faq.destroy', record.id), {
            preserveScroll: true,
        })
    });
}

const actions = [
    {text: "Modifier", action: (record) => formModal.value.update(record), icon: 'fa-pen-to-square'},
    {
        text: "Supprimer",
        action: handleDelete,
        icon: 'fa-trash',
        disabled: (record) => record.is_you || props.data.total < 1
    },
]

</script>

<template>
    <authenticated-layout :title="title" selected-menu="faq">
<!--        v-if="data.data.length < 0"-->
        <template #actions >
            <ButtonIcon @click="() => formModal.add()" type="primary" text="Nouveau" icon="fa-plus"/>
        </template>

        <data-table
            :columns="columns"
            :data="data"
            :actions="actions"
            class="main-shadow mt-5"
            :show-index="true">

        </data-table>

    </authenticated-layout>


    <FaqForm ref="formModal" />
</template>

<style scoped>

</style>
