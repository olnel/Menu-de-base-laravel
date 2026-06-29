<template>
    <SousMenuPrincipale
        v-if="can('dashboard.rentabilite')"
        title="Rentabilité des Actifs"
        selectedMenu="dashboard_rentabilite"
    >
        <!-- Filter Bar -->
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
                        <div class="bg-white p-4 w-96 space-y-4 rounded-xl">
                            <div class="flex flex-col gap-2">
                                <label
                                    class="text-xs font-semibold text-gray-500 uppercase tracking-wider"
                                    >Période</label
                                >
                                <div class="grid grid-cols-2 gap-2">
                                    <a-date-picker
                                        v-model:value="filter.start_date"
                                        :format="dateFormat"
                                        size="large"
                                        class="w-full !rounded-lg"
                                        placeholder="Du"
                                        :value-format="'YYYY-MM-DD'"
                                    />
                                    <a-date-picker
                                        v-model:value="filter.end_date"
                                        :format="dateFormat"
                                        size="large"
                                        class="w-full !rounded-lg"
                                        placeholder="Au"
                                        :value-format="'YYYY-MM-DD'"
                                    />
                                </div>
                            </div>
                            <div class="flex gap-2 pt-2">
                                <a-button
                                    block
                                    type="primary"
                                    size="large"
                                    class="!rounded-lg"
                                    @click="applyFilters"
                                    >Appliquer</a-button
                                >
                                <a-button
                                    block
                                    type="default"
                                    size="large"
                                    class="!rounded-lg"
                                    @click="dropdownVisible = false"
                                    >Fermer</a-button
                                >
                            </div>
                        </div>
                    </template>
                    <a-button
                        size="large"
                        type="default"
                        class="!rounded-none border-r-0 focus:z-10 group"
                    >
                        <Space>
                            <font-awesome-icon
                                class="text-[15px] text-gray-400 group-hover:text-primary transition-colors"
                                icon="fa-filter"
                            />
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
                <div
                    class="group relative overflow-hidden rounded-2xl bg-white p-6 shadow-sm border border-gray-100/80 hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300 cursor-default"
                >
                    <div
                        class="absolute inset-x-0 top-0 h-0.5 rounded-t-2xl bg-gradient-to-r from-blue-400 to-blue-600"
                    ></div>
                    <div class="flex items-start justify-between gap-4">
                        <div class="min-w-0">
                            <p
                                class="text-[11px] font-bold uppercase tracking-widest text-gray-400"
                            >
                                Chiffre d'Affaires
                            </p>
                            <p
                                class="mt-2 text-3xl font-black text-gray-900 tabular-nums"
                            >
                                {{ formatCurrencyLocal(summary.total_revenus) }}
                            </p>
                        </div>
                        <div
                            class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-500 text-xl group-hover:bg-blue-500 group-hover:text-white transition-all duration-300"
                        >
                            <font-awesome-icon
                                icon="fas fa-money-bill-trend-up"
                            />
                        </div>
                    </div>
                    <div class="mt-4 h-px bg-gray-100"></div>
                    <p class="mt-3 text-xs text-gray-400">
                        Total des revenus générés
                    </p>
                </div>

                <div
                    class="group relative overflow-hidden rounded-2xl bg-white p-6 shadow-sm border border-gray-100/80 hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300 cursor-default"
                >
                    <div
                        class="absolute inset-x-0 top-0 h-0.5 rounded-t-2xl bg-gradient-to-r from-emerald-400 to-teal-500"
                    ></div>
                    <div class="flex items-start justify-between gap-4">
                        <div class="min-w-0">
                            <p
                                class="text-[11px] font-bold uppercase tracking-widest text-gray-400"
                            >
                                Marge Nette
                            </p>
                            <p
                                class="mt-2 text-3xl font-black text-gray-900 tabular-nums"
                                :class="
                                    summary.total_marge >= 0
                                        ? 'text-emerald-600'
                                        : 'text-red-600'
                                "
                            >
                                {{ formatCurrencyLocal(summary.total_marge) }}
                            </p>
                        </div>
                        <div
                            class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-500 text-xl group-hover:bg-emerald-500 group-hover:text-white transition-all duration-300"
                        >
                            <font-awesome-icon icon="fas fa-sack-dollar" />
                        </div>
                    </div>
                    <div class="mt-4 h-px bg-gray-100"></div>
                    <p class="mt-3 text-xs text-gray-400">
                        Revenus - Toutes les charges
                    </p>
                </div>

                <div
                    class="group relative overflow-hidden rounded-2xl bg-white p-6 shadow-sm border border-gray-100/80 hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300 cursor-default"
                >
                    <div
                        class="absolute inset-x-0 top-0 h-0.5 rounded-t-2xl bg-gradient-to-r from-amber-400 to-orange-500"
                    ></div>
                    <div class="flex items-start justify-between gap-4">
                        <div class="min-w-0">
                            <p
                                class="text-[11px] font-bold uppercase tracking-widest text-gray-400"
                            >
                                Charges Totales
                            </p>
                            <p
                                class="mt-2 text-3xl font-black text-gray-900 tabular-nums"
                            >
                                {{ formatCurrencyLocal(summary.total_charges) }}
                            </p>
                        </div>
                        <div
                            class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-xl bg-amber-50 text-amber-500 text-xl group-hover:bg-amber-500 group-hover:text-white transition-all duration-300"
                        >
                            <font-awesome-icon
                                icon="fas fa-file-invoice-dollar"
                            />
                        </div>
                    </div>
                    <div class="mt-4 h-px bg-gray-100"></div>
                    <p class="mt-3 text-xs text-gray-400">
                        Maintenance + Route + Social
                    </p>
                </div>

                <div
                    class="group relative overflow-hidden rounded-2xl bg-white p-6 shadow-sm border border-gray-100/80 hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300 cursor-default"
                >
                    <div
                        class="absolute inset-x-0 top-0 h-0.5 rounded-t-2xl bg-gradient-to-r from-violet-400 to-purple-600"
                    ></div>
                    <div class="flex items-start justify-between gap-4">
                        <div class="min-w-0">
                            <p
                                class="text-[11px] font-bold uppercase tracking-widest text-gray-400"
                            >
                                ROI Moyen
                            </p>
                            <p
                                class="mt-2 text-3xl font-black text-gray-900 tabular-nums"
                            >
                                {{
                                    Math.round(summary.average_roi * 100) / 100
                                }}%
                            </p>
                        </div>
                        <div
                            class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-xl bg-violet-50 text-violet-500 text-xl group-hover:bg-violet-500 group-hover:text-white transition-all duration-300"
                        >
                            <font-awesome-icon icon="fas fa-chart-line" />
                        </div>
                    </div>
                    <div class="mt-4 h-px bg-gray-100"></div>
                    <p class="mt-3 text-xs text-gray-400">
                        Retour sur investissement moyen
                    </p>
                </div>
            </div>

            <!-- Profitability Detail Table -->
            <div
                class="rounded-2xl bg-white shadow-sm border border-gray-100/80 overflow-hidden"
            >
                <div
                    class="flex items-center justify-between px-6 py-5 border-b border-gray-100 bg-gray-50/40"
                >
                    <div class="flex items-center gap-3">
                        <div
                            class="flex h-9 w-9 items-center justify-center rounded-xl bg-primary/10 ring-1 ring-primary/20"
                        >
                            <font-awesome-icon
                                icon="fas fa-table-list"
                                class="text-primary text-sm"
                            />
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-gray-900">
                                Analyse de Rentabilité par Actif
                            </h3>
                            <p class="text-xs text-gray-400 mt-0.5">
                                Détail des revenus et coûts par véhicule et
                                remorque
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2" v-if="can('dashboard.rentabilite.export')">
                        <a-button
                            type="default"
                            class="!rounded-lg flex items-center gap-1.5"
                            :href="exportExcelUrl"
                            target="_blank"
                        >
                            <font-awesome-icon
                                icon="fas fa-file-excel"
                                class="text-emerald-600"
                            />
                            <span>Exporter Excel</span>
                        </a-button>
                        <a-button
                            type="default"
                            class="!rounded-lg flex items-center gap-1.5"
                            :href="exportPdfUrl"
                            target="_blank"
                        >
                            <font-awesome-icon
                                icon="fas fa-file-pdf"
                                class="text-red-600"
                            />
                            <span>Exporter PDF</span>
                        </a-button>
                    </div>
                </div>
                <BaseDataTable
                    :columns="columns"
                    :data="tableDataWrapper"
                    :filters="computedFilters"
                    :show-index="false"
                    :btn_action="false"
                >
                    <template #default="{ column, record }">
                        <template v-if="column.key === 'actif'">
                            <div class="flex items-center gap-3">
                                <div
                                    class="flex h-9 w-9 items-center justify-center rounded-xl transition-colors duration-150"
                                    :class="
                                        record.type === 'vehicule'
                                            ? 'bg-blue-50 text-blue-400 group-hover:bg-blue-100'
                                            : 'bg-amber-50 text-amber-400 group-hover:bg-amber-100'
                                    "
                                >
                                    <font-awesome-icon
                                        :icon="
                                            record.type === 'vehicule'
                                                ? 'fas fa-truck'
                                                : 'fas fa-trailer'
                                        "
                                    />
                                </div>
                                <div>
                                    <p
                                        class="font-bold text-gray-800 leading-none"
                                    >
                                        {{ record.label }}
                                    </p>
                                    <p
                                        class="text-[10px] text-gray-400 mt-1 uppercase tracking-tight"
                                    >
                                        {{ record.sub_label }}
                                    </p>
                                </div>
                            </div>
                        </template>
                        <template v-else-if="column.key === 'valeur_initial'">
                            <span
                                class="font-medium text-gray-600 tabular-nums"
                            >
                                {{ formatCurrencyLocal(record.valeur_initial) }}
                            </span>
                        </template>
                        <template v-else-if="column.key === 'revenus'">
                            <span class="font-bold text-gray-900 tabular-nums">
                                {{ formatCurrencyLocal(record.revenus) }}
                            </span>
                            <p
                                class="text-[10px] text-gray-400 font-normal leading-tight mt-0.5"
                            >
                                {{ record.nb_voyages }} voyage{{
                                    record.nb_voyages > 1 ? "s" : ""
                                }}
                            </p>
                        </template>
                        <template
                            v-else-if="column.key === 'depense_maintenance'"
                        >
                            <span class="text-gray-600 tabular-nums">
                                {{ formatCurrencyLocal(record.depense_maintenance) }}
                            </span>
                        </template>
                        <template v-else-if="column.key === 'charges_route'">
                            <span class="text-gray-600 tabular-nums">
                                {{ formatCurrencyLocal(record.charges_route) }}
                            </span>
                        </template>
                        <template v-else-if="column.key === 'cout_social'">
                            <span class="text-gray-600 tabular-nums">
                                {{ formatCurrencyLocal(record.cout_social) }}
                            </span>
                        </template>
                        <template v-else-if="column.key === 'marge_nette'">
                            <span
                                class="font-black tabular-nums"
                                :class="
                                    record.marge_nette >= 0
                                        ? 'text-emerald-600'
                                        : 'text-red-600'
                                "
                            >
                                {{ formatCurrencyLocal(record.marge_nette) }}
                            </span>
                        </template>
                        <template v-else-if="column.key === 'roi'">
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold"
                                :class="getROIBadgeClass(record.roi)"
                            >
                                {{ record.roi }}%
                            </span>
                        </template>
                    </template>
                </BaseDataTable>
            </div>
        </div>
    </SousMenuPrincipale>
