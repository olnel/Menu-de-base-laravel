<script setup>

import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import DataTable from "@/Components/DataTable.vue";
import Toolbar from "@/Components/Toolbar.vue";
import ButtonIcon from "@/Components/ButtonIcon.vue";
import Recherche from "@/Components/Recherche.vue";
import {computed, ref} from "vue";
import {reloadPage} from "../../../Utils/functions.js";
import {confirm_delete} from "../../../Utils/confirmation_modal.js";
import {router} from "@inertiajs/vue3";
import CategorieForm from "@/Pages/Categorie/CategorieForm.vue";
import FilterButtonItem from "@/Components/FilterButtonItem.vue";
import FilterButton from "@/Components/FilterButton.vue";

const props = defineProps({
    data: {
        type: Object,
        default: () => {}
    },
    filters: {
        type: Object,
        default: () => {}
    },

})

const formModal = ref()
const title = computed(() => `Catégorie (${ props.data?.total ?? 0 })`)

const columns = [
    {key: "nom_categorie", title: "Catégorie", dataIndex: "nom_categorie"},
]

const handleDelete = (record) => {
    confirm_delete(() => {
        router.delete(route('utilisateur.destroy', record.id), {
            preserveScroll: true,
        })
    });
}

const actions = [
    {text: "Modifier", action: (record) => formModal.value.update(record), icon: 'fa-pen-to-square'},
    {text: "Supprimer", action: handleDelete, icon: 'fa-trash', disabled: (record) => record.is_you || props.data.total < 1},
]

</script>

<template>
    <authenticated-layout :title="title" selected-menu="categorie" >
        <template #actions>
            <ButtonIcon @click="() => formModal.add()" type="primary" text="Nouveau Catégories" icon="fa-plus"/>
        </template>

        <data-table
            :columns="columns"
            :data="data"
            :actions="actions"
            class="main-shadow mt-5"
            :show-index="true">

        </data-table>

    </authenticated-layout>


    <CategorieForm ref="formModal"  />
</template>

<style scoped>

</style>
