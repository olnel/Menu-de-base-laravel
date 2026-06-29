<script setup>
import ButtonIcon from "@/Components/ButtonIcon.vue";
import DataTable from "@/Components/DataTable.vue";
import FilterBase from "@/Components/Filter/FilterBase.vue";
import SousMenuPrincipale from "@/Pages/Menu/SousMenuPrincipale.vue";
import PneuRemplacementForm from "@/Pages/PneuRemplacement/PneuRemplacementForm.vue";
import usePermissions from "@/UserPermissions/usePermissions.js";
import { router } from "@inertiajs/vue3";
import { DownOutlined } from "@ant-design/icons-vue";
import { computed, ref } from "vue";
import { createSearchFilter, gotoSearch } from "../../../Utils/FiltreUtils.js";

const { can } = usePermissions();

const props = defineProps({
    data:      { type: Object, default: () => ({}) },
    vehicules: { type: Array,  default: () => [] },
    remorques: { type: Array,  default: () => [] },
    magasins:  { type: Array,  default: () => [] },
});

const title = computed(
    () => `Remplacements & Permutations (${props.data?.total ?? 0})`
);

const dropdownVisible = ref(false);
const refFormModal    = ref();

const filter = ref({
    ...createSearchFilter(),
    start_date:     null,
    end_date:       null,
    vehicule_id:    null,
    type_operation: null,
});

const applyFilters = () => {
    gotoSearch(filter.value, route("pneu_remplacement.index"));
    closeDropdown();
};

const resetFilters = () => {
    filter.value.search         = "";
    filter.value.start_date     = null;
    filter.value.end_date       = null;
    filter.value.vehicule_id    = null;
    filter.value.type_operation = null;
    applyFilters();
};

const closeDropdown = () => {
    dropdownVisible.value = false;
};

const columns = [
    {
        key:              "date_operation",
        title:            "Date",
        dataIndex:        "date_operation",
        width:            100,
        customCell:       () => ({ class: "text-center align-top" }),
        customHeaderCell: () => ({ class: "!text-center" }),
    },
    {
        key:              "type_operation",
        title:            "Type",
        width:            130,
        customCell:       () => ({ class: "text-center align-top" }),
        customHeaderCell: () => ({ class: "!text-center" }),
    },
    {
        key:              "source",
        title:            "Source (pneu retiré / origine A)",
        width:            260,
        customCell:       () => ({ class: "align-top" }),
    },
    {
        key:              "fleche",
        title:            "",
        width:            36,
        customCell:       () => ({ class: "text-center align-middle" }),
        customHeaderCell: () => ({ class: "!text-center" }),
    },
    {
        key:              "destination",
        title:            "Destination (pneu monté / arrivée B)",
        width:            260,
        customCell:       () => ({ class: "align-top" }),
    },
    {
        key:       "technicien",
        title:     "Technicien",
        dataIndex: "technicien",
        width:     130,
        customCell: () => ({ class: "align-top" }),
    },
    {
        key:       "nom_user",
        title:     "Utilisateur",
        dataIndex: "nom_user",
        width:     110,
        customCell: () => ({ class: "align-top" }),
    },
    {
        key:              "actions",
        title:            "Actions",
        width:            80,
        customCell:       () => ({ class: "text-center align-top" }),
        customHeaderCell: () => ({ class: "!text-center" }),
    },
];

const MOTIF_LABELS = {
    usure:     "Usure",
    crevaison: "Crevaison",
    vol:       "Vol",
    fin_vie:   "Fin de vie",
    autre:     "Autre",
};

const MOTIF_COLORS = {
    usure:     "orange",
    crevaison: "red",
    vol:       "purple",
    fin_vie:   "volcano",
    autre:     "default",
};

const supportLabel = (serie) => {
    if (!serie) return null;
    return serie.vehicule?.immatriculation ?? serie.remorque?.numero_remorque ?? null;
};

const supportIcon = (serie) => {
    if (!serie) return null;
    return serie.vehicule ? "fa-truck" : (serie.remorque ? "fa-trailer" : null);
};
</script>