</template>

<script setup>
import BaseDataTable from "@/Components/BaseDataTable.vue";
import FilterBase from "@/Components/Filter/FilterBase.vue";
import SousMenuPrincipale from "@/Pages/Menu/SousMenuPrincipale.vue";
import usePermissions from "@/UserPermissions/usePermissions.js";
import { DownOutlined } from "@ant-design/icons-vue";
import { Space } from "ant-design-vue";
import dayjs from "dayjs";
import { computed, ref, watch } from "vue";
import { useCurrency } from "@/Composables/useCurrency.js";
import {
    createSearchFilter,
    gotoSearch,
} from "../../../../Utils/FiltreUtils.js";

const dropdownVisible = ref(false);
const dateFormat = "DD/MM/YYYY";

const { can } = usePermissions();

const props = defineProps({
    data: { type: Array, default: () => [] },
    summary: { type: Object, default: () => ({}) },
    pagination: { type: Object, default: () => ({}) },
    filters: { type: Object, default: () => ({}) },
});

const columns = [
    {
        title: "Actif",
        dataIndex: "label",
        key: "actif",
    },
    {
        title: "Investissement",
        dataIndex: "valeur_initial",
        key: "valeur_initial",
        align: "right",
    },
    {
        title: "Revenus",
        dataIndex: "revenus",
        key: "revenus",
        align: "right",
    },
    {
        title: "Maintenance",
        dataIndex: "depense_maintenance",
        key: "depense_maintenance",
        align: "right",
    },
    {
        title: "Charges Route",
        dataIndex: "charges_route",
        key: "charges_route",
        align: "right",
    },
    {
        title: "Coût Social",
        dataIndex: "cout_social",
        key: "cout_social",
        align: "right",
    },
    {
        title: "Marge Nette",
        dataIndex: "marge_nette",
        key: "marge_nette",
        align: "right",
    },
    {
        title: "ROI",
        dataIndex: "roi",
        key: "roi",
        align: "center",
    },
];

