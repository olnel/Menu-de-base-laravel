<script setup>
import ButtonIcon from "@/Components/ButtonIcon.vue";
import DataTable from "@/Components/DataTable.vue";
import FilterBase from "@/Components/Filter/FilterBase.vue";
import SousMenuPrincipale from "@/Pages/Menu/SousMenuPrincipale.vue";
import TypeSalarieForm from "@/Pages/TypeSalarie/Form/TypeSalarieForm.vue";
import usePermissions from "@/UserPermissions/usePermissions.js";
import { router } from "@inertiajs/vue3";
import { computed, ref } from "vue";
import { confirm_delete } from "../../../Utils/confirmation_modal.js";
import { createSearchFilter, gotoSearch } from "../../../Utils/FiltreUtils.js";

const { can } = usePermissions();

const props = defineProps({
    data: { type: Object, default: () => ({}) },
    filters: { type: Object, default: () => ({}) },
    flash: { type: Object, default: () => ({}) },
});

const formModal = ref();
const title = computed(() => `Types de salarié (${props.data?.total ?? 0})`);
const filter = ref(createSearchFilter());

const columns = [
    {
        key: "libelle",
        title: "Libellé",
        dataIndex: "libelle",
    },
    {
        key: "description",
        title: "Description",
        dataIndex: "description",
    },
];

const handleDelete = (record) => {
    confirm_delete(() => {
        router.delete(route("type_salarie.destroy", record.id), {
            preserveScroll: true,
        });
    });
};

const actions = [
    {
        text: "Modifier",
        action: (record) => formModal.value.update(record),
        icon: "fa-pen-to-square",
        privilege: "type_salarie.update",
        disabled: (record) => ['chauffeur', 'aide chauffeur'].includes(record.libelle.toLowerCase()),
    },
    {
        text: "Supprimer",
        action: handleDelete,
        icon: "fa-trash",
        class: "!text-red-600",
        disabled: (record) => props.data.total < 1 || ['chauffeur', 'aide chauffeur'].includes(record.libelle.toLowerCase()),
        privilege: "type_salarie.destroy",
    },
];

const applyFilters = (data) => {
    filter.value = data;
    const url = route("type_salarie.index");
    gotoSearch(filter.value, url);
};

const resetFilters = () => {
    filter.value.search = "";
    applyFilters();
};
</script>

<template>
    <SousMenuPrincipale :title="title" selectedMenu="type_salarie">
        <template #top>
            <FilterBase
                v-model="props.filters"
                @search="applyFilters"
                @reset="resetFilters"
            >
                <template #add>
                    <ButtonIcon
                        v-if="can('type_salarie.store')"
                        @click="() => formModal.add()"
                        type="primary"
                        text="Nouveau Type"
                        icon="fa-plus"
                    />
                </template>
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
        <TypeSalarieForm ref="formModal" />
    </SousMenuPrincipale>
</template>

<style scoped></style>
