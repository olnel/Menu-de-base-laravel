<script setup>

import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import DataTable from "@/Components/DataTable.vue";
import {computed, ref} from "vue";

import {confirm_delete} from "../../../../Utils/confirmation_modal.js";
import {router} from "@inertiajs/vue3";
import FeatureForm from "@/Pages/Accueil/Feature/FeatureForm.vue";
import {reloadPage} from "../../../../Utils/functions.js";
import FilterButton from "@/Components/FilterButton.vue";
import ButtonIcon from "@/Components/ButtonIcon.vue";
import Toolbar from "@/Components/Toolbar.vue";
import FilterButtonItem from "@/Components/FilterButtonItem.vue";
import Recherche from "@/Components/Recherche.vue";



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
const title = computed(() => `Features (${ props.data?.total ?? 0 })`)

const columns = [
    {key: "Titre", title: "titre", dataIndex: "titre"},
    {key: "Description", title: "description", dataIndex: "description"},
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
    // {text: "Supprimer", action: handleDelete, icon: 'fa-trash', disabled: (record) => record.is_you || props.data.total < 1},
]

</script>

<template>
    <authenticated-layout :title="title" selected-menu="feature" >
<!--        <Toolbar>
            <ButtonIcon @click="reloadPage(filters)" text="Actualiser" icon="fa-refresh"/>
            <Recherche v-model:value="filters.search" @on-search="reloadPage(filters)" />
        </Toolbar>-->

        <data-table
            :columns="columns"
            :data="data"
            :actions="actions"
            class="main-shadow mt-5"
            :show-index="true"
            :btn_action="false"
        >

        </data-table>

    </authenticated-layout>


    <FeatureForm ref="formModal">

    </FeatureForm>
</template>

<style scoped>

</style>
