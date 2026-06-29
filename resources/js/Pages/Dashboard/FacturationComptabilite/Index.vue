<script setup>
import SousMenuPrincipale from "@/Pages/Menu/SousMenuPrincipale.vue";
import {computed, ref} from "vue";
import FilterBase from "@/Components/Filter/FilterBase.vue";
import usePermissions from "@/UserPermissions/usePermissions.js";
import {Space} from "ant-design-vue";
import {DownOutlined} from "@ant-design/icons-vue";
import {createSearchFilter, gotoSearch} from "../../../../Utils/FiltreUtils.js";
import HorizontalBarChart from "@/Components/Charts/HorizontalBarChart.vue";
import BarChart from "@/Components/Charts/BarChart.vue";
import PieChart from "@/Components/Charts/PieChart.vue";

import dayjs from "dayjs";

const {can} = usePermissions()
const dropdownVisible = ref(false);

const dateFormat = 'DD/MM/YYYY';
const getFirstDayOfMonth = () => {
    return dayjs().startOf('month').format('DD-MM-YYYY');
};

const getLastDayOfMonth = () => {
    return dayjs().endOf('month').format('DD-MM-YYYY');
};

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
    },

    clients: {
        type: Array,
        default: () => []
    },
    totalGeneral: {
        type: Array,
        default: () => []
    },
    count_par_statut_facture: {
        type: Array,
        default: () => []
    },
    count_facture: {
        type: Number,
        default: 0
    },
    totaux: {
        type: Array,
        default: () => []
    },
    comptes_a_recevoir: {
        type: Object,
        default: () => ({})
    },
    comptes_a_payer: {
        type: Object,
        default: () => ({})
    },
    etat_financier: {
        type: Object,
        default: () => ({
            recevables: [],
            payables: []
        })
    }
});

const activeTab = ref('recevables');

const receivablesColumns = [
    { title: 'Client', dataIndex: 'nom', key: 'nom' },
    { title: 'Total Dû', dataIndex: 'total_du', key: 'total_du', align: 'right' },
    { title: 'Total Payé', dataIndex: 'total_paye', key: 'total_paye', align: 'right' },
    { title: 'Solde', dataIndex: 'solde', key: 'solde', align: 'right' },
];

const payablesColumns = [
    { title: 'Fournisseur', dataIndex: 'nom', key: 'nom' },
    { title: 'Total Dû', dataIndex: 'total_du', key: 'total_du', align: 'right' },
    { title: 'Total Payé', dataIndex: 'total_paye', key: 'total_paye', align: 'right' },
    { title: 'Solde', dataIndex: 'solde', key: 'solde', align: 'right' },
];

const formModal = ref();
const title = computed(() => `Dashaboard Facturation & comptabilité (${props.data?.total ?? 0})`);
const filter = ref({...createSearchFilter(), start_date: getFirstDayOfMonth(), end_date: getLastDayOfMonth()});

const applyFilters = () => {
    const url = route('dashboard.comptablite');
    gotoSearch(filter.value, url, ['totalGeneral', 'count_par_statut_facture', 'count_facture', 'totaux']);
};

const resetFilters = () => {
    filter.value.search = "";
    filter.value.start_date = getFirstDayOfMonth();
    filter.value.end_date = getLastDayOfMonth();
    filter.value.nom_client = null;
    applyFilters();
};

const formatNumber = (val) => {
    return new Intl.NumberFormat('fr-FR').format(val);
};

function getIcon(status) {
    switch (status) {
        case 'Brouillon':
            return 'fa-file-alt';        // exemple : document
        case 'envoyée':
            return 'fa-paper-plane';     // exemple : envoyé
        case 'partiellement payée':
            return 'fa-hourglass-half';  // exemple : en attente
        case 'payée':
            return 'fa-check-circle';    // exemple : payé
        default:
            return 'fa-question-circle';
    }
}


</script>

