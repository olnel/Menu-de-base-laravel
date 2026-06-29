<template>
    <SousMenuPrincipale :title="title" selectedMenu="carburant-mouvements">
        <template #top>
            <FilterBase
            v-model="filter"
            @search="applyFilters"
            @reset="resetFilters"
        >
            <template #import v-if="can('export_carburant_mouvement.export')">
                <ExportData :show_import="false" :title="'Export data'" :columns="columns" :filter="filter" :url="route('carburant_card_mouvement.export')" >
                    <template #import >
                        <excel-import-base-standard :columns="columns" model="test"/>
                    </template>
                </ExportData>
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
                            <a-range-picker
                                v-model:value="dateRange"
                                format="DD/MM/YYYY"
                                class="w-full"
                                size="large"
                                :placeholder="['Date de début', 'Date de fin']"
                                @change="handleDateRangeChange"
                            />
                            <a-select
                                v-model:value="filter.type_mvmt"
                                placeholder="Type de mouvement"
                                class="w-full"
                                size="large"
                                allow-clear
                            >
                                <a-select-option value="recharge"
                                    >Recharge</a-select-option
                                >
                                <a-select-option value="ajustement"
                                    >Ajustement</a-select-option
                                >
                                <a-select-option value="achat_carte"
                                    >Achat par Carte</a-select-option
                                >
                                <a-select-option value="achat_espece"
                                    >Achat par Espèce</a-select-option
                                >
                                <a-select-option value="annulation_transaction"
                                    >Annulation Transaction</a-select-option
                                >
                            </a-select>

                            <a-select
                                v-model:value="filter.user_id"
                                placeholder="Utilisateur"
                                class="w-full"
                                size="large"
                                allow-clear
                                show-search
                                :filter-option="
                                    (input, option) =>
                                        option.label
                                            .toLowerCase()
                                            .includes(input.toLowerCase())
                                "
                                option-filter-prop="label"
                            >
                                <a-select-option
                                    v-for="user in users"
                                    :key="user.value"
                                    :value="user.value"
                                    :label="user.label"
                                >
                                    {{ user.label }}
                                </a-select-option>
                            </a-select>

                            <a-select
                                v-model:value="filter.carburant_card_id"
                                placeholder="Carte Carburant"
                                class="w-full"
                                size="large"
                                allow-clear
                                show-search
                                :filter-option="
                                    (input, option) =>
                                        option.label
                                            .toLowerCase()
                                            .includes(input.toLowerCase())
                                "
                                option-filter-prop="label"
                            >
                                <a-select-option
                                    v-for="card in carburantCards"
                                    :key="card.value"
                                    :value="card.value"
                                    :label="card.label"
                                >
                                    {{ card.label }}
                                </a-select-option>
                            </a-select>

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
            class="main-shadow"
            :show-index="true"
            :btn_action="false"
        >
        </BaseDataTable>
        <!-- <FormulaireCarburantTransaction
            ref="formModal"
            :carburant-cards="carburantCards"
            :chauffeurs="chauffeurs"
            :vehicules="vehicules"
            :users="users"
        /> -->
    </SousMenuPrincipale>
</template>

<script setup>
import BaseDataTable from "@/Components/DataTable.vue";
import FilterBase from "@/Components/Filter/FilterBase.vue";
import SousMenuPrincipale from "@/Pages/Menu/SousMenuPrincipale.vue";
import usePermissions from "@/UserPermissions/usePermissions.js";
import { DownOutlined } from "@ant-design/icons-vue";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { Space } from "ant-design-vue"; // Removed unused Ant Design components
import dayjs from "dayjs";
import { computed, ref } from "vue"; // Removed 'watch' as it's no longer needed
import { createSearchFilter, gotoSearch } from "../../../Utils/FiltreUtils.js";
import ExportData from "@/Components/ExportData/ExportData.vue";
import ExcelImportBaseStandard from "@/Components/ExcelImportBaseStandard.vue";

const { can } = usePermissions();
const title = computed(() => `Liste Des Mouvements Carburant (${props.data?.total ?? 0})`);

const props = defineProps({
    data: { type: Object,default: () =>({})},
    filters:{ type: Object,default: () =>({})},
    carburantCards: { type: Array,default: () => []},
    vehicules: { type: Array,default: () => []},
    chauffeurs: { type: Array,default: () => []},
    users: { type: Array,default: () => []},
});

