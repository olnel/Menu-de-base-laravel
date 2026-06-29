<template>
    <SousMenuPrincipale :title="title" selectedMenu="carburant-cards">
        <template #top>
            <FilterBase v-model="filter" @search="applyFilters" @reset="resetFilters">
            <template #import v-if="can('export_carburant_card.export')">
                <ExportData :show_import="false" :title="'Export data'" :columns="columns" :filter="filter" :url="route('carburant_card.export')" >
                    <template #import >
                        <excel-import-base-standard :columns="columns" model="test"/>
                    </template>
                </ExportData>
            </template>
            <template #add>
                <div class="flex gap-2 items-center justify-center">
                    <ButtonIcon
                        v-if="can('carburant_cards.store')"
                        @click="() => formModal.add()"
                        type="primary"
                        text="Nouvelle Carte Carburant"
                        icon="fa-plus"
                        :show-index="true"
                        class="!rounded-md hover:!text-white hover:!bg-sky-800 transition-all duration-300"
                    />
                    <ButtonIcon
                        v-if="
                            selectedRowKeys.length > 0 &&
                            can('carburant_cards.ajustement')
                        "
                        @click="handleAjustement"
                        type="primary"
                        text="Ajustement"
                        icon="fa-check"
                        :show-index="true"
                    />
                </div>
            </template>
            <template #otherFilter>
                <a-popover
                    placement="bottomRight"
                    trigger="click"
                    :visible="dropdownVisible"
                    @visibleChange="(val) => (dropdownVisible = val)"
                >
                    <template #content>
                        <div class="bg-white p-4 w-96 space-y-2 rounded-md">
                            <a-select
                                v-model:value="filter.active"
                                placeholder="Statut"
                                class="w-full"
                                size="large"
                                allow-clear
                            >
                                <a-select-option :value="true"
                                    >Carte Active</a-select-option
                                >
                                <a-select-option :value="false"
                                    >Carte Desactivé</a-select-option
                                >
                            </a-select>

                            <div class="flex space-x-2 !mt-6">
                                <a-button
                                    block
                                    type="primary"
                                    size="middle"
                                    @click="applyFilters(filter)"
                                    >Appliquer</a-button
                                >
                                <a-button
                                    block
                                    type="default"
                                    size="middle"
                                    @click="closeDropdown"
                                    >Fermer</a-button
                                >
                            </div>
                        </div>
                    </template>

                    <a-button
                        size="large"
                        type="default"
                        class="!rounded-none border-r-0 focus:z-10"
                    >
                        <Space>
                            <font-awesome-icon
                                class="text-[15px]"
                                icon="fa-filter"
                            />
                            Filtres
                            <DownOutlined />
                        </Space>
                    </a-button>
                </a-popover>
            </template>
        </FilterBase>
        </template>

        <BaseDataTable
            :columns="columns"
            :data="data"
            :actions="actions"
            :show-index="true"
            class="main-shadow custom-checkbox-table"
            :customRow="
                (record) => ({
                    class: {
                        'inactive-card-row': !record.active,
                        'low-balance-row': record.active && record.soldeFaible,
                    },
                })
            "
            :rowSelection="rowSelection"
            :rowKey="'id'"
        >
        </BaseDataTable>
        <CarburantCardForm ref="formModal" />
        <FormulaireAjustementCard
            ref="adjustmentModal"
            @adjustmentSuccess="selectedRowKeys = []"
        />
    </SousMenuPrincipale>
</template>

<script setup>
import ButtonIcon from "@/Components/ButtonIcon.vue";
import BaseDataTable from "@/Components/DataTable.vue";
import FilterBase from "@/Components/Filter/FilterBase.vue";
import SousMenuPrincipale from "@/Pages/Menu/SousMenuPrincipale.vue";
import usePermissions from "@/UserPermissions/usePermissions.js";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { router } from "@inertiajs/vue3";
import { Tooltip } from "ant-design-vue";
import { computed, h, ref } from "vue";
import {
    confirm_delete,
    confirm_save,
} from "../../../Utils/confirmation_modal.js";
import { createSearchFilter, gotoSearch } from "../../../Utils/FiltreUtils.js";
import FormulaireAjustementCard from "./Formulaire/FormulaireAjustementCard.vue";
import CarburantCardForm from "./Formulaire/FormulaireCarburantCard.vue";
import ExportData from "@/Components/ExportData/ExportData.vue";
import ExcelImportBaseStandard from "@/Components/ExcelImportBaseStandard.vue";

