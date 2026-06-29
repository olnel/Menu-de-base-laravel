<script>
const TYPE_ARTICLE = [
    { value: "CONSOMMMABLE", label: "Consommmable" },
    { value: "PIÈCE MAJEURE", label: "Pièce majeure" },
    { value: "PNEU", label: "Pneu" },
];
</script>

<script setup>
import ButtonIcon from "@/Components/ButtonIcon.vue";
import DataTable from "@/Components/DataTable.vue";
import ExcelImportBaseStandard from "@/Components/ExcelImportBaseStandard.vue";
import ExportData from "@/Components/ExportData/ExportData.vue";
import FilterBase from "@/Components/Filter/FilterBase.vue";
import PneuSeriesDrawer from "@/Components/PneuSeriesDrawer.vue";
import SousMenuPrincipale from "@/Pages/Menu/SousMenuPrincipale.vue";
import ArticleFormulaire from "@/Pages/Article/Formulaire/ArticleFormulaire.vue";
import usePermissions from "@/UserPermissions/usePermissions.js";
import { DownOutlined } from "@ant-design/icons-vue";
import { router } from "@inertiajs/vue3";
import { Space } from "ant-design-vue";
import { computed, ref } from "vue";
import { confirm_delete } from "../../../Utils/confirmation_modal.js";
import { createSearchFilter, gotoSearch } from "../../../Utils/FiltreUtils.js";

const { can } = usePermissions();
const props = defineProps({
    data: {
        type: Object,
        default: () => ({}),
    },
    famille_articles: {
        type: Array,
        default: () => [],
    },
    famille_articles_modified: {
        type: Array,
        default: () => [],
    },
    magasins: {
        type: Array,
        default: () => [],
    },
    filters: {
        type: Object,
        default: () => ({}),
    },
    flash: {
        type: Object,
        default: () => ({}),
    },
});

const formModal        = ref();
const pneuSeriesDrawer = ref();
const title = computed(() => `Liste des Articles (${props.data?.total ?? 0})`);
const filter = ref({
    ...createSearchFilter(),
    type_article: null,
    magasin_id: null,
    article_famille_id: null,
});
const dropdownVisible = ref(false);

const columns = [
    {
        key: "nom_famille_article",
        title: "Famille Article",
        dataIndex: "nom_famille_article",
    },
    { key: "marque", title: "Marque", dataIndex: "marque" },
    { key: "reference", title: "Référence", dataIndex: "reference" },
    { key: "designation", title: "Désignation", dataIndex: "designation" },
    {
        key: "type_article",
        title: "Type d'article",
        dataIndex: "type_article",
        width: 150,
        customCell: () => ({ class: "text-center" }),
        customHeaderCell: () => ({ class: "!text-center" }),
    },
    {
        key: "pneu_series_btn",
        title: "Séries N°",
        width: 120,
        customCell: () => ({ class: "text-center" }),
        customHeaderCell: () => ({ class: "!text-center" }),
    },
    {
        key: "valeur",
        title: "Valeur",
        dataIndex: "valeur",
        width: 130,
        customCell: () => ({ class: "text-right" }),
        customHeaderCell: () => ({ class: "!text-right" }),
    },
    {
        key: "stock",
        title: "stock",
        dataIndex: "stock",
        width: 130,
        customCell: () => ({ class: "text-right" }),
        customHeaderCell: () => ({ class: "!text-right" }),
    },
    {
        key: "total_stock",
        title: "stock Total",
        dataIndex: "total_stock",
        width: 130,
        customCell: () => ({ class: "text-right" }),
        customHeaderCell: () => ({ class: "!text-right" }),
    },
    {
        key: "seuil_stock",
        title: "Seuil Stock",
        dataIndex: "seuil_stock",
        width: 130,
        customCell: () => ({ class: "text-right" }),
        customHeaderCell: () => ({ class: "!text-right" }),
    },
];

const isBelowSeuil = (record) =>
    record.seuil_stock !== null &&
    record.seuil_stock !== undefined &&
    Number(record.stock) <= Number(record.seuil_stock);

const rowClassName = (record) => (isBelowSeuil(record) ? 'row-danger' : '');

const handleDelete = (record) => {
    confirm_delete(() => {
        router.delete(route("article.destroy", record.id), {
            preserveScroll: true,
        });
    });
};

const actions = [
    {
        text: "Modifier",
        action: (record) => formModal.value.update(record),
        icon: "fa-pen-to-square",
        privilege: "article.update",
    },
    {
        text: "Supprimer",
        action: handleDelete,
        icon: "fa-trash",
        class: "!text-red-600",
        disabled: (record) => record.is_you || props.data.total < 1,
        privilege: "article.destroy",
    },
];
const applyFilters = (data) => {
    console.log(data);
    filter.value = data;
    closeDropdown();
    const url = route("article.index");
    gotoSearch(filter.value, url);
};

const resetFilters = () => {
    filter.value.search = null;
    filter.value.type_article = null;
    filter.value.article_famille_id = null;
    filter.value.magasin_id = null;
    const url = route("article.index");
    gotoSearch(filter.value, url);
};

const closeDropdown = () => {
    dropdownVisible.value = false;
};
</script>

