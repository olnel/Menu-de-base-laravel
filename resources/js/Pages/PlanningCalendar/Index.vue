<script setup>
import SousMenuPrincipale from "@/Pages/Menu/SousMenuPrincipale.vue";
import ButtonIcon from "@/Components/ButtonIcon.vue";
import { computed, ref, watch } from "vue";
import { createSearchFilter, gotoSearch } from "../../../Utils/FiltreUtils.js";
import PlanningCalendarFormulaire from "@/Pages/PlanningCalendar/Formulaire/PlanningCalendarFormulaire.vue";
import DataTable from "@/Components/DataTable.vue";
import { confirm_delete } from "../../../Utils/confirmation_modal.js";
import { router } from "@inertiajs/vue3";
import FilterBase from "@/Components/Filter/FilterBase.vue";
import usePermissions from "@/UserPermissions/usePermissions.js";
import dayjs from "dayjs";
import ExcelImportBaseStandard from "@/Components/ExcelImportBaseStandard.vue";
import ExportData from "@/Components/ExportData/ExportData.vue";

const { can } = usePermissions();

// Seuil en-dessous duquel une alerte active devient "urgente"
const URGENT_DAYS = 3;

const ALERT_CLASSES = {
    overdue: 'bg-red-100 text-red-700',
    urgent:  'bg-orange-100 text-orange-700',
    warning: 'bg-amber-100 text-amber-700',
    ok:      'bg-green-100 text-green-700',
};

const ALERT_ICONS = {
    overdue: 'fas fa-triangle-exclamation',
    urgent:  'fas fa-bell',
    warning: 'fas fa-clock',
    ok:      'fas fa-check-circle',
};

/**
 * Calcule l'état d'alerte d'une maintenance.
 * Retourne null si les données sont insuffisantes.
 *
 * @param {string} dateMaintenance  Date ISO (YYYY-MM-DD)
 * @param {number} joursPreveance   Nombre de jours de prévenance configuré
 * @returns {{ status: string, label: string, daysRemaining: number } | null}
 */
function computeAlertInfo(dateMaintenance, joursPreveance) {
    if (!dateMaintenance || joursPreveance == null) return null;

    const today        = dayjs().startOf('day');
    const due          = dayjs(dateMaintenance).startOf('day');
    const daysRemaining = due.diff(today, 'day');

    if (daysRemaining < 0) {
        return { status: 'overdue', label: `Dépassé de ${Math.abs(daysRemaining)}j`, daysRemaining };
    }

    const windowStart = due.subtract(joursPreveance, 'day');
    const inWindow    = joursPreveance > 0 && today.valueOf() >= windowStart.valueOf();

    if (inWindow) {
        return {
            status: daysRemaining <= URGENT_DAYS ? 'urgent' : 'warning',
            label:  daysRemaining === 0 ? "Aujourd'hui" : `Dans ${daysRemaining}j`,
            daysRemaining,
        };
    }

    return { status: 'ok', label: `Dans ${daysRemaining}j`, daysRemaining };
}

const props = defineProps({
    data:               { type: Object, default: () => ({}) },
    filters:            { type: Object, default: () => ({}) },
    vehiculeListes:     { type: Array,  default: () => [] },
    libelleMaintenances:{ type: Array,  default: () => [] },
    flash:              { type: Object, default: () => ({}) },
    errors: Object,
});

const localLibelleMaintenance = ref([...props.libelleMaintenances]);
const formModal = ref();
const filter    = ref(createSearchFilter());

const title = computed(() => `Planning de maintenance (${props.data?.total ?? 0})`);

const rows = computed(() => props.data?.data ?? []);

const alertCounts = computed(() => {
    const counts = { overdue: 0, urgent: 0, warning: 0 };
    rows.value.forEach(r => {
        const info = computeAlertInfo(r.date_maintenance, r.notification);
        if (info && info.status in counts) counts[info.status]++;
    });
    return counts;
});

const hasAlerts = computed(() => Object.values(alertCounts.value).some(n => n > 0));

const columns = [
    { key: "date_maintenance", title: "Date",               width: '120px', dataIndex: "date_maintenance", customCell: () => ({ class: 'text-center' }) },
    { key: "immatriculation",  title: "N° immatriculation", width: '170px', dataIndex: "immatriculation",  customCell: () => ({ class: 'text-center' }) },
    { key: "libelle",          title: "Libellé",                             dataIndex: "libelle" },
    { key: "background",       title: "Couleur fond",                        dataIndex: "background" },
    { key: "text_color",       title: "Couleur texte",                       dataIndex: "text_color" },
    { key: "notification",     title: "Prévenance / Alerte",                 dataIndex: "notification", customCell: () => ({ class: 'text-center' }) },
];

const handleDelete = (record) => {
    confirm_delete(() => {
        router.delete(route('planning_calendar.destroy', record.id), { preserveScroll: true });
    });
};

const actions = [
    { text: "Modification", action: (record) => formModal.value.update(record), icon: 'fa-pen-to-square', privilege: 'planning_calendar.update' },
    { text: "Supprimer",    action: handleDelete, class: "!text-red-600", icon: 'fa-trash', disabled: (record) => record.is_you || props.data.total < 1, privilege: 'planning_calendar.destroy' },
];

const applyFilters = (data) => {
    filter.value = data;
    gotoSearch(filter.value, route('planning_calendar.index'));
};

const resetFilters = () => {
    filter.value.search = "";
    applyFilters();
};

