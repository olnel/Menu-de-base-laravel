<template>
    <SousMenuPrincipale title="Dashboard Voyage" selectedMenu="dashboard_voyage" v-if="can('dashboard.voyage')">

        <!-- ── Filtre ──────────────────────────────────────────────────────── -->
        <FilterBase v-model="filter" @search="applyFilters" @reset="resetFilters">
            <template #otherFilter>
                <a-popover placement="bottomRight" trigger="click" v-model:open="dropdownVisible">
                    <template #content>
                        <div class="bg-white p-4 w-96 space-y-4 rounded-xl">
                            <div class="flex flex-col gap-2">
                                <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Période</label>
                                <div class="grid grid-cols-2 gap-2">
                                    <a-date-picker v-model:value="filter.start_date" :format="dateFormat" size="large" class="w-full !rounded-lg" placeholder="Du" value-format="DD-MM-YYYY" />
                                    <a-date-picker v-model:value="filter.end_date"   :format="dateFormat" size="large" class="w-full !rounded-lg" placeholder="Au" value-format="DD-MM-YYYY" />
                                </div>
                            </div>
                            <div class="flex gap-2 pt-2">
                                <a-button block type="primary" size="large" class="!rounded-lg" @click="applyFilters">Appliquer</a-button>
                                <a-button block size="large" class="!rounded-lg" @click="dropdownVisible = false">Fermer</a-button>
                            </div>
                        </div>
                    </template>
                    <a-button size="large" type="default" class="!rounded-none border-r-0 focus:z-10 group">
                        <Space>
                            <font-awesome-icon class="text-[15px] text-gray-400 group-hover:text-primary transition-colors" icon="fa-filter" />
                            <span class="text-gray-600">Filtres</span>
                            <DownOutlined class="text-xs text-gray-400" />
                        </Space>
                    </a-button>
                </a-popover>
            </template>
        </FilterBase>

        <div class="mt-8 space-y-8">

            <!-- ── KPI Cards ────────────────────────────────────────────────── -->
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5">

                <!-- Total Voyages -->
                <div class="group relative overflow-hidden rounded-2xl bg-white p-6 shadow-sm border border-gray-100/80 hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300 cursor-default">
                    <div class="absolute inset-x-0 top-0 h-0.5 rounded-t-2xl bg-gradient-to-r from-blue-400 to-blue-600"></div>
                    <div class="flex items-start justify-between gap-4">
                        <div class="min-w-0">
                            <p class="text-[11px] font-bold uppercase tracking-widest text-gray-400">Total Voyages</p>
                            <p class="mt-2 text-4xl font-black text-gray-900 tabular-nums">{{ recap_voyage?.totalVoyage ?? 0 }}</p>
                        </div>
                        <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-500 text-xl group-hover:bg-blue-500 group-hover:text-white transition-all duration-300">
                            <font-awesome-icon icon="fas fa-map-marked-alt" />
                        </div>
                    </div>
                    <div class="mt-4 h-px bg-gray-100"></div>
                    <div class="mt-3 flex items-center gap-3 text-xs text-gray-500">
                        <span class="inline-flex items-center gap-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-blue-400"></span>
                            Facturé&nbsp;<strong class="text-gray-700">{{ recap_voyage?.recapVoyageStatutReservation?.voyageFacturer ?? 0 }}</strong>
                        </span>
                        <span class="inline-flex items-center gap-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-gray-300"></span>
                            Non facturé&nbsp;<strong class="text-gray-700">{{ recap_voyage?.recapVoyageStatutReservation?.voyageNonFacturer ?? 0 }}</strong>
                        </span>
                    </div>
                </div>

                <!-- Taux Facturation -->
                <div class="group relative overflow-hidden rounded-2xl bg-white p-6 shadow-sm border border-gray-100/80 hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300 cursor-default">
                    <div class="absolute inset-x-0 top-0 h-0.5 rounded-t-2xl bg-gradient-to-r from-emerald-400 to-teal-500"></div>
                    <div class="flex items-start justify-between gap-4">
                        <div class="min-w-0">
                            <p class="text-[11px] font-bold uppercase tracking-widest text-gray-400">Taux Facturation</p>
                            <p class="mt-2 text-4xl font-black text-gray-900 tabular-nums">{{ tauxFacturation }}<span class="text-2xl font-bold text-gray-400">%</span></p>
                        </div>
                        <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-500 text-xl group-hover:bg-emerald-500 group-hover:text-white transition-all duration-300">
                            <font-awesome-icon icon="fas fa-file-invoice-dollar" />
                        </div>
                    </div>
                    <div class="mt-4 h-px bg-gray-100"></div>
                    <p class="mt-3 text-xs text-gray-400">Part des voyages déjà facturés</p>
                </div>

                <!-- Total Réservations -->
                <div class="group relative overflow-hidden rounded-2xl bg-white p-6 shadow-sm border border-gray-100/80 hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300 cursor-default">
                    <div class="absolute inset-x-0 top-0 h-0.5 rounded-t-2xl bg-gradient-to-r from-amber-400 to-orange-500"></div>
                    <div class="flex items-start justify-between gap-4">
                        <div class="min-w-0">
                            <p class="text-[11px] font-bold uppercase tracking-widest text-gray-400">Total Réservations</p>
                            <p class="mt-2 text-4xl font-black text-gray-900 tabular-nums">{{ recap_reservation?.totalReservation ?? 0 }}</p>
                        </div>
                        <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-xl bg-amber-50 text-amber-500 text-xl group-hover:bg-amber-500 group-hover:text-white transition-all duration-300">
                            <font-awesome-icon icon="fas fa-calendar-check" />
                        </div>
                    </div>
                    <div class="mt-4 h-px bg-gray-100"></div>
                    <div class="mt-3 flex items-center gap-3 text-xs text-gray-500">
                        <span class="inline-flex items-center gap-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                            Validée&nbsp;<strong class="text-gray-700">{{ recap_reservation?.StatutReservation?.est_valide ?? 0 }}</strong>
                        </span>
                        <span class="inline-flex items-center gap-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-gray-300"></span>
                            Non validée&nbsp;<strong class="text-gray-700">{{ recap_reservation?.StatutReservation?.non_valide ?? 0 }}</strong>
                        </span>
                    </div>
                </div>

                <!-- Destinations -->
                <div class="group relative overflow-hidden rounded-2xl bg-white p-6 shadow-sm border border-gray-100/80 hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300 cursor-default">
                    <div class="absolute inset-x-0 top-0 h-0.5 rounded-t-2xl bg-gradient-to-r from-violet-400 to-purple-600"></div>
                    <div class="flex items-start justify-between gap-4">
                        <div class="min-w-0">
                            <p class="text-[11px] font-bold uppercase tracking-widest text-gray-400">Destinations</p>
                            <p class="mt-2 text-4xl font-black text-gray-900 tabular-nums">{{ destinations?.length ?? 0 }}</p>
                        </div>
                        <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-xl bg-violet-50 text-violet-500 text-xl group-hover:bg-violet-500 group-hover:text-white transition-all duration-300">
                            <font-awesome-icon icon="fas fa-location-dot" />
                        </div>
                    </div>
                    <div class="mt-4 h-px bg-gray-100"></div>
                    <p class="mt-3 text-xs text-gray-400">Routes desservies sur la période</p>
                </div>
            </div>

            <!-- ── Graphiques : Destinations & Top clients ───────────────── -->
            <div v-if="isAtLeast('standard')" class="grid grid-cols-1 xl:grid-cols-12 gap-6 items-stretch">

                <!-- Voyages & réservations par destination -->
                <div class="xl:col-span-8 flex flex-col">
                    <div class="flex-1 rounded-2xl bg-white shadow-sm border border-gray-100/80 overflow-hidden flex flex-col">
                        <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100 bg-gray-50/40">
                            <div class="flex items-center gap-3">
                                <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-blue-50 ring-1 ring-blue-100">
                                    <font-awesome-icon icon="fas fa-chart-bar" class="text-blue-500 text-sm" />
                                </div>
                                <div>
                                    <h3 class="text-sm font-bold text-gray-900">Voyages & réservations par destination</h3>
                                    <p class="text-xs text-gray-400 mt-0.5">Comparaison des volumes par route sur la période</p>
                                </div>
                            </div>
                        </div>
                        <HorizontalBarChart
                            :items="destinations ?? []"
                            label-key="destination"
                            value-key="count_voyage"
                            value2-key="count_reservation"
                            primary-label="Voyages"
                            secondary-label="Réservations"
                            primary-color="#3B82F6"
                            secondary-color="#8B5CF6"
                            :show-legend="true"
                            mode="dual"
                            orientation="x"
                            :show-pagination="true"
                            :page-size="6"
                            height="360px"
                            class="!shadow-none !rounded-none !border-0"
                        />
                    </div>
                </div>

                <!-- Top clients -->
                <div class="xl:col-span-4 flex flex-col">
                    <div class="flex-1 rounded-2xl bg-white shadow-sm border border-gray-100/80 overflow-hidden flex flex-col">
                        <div class="flex items-center gap-3 px-6 py-5 border-b border-gray-100 bg-gray-50/40">
                            <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-50 ring-1 ring-emerald-100">
                                <font-awesome-icon icon="fas fa-trophy" class="text-emerald-500 text-sm" />
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-gray-900">Top clients</h3>
                                <p class="text-xs text-gray-400 mt-0.5">Classement par nombre de voyages actifs</p>
                            </div>
                        </div>
                        <div class="flex-1">
                            <RankedList
                                :items="clients ?? []"
                                label-key="nom_client"
                                value-key="count_voyage"
                                primary-label="Voyages"
                                :show-pagination="true"
                                :page-size="6"
                                primary-color="#10B981"
                                gradient-from="#10B981"
                                gradient-to="#059669"
                                class="!shadow-none !rounded-none !border-0"
                            />
                        </div>
                    </div>
                </div>
            </div>

            <!-- ── Répartition annuelle ──────────────────────────────────── -->
            <div
                v-if="isAtLeast('advanced') && recap_voyage?.recapByAnnee?.length"
                class="grid grid-cols-1 md:grid-cols-2 gap-6"
            >
                <div
                    v-for="(item, index) in recap_voyage.recapByAnnee"
                    :key="index"
                    class="rounded-2xl bg-white shadow-sm border border-gray-100/80 overflow-hidden"
                >
                    <div class="flex items-center gap-3 px-6 py-5 border-b border-gray-100 bg-gray-50/40">
                        <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-violet-50 ring-1 ring-violet-100">
                            <font-awesome-icon icon="fas fa-chart-pie" class="text-violet-500 text-sm" />
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-gray-900">Répartition des voyages</h3>
                            <p class="text-xs text-gray-400 mt-0.5">Année {{ item.annee }}</p>
                        </div>
                    </div>
                    <div class="p-6 flex justify-center">
                        <PieChart :element="item" :size="400" />
                    </div>
                </div>
            </div>

        </div>
    </SousMenuPrincipale>
