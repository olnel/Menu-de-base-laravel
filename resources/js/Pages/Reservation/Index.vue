<template>
    <SousMenuPrincipale
        :title="title"
        selectedMenu="reservation"

    >
        <template #top>
            <FilterBase
            v-model="filter"
            @search="applyFilters"
            @reset="resetFilters"
        >
            <template #import v-if="can('export_reservation.export')">
                <ExportData :show_import="false" :title="'Export data'" :columns="columns" :filter="filter" :url="route('reservation.export')" >
                    <template #import >
                        <excel-import-base-standard :columns="columns" model="test"/>
                    </template>
                </ExportData>
            </template>
            <template #add>
                <div class="flex gap-2 items-center justify-center">
                    <a-segmented
                        v-model:value="currentView"
                        :options="[
                            { label: 'Tableau', value: 'table' },
                            { label: 'Calendrier', value: 'calendar' },
                        ]"
                        size="large"
                        class="switch-segmented"
                    />
                    <ButtonIcon
                        v-if="
                            can('reservation.store') && currentView === 'table'
                        "
                        @click="() => formModal.add()"
                        type="primary"
                        text="Nouvelle Reservation"
                        icon="fa-plus"
                    />
                </div>
            </template>
            <template #otherFilter>
                <div class="flex items-center space-x-2">
                    <a-popover
                        placement="bottomRight"
                        trigger="click"
                        :visible="dropdownVisible"
                        @visibleChange="(val) => (dropdownVisible = val)"
                    >
                        <template #content>
                            <div class="bg-white p-4 w-96 space-y-2 rounded-md">
                                <a-select
                                    v-model:value="filter.client_id"
                                    placeholder="Client"
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
                                        v-for="client in clients"
                                        :key="client.value"
                                        :value="client.value"
                                        :label="client.label"
                                    >
                                        {{ client.label }}
                                    </a-select-option>
                                </a-select>

                                <div class="flex space-x-2 !mt-6">
                                    <a-button
                                        block
                                        type="primary"
                                        size="middle"
                                        @click="applyFilters()"
                                        >Appliquer</a-button
                                    >
                                    <a-button
                                        block
                                        type="default"
                                        size="middle"
                                        @click="closeDropdown"
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
                </div>
            </template>
        </FilterBase>
        </template>

        <Transition name="slide-right" mode="out-in">
            <div v-if="currentView === 'table'" key="table-view">
                <DataTable
                    :columns="columns"
                    :data="props.data"
                    :actions="actions"
                    class="main-shadow"
                    :show-index="true"
                    :btn_action="true"
                    rowKey="id"
                >
                    <template #bodyCell="{ column, record }">
                        <template v-if="column.key === 'date_reservation'">
                            {{
                                dayjs(record.date_reservation).format(
                                    "DD/MM/YYYY"
                                ) ?? "-"
                            }}
                        </template>
                        <template v-if="column.key === 'numero_reservation'">
                            {{ record.numero_reservation ?? "-" }}
                        </template>
                        <template v-if="column.key === 'client'">
                            {{ record.nom_client ?? "-" }}
                        </template>
                        <template v-if="column.key === 'lieu_chargement'">
                            {{ record.lieu_chargement ?? "-" }}
                        </template>
                        <template v-if="column.key === 'lieu_livraison'">
                            {{ record.lieu_livraison ?? "-" }}
                        </template>
                        <template v-if="column.key === 'commentaire'">
                            {{ record.commentaire ?? "-" }}
                        </template>
                        <template v-if="column.key === 'user'">
                            {{ record?.user?.name ?? "-" }}
                        </template>
                        <template v-if="column.key === 'nbr_vehicule'">{{
                            record?.nbr_vehicule
                        }}</template>
                        <template v-if="column.key === 'etat'">{{
                            record?.etat_facture === "valide"
                                ? "valide"
                                : "non valide"
                        }}</template>
                    </template>
                </DataTable>
            </div>

            <div v-else-if="currentView === 'calendar'" key="calendar-view">
                <ReservationsCalendar
                    :events="calendarEvents"
                    :current-date="calendarCurrentDate"
                    :is-loading="isCalendarLoading"
                    :is-navigating="isCalendarNavigating"
                    :filter-date="calendarFilterDate"
                    :can-update="can('reservation.update')"
                    :can-delete="can('reservation.destroy')"
                    :can-view-details="can('reservation.details')"
                    @date-navigate="handleCalendarDateNavigate"
                    @date-filter-change="handleCalendarDateFilterChange"
                    @go-to-today="handleCalendarGoToToday"
                    @add-event="handleCalendarAddEvent"
                    @edit-event="handleCalendarEditEvent"
                    @delete-event="handleCalendarDeleteEvent"
                    @view-details="handleCalendarViewDetails"
                />
            </div>
        </Transition>

        <FormulaireReservation
            ref="formModal"
            :clients="clients"
            :vehicules="vehicules"
            :lieux_chargement="props.lieux_chargement"
            :lieux_livraison="props.lieux_livraison"
            :flash="props.flash"
        />
        <VoyageForm
            ref="voyageDetailsFormModal"
            :vehicules="vehicules"
            :chauffeurs="chauffeurs"
        />
    </SousMenuPrincipale>
