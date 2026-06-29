<template>
    <SousMenuPrincipale title="Dashboard Carburant" selectedMenu="dashboard_carburant" v-if="can('dashboard.carburant')">
        <!-- Section des filtres avec espacement amélioré -->

        <FilterBase v-model="filter" @search="applyFilters" @reset="resetFilters">
            <template #otherFilter>
                <a-popover placement="bottomRight" trigger="click" :visible="dropdownVisible" @visibleChange="(val) => (dropdownVisible = val)" >
                    <template #content>
                        <div class="bg-white p-4 w-96 space-y-2 rounded-md">
                            <a-date-picker
                                v-model:value="filter.start_date"
                                :format="dateFormat"
                                size="large"
                                class="w-full"
                                placeholder="Du"
                                :value-format="'YYYY-MM-DD'"
                            />
                            <a-date-picker
                                v-model:value="filter.end_date"
                                :format="dateFormat"
                                size="large"
                                class="w-full"
                                placeholder="Au"
                                :value-format="'YYYY-MM-DD'"
                            />
                            <div class="flex space-x-2 !mt-6">
                                <a-button block type="primary" size="middle" @click="applyFilters" >Appliquer</a-button>
                                <a-button block type="default" size="middle" @click="dropdownVisible = false">Fermer</a-button>
                            </div>
                        </div>
                    </template>
                    <a-button size="large" type="default" class="!rounded-none border-r-0 focus:z-10">
                        <Space>
                            <font-awesome-icon class="text-[15px]" icon="fa-filter"/>
                            Filtres
                            <DownOutlined />
                        </Space>
                    </a-button>
                </a-popover>
            </template>
        </FilterBase>
        <div class="bg-white md:p-10 p-6">
            <!-- Cartes de statistiques (2 cartes) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 px-4">
                <TestStatCard title="Cartes carburant" :value="props.data?.totalCard?.total ?? 0" icon="fa-credit-card"
                    :details="[
                        {label: 'Activé',value: props.data?.totalCard?.statusCard?.cardActive ?? 0},
                        {label: 'Desactivé',value: props.data?.totalCard?.statusCard?.cardDesactive ?? 0},
                    ]"
                    gradient-from="#3B82F6"
                    gradient-to="#9333EA"
                />
                <TestStatCard title="Transactions carburant" :value="props.data?.totalCarbtrans ?? 0" icon="fa-gas-pump"
                    :details="props.data?.transactionBytype ?? []"
                    gradient-from="#FACC15"
                    gradient-to="#F43F5E"
                />
            </div>

            <div v-if="isAtLeast('standard')" class="mt-6 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-12 auto-rows-fr gap-6 items-stretch">
                <div class="xl:col-span-5 md:col-span-1">
                    <CarbPieChart :element="props.data?.mouvementByType ?? {data: {},annee: ''}" :height="360" title="Répartition des mouvements par type"/>
                </div>
                <div class="xl:col-span-7 md:col-span-1">
                    <RankedList :items="props.data?.topCardsByTransactions ?? []" label-key="label" value-key="value" primary-label="Transactions"
                        title="Top cartes par nombre de transactions"
                        primary-color="#3B82F6"
                        gradient-from="#3B82F6"
                        gradient-to="#B979F4"
                    />
                </div>
                <div class="xl:col-span-12 md:col-span-2">
                    <HorizontalBarChart
                        :items="props.data?.topVehiculesByLitres ?? []"
                        label-key="label"
                        value-key="value"
                        value2-key="value2"
                        primary-label="Litres"
                        secondary-label="Transactions"
                        primary-color="#FACC15"
                        secondary-color="#FF617C"
                        :show-legend="true"
                        mode="dual"
                        :show-pagination="true"
                        :page-size="6"
                        title="Transactions et litres ravitaillés de chaque véhicule"
                    />
                </div>
            </div>
        </div>
    </SousMenuPrincipale>
</template>

<script setup>
import CarbPieChart from "@/Components/Charts/CarbPieChart.vue";
import HorizontalBarChart from "@/Components/Charts/HorizontalBarChart.vue";
import FilterBase from "@/Components/Filter/FilterBase.vue";
import SousMenuPrincipale from "@/Pages/Menu/SousMenuPrincipale.vue";
import RankedList from "@/Pages/Dashboard/Partials/RankedList.vue";
import usePermissions from "@/UserPermissions/usePermissions.js";
import { DownOutlined } from "@ant-design/icons-vue";
import { Space } from "ant-design-vue";
import dayjs from "dayjs";
import { ref } from "vue";
import { createSearchFilter, gotoSearch } from "../../../../Utils/FiltreUtils.js";
import { usePlan } from "@/Composables/usePlan.js";
import TestStatCard from "../Partials/TestStatCard.vue";

const { can } = usePermissions();
const { isAtLeast } = usePlan()
const dropdownVisible = ref(false);
const dateFormat = "DD/MM/YYYY";

const props = defineProps({
    data: { type: Object, default: () => ({}) },
    filters: { type: Object, default: () => ({}) },
    flash: { type: Object, default: () => ({}) },
});

const getMonthStartDate = () => dayjs().startOf("month").format("YYYY-MM-DD");
const getMonthEndDate = () => dayjs().endOf("month").format("YYYY-MM-DD");

const filter = ref({
    ...createSearchFilter(),
    search: props.filters && props.filters.search ? props.filters.search : "",
    start_date: props.filters && props.filters.start_date ? props.filters.start_date : getMonthStartDate(),
    end_date: props.filters && props.filters.end_date ? props.filters.end_date : getMonthEndDate(),
});

const applyFilters = () => {
    const url = route("dashboard.carburant");
    gotoSearch(filter.value, url, ["data"]);
};

const resetFilters = () => {
    filter.value.search = "";
    filter.value.start_date = getMonthStartDate();
    filter.value.end_date = getMonthEndDate();
    applyFilters();
};
</script>
