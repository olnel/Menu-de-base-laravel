<script setup>

import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import DataTable from "@/Components/DataTable.vue";
import {computed, ref} from "vue";

import {confirm_delete} from "../../../../Utils/confirmation_modal.js";
import {router} from "@inertiajs/vue3";
import ButtonIcon from "@/Components/ButtonIcon.vue";
import EvenementForm from "@/Pages/Accueil/Evenement/EvenementForm.vue";


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
    flash: {
        type: Object,
        default: () => ({})
    }

})

const formModal = ref()
const title = computed(() => `Evènement (${props.data?.total ?? 0})`)

const columns = [
    {key: "image", title: "image", dataIndex: "thumb_image"},
    {key: "Titre", title: "titre", dataIndex: "title"},
    {key: "Description", title: "description", dataIndex: "description"},
    {key: "Boutton Label", title: "button_label", dataIndex: "button_label"},
    {key: "Boutton Link", title: "button_link", dataIndex: "button_link"},

]

const handleDelete = (record) => {
    confirm_delete(() => {
        router.delete(route('evenement.destroy', record.id), {
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
    <authenticated-layout :title="title" selected-menu="evenement">
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


    <EvenementForm ref="formModal">

    </EvenementForm>
</template>

<style scoped>

</style>
