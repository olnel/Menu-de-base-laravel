<script setup>
import ButtonIcon from "@/Components/ButtonIcon.vue";
import DataTable from "@/Components/DataTable.vue";
import ExcelImportBaseStandard from "@/Components/ExcelImportBaseStandard.vue";
import ExportData from "@/Components/ExportData/ExportData.vue";
import FilterBase from "@/Components/Filter/FilterBase.vue";
import SousMenuPrincipale from "@/Pages/Menu/SousMenuPrincipale.vue";
import PneuInventaireForm from "@/Pages/PneuInventaire/PneuInventaireForm.vue";
import usePermissions from "@/UserPermissions/usePermissions.js";
import { DownOutlined } from "@ant-design/icons-vue";
import { computed, ref } from "vue";
import { createSearchFilter, gotoSearch } from "../../../Utils/FiltreUtils.js";

const title = computed(
    () => `Historique des inventaires (${props.data?.total ?? 0})`
);
const { can } = usePermissions();

const props = defineProps({
    data:      { type: Object, default: () => ({}) },
    filters:   { type: Object, default: () => ({}) },
    flash:     { type: Object, default: () => ({}) },
    articles:  { type: Object, default: () => ({}) },
    magasins:  { type: Object, default: () => ({}) },
    vehicules: { type: Array,  default: () => [] },
    remorques: { type: Array,  default: () => [] },
});
const dropdownVisible = ref(false);
const refFormModal = ref();
const filter = ref({
    ...createSearchFilter(),
    article: null,
    magasin: null,
    start_date: null,
    end_date: null,
});

const applyFilters = () => {
    const url = route("pneu_inventaire.index");
    gotoSearch(filter.value, url);
    closeDropdown();
};

const resetFilters = () => {
    filter.value.search = "";
    filter.value.article = null;
    filter.value.magasin = null;
    filter.value.start_date = null;
    filter.value.end_date = null;
    applyFilters();
};

const closeDropdown = () => {
    dropdownVisible.value = false;
};