</template>

<script setup>
import HorizontalBarChart from "@/Components/Charts/HorizontalBarChart.vue";
import PieChart from "@/Components/Charts/PieChart.vue";
import FilterBase from "@/Components/Filter/FilterBase.vue";
import SousMenuPrincipale from "@/Pages/Menu/SousMenuPrincipale.vue";
import RankedList from "@/Pages/Dashboard/Partials/RankedList.vue";
import usePermissions from "@/UserPermissions/usePermissions.js";
import { DownOutlined } from "@ant-design/icons-vue";
import { Space } from "ant-design-vue";
import dayjs from "dayjs";
import { computed, ref } from "vue";
import { createSearchFilter, gotoSearch } from "../../../../Utils/FiltreUtils.js";
import { usePlan } from "@/Composables/usePlan.js";

const { can } = usePermissions();
const dropdownVisible = ref(false);
const dateFormat = "DD/MM/YYYY";

const props = defineProps({
    data:              { type: Object, default: () => ({}) },
    filters:           { type: Object, default: () => ({}) },
    flash:             { type: Object, default: () => ({}) },
    recap_voyage:      { type: [Object, Array], default: () => ({}) },
    recap_reservation: { type: [Object, Array], default: () => ({}) },
    destinations:      { type: Array, default: () => [] },
    clients:           { type: Array, default: () => [] },
});

const getMonthStartDate = () => dayjs().startOf("month").format("DD-MM-YYYY");
const getMonthEndDate   = () => dayjs().endOf("month").format("DD-MM-YYYY");

const filter = ref({
    ...createSearchFilter(),
    search:     props.filters?.search     ?? "",
    start_date: props.filters?.start_date ?? getMonthStartDate(),
    end_date:   props.filters?.end_date   ?? getMonthEndDate(),
});

const { isAtLeast } = usePlan()

const tauxFacturation = computed(() => {
    const total    = props.recap_voyage?.totalVoyage ?? 0;
    const factures = props.recap_voyage?.recapVoyageStatutReservation?.voyageFacturer ?? 0;
    if (total === 0) return 0;
    return Math.round((factures / total) * 100);
});

const applyFilters = () => {
    dropdownVisible.value = false;
    gotoSearch(filter.value, route("dashboard.voyage"), ["recap_voyage", "recap_reservation", "destinations", "clients"]);
};

const resetFilters = () => {
    filter.value.search     = "";
    filter.value.start_date = getMonthStartDate();
    filter.value.end_date   = getMonthEndDate();
    applyFilters();
};
</script>