<template>
    <SousMenuPrincipale :title="title" selectedMenu="dashboard_facturation" v-if="can('dashboard.comptablite')">
        <FilterBase v-model="filter"
                    @search="applyFilters"
                    @reset="resetFilters"
        >
            <template #otherFilter>
                <a-popover
                    placement="bottomRight"
                    trigger="click"
                    :visible="dropdownVisible"
                    @visibleChange="val => dropdownVisible = val"
                >
                    <template #content>
                        <div class="bg-white p-4 w-96 space-y-2 rounded-md">
                            <a-date-picker
                                v-model:value="filter.start_date"
                                :format="dateFormat"
                                size="large"
                                class="w-full"
                                placeholder="Du"
                                :value-format="'DD-MM-YYYY'"
                            />
                            <a-date-picker
                                v-model:value="filter.end_date"
                                :format="dateFormat"
                                size="large"
                                class="w-full"
                                placeholder="Au"
                                :value-format="'DD-MM-YYYY'"
                            />
                            <div class="flex space-x-2 !mt-6">
                                <a-button block type="primary" size="middle" @click="applyFilters">Appliquer</a-button>
                                <a-button block type="default" size="middle" @click="dropdownVisible = false">Fermer
                                </a-button>
                            </div>
                        </div>
                    </template>
                    <a-button size="large" type="default" class="!rounded-none border-r-0 focus:z-10">
                        <Space>
                            <font-awesome-icon class="text-[15px]" icon="fa-filter"/>
                            Filtres
                            <DownOutlined/>
                        </Space>
                    </a-button>
                </a-popover>
            </template>
        </FilterBase>
        <div class="bg-white p-5">


