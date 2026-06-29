<template>
    <div class="calendar-header-container">
        <div class="calendar-header">
            <div class="navigation-section">
                <a-button
                    @click="previousMonth"
                    type="text"
                    class="nav-button"
                    :loading="isLoading || isNavigating"
                >
                    <template #icon>
                        <LeftOutlined />
                    </template>
                </a-button>

                <h2 class="month-title">{{ currentMonthYear }}</h2>

                <a-button
                    @click="nextMonth"
                    type="text"
                    class="nav-button"
                    :loading="isLoading || isNavigating"
                >
                    <template #icon>
                        <RightOutlined />
                    </template>
                </a-button>
            </div>

            <div class="action-section">
                <a-button
                    @click="goToToday"
                    type="primary"
                    ghost
                    class="today-button"
                >
                    Aujourd'hui
                </a-button>

                <a-date-picker
                    :value="filterDate"
                    format="DD/MM/YYYY"
                    placeholder="Rechercher une date"
                    @change="onDateFilterChange"
                    class="date-picker"
                />
            </div>
        </div>
    </div>

    <div class="active-filters" v-if="hasActiveFilters">
        <div class="filters-header">
            <h4>Filtres actifs</h4>
        </div>
        <a-tag
            v-if="filterDate"
            closable
            @close="onDateFilterChange(null)"
            color="blue"
            class="filter-tag"
        >
            <template #icon>
                <CalendarOutlined />
            </template>
            Date: {{ filterDate.format("DD/MM/YYYY") }}
        </a-tag>
    </div>

    <div class="calendar-grid" :class="{ loading: isLoading || isNavigating }">
        <div class="calendar-weekdays">
            <div v-for="day in weekDays" :key="day" class="weekday-header">
                {{ day }}
            </div>
        </div>

        <div class="calendar-days">
            <div
                v-for="(day, index) in filteredCalendarDays"
                :key="`${day.date}-${day.month}`"
                :class="[
                    'calendar-day',
                    {
                        'other-month': !day.isCurrentMonth,
                        today: day.isToday,
                        'has-events': day.events.length > 0,
                        'filtered-day': day.isFiltered,
                        loading: isLoading || isNavigating,
                    },
                ]"
                :style="{ '--i': index }"
                @click="selectDay(day)"
            >
                <div class="day-number">{{ day.day }}</div>

                <div class="day-events">
                    <div
                        v-for="event in day.events.slice(0, 3)"
                        :key="event.id"
                        class="event-item mb-1"
                        :class="{
                            'event-valid': event.etat_facture === 'valide',
                            'event-invalid':
                                event.etat_facture === 'non_valide',
                        }"

                    >
                        <a-dropdown trigger="contextmenu" placement="topLeft">
                            <template #overlay>
                                <div class="dropdown-buttons-container">
                                    <a-button
                                        v-if="props.canUpdate"
                                        type="text"
                                        :icon="h(EditOutlined)"
                                        @click="emits('edit-event', event)"
                                    >
                                        Modifier
                                    </a-button>
                                    <a-button
                                        v-if="props.canDelete"
                                        type="text"
                                        danger
                                        :icon="h(DeleteOutlined)"
                                        @click="emits('delete-event', event)"
                                        :disabled="event.voyages > 0"
                                    >
                                        Supprimer
                                    </a-button>
                                    <a-button
                                        v-if="props.canViewDetails"
                                        type="text"
                                        :icon="h(EyeOutlined)"
                                        @click="emits('view-details', event)"
                                        :disabled="
                                            event.etat_facture == 'non_valide'
                                        "
                                    >
                                        Voir Détails
                                    </a-button>
                                </div>
                            </template>
                            <span class="event-title">
                                <span
                                    class="event-status-indicator"
                                    :class="{
                                        'status-valid':
                                            event.etat_facture === 'valide',
                                        'status-invalid':
                                            event.etat_facture === 'non_valide',
                                    }"
                                ></span>
                                {{ event.title }}
                            </span>
                        </a-dropdown>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import {
    CalendarOutlined,
    DeleteOutlined,
    EditOutlined,
    EyeOutlined,
    LeftOutlined,
    RightOutlined,
} from "@ant-design/icons-vue";
import { Button, DatePicker, Dropdown, Tag } from "ant-design-vue";
import dayjs from "dayjs";
import "dayjs/locale/fr";
import { computed, h } from "vue";

