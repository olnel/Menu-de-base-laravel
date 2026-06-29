<script setup>
import { computed } from "vue";

const props = defineProps({
    title: String,
    items: Array,
    icon: String,
    columns: { type: Array, default: () => [] },
    rowKey: { type: String, default: "id" },
});



// Utiliser les colonnes fournies ou les colonnes par défaut
const tableColumns = computed(() => {
    return props.columns.length > 0 ? props.columns : [];
});

// Transformer les données des items pour correspondre au format attendu
const tableData = computed(() => {
    if (!props.items || !Array.isArray(props.items)) return [];

    return props.items.map((item, index) => ({
        ...item,
        rank: index + 1,
        key: item.id || item.tabel || index,
    }));
});
</script>

<template>
    <a-card :bordered="false" class="bg-white main-rounded main-shadow w-full">
        <div class="flex items-center gap-3 mb-4">
            <font-awesome-icon :icon="icon" class="w-6 h-6 text-yellow-500" />
            <h2 class="text-xl font-bold">{{ title }}</h2>
        </div>

        <a-table
            :columns="tableColumns"
            :data-source="tableData"
            :pagination="false"
            :bordered="false"
            size="small"
            :row-key="rowKey"
            class="leaderboard-table"
        >
            <template #bodyCell="{ column, record, text, index }">
                <slot
                    name="bodyCell"
                    :column="column"
                    :record="record"
                    :text="text"
                    :index="index"
                />

                <!-- Colonne Rang par défaut -->
                <template v-if="column.key === 'rank'">
                    <span
                        :class="`w-8 h-8 flex items-center justify-center rounded-full ${
                            record.rank === 1
                                ? 'bg-yellow-500'
                                : record.rank === 2
                                ? 'bg-gray-400'
                                : record.rank === 3
                                ? 'bg-amber-600'
                                : 'bg-gray-200'
                        } text-white font-bold text-sm`"
                    >
                        {{ record.rank }}
                    </span>
                </template>

                <!-- Colonne Label par défaut -->
                <template v-else-if="column.key === 'label'">
                    <span class="font-medium">{{ record.label }}</span>
                </template>

                <!-- Colonne Value par défaut -->
                <template v-else-if="column.key === 'value'">
                    <span class="font-semibold text-primary">{{
                        record.value
                    }}</span>
                </template>

                <!-- Colonne Description par défaut -->
                <template v-else-if="column.key === 'desc'">
                    <span class="text-sm text-text-primary/70">{{
                        record.desc
                    }}</span>
                </template>
            </template>
        </a-table>
    </a-card>
</template>

<style scoped>
.leaderboard-table :deep(.ant-table-thead > tr > th) {
    background-color: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
    font-weight: 600;
    color: #374151;
}

.leaderboard-table :deep(.ant-table-tbody > tr > td) {
    border-bottom: 1px solid #f1f5f9;
    padding: 12px 16px;
}

.leaderboard-table :deep(.ant-table-tbody > tr:hover > td) {
    background-color: #f8fafc;
}

.leaderboard-table :deep(.ant-table-tbody > tr:last-child > td) {
    border-bottom: none;
}
</style>