</template>
<script setup>
import { confirm_delete } from "@/../Utils/confirmation_modal.js";
import ButtonIcon from "@/Components/ButtonIcon.vue";
import ReservationsCalendar from "@/Components/Calendar/ReservationsCalendar.vue";
import DataTable from "@/Components/DataTable.vue";
import FilterBase from "@/Components/Filter/FilterBase.vue";
import SousMenuPrincipale from "@/Pages/Menu/SousMenuPrincipale.vue";
import FormulaireReservation from "@/Pages/Reservation/Formulaire/ReservationFormulaire.vue";
import VoyageForm from "@/Pages/Reservation/Formulaire/VoyageForm.vue";
import usePermissions from "@/UserPermissions/usePermissions.js";
import { DownOutlined } from "@ant-design/icons-vue";
import { router } from "@inertiajs/vue3";
import dayjs from "dayjs";
import { computed, ref, watch } from "vue";
import { createSearchFilter, gotoSearch } from "../../../Utils/FiltreUtils.js";
import ExportData from "@/Components/ExportData/ExportData.vue";
import ExcelImportBaseStandard from "@/Components/ExcelImportBaseStandard.vue";

const { can } = usePermissions();
const formModal = ref(null);

const voyageDetailsFormModal = ref(null);

const currentView = ref("table"); // 'table' ou 'calendar'

// Pour le calendrier
const calendarCurrentDate = ref(dayjs());
const calendarFilterDate = ref(null);
const isCalendarLoading = ref(false);
const isCalendarNavigating = ref(false);

const title = computed(
    () => `Liste des reservations (${props.data?.total ?? 0})`
);

const props = defineProps({
    data: {
        type: Object,
        default: () => ({}),
    },
    clients: {
        type: Array,
        default: () => [],
    },
    vehicules: {
        type: Array,
        default: () => [],
    },
    lieux_chargement: {
        type: Array,
        default: () => [],
    },
    lieux_livraison: {
        type: Array,
        default: () => [],
    },
    chauffeurs: {
        type: Array,
        default: () => [],
    },
    filters: {
        type: Object,
        default: () => ({}),
    },
    flash: {
        type: Object,
        default: () => ({}),
    },
});

// Charger les événements du calendrier au changement de vue si on passe au calendrier
watch(currentView,(newView) => {if (newView === "calendar") {loadCalendarData()}},
    { immediate: true }
);

const dropdownVisible = ref(false);
const filter = ref({...createSearchFilter(),client_id: props.filters.client_id || null});

const applyFilters = () => {
    const url = route("reservation.index");
    const params = { ...filter.value };

    if (currentView.value === "calendar") {
        params.month = calendarCurrentDate.value.month() + 1;
        params.year = calendarCurrentDate.value.year();
        if (calendarFilterDate.value) { params.date_filtre = calendarFilterDate.value.format("YYYY-MM-DD")}
    }
    else {}

    gotoSearch(params, url);
    closeDropdown();
};

const resetFilters = () => {
    filter.value.search = ""
    filter.value.client_id = null;
    // Recharger les données du calendrier avec les filtres réinitialisés
    if (currentView.value === "calendar") {
        calendarCurrentDate.value = dayjs();
        calendarFilterDate.value = null;
        applyFilters();
    } else {
        applyFilters();
    }
};

const closeDropdown = () => {
    dropdownVisible.value = false;
};

const handleDelete = (record) => {
    confirm_delete(() => { router.delete(route("reservation.destroy", record.id), { preserveScroll : true})})
};