dayjs.locale("fr");

const weekDays = ["Lun", "Mar", "Mer", "Jeu", "Ven", "Sam", "Dim"];

const props = defineProps({
    events: {
        type: Array,
        default: () => [],
    },
    currentDate: {
        type: Object, // dayjs object
        required: true,
    },
    isLoading: {
        type: Boolean,
        default: false,
    },
    isNavigating: {
        type: Boolean,
        default: false,
    },
    filterDate: {
        type: Object, // dayjs object or null
        default: null,
    },
    canUpdate: { type: Boolean, default: false },
    canDelete: { type: Boolean, default: false },
    canViewDetails: { type: Boolean, default: false },
});

const emits = defineEmits([
    "date-navigate",
    "date-filter-change",
    "go-to-today",
    "add-event",
    "edit-event",
    "delete-event",
    "view-details",
]);

// Composants Ant Design
const AButton = Button;
const ADatePicker = DatePicker;
const ATag = Tag;
const ADropdown = Dropdown;
// const AMenu = Menu; // Plus utilisé directement

// Titre du mois courant
const currentMonthYear = computed(() => {
    return props.currentDate.format("MMMM YYYY");
});
// Vérifier s'il y a des filtres actifs
const hasActiveFilters = computed(() => {
    return props.filterDate !== null;
});

// Génération des jours du calendrier
const calendarDays = computed(() => {
    const startOfMonth = props.currentDate.startOf("month");
    const endOfMonth = props.currentDate.endOf("month");
    const startOfWeek = startOfMonth.startOf("week").add(1, "day");
    const endOfWeek = endOfMonth.endOf("week").add(1, "day");

    const days = [];
    let current = startOfWeek;

    while (current.isBefore(endOfWeek) || current.isSame(endOfWeek, "day")) {
        const dayEvents = props.events.filter((event) => {
            return dayjs(event.date).isSame(current, "day");
        });

        days.push({
            date: current.format("YYYY-MM-DD"),
            day: current.date(),
            month: current.month(),
            isCurrentMonth: current.isSame(props.currentDate, "month"),
            isToday: current.isSame(dayjs(), "day"),
            events: dayEvents,
            isFiltered: false, // This will be calculated in filteredCalendarDays
        });

        current = current.add(1, "day");
    }

    return days;
});

// Jours filtrés selon les critères de recherche
const filteredCalendarDays = computed(() => {
    if (!hasActiveFilters.value) {
        return calendarDays.value;
    }

    return calendarDays.value.map((day) => {
        let isFiltered = false;

        if (props.filterDate) {
            isFiltered = dayjs(day.date).isSame(props.filterDate, "day");
        }

        return {
            ...day,
            isFiltered,
        };
    });
});

const previousMonth = () => {
    emits("date-navigate", props.currentDate.subtract(1, "month"));
};

const nextMonth = () => {
    emits("date-navigate", props.currentDate.add(1, "month"));
};

const goToToday = () => {
    emits("go-to-today");
};

// Méthodes de filtrage
const onDateFilterChange = (date) => {
    emits("date-filter-change", date);
};

const selectDay = (day) => {
    emits("add-event", day.date);
};
</script>

<style scoped>
.custom-calendar {
    font-family: "Inter", sans-serif;
    background-color: #f0f2f5;
    padding: 20px;
    border-radius: 12px;
}

.calendar-header-container {
    background: white;
    padding: 16px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);

    margin-bottom: 20px;
}

