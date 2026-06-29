<template>
    <SousMenuPrincipale title="Dashboard Véhicule" selectedMenu="dashboard_vehicule" v-if="can('dashboard.vehicule')">
        <!-- Filter Bar -->
        <FilterBase v-model="filter" @search="applyFilters" @reset="resetFilters">
            <template #otherFilter>
                <a-popover
                    placement="bottomRight"
                    trigger="click"
                    :visible="dropdownVisible"
                    @visibleChange="(val) => (dropdownVisible = val)"
                >
                    <template #content>
                        <div class="bg-white p-4 w-96 space-y-4 rounded-xl">
                            <div class="flex flex-col gap-2">
                                <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Période</label>
                                <div class="grid grid-cols-2 gap-2">
                                    <a-date-picker
                                        v-model:value="filter.start_date"
                                        :format="dateFormat"
                                        size="large"
                                        class="w-full !rounded-lg"
                                        placeholder="Du"
                                        :value-format="'DD-MM-YYYY'"
                                    />
                                    <a-date-picker
                                        v-model:value="filter.end_date"
                                        :format="dateFormat"
                                        size="large"
                                        class="w-full !rounded-lg"
                                        placeholder="Au"
                                        :value-format="'DD-MM-YYYY'"
                                    />
                                </div>
                            </div>
                            <div class="flex gap-2 pt-2">
                                <a-button block type="primary" size="large" class="!rounded-lg" @click="applyFilters">Appliquer</a-button>
                                <a-button block type="default" size="large" class="!rounded-lg" @click="dropdownVisible = false">Fermer</a-button>
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

            <!-- KPI Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5">

                <div class="group relative overflow-hidden rounded-2xl bg-white p-6 shadow-sm border border-gray-100/80 hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300 cursor-default">
                    <div class="absolute inset-x-0 top-0 h-0.5 rounded-t-2xl bg-gradient-to-r from-blue-400 to-blue-600"></div>
                    <div class="flex items-start justify-between gap-4">
                        <div class="min-w-0">
                            <p class="text-[11px] font-bold uppercase tracking-widest text-gray-400">Total Véhicules</p>
                            <p class="mt-2 text-4xl font-black text-gray-900 tabular-nums">{{ data.totalVehicule || 0 }}</p>
                        </div>
                        <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-500 text-xl group-hover:bg-blue-500 group-hover:text-white transition-all duration-300">
                            <font-awesome-icon icon="fas fa-truck" />
                        </div>
                    </div>
                    <div class="mt-4 h-px bg-gray-100"></div>
                    <p class="mt-3 text-xs text-gray-400">Ensemble de la flotte de camions</p>
                </div>

                <div class="group relative overflow-hidden rounded-2xl bg-white p-6 shadow-sm border border-gray-100/80 hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300 cursor-default">
                    <div class="absolute inset-x-0 top-0 h-0.5 rounded-t-2xl bg-gradient-to-r from-amber-400 to-orange-500"></div>
                    <div class="flex items-start justify-between gap-4">
                        <div class="min-w-0">
                            <p class="text-[11px] font-bold uppercase tracking-widest text-gray-400">Total Remorques</p>
                            <p class="mt-2 text-4xl font-black text-gray-900 tabular-nums">{{ data.totalRemorque || 0 }}</p>
                        </div>
                        <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-xl bg-amber-50 text-amber-500 text-xl group-hover:bg-amber-500 group-hover:text-white transition-all duration-300">
                            <font-awesome-icon icon="fas fa-trailer" />
                        </div>
                    </div>
                    <div class="mt-4 h-px bg-gray-100"></div>
                    <p class="mt-3 text-xs text-gray-400">Remorques disponibles en flotte</p>
                </div>

                <div class="group relative overflow-hidden rounded-2xl bg-white p-6 shadow-sm border border-gray-100/80 hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300 cursor-default">
                    <div class="absolute inset-x-0 top-0 h-0.5 rounded-t-2xl bg-gradient-to-r from-emerald-400 to-teal-500"></div>
                    <div class="flex items-start justify-between gap-4">
                        <div class="min-w-0">
                            <p class="text-[11px] font-bold uppercase tracking-widest text-gray-400">Total Voyages</p>
                            <p class="mt-2 text-4xl font-black text-gray-900 tabular-nums">{{ totalVoyages }}</p>
                        </div>
                        <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-500 text-xl group-hover:bg-emerald-500 group-hover:text-white transition-all duration-300">
                            <font-awesome-icon icon="fas fa-route" />
                        </div>
                    </div>
                    <div class="mt-4 h-px bg-gray-100"></div>
                    <p class="mt-3 text-xs text-gray-400">Voyages effectués sur la période</p>
                </div>

                <div class="group relative overflow-hidden rounded-2xl bg-white p-6 shadow-sm border border-gray-100/80 hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300 cursor-default">
                    <div class="absolute inset-x-0 top-0 h-0.5 rounded-t-2xl bg-gradient-to-r from-violet-400 to-purple-600"></div>
                    <div class="flex items-start justify-between gap-4">
                        <div class="min-w-0">
                            <p class="text-[11px] font-bold uppercase tracking-widest text-gray-400">Kilométrage Total</p>
                            <p class="mt-2 text-4xl font-black text-gray-900 tabular-nums leading-none">
                                {{ totalKmFormatted }}
                                <span class="text-lg font-semibold text-gray-400 ml-1">km</span>
                            </p>
                        </div>
                        <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-xl bg-violet-50 text-violet-500 text-xl group-hover:bg-violet-500 group-hover:text-white transition-all duration-300">
                            <font-awesome-icon icon="fas fa-road" />
                        </div>
                    </div>
                    <div class="mt-4 h-px bg-gray-100"></div>
                    <p class="mt-3 text-xs text-gray-400">Distance totale parcourue</p>
                </div>
            </div>

            <!-- Charts Section -->
            <div v-if="isAtLeast('standard')" class="grid grid-cols-1 xl:grid-cols-12 gap-6 items-stretch">

                <!-- Bar Chart -->
                <div class="xl:col-span-8 flex flex-col">
                    <div class="flex-1 rounded-2xl bg-white shadow-sm border border-gray-100/80 overflow-hidden flex flex-col">
                        <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100 bg-gray-50/40">
                            <div class="flex items-center gap-3">
                                <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-blue-50 ring-1 ring-blue-100">
                                    <font-awesome-icon icon="fas fa-chart-bar" class="text-blue-500 text-sm" />
                                </div>
                                <div>
                                    <h3 class="text-sm font-bold text-gray-900">Activité par véhicule</h3>
                                    <p class="text-xs text-gray-400 mt-0.5">Voyages et kilométrage sur la période</p>
                                </div>
                            </div>
                            <div class="hidden sm:flex items-center gap-4 text-xs text-gray-500">
                                <span class="flex items-center gap-1.5">
                                    <span class="inline-block h-2.5 w-2.5 rounded-sm bg-blue-500"></span>
                                    Voyages
                                </span>
                                <span class="flex items-center gap-1.5">
                                    <span class="inline-block h-2.5 w-2.5 rounded-sm bg-violet-500"></span>
                                    Kilométrage
                                </span>
                            </div>
                        </div>
                        <HorizontalBarChart
                            :items="data.vehiculeStats ?? []"
                            label-key="label"
                            value-key="nbr_voyage"
                            value2-key="total_kilometrage"
                            primary-label="Nombre de voyages"
                            secondary-label="Kilométrage total"
                            primary-color="#3B82F6"
                            secondary-color="#8B5CF6"
                            height="420px"
                            :show-legend="false"
                            mode="dual"
                            orientation="x"
                            title=""
                            :show-pagination="true"
                            :page-size="6"
                            class="!shadow-none !rounded-none !border-0"
                        />
                    </div>
                </div>

                <!-- Ranked List -->
                <div class="xl:col-span-4 flex flex-col">
                    <div class="flex-1 rounded-2xl bg-white shadow-sm border border-gray-100/80 overflow-hidden flex flex-col">
                        <div class="flex items-center gap-3 px-6 py-5 border-b border-gray-100 bg-gray-50/40">
                            <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-amber-50 ring-1 ring-amber-100">
                                <font-awesome-icon icon="fas fa-trophy" class="text-amber-500 text-sm" />
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-gray-900">Remorques les plus actives</h3>
                                <p class="text-xs text-gray-400 mt-0.5">Classement par nombre de voyages</p>
                            </div>
                        </div>
                        <div class="flex-1">
                            <RankedList
                                :items="data.remorqueStats ?? []"
                                title=""
                                value-key="nbr_voyage"
                                label-key="numero_remorque"
                                primary-label="Voyages"
                                :show-pagination="true"
                                :page-size="6"
                                primary-color="#F59E0B"
                                gradient-from="#F59E0B"
                                gradient-to="#D97706"
                                class="!shadow-none !rounded-none !border-0"
                            />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Fleet Detail Table -->
            <div v-if="isAtLeast('advanced') && sortedVehiculeStats.length" class="rounded-2xl bg-white shadow-sm border border-gray-100/80 overflow-hidden">
                <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100 bg-gray-50/40">
                    <div class="flex items-center gap-3">
                        <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-50 ring-1 ring-emerald-100">
                            <font-awesome-icon icon="fas fa-list-check" class="text-emerald-500 text-sm" />
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-gray-900">Détail par véhicule</h3>
                            <p class="text-xs text-gray-400 mt-0.5">Performance individuelle de la flotte</p>
                        </div>
                    </div>
                    <span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-bold text-gray-500">
                        {{ sortedVehiculeStats.length }} véhicule{{ sortedVehiculeStats.length > 1 ? 's' : '' }}
                    </span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-gray-50/60 border-b border-gray-100">
                                <th class="px-6 py-3 text-left text-[11px] font-bold uppercase tracking-wider text-gray-400 w-14">#</th>
                                <th class="px-6 py-3 text-left text-[11px] font-bold uppercase tracking-wider text-gray-400">Véhicule</th>
                                <th class="px-6 py-3 text-right text-[11px] font-bold uppercase tracking-wider text-gray-400">Voyages</th>
                                <th class="px-6 py-3 text-right text-[11px] font-bold uppercase tracking-wider text-gray-400">Kilométrage</th>
                                <th class="px-6 py-3 text-left text-[11px] font-bold uppercase tracking-wider text-gray-400 w-48">Activité relative</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <tr
                                v-for="(vehicle, idx) in sortedVehiculeStats"
                                :key="vehicle.label + idx"
                                class="group hover:bg-blue-50/20 transition-colors duration-150"
                            >
                                <td class="px-6 py-4">
                                    <span
                                        class="flex h-7 w-7 items-center justify-center rounded-lg text-xs font-extrabold"
                                        :class="{
                                            'bg-amber-100 text-amber-600': idx === 0,
                                            'bg-gray-200 text-gray-500': idx === 1,
                                            'bg-orange-100 text-orange-500': idx === 2,
                                            'bg-gray-100 text-gray-400': idx > 2,
                                        }"
                                    >{{ idx + 1 }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-blue-50 text-blue-400 text-sm group-hover:bg-blue-100 transition-colors duration-150">
                                            <font-awesome-icon icon="fas fa-truck" />
                                        </div>
                                        <span class="font-semibold text-gray-800 truncate max-w-[180px]">{{ vehicle.label }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <span class="font-bold text-gray-900 tabular-nums">{{ formatNumber(vehicle.nbr_voyage) }}</span>
                                    <span class="ml-1 text-xs text-gray-400">voyage{{ vehicle.nbr_voyage > 1 ? 's' : '' }}</span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <span class="font-bold text-gray-900 tabular-nums">{{ formatNumber(vehicle.total_kilometrage) }}</span>
                                    <span class="ml-1 text-xs text-gray-400">km</span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <div class="flex-1 h-2 rounded-full bg-gray-100 overflow-hidden">
                                            <div
                                                class="h-full rounded-full bg-gradient-to-r from-blue-400 to-blue-600 transition-all duration-700 ease-out"
                                                :style="{ width: vehicleActivityPercentage(vehicle) + '%' }"
                                            ></div>
                                        </div>
                                        <span class="w-9 text-right text-xs font-semibold text-gray-400 tabular-nums">
                                            {{ vehicleActivityPercentage(vehicle) }}%
                                        </span>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </SousMenuPrincipale>
</template>

<script setup>
import HorizontalBarChart from "@/Components/Charts/HorizontalBarChart.vue";
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
    data: { type: Object, default: () => ({}) },
    filters: { type: Object, default: () => ({}) },
    flash: { type: Object, default: () => ({}) },
});

