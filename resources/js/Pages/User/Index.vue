<script setup>

import SousMenuPrincipale from "@/Pages/Menu/SousMenuPrincipale.vue";
import DataTable from "@/Components/DataTable.vue";
import Toolbar from "@/Components/Toolbar.vue";
import ButtonIcon from "@/Components/ButtonIcon.vue";
import Recherche from "@/Components/Recherche.vue";
import {computed, ref} from "vue";
import {reloadPage} from "../../../Utils/functions.js";
import {confirm_delete} from "../../../Utils/confirmation_modal.js";
import {router} from "@inertiajs/vue3";
import UserForm from "@/Pages/User/Partials/UserForm.vue";
import {createSearchFilter, gotoSearch} from "../../../Utils/FiltreUtils.js";
import { h } from "vue";
import FilterBase from "@/Components/Filter/FilterBase.vue";
import usePermissions from "@/UserPermissions/usePermissions.js";
import ExcelImportBaseStandard from "@/Components/ExcelImportBaseStandard.vue";
import ExportData from "@/Components/ExportData/ExportData.vue";

const { can } = usePermissions()
const props = defineProps({
    data: {
        type: Object,
        default: () => {}
    },
    user_groupes: {
        type: Object,
        default: () => {}
    },
    filters: {
        type: Object,
        default: () => {}
    },

})
const filter = ref(createSearchFilter())
const formModal = ref()
const title = computed(() => `Utilisateurs (${ props.data?.total ?? 0 })`)

const columns = [
{
        key: "photo",
        title: "photo",
        customRender: ({ record }) =>
            h("img", {
                src: record.thumb_img,
                alt: "Chauffeur Photo",
                class: "w-10 h-10 object-cover ",
            }),
    },
    {key: "name", title: "Nom", dataIndex: "name"},
    {key: "email", title: "Email", dataIndex: "email"},
    // {key: "tel", title: "Téléphone", dataIndex: "tel"},
    {key: "privileges_name", title: "Role", dataIndex: "privileges_name"}
    // {key: "user_group_name", title: "Role", dataIndex: "user_group_name"}
    // {key: "is_you", title: "", dataIndex: "is_you", width: 50},


]

const handleDelete = (record) => {
    confirm_delete(() => {
        router.delete(route('user.destroy', record.id), {
            preserveScroll: true,
        })
    });
}

const actions = [

    {text: "Modifier", action: (record) => formModal.value.update(record), icon: 'fa-pen-to-square', privilege:"user.update"},
    {text: "Supprimer",       class:"!text-red-600", action: handleDelete, icon: 'fa-trash', disabled: (record) => record.is_you || props.data.total < 1, privilege:"user.destroy"},

]

const applyFilters = (data) => {
    filter.value = data;
    const url = route('user.index');
    gotoSearch(filter.value, url)
};

const resetFilters = (data) => {
    props.filters.search = ""
    filter.value.search = ""
    applyFilters();
};

</script>

<template>
    <SousMenuPrincipale :title="title" selectedMenu="user">
        <template #top>
            <FilterBase v-model="props.filters"
                        @search="applyFilters"
                        @reset="resetFilters"
        >
            <template #add v-if="can('user.store')">
                <ButtonIcon @click="() => formModal.add()" type="primary" text="Creé un utilisateur" icon="fa-plus" v-if="can('user.store')"/>
            </template>
            <template #import v-if="can('export_user.export')">
                <ExportData :show_import="false" :title="'Export data'" :columns="columns" :filter="filter" :url="route('user.export')" >
                    <template #import >
                        <excel-import-base-standard model="test"
                            :columns="columns"/>
                    </template>
                </ExportData>
            </template>
        </FilterBase>
        </template>
            <data-table :columns="columns" :data="data" :actions="actions" class="main-shadow" :btn_action="false" >
<!--                <template #default="{ column, record, text }">
                    <template v-if="column.key === 'is_you'">
                        <a-tag v-if="text" color="success" :bordered="false">
                            VOUS
                        </a-tag>
                    </template>
                </template>-->
                <!-- SearchBar in the new slot -->

            </data-table>

    </SousMenuPrincipale>

    <UserForm ref="formModal" :roles="props.user_groupes" />
</template>

<style scoped>

</style>
