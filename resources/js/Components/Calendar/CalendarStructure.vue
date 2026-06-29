<template>
    <div class="calendar-header-container">
        <div class="calendar-header">
            <div class="navigation-section">
                <a-button
                    @click="onPreviousMonth"
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
                    @click="onNextMonth"
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
                    @click="$emit('go-to-today')"
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
                        class="mb-1"
                    >
                        <a-popover 
                            trigger="hover" 
                            placement="top"
                            :title="event.entity_name ? `${event.entity_name} : ${event.nom_document || event.title}` : event.title"
                        >
                            <template #content>
                                <div class="max-w-xs">
                                    <p v-if="event.entity_type" class="mb-1"><strong>Type:</strong> {{ event.entity_type }}</p>
                                    <p v-if="event.date" class="mb-1"><strong>Date:</strong> {{ dayjs(event.date).format('DD/MM/YYYY') }}</p>
                                    <p v-if="event.description" class="mb-0"><strong>Détails:</strong> {{ event.description }}</p>
                                </div>
                            </template>
                            <div
                                class="event-item"
                                :style="{
                                    backgroundColor: event.backgroundColor,
                                    color: event.textColor,
                                }"
                            >
                                {{ event.title }}
                            </div>
                        </a-popover>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { LeftOutlined, RightOutlined } from "@ant-design/icons-vue";
import { Button, DatePicker, Tag, Popover } from "ant-design-vue";
import dayjs from "dayjs";
import { computed } from "vue";

const weekDays = ["Lun", "Mar", "Mer", "Jeu", "Ven", "Sam", "Dim"];

const props = defineProps({
    events: { type: Array, default: () => [] },
    currentDate: { type: Object, required: true }, // dayjs instance
    isLoading: { type: Boolean, default: false },
    isNavigating: { type: Boolean, default: false },
    filterDate: { type: Object, default: null }, // dayjs or null
});

const emits = defineEmits([
    "date-navigate",
    "date-filter-change",
    "go-to-today",
    "add-event",
]);

const AButton = Button;
const ADatePicker = DatePicker;
const ATag = Tag;
const APopover = Popover;

const currentMonthYear = computed(() => props.currentDate.format("MMMM YYYY"));

const calendarDays = computed(() => {
    const startOfMonth = props.currentDate.startOf("month");
    const endOfMonth = props.currentDate.endOf("month");
    const startOfWeek = startOfMonth.startOf("week").add(1, "day");
    const endOfWeek = endOfMonth.endOf("week").add(1, "day");

    const days = [];
    let current = startOfWeek;

    while (current.isBefore(endOfWeek) || current.isSame(endOfWeek, "day")) {
        const dayEvents = props.events.filter((event) =>
            dayjs(event.date).isSame(current, "day")
        );
        days.push({
            date: current.format("YYYY-MM-DD"),
            day: current.date(),
            month: current.month(),
            isCurrentMonth: current.isSame(props.currentDate, "month"),
            isToday: current.isSame(dayjs(), "day"),
            events: dayEvents,
            isFiltered: false,
        });
        current = current.add(1, "day");
    }

    return days;
});

const filteredCalendarDays = computed(() => {
    if (!props.filterDate) return calendarDays.value;
    return calendarDays.value.map((day) => ({
        ...day,
        isFiltered: dayjs(day.date).isSame(props.filterDate, "day"),
    }));
});

const onDateFilterChange = (date) => {
    emits("date-filter-change", date);
};

const clearDateFilter = () => {
    emits("date-filter-change", null);
};

const onPreviousMonth = () => {
    emits("date-navigate", props.currentDate.subtract(1, "month"));
};

const onNextMonth = () => {
    emits("date-navigate", props.currentDate.add(1, "month"));
};

const selectDay = (day) => {
    emits("add-event", day);
};
</script>

<style scoped>
.calendar-header-container {
    background: white;
    padding: 16px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
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

.calendar-grid {
    border: 1px solid #e8e8e8;
    overflow: hidden;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    position: relative;
    transition: all 0.3s ease;
    border-radius: 8px;
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
.calendar-day:hover:not(.loading) {
    background: linear-gradient(135deg, #f8f9ff, #e6f2ff);
    transform: scale(1.02);
    z-index: 2;
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
}
.calendar-day:nth-child(7n) {
    border-right: none;
}
.calendar-day.other-month {
    background-color: #fafafa;
    color: #bbb;
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
    margin-bottom: 6px;
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
</style>