// Computed property for calendar events from props.data
const calendarEvents = computed(() => {
    if (!props.data || !props.data.data) return [];

    const filteredByMonthAndYear = props.data.data.filter((event) => {
        return dayjs(event.date_reservation).isSame( calendarCurrentDate.value, "month");
    });

    return filteredByMonthAndYear.map((event) => ({
        id: event.id,
        title: `${event.numero_reservation} - ${event.client?.nom_client ?? "N/A"}`,
        date: dayjs(event.date_reservation).format("YYYY-MM-DD"),
        backgroundColor: "#1890ff",
        textColor: "#ffffff",
        ...event,
    }));
});

const loadCalendarData = async () => {
    isCalendarLoading.value = false;
    isCalendarNavigating.value = false;
};

const handleCalendarDateNavigate = (newDate) => { calendarCurrentDate.value = newDate;};

const handleCalendarDateFilterChange = (date) => {
    calendarFilterDate.value = date;
    if (date) { calendarCurrentDate.value = date;}
    else { calendarCurrentDate.value = dayjs();} // Retour à la date d'aujourd'hui si le filtre est effacé
};

const handleCalendarGoToToday = () => {
    calendarCurrentDate.value = dayjs();
    calendarFilterDate.value = null;
};

//ajouter un reservation depuis le calendrier
const handleCalendarAddEvent = (date) => {
    if (can("reservation.store")) { formModal.value.add({ date_reservation: date });}
    else return
};

const handleCalendarEditEvent = (record) => { formModal.value.update(record)}

const handleCalendarDeleteEvent = (record) => {handleDelete(record)}

const handleCalendarViewDetails = (record) => { voyageDetailsFormModal.value.open(record)};

const columns = ref([
    { key: "date_reservation",title: "Date",width: 100,dataIndex:"date_reservation",
        customHeaderCell: () => ({ class: "!text-center whitespace-nowrap" }),
        customCell: () => ({ class: "!text-center" }),
    },
    { key: "numero_reservation",title: "N° Res", width: 100, dataIndex:"numero_reservation",
        customHeaderCell: () => ({ class: "!text-center whitespace-nowrap" }),
        customCell: () => ({ class: "!text-center" }),
    },
    { key: "client", title: "Client", width: 100, dataIndex:"client"},
    { key: "nbr_vehicule",title: "nbr vehicule",width: 100,dataIndex:"nbr_vehicule",
        customHeaderCell: () => ({ class: "!text-center" }),
        customCell: () => ({ class: "!text-center" }),
    },
    { key: "lieu_chargement", title: "Lieu chargmt", width: 100, dataIndex:"lieu_chargement" },
    { key: "lieu_livraison", title: "Lieu livraison", width: 100, dataIndex:"lieu_livraison" },
    { key: "commentaire", title: "Commentaire", width: 150, dataIndex:"commentaire" },
    { key: "etat", title: "Etat", width: 100, dataIndex:"etat" },
    { key: "user", title: "Utilisateur", width: 100, dataIndex:"user",
        customHeaderCell: () => ({ class: "!text-center" }),
        customCell: () => ({ class: "!text-center" }),
    },
]);
const actions = [
    {
        text: "Modifier",
        action: (record) => formModal.value.update(record),
        icon: "fa-edit",
        privilege: "reservation.update",
    },
    {
        text: "Voir les Details",
        icon: "fa-eye",
        action: (record) => voyageDetailsFormModal.value.open(record),
        privilege: "reservation.details",
        disabled: (record) => record.etat_facture == "non_valide",
    },
    {
        text: "Supprimer",
        action: handleDelete,
        icon: "fa-trash",
        class: "!text-red-600",
        privilege: "reservation.destroy",
        disabled: (record) => props.data.total < 1 || record.voyages > 0,
    },
];
</script>

<style scoped>
:deep(.switch-segmented .ant-segmented-item-label) {
    @apply px-2 whitespace-normal !important;
}

:deep(.switch-segmented .ant-segmented-item-selected) {
    @apply bg-[#172A6C] text-white shadow-md !important;
}
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.2s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}

.slide-right-enter-active,
.slide-right-leave-active {
    transition: all 0.2s ease-in;
}

.slide-right-enter-from {
    opacity: 0;
    transform: translateX(50px);
}

.slide-right-leave-to {
    opacity: 0;
    transform: translateX(-50px);
}
</style>
