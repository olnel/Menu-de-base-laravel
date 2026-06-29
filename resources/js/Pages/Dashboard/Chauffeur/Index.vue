<template>
    <SousMenuPrincipale title="Dashboard Chauffeurs" selectedMenu="dashboard_chauffeur" v-if="can('dashboard.chauffeur')">

        <!-- Filtre -->
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

            <!-- KPI Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-5">

                <!-- Total heures conduites -->
                <div class="group relative overflow-hidden rounded-2xl bg-white p-6 shadow-sm border border-gray-100/80 hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300 cursor-default">
                    <div class="absolute inset-x-0 top-0 h-0.5 rounded-t-2xl bg-gradient-to-r from-blue-400 to-blue-600"></div>
                    <div class="flex items-start justify-between gap-4">
                        <div class="min-w-0">
                            <p class="text-[11px] font-bold uppercase tracking-widest text-gray-400">Total Heures conduites</p>
                            <p class="mt-2 text-4xl font-black text-gray-900 tabular-nums">{{ formatHeure(data.kpi?.total_heures) }}</p>
                        </div>
                        <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-500 text-xl group-hover:bg-blue-500 group-hover:text-white transition-all duration-300">
                            <font-awesome-icon icon="fas fa-clock" />
                        </div>
                    </div>
                    <div class="mt-4 h-px bg-gray-100"></div>
                    <p class="mt-3 text-xs text-gray-400">Facturables + non-facturables sur la période</p>
                </div>

                <!-- Heures facturables -->
                <div class="group relative overflow-hidden rounded-2xl bg-white p-6 shadow-sm border border-gray-100/80 hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300 cursor-default">
                    <div class="absolute inset-x-0 top-0 h-0.5 rounded-t-2xl bg-gradient-to-r from-emerald-400 to-teal-500"></div>
                    <div class="flex items-start justify-between gap-4">
                        <div class="min-w-0">
                            <p class="text-[11px] font-bold uppercase tracking-widest text-gray-400">Heures Facturables</p>
                            <p class="mt-2 text-4xl font-black text-gray-900 tabular-nums">{{ formatHeure(data.kpi?.total_heures_facturables) }}</p>
                        </div>
                        <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-500 text-xl group-hover:bg-emerald-500 group-hover:text-white transition-all duration-300">
                            <font-awesome-icon icon="fas fa-file-invoice-dollar" />
                        </div>
                    </div>
                    <div class="mt-4 h-px bg-gray-100"></div>
                    <div class="mt-3 flex items-center gap-2">
                        <div class="h-1.5 flex-1 rounded-full bg-gray-100 overflow-hidden">
                            <div class="h-full rounded-full bg-emerald-400 transition-all duration-700" :style="{ width: facturablesPct + '%' }"></div>
                        </div>
                        <span class="text-xs font-bold text-emerald-500">{{ facturablesPct }}%</span>
                    </div>
                </div>

                <!-- Heures non-facturables -->
                <div class="group relative overflow-hidden rounded-2xl bg-white p-6 shadow-sm border border-gray-100/80 hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300 cursor-default">
                    <div class="absolute inset-x-0 top-0 h-0.5 rounded-t-2xl bg-gradient-to-r from-amber-400 to-orange-500"></div>
                    <div class="flex items-start justify-between gap-4">
                        <div class="min-w-0">
                            <p class="text-[11px] font-bold uppercase tracking-widest text-gray-400">Heures Non-Facturables</p>
                            <p class="mt-2 text-4xl font-black text-gray-900 tabular-nums">{{ formatHeure(data.kpi?.total_heures_non_facturables) }}</p>
                        </div>
                        <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-xl bg-amber-50 text-amber-500 text-xl group-hover:bg-amber-500 group-hover:text-white transition-all duration-300">
                            <font-awesome-icon icon="fas fa-hourglass-half" />
                        </div>
                    </div>
                    <div class="mt-4 h-px bg-gray-100"></div>
                    <p class="mt-3 text-xs text-gray-400">Attentes, pauses et temps hors mission</p>
                </div>

                <!-- Total KM -->
                <div class="group relative overflow-hidden rounded-2xl bg-white p-6 shadow-sm border border-gray-100/80 hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300 cursor-default">
                    <div class="absolute inset-x-0 top-0 h-0.5 rounded-t-2xl bg-gradient-to-r from-violet-400 to-purple-600"></div>
                    <div class="flex items-start justify-between gap-4">
                        <div class="min-w-0">
                            <p class="text-[11px] font-bold uppercase tracking-widest text-gray-400">Kilomètres parcourus</p>
                            <p class="mt-2 text-4xl font-black text-gray-900 tabular-nums">{{ (data.kpi?.total_km ?? 0).toLocaleString('fr-FR') }}</p>
                        </div>
                        <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-xl bg-violet-50 text-violet-500 text-xl group-hover:bg-violet-500 group-hover:text-white transition-all duration-300">
                            <font-awesome-icon icon="fas fa-route" />
                        </div>
                    </div>
                    <div class="mt-4 h-px bg-gray-100"></div>
                    <p class="mt-3 text-xs text-gray-400">Distance totale sur la période</p>
                </div>

                <!-- Nb voyages -->
                <div class="group relative overflow-hidden rounded-2xl bg-white p-6 shadow-sm border border-gray-100/80 hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300 cursor-default">
                    <div class="absolute inset-x-0 top-0 h-0.5 rounded-t-2xl bg-gradient-to-r from-sky-400 to-blue-500"></div>
                    <div class="flex items-start justify-between gap-4">
                        <div class="min-w-0">
                            <p class="text-[11px] font-bold uppercase tracking-widest text-gray-400">Voyages effectués</p>
                            <p class="mt-2 text-4xl font-black text-gray-900 tabular-nums">{{ data.kpi?.total_voyages ?? 0 }}</p>
                        </div>
                        <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-xl bg-sky-50 text-sky-500 text-xl group-hover:bg-sky-500 group-hover:text-white transition-all duration-300">
                            <font-awesome-icon icon="fas fa-truck-moving" />
                        </div>
                    </div>
                    <div class="mt-4 h-px bg-gray-100"></div>
                    <p class="mt-3 text-xs text-gray-400">Nombre total de voyages sur la période</p>
                </div>

                <!-- Nb chauffeurs actifs -->
                <div class="group relative overflow-hidden rounded-2xl bg-white p-6 shadow-sm border border-gray-100/80 hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300 cursor-default">
                    <div class="absolute inset-x-0 top-0 h-0.5 rounded-t-2xl bg-gradient-to-r from-rose-400 to-pink-500"></div>
                    <div class="flex items-start justify-between gap-4">
                        <div class="min-w-0">
                            <p class="text-[11px] font-bold uppercase tracking-widest text-gray-400">Chauffeurs actifs</p>
                            <p class="mt-2 text-4xl font-black text-gray-900 tabular-nums">{{ data.kpi?.nb_chauffeurs ?? 0 }}</p>
                        </div>
                        <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-xl bg-rose-50 text-rose-500 text-xl group-hover:bg-rose-500 group-hover:text-white transition-all duration-300">
                            <font-awesome-icon icon="fas fa-user-tie" />
                        </div>
                    </div>
                    <div class="mt-4 h-px bg-gray-100"></div>
                    <p class="mt-3 text-xs text-gray-400">Titulaires ayant réalisé au moins un voyage</p>
                </div>
            </div>

            <!-- Graphiques : évolution mensuelle + classement -->
            <div v-if="isAtLeast('standard')" class="grid grid-cols-1 xl:grid-cols-12 gap-6 items-stretch">

                <!-- Évolution mensuelle des heures -->
                <div class="xl:col-span-8 flex flex-col">
                    <div class="flex-1 rounded-2xl bg-white shadow-sm border border-gray-100/80 overflow-hidden flex flex-col">
                        <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100 bg-gray-50/40">
                            <div class="flex items-center gap-3">
                                <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-blue-50 ring-1 ring-blue-100">
                                    <font-awesome-icon icon="fas fa-chart-bar" class="text-blue-500 text-sm" />
                                </div>
                                <div>
                                    <h3 class="text-sm font-bold text-gray-900">Heures conduites par mois</h3>
                                    <p class="text-xs text-gray-400 mt-0.5">Évolution sur 12 mois glissants</p>
                                </div>
                            </div>
                        </div>
                        <HorizontalBarChart
                            :items="data.mensuel ?? []"
                            label-key="label"
                            value-key="value"
                            primary-label="Heures"
                            primary-color="#3B82F6"
                            height="360px"
                            :show-legend="false"
                            orientation="x"
                            :show-pagination="false"
                            class="!shadow-none !rounded-none !border-0"
                        />
                    </div>
                </div>

                <!-- Top chauffeurs par heures -->
                <div class="xl:col-span-4 flex flex-col">
                    <div class="flex-1 rounded-2xl bg-white shadow-sm border border-gray-100/80 overflow-hidden flex flex-col">
                        <div class="flex items-center gap-3 px-6 py-5 border-b border-gray-100 bg-gray-50/40">
                            <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-amber-50 ring-1 ring-amber-100">
                                <font-awesome-icon icon="fas fa-trophy" class="text-amber-500 text-sm" />
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-gray-900">Classement chauffeurs</h3>
                                <p class="text-xs text-gray-400 mt-0.5">Par heures totales conduites</p>
                            </div>
                        </div>
                        <div class="flex-1">
                            <RankedList
                                :items="topChauffeurs"
                                label-key="label"
                                value-key="value"
                                primary-label="Heures"
                                :show-pagination="true"
                                :page-size="6"
                                primary-color="#F59E0B"
                                gradient-from="#F59E0B"
                                gradient-to="#EF4444"
                                class="!shadow-none !rounded-none !border-0"
                            />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tableau détaillé par chauffeur -->
            <div v-if="isAtLeast('advanced') && data.par_chauffeur?.length" class="rounded-2xl bg-white shadow-sm border border-gray-100/80 overflow-hidden">
                <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100 bg-gray-50/40">
                    <div class="flex items-center gap-3">
                        <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-gray-100 ring-1 ring-gray-200">
                            <font-awesome-icon icon="fas fa-table-list" class="text-gray-500 text-sm" />
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-gray-900">Récapitulatif par chauffeur</h3>
                            <p class="text-xs text-gray-400 mt-0.5">Détail des heures et kilométrage sur la période</p>
                        </div>
                    </div>
                    <span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-bold text-gray-500">
                        {{ data.par_chauffeur.length }} chauffeur{{ data.par_chauffeur.length > 1 ? 's' : '' }}
                    </span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-gray-50/60 border-b border-gray-100">
                                <th class="px-5 py-3 text-center text-[11px] font-bold uppercase tracking-wider text-gray-400 w-12">#</th>
                                <th class="px-5 py-3 text-left text-[11px] font-bold uppercase tracking-wider text-gray-400">Chauffeur</th>
                                <th class="px-5 py-3 text-right text-[11px] font-bold uppercase tracking-wider text-gray-400">H. Facturables</th>
                                <th class="px-5 py-3 text-right text-[11px] font-bold uppercase tracking-wider text-gray-400">H. Non-Fact.</th>
                                <th class="px-5 py-3 text-right text-[11px] font-bold uppercase tracking-wider text-gray-400">Total Heures</th>
                                <th class="px-5 py-3 text-right text-[11px] font-bold uppercase tracking-wider text-gray-400">KM parcourus</th>
                                <th class="px-5 py-3 text-right text-[11px] font-bold uppercase tracking-wider text-gray-400">Voyages</th>
                                <th class="px-5 py-3 text-left text-[11px] font-bold uppercase tracking-wider text-gray-400">Répartition heures</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <tr
                                v-for="(row, idx) in data.par_chauffeur"
                                :key="row.chauffeur_id"
                                class="group hover:bg-gray-50/60 transition-colors duration-150"
                            >
                                <!-- Rang -->
                                <td class="px-5 py-4 text-center">
                                    <span
                                        class="inline-flex h-6 w-6 items-center justify-center rounded-full text-xs font-black"
                                        :class="rankClass(idx)"
                                    >{{ idx + 1 }}</span>
                                </td>

                                <!-- Nom chauffeur -->
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-full bg-blue-50 text-blue-600 text-xs font-bold">
                                            {{ initiales(row.nom_chauffeur) }}
                                        </div>
                                        <span class="font-semibold text-gray-800">{{ row.nom_chauffeur }}</span>
                                    </div>
                                </td>

                                <!-- H. Facturables -->
                                <td class="px-5 py-4 text-right">
                                    <span class="font-semibold text-emerald-600">{{ formatHeure(row.total_heures_facturables) }}</span>
                                </td>

                                <!-- H. Non-Fact. -->
                                <td class="px-5 py-4 text-right">
                                    <span class="font-semibold text-amber-600">{{ formatHeure(row.total_heures_non_facturables) }}</span>
                                </td>

                                <!-- Total heures -->
                                <td class="px-5 py-4 text-right">
                                    <span class="font-black text-gray-900">{{ formatHeure(row.total_heures) }}</span>
                                </td>

                                <!-- KM -->
                                <td class="px-5 py-4 text-right">
                                    <span class="font-semibold text-violet-600">{{ Number(row.total_km).toLocaleString('fr-FR') }} km</span>
                                </td>

                                <!-- Nb voyages -->
                                <td class="px-5 py-4 text-right">
                                    <span class="font-semibold text-sky-600">{{ row.total_voyages }}</span>
                                </td>

                                <!-- Barre de répartition -->
                                <td class="px-5 py-4 w-40">
                                    <div v-if="row.total_heures > 0" class="space-y-1">
                                        <div class="flex h-2 rounded-full overflow-hidden bg-gray-100">
                                            <div
                                                class="bg-emerald-400 transition-all duration-700"
                                                :style="{ width: facturablePct(row) + '%' }"
                                            ></div>
                                            <div
                                                class="bg-amber-400 transition-all duration-700"
                                                :style="{ width: (100 - facturablePct(row)) + '%' }"
                                            ></div>
                                        </div>
                                        <div class="flex justify-between text-[10px] text-gray-400">
                                            <span class="text-emerald-500">{{ facturablePct(row) }}% fact.</span>
                                        </div>
                                    </div>
                                    <span v-else class="text-xs text-gray-300">—</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Aucune donnée -->
            <div v-if="isAtLeast('advanced') && !data.par_chauffeur?.length" class="rounded-2xl bg-white shadow-sm border border-gray-100/80 p-16 text-center">
                <font-awesome-icon icon="fas fa-user-slash" class="text-4xl text-gray-200 mb-4" />
                <p class="text-sm font-semibold text-gray-400">Aucune donnée de conduite sur la période</p>
                <p class="text-xs text-gray-300 mt-1">Modifiez la période ou vérifiez que les voyages ont été saisis</p>
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
const props = defineProps({
    data:    { type: Object, default: () => ({}) },
    filters: { type: Object, default: () => ({}) },
});

