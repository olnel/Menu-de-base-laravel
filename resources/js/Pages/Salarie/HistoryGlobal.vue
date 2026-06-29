<script setup>
import DataTable from "@/Components/DataTable.vue";
import FilterBase from "@/Components/Filter/FilterBase.vue";
import SousMenuPrincipale from "@/Pages/Menu/SousMenuPrincipale.vue";
import usePermissions from "@/UserPermissions/usePermissions.js";
import { router } from "@inertiajs/vue3";
import { computed, ref } from "vue";
import { createSearchFilter, gotoSearch } from "../../../Utils/FiltreUtils.js";
import { DownOutlined } from "@ant-design/icons-vue";
import { Space } from "ant-design-vue";

const { can } = usePermissions();
const props = defineProps({
    data: { type: Object, default: () => ({}) },
    filters: { type: Object, default: () => ({}) },
    actions_list: { type: Array, default: () => [] },
});

const dropdownVisible = ref(false);
const filter = ref({
    ...createSearchFilter(),
    search: props.filters.search || "",
    action: props.filters.action || null,
    date_start: props.filters.date_start || null,
    date_end: props.filters.date_end || null,
});

const title = "Historique Global du Personnel";

const columns = [
    {
        key: "created_at",
        title: "Date & Heure",
        dataIndex: "created_at",
        customRender: ({ text }) => new Date(text).toLocaleString(),
        width: "180px",
    },
    {
        key: "action",
        title: "Action",
        dataIndex: "action",
        width: "150px",
    },
    {
        key: "salarie",
        title: "Salarié concerné",
        dataIndex: ["salarie", "nom"],
        customRender: ({ record }) =>
            record.salarie
                ? `${record.salarie.nom} ${record.salarie.prenom ?? ""} (${record.salarie.matricule})`
                : "Salarié supprimé définitivement",
        width: "250px",
    },
    {
        key: "user",
        title: "Effectué par",
        dataIndex: ["user", "name"],
        width: "150px",
    },
    {
        key: "details",
        title: "Détails des modifications",
    },
];

const applyFilters = (data) => {
    filter.value = data;
    const url = route("salarie.history_global");
    gotoSearch(filter.value, url);
};

const resetFilters = () => {
    filter.value = {
        ...createSearchFilter(),
        action: null,
        date_start: null,
        date_end: null,
    };
    applyFilters(filter.value);
};

const handlePageChange = (pag) => {
    router.get(
        route("salarie.history_global"),
        {
            ...filter.value,
            page: pag.current,
        },
        {
            preserveState: true,
            preserveScroll: true,
        },
    );
};
</script>

<template>
    <SousMenuPrincipale :title="title" selectedMenu="salarie_history" v-if="can('salarie.history_global')">
        <template #top>
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
                            <div class="bg-white p-4 w-80 space-y-4 rounded-md">
                                <div class="space-y-1">
                                    <label
                                        class="text-xs font-bold text-gray-400 uppercase"
                                        >Action</label
                                    >
                                    <a-select
                                        v-model:value="filter.action"
                                        placeholder="Filtrer par action"
                                        class="w-full"
                                        size="large"
                                        allow-clear
                                    >
                                        <a-select-option
                                            v-for="action in actions_list"
                                            :key="action"
                                            :value="action"
                                        >
                                            {{ action }}
                                        </a-select-option>
                                    </a-select>
                                </div>

                                <div class="space-y-1">
                                    <label
                                        class="text-xs font-bold text-gray-400 uppercase"
                                        >Période</label
                                    >
                                    <div class="grid grid-cols-1 gap-2">
                                        <a-date-picker
                                            v-model:value="filter.date_start"
                                            placeholder="Date début"
                                            value-format="YYYY-MM-DD"
                                            size="large"
                                            class="w-full"
                                        />
                                        <a-date-picker
                                            v-model:value="filter.date_end"
                                            placeholder="Date fin"
                                            value-format="YYYY-MM-DD"
                                            size="large"
                                            class="w-full"
                                        />
                                    </div>
                                </div>

                                <div class="flex space-x-2 !mt-6">
                                    <a-button
                                        block
                                        type="primary"
                                        size="middle"
                                        @click="applyFilters(filter)"
                                        >Appliquer</a-button
                                    >
                                    <a-button
                                        block
                                        type="default"
                                        size="middle"
                                        @click="dropdownVisible = false"
                                        >Fermer</a-button
                                    >
                                </div>
                            </div>
                        </template>
                        <a-button
                            size="large"
                            type="default"
                            class="!rounded-none border-r-0 focus:z-10"
                        >
                            <Space>
                                <font-awesome-icon
                                    class="text-[15px]"
                                    icon="fa-filter"
                                />
                                Filtres
                                <DownOutlined />
                            </Space>
                        </a-button>
                    </a-popover>
                </template>
            </FilterBase>
        </template>

        <DataTable
            :columns="columns"
            :data="data"
            class="main-shadow"
            :show-index="true"
            :btn_action="false"
            @page-change="handlePageChange"
        >
            <template #bodyCell="{ column, record }">
                <template v-if="column.key === 'action'">
                    <a-tag
                        :color="
                            record.action === 'Suppression'
                                ? 'red'
                                : record.action === 'Enregistrement'
                                  ? 'green'
                                  : 'blue'
                        "
                    >
                        {{ record.action }}
                    </a-tag>
                </template>
                <template v-if="column.key === 'details'">
                    <div
                        v-if="record.action === 'Modification'"
                        class="text-[11px] leading-tight py-1"
                    >
                        <div
                            v-for="(val, key) in record.new_values"
                            :key="key"
                            class="mb-1"
                        >
                            <span class="font-semibold text-gray-600 uppercase"
                                >{{ key }} :</span
                            >
                            <span
                                class="text-red-400 line-through mx-1"
                                v-if="
                                    record.old_values && record.old_values[key]
                                "
                            >
                                {{ record.old_values[key] }}
                            </span>
                            <font-awesome-icon
                                icon="fa-solid fa-arrow-right"
                                class="text-[9px] text-gray-400 mx-1"
                            />
                            <span class="text-green-600 font-medium">{{
                                val
                            }}</span>
                        </div>
                    </div>
                    <div
                        v-else-if="
                            record.action === 'Suppression' && record.old_values
                        "
                        class="text-[11px] text-gray-400 italic"
                    >
                        Données archivées (Soft Delete)
                    </div>
                    <div v-else class="text-[11px] text-gray-400">-</div>
                </template>
            </template>
        </DataTable>
    </SousMenuPrincipale>
</template>

<style scoped>
:deep(.ant-input-search-button) {
    @apply bg-primary border-primary;
}
</style>