const dropdownVisible = ref(false);
const filter = ref({...createSearchFilter(),
    type_mvmt: props.filters.type_mvmt || null,
    user_id: props.filters.user_id || null,
    start_date: props.filters.start_date || null,
    end_date: props.filters.end_date || null,
    carburant_card_id: props.filters.carburant_card_id || null,
});
const dateRange = ref(filter.value.start_date && filter.value.end_date? [dayjs(filter.value.start_date),dayjs(filter.value.end_date)] : []);
const handleDateRangeChange = (dates, dateStrings) => {filter.value.start_date = dateStrings[0];filter.value.end_date = dateStrings[1]};
const applyFilters = () => {
    const url = route("carburant_mouvements.index");
    gotoSearch(filter.value, url);
    closeDropdown();
};
const resetFilters = () => {
    filter.value = {...createSearchFilter(),start_date: null,end_date: null,type_mvmt: null,user_id: null,carburant_card_id: null};
    dateRange.value = [];
    applyFilters();
};
const closeDropdown = () => {dropdownVisible.value = false;};

const columns = [
    { key: "date_mvmt",title: "Date-mvmt",width: 100,
        customRender: ({ record }) => record.date_mvmt ? dayjs(record.date_mvmt).format("DD/MM/YYYY") : "-",
        customCell: () => ({ class: "!text-center" }),
        customHeaderCell: () => ({ class: "!text-center whitespace-nowrap" }),
    },
    { key: "mvmt_type",title: "mouvement",dataIndex: "type",width: 100,},
    { key: "numero_carte",title: "N° Carte",dataIndex: "carburant_card",width: 100,
        customRender: ({ record }) => (record.card ? record.card : "-"),
        customCell: () => ({ class: "!text-center" }),
        customHeaderCell: () => ({ class: "!text-center whitespace-nowrap" }),
    },
    { key: "chauffeur_assoc",title: "chauffeur",width: 150,
        customRender: ({ record }) => record.nom_chauffeur ? record.nom_chauffeur : "-",
    },
    { key: "vehicule_assoc",title: "Vehicules",width:100,
        customRender: ({ record }) =>record.matricule_vehicule ? record.matricule_vehicule : "-",
    },
    { key: "reference_mvmt",title: "reference",width: 100,
        customRender: ({ record }) => record.reference_mvmt ? record.reference_mvmt : "-",
        customCell: () => ({ class: "!text-left" }),
        customHeaderCell: () => ({ class: "!text-center whitespace-nowrap" }),
    },
    { key: "quantite_initiale",title: "montant.init",width : 100,
        customRender: ({ record }) => record.montant_initiale ? new Intl.NumberFormat().format(record.montant_initiale) : "-",
        customCell: () => ({ class: "!text-right" }),
        customHeaderCell: () => ({ class: "!text-right whitespace-nowrap" }),
    },
    { key: "quantite_mvmt",title: "montant.mvmt",width: 100,
        customRender: ({ record }) => record.montant_mvmt ? new Intl.NumberFormat().format(record.montant_mvmt) : "-",
        customCell: () => ({ class: "!text-right" }),
        customHeaderCell: () => ({ class: "!text-right whitespace-nowrap" }),
    },
    { key: "quantite_finale", title: "montant.fin",width: 100,
        customRender: ({ record }) => record.montant_finale ? new Intl.NumberFormat().format(record.montant_finale) : "-",
        customCell: () => ({ class: "!text-right" }),
        customHeaderCell: () => ({ class: "!text-right whitespace-nowrap" }),
    },
    { key: "commentaire",title:"Commentaire",width: 150,
        customRender: ({ record }) => record.commentaire ? record.commentaire : "-",
    },
    {key: "nom_user",
        title: "Utilisateur",
        customRender: ({ record }) => record.nom_user || "-",
        width: 100,
    },
    { key: "date_enregistrement",title: "Date.enreg",dataIndex: "date_heure_enregistrement",width: 100,
        customRender: ({ text }) => text ? dayjs(text).format("DD/MM/YYYY HH:MM") : "-",
        customCell: () => ({ class: "!text-center" }),
        customHeaderCell: () => ({ class: "!text-center whitespace-nowrap" }),
    },
];
</script>

<style scoped></style>
