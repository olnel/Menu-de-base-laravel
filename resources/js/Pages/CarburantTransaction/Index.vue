<template>
    <SousMenuPrincipale :title="title" selectedMenu="carburant-transactions">
        <template #top>
            <FilterBase
            v-model="filter"
            @search="applyFilters"
            @reset="resetFilters"
        >
            <template #add>
                <ButtonIcon
                    v-if="can('carburant_transactions.store')"
                    @click="() => formModal.add()"
                    type="primary"
                    text="Nouvelle Transaction Carburant"
                    icon="fa-plus"
                    :show-index="true"
                    class="!rounded-md hover:!text-white hover:!bg-sky-800 transition-all duration-300"
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
                                v-model:value="filter.type_mvmt"
                                placeholder="Type de transaction"
                                class="w-full"
                                size="large"
                                allow-clear
                            >
                                <a-select-option value="achat_carte"
                                    >Achat par Carte</a-select-option
                                >
                                <a-select-option value="achat_espece"
                                    >Achat par Espèce</a-select-option
                                >
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

                            <a-select
                                v-model:value="filter.carburant_card_id"
                                placeholder="Carte Carburant"
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
                                    v-for="card in carburantCards"
                                    :key="card.value"
                                    :value="card.value"
                                    :label="card.label"
                                >
                                    {{ card.label }}
                                </a-select-option>
                            </a-select>

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
                                v-model:value="filter.chauffeur_id"
                                placeholder="Chauffeur"
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
                                    v-for="chauffeur in chauffeurs"
                                    :key="chauffeur.value"
                                    :value="chauffeur.value"
                                    :label="chauffeur.label"
                                >
                                    {{ chauffeur.label }}
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
            </template>
        </FilterBase>
        </template>

        <BaseDataTable
            :columns="columns"
            :data="data"
            :actions="actions"
            class="main-shadow"
            :show-index="true"
            :btn_action="false"
        >
        </BaseDataTable>
        <FormulaireCarburantTransaction
            ref="formModal"
            :carburant-cards="carburantCards"
            :chauffeurs="chauffeurs"
            :vehicules="vehicules"
            :users="users"
        />
    </SousMenuPrincipale>
</template>

<script setup>
import ButtonIcon from "@/Components/ButtonIcon.vue";
import BaseDataTable from "@/Components/DataTable.vue";
import FilterBase from "@/Components/Filter/FilterBase.vue";
import SousMenuPrincipale from "@/Pages/Menu/SousMenuPrincipale.vue";
import usePermissions from "@/UserPermissions/usePermissions.js";
import { DownOutlined } from "@ant-design/icons-vue";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { router } from "@inertiajs/vue3";
import dayjs from "dayjs";
import { computed, ref } from "vue";
import { confirm_delete } from "../../../Utils/confirmation_modal.js";
import { createSearchFilter, gotoSearch } from "../../../Utils/FiltreUtils.js";
import FormulaireCarburantTransaction from "./Formulaire/FormulaireCarburantTransaction.vue";

const props = defineProps({
    data: { type: Object,default: () => ({})},
    filters: { type: Object,default: () => ({})},
    carburantCards: { type: Array,default: ()=>[]},
    vehicules: { type: Array,default: ()=>[]},
    chauffeurs: { type: Array,default: ()=>[]},
    users: { type: Array,default: ()=>[]},
});

const title = computed( () => `Liste Des Transactions Carburant (${props.data?.total ?? 0})`);

const formModal = ref();
const dropdownVisible = ref(false);

const filter = ref({
    ...createSearchFilter(),
    type_mvmt: props.filters.type_mvmt || null,
    user_id: props.filters.user_id || null,
    start_date: props.filters.start_date || null,
    end_date: props.filters.end_date || null,
    carburant_card_id: props.filters.carburant_card_id || null,
    vehicule_id: props.filters.vehicule_id || null,
    chauffeur_id: props.filters.chauffeur_id || null,
});