<!--            <h1 class="text-2xl font-bold text-gray-700">Vue d'ensemble</h1>-->
<!--            <p class="text-gray-600">Résumé des totaux de trésorerie et des factures clients</p>-->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-5 ">
                <div class="p-6 rounded-xl shadow-md bg-gradient-to-br from-blue-50 to-blue-100 flex flex-col items-center">
                    <font-awesome-icon icon="fa-coins" class="text-4xl text-blue-600 mb-2" />
                    <p class="text-gray-600 text-lg font-semibold">Total Trésorerie</p>
                    <span class="text-2xl font-bold text-gray-800 mt-1">
                        {{ formatNumber(totalGeneral.total_general) }} Ar
                    </span>
                </div>


                <div class="p-6 rounded-xl shadow-md bg-gradient-to-br from-green-50 to-green-100 flex flex-col items-center">
                    <p class="text-gray-600 text-lg font-semibold">Factures Clients</p>
                    <span class="text-2xl font-bold text-gray-800 mt-1">
                      {{ count_facture }}
                    </span>
                </div>
                <!-- Total TTC -->
                <div class="p-6 rounded-xl shadow-md bg-gradient-to-br from-purple-50 to-purple-100 flex flex-col items-center">
                    <font-awesome-icon icon="fa-cash-register" class="text-4xl text-purple-600 mb-2" />
                    <p class="text-gray-600 text-lg font-semibold">Total TTC</p>
                    <span class="text-2xl font-bold text-gray-800 mt-1">
                        {{ formatNumber(totaux.total_ttc) }} Ar
                    </span>
                </div>

                <!-- Reste à Payer -->
                <div class="p-6 rounded-xl shadow-md bg-gradient-to-br from-red-50 to-red-100 flex flex-col items-center">
                    <font-awesome-icon icon="fa-exclamation-circle" class="text-4xl text-red-600 mb-2" />
                    <p class="text-gray-600 text-lg font-semibold">Reste à Payer</p>
                    <span class="text-2xl font-bold text-gray-800 mt-1">
                        {{ formatNumber(totaux.montant_reste_paye) }} Ar
                    </span>
                </div>

            </div>


            <div class="grid lg:grid-cols-2 grid-cols-1 gap-6">
                <!-- Bloc Totaux -->
                <div class="col-span-1">
                    <!-- Titre avec total général -->
                    <h2 class="text-lg font-bold bg-gradient-to-r from-blue-50 to-blue-100 p-4 flex justify-between items-center rounded-t-xl shadow-sm">
                        <span class="uppercase tracking-wide text-gray-700">Récapitulatif du Trésorerie</span>
                    </h2>
                    <!-- Liste des trésoreries -->
                    <div class="  rounded-b-xl  pt-4  ">
                        <div class="grid lg:grid-cols-2 md:grid-cols-3 xl:grid-cols-3 grid-cols-1  gap-4 ">
                            <div class="p-4 border border-gray-200 rounded-lg shadow-sm hover:shadow-md transition bg-gray-50 " v-for="(item, index) in totalGeneral.totaux_par_tresorerie"
                                 :key="index"
                            >
                                <!-- Nom trésorerie -->
                                <span class="text-sm text-gray-700 mb-2 font-semibold">
                                    {{ item.nom_tresorerie }}
                                </span>

                                <!-- Solde -->
                                <div class="flex justify-between text-sm text-gray-600">
                                    <span>Solde</span>
                                    <span class="font-semibold text-green-600 text-right">
                                        {{ formatNumber(item.tresorerie.solde) }}Ar
                                    </span>
                                </div>

                                <!-- Entrées -->
                                <div class="flex justify-between text-sm text-gray-600 mt-1">
                                    <span>Entrées</span>
                                    <span class="font-semibold text-blue-600 text-right">
                                        {{ formatNumber(item.total_entrees) }}Ar
                                    </span>
                                </div>

                                <!-- Sorties -->
                                <div class="flex justify-between text-sm text-gray-600 mt-1">
                                    <span>Sorties</span>
                                    <span class="font-semibold text-red-600 text-right">
                                        {{ formatNumber(item.total_sorties) }}Ar
                                    </span>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>


                <div class="col-span-1">
                    <!-- Header -->
                    <h2 class="text-lg font-bold bg-gradient-to-r from-green-50 to-green-100 p-4 flex justify-between items-center rounded-t-xl shadow-sm">
                        <span class="uppercase tracking-wide text-gray-700">Détails Facture Client</span>
                    </h2>

                    <!-- Conteneur des cartes -->
                    <div class="flex flex-wrap gap-4 mt-4">
                        <div v-for="(item, index) in count_par_statut_facture" :key="index" class="flex-1 min-w-[140px]">
                            <a-card class="text-center hover:shadow-lg transition h-full flex flex-col justify-center items-center rounded-xl bg-white shadow-sm"
                                    :body-style="{ padding: '10px'}">
                                <!-- Icône selon le statut -->
                                <div class="mb-2 text-3xl" :class="{
                                                              'text-yellow-500': index === 'Brouillon',
                                                              'text-blue-500': index === 'envoyée',
                                                              'text-orange-500': index === 'partiellement payée',
                                                              'text-green-500': index === 'payée'
                                                            }">
                                    <font-awesome-icon :icon="getIcon(index)" />
                                </div>

                                <p class="text-md text-gray-600 uppercase font-medium">{{ index }}</p>

                                <span class="text-2xl font-bold text-gray-800 mt-1">{{ item }}</span>
                            </a-card>
                        </div>
                    </div>
