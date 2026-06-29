<template>
    <SousMenuPrincipale title="Dashboard Pneus" selectedMenu="dashboard_pneu" v-if="can('dashboard.pneu')">

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

                <!-- En service -->
                <div class="group relative overflow-hidden rounded-2xl bg-white p-6 shadow-sm border border-gray-100/80 hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300 cursor-default">
                    <div class="absolute inset-x-0 top-0 h-0.5 rounded-t-2xl bg-gradient-to-r from-blue-400 to-blue-600"></div>
                    <div class="flex items-start justify-between gap-4">
                        <div class="min-w-0">
                            <p class="text-[11px] font-bold uppercase tracking-widest text-gray-400">En service</p>
                            <p class="mt-2 text-4xl font-black text-gray-900 tabular-nums">{{ data.kpi?.totalEnService ?? 0 }}</p>
                        </div>
                        <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-500 text-xl group-hover:bg-blue-500 group-hover:text-white transition-all duration-300">
                            <font-awesome-icon icon="fas fa-circle-dot" />
                        </div>
                    </div>
                    <div class="mt-4 h-px bg-gray-100"></div>
                    <p class="mt-3 text-xs text-gray-400">Montés sur véhicule ou remorque</p>
                </div>

                <!-- En stock -->
                <div class="group relative overflow-hidden rounded-2xl bg-white p-6 shadow-sm border border-gray-100/80 hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300 cursor-default">
                    <div class="absolute inset-x-0 top-0 h-0.5 rounded-t-2xl bg-gradient-to-r from-emerald-400 to-teal-500"></div>
                    <div class="flex items-start justify-between gap-4">
                        <div class="min-w-0">
                            <p class="text-[11px] font-bold uppercase tracking-widest text-gray-400">En stock</p>
                            <p class="mt-2 text-4xl font-black text-gray-900 tabular-nums">{{ data.kpi?.totalEnStock ?? 0 }}</p>
                        </div>
                        <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-500 text-xl group-hover:bg-emerald-500 group-hover:text-white transition-all duration-300">
                            <font-awesome-icon icon="fas fa-warehouse" />
                        </div>
                    </div>
                    <div class="mt-4 h-px bg-gray-100"></div>
                    <p class="mt-3 text-xs text-gray-400">Disponibles en magasin</p>
                </div>

                <!-- Opérations période -->
                <div class="group relative overflow-hidden rounded-2xl bg-white p-6 shadow-sm border border-gray-100/80 hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300 cursor-default">
                    <div class="absolute inset-x-0 top-0 h-0.5 rounded-t-2xl bg-gradient-to-r from-amber-400 to-orange-500"></div>
                    <div class="flex items-start justify-between gap-4">
                        <div class="min-w-0">
                            <p class="text-[11px] font-bold uppercase tracking-widest text-gray-400">Opérations</p>
                            <p class="mt-2 text-4xl font-black text-gray-900 tabular-nums">{{ data.kpi?.totalRemplacements ?? 0 }}</p>
                        </div>
                        <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-xl bg-amber-50 text-amber-500 text-xl group-hover:bg-amber-500 group-hover:text-white transition-all duration-300">
                            <font-awesome-icon icon="fas fa-arrows-rotate" />
                        </div>
                    </div>
                    <div class="mt-4 h-px bg-gray-100"></div>
                    <p class="mt-3 text-xs text-gray-400">Remplacements / permutations sur la période</p>
                </div>

                <!-- À surveiller -->
                <div class="group relative overflow-hidden rounded-2xl bg-white p-6 shadow-sm border border-gray-100/80 hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300 cursor-default">
                    <div class="absolute inset-x-0 top-0 h-0.5 rounded-t-2xl bg-gradient-to-r from-red-400 to-rose-500"></div>
                    <div class="flex items-start justify-between gap-4">
                        <div class="min-w-0">
                            <p class="text-[11px] font-bold uppercase tracking-widest text-gray-400">À surveiller</p>
                            <p class="mt-2 text-4xl font-black tabular-nums" :class="(data.kpi?.pneusASurveiller ?? 0) > 0 ? 'text-red-600' : 'text-gray-900'">
                                {{ data.kpi?.pneusASurveiller ?? 0 }}
                            </p>
                        </div>
                        <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-xl bg-red-50 text-red-500 text-xl group-hover:bg-red-500 group-hover:text-white transition-all duration-300">
                            <font-awesome-icon icon="fas fa-triangle-exclamation" />
                        </div>
                    </div>
                    <div class="mt-4 h-px bg-gray-100"></div>
                    <p class="mt-3 text-xs text-gray-400">Pneus usés ou rechapés sur la flotte</p>
                </div>
            </div>

            <!-- ── Graphiques : Opérations & Top pneus ───────────────────── -->
            <div v-if="isAtLeast('standard')" class="grid grid-cols-1 xl:grid-cols-12 gap-6 items-stretch">

                <!-- Remplacements par mois -->
                <div class="xl:col-span-8 flex flex-col">
                    <div class="flex-1 rounded-2xl bg-white shadow-sm border border-gray-100/80 overflow-hidden flex flex-col">
                        <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100 bg-gray-50/40">
                            <div class="flex items-center gap-3">
                                <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-amber-50 ring-1 ring-amber-100">
                                    <font-awesome-icon icon="fas fa-chart-bar" class="text-amber-500 text-sm" />
                                </div>
                                <div>
                                    <h3 class="text-sm font-bold text-gray-900">Opérations par mois</h3>
                                    <p class="text-xs text-gray-400 mt-0.5">Remplacements et permutations sur 12 mois glissants</p>
                                </div>
                            </div>
                        </div>
                        <HorizontalBarChart
                            :items="data.remplacements_par_mois ?? []"
                            label-key="label"
                            value-key="value"
                            primary-label="Opérations"
                            primary-color="#F59E0B"
                            height="360px"
                            :show-legend="false"
                            orientation="x"
                            :show-pagination="false"
                            class="!shadow-none !rounded-none !border-0"
                        />
                    </div>
                </div>

                <!-- Top pneus par voyages -->
                <div class="xl:col-span-4 flex flex-col">
                    <div class="flex-1 rounded-2xl bg-white shadow-sm border border-gray-100/80 overflow-hidden flex flex-col">
                        <div class="flex items-center gap-3 px-6 py-5 border-b border-gray-100 bg-gray-50/40">
                            <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-blue-50 ring-1 ring-blue-100">
                                <font-awesome-icon icon="fas fa-trophy" class="text-blue-500 text-sm" />
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-gray-900">Top pneus</h3>
                                <p class="text-xs text-gray-400 mt-0.5">Classement par nombre de voyages actifs</p>
                            </div>
                        </div>
                        <div class="flex-1">
                            <RankedList
                                :items="data.top_pneus ?? []"
                                label-key="label"
                                value-key="value"
                                primary-label="Voyages"
                                :show-pagination="true"
                                :page-size="6"
                                primary-color="#3B82F6"
                                gradient-from="#3B82F6"
                                gradient-to="#6366F1"
                                class="!shadow-none !rounded-none !border-0"
                            />
                        </div>
                    </div>
                </div>
            </div>

            <!-- ── Distribution état + type opération ───────────────────── -->
            <div v-if="isAtLeast('advanced')" class="grid grid-cols-1 xl:grid-cols-2 gap-6">

                <!-- Distribution par état -->
                <div class="rounded-2xl bg-white shadow-sm border border-gray-100/80 overflow-hidden">
                    <div class="flex items-center gap-3 px-6 py-5 border-b border-gray-100 bg-gray-50/40">
                        <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-50 ring-1 ring-emerald-100">
                            <font-awesome-icon icon="fas fa-circle-half-stroke" class="text-emerald-500 text-sm" />
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-gray-900">Répartition par état</h3>
                            <p class="text-xs text-gray-400 mt-0.5">Distribution de tous les pneus du parc</p>
                        </div>
                    </div>
                    <div class="p-6 space-y-3">
                        <div v-if="!data.distribution_etat?.length" class="text-center py-8 text-gray-400 text-sm">Aucune donnée</div>
                        <div v-for="item in data.distribution_etat" :key="item.label" class="flex items-center gap-3">
                            <span class="w-20 text-xs font-semibold text-gray-600 truncate">{{ item.label }}</span>
                            <div class="flex-1 h-3 rounded-full bg-gray-100 overflow-hidden">
                                <div
                                    class="h-full rounded-full transition-all duration-700"
                                    :style="{ width: etatPct(item) + '%', backgroundColor: etatColor(item.label) }"
                                ></div>
                            </div>
                            <span class="w-10 text-right text-xs font-bold tabular-nums" :style="{ color: etatColor(item.label) }">{{ item.value }}</span>
                            <span class="w-9 text-right text-xs text-gray-400 tabular-nums">{{ etatPct(item) }}%</span>
                        </div>
                    </div>
                </div>

                <!-- Distribution remplacement vs permutation -->
                <div class="rounded-2xl bg-white shadow-sm border border-gray-100/80 overflow-hidden">
                    <div class="flex items-center gap-3 px-6 py-5 border-b border-gray-100 bg-gray-50/40">
                        <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-violet-50 ring-1 ring-violet-100">
                            <font-awesome-icon icon="fas fa-arrows-rotate" class="text-violet-500 text-sm" />
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-gray-900">Type d'opération</h3>
                            <p class="text-xs text-gray-400 mt-0.5">Répartition sur la période sélectionnée</p>
                        </div>
                    </div>
                    <div class="p-6">
                        <div v-if="!data.distribution_type_op?.length" class="text-center py-8 text-gray-400 text-sm">Aucune opération sur la période</div>
                        <div v-else class="grid grid-cols-2 gap-4">
                            <div
                                v-for="(item, i) in data.distribution_type_op" :key="item.label"
                                class="rounded-xl p-5 flex flex-col items-center gap-2"
                                :class="i === 0 ? 'bg-amber-50 border border-amber-100' : 'bg-violet-50 border border-violet-100'"
                            >
                                <span class="text-3xl font-black tabular-nums" :class="i === 0 ? 'text-amber-600' : 'text-violet-600'">{{ item.value }}</span>
                                <span class="text-xs font-semibold uppercase tracking-wider" :class="i === 0 ? 'text-amber-500' : 'text-violet-500'">{{ item.label }}</span>
                                <span class="text-xs text-gray-400">{{ typeOpPct(item) }}% du total</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ── Tableau dernières opérations ─────────────────────────── -->
            <div v-if="isAtLeast('advanced') && data.derniers_ops?.length" class="rounded-2xl bg-white shadow-sm border border-gray-100/80 overflow-hidden">
                <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100 bg-gray-50/40">
                    <div class="flex items-center gap-3">
                        <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-gray-100 ring-1 ring-gray-200">
                            <font-awesome-icon icon="fas fa-list-check" class="text-gray-500 text-sm" />
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-gray-900">Dernières opérations</h3>
                            <p class="text-xs text-gray-400 mt-0.5">Remplacements et permutations récents</p>
                        </div>
                    </div>
                    <span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-bold text-gray-500">
                        {{ data.derniers_ops.length }} opération{{ data.derniers_ops.length > 1 ? 's' : '' }}
                    </span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-gray-50/60 border-b border-gray-100">
                                <th class="px-5 py-3 text-left text-[11px] font-bold uppercase tracking-wider text-gray-400">Date</th>
                                <th class="px-5 py-3 text-left text-[11px] font-bold uppercase tracking-wider text-gray-400">Type</th>
                                <th class="px-5 py-3 text-left text-[11px] font-bold uppercase tracking-wider text-gray-400">Véhicule / Remorque</th>
                                <th class="px-5 py-3 text-left text-[11px] font-bold uppercase tracking-wider text-gray-400">Pneu retiré</th>
                                <th class="px-5 py-3 text-left text-[11px] font-bold uppercase tracking-wider text-gray-400">Pneu monté</th>
                                <th class="px-5 py-3 text-left text-[11px] font-bold uppercase tracking-wider text-gray-400">Position</th>
                                <th class="px-5 py-3 text-left text-[11px] font-bold uppercase tracking-wider text-gray-400">Motif</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <tr v-for="(op, idx) in data.derniers_ops" :key="op.id ?? idx" class="group hover:bg-gray-50/60 transition-colors duration-150">
                                <td class="px-5 py-3.5 whitespace-nowrap">
                                    <span class="text-xs font-semibold text-gray-700">{{ formatDate(op.date_operation) }}</span>
                                </td>
                                <td class="px-5 py-3.5">
                                    <span
                                        class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-semibold"
                                        :class="op.type_operation === 'remplacement' ? 'bg-amber-100 text-amber-700' : 'bg-violet-100 text-violet-700'"
                                    >
                                        <font-awesome-icon :icon="op.type_operation === 'remplacement' ? 'fas fa-arrow-right-arrow-left' : 'fas fa-arrows-rotate'" class="text-[10px]" />
                                        {{ op.type_operation === 'remplacement' ? 'Remplacement' : 'Permutation' }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5">
                                    <span class="font-medium text-gray-800">{{ op.immatriculation ?? op.numero_remorque ?? '—' }}</span>
                                </td>
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center gap-1.5">
                                        <span class="w-1.5 h-1.5 rounded-full bg-red-400 flex-shrink-0"></span>
                                        <span class="font-mono text-xs text-gray-700">{{ op.pneu_retire ?? '—' }}</span>
                                        <a-tag v-if="op.etat_retire" color="default" class="!text-[10px] !px-1 !py-0 !leading-4">{{ op.etat_retire }}</a-tag>
                                    </div>
                                </td>
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center gap-1.5">
                                        <span class="w-1.5 h-1.5 rounded-full bg-green-400 flex-shrink-0"></span>
                                        <span class="font-mono text-xs text-gray-700">{{ op.pneu_monte ?? '—' }}</span>
                                    </div>
                                </td>
                                <td class="px-5 py-3.5 text-xs text-gray-500">{{ op.position_retire ?? '—' }}</td>
                                <td class="px-5 py-3.5">
                                    <span v-if="op.motif" class="inline-flex items-center rounded-full bg-gray-100 px-2 py-0.5 text-xs text-gray-600 capitalize">{{ op.motif }}</span>
                                    <span v-else class="text-gray-400 text-xs">—</span>
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
const props = defineProps({
    data:    { type: Object, default: () => ({}) },
    filters: { type: Object, default: () => ({}) },
});

const { isAtLeast } = usePlan()
const dateFormat       = "DD/MM/YYYY";
const dropdownVisible  = ref(false);

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
    gotoSearch(filter.value, route("dashboard.pneu"), ["data"]);
};

const resetFilters = () => {
    filter.value.search     = "";
    filter.value.start_date = getMonthStart();
    filter.value.end_date   = getMonthEnd();
    applyFilters();
};

// ── Helpers ────────────────────────────────────────────────────────────────

const formatDate = (d) => d ? dayjs(d).format("DD/MM/YYYY") : "—";

const ETAT_COLORS = {
    NEUF:    "#10B981",
    BON:     "#3B82F6",
    "USÉ":   "#F59E0B",
    RECHAPÉ: "#8B5CF6",
};
const etatColor = (etat) => ETAT_COLORS[etat?.toUpperCase()] ?? "#9CA3AF";

const maxEtat = computed(() =>
    Math.max(0, ...(props.data.distribution_etat ?? []).map(e => e.value))
);
const etatPct = (item) =>
    maxEtat.value > 0 ? Math.round((item.value / maxEtat.value) * 100) : 0;

const totalTypeOp = computed(() =>
    (props.data.distribution_type_op ?? []).reduce((s, i) => s + i.value, 0)
);
const typeOpPct = (item) =>
    totalTypeOp.value > 0 ? Math.round((item.value / totalTypeOp.value) * 100) : 0;
</script>