watch(() => props.libelleMaintenances, (newVal) => {
    localLibelleMaintenance.value = newVal;
}, { deep: true });
</script>

<template>
    <SousMenuPrincipale :title="title" selectedMenu="calendar-planning">
        <template #top>
            <FilterBase v-model="props.filters" @search="applyFilters" @reset="resetFilters">
            <template #add>
                <ButtonIcon
                    v-if="can('planning_calendar.store')"
                    @click="() => formModal.add()"
                    type="primary"
                    text="Nouveau"
                    icon="fa-plus"
                />
            </template>
            <template #import v-if="can('export_planning_calendar.export')">
                <ExportData :show_import="false" title="Export data" :columns="columns" :filter="filter" :url="route('planning_calendar.export')">
                    <template #import>
                        <excel-import-base-standard model="test" :columns="columns" />
                    </template>
                </ExportData>
            </template>
        </FilterBase>
        </template>

        <!-- ── Bandeau d'alertes de prévenance ──────────────────────────── -->
        <div v-if="hasAlerts" class="mt-4 flex flex-wrap gap-3">
            <div
                v-if="alertCounts.overdue"
                class="flex items-center gap-2 rounded-lg bg-red-50 border border-red-200 px-4 py-2.5 text-sm"
            >
                <font-awesome-icon icon="fas fa-triangle-exclamation" class="text-red-500" />
                <span class="font-semibold text-red-700">
                    {{ alertCounts.overdue }}
                    maintenance{{ alertCounts.overdue > 1 ? 's' : '' }}
                    dépassée{{ alertCounts.overdue > 1 ? 's' : '' }}
                </span>
            </div>

            <div
                v-if="alertCounts.urgent"
                class="flex items-center gap-2 rounded-lg bg-orange-50 border border-orange-200 px-4 py-2.5 text-sm"
            >
                <font-awesome-icon icon="fas fa-bell" class="text-orange-500" />
                <span class="font-semibold text-orange-700">
                    {{ alertCounts.urgent }}
                    urgente{{ alertCounts.urgent > 1 ? 's' : '' }}
                    — échéance dans ≤ {{ URGENT_DAYS }} jours
                </span>
            </div>

            <div
                v-if="alertCounts.warning"
                class="flex items-center gap-2 rounded-lg bg-amber-50 border border-amber-200 px-4 py-2.5 text-sm"
            >
                <font-awesome-icon icon="fas fa-clock" class="text-amber-500" />
                <span class="font-semibold text-amber-700">
                    {{ alertCounts.warning }}
                    dans la fenêtre de prévenance
                </span>
            </div>
        </div>

        <!-- ── Tableau ───────────────────────────────────────────────────── -->
        <DataTable
            :columns="columns"
            :data="data"
            :actions="actions"
            class="custom-table main-shadow"
            :show-index="true"
            :btn_action="false"
        >
            <template #bodyCell="{ column, record }">

                <!-- Date : colorée selon l'état d'alerte -->
                <template v-if="column.key === 'date_maintenance'">
                    <span
                        class="font-semibold"
                        :class="{
                            'text-red-600':   ['overdue', 'urgent'].includes(computeAlertInfo(record.date_maintenance, record.notification)?.status),
                            'text-amber-600': computeAlertInfo(record.date_maintenance, record.notification)?.status === 'warning',
                            'text-gray-700':  computeAlertInfo(record.date_maintenance, record.notification)?.status === 'ok',
                        }"
                    >
                        {{ record.date_maintenance ? dayjs(record.date_maintenance).format('DD/MM/YYYY') : '—' }}
                    </span>
                </template>

                <!-- Couleur de fond -->
                <template v-if="column.key === 'background'">
                    <div class="flex items-center gap-2">
                        <div class="w-6 h-6 rounded shadow border flex-shrink-0" :style="{ backgroundColor: record.background }"></div>
                        <span class="text-xs text-gray-600">{{ record.background }}</span>
                    </div>
                </template>

                <!-- Couleur de texte -->
                <template v-if="column.key === 'text_color'">
                    <div class="flex items-center gap-2">
                        <div class="w-6 h-6 rounded shadow border flex-shrink-0" :style="{ backgroundColor: record.text_color }"></div>
                        <span class="text-xs text-gray-600">{{ record.text_color }}</span>
                    </div>
                </template>

                <!-- Prévenance / Alerte : jours configurés + badge d'état -->
                <template v-if="column.key === 'notification'">
                    <div class="flex flex-col items-center gap-1">
                        <span class="text-xs text-gray-400">
                            {{ record.notification ?? 0 }} j prévenance
                        </span>
                        <span
                            v-if="computeAlertInfo(record.date_maintenance, record.notification)"
                            class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-semibold"
                            :class="ALERT_CLASSES[computeAlertInfo(record.date_maintenance, record.notification).status]"
                        >
                            <font-awesome-icon
                                :icon="ALERT_ICONS[computeAlertInfo(record.date_maintenance, record.notification).status]"
                                class="text-[10px]"
                            />
                            {{ computeAlertInfo(record.date_maintenance, record.notification).label }}
                        </span>
                    </div>
                </template>

            </template>
        </DataTable>

        <PlanningCalendarFormulaire
            :vehicule-listes="props.vehiculeListes"
            :libelle-maintenance="localLibelleMaintenance"
            ref="formModal"
        />

    </SousMenuPrincipale>
</template>

<style scoped>
/* Styles spécifiques si nécessaires */
</style>