const dateRange = ref( filter.value.start_date && filter.value.end_date ? [dayjs(filter.value.start_date), dayjs(filter.value.end_date)] : [] );

const handleDateRangeChange = (dates, dateStrings) => {
    filter.value.start_date = dateStrings[0];
    filter.value.end_date = dateStrings[1];
};
const applyFilters = () => {
    const url = route("carburant_transactions.index");
    gotoSearch(filter.value, url);
    closeDropdown();
};
const resetFilters = () => {
    filter.value = {...createSearchFilter(),start_date: null,end_date: null, type_mvmt: null, user_id: null, carburant_card_id: null, vehicule_id: null, chauffeur_id: null};
    dateRange.value = [];
    applyFilters();
};

const { can } = usePermissions();
const closeDropdown = () => {dropdownVisible.value = false};

const handleDelete = (record) => {confirm_delete(() => { router.delete(route("carburant_transactions.destroy", record.id), { preserveScroll: true})})};

const columns = [
    { key: "date_transa",title: "Date-trans°",width: 100,
        customRender: ({ text }) =>text ? dayjs(text.date_transaction).format("DD/MM/YYYY") : "-",
        customCell: () => ({ class: "!text-center" }),
        customHeaderCell: () => ({ class: "!text-center whitespace-nowrap" }),
    },
    { key: "transa_type",title: "Type",dataIndex: "type",width:100,
        customCell: () => ({ class: "!text-center" }),
        customHeaderCell: () => ({ class: "!text-center whitespace-nowrap" }),
    },
    { key: "reference",title: "reference",width: 100,
        customRender: ({ record }) => record.reference ?? "-",
        customCell: () => ({ class: "!text-center" }),
        customHeaderCell: () => ({ class: "!text-center whitespace-nowrap" }),
    },
    { key: "numero_carte",title: "N° Carte",dataIndex: "carburant_card",width: 100,
        customRender: ({ record }) => (record.card ? record.card : "-"),
        customCell: () => ({ class: "!text-center" }),
        customHeaderCell: () => ({ class: "!text-center whitespace-nowrap" }),
    },
    { key: "chauffeur_assoc",title: "chauffeur",width: 100,
        customRender: ({ record }) => record.nom_chauffeur ? record.nom_chauffeur : "-",
        customCell: () => ({ class: "!text-left" }),
        customHeaderCell: () => ({ class: "!text-left whitespace-nowrap" }),
    },
    { key: "vehicule_assoc",title: "vehicule",width: 100,
        customRender: ({ record }) => record.matricule_vehicule ? record.matricule_vehicule : "-",
        customCell: () => ({ class: "!text-left" }),
        customHeaderCell: () => ({ class: "!text-left whitespace-nowrap" }),
    },
    { key: "montant",title: "Montant",dataIndex: "montant",
        customCell: () => ({ class: "text-right" }),
        customHeaderCell: () => ({ class: "!text-right whitespace-nowrap" }),
        customRender: ({ text }) => new Intl.NumberFormat().format(text),
    },
    { key: "commentaire",title: "commentaire",width: 100,
        customRender: ({ record }) => record.commentaire ? record.commentaire : "-",
    },
    { key: "nom_user",title: "Utilisateur",width: 100,
        customRender: ({ record }) => record.nom_user || "-",
    },
    { key: "date_enregistrement",title: "Date-enreg",dataIndex: "date_heure_enregistrement",width: 100,
        customRender: ({ text }) => text ? dayjs(text).format("DD/MM/YYYY HH:MM") : "-",
        customCell: () => ({ class: "!text-center" }),
        customHeaderCell: () => ({ class: "!text-center whitespace-nowrap" }),
    },
];

const actions = [
    { text: "Modifier",action: (record) => formModal.value.update(record),icon: "fa-pen-to-square",privilege: "carburant_transactions.update"},
    { text: "Supprimer",action: handleDelete,class: "!text-red-600",icon: "fa-trash", privilege: "carburant_transactions.destroy"},
];
</script>

<style scoped></style>
