<script setup>
import { ArcElement, Chart as ChartJS, Legend, Title, Tooltip } from "chart.js";
import ChartDataLabels from "chartjs-plugin-datalabels";
import { computed } from "vue";
import { Pie } from "vue-chartjs";

ChartJS.register(Title, Tooltip, Legend, ArcElement, ChartDataLabels);

const props = defineProps({
    element: {
        type: Object,
        required: false,
        default: () => ({ data: {}, annee: "" }),
    },
    title: {
        type: String,
        default: "",
    },
    titleColorClass: {
        type: String,
        default: "text-gray-600",
    },
    height: {
        type: Number,
        default: 360,
    },
    colors: {
        type: Array,
        default: () => [
            "#3B82F6", // blue
            "#9333EA", // purple
            "#FACC15", // yellow
            "#F43F5E", // rose/red
            "#2A9D8F", // teal
            "#457B9D", // steel blue
            "#06D6A0", // green
            "#FF6B6B", // light red
            "#264653", // dark blue-gray
            "#E76F51", // orange
            "#9FBB95", // muted green
            "#79E4E7", // light turquoise
        ],
    },
});

const hasData = computed(() => {
    return (
        props.element &&
        props.element.data &&
        Object.keys(props.element.data).length > 0
    );
});

const chartData = computed(() => {
    if (hasData.value) {
        const labels = Object.keys(props.element.data);
        const values = Object.values(props.element.data);
        const palette =
            Array.isArray(props.colors) && props.colors.length
                ? props.colors
                : [
                      "#FF617C",
                      "#9FBB95",
                      "#79E4E7",
                      "#457B9D",
                      "#1D3557",
                      "#F4A261",
                      "#E76F51",
                      "#2A9D8F",
                      "#264653",
                      "#FFD166",
                      "#06D6A0",
                      "#FF6B6B",
                  ];
        return {
            labels,
            datasets: [
                {
                    label: props.element?.annee
                        ? `Répartition ${props.element.annee}`
                        : "Répartition",
                    data: values,
                    backgroundColor: labels.map(
                        (_, i) => palette[i % palette.length]
                    ),
                },
            ],
        };
    } else {
        // État vide
        return {
            labels: ["Aucune donnée"],
            datasets: [
                {
                    label: "Aucune donnée",
                    data: [1],
                    backgroundColor: ["#F1F1F1"], // Gris clair
                    hoverBackgroundColor: ["#F1F1F1"],
                },
            ],
        };
    }
});

const chartOptions = computed(() => ({
    responsive: true,
    maintainAspectRatio: false,
    cutout: "50%",
    plugins: {
        legend: {
            position: "right",
            display: true,
        },
        tooltip: {
            enabled: true,
        },

        title: {
            display: false,
            text: "",
        },
        datalabels: {
            color: "#fff",
            font: { weight: "bold", size: 20 },
            formatter: (value) => (value > 0 && hasData.value ? value : ""),
        },
    },
}));
</script>

<template>
    <div class="w-full">
        <div class="w-full h-full bg-white rounded-xl p-4 sm:p-6 flex flex-col">
            <div
                v-if="title"
                :class="[
                    'text-lg font-semibold text-center uppercase bg-gray-100  p-4 mb-4',
                    titleColorClass,
                ]"
            >
                {{ title }}
            </div>
            <div class="flex-1">
                <div class="h-full" :style="{ height: height + 'px' }">
                    <Pie :data="chartData" :options="chartOptions" />
                </div>
            </div>
        </div>
    </div>
</template>
