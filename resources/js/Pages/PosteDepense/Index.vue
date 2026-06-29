<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import DataTable from "@/Components/DataTable.vue";
import ButtonIcon from "@/Components/ButtonIcon.vue";
import {computed, ref} from "vue";
import {confirm_delete} from "../../../Utils/confirmation_modal.js";
import {router} from "@inertiajs/vue3";
import FilterBase from "@/Components/Filter/FilterBase.vue";
import {createSearchFilter, gotoSearch} from "../../../Utils/FiltreUtils.js";
import usePermissions from "@/UserPermissions/usePermissions.js";
import MagasinForm from "@/Pages/Magasin/Form/MagasinForm.vue";
import PosteDepenseFormulaire from "@/Pages/PosteDepense/Form/PosteDepenseFormulaire.vue";

const {can} = usePermissions()

const props = defineProps({
    data: {
        type: Object,
        default: () => ({})
    },
    filters: {
        type: Object,
        default: () => ({})
    },
    flash: {
        type: Object,
        default: () => ({})
    }
});

const formModal = ref();
const title = computed(() => `Liste Poste de dépense (${props.data?.total ?? 0})`);
const filter = ref(createSearchFilter())

const columns = [
    {key: "libelle", title: "libellé", dataIndex: "libelle"},
];

const handleDelete = (record) => {
    confirm_delete(() => {
        router.delete(route('postedepense.destroy', record.id), {
            preserveScroll: true,
        });
    });
};

const actions = [
    {
        text: "Modifier",
        action: (record) => formModal.value.update(record),
        icon: 'fa-pen-to-square',
        privilege: 'postedepense.update',
    },
    {
        text: "Supprimer",
        action: handleDelete,
        icon: 'fa-trash',
        class:"!text-red-600",
        disabled: (record) => record.is_you || props.data.total < 1,
        privilege: 'postedepense.destroy',
    },
];
const applyFilters = (data) => {
    filter.value = data;
    const url = route('postedepense.index');
    gotoSearch(filter.value, url)
};

const resetFilters = () => {
    filter.value.search = ""
    applyFilters();
};


</script>

<template>
    <AuthenticatedLayout :title="title" selected-menu="postedepense" v-if="can('postedepense.index')">

        <FilterBase v-model="props.filters"
                    @search="applyFilters"
                    @reset="resetFilters"
        >
            <template #add>
                <ButtonIcon v-if="can('postedepense.store')" @click="() => formModal.add()" type="primary"
                            text="Nouveau Poste de dépense" icon="fa-plus"/>
            </template>
        </FilterBase>

        <DataTable
            :columns="columns"
            :data="data"
            :actions="actions"
            class="main-shadow"
            :show-index="true"
            :btn_action="false"
        >


        </DataTable>
        <PosteDepenseFormulaire ref="formModal"

        />
    </AuthenticatedLayout>
</template>

<style scoped>
/* Styles spécifiques si nécessaires */
</style>
