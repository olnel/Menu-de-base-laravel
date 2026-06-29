<script setup>

import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import DataTable from "@/Components/DataTable.vue";
import {computed, ref} from "vue";

import {confirm_delete} from "../../../../Utils/confirmation_modal.js";
import {router} from "@inertiajs/vue3";
import ButtonIcon from "@/Components/ButtonIcon.vue";
import PointdeventedetailForm from "@/Pages/Accueil/Pointdeventedetail/PointdeventedetailForm.vue";


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
const title = computed(() => `Boutique (${props.data?.total ?? 0})`)

const columns = [
    {key: "image", title: "image", dataIndex: "thumb_img"},
    {key: "nom_boutique", title: "Boutique", dataIndex: "nom_boutique"},
    {key: "telephone", title: "Contact", dataIndex: "telephone"},
    {key: "addresse", title: "Adresse", dataIndex: "addresse"},
]

const handleDelete = (record) => {
    confirm_delete(() => {
        router.delete(route('pointdeventedetail.destroy', record.id), {
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
    <authenticated-layout :title="title" selected-menu="pointdeventedetail">
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


    <PointdeventedetailForm ref="formModal">

    </PointdeventedetailForm>
</template>

<style scoped>

</style>
