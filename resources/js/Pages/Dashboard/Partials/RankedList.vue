<template>
    <a-card :bordered="false" class="rounded-xl shadow-xl overflow-hidden">
        <div class="space-y-4">
            <div v-if="title" class="mb-2">
                <div
                    class="text-lg font-semibold text-center uppercase bg-gray-100 p-4"
                >
                    {{ title }}
                </div>
            </div>

            <div class="divide-y">
                <div
                    v-for="(row, idx) in processed"
                    :key="`rank-${currentPage}-${idx}-${row[labelKey]}`"
                    class="flex items-center gap-4 py-3 px-2"
                >
                    <div
                        class="w-10 h-10 flex items-center justify-center rounded-lg text-white font-bold shrink-0"
                        :style="{
                            background: `linear-gradient(135deg, ${gradientFrom}, ${gradientTo})`,
                        }"
                    >
                        {{ startRank + idx }}
                    </div>

                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between gap-3">
                            <div class="truncate font-medium text-gray-800">
                                {{ row[labelKey] ?? "" }}
                            </div>
                            <div class="text-right">
                                <div class="text-sm text-gray-500">
                                    {{ primaryLabel }}
                                </div>
                                <div
                                    class="text-base font-semibold"
                                    :style="{ color: primaryColor }"
                                >
                                    {{ formatNumber(row[valueKey]) }}
                                </div>
                            </div>
                        </div>
                        <div class="mt-2">
                            <div
                                class="w-full h-2 bg-gray-100 rounded-full overflow-hidden"
                            >
                                <div
                                    class="h-full rounded-full"
                                    :style="{
                                        width:
                                            Math.max(
                                                0,
                                                Math.min(100, percentage(row))
                                            ) + '%',
                                        backgroundColor: primaryColor,
                                    }"
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div
                v-if="showPagination && totalPages > 1"
                class="mt-2 grid grid-cols-3 items-center"
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
import { computed, ref, watch } from "vue";

const props = defineProps({
    items: { type: Array, default: () => [] },
    labelKey: { type: String, default: "label" },
    valueKey: { type: String, default: "value" },
    title: { type: String, default: "" },
    primaryLabel: { type: String, default: "Valeur" },
    pageSize: { type: Number, default: 6 },
    showPagination: { type: Boolean, default: true },
    primaryColor: { type: String, default: "#3B82F6" },
    gradientFrom: { type: String, default: "#3B82F6" },
    gradientTo: { type: String, default: "#9333EA" },
});

const currentPage = ref(1);

const sortedItems = computed(() => {
    return [...props.items] .filter( (x) => typeof x?.[props.valueKey] === "number" && !Number.isNaN(x[props.valueKey]))
        .sort((a, b) => (b[props.valueKey] ?? 0) - (a[props.valueKey] ?? 0));
});

const totalPages = computed(() =>
    Math.max(
        1,
        Math.ceil( sortedItems.value.length / Math.max(1, Number(props.pageSize) || 1))
    )
);

const processed = computed(() => {
    const size = Math.max(1, Number(props.pageSize) || 1);
    const start = (currentPage.value - 1) * size;
    const end = start + size;
    return sortedItems.value.slice(start, end);
});

const startRank = computed(() => {
    const size = Math.max(1, Number(props.pageSize) || 1);
    return (currentPage.value - 1) * size + 1;
});

const maxValue = computed(() => {
    if (!sortedItems.value.length) return 0;
    return Math.max(...sortedItems.value.map((x) => Number(x?.[props.valueKey]) || 0));
});

const percentage = (row) => {
    const val = Number(row?.[props.valueKey]) || 0;
    if (maxValue.value <= 0) return 0;
    return (val / maxValue.value) * 100;
};

const formatNumber = (n) => {
    const num = Number(n) || 0;
    return new Intl.NumberFormat(undefined, { maximumFractionDigits: 0}).format(num);
};

watch([() => props.items, () => props.pageSize], () => {
    if (currentPage.value > totalPages.value) currentPage.value = totalPages.value;
    if (currentPage.value < 1) currentPage.value = 1;
});
</script>