<template>
    <SousMenuPrincipale :title="title" selectedMenu="article">
        <template #top>
            <FilterBase
                v-model="filter"
                @search="applyFilters"
                @reset="resetFilters"
            >
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
                                    v-model:value="filter.type_article"
                                    placeholder="Type d'article"
                                    class="w-full"
                                    size="large"
                                    allow-clear
                                    :options="TYPE_ARTICLE"
                                >
                                </a-select>

                                <a-select
                                    v-model:value="filter.article_famille_id"
                                    placeholder="Famille d'article"
                                    class="w-full"
                                    size="large"
                                    allow-clear
                                >
                                    <a-select-option
                                        v-for="type in props.famille_articles"
                                        :key="type.value"
                                        :value="type.value"
                                    >
                                        {{ type.label }}
                                    </a-select-option>
                                </a-select>

                                <a-select
                                    v-model:value="filter.magasin_id"
                                    placeholder="Liste Magasin"
                                    class="w-full"
                                    size="large"
                                    allow-clear
                                >
                                    <a-select-option
                                        v-for="type in props.magasins"
                                        :key="type.value"
                                        :value="type.value"
                                    >
                                        {{ type.label }}
                                    </a-select-option>
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

                <template #add>
                    <ButtonIcon
                        v-if="can('article.store')"
                        @click="() => formModal.add()"
                        type="primary"
                        text="Nouveau Article"
                        icon="fa-plus"
                    />
                </template>

                <template #import v-if="can('export_article.export')">
                    <ExportData
                        :show_import="false"
                        :title="'Export data'"
                        :columns="columns"
                        :filter="filter"
                        :url="route('article.export')"
                    >
                        <template #import>
                            <excel-import-base-standard
                                :columns="columns"
                                model="test"
                            ></excel-import-base-standard>
                        </template>
                    </ExportData>
                </template>
            </FilterBase>
        </template>

        <DataTable
            :columns="columns"
            :data="data"
            :actions="actions"
            class="main-shadow"
            :show-index="true"
            :btn_action="true"
            :row-class-name="rowClassName"
        >
            <template #bodyCell="{ column, record }">

                <!-- Type article — badge coloré -->
                <template v-if="column.key === 'type_article'">
                    <a-tag
                        :color="record.type_article === 'PNEU' ? 'blue' : record.type_article === 'PIÈCE MAJEURE' ? 'purple' : 'default'"
                        :bordered="false"
                        class="!text-xs !font-medium"
                    >
                        <font-awesome-icon
                            :icon="record.type_article === 'PNEU' ? 'fa-circle' : record.type_article === 'PIÈCE MAJEURE' ? 'fa-gear' : 'fa-box'"
                            class="mr-1 text-[10px]"
                        />
                        {{ record.type_article }}
                    </a-tag>
                </template>

                <!-- Bouton direct "Voir séries" pour les pneus -->
                <template v-else-if="column.key === 'pneu_series_btn'">
                    <a-tooltip v-if="record.type_article === 'PNEU'" title="Voir les N° de série">
                        <a-button
                            size="small"
                            type="primary"
                            ghost
                            class="!text-xs !px-2 !h-7"
                            @click="pneuSeriesDrawer.openDrawer(record)"
                        >
                            <font-awesome-icon icon="fa-list" class="mr-1 text-[10px]" />
                            N° Séries
                            <a-badge
                                v-if="record.total_stock > 0"
                                :count="record.total_stock"
                                :overflow-count="999"
                                color="blue"
                                class="!ml-1.5"
                                :number-style="{ fontSize: '10px', height: '16px', lineHeight: '16px', padding: '0 4px', minWidth: '16px' }"
                            />
                        </a-button>
                    </a-tooltip>
                    <span v-else class="text-gray-200 text-xs">—</span>
                </template>

                <template v-else-if="column.key === 'valeur'">
                    {{ new Intl.NumberFormat().format(record.valeur) }}
                </template>
                <template v-else-if="column.key === 'stock'">
                    <template v-if="record.type_article === 'PNEU'">
                        <div class="flex flex-col items-end gap-0.5">
                            <span
                                v-if="isBelowSeuil(record)"
                                class="inline-flex items-center gap-1.5 font-semibold text-red-600"
                            >
                                <font-awesome-icon icon="fa-solid fa-triangle-exclamation" class="text-xs" />
                                {{ record.stock ?? 0 }}
                            </span>
                            <span v-else>{{ record.stock ?? 0 }}</span>
                            <span
                                v-if="record.pneu_en_utilisation > 0"
                                class="inline-flex items-center gap-1 text-xs text-blue-500"
                            >
                                <font-awesome-icon icon="fa-solid fa-truck" class="text-[10px]" />
                                {{ record.pneu_en_utilisation }} utilisé(s)
                            </span>
                        </div>
                    </template>
                    <template v-else>
                        <span
                            v-if="isBelowSeuil(record)"
                            class="inline-flex items-center gap-1.5 font-semibold text-red-600"
                        >
                            <font-awesome-icon icon="fa-solid fa-triangle-exclamation" class="text-xs" />
                            {{ record.stock ?? 0 }}
                        </span>
                        <span v-else>{{ record.stock ?? 0 }}</span>
                    </template>
                </template>
                <template v-else-if="column.key === 'seuil_stock'">
                    <span v-if="record.seuil_stock !== null && record.seuil_stock !== undefined" class="text-gray-500">
                        {{ record.seuil_stock }}
                    </span>
                    <span v-else class="text-gray-300">—</span>
                </template>
            </template>
        </DataTable>
        <ArticleFormulaire
            ref="formModal"
            :LIST_FAMILLE_ARTICLE="famille_articles_modified"
        />
        <PneuSeriesDrawer ref="pneuSeriesDrawer" />
    </SousMenuPrincipale>
</template>

<style scoped>
/* Styles spécifiques si nécessaires */
</style>
