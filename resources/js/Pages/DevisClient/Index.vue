<script setup>
import SousMenuPrincipale from "@/Pages/Menu/SousMenuPrincipale.vue";
import DataTable from "@/Components/DataTable.vue";
import ButtonIcon from "@/Components/ButtonIcon.vue";
import { computed, reactive, ref } from "vue";
import { confirm_delete } from "../../../Utils/confirmation_modal.js";
import { router } from "@inertiajs/vue3";
import FilterBase from "@/Components/Filter/FilterBase.vue";
import { createSearchFilter, gotoSearch } from "../../../Utils/FiltreUtils.js";
import usePermissions from "@/UserPermissions/usePermissions.js";
import { DownOutlined } from "@ant-design/icons-vue";
import {message, Space} from "ant-design-vue";
import dayjs from "dayjs";
import DevisClientFormulaire from "@/Pages/DevisClient/Formulaire/DevisClientFormulaire.vue";

const { can } = usePermissions();
const dropdownVisible = ref(false);
const dateFormat = 'DD/MM/YYYY'; // Format plus standard pour la France
const dateTimeFormat = 'DD/MM/YYYY HH:mm'; // Format avec heures et minutes

const props = defineProps({
    data: {
        type: Object,
        default: () => ({})
    },
    magasins: {
        type: Array,
        default: []
    },
    filters: {
        type: Object,
        default: () => ({})
    },
    flash: {
        type: Object,
        default: () => ({})
    },
    list_clients: {
        type: Array,
        default: () => ([])
    },
    errors: Object
});

const refFormModal = ref();

const filter = ref({ ...createSearchFilter(), start_date: null, end_date: null, nom_client: null });

const title = computed(() => `Liste des Devis (${props.data?.total ?? 0})`);

const columns = [
    {
        key: "date_devis",
        title: "Date devis",
        dataIndex: "date_devis",
        width: 100,
        customCell: () => ({ class: 'text-center' }),
        customHeaderCell:()=>({class:'!text-center'})
    },
    {
        key: "numero_devis",
        title: "Numero",
        dataIndex: "numero_devis",
        width: 100,
        customCell: () => ({ class: 'text-center' }),
        customHeaderCell:()=>({class:'!text-center'})
    },
    {
        key: "nom_client",
        title: "Client",
        dataIndex: "nom_client",
        width: 120
    },
    {
        key: "validite_devis",
        title: "Validité devis",
        dataIndex: "validite_devis",
        width: 100,
        customCell: () => ({ class: 'text-center' }),
        customHeaderCell:()=>({class:'!text-center'})
    },
    {
        key: "condition_delais",
        title: "Délais",
        dataIndex: "condition_delais",
        width: 100
    },
    {
        key: "condition_paiement",
        title: "Paiement",
        dataIndex: "condition_paiement",
        width: 100
    },
    {
        key: "montant_total",
        title: "Total",
        dataIndex: "montant_total",
        width: 100,
        customCell: () => ({ class: 'text-right' }),
        customHeaderCell:()=>({class:'!text-right'})
    },
    {
        key: "nom_user",
        title: "Utilisateur",
        dataIndex: "nom_user",
        width: 100
    },
    {
        key: "created_at",
        title: "Date création",
        dataIndex: "created_at",
        width: 120,
    },
];

