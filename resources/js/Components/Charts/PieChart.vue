<script setup>
import { Pie } from 'vue-chartjs'
import {
    Chart as ChartJS,
    Title,
    Tooltip,
    Legend,
    ArcElement
} from 'chart.js'
import ChartDataLabels from 'chartjs-plugin-datalabels'
import { computed } from "vue"

ChartJS.register(Title, Tooltip, Legend, ArcElement, ChartDataLabels)

const props = defineProps({
    element: {
        type: Object,
        required: true
    },
    size: { // largeur/hauteur
        type: Number,
        default: 300
    }
})

const chartData = computed(() => {
    return {
        labels: Object.keys(props.element.data),
        datasets: [
            {
                label: `Voyages ${props.element.annee}`,
                data: Object.values(props.element.data),
                backgroundColor: [
                    '#E63946', // rouge vif
                    '#F1FAEE', // blanc cassé
                    '#A8DADC', // turquoise clair
                    '#457B9D', // bleu moyen
                    '#1D3557', // bleu foncé
                    '#F4A261', // orange clair
                    '#E76F51', // saumon/orange foncé
                    '#2A9D8F', // vert moyen
                    '#264653', // bleu-gris foncé
                    '#FFD166', // jaune vif
                    '#06D6A0', // vert clair vif
                    '#FF6B6B'  // rouge clair
                ]
            }
        ]
    }
})

const chartOptions = {
    responsive: false, // ⚠️ désactive responsive sinon width/height ignorés
    maintainAspectRatio: false,
    plugins: {
        legend: {
            position: 'right'
        },
        title: {
            display: true,
            // text: (ctx) => `Répartition des voyages (${props.element.annee})`
            text: (ctx) => ``
        },
        datalabels: {
            color: '#fff',
            font: {weight: 'bold', size: 12},
            formatter: (value) => value > 0 ? value : ''
        }
    }
}
</script>

<template>
    <div :style="{ width: props.size + 'px', height: props.size + 'px' }">
        <Pie :data="chartData" :options="chartOptions" :width="props.size" :height="props.size"/>
    </div>
</template>