const tableDataWrapper = computed(() => {
    return {
        data: props.data,
        total: props.pagination.total || 0,
        current_page: props.pagination.current_page || 1,
        per_page: props.pagination.per_page || 10,
    };
});

const computedFilters = computed(() => {
    const { page, ...rest } = filter.value;
    return rest;
});

const exportExcelUrl = computed(() => {
    const params = new URLSearchParams();
    if (filter.value.search) params.append("search", filter.value.search);
    if (filter.value.start_date)
        params.append("start_date", filter.value.start_date);
    if (filter.value.end_date) params.append("end_date", filter.value.end_date);
    params.append("type_export", "xlsx");
    return route("dashboard.rentabilite.export") + "?" + params.toString();
});

const exportPdfUrl = computed(() => {
    const params = new URLSearchParams();
    if (filter.value.search) params.append("search", filter.value.search);
    if (filter.value.start_date)
        params.append("start_date", filter.value.start_date);
    if (filter.value.end_date) params.append("end_date", filter.value.end_date);
    params.append("type_export", "pdf");
    return route("dashboard.rentabilite.export") + "?" + params.toString();
});

const getMonthStartDate = () => dayjs().startOf("month").format("YYYY-MM-DD");
const getMonthEndDate = () => dayjs().endOf("month").format("YYYY-MM-DD");

