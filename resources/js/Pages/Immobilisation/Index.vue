<script setup>
import SousMenuPrincipale from "@/Pages/Menu/SousMenuPrincipale.vue";
import DataTable from "@/Components/DataTable.vue";
import ButtonIcon from "@/Components/ButtonIcon.vue";
import {computed, ref} from "vue";
import {confirm_delete} from "../../../Utils/confirmation_modal.js";
import {router} from "@inertiajs/vue3";
import FilterBase from "@/Components/Filter/FilterBase.vue";
import {createSearchFilter, gotoSearch} from "../../../Utils/FiltreUtils.js";
import usePermissions from "@/UserPermissions/usePermissions.js";
import MagasinForm from "@/Pages/Magasin/Form/MagasinForm.vue";
import ImmobilisationForm from "@/Pages/Immobilisation/Form/ImmobilisationForm.vue";

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
const title = computed(() => `Liste Immobilisation (${props.data?.total ?? 0})`);
const filter = ref(createSearchFilter())

const columns = [
    {key: "libelle", title: "libellé", dataIndex: "libelle"},
    {key: "valeur", title: "valeur", dataIndex: "valeur",width: 200,customCell: () => ({ class: 'text-right' }),customHeaderCell:()=>({class:'!text-right'})},
];



const actions = [
    {
        text: "Modifier",
        action: (record) => formModal.value.update(record),
        icon: 'fa-pen-to-square',
        privilege: 'immobilisation.update'
    },
];
const applyFilters = (data) => {
    filter.value = data;
    const url = route('immobilisation.index');
    gotoSearch(filter.value, url)
};

const resetFilters = () => {
    filter.value.search = ""
    applyFilters();
};


</script>

<template>
    <SousMenuPrincipale :title="title" selectedMenu="immobilisation">
        <template #top>
            <FilterBase v-model="props.filters"
                        @search="applyFilters"
                        @reset="resetFilters"
        >

        </FilterBase>
        </template>

        <DataTable
            :columns="columns"
            :data="data"
            :actions="actions"
            class="main-shadow"
            :show-index="true"
            :btn_action="false"
        >


        </DataTable>
        <ImmobilisationForm ref="formModal"

        />
    </SousMenuPrincipale>
</template>

<style scoped>
/* Styles spécifiques si nécessaires */
</style>
