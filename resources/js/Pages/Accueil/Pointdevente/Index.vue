<script setup>

import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import DataTable from "@/Components/DataTable.vue";
import {computed, ref} from "vue";

import PointdeventeForm from "@/Pages/Accueil/Pointdevente/PointdeventeForm.vue";
import Toolbar from "@/Components/Toolbar.vue";
import ButtonIcon from "@/Components/ButtonIcon.vue";
import Recherche from "@/Components/Recherche.vue";


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
const title = computed(() => `Point de Vente (${props.data?.total ?? 0})`)

const columns = [
    {key: "Titre", title: "title", dataIndex: "title"},
    {key: "Description", title: "description", dataIndex: "description"},
]


const actions = [
    {text: "Modifier", action: (record) => formModal.value.update(record), icon: 'fa-pen-to-square'},
]

</script>

<template>
    <authenticated-layout :title="title" selected-menu="pointdevente">
<!--        <Toolbar>
            <ButtonIcon @click="reloadPage(filters)" text="Actualiser" icon="fa-refresh"/>
            <Recherche v-model:value="filters.search" @on-search="reloadPage(filters)"/>
        </Toolbar>-->
        <data-table
            :columns="columns"
            :data="data"
            :actions="actions"
            class="main-shadow mt-5"
            :show-index="true">

        </data-table>

    </authenticated-layout>


    <PointdeventeForm ref="formModal">

    </PointdeventeForm>
</template>

<style scoped>

</style>
