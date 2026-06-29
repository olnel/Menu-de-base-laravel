<template>
    <SousMenuPrincipale :title="title" selectedMenu="activity_log" v-if="can('activity_log.index')">
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
                        <div class="bg-white p-4 w-96 space-y-2 rounded-md">
                            <a-range-picker
                                v-model:value="dateRange"
                                format="YYYY-MM-DD"
                                value-format="YYYY-MM-DD"
                                class="w-full"
                                size="large"
                                :placeholder="['Date de début', 'Date de fin']"
                                @change="handleDateRangeChange"
                            />

                            <a-select
                                v-model:value="filter.action"
                                placeholder="Type d'action"
                                class="w-full"
                                size="large"
                                allow-clear
                            >
                                <a-select-option
                                    v-for="act in actions"
                                    :key="act"
                                    :value="act"
                                    :label="act"
                                >
                                    {{ act }}
                                </a-select-option>
                            </a-select>

                            <a-select
                                v-model:value="filter.module"
                                placeholder="Module"
                                class="w-full"
                                size="large"
                                allow-clear
                            >
                                <a-select-option
                                    v-for="mod in modules"
                                    :key="mod"
                                    :value="mod"
                                    :label="mod"
                                >
                                    {{ mod }}
                                </a-select-option>
                            </a-select>

                            <a-select
                                v-model:value="filter.user_id"
                                placeholder="Utilisateur"
                                class="w-full"
                                size="large"
                                allow-clear
                                show-search
                                :filter-option="
                                    (input, option) =>
                                        option.label
                                            .toLowerCase()
                                            .includes(input.toLowerCase())
                                "
                                option-filter-prop="label"
                            >
                                <a-select-option
                                    v-for="user in users"
                                    :key="user.value"
                                    :value="user.value"
                                    :label="user.label"
                                >
                                    {{ user.label }}
                                </a-select-option>
                            </a-select>

                            <div class="flex space-x-2 !mt-6">
                                <a-button
                                    block
                                    type="primary"
                                    size="middle"
                                    @click="applyFilters()"
                                >
                                    Appliquer
                                </a-button>
                                <a-button
                                    block
                                    type="default"
                                    size="middle"
                                    @click="closeDropdown"
                                >
                                    Fermer
                                </a-button>
                            </div>
                        </div>
                    </template>

                    <a-button
                        size="large"
                        type="default"
                        class="!rounded-none border-r-0 focus:z-10"
                    >
                        <a-space>
                            <font-awesome-icon
                                class="text-[15px]"
                                icon="fa-filter"
                            />
                            Filtres
                            <DownOutlined />
                        </a-space>
                    </a-button>
                </a-popover>
            </template>
        </FilterBase>
        </template>

        <BaseDataTable
            :columns="columns"
            :data="data"
            :actions="rowActions"
            class="main-shadow"
            :show-index="true"
            :btn_action="false"
        >
            <template #bodyCell="{ column, record }">
                <template v-if="column.key === 'created_at'">
                    {{ formatDate(record.created_at) }}
                </template>

                <template v-if="column.key === 'action'">
                    <a-tag :color="actionColor(record.action)">
                        {{ record.action }}
                    </a-tag>
                </template>

                <template v-if="column.key === 'module'">
                    <span class="text-gray-700">{{ record.module || "-" }}</span>
                </template>

                <template v-if="column.key === 'description'">
                    <span class="text-gray-800">
                        {{ record.description || "-" }}
                    </span>
                </template>

                <template v-if="column.key === 'subject'">
                    <span class="text-xs text-gray-500" v-if="record.subject_type">
                        {{ shortClass(record.subject_type) }}
                        <template v-if="record.subject_id">
                            #{{ record.subject_id }}
                        </template>
                    </span>
                    <span v-else>-</span>
                </template>
            </template>
        </BaseDataTable>

        <a-modal
            v-model:open="detailVisible"
            :title="`Détail du log #${detailRecord?.id ?? ''}`"
            :footer="null"
            width="720px"
        >
            <div v-if="detailRecord" class="space-y-3 text-sm">
                <div class="grid grid-cols-2 gap-2">
                    <div><strong>Utilisateur :</strong> {{ detailRecord.user_name || '-' }}</div>
                    <div><strong>Email :</strong> {{ detailRecord.user_email || '-' }}</div>
                    <div><strong>Action :</strong> {{ detailRecord.action }}</div>
                    <div><strong>Module :</strong> {{ detailRecord.module || '-' }}</div>
                    <div><strong>IP :</strong> {{ detailRecord.ip_address || '-' }}</div>
                    <div><strong>Méthode :</strong> {{ detailRecord.method || '-' }}</div>
                    <div class="col-span-2"><strong>URL :</strong> <span class="break-all">{{ detailRecord.url || '-' }}</span></div>
                    <div class="col-span-2"><strong>Route :</strong> {{ detailRecord.route_name || '-' }}</div>
                    <div class="col-span-2"><strong>User-Agent :</strong> <span class="break-all text-xs text-gray-500">{{ detailRecord.user_agent || '-' }}</span></div>
                    <div class="col-span-2"><strong>Cible :</strong> {{ shortClass(detailRecord.subject_type) }} <template v-if="detailRecord.subject_id">#{{ detailRecord.subject_id }}</template></div>
                </div>

                <div v-if="detailRecord.old_values || detailRecord.new_values">
                    <p class="font-semibold mb-2">Modifications</p>
                    <table class="w-full text-xs border border-gray-200 rounded overflow-hidden">
                        <thead>
                            <tr class="bg-gray-100 text-gray-600">
                                <th class="px-3 py-2 text-left font-medium w-1/3">Champ</th>
                                <th class="px-3 py-2 text-left font-medium w-1/3 text-red-500">Ancienne valeur</th>
                                <th class="px-3 py-2 text-left font-medium w-1/3 text-green-600">Nouvelle valeur</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <tr v-for="(_, key) in mergedKeys(detailRecord)" :key="key" class="hover:bg-gray-50">
                                <td class="px-3 py-2 font-medium text-gray-700">{{ key }}</td>
                                <td class="px-3 py-2 text-red-600">
                                    {{ detailRecord.old_values?.[key] ?? '—' }}
                                </td>
                                <td class="px-3 py-2 text-green-700">
                                    {{ detailRecord.new_values?.[key] ?? '—' }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </a-modal>
    </SousMenuPrincipale>
</template>

<script setup>
import SousMenuPrincipale from "@/Pages/Menu/SousMenuPrincipale.vue";
import BaseDataTable from "@/Components/DataTable.vue";
import FilterBase from "@/Components/Filter/FilterBase.vue";
import usePermissions from "@/UserPermissions/usePermissions.js";
import { DownOutlined } from "@ant-design/icons-vue";
import dayjs from "dayjs";
import { computed, ref } from "vue";
import { createSearchFilter, gotoSearch } from "../../../Utils/FiltreUtils.js";

const props = defineProps({
    data:    { type: Object, default: () => ({}) },
    modules: { type: Array,  default: () => [] },
    actions: { type: Array,  default: () => [] },
    users:   { type: Array,  default: () => [] },
    filters: { type: Object, default: () => ({}) },
});

const { can } = usePermissions();

const mergedKeys = (record) => {
    const keys = new Set([
        ...Object.keys(record.old_values ?? {}),
        ...Object.keys(record.new_values ?? {}),
    ]);
    return Object.fromEntries([...keys].map((k) => [k, true]));
};

const title = computed(
    () => `Journal des actions utilisateurs (${props.data?.total ?? 0})`
);

const dropdownVisible = ref(false);
const detailVisible = ref(false);
const detailRecord = ref(null);

const filter = ref({
    ...createSearchFilter(),
    action:     props.filters.action     || null,
    module:     props.filters.module     || null,
    user_id:    props.filters.user_id    || null,
    start_date: props.filters.start_date || null,
    end_date:   props.filters.end_date   || null,
});

const dateRange = ref(
    filter.value.start_date && filter.value.end_date
        ? [dayjs(filter.value.start_date), dayjs(filter.value.end_date)]
        : []
);

const handleDateRangeChange = (dates, dateStrings) => {
    filter.value.start_date = dateStrings[0];
    filter.value.end_date   = dateStrings[1];
};

const applyFilters = () => {
    const url = route("activity_log.index");
    gotoSearch(filter.value, url);
    closeDropdown();
};

const resetFilters = () => {
    filter.value = {
        ...createSearchFilter(),
        action: null,
        module: null,
        user_id: null,
        start_date: null,
        end_date: null,
    };
    dateRange.value = [];
    applyFilters();
};

const closeDropdown = () => {
    dropdownVisible.value = false;
};

const formatDate = (value) =>
    value ? dayjs(value).format("DD/MM/YYYY HH:mm:ss") : "-";

const shortClass = (type) =>
    type ? type.split("\\").pop() : "-";

const actionColor = (action) => {
    switch (action) {
        case "created":       return "green";
        case "updated":       return "gold";
        case "deleted":       return "red";
        case "login":         return "blue";
        case "logout":        return "default";
        case "login_failed":  return "volcano";
        case "exported":      return "geekblue";
        case "printed":       return "cyan";
        case "mail_sent":     return "purple";
        default:              return "default";
    }
};

const showDetail = (record) => {
    detailRecord.value = record;
    detailVisible.value = true;
};

const columns = [
    {
        key: "created_at",
        title: "Date",
        dataIndex: "created_at",
        width: 160,
        customCell: () => ({ class: "!text-center whitespace-nowrap" }),
        customHeaderCell: () => ({ class: "!text-center whitespace-nowrap" }),
    },
    {
        key: "user_name",
        title: "Utilisateur",
        dataIndex: "user_name",
        width: 160,
        customCell: () => ({ class: "!text-left" }),
        customHeaderCell: () => ({ class: "!text-left whitespace-nowrap" }),
    },
    {
        key: "action",
        title: "Action",
        dataIndex: "action",
        width: 120,
        customCell: () => ({ class: "!text-center" }),
        customHeaderCell: () => ({ class: "!text-center whitespace-nowrap" }),
    },
    {
        key: "module",
        title: "Module",
        dataIndex: "module",
        width: 140,
        customCell: () => ({ class: "!text-center" }),
        customHeaderCell: () => ({ class: "!text-center whitespace-nowrap" }),
    },
    {
        key: "description",
        title: "Description",
        dataIndex: "description",
        customHeaderCell: () => ({ class: "!text-left whitespace-nowrap" }),
    },
    {
        key: "subject",
        title: "Cible",
        width: 180,
        customCell: () => ({ class: "!text-center" }),
        customHeaderCell: () => ({ class: "!text-center whitespace-nowrap" }),
    },
    {
        key: "ip_address",
        title: "IP",
        dataIndex: "ip_address",
        width: 130,
        customCell: () => ({ class: "!text-center" }),
        customHeaderCell: () => ({ class: "!text-center whitespace-nowrap" }),
    },
];

const rowActions = [
    {
        text: "Détails",
        action: showDetail,
        icon: "fa-eye",
        privilege: "activity_log.index",
    },
];
</script>

<style scoped></style>