<template>
    <SousMenuPrincipale :title="title" selectedMenu="pneu_remplacement" v-if="can('pneu_remplacement.index')">
        <template #top>
            <FilterBase
            v-model="filter"
            @search="applyFilters"
            @reset="resetFilters"
        >
            <template #add>
                <ButtonIcon
                    v-if="can('pneu_remplacement.store')"
                    @click="() => refFormModal.add()"
                    type="primary"
                    text="Ajouter"
                    icon="fa-plus"
                />
            </template>

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
                                :format="'YYYY/MM/DD'"
                                size="large"
                                class="w-full"
                                placeholder="Du"
                                :value-format="'DD-MM-YYYY'"
                            />
                            <a-date-picker
                                v-model:value="filter.end_date"
                                :format="'YYYY/MM/DD'"
                                size="large"
                                class="w-full"
                                placeholder="Au"
                                :value-format="'DD-MM-YYYY'"
                            />
                            <a-select
                                v-model:value="filter.vehicule_id"
                                :options="props.vehicules"
                                size="large"
                                allowClear
                                show-search
                                placeholder="Sélectionner un véhicule"
                                class="w-full"
                                :filter-option="(input, opt) => opt.label?.toLowerCase().includes(input.toLowerCase())"
                            />
                            <a-select
                                v-model:value="filter.type_operation"
                                size="large"
                                allowClear
                                placeholder="Type d'opération"
                                class="w-full"
                                :options="[
                                    { value: 'remplacement', label: 'Remplacement' },
                                    { value: 'permutation',  label: 'Permutation'  },
                                ]"
                            />
                            <div class="flex space-x-2 !mt-4">
                                <a-button block type="primary" size="middle" @click="applyFilters()">
                                    Appliquer
                                </a-button>
                                <a-button block type="default" size="middle" @click="closeDropdown">
                                    Fermer
                                </a-button>
                            </div>
                        </div>
                    </template>
                    <a-button size="large" type="default" class="!rounded-none border-r-0 focus:z-10">
                        <a-space>
                            <font-awesome-icon class="text-[15px]" icon="fa-filter" />
                            Filtres
                            <DownOutlined />
                        </a-space>
                    </a-button>
                </a-popover>
            </template>
        </FilterBase>
        </template>

        <DataTable
            :columns="columns"
            :data="props.data"
            class="main-shadow mt-4"
            :show-index="true"
            :btn_action="false"
        >
            <template #bodyCell="{ column, record }">

                <!-- ── Type ──────────────────────────────────────────────── -->
                <template v-if="column.key === 'type_operation'">
                    <a-tag
                        :color="record.type_operation === 'remplacement' ? 'red' : 'blue'"
                        :bordered="false"
                        class="font-medium"
                    >
                        <font-awesome-icon
                            :icon="record.type_operation === 'remplacement' ? 'fa-rotate' : 'fa-arrows-left-right'"
                            class="mr-1"
                        />
                        {{ record.type_operation === 'remplacement' ? 'Remplacement' : 'Permutation' }}
                    </a-tag>
                </template>

                <!-- ── SOURCE : pneu retiré / origine A ─────────────────── -->
                <template v-else-if="column.key === 'source'">
                    <div class="space-y-1 py-0.5">
                        <!-- Badge A pour permutation -->
                        <div v-if="record.type_operation === 'permutation'" class="flex items-center gap-1 mb-1">
                            <span class="inline-flex items-center justify-center w-4 h-4 rounded-full bg-orange-500 text-white text-[10px] font-bold">A</span>
                            <span class="text-[10px] text-orange-600 font-semibold uppercase tracking-wide">Origine</span>
                        </div>

                        <!-- Véhicule / Remorque du pneu retiré -->
                        <div v-if="supportLabel(record.pneu_serie_retire)" class="flex items-center gap-1.5 text-xs text-gray-600">
                            <font-awesome-icon :icon="supportIcon(record.pneu_serie_retire)" class="text-gray-400 w-3" />
                            <span class="font-medium">{{ supportLabel(record.pneu_serie_retire) }}</span>
                        </div>
                        <!-- Véhicule de la fiche (remplacement) -->
                        <div v-else-if="record.vehicule?.immatriculation || record.remorque?.numero_remorque" class="flex items-center gap-1.5 text-xs text-gray-600">
                            <font-awesome-icon :icon="record.vehicule ? 'fa-truck' : 'fa-trailer'" class="text-gray-400 w-3" />
                            <span class="font-medium">{{ record.vehicule?.immatriculation ?? record.remorque?.numero_remorque }}</span>
                        </div>

                        <!-- N° série -->
                        <div class="flex items-center gap-1.5">
                            <span class="inline-block w-1.5 h-1.5 rounded-full bg-red-400 flex-shrink-0"></span>
                            <code class="text-xs font-mono text-gray-800 bg-gray-100 px-1.5 py-0.5 rounded">
                                {{ record.pneu_serie_retire?.numero_serie ?? '—' }}
                            </code>
                        </div>

                        <!-- Position -->
                        <div v-if="record.position_retire" class="flex items-center gap-1.5 text-xs text-gray-500">
                            <font-awesome-icon icon="fa-location-dot" class="text-gray-300 w-3" />
                            {{ record.position_retire }}
                        </div>

                        <!-- Motif (remplacement uniquement) -->
                        <div v-if="record.motif && record.type_operation === 'remplacement'" class="mt-1">
                            <a-tag :color="MOTIF_COLORS[record.motif] ?? 'default'" :bordered="false" class="!text-[11px] !px-1.5 !py-0">
                                {{ MOTIF_LABELS[record.motif] ?? record.motif }}
                            </a-tag>
                        </div>
                    </div>
                </template>

                <!-- ── FLÈCHE centrale ────────────────────────────────────── -->
                <template v-else-if="column.key === 'fleche'">
                    <font-awesome-icon
                        :icon="record.type_operation === 'permutation' ? 'fa-arrows-left-right' : 'fa-arrow-right'"
                        :class="record.type_operation === 'permutation' ? 'text-blue-400' : 'text-green-400'"
                        class="text-sm"
                    />
                </template>

                <!-- ── DESTINATION : pneu monté / arrivée B ──────────────── -->
                <template v-else-if="column.key === 'destination'">
                    <div v-if="record.pneu_serie_monte" class="space-y-1 py-0.5">
                        <!-- Badge B pour permutation -->
                        <div v-if="record.type_operation === 'permutation'" class="flex items-center gap-1 mb-1">
                            <span class="inline-flex items-center justify-center w-4 h-4 rounded-full bg-blue-500 text-white text-[10px] font-bold">B</span>
                            <span class="text-[10px] text-blue-600 font-semibold uppercase tracking-wide">Destination</span>
                        </div>

                        <!-- Véhicule / Remorque du pneu monté -->
                        <div v-if="supportLabel(record.pneu_serie_monte)" class="flex items-center gap-1.5 text-xs text-gray-600">
                            <font-awesome-icon :icon="supportIcon(record.pneu_serie_monte)" class="text-gray-400 w-3" />
                            <span class="font-medium">{{ supportLabel(record.pneu_serie_monte) }}</span>
                        </div>

                        <!-- N° série -->
                        <div class="flex items-center gap-1.5">
                            <span class="inline-block w-1.5 h-1.5 rounded-full bg-green-400 flex-shrink-0"></span>
                            <code class="text-xs font-mono text-gray-800 bg-gray-100 px-1.5 py-0.5 rounded">
                                {{ record.pneu_serie_monte.numero_serie }}
                            </code>
                        </div>

                        <!-- Position montée -->
                        <div v-if="record.position_monte" class="flex items-center gap-1.5 text-xs text-gray-500">
                            <font-awesome-icon icon="fa-location-dot" class="text-gray-300 w-3" />
                            {{ record.position_monte }}
                        </div>
                    </div>
                    <span v-else class="text-gray-300 text-sm">—</span>
                </template>

                <!-- ── Actions ────────────────────────────────────────────── -->
                <template v-else-if="column.key === 'actions'">
                    <div class="flex gap-1 justify-center">
                        <a-tooltip title="Modifier">
                            <a-button
                                v-if="can('pneu_remplacement.update')"
                                size="small"
                                type="text"
                                @click="() => refFormModal.edit(record)"
                            >
                                <font-awesome-icon icon="fa-pen" class="text-blue-500" />
                            </a-button>
                        </a-tooltip>
                        <a-tooltip title="Supprimer">
                            <a-popconfirm
                                v-if="can('pneu_remplacement.destroy')"
                                title="Supprimer cette opération ?"
                                ok-text="Oui"
                                cancel-text="Non"
                                @confirm="router.delete(route('pneu_remplacement.destroy', record.id))"
                            >
                                <a-button size="small" type="text">
                                    <font-awesome-icon icon="fa-trash" class="text-red-400" />
                                </a-button>
                            </a-popconfirm>
                        </a-tooltip>
                    </div>
                </template>

            </template>
        </DataTable>

        <PneuRemplacementForm
            ref="refFormModal"
            :vehicules="vehicules"
            :remorques="remorques"
            :magasins="magasins"
        />
    </SousMenuPrincipale>
</template>

<style scoped></style>
