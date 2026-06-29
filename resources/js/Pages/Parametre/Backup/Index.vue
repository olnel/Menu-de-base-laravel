<script setup>
import SousMenuPrincipale from "@/Pages/Menu/SousMenuPrincipale.vue";
import DataTable from "@/Components/DataTable.vue";
import ButtonIcon from "@/Components/ButtonIcon.vue";
import { computed, ref } from "vue";
import { router } from "@inertiajs/vue3";
import { confirm_delete } from "../../../../Utils/confirmation_modal.js";
import usePermissions from "@/UserPermissions/usePermissions.js";
import FilterBase from "@/Components/Filter/FilterBase.vue";
import { createSearchFilter, gotoSearch } from "../../../../Utils/FiltreUtils.js";
import dayjs from "dayjs";
import { message } from "ant-design-vue";

const { can } = usePermissions();
const props = defineProps({
    data: { type: Object, default: () => ({}) },
    filters: { type: Object, default: () => ({}) },
});

const filter = ref({
    ...createSearchFilter(),
    start_date: props.filters.start_date || null,
    end_date: props.filters.end_date || null,
});

const dateRange = ref(
    filter.value.start_date && filter.value.end_date
        ? [dayjs(filter.value.start_date), dayjs(filter.value.end_date)]
        : []
);

const dropdownVisible = ref(false);
const title = computed(() => `Sauvegardes du Système (${props.data?.total ?? 0})`);

const handleDateRangeChange = (dates, dateStrings) => {
    filter.value.start_date = dateStrings[0];
    filter.value.end_date = dateStrings[1];
};

const applyFilters = (data) => {
    filter.value = data;
    const url = route('backups.index');
    gotoSearch(filter.value, url);
    closeDropdown();
};

const resetFilters = () => {
    filter.value.search = "";
    filter.value.start_date = null;
    filter.value.end_date = null;
    dateRange.value = [];
    applyFilters(filter.value);
};

const closeDropdown = () => {
    dropdownVisible.value = false;
};

const columns = [
    { key: "created_at", title: "Date & Heure", width: 200 },
    { key: "filename", title: "Nom du fichier" },
    { key: "size", title: "Taille", width: 120 },
    { key: "user", title: "Par", width: 150 },
    { key: "status", title: "Statut", width: 100 },
];

const formatSize = (bytes) => {
    if (!bytes) return "0 B";
    const k = 1024;
    const sizes = ["B", "KB", "MB", "GB", "TB"];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + " " + sizes[i];
};

const handleRunBackup = () => {
    router.post(route('backups.store'), {}, {
        onBefore: () => {
            message.loading({ content: "Sauvegarde en cours...", key: 'backup_proc' });
        },
        onSuccess: () => {
            message.success({ content: "Sauvegarde terminée !", key: 'backup_proc' });
        },
        onError: () => {
            message.error({ content: "Échec de la sauvegarde", key: 'backup_proc' });
        }
    });
};

const handleDelete = (record) => {
    confirm_delete(() => {
        router.delete(route('backups.destroy', record.id), {
            preserveScroll: true,
        });
    });
};

const actions = [
    {
        text: "Télécharger",
        action: (record) => window.open(route('backups.download', record.id)),
        icon: 'fa-download',
        privilege: 'backups.download',
    },
    {
        text: "Supprimer",
        action: handleDelete,
        icon: 'fa-trash',
        class: "!text-red-600",
        privilege: 'backups.destroy'
    },
];
</script>

<template>
    <SousMenuPrincipale :title="title" selectedMenu="backup" v-if="can('backups.index')">
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

                            <div class="flex space-x-2 !mt-6">
                                <a-button
                                    block
                                    type="primary"
                                    size="middle"
                                    @click="applyFilters(filter)"
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

            <template #add>
                <ButtonIcon
                    v-if="can('backups.store')"
                    @click="handleRunBackup"
                    type="primary"
                    text="Nouvelle Sauvegarde"
                    icon="fa-database"
                />
            </template>
        </FilterBase>
        </template>

        <DataTable
            :columns="columns"
            :data="data"
            :actions="actions"
            class="main-shadow"
            :show-index="true"
            :btn_action="false"
        >
            <template #bodyCell="{ column, record }">
                <template v-if="column.key === 'created_at'">
                    {{ dayjs(record.created_at).format("DD/MM/YYYY HH:mm:ss") }}
                </template>

                <template v-if="column.key === 'filename'">
                    <span class="font-mono text-xs">{{ record.filename }}</span>
                </template>

                <template v-if="column.key === 'size'">
                    {{ formatSize(record.size) }}
                </template>

                <template v-if="column.key === 'user'">
                    {{ record.user?.name || 'Système' }}
                </template>

                <template v-if="column.key === 'status'">
                    <a-tag :color="record.status === 'success' ? 'green' : 'red'">
                        {{ record.status === 'success' ? 'Réussi' : 'Échec' }}
                    </a-tag>
                </template>
            </template>
        </DataTable>
    </SousMenuPrincipale>
</template>

<style scoped>
.font-mono {
    font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
}
</style>