const getMonthStartDate = () => dayjs().startOf("month").format("DD-MM-YYYY");
const getMonthEndDate = () => dayjs().endOf("month").format("DD-MM-YYYY");

const filter = ref({
    ...createSearchFilter(),
    search: props.filters?.search ?? "",
    start_date: props.filters?.start_date ?? getMonthStartDate(),
    end_date: props.filters?.end_date ?? getMonthEndDate(),
});

const { isAtLeast } = usePlan()

const periodLabel = computed(() => {
    const start = filter.value.start_date;
    const end = filter.value.end_date;
    if (!start && !end) return "Toute la période";
    const fmt = (d) => d?.split("-").reverse().join("/") ?? "—";
    return `Période du ${fmt(start)} au ${fmt(end)}`;
});

const totalVoyages = computed(() =>
    (props.data.vehiculeStats ?? []).reduce((sum, v) => sum + (v.nbr_voyage ?? 0), 0)
);

const totalKm = computed(() =>
    (props.data.vehiculeStats ?? []).reduce((sum, v) => sum + (v.total_kilometrage ?? 0), 0)
);

const totalKmFormatted = computed(() =>
    Number(totalKm.value).toLocaleString("fr-FR")
);

const sortedVehiculeStats = computed(() =>
    [...(props.data.vehiculeStats ?? [])].sort((a, b) => (b.nbr_voyage ?? 0) - (a.nbr_voyage ?? 0))
);

const maxVoyages = computed(() =>
    Math.max(0, ...sortedVehiculeStats.value.map((v) => v.nbr_voyage ?? 0))
);

const vehicleActivityPercentage = (vehicle) => {
    if (maxVoyages.value <= 0) return 0;
    return Math.round(((vehicle.nbr_voyage ?? 0) / maxVoyages.value) * 100);
};

const formatNumber = (n) => {
    return new Intl.NumberFormat("fr-FR", { maximumFractionDigits: 0 }).format(Number(n) || 0);
};

const applyFilters = () => {
    gotoSearch(filter.value, route("dashboard.vehicule"), ["data"]);
};

const resetFilters = () => {
    filter.value.search = "";
    filter.value.start_date = getMonthStartDate();
    filter.value.end_date = getMonthEndDate();
    applyFilters();
};
</script>
