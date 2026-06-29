<script setup>
import DataTable from "@/Components/DataTable.vue";
import FilterBase from "@/Components/Filter/FilterBase.vue";
import SousMenuPrincipale from "@/Pages/Menu/SousMenuPrincipale.vue";
import usePermissions from "@/UserPermissions/usePermissions.js";
import { DownOutlined } from "@ant-design/icons-vue";
import { router, useForm } from "@inertiajs/vue3";
import { Space } from "ant-design-vue";
import dayjs from "dayjs";
import { computed, ref } from "vue";
import { confirm_delete } from "../../../Utils/confirmation_modal.js";
import { createSearchFilter, gotoSearch } from "../../../Utils/FiltreUtils.js";

const { can } = usePermissions();

const STATUTS = [
    { value: 'en_attente', label: 'En attente' },
    { value: 'en_cours',   label: 'En cours'   },
    { value: 'resolue',    label: 'Résolue'     },
    { value: 'rejetee',    label: 'Rejetée'     },
];

const STATUT_STYLE = {
    en_attente: { backgroundColor: '#FEF3C7', color: '#92400E', borderColor: '#FDE68A' },
    en_cours:   { backgroundColor: '#DBEAFE', color: '#1E40AF', borderColor: '#BFDBFE' },
    resolue:    { backgroundColor: '#D1FAE5', color: '#065F46', borderColor: '#6EE7B7' },
    rejetee:    { backgroundColor: '#FEE2E2', color: '#991B1B', borderColor: '#FECACA' },
};

const CATEGORIES = {
    retard:                'Retard de livraison',
    casse:                 'Casse / Dommage',
    perte:                 'Perte de marchandise',
    mauvaise_manipulation: 'Mauvaise manipulation',
    autre:                 'Autre',
};

const props = defineProps({
    data:    { type: Object, default: () => ({}) },
    clients: { type: Array,  default: () => [] },
    filters: { type: Object, default: () => ({}) },
});

const filter = ref({
    ...createSearchFilter(),
    statut:     props.filters?.statut     || null,
    client_id:  props.filters?.client_id  || null,
    start_date: props.filters?.start_date || null,
    end_date:   props.filters?.end_date   || null,
});

const popoverVisible = ref(false);
const drawerVisible  = ref(false);
const selected       = ref(null);

const form = useForm({ statut: '', reponse: '' });

const openDrawer = (record) => {
    selected.value = record;
    form.statut    = record.statut;
    form.reponse   = record.reponse ?? '';
    drawerVisible.value = true;
};

const submitUpdate = () => {
    form.put(route('reclamation.update', selected.value.id), {
        preserveScroll: true,
        onSuccess: () => { drawerVisible.value = false; },
    });
};

const title = computed(() => `Réclamations (${props.data?.total ?? 0})`);

const columns = [
    {
        key: 'created_at', title: 'Date', width: 110,
        customCell:       () => ({ class: 'text-center' }),
        customHeaderCell: () => ({ class: '!text-center' }),
    },
    { key: 'numero_reclamation', title: 'N° Réclamation', dataIndex: 'numero_reclamation', width: 160 },
    { key: 'client',             title: 'Client',          width: 180 },
    { key: 'categorie',          title: 'Catégorie',        width: 170 },
    { key: 'objet',              title: 'Objet' },
    {
        key: 'statut', title: 'Statut', width: 140,
        customCell:       () => ({ class: 'text-center' }),
        customHeaderCell: () => ({ class: '!text-center' }),
    },
    { key: 'voyage',     title: 'Voyage', width: 130 },
];

const actions = [
    {
        text:      'Voir / Traiter',
        icon:      'fa-eye',
        privilege: 'reclamation.update',
        action:    (record) => openDrawer(record),
    },
    {
        text:      'Supprimer',
        icon:      'fa-trash',
        class:     '!text-red-600',
        privilege: 'reclamation.destroy',
        action:    (record) =>
            confirm_delete(() =>
                router.delete(route('reclamation.destroy', record.id), { preserveScroll: true })
            ),
    },
];

const applyFilters = () => {
    popoverVisible.value = false;
    gotoSearch(filter.value, route('reclamation.index'));
};

