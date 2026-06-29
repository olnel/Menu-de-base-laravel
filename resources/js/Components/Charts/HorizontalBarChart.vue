<template>
    <a-card
        :bordered="false"
        class="rounded-xl shadow-xl group relative overflow-hidden"
    >
        <div class="space-y-6">
            <!-- Titre -->
            <div v-if="title" class="mb-6">
                <div
                    class="text-lg font-semibold text-center uppercase bg-gray-100 p-4"
                >
                    {{ title }}
                </div>
            </div>

            <!-- Graphique -->
            <div class="relative" :style="{ height: height }">
                <Bar :data="chartData" :options="chartOptions" />
            </div>

            <!-- Pagination -->
            <div
                v-if="showPagination && totalPages > 1"
                class="mt-4 grid grid-cols-3 items-center"
            >
                <div class="justify-self-start">
                    <a-button
                        size="middle"
                        shape="round"
                        :disabled="currentPage <= 1"
                        @click="currentPage--"
                    >
                        Précédent
                    </a-button>
                </div>
                <div class="justify-self-center text-sm text-gray-500">
                    Page {{ currentPage }} / {{ totalPages }}
                </div>
                <div class="justify-self-end">
                    <a-button
                        size="middle"
                        shape="round"
                        :disabled="currentPage >= totalPages"
                        @click="currentPage++"
                    >
                        Suivant
                    </a-button>
                </div>
            </div>
        </div>
    </a-card>
</template>

<script setup>
import {
    BarElement,
    CategoryScale,
    Chart as ChartJS,
    Legend,
    LinearScale,
    Tooltip,
} from "chart.js";
import { computed, ref, watch } from "vue";
import { Bar } from "vue-chartjs";

ChartJS.register(CategoryScale, LinearScale, BarElement, Tooltip, Legend);

const props = defineProps({
    items: { type: Array, default: () => [] },
    labelKey: { type: String, default: "label" },
    valueKey: { type: String, default: "value" },
    value2Key: { type: String, default: "value2" },
    pageSize: { type: Number, default: null },
    primaryColor: { type: String, default: "#172A6C" },
    secondaryColor: { type: String, default: "#10B981" },
    height: { type: String, default: "360px" },
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

const currentPage = ref(1);
const isHorizontal = computed(() => props.orientation === "y");
const axisIdKey = computed(() => (isHorizontal.value ? "xAxisID" : "yAxisID"));
const primaryAxis = computed(() => (isHorizontal.value ? "x" : "y"));
const secondaryAxis = computed(() => (isHorizontal.value ? "x2" : "y2"));

const effectivePageSize = computed(() => {
    const size = props.pageSize ?? props.items.length;
    return Math.max(1, Number(size)) || 1;
});

const sortedItems = computed(() => {
    return [...props.items]
        .filter(
            (item) =>
                typeof item?.[props.valueKey] === "number" &&
                !Number.isNaN(item[props.valueKey])
        )
        .sort((a, b) => (b[props.valueKey] ?? 0) - (a[props.valueKey] ?? 0));
});

const totalPages = computed(() => {
    if (!props.showPagination) return 1;
    return Math.max(
        1,
        Math.ceil(sortedItems.value.length / effectivePageSize.value)
    );
});

const processed = computed(() => {
    if (!props.showPagination) return sortedItems.value;
    const start = (currentPage.value - 1) * effectivePageSize.value;
    const end = start + effectivePageSize.value;
    return sortedItems.value.slice(start, end);
});

// Détection de la présence de données exploitables
const hasData = computed(() => {
    if (!Array.isArray(processed.value) || processed.value.length === 0)
        return false;
    return processed.value.some(
        (x) =>
            Number.isFinite(Number(x?.[props.valueKey])) &&
            Number(x?.[props.valueKey]) > 0
    );
});

const hasSecondaryData = computed(() => {
    return (
        props.mode === "dual" &&
        processed.value.some((x) => Number(x?.[props.value2Key]) > 0)
    );
});

const pages = computed(() => {
    const total = totalPages.value;
    const current = currentPage.value;
    const maxButtons = Math.max(3, props.maxPageButtons);

    if (total <= maxButtons)
        return Array.from({ length: total }, (_, i) => i + 1);

    const half = Math.floor((maxButtons - 2) / 2);
    let start = Math.max(2, current - half);
    let end = Math.min(total - 1, current + half);

    if (end - start + 1 < maxButtons - 2) {
        if (start === 2)
            end = Math.min(
                total - 1,
                end + (maxButtons - 2 - (end - start + 1))
            );
        else if (end === total - 1)
            start = Math.max(2, start - (maxButtons - 2 - (end - start + 1)));
    }

    const pagesList = [1];
    if (start > 2) pagesList.push("...");
    for (let i = start; i <= end; i++) pagesList.push(i);
    if (end < total - 1) pagesList.push("...");
    pagesList.push(total);

    return pagesList;
});

const chartData = computed(() => {
    if (hasData.value) {
        const datasets = [
            {
                label: props.primaryLabel,
                data: processed.value.map(
                    (x) => Number(x?.[props.valueKey]) || 0
                ),
                backgroundColor: props.primaryColor,
                borderRadius: 0,
                barThickness: 25,
                [axisIdKey.value]: primaryAxis.value,
            },
        ];

        if (hasSecondaryData.value) {
            datasets.push({
                label: props.secondaryLabel,
                data: processed.value.map(
                    (x) => Number(x?.[props.value2Key]) || 0
                ),
                backgroundColor: props.secondaryColor,
                borderRadius: 0,
                barThickness: 25,
                [axisIdKey.value]: secondaryAxis.value,
            });
        }

        return {
            labels: processed.value.map((x) => x?.[props.labelKey] ?? ""),
            datasets,
        };
    }

    // État vide
    return {
        labels: ["Aucune donnée"],
        datasets: [
            {
                label: "Aucune donnée",
                borderRadius: 0,
                barThickness: 25,
                [axisIdKey.value]: primaryAxis.value,
            },
        ],
    };
});

const chartOptions = computed(() => {
    const valuePrimary = isHorizontal.value ? "x" : "y";
    const valueSecondary = isHorizontal.value ? "x2" : "y2";
    const category = isHorizontal.value ? "y" : "x";
    const scales = {
        [category]: { grid: { display: false, drawBorder: false } },
        [valuePrimary]: {
            beginAtZero: true,
            position: isHorizontal.value ? "bottom" : "left",
            ticks: { precision: 0 },
            grid: { display: false, drawBorder: false },
        },
    };

    if (hasSecondaryData.value) {
        scales[valueSecondary] = {
            beginAtZero: true,
            position: isHorizontal.value ? "top" : "right",
            ticks: { callback: (value) => value },
            grid: { display: false, drawBorder: false },
        };
    }

    return {
        indexAxis: props.orientation,
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: props.showLegend,
                position: "top",
                align: "center",
                labels: { padding: 20, font: { size: 12, weight: "500" } },
            },
            tooltip: {
                callbacks: {
                    label: (context) => {
                        const value = isHorizontal.value
                            ? context.parsed.x
                            : context.parsed.y;
                        return `${context.dataset.label} : ${Math.max(
                            0,
                            Math.round(value)
                        )}`;
                    },
                },
            },
        },
        scales,
    };
});
// Gestion de la pagination
watch([() => props.items, effectivePageSize], () => {
    if (currentPage.value > totalPages.value)
        currentPage.value = totalPages.value;
    if (currentPage.value < 1) currentPage.value = 1;
});
</script>
