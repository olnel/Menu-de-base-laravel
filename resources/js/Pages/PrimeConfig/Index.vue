<script setup>
import ButtonIcon from "@/Components/ButtonIcon.vue";
import DataTable from "@/Components/DataTable.vue";
import FilterBase from "@/Components/Filter/FilterBase.vue";
import SousMenuPrincipale from "@/Pages/Menu/SousMenuPrincipale.vue";
import PrimeConfigForm from "@/Pages/PrimeConfig/Form/PrimeConfigForm.vue";
import usePermissions from "@/UserPermissions/usePermissions.js";
import { router } from "@inertiajs/vue3";
import { useCurrency } from "@/Composables/useCurrency.js";
import { computed, ref } from "vue";
import { confirm_delete } from "../../../Utils/confirmation_modal.js";
import { createSearchFilter, gotoSearch } from "../../../Utils/FiltreUtils.js";

const { can } = usePermissions();

const props = defineProps({
    data: { type: Object, default: () => ({}) },
    types_salarie: { type: Array, default: () => [] },
    filters: { type: Object, default: () => ({}) },
    flash: { type: Object, default: () => ({}) },
});

const formModal = ref();
const title = computed(() => `Configuration des Primes (${props.data?.total ?? 0})`);
const filter = ref(createSearchFilter());

const { formatCurrency: fmtCcy } = useCurrency({ minimumFractionDigits: 2 });

const columns = [
    {
        key: "libelle",
        title: "Libellé",
        dataIndex: "libelle",
        width: "300px"
    },
    {
        key: "montant",
        title: "Montant",
        dataIndex: "montant",
        align: "right",
        customRender: ({ text }) => fmtCcy(text),
        width: "150px"
    },
    {
        key: "type_salarie",
        title: "Type Salarié",
        dataIndex: ["type_salarie", "libelle"],
        customRender: ({ record }) => record.is_global ? "Global" : (record.type_salarie?.libelle ?? "-"),
        width: "200px"
    },
    {
        key: "is_per_voyage",
        title: "Calcul",
        dataIndex: "is_per_voyage",
        align: "center",
        width: "150px"
    },
];

const handleDelete = (record) => {
    confirm_delete(() => {
        router.delete(route("prime_config.destroy", record.id), {
            preserveScroll: true,
        });
    });
};

const actions = [
    {
        text: "Modifier",
        action: (record) => formModal.value.update(record),
        icon: "fa-pen-to-square",
        privilege: "prime_config.update",
        disabled: (record) => !!record.is_per_voyage,
    },
    {
        text: "Supprimer",
        action: handleDelete,
        icon: "fa-trash",
        class: "!text-red-600",
        disabled: (record) => props.data.total < 1 || !!record.is_per_voyage,
        privilege: "prime_config.destroy",
    },
];

const applyFilters = (data) => {
    filter.value = data;
    const url = route("prime_config.index");
    gotoSearch(filter.value, url);
};

const resetFilters = () => {
    filter.value.search = "";
    applyFilters();
};
</script>

<template>
    <SousMenuPrincipale :title="title" selectedMenu="prime_config" v-if="can('prime_config.index')">
        <template #top>
            <FilterBase
                v-model="props.filters"
                @search="applyFilters"
                @reset="resetFilters"
            >
                <template #add>
                    <ButtonIcon
                        v-if="can('prime_config.store')"
                        @click="() => formModal.add()"
                        type="primary"
                        text="Nouvelle Prime"
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
            <template #bodyCell="{ column, record, text }">
                <template v-if="column.key === 'is_per_voyage'">
                    <a-tag :color="text ? 'blue' : 'gray'">
                        {{ text ? 'Par Voyage' : 'Fixe' }}
                    </a-tag>
                </template>
            </template>
        </DataTable>
        <PrimeConfigForm ref="formModal" :types_salarie="types_salarie" />
    </SousMenuPrincipale>
</template>

<style scoped></style>