function printBonCommande(record) {

    const link = document.createElement('a');
    link.href = route('devisclient.print', { devisclient: record.id });
    link.target = '_blank';
    link.download = `devis-${record.numero_devis}.pdf`;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

const actions = [
    {
        text: "Modifier",
        action: (record) => refFormModal.value.update(record),
        icon: 'fa-pen-to-square',
        privilege: 'devisclient.update'
    },
    {
        text: "Impression Devis",
        action: (record) =>printBonCommande(record),
        icon: 'fa-print',
        privilege: 'devisclient.impression'
    },
    {
        text: "Envoi mail Client",
        action: (record) =>sendMailToClient(record),
        icon: 'fa-paper-plane',
        privilege: 'devisclient.envoi_email'
    },
    {
        text: "Supprimer",
        action: (record) => confirm_delete(() => {
            router.delete(route('devisclient.destroy', record.id), {
                preserveScroll: true,
            });
        }),
        icon: 'fa-trash',
        class: "!text-red-600",
        privilege: 'devisclient.destroy'
    },
];

const applyFilters = (data) => {
    filter.value = data;
    const url = route('devisclient.index');
    gotoSearch(filter.value, url);
};

const resetFilters = () => {
    filter.value = { ...createSearchFilter(), start_date: null, end_date: null, nom_client: null };
    const url = route('devisclient.index');
    gotoSearch(filter.value, url);
};

const formatDate = (dateString) => {
    return dateString ? dayjs(dateString).format(dateFormat) : '-';
};

const formatDateTime = (dateString) => {
    return dateString ? dayjs(dateString).format(dateTimeFormat) : '-';
};

const sendMailToClient = (record) => {
    const url = route('devisclients.sendMail', { devisclient: record.id });
    router.visit(url, {
        preserveState: true,
        preserveScroll: true,
        only: ['flash'],
        onSuccess: (response) => {
            if (response.props.flash.type === 'success') {
                message.success(response.props.flash.message);
            } else {
                message.error(response.props.flash.message);
            }
        },
    });
}
</script>

<template>
    <SousMenuPrincipale :title="title" selectedMenu="devisclient">
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
                        @visibleChange="val => dropdownVisible = val"
                    >
                        <template #content>
                            <div class="bg-white p-4 w-96 space-y-2 rounded-md">
                                <a-date-picker
                                    v-model:value="filter.start_date"
                                    :format="dateFormat"
                                    size="large"
                                    class="w-full"
                                    placeholder="Du"
                                    :value-format="'DD-MM-YYYY'"
                                />
                                <a-date-picker
                                    v-model:value="filter.end_date"
                                    :format="dateFormat"
                                    size="large"
                                    class="w-full"
                                    placeholder="Au"
                                    :value-format="'DD-MM-YYYY'"
                                />
                                <a-select
                                    v-model:value="filter.nom_client"
                                    placeholder="Client"
                                    :options="props.list_clients"
                                    class="w-full"
                                    size="large"
                                    allow-clear
                                />
                                <div class="flex space-x-2 !mt-6">
                                    <a-button block type="primary" size="middle" @click="applyFilters(filter)">Appliquer</a-button>
                                    <a-button block type="default" size="middle" @click="dropdownVisible = false">Fermer</a-button>
                                </div>
                            </div>
                        </template>
                        <a-button size="large" type="default" class="!rounded-none border-r-0 focus:z-10">
                            <Space>
                                <font-awesome-icon class="text-[15px]" icon="fa-filter" />
                                Filtres
                                <DownOutlined />
                            </Space>
                        </a-button>
                    </a-popover>
                </template>

                <template #add>
                    <ButtonIcon
                        v-if="can('devisclient.store')"
                        @click="() => refFormModal.add()"
                        type="primary"
                        text="Nouveau Devis"
                        icon="fa-plus"
                        class="!rounded-md hover:!text-white hover:!bg-sky-800 transition-all duration-300"
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
            :btn_action="true"
        >
            <template #bodyCell="{ column, record }">
                <!-- Formatage des dates -->
                <template v-if="column.key === 'date_devis'">
                    {{ formatDate(record.date_devis) }}
                </template>

                <template v-if="column.key === 'validite_devis'">
                    {{ formatDate(record.validite_devis) }}
                </template>

                <template v-if="column.key === 'created_at'">
                    {{ formatDateTime(record.created_at) }}
                </template>

                <!-- Formatage des montants -->
                <template v-if="column.key === 'montant_total'">
                    {{ new Intl.NumberFormat('fr-FR').format(record.montant_total) }}
                </template>
            </template>
        </DataTable>

        <DevisClientFormulaire
            ref="refFormModal"
            :magasins="magasins"
            :clients="list_clients"
        />
    </SousMenuPrincipale>
</template>

<style scoped>
/* Styles spécifiques si nécessaire */
</style>
