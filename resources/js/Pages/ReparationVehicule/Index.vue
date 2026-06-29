<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import DataTable from "@/Components/DataTable.vue";
import ButtonIcon from "@/Components/ButtonIcon.vue";
import { computed, ref } from "vue";
import { DownOutlined } from "@ant-design/icons-vue";
import { confirm_delete } from "../../../Utils/confirmation_modal.js";
import { router } from "@inertiajs/vue3";
import { useCurrency } from "@/Composables/useCurrency.js";
import FilterBase from "@/Components/Filter/FilterBase.vue";
import { createSearchFilter, gotoSearch } from "../../../Utils/FiltreUtils.js";
import usePermissions from "@/UserPermissions/usePermissions.js";
import dayjs from "dayjs";
import MaintenanceCurativeForm from "./MaintenanceCurativeForm.vue";
import SousMenuPrincipale from "@/Pages/Menu/SousMenuPrincipale.vue";

const { can } = usePermissions();

const props = defineProps({
    data: {
        type: Object,
        default: () => ({}),
    },
    vehicules: Array,
    magasins: Array,
    remorques: Array,
    salaries: Array,
    articles: Array,
    filters: Object,
    flash: Object,
    errors: Object,
});

const refFormModal = ref();

const dropdownVisible = ref(false);

const filter = ref({
    ...createSearchFilter(),
    date_reparation: props.filters?.date_reparation || null,
    vehicule_id: props.filters?.vehicule_id || null,
    numero_remorque: props.filters?.numero_remorque || null,
    ...props.filters,
});

const title = computed(
    () => `Maintenance Curative (${props.data?.total ?? 0})`,
);

const columns = [
    {
        key: "date_reparation",
        title: "Date Début",
        dataIndex: "date_reparation",
        width: 150,
        customCell: () => ({ class: "text-left" }),
    },
    {
        key: "date_fin_reparation",
        title: "Date Fin",
        dataIndex: "date_fin_reparation",
        width: 150,
        customCell: () => ({ class: "text-left" }),
    },
    {
        key: "immatriculation",
        title: "Véhicule",
        dataIndex: "immatriculation",
        width: 150,
    },
    {
        key: "prix_total_pieces",
        title: "Total Pièces",
        dataIndex: "prix_total_pieces",
        width: 120,
        customCell: () => ({ class: "text-right" }),
    },
    {
        key: "prix_total_main_oeuvre",
        title: "Total M.O",
        dataIndex: "prix_total_main_oeuvre",
        width: 120,
        customCell: () => ({ class: "text-right" }),
    },
    {
        key: "montant_total",
        title: "Montant Total",
        dataIndex: "montant_total",
        width: 150,
        customCell: () => ({ class: "text-right" }),
    },
    // { key: "statut", title: "Statut", dataIndex: "statut", width: 100, customCell: () => ({ class: 'text-center' }) },
    
    {
        key: "responsable_name",
        title: "Responsable",
        dataIndex: "responsable_name",
        width: 150,
        customCell: () => ({ class: "text-center" }),
    },
    {
        key: "created_by_name",
        title: "Enregistré par",
        dataIndex: "created_by_name",
        width: 150,
        customHeaderCell: () => ({ class: "!text-center whitespace-nowrap" }),
        customCell: () => ({ class: "text-center" }),
    },
    {
        key: "date_enreg",
        title: "Date Enregistrement",
        dataIndex: "date_enreg",
        width: 200,
        customHeaderCell: () => ({ class: "!text-center whitespace-nowrap" }),
        customCell: () => ({ class: "text-center" }),
    },
];

const handleDelete = (record) => {
    confirm_delete(() => {
        router.delete(route("reparation_vehicule.destroy", record.id), {
            preserveScroll: true,
        });
    });
};

const actions = [
    {
        text: "Modifier",
        action: (record) => refFormModal.value.update(record),
        icon: "fa-pen-to-square",
        privilege: "reparation_vehicule.update",
    },
    {
        text: "Supprimer",
        action: handleDelete,
        icon: "fa-trash",
        class: "!text-red-600",
        privilege: "reparation_vehicule.destroy",
    },
    {
        text: "Tickets",
        action: (record) => generateTickets(record),
        icon: "fa-ticket-alt",
        privilege: "reparation_vehicule.update",
        visible: (record) => record.have_ticket,
    },
];

const loadingTickets = ref(false);

const generateTickets = (record) => {
    loadingTickets.value = true;
    router.post(
        route("reparation_vehicule.tickets.generate", record.id),
        {},
        {
            preserveScroll: true,
            onSuccess: () => {
                loadingTickets.value = false;
                // Ouvrir le PDF avec tous les tickets
                window.open(
                    route("reparation_vehicule.tickets.download_all", record.id),
                    "_blank"
                );
            },
            onError: () => {
                loadingTickets.value = false;
            },
        }
    );
};

const applyFilters = (data) => {
    filter.value = data;
    const url = route("reparation_vehicule.index");
    gotoSearch(filter.value, url);
    dropdownVisible.value = false;
};

const resetFilters = () => {
    filter.value.search = "";
    filter.value.date_reparation = null;
    filter.value.vehicule_id = null;
    filter.value.numero_remorque = null;
    const url = route("reparation_vehicule.index");
    gotoSearch(filter.value, url);
};

const { formatCurrency: formatCurrencyLocal } = useCurrency();
</script>