const resetFilters = () => {
    filter.value = { ...createSearchFilter(), statut: null, client_id: null, start_date: null, end_date: null };
    gotoSearch(filter.value, route('reclamation.index'));
};

const statutLabel    = (s) => STATUTS.find((x) => x.value === s)?.label ?? s;
const statutStyle    = (s) => STATUT_STYLE[s] ?? STATUT_STYLE.en_attente;
const categorieLabel = (c) => CATEGORIES[c] ?? c;
</script>

<template>
    <SousMenuPrincipale :title="title" selectedMenu="reclamation" v-if="can('reclamation.index')">
        <template #top>
            <FilterBase v-model="filter" @search="applyFilters" @reset="resetFilters">
                <template #otherFilter>
                    <a-popover placement="bottomRight" trigger="click" v-model:open="popoverVisible">
                        <template #content>
                            <div class="bg-white p-4 w-72 space-y-3 rounded-md">
                                <a-date-picker
                                    v-model:value="filter.start_date"
                                    size="large"
                                    format="DD/MM/YYYY"
                                    value-format="YYYY-MM-DD"
                                    placeholder="Date début"
                                    allow-clear
                                    class="w-full"
                                />
                                <a-date-picker
                                    v-model:value="filter.end_date"
                                    size="large"
                                    format="DD/MM/YYYY"
                                    value-format="YYYY-MM-DD"
                                    placeholder="Date fin"
                                    allow-clear
                                    class="w-full"
                                />
                                <a-select
                                    v-model:value="filter.statut"
                                    placeholder="Statut"
                                    class="w-full"
                                    size="large"
                                    allow-clear
                                >
                                    <a-select-option v-for="s in STATUTS" :key="s.value" :value="s.value">
                                        {{ s.label }}
                                    </a-select-option>
                                </a-select>

                                <a-select
                                    v-model:value="filter.client_id"
                                    placeholder="Client"
                                    class="w-full"
                                    size="large"
                                    allow-clear
                                    show-search
                                    option-filter-prop="label"
                                >
                                    <a-select-option
                                        v-for="c in clients"
                                        :key="c.value"
                                        :value="c.value"
                                        :label="c.label"
                                    >{{ c.label }}</a-select-option>
                                </a-select>



                                <div class="flex gap-2 !mt-4">
                                    <a-button block type="primary" size="middle" @click="applyFilters">Appliquer</a-button>
                                    <a-button block type="default" size="middle" @click="popoverVisible = false">Fermer</a-button>
                                </div>
                            </div>
                        </template>

                        <a-button size="large" type="default" class="!rounded-none border-r-0 focus:z-10">
                            <Space>
                                <font-awesome-icon icon="fa-solid fa-filter" class="text-[15px]" />
                                Filtres
                                <DownOutlined />
                            </Space>
                        </a-button>
                    </a-popover>
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
                <template v-if="column.key === 'client'">
                    <span class="font-medium text-gray-700">{{ record.client?.nom_client ?? '—' }}</span>
                </template>

                <template v-else-if="column.key === 'categorie'">
                    <span class="text-xs text-gray-600">{{ categorieLabel(record.categorie) }}</span>
                </template>

                <template v-else-if="column.key === 'statut'">
                    <span
                        class="inline-flex items-center text-xs font-semibold px-2.5 py-0.5 rounded-full border"
                        :style="statutStyle(record.statut)"
                    >{{ statutLabel(record.statut) }}</span>
                </template>

                <template v-else-if="column.key === 'voyage'">
                    <span v-if="record.voyage?.numero_voyage" class="text-xs text-blue-500 flex items-center gap-1">
                        <font-awesome-icon icon="fa-solid fa-truck" class="text-[10px]" />
                        {{ record.voyage.numero_voyage }}
                    </span>
                    <span v-else class="text-gray-300 text-xs">—</span>
                </template>

                <template v-else-if="column.key === 'created_at'">
                    {{ dayjs(record.created_at).format('DD/MM/YYYY') }}
                </template>
            </template>
        </DataTable>

        <!-- Drawer : détail + traitement admin -->
        <a-drawer
            v-model:open="drawerVisible"
            title="Réclamation"
            placement="right"
            :width="540"
            destroy-on-close
        >
            <template v-if="selected">
                <!-- En-tête -->
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-10 h-10 rounded-xl bg-rose-50 flex items-center justify-center flex-shrink-0">
                        <font-awesome-icon icon="fa-solid fa-triangle-exclamation" class="text-rose-500" />
                    </div>
                    <div>
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="font-bold text-gray-800">{{ selected.numero_reclamation }}</span>
                            <span
                                class="inline-flex items-center text-xs font-semibold px-2 py-0.5 rounded-full border"
                                :style="statutStyle(selected.statut)"
                            >{{ statutLabel(selected.statut) }}</span>
                        </div>
                        <p class="text-xs text-gray-400 mt-0.5">
                            {{ dayjs(selected.created_at).format('DD/MM/YYYY') }}
                            <span v-if="selected.client"> · {{ selected.client.nom_client }}</span>
                        </p>
                    </div>
                </div>

                <a-divider class="!my-3" />

                <!-- Métadonnées -->
                <div class="grid grid-cols-2 gap-3 mb-4 text-sm">
                    <div>
                        <p class="text-xs text-gray-400 mb-0.5">Catégorie</p>
                        <p class="font-medium text-gray-700">{{ categorieLabel(selected.categorie) }}</p>
                    </div>
                    <div v-if="selected.voyage">
                        <p class="text-xs text-gray-400 mb-0.5">Voyage</p>
                        <p class="font-medium text-blue-600 flex items-center gap-1">
                            <font-awesome-icon icon="fa-solid fa-truck" class="text-[10px]" />
                            {{ selected.voyage.numero_voyage }}
                        </p>
                    </div>
                </div>

                <!-- Objet -->
                <div class="mb-4">
                    <p class="text-xs text-gray-400 mb-1">Objet</p>
                    <p class="font-semibold text-gray-800">{{ selected.objet }}</p>
                </div>

                <!-- Description -->
                <div class="mb-4">
                    <p class="text-xs text-gray-400 mb-1">Description</p>
                    <p class="text-sm text-gray-700 bg-gray-50 rounded-lg p-3 whitespace-pre-line">
                        {{ selected.description }}
                    </p>
                </div>

                <!-- Images -->
                <div v-if="selected.images?.length" class="mb-5">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3 flex items-center gap-2">
                        <font-awesome-icon icon="fa-solid fa-images" />
                        Photos jointes ({{ selected.images.length }})
                    </p>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                        <a
                            v-for="img in selected.images"
                            :key="img.id"
                            :href="img.url"
                            target="_blank"
                            class="block aspect-square rounded-xl overflow-hidden border border-gray-100 hover:border-secondary transition-colors group"
                        >
                            <img
                                :src="img.url"
                                :alt="img.nom_original"
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-200"
                            />
                        </a>
                    </div>
                </div>

                <a-divider class="!my-4" />

                <!-- Formulaire admin -->
                <div class="space-y-4">
                    <h3 class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                        <font-awesome-icon icon="fa-solid fa-pen-to-square" class="text-secondary" />
                        Traitement admin
                    </h3>

                    <div>
                        <label class="text-xs font-medium text-gray-500 block mb-1.5">Statut</label>
                        <a-select v-model:value="form.statut" class="w-full" size="large">
                            <a-select-option v-for="s in STATUTS" :key="s.value" :value="s.value">
                                {{ s.label }}
                            </a-select-option>
                        </a-select>
                    </div>

                    <div>
                        <label class="text-xs font-medium text-gray-500 block mb-1.5">
                            Réponse / Commentaire admin
                        </label>
                        <a-textarea
                            v-model:value="form.reponse"
                            :rows="4"
                            placeholder="Saisir une réponse pour le client…"
                        />
                    </div>

                    <a-button
                        type="primary"
                        block
                        size="large"
                        :loading="form.processing"
                        @click="submitUpdate"
                    >
                        <font-awesome-icon icon="fa-solid fa-floppy-disk" class="mr-1.5" />
                        Enregistrer
                    </a-button>
                </div>
            </template>
        </a-drawer>

    </SousMenuPrincipale>
</template>