const { can } = usePermissions();
const formModal = ref();
const adjustmentModal = ref();
const title = computed(() => `Liste Des Cartes Carburant (${props.data?.total ?? 0})`);

const props = defineProps({
    data: { type: Object, default: () => ({})},
    filters: { type: Object, default: ()=>({})}
});

const dropdownVisible = ref(false);
const filter = ref({...createSearchFilter(),...props.filters,active: props.filters.active !== undefined ? props.filters.active : null,});
const applyFilters = (data) => {
    filter.value = data;
    const url = route("carburant_cards.index");
    gotoSearch(filter.value, url);
};
const resetFilters = () => {
    filter.value = { ...createSearchFilter(), active: null};
    applyFilters(filter.value);
};
const closeDropdown = () => {
    dropdownVisible.value = false;
};

const handleAjustement = () => {
    if (selectedRowKeys.value.length > 0) { adjustmentModal.value.add(selectedRowKeys.value)}// Ouvre le modal et passe les IDs
};
const selectedRowKeys = ref([]);
const onSelectChange = (keys) => {selectedRowKeys.value = keys;};
const rowSelection = computed(() => ({
    selectedRowKeys: selectedRowKeys.value,
    onChange: onSelectChange,
    getCheckboxProps: (record) => ({
        disabled: !record.active || !can('carburant_cards.RechargeCard'),
        name: record.name,
    }),
}));

const handleDelete = (record) => {
    confirm_delete(() => {router.delete(route("carburant_cards.destroy", record.id), {preserveScroll: true})});
};
const columns = [
    { key: "numero_carte",title: "N° carte",dataIndex: "numero_carte",width: 100,
        customCell: () => ({ class: "!text-center" }),
        customHeaderCell: () => ({ class: "!text-center whitespace-nowrap" }),
    },
    { key: "plafond_mensuel",title:"Plafond",dataIndex: "plafond_mensuel",width: 100,
        customCell: () => ({ class: "text-right" }),
        customHeaderCell: () => ({ class: "!text-right whitespace-nowrap" }),
        customRender: ({ text }) => new Intl.NumberFormat().format(text),
    },
    { key: "solde",title: "Solde",dataIndex: "solde",width: 100,
        customCell: () => ({ class: "text-right" }),
        customHeaderCell: () => ({ class: "!text-right whitespace-nowrap" }),
        customRender: ({ text, record }) => {
            const formattedSolde = new Intl.NumberFormat().format(text);
            return h("div", { class: "inline-flex items-center gap-2" }, [
                record.active && record.soldeFaible
                    ? h(Tooltip, { title: "Solde faible" }, () =>
                        h(FontAwesomeIcon, {
                            icon: ["fas", "triangle-exclamation"],
                            class: "text-red-600 text-xl",
                        })
                    )
                    : null,
                formattedSolde,
            ]);
        },
    },
    { key: "status",title: "status",
        customRender: ({ record }) => (record.active ? "activé" : "desactivé"),
        width: 25,
        customCell: () => ({ class: "text-right" }),
        customHeaderCell: () => ({ class: "!text-right whitespace-nowrap" }),
    },
];
const actions = [
    { text: "Modifier",action: (record) => formModal.value.update(record),icon: "fa-pen-to-square",privilege: "carburant_cards.update"},
    { text: "Désactiver/Activer",icon: "fa-power-off",privilege: "updateCardStatus",
        action: (record) => { confirm_save(() => {router.get(route("updateCardStatus", record.id),{},{preserveScroll: true,preserveState: true})},
                record.active ? "Désactivation Carte Carburant" : "Activation Carte Carburant",
                record.active ? "Voulez vous vraiment désactiver la carte de carburant ?" : "Voulez vous vraiment activer la carte de carburant ?",
                record.active ? "DESACTIVER" : "ACTIVER"
                );
        },
    },
    { text: "Supprimer",action: handleDelete,icon: "fa-trash",class:"!text-red-600",disabled: (record) => props.data.total < 1,privilege:"carburant_cards.destroy"}
];
</script>

<style>
.ant-table-wrapper .ant-table-tbody > tr.ant-table-row-selected > td {
    background-color: #d1e4ffe7 !important;
}
.inactive-card-row {
    @apply bg-gray-300 text-gray-800;
}
.low-balance-row {
    @apply bg-red-100;
}
.custom-checkbox-table .ant-checkbox-inner {
    width: 14px;
    height: 14px;
}
</style>
