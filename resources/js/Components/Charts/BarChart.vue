<template>
    <a-card :bordered="false" class="rounded-xl shadow-xl group relative overflow-hidden">
        <div class="space-y-6">
            <!-- Titre et sous-titre -->
            <div v-if="title || subtitle" class="flex items-center justify-center mb-6">
                <div>
                    <h4 v-if="title" class="text-lg font-semibold text-gray-600 mb-1">{{ title }}</h4>
                    <p v-if="subtitle" class="text-sm text-gray-500">{{ subtitle }}</p>
                </div>
            </div>

            <!-- Chart -->
            <div class="relative" :style="{ height: props.height }">
                <Bar :data="chartData" :options="chartOptions" />
            </div>

            <!-- Pagination -->
            <div v-if="showPagination && totalPages > 1" class="mt-4 grid grid-cols-3 items-center">
                <div class="justify-self-start">
                    <a-button size="middle" shape="round" :disabled="currentPage <= 1" @click="goToPrevPage">Précédent</a-button>
                </div>
                <div class="justify-self-center flex items-center gap-1">
                    <template v-for="(p, idx) in pages" :key="`p-${idx}-${p}`">
                        <span v-if="p === '...'" class="px-2 text-gray-400 select-none">...</span>
                        <a-button v-else size="middle" shape="circle" :type="p === currentPage ? 'primary' : 'default'" @click="goToPage(p)">
                            {{ p }}
                        </a-button>
                    </template>
                </div>
                <div class="justify-self-end">
                    <a-button size="middle" shape="round" :disabled="currentPage >= totalPages" @click="goToNextPage">Suivant</a-button>
                </div>
            </div>
        </div>
    </a-card>
</template>

<script setup>
import { Bar } from "vue-chartjs";
import {
    Chart as ChartJS,
    CategoryScale,
    LinearScale,
    BarElement,
    Tooltip,
    Legend,
} from "chart.js";
import { computed, ref, watch } from "vue";

ChartJS.register(CategoryScale, LinearScale, BarElement, Tooltip, Legend);

const props = defineProps({
    items: { type: Array, default: () => [] },
    labelKey: { type: String, default: "label" },
    valueKey: { type: String, default: "value" },
    value2Key: { type: String, default: "value2" },
    top: { type: Number, default: 4 },
    pageSize: { type: Number, default: null },
    primaryColor: { type: String, default: "#172A6C" },
    secondaryColor: { type: String, default: "#10B981" },
    height: { type: String, default: "320px" },
    primaryLabel: { type: String, default: "Label 1" },
    secondaryLabel: { type: String, default: "Label 2" },
    showOthers: { type: Boolean, default: true },
    showLegend: { type: Boolean, default: false },
    mode: { type: String, default: "dual" },
    title: { type: String, default: "" },
    subtitle: { type: String, default: "" },
    showPagination: { type: Boolean, default: false },
    maxPageButtons: { type: Number, default: 7 },
    orientation: { type: String, default: "x" },
});

const effectivePageSize = computed(() => {
    const size = props.pageSize != null ? props.pageSize : props.top;
    return size > 0 ? size : 4;
});

const currentPage = ref(1);

const sortedItems = computed(() => {
    const base = Array.isArray(props.items) ? props.items : [];
    return [...base]
        .filter((x) => typeof x?.[props.valueKey] === "number" && !Number.isNaN(x[props.valueKey]))
        .sort((a, b) => (b[props.valueKey] ?? 0) - (a[props.valueKey] ?? 0));
});

const totalPages = computed(() => {
    if (!props.showPagination) return 1;
    return Math.max(1, Math.ceil(sortedItems.value.length / effectivePageSize.value));
});

const processed = computed(() => {
    if (!props.showPagination) return sortedItems.value;

    const start = (currentPage.value - 1) * effectivePageSize.value;
    const end = start + effectivePageSize.value;
    return sortedItems.value.slice(start, end);
});

const hasSecondaryData = computed(() => {
    if (props.mode !== "dual") return false;
    return processed.value.some((x) => Number(x?.[props.value2Key]) > 0);
});

const pages = computed(() => {
    const total = totalPages.value;
    const current = currentPage.value;
    const maxButtons = Math.max(3, props.maxPageButtons || 7);

    if (total <= maxButtons) return Array.from({ length: total }, (_, i) => i + 1);

    const innerSlots = maxButtons - 2;
    const half = Math.floor(innerSlots / 2);
    let start = Math.max(2, current - half);
    let end = Math.min(total - 1, current + half);
    const visibleCount = end - start + 1;

    if (visibleCount < innerSlots) {
        const remaining = innerSlots - visibleCount;
        if (start === 2) end = Math.min(total - 1, end + remaining);
        else if (end === total - 1) start = Math.max(2, start - remaining);
    }

    const range = [];
    for (let i = start; i <= end; i++) range.push(i);

    const list = [1];
    if (start > 2) list.push("...");
    list.push(...range);
    if (end < total - 1) list.push("...");
    list.push(total);
    return list;
});

// Chart.js Data
const chartData = computed(() => {
    const isHorizontal = props.orientation === "y";
    const valueAxis = isHorizontal ? "x" : "y";
    const categoryAxis = isHorizontal ? "y" : "x";

    const datasets = [
        {
            label: props.primaryLabel,
            data: processed.value.map((x) => Number(x?.[props.valueKey]) || 0),
            backgroundColor: props.primaryColor,
            borderRadius: 0,
            barThickness: 25,
            [isHorizontal ? "xAxisID" : "yAxisID"]: valueAxis,
        },
    ];

    if (hasSecondaryData.value) {
        datasets.push({
            label: props.secondaryLabel,
            data: processed.value.map((x) => Number(x?.[props.value2Key]) || 0),
            backgroundColor: props.secondaryColor,
            borderRadius: 0,
            barThickness: 25,
            [isHorizontal ? "xAxisID" : "yAxisID"]: valueAxis, // ✅ même axe que le primaire
        });
    }

    return {
        labels: processed.value.map((x) => x?.[props.labelKey] ?? ""),
        datasets,
    };
});

// Chart.js Options
const chartOptions = computed(() => {
    const isHorizontal = props.orientation === "y";
    const valueAxis = isHorizontal ? "x" : "y";
    const categoryAxis = isHorizontal ? "y" : "x";

    return {
        indexAxis: props.orientation,
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: props.showLegend, position: "top", align: "center" },
            tooltip: {
                callbacks: {
                    label: (ctx) => {
                        const value = isHorizontal ? ctx.parsed.x : ctx.parsed.y;
                        return `${ctx.dataset.label}: ${Math.max(0, Math.round(value))}`;
                    },
                },
            },
        },
        scales: {
            [categoryAxis]: { grid: { display: false, drawBorder: false } },
            [valueAxis]: { beginAtZero: true, position: isHorizontal ? "bottom" : "left", ticks: { precision: 0 }, grid: { display: false, drawBorder: false } },
        },
    };
});

// Pagination functions
function clampCurrentPage() {
    if (currentPage.value > totalPages.value) currentPage.value = totalPages.value;
    if (currentPage.value < 1) currentPage.value = 1;
}
watch([() => props.items, effectivePageSize], clampCurrentPage, { deep: true });

function goToPrevPage() { if (currentPage.value > 1) currentPage.value -= 1; }
function goToNextPage() { if (currentPage.value < totalPages.value) currentPage.value += 1; }
function goToPage(page) { if (typeof page === "number" && page >= 1 && page <= totalPages.value) currentPage.value = page; }
</script>
