<script setup>

import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import DataTable from "@/Components/DataTable.vue";
import {computed, ref} from "vue";

import {confirm_delete} from "../../../../Utils/confirmation_modal.js";
import {router} from "@inertiajs/vue3";
import ButtonIcon from "@/Components/ButtonIcon.vue";
import EvenementForm from "@/Pages/Accueil/Evenement/EvenementForm.vue";
import AproposForm from "@/Pages/Accueil/Apropos/AproposForm.vue";


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
const title = computed(() => `Apropos (${props.data?.total ?? 0})`)

const columns = [
    {key: "image", title: "1er img", dataIndex: "thumb_image"},
    {key: "img_2", title: "2end img", dataIndex: "thumb_img2"},
    {key: "titre_principal", title: "Titre", dataIndex: "titre_principal"},
    {key: "Description", title: "description", dataIndex: "description"},
    {key: "button_label", title: "Boutton Label", dataIndex: "button_label"},
    {key: "button_link", title: "Boutton Link", dataIndex: "button_link"},

]

const handleDelete = (record) => {
    confirm_delete(() => {
        router.delete(route('apropos.destroy', record.id), {
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
    <authenticated-layout :title="title" selected-menu="apropos">
        <template #actions v-if="data.data.length < 0">
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


    <AproposForm ref="formModal">

    </AproposForm>
</template>

<style scoped>

</style>
