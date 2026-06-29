<script setup>
import CalendarStructure from "@/Components/Calendar/CalendarStructure.vue";
import FilterBase from "@/Components/Filter/FilterBase.vue";
import SousMenuPrincipale from "@/Pages/Menu/SousMenuPrincipale.vue";
import usePermissions from "@/UserPermissions/usePermissions.js";
import { DownOutlined } from "@ant-design/icons-vue";
import { router } from "@inertiajs/vue3";
import { message } from "ant-design-vue";
import dayjs from "dayjs";
import "dayjs/locale/fr";
import { onMounted, ref } from "vue";

dayjs.locale("fr");
const { can } = usePermissions();

const props = defineProps({
    data: {
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
    errors: Object,
});

const title = ref("Calendrier");

// État réactif
const currentDate = ref(dayjs());
const events = ref([]);
const filterDate = ref(null);
const isLoading = ref(false);
const isNavigating = ref(false);

const filter = ref({
    search: "",
    start_date: null,
    end_date: null,
});

const dropdownVisible = ref(false);
const closeDropdown = () => {
    dropdownVisible.value = false;
};

const applyFilters = () => {
    loadCalendarData({
        search_calendrier: filter.value.search || undefined,
        start_date: filter.value.start_date ? dayjs(filter.value.start_date).format("YYYY-MM-DD") : undefined,
        end_date: filter.value.end_date ? dayjs(filter.value.end_date).format("YYYY-MM-DD") : undefined,
    });
};

const resetFilters = () => {
    filter.value.search = "";
    filter.value.start_date = null;
    filter.value.end_date = null;
    loadCalendarData();
};

const newEvent = ref({
    title: "",
    date: null,
    backgroundColor: "#1890ff",
    textColor: "#ffffff",
});

// Normaliser les dates des événements au chargement
onMounted(() => {
    events.value = props.data.map((event) => ({
        ...event,
        date: dayjs(event.date, "DD-MM-YYYY").format("YYYY-MM-DD"),
    }));

    // Initialiser la date de filtre si elle existe dans les props
    if (props.filters.date) {
        filterDate.value = dayjs(props.filters.date);
    }

    // Initialiser la date courante si elle existe dans les props
    if (props.filters.current_date) {
        currentDate.value = dayjs(props.filters.current_date);
    }
});

// Fonction pour charger les données depuis le backend
const loadCalendarData = async (params = {}, showNavigationLoader = false) => {
    if (showNavigationLoader) {
        isNavigating.value = true;
    } else {
        isLoading.value = true;
    }

    const searchParams = {
        date_filtre: currentDate.value.format("YYYY-MM-DD"),
        ...params,
    };

    if (filterDate.value) {
        searchParams.filter_date = filterDate.value.format("YYYY-MM-DD");
    }

    try {
        await new Promise((resolve, reject) => {
            router.get(route("calendrier.index"), searchParams, {
                preserveState: true,
                preserveScroll: true,
                onSuccess: (page) => {
                    // Mettre à jour les événements avec les nouvelles données
                    events.value = page.props.data.map((event) => ({
                        ...event,
                        date: dayjs(event.date, "DD-MM-YYYY").format(
                            "YYYY-MM-DD"
                        ),
                    }));
                    resolve(page);
                },
                onError: (errors) => {
                    console.error("Erreurs:", errors);
                    message.error("Erreur lors du chargement des données");
                    reject(errors);
                },
            });
        });
    } finally {
        isLoading.value = false;
        isNavigating.value = false;
    }
};

const goToToday = async () => {
    currentDate.value = dayjs();
    filterDate.value = null;
    await loadCalendarData({}, true);
};

// Méthodes de filtrage
const onDateFilterChange = (date) => {
    filterDate.value = date;
    if (date) {
        currentDate.value = date;
    }
};

// Navigation handler
const handleDateNavigate = async (newDate) => {

    currentDate.value = dayjs(newDate);
    await loadCalendarData({}, true);
};

// Gestion des événements
const selectDay = (day) => {
    if (isLoading.value || isNavigating.value) return;
    newEvent.value.date = dayjs(day.date);
};
</script>

<template>
    <SousMenuPrincipale :title="title" selectedMenu="calendrier" v-if="can('calendrier.index')">
        <div class="custom-calendar">
            <FilterBase
                v-model="filter"
                @search="applyFilters"
                @reset="resetFilters"
                :show_boxShasow="true"
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
                                <a-date-picker
                                    v-model:value="filter.start_date"
                                    format="DD/MM/YYYY"
                                    placeholder="Du"
                                    class="w-full text-center"
                                    :value-format="'YYYY-MM-DD'"
                                />
                                <a-date-picker
                                    v-model:value="filter.end_date"
                                    format="DD/MM/YYYY"
                                    placeholder="Au"
                                    class="w-full text-center"
                                    :value-format="'YYYY-MM-DD'"
                                />
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
            <CalendarStructure
                :events="events"
                :current-date="currentDate"
                :is-loading="isLoading"
                :is-navigating="isNavigating"
                :filter-date="filterDate"
                @date-navigate="handleDateNavigate"
                @date-filter-change="onDateFilterChange"
                @go-to-today="goToToday"
                @add-event="selectDay"
            />

            <!-- La grille du calendrier est rendue par CalendarStructure -->
        </div>
    </SousMenuPrincipale>
</template>

<style scoped>
</style>