const columns = [
    {
        key:             "date_inventaire",
        title:           "Date",
        dataIndex:       "date_inventaire",
        width:           110,
        customCell:      () => ({ class: "text-center" }),
        customHeaderCell: () => ({ class: "!text-center" }),
    },
    {
        key:       "nom_magasin",
        title:     "Magasin",
        dataIndex: "nom_magasin",
        width:     130,
    },
    {
        key:       "numero_serie",
        title:     "N° Série",
        dataIndex: "numero_serie",
        width:     140,
    },
    {
        key:       "etat",
        title:     "État",
        dataIndex: "etat",
        width:     90,
        customCell:      () => ({ class: "text-center" }),
        customHeaderCell: () => ({ class: "!text-center" }),
    },
    {
        key:       "reference",
        title:     "Article",
        dataIndex: "reference",
        width:     130,
    },
    {
        key:   "is_existe",
        title: "Existe",
        width: 80,
        customCell:      () => ({ class: "text-center" }),
        customHeaderCell: () => ({ class: "!text-center" }),
    },
    {
        key:   "occupation",
        title: "Occupation",
        width: 150,
        customCell:      () => ({ class: "text-center" }),
        customHeaderCell: () => ({ class: "!text-center" }),
    },
    {
        key:       "remarque",
        title:     "Remarque",
        dataIndex: "remarque",
        width:     150,
    },
    {
        key:       "nom_user",
        title:     "Utilisateur",
        dataIndex: "nom_user",
        width:     120,
    },
];
</script>
<template>
    <SousMenuPrincipale :title="title" selectedMenu="pneu_inventaire" v-if="can('pneu_inventaire.index')">
        <template #top>
            <FilterBase
            v-model="filter"
            @search="applyFilters"
            @reset="resetFilters"
        >
            <template #import v-if="can('export_pneu_inventaire.export')">
                <ExportData :show_import="false" :title="'Export data'" :columns="columns" :filter="filter" :url="route('pneu_inventaire.export')">
                    <template #import>
                        <excel-import-base-standard :columns="columns" model="test"/>
                    </template>
                </ExportData>
            </template>

            <template #add>
                <div class="flex gap-2 items-center justify-center">
                    <ButtonIcon
                        v-if="can('pneu_inventaire.store')"
                        @click="() => refFormModal.add()"
                        type="primary"
                        text="Ajouter un"
                        icon="fa-plus"
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
                            <a-date-picker
                                v-model:value="filter.start_date"
                                :format="'YYYY/MM/DD'"
                                size="large"
                                class="w-full text-center"
                                placeholder="Du"
                                :value-format="'DD-MM-YYYY'"
                            />
                            <a-date-picker
                                v-model:value="filter.end_date"
                                :format="'YYYY/MM/DD'"
                                size="large"
                                class="w-full text-center"
                                placeholder="Au"
                                :value-format="'DD-MM-YYYY'"
                            />
                            <div class="grid grid-cols-1 gap-3">
                                <div>
                                    <a-select
                                        v-model:value="filter.magasin"
                                        :options="props.magasins"
                                        size="large"
                                        allowClear
                                        show-search
                                        :filter-option="
                                            (input, option) =>
                                                option.label
                                                    .toLowerCase()
                                                    .includes(
                                                        input.toLowerCase()
                                                    )
                                        "
                                        placeholder="Sélectionner magasin"
                                        class="w-full"
                                    />
                                </div>
                                <div>
                                    <a-select
                                        v-model:value="filter.article"
                                        :options="props.articles"
                                        size="large"
                                        allowClear
                                        show-search
                                        :filter-option="
                                            (input, option) =>
                                                option.label
                                                    .toLowerCase()
                                                    .includes(
                                                        input.toLowerCase()
                                                    )
                                        "
                                        placeholder="Sélectionner article"
                                        class="w-full"
                                    />
                                </div>
                            </div>
                            <div class="flex space-x-2 !mt-6">
                                <a-button
                                    block
                                    type="primary"
                                    size="middle"
                                    @click="applyFilters()"
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
                        <a-space>
                            <font-awesome-icon
                                class="text-[15px]"
                                icon="fa-filter"
                            />
                            Filtres
                            <DownOutlined />
                        </a-space>
                    </a-button>
                </a-popover>
            </template>
        </FilterBase>
        </template>
        <DataTable
            :columns="columns"
            :data="props.data"
            class="main-shadow mt-4"
            :show-index="true"
            :btn_action="false"
        >
            <template #bodyCell="{ column, record }">
                <!-- État -->
                <template v-if="column.key === 'etat'">
                    <a-tag
                        :color="
                            record.etat === 'neuf'     ? 'green'  :
                            record.etat === 'bon'      ? 'blue'   :
                            record.etat === 'rechappe' ? 'orange' : 'default'
                        "
                        :bordered="false"
                    >
                        {{
                            record.etat === 'neuf'     ? 'Neuf'     :
                            record.etat === 'bon'      ? 'Bon'      :
                            record.etat === 'rechappe' ? 'Rechappé' : '—'
                        }}
                    </a-tag>
                </template>

                <!-- Existe -->
                <template v-else-if="column.key === 'is_existe'">
                    <a-tag :color="record.is_existe ? 'green' : 'red'" :bordered="false">
                        {{ record.is_existe ? 'Oui' : 'Non' }}
                    </a-tag>
                </template>

                <!-- Occupation -->
                <template v-else-if="column.key === 'occupation'">
                    <template v-if="record.occupe">
                        <a-tag color="blue" :bordered="false">
                            <template v-if="record.vehicule_label">
                                <font-awesome-icon icon="fa-truck" class="mr-1" />
                                {{ record.vehicule_label }}
                            </template>
                            <template v-else-if="record.remorque_label">
                                <font-awesome-icon icon="fa-trailer" class="mr-1" />
                                {{ record.remorque_label }}
                            </template>
                            <template v-else>Occupé</template>
                        </a-tag>
                    </template>
                    <a-tag v-else color="default" :bordered="false">Libre</a-tag>
                </template>
            </template>
        </DataTable>

        <PneuInventaireForm
            :magasins="magasins"
            :articles="articles"
            :vehicules="vehicules"
            :remorques="remorques"
            :flash="flash"
            ref="refFormModal"
        />
    </SousMenuPrincipale>
</template>
<style scoped></style>
