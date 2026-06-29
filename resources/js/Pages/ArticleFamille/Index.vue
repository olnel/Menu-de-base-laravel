<script setup>
import ButtonIcon from "@/Components/ButtonIcon.vue";
import DataTable from "@/Components/DataTable.vue";
import FilterBase from "@/Components/Filter/FilterBase.vue";
import SousMenuPrincipale from "@/Pages/Menu/SousMenuPrincipale.vue";
import ArticleFamilleForm from "@/Pages/ArticleFamille/Form/ArticleFamilleForm.vue";
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
const title = computed(() => `Familles d'article (${props.data?.total ?? 0})`);
const filter = ref(createSearchFilter());

const columns = [
    {
        key: "nom_famille_article",
        title: "Libellé",
        dataIndex: "nom_famille_article",
    },
];

const handleDelete = (record) => {
    confirm_delete(() => {
        router.delete(route("article_famille.destroy", record.id), {
            preserveScroll: true,
        });
    });
};

const actions = [
    {
        text: "Modifier",
        action: (record) => formModal.value.update(record),
        icon: "fa-pen-to-square",
        privilege: "article_famille.update",
    },
    {
        text: "Supprimer",
        action: handleDelete,
        icon: "fa-trash",
        class: "!text-red-600",
        disabled: (record) => props.data.total < 1,
        privilege: "article_famille.destroy",
    },
];

const applyFilters = (data) => {
    filter.value = data;
    const url = route("article_famille.index");
    gotoSearch(filter.value, url);
};

const resetFilters = () => {
    filter.value.search = "";
    applyFilters();
};
</script>

<template>
    <SousMenuPrincipale :title="title" selectedMenu="article_famille">
        <template #top>
            <FilterBase
                v-model="props.filters"
                @search="applyFilters"
                @reset="resetFilters"
            >
                <template #add>
                    <ButtonIcon
                        v-if="can('article_famille.store')"
                        @click="() => formModal.add()"
                        type="primary"
                        text="Nouvelle Famille"
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
        <ArticleFamilleForm ref="formModal" />
    </SousMenuPrincipale>
</template>

<style scoped></style>
