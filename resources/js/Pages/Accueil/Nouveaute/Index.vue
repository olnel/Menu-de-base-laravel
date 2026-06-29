<script setup>

import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import DataTable from "@/Components/DataTable.vue";
import {computed, ref} from "vue";

import {confirm_delete} from "../../../../Utils/confirmation_modal.js";
import {router} from "@inertiajs/vue3";
import ButtonIcon from "@/Components/ButtonIcon.vue";
import NouveauteForm from "@/Pages/Accueil/Nouveaute/NouveauteForm.vue";


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
const title = computed(() => `News (${props.data?.total ?? 0})`)

const columns = [
    {key: "image", title: "image", dataIndex: "thumb_img"},
    {key: "Titre", title: "titre", dataIndex: "title"},
    {key: "Sous-Titre", title: "subtitle", dataIndex: "subtitle"},
    {key: "button_label", title: "Boutton Label", dataIndex: "button_label"},
    {key: "button_link", title: "Boutton Link", dataIndex: "button_link"},
    {key: "description", title: "Description", dataIndex: "description"},
]

const handleDelete = (record) => {
    confirm_delete(() => {
        router.delete(route('nouveaute.destroy', record.id), {
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
    <authenticated-layout :title="title" selected-menu="nouveaute">
        <template #actions>
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


    <NouveauteForm ref="formModal">

    </NouveauteForm>
</template>

<style scoped>

</style>