const filter = ref({
    ...createSearchFilter(),
    search: props.filters?.search ?? "",
    start_date: props.filters?.start_date ?? getMonthStartDate(),
    end_date: props.filters?.end_date ?? getMonthEndDate(),
    page: props.pagination?.current_page ?? 1,
});

watch(
    () => props.pagination?.current_page,
    (newPage) => {
        if (newPage) {
            filter.value.page = newPage;
        }
    },
);

const { formatCurrency: formatCurrencyLocal } = useCurrency({ maximumFractionDigits: 0 });

const getROIBadgeClass = (roi) => {
    if (roi >= 20) return "bg-emerald-100 text-emerald-700";
    if (roi >= 5) return "bg-blue-100 text-blue-700";
    if (roi > 0) return "bg-amber-100 text-amber-700";
    return "bg-red-100 text-red-700";
};

const applyFilters = () => {
    filter.value.page = 1; // Reset to page 1 on new search/filter
    gotoSearch(filter.value, route("dashboard.rentabilite"), [
        "data",
        "pagination",
        "summary",
    ]);
};

const handlePageChange = (page) => {
    filter.value.page = page;
    gotoSearch(filter.value, route("dashboard.rentabilite"), [
        "data",
        "pagination",
        "summary",
    ]);
};

const resetFilters = () => {
    filter.value.search = "";
    filter.value.start_date = getMonthStartDate();
    filter.value.end_date = getMonthEndDate();
    filter.value.page = 1;
    applyFilters();
};
</script>

<style scoped>
:deep(.ant-table-thead > tr > th) {
    @apply !text-gray-900;
}
</style>