<template>
    <!-- <AuthenticatedLayout :title="title" selected-menu="reparation_vehicule" v-if="can('reparation_vehicule.index')"> -->
    <SousMenuPrincipale :title="title" selectedMenu="reparation_vehicule" v-if="can('reparation_vehicule.index')">
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
                            <a-date-picker
                                v-model:value="filter.date_reparation"
                                format="YYYY/MM/DD"
                                size="large"
                                class="w-full text-center"
                                placeholder="Date réparation"
                                value-format="YYYY-MM-DD"
                            />
                            <a-select
                                v-model:value="filter.vehicule_id"
                                placeholder="Véhicule"
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
                                    v-for="vehicule in vehicules"
                                    :key="vehicule.value"
                                    :value="vehicule.value"
                                    :label="vehicule.label"
                                >
                                    {{ vehicule.label }}
                                </a-select-option>
                            </a-select>
                            <a-select
                                v-model:value="filter.numero_remorque"
                                placeholder="Remorque"
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
                                    v-for="remorque in remorques"
                                    :key="remorque.numero_remorque"
                                    :value="remorque.numero_remorque"
                                    :label="remorque.numero_remorque"
                                >
                                    {{ remorque.numero_remorque }}
                                </a-select-option>
                            </a-select>
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
                    @click="() => refFormModal.add()"
                    type="primary"
                    text="Nouvelle Maintenance"
                    icon="fa-plus"
                    class="!rounded-md hover:!text-white hover:!bg-sky-800 transition-all duration-300"
                />
            </template>
        </FilterBase>

        <DataTable
            :columns="columns"
            :data="data"
            :actions="actions"
            class="main-shadow"
            :show-index="true"
            :btn_action="true"
            :expandable="{
                expandedRowRender: (record) => true,
            }"
        >
            <template #expandedRowRender="{ record }">
                <div class="p-4 bg-gray-50 rounded-lg">
                    <div
                        v-for="article in record.articles"
                        :key="article.id"
                        class="mb-4 bg-white p-3 rounded shadow-sm border"
                    >
                        <div
                            class="flex justify-between items-center border-b pb-2 mb-2"
                        >
                            <span class="font-bold text-blue-600">
                                {{
                                    article.is_consommable
                                        ? "Article Consommable"
                                        : article.is_remorque
                                          ? "Remorque: " +
                                            article.numero_remorque
                                          : "Véhicule Principal"
                                }}
                            </span>
                            <span class="text-gray-600">
                                Pièces:
                                {{ formatCurrencyLocal(article.prix_total_pieces) }}
                                | MO:
                                {{
                                    formatCurrencyLocal(
                                        article.prix_total_main_oeuvre,
                                    )
                                }}
                                | <span class="font-bold">TOTAL</span>:
                                {{ formatCurrencyLocal(article.montant_total) }}
                            </span>
                        </div>
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="text-left text-gray-500 border-b">
                                    <th class="py-1">
                                        Articles (Remplacé / Nouveau)
                                    </th>
                                    <th class="py-1">Magasin à retirer</th>
                                    <th class="py-1">Qte</th>
                                    <th class="py-1">P.U</th>
                                    <th class="py-1">Total</th>
                                    <th class="py-1">Technicien</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="detail in article.details"
                                    :key="detail.id"
                                    class="border-b last:border-0"
                                >
                                    <td class="py-1">
                                        <span
                                            class="text-red-500 line-through mr-1"
                                            v-if="
                                                detail.designation_article_changer
                                            "
                                        >
                                            {{
                                                detail.designation_article_changer
                                            }}
                                        </span>
                                        <span
                                            class="text-gray-400 mr-1"
                                            v-if="
                                                detail.designation_article_changer &&
                                                detail.designation_article
                                            "
                                            >/</span
                                        >
                                        <span
                                            class="text-green-600 font-medium"
                                        >
                                            {{ detail.designation_article }}
                                        </span>
                                    </td>
                                    <td class="py-1">
                                        {{ detail.magasin?.nom_magasin || "-" }}
                                    </td>
                                    <td class="py-1">
                                        {{ detail.quantite_article }}
                                    </td>
                                    <td class="py-1">
                                        {{
                                            formatCurrencyLocal(
                                                detail.prix_unitaire_article,
                                            )
                                        }}
                                    </td>
                                    <td class="py-1">
                                        {{
                                            formatCurrencyLocal(
                                                detail.montant_pieces_article,
                                            )
                                        }}
                                    </td>
                                    <td class="py-1">
                                        {{ detail.technicien }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </template>

            <template #bodyCell="{ column, record }">
                <template
                    v-if="
                        ['date_reparation', 'date_fin_reparation'].includes(
                            column.key,
                        )
                    "
                >
                    {{
                        record[column.key]
                            ? dayjs(record[column.key]).format(
                                  "DD/MM/YYYY à HH:mm",
                              )
                            : ""
                    }}
                </template>
                <template
                    v-if="
                        [
                            'prix_total_pieces',
                            'prix_total_main_oeuvre',
                            'montant_total',
                        ].includes(column.key)
                    "
                >
                    {{ formatCurrencyLocal(record[column.key]) }}
                </template>
                <template v-if="column.key === 'statut'">
                    <a-tag
                        :color="
                            record.statut === 'termine'
                                ? 'green'
                                : record.statut === 'en_cours'
                                  ? 'blue'
                                  : 'orange'
                        "
                    >
                        {{ record.statut.toUpperCase() }}
                    </a-tag>
                </template>
            </template>
        </DataTable>

        <MaintenanceCurativeForm
            ref="refFormModal"
            :vehicules="vehicules"
            :magasins="magasins"
            :remorques="remorques"
            :salaries="salaries"
            :articles="articles"
        />
    </SousMenuPrincipale>
</template>

<style scoped></style>