.calendar-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 16px;
    flex-wrap: wrap;
}

.navigation-section {
    display: flex;
    align-items: center;
    gap: 12px;
}

.month-title {
    margin: 0;
    font-size: 1.5rem;
    font-weight: 600;
    color: #2c3e50;
    min-width: 200px;
    text-align: center;
}

.nav-button {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 8px;
    border: 1px solid #f0f0f0;
    transition: all 0.2s;
}

.nav-button:hover {
    background: #f5f5f5;
    border-color: #d9d9d9;
}

.action-section {
    display: flex;
    align-items: center;
    gap: 12px;
}

.today-button {
    font-weight: 500;
}

.date-picker {
    width: 200px;
}

.clear-btn {
    color: rgba(255, 255, 255, 0.9);
    border: 2px solid rgba(255, 255, 255, 0.3);
    border-radius: 12px;
    height: 44px;
    padding: 0 20px;
    font-weight: 500;
    backdrop-filter: blur(15px);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.clear-btn:hover:not(:disabled) {
    color: white;
    background: rgba(255, 255, 255, 0.15);
    transform: translateY(-2px);
}

.status-indicator {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-top: 20px;
    padding: 16px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 12px;
    backdrop-filter: blur(15px);
}

.loading-dots {
    display: flex;
    gap: 4px;
}

.loading-dots span {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: white;
    animation: bounce 1.4s ease-in-out infinite both;
}

.loading-dots span:nth-child(1) {
    animation-delay: -0.32s;
}
.loading-dots span:nth-child(2) {
    animation-delay: -0.16s;
}
.loading-dots span:nth-child(3) {
    animation-delay: 0s;
}

@keyframes bounce {
    0%,
    80%,
    100% {
        transform: scale(0.8);
        opacity: 0.5;
    }
    40% {
        transform: scale(1);
        opacity: 1;
    }
}

.status-text {
    font-size: 14px;
    font-weight: 500;
    color: rgba(255, 255, 255, 0.9);
}

.active-filters {
    margin-bottom: 20px;
    padding: 20px;
    background: linear-gradient(135deg, #f8f9fa, #e9ecef);
    border-radius: 16px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
}

.filters-header {
    margin-bottom: 12px;
}

.filters-header h4 {
    margin: 0;
    color: #495057;
    font-size: 16px;
    font-weight: 600;
}

.filter-tag {
    border-radius: 20px;
    padding: 8px 16px;
    font-size: 13px;
    font-weight: 500;
    border: none;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.calendar-grid {
    border: 1px solid #e8e8e8;
    overflow: hidden;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    position: relative;
    transition: all 0.3s ease;
    border-radius: 8px; /* Added for consistency */
}

.calendar-grid.loading {
    opacity: 0.7;
    pointer-events: none;
}

.calendar-grid.loading::after {
    content: "";
    position: absolute;
    top: 50%;
    left: 50%;
    width: 40px;
    height: 40px;
    margin: -20px 0 0 -20px;
    border: 3px solid #f3f3f3;
    border-top: 3px solid #667eea;
    border-radius: 50%;
    animation: spin 1s linear infinite;
    z-index: 10;
}

@keyframes spin {
    0% {
        transform: rotate(0deg);
    }
    100% {
        transform: rotate(360deg);
    }
}

.calendar-weekdays {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    background: linear-gradient(135deg, #f8f9fa, #e9ecef);
}

.weekday-header {
    padding: 20px 12px;
    text-align: center;
    font-weight: 700;
    color: #495057;
    border-right: 1px solid #dee2e6;
    font-size: 14px;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.weekday-header:last-child {
    border-right: none;
}

.calendar-days {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
}

.calendar-day {
    min-height: 120px;
    border-right: 1px solid #e8e8e8;
    border-bottom: 1px solid #e8e8e8;
    padding: 16px;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    background: white;
}

.calendar-day:hover:not(.loading) {
    background: linear-gradient(135deg, #f8f9ff, #e6f2ff);
    transform: scale(1.02);
    z-index: 2;
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
}

.calendar-day.other-month {
    background-color: #fafafa;
    color: #bbb;
}

.calendar-days .calendar-day {
    opacity: 0;
    transform: translateY(20px);
    animation: slideIn 0.2s forwards ease-out;
}

.calendar-days .calendar-day:nth-child(n) {
    animation-delay: calc(var(--i) * 0.01s);
}

@keyframes slideIn {
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.calendar-day:nth-child(7n) {
    border-right: none;
}

.calendar-day.today {
    background: linear-gradient(135deg, #e3f2fd, #bbdefb);
    border: 2px solid #2196f3;
}

.calendar-day.today .day-number {
    background: linear-gradient(45deg, #2196f3, #1976d2);
    color: white;
    border-radius: 50%;
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    box-shadow: 0 4px 12px rgba(33, 150, 243, 0.4);
}

.calendar-day.filtered-day {
    background: linear-gradient(135deg, #fff3e0, #ffcc02);
    border: 2px solid #ff9800;
}

.event-item {
    padding: 8px 8px;
    border-radius: 6px;
    margin-bottom: 6px; /* Plus d'espace entre les événements */
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
    font-weight: 500;
    text-overflow: ellipsis;
    white-space: nowrap;
    overflow: hidden;
    transition: all 0.2s ease-in-out;
    border: 2px solid transparent;
}

.event-item:hover {
    opacity: 0.9;
    transform: translateY(-1px);
}

/* Styles pour les événements valides */
.event-valid {
    background: linear-gradient(135deg, #f0f9ff, #e0f2fe) !important;
    color: #0369a1 !important;
    border-color: #0ea5e9 !important;
}

.event-valid:hover {
    background: linear-gradient(135deg, #e0f2fe, #bae6fd) !important;
    box-shadow: 0 4px 12px rgba(14, 165, 233, 0.15) !important;
}

/* Styles pour les événements invalides */
.event-invalid {
    background: linear-gradient(135deg, #fefce8, #fef3c7) !important;
    color: #a16207 !important;
    border-color: #eab308 !important;
}

.event-invalid:hover {
    background: linear-gradient(135deg, #fef3c7, #fde68a) !important;
    box-shadow: 0 4px 12px rgba(234, 179, 8, 0.15) !important;
}

/* Indicateur de statut */
.event-status-indicator {
    display: inline-block;
    width: 8px;
    height: 8px;
    border-radius: 50%;
    margin-right: 6px;
    vertical-align: middle;
}

.status-valid {
    background-color: #0ea5e9;
    box-shadow: 0 0 0 2px rgba(14, 165, 233, 0.2);
}

.status-invalid {
    background-color: #eab308;
    box-shadow: 0 0 0 2px rgba(234, 179, 8, 0.2);
}

.day-events {
    margin-top: 8px; /* Plus d'espace entre le numéro du jour et les événements */
}

.event-title {
    cursor: pointer;
    display: block; /* S'assurer que le span occupe toute la largeur pour le clic */
    width: 100%;
}

/* Styles pour le nouveau conteneur de boutons dans le dropdown */
.dropdown-buttons-container {
    @apply flex flex-col p-1 bg-white rounded-lg shadow-lg;
}

.dropdown-buttons-container .ant-btn {
    @apply w-full text-left mb-1 border-none px-2 py-2 h-auto;
}
.dropdown-buttons-container .ant-btn:last-child {
    margin-bottom: 0;
}
.dropdown-buttons-container .ant-btn-text:hover,
.dropdown-buttons-container .ant-btn-text:focus {
    background-color: #e6f7ff;
    color: #1890ff;
}

.dropdown-buttons-container .ant-btn .anticon {
    @apply mr-2;
}
</style>