<!--                    <div class="grid grid-cols-2 mt-4 gap-4">
                        <div v-for="(item, index) in totaux" :key="'total-' + index"
                             class="p-4 rounded-xl shadow hover:shadow-md transition flex flex-col items-center justify-center bg-gradient-to-b from-blue-50 to-white border">

                            &lt;!&ndash; Icônes adaptées aux totaux &ndash;&gt;
                            <div class="mb-2 text-4xl"
                                 :class="{
                                           'text-blue-600': index === 'total_ttc',
                                           'text-orange-500': index === 'total_partiellement_paye',
                                           'text-green-600': index === 'total_paye',
                                           'text-red-500': index === 'montant_reste_paye'
                                         }">

                            </div>

                            &lt;!&ndash; Label formaté &ndash;&gt;
                            <p class="text-lg  text-gray-600 uppercase tracking-wide font-semibold">
                                {{ index.replace(/_/g, " ") }}
                            </p>

                            &lt;!&ndash; Valeur &ndash;&gt;
                            <span class="text-2xl font-bold text-gray-800 mt-1">
                                {{ formatNumber(item) }} Ar
                            </span>
                        </div>
                    </div>-->

                </div>
            </div>

            <!-- État Financier : Comptes à Recevoir / Payer -->
            <div class="mt-8 bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                <div class="bg-gradient-to-r from-gray-700 to-gray-800 p-4 flex justify-between items-center">
                    <h2 class="text-lg font-bold text-white uppercase tracking-wide">
                        <font-awesome-icon icon="fa-file-invoice" class="mr-2" />
                        État Financier : Comptes à Recevoir & Payer
                    </h2>
                    <div class="flex space-x-2">
                        <a-radio-group v-model:value="activeTab" button-style="solid">
                            <a-radio-button value="recevables">Comptes à Recevoir (Clients)</a-radio-button>
                            <a-radio-button value="payables">Comptes à Payer (Fournisseurs)</a-radio-button>
                        </a-radio-group>
                    </div>
                </div>

                <div class="p-6">
                    <div v-if="activeTab === 'recevables'">
                        <a-table 
                            :columns="receivablesColumns" 
                            :data-source="etat_financier.recevables" 
                            :pagination="false"
                            size="middle"
                            class="ant-table-striped"
                        >
                            <template #bodyCell="{ column, record, text }">
                                <template v-if="column.key === 'total_du'">
                                    <span class="font-semibold">{{ formatNumber(text) }} Ar</span>
                                </template>
                                <template v-if="column.key === 'total_paye'">
                                    <span class="text-green-600 font-semibold">{{ formatNumber(text) }} Ar</span>
                                </template>
                                <template v-if="column.key === 'solde'">
                                    <span class="text-red-600 font-bold">{{ formatNumber(text) }} Ar</span>
                                </template>
                            </template>
                        </a-table>
                        <div class="mt-4 p-4 bg-red-50 rounded-lg flex justify-between items-center border border-red-100">
                            <span class="text-gray-700 font-bold uppercase">Total Général à Recevoir :</span>
                            <span class="text-2xl font-black text-red-600">{{ formatNumber(comptes_a_recevoir.total) }} Ar</span>
                        </div>
                    </div>

                    <div v-if="activeTab === 'payables'">
                        <a-table 
                            :columns="payablesColumns" 
                            :data-source="etat_financier.payables" 
                            :pagination="false"
                            size="middle"
                            class="ant-table-striped"
                        >
                            <template #bodyCell="{ column, record, text }">
                                <template v-if="column.key === 'total_du'">
                                    <span class="font-semibold">{{ formatNumber(text) }} Ar</span>
                                </template>
                                <template v-if="column.key === 'total_paye'">
                                    <span class="text-blue-600 font-semibold">{{ formatNumber(text) }} Ar</span>
                                </template>
                                <template v-if="column.key === 'solde'">
                                    <span class="text-orange-600 font-bold">{{ formatNumber(text) }} Ar</span>
                                </template>
                            </template>
                        </a-table>
                        <div class="mt-4 p-4 bg-orange-50 rounded-lg flex justify-between items-center border border-orange-100">
                            <span class="text-gray-700 font-bold uppercase">Total Général à Payer :</span>
                            <span class="text-2xl font-black text-orange-600">{{ formatNumber(comptes_a_payer.total) }} Ar</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </SousMenuPrincipale>
</template>

<style scoped>
/* Styles spécifiques si nécessaires */
</style>