const { isAtLeast } = usePlan()
const dateFormat      = "DD/MM/YYYY";
const dropdownVisible = ref(false);

const getMonthStart = () => dayjs().startOf("month").format("DD-MM-YYYY");
const getMonthEnd   = () => dayjs().endOf("month").format("DD-MM-YYYY");

const filter = ref({
    ...createSearchFilter(),
    search:     props.filters?.search     ?? "",
    start_date: props.filters?.start_date ?? getMonthStart(),
    end_date:   props.filters?.end_date   ?? getMonthEnd(),
});

const applyFilters = () => {
    dropdownVisible.value = false;
    gotoSearch(filter.value, route("dashboard.chauffeur"), ["data"]);
};

const resetFilters = () => {
    filter.value.search     = "";
    filter.value.start_date = getMonthStart();
    filter.value.end_date   = getMonthEnd();
    applyFilters();
};

// Helpers
const formatHeure = (h) => {
    const val = parseFloat(h ?? 0);
    if (isNaN(val)) return "0 h";
    const hrs = Math.floor(val);
    const min = Math.round((val - hrs) * 60);
    return min > 0 ? `${hrs}h${String(min).padStart(2, "0")}` : `${hrs} h`;
};

const initiales = (nom) => {
    if (!nom) return "?";
    return nom.split(" ").slice(0, 2).map((w) => w[0]).join("").toUpperCase();
};

const facturablesPct = computed(() => {
    const total = props.data.kpi?.total_heures ?? 0;
    const fact  = props.data.kpi?.total_heures_facturables ?? 0;
    return total > 0 ? Math.round((fact / total) * 100) : 0;
});

const facturablePct = (row) => {
    if (!row.total_heures || row.total_heures <= 0) return 0;
    return Math.round((row.total_heures_facturables / row.total_heures) * 100);
};

const topChauffeurs = computed(() =>
    (props.data.par_chauffeur ?? []).map((r) => ({
        label: r.nom_chauffeur,
        value: parseFloat(r.total_heures ?? 0),
    }))
);

const rankClass = (idx) => {
    if (idx === 0) return "bg-amber-100 text-amber-700";
    if (idx === 1) return "bg-gray-200 text-gray-600";
    if (idx === 2) return "bg-orange-100 text-orange-700";
    return "bg-gray-100 text-gray-500";
};
</script>
