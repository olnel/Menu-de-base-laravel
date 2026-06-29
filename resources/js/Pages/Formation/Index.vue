<script setup>
import ButtonIcon from "@/Components/ButtonIcon.vue";
import DataTable from "@/Components/DataTable.vue";
import FilterBase from "@/Components/Filter/FilterBase.vue";
import SousMenuPrincipale from "@/Pages/Menu/SousMenuPrincipale.vue";
import FormationForm from "@/Pages/Formation/Form/FormationForm.vue";
import SessionFormationForm from "@/Pages/Formation/Form/SessionFormationForm.vue";
import usePermissions from "@/UserPermissions/usePermissions.js";
import { router } from "@inertiajs/vue3";
import { computed, ref } from "vue";
import axios from "axios";
import { confirm_delete } from "../../../Utils/confirmation_modal.js";
import { createSearchFilter, gotoSearch } from "../../../Utils/FiltreUtils.js";

const { can } = usePermissions();

const props = defineProps({
    data: { type: Object, default: () => ({}) },
    salaries: { type: Array, default: () => [] },
    filters: { type: Object, default: () => ({}) },
    flash: { type: Object, default: () => ({}) },
});

const formModal = ref();
const sessionModal = ref();
const detailModal = ref(false);
const selectedFormation = ref(null);
const formationDetails = ref(null);
const loadingDetails = ref(false);

const title = computed(() => `Formations (${props.data?.total ?? 0})`);

const filter = ref(createSearchFilter());

const columns = [
    {
        key: "nom",
        title: "Formation",
        dataIndex: "nom",
        customCell: () => ({ class: "!font-bold !text-primary" }),
        width: "300px",
    },
    {
        key: "description",
        title: "Description",
        dataIndex: "description",
        width: "300px",
        customRender: ({ text }) => text ?? "-",
    },
    {
        key: "periode_renouvellement_mois",
        title: "Renouvellement",
        align: "center",
        width: "150px",
        dataIndex: "periode_renouvellement_mois",
        customRender: ({ text }) => {
            if (text === 1) return "1 mois";
            if (text >= 12) {
                const ans = Math.floor(text / 12);
                const mois = text % 12;
                if (mois === 0) return `${ans} an${ans > 1 ? 's' : ''}`;
                return `${ans} an${ans > 1 ? 's' : ''} ${mois} mois`;
            }
            return `${text} mois`;
        },
    },
    {
        key: "alerte_avant_jours",
        title: "Alerte (Jours)",
        dataIndex: "alerte_avant_jours",
        align: "center",
        width: "130px",
        customRender: ({ text }) => `J-${text}`,
    },
    {
        key: "suivante",
        title: "Formation Suivante",
        width: "200px",
        customRender: ({ record }) => {
            return record.formation_suivante?.nom ?? "-";
        },
    },
    {
        key: "derniere_session",
        title: "Dernière Session",
        align: "center",
        width: "180px",
        customRender: ({ record }) => {
            return record.session_formations_count > 0
                ? `${record.session_formations_count} session(s)`
                : "Aucune";
        },
    },
];

const handleDelete = (record) => {
    confirm_delete(() => {
        router.delete(route("formations.destroy", record.id), {
            preserveScroll: true,
        });
    });
};

const showDetail = async (formation) => {
    selectedFormation.value = formation;
    loadingDetails.value = true;
    detailModal.value = true;

    try {
        const response = await axios.get(route("formations.derniere_session.participants", formation.id));
        formationDetails.value = response.data;
    } catch (e) {
        formationDetails.value = null;
    } finally {
        loadingDetails.value = false;
    }
};

// Impression par session
const printModal = ref(false);
const sessionsList = ref([]);
const selectedSessionId = ref(null);
const loadingSessions = ref(false);
const printingFormation = ref(null);

const openPrintModal = async (formation) => {
    printingFormation.value = formation;
    sessionsList.value = [];
    selectedSessionId.value = null;
    printModal.value = true;
    loadingSessions.value = true;

    try {
        const response = await axios.get(route("formations.sessions", formation.id));
        sessionsList.value = response.data;
        if (sessionsList.value.length > 0) {
            selectedSessionId.value = sessionsList.value[0].id;
        }
    } catch (e) {
        console.error("Erreur lors de la récupération des sessions :", e);
    } finally {
        loadingSessions.value = false;
    }
};

const handlePrint = () => {
    if (!selectedSessionId.value) return;
    window.open(route("formations.sessions.print", selectedSessionId.value), "_blank");
    printModal.value = false;
};

const formatDate = (dateStr) => {
    if (!dateStr) return "";
    const date = new Date(dateStr);
    if (isNaN(date.getTime())) return dateStr;
    return date.toLocaleDateString("fr-FR");
};

const actions = [
    {
        text: "Détails & Participants",
        action: (record) => showDetail(record),
        icon: "fa-users",
        class: "!text-blue-500",
        privilege: "formations.index",
    },
    {
        text: "Imprimer la liste des participants",
        action: (record) => openPrintModal(record),
        icon: "fa-print",
        class: "!text-gray-600",
        privilege: "formations.index",
        disabled: (record) => !record.session_formations_count || record.session_formations_count === 0,
    },
    {
        text: "Nouvelle Session",
        action: (record) => sessionModal.value.open(record),
        icon: "fa-calendar-plus",
        class: "!text-green-500",
        privilege: "formations.sessions.store",
        visible: (record) => record.id > 0,
    },
    {
        text: "Modifier",
        action: (record) => formModal.value.update(record),
        icon: "fa-pen-to-square",
        privilege: "formations.update",
    },
    {
        text: "Supprimer",
        action: handleDelete,
        icon: "fa-trash",
        class: "!text-red-600",
        disabled: (record) => props.data.total < 1,
        privilege: "formations.destroy",
    },
];

const applyFilters = (data) => {
    filter.value = data;
    const url = route("formations.index");
    gotoSearch(filter.value, url);
};

const resetFilters = () => {
    filter.value.search = "";
    applyFilters();
};
</script>

<template>
    <SousMenuPrincipale :title="title" selectedMenu="formation">
        <template #top>
            <FilterBase
                v-model="props.filters"
                @search="applyFilters"
                @reset="resetFilters"
            >
                <template #add>
                    <ButtonIcon
                        v-if="can('formations.store')"
                        @click="() => formModal.add()"
                        type="primary"
                        text="Nouvelle Formation"
                        icon="fa-plus"
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
            :btn_action="false"
        >
            <template #bodyCell="{ column, record, text }">
                <template v-if="column.key === 'derniere_session'">
                    <div class="flex items-center justify-center">
                        <a-tag v-if="record.session_formations_count > 0" color="blue">
                            {{ record.session_formations_count }} session(s)
                        </a-tag>
                        <a-tag v-else color="default">Aucune</a-tag>
                    </div>
                </template>
            </template>
        </DataTable>

        <!-- Modal Détails Formation -->
        <a-modal
            v-model:open="detailModal"
            :title="selectedFormation?.nom ?? 'Détails Formation'"
            width="800px"
            :footer="null"
            centered
            destroyOnClose
        >
            <div v-if="loadingDetails" class="py-10 text-center">
                <a-spin size="large" />
            </div>
            <div v-else-if="formationDetails" class="space-y-6">
                <!-- Infos formation -->
                <div class="bg-gray-50 p-4 rounded-lg border border-gray-100">
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <span class="text-xs font-bold text-gray-500 uppercase tracking-wider block mb-1">Renouvellement</span>
                            <span class="text-sm font-semibold text-gray-800">
                                {{ selectedFormation?.periode_renouvellement_mois >= 12
                                    ? `${Math.floor(selectedFormation.periode_renouvellement_mois / 12)} an(s)`
                                    : `${selectedFormation?.periode_renouvellement_mois} mois` }}
                            </span>
                        </div>
                        <div>
                            <span class="text-xs font-bold text-gray-500 uppercase tracking-wider block mb-1">Alerte</span>
                            <span class="text-sm font-semibold text-gray-800">{{ selectedFormation?.alerte_avant_jours }} jours avant</span>
                        </div>
                        <div v-if="formationDetails.formation?.formation_suivante">
                            <span class="text-xs font-bold text-gray-500 uppercase tracking-wider block mb-1">Formation Suivante</span>
                            <a-tag color="purple">{{ formationDetails.formation.formation_suivante.nom }}</a-tag>
                        </div>
                    </div>
                </div>

                <!-- Dernière session -->
                <div v-if="formationDetails.session">
                    <h4 class="text-sm font-bold text-gray-700 mb-3 flex items-center gap-2">
                        <font-awesome-icon icon="fa-solid fa-clock" class="text-primary" />
                        Dernière session : {{ formationDetails.session.date_formation }}
                    </h4>
                    <p v-if="formationDetails.session.observation" class="text-xs text-gray-500 mb-3 italic">
                        {{ formationDetails.session.observation }}
                    </p>

                    <!-- Prochaine date -->
                    <div v-if="formationDetails.session.date_prochaine_formation"
                         class="mb-4 p-3 bg-amber-50 rounded-lg border border-amber-200 flex items-center gap-3">
                        <font-awesome-icon icon="fa-solid fa-calendar-clock" class="text-amber-600 text-lg" />
                        <div>
                            <span class="text-xs font-bold text-amber-700 uppercase block">Prochaine échéance</span>
                            <span class="text-sm font-semibold text-amber-800">
                                {{ formationDetails.session.date_prochaine_formation }}
                            </span>
                        </div>
                    </div>

                    <!-- Participants -->
                    <h4 class="text-sm font-bold text-gray-700 mb-3 flex items-center gap-2">
                        <font-awesome-icon icon="fa-solid fa-users" class="text-primary" />
                        Participants ({{ formationDetails.participants?.length ?? 0 }})
                    </h4>

                    <div v-if="formationDetails.participants?.length > 0" class="space-y-2">
                        <div v-for="participant in formationDetails.participants"
                             :key="participant.id"
                             class="flex items-center gap-3 p-2 bg-white rounded-lg border border-gray-100 hover:border-primary/30 transition-colors"
                        >
                            <div class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center text-primary text-xs font-bold">
                                {{ participant.prenom?.charAt(0) ?? '?' }}{{ participant.nom?.charAt(0) ?? '' }}
                            </div>
                            <div>
                                <span class="text-sm font-semibold text-gray-700">
                                    {{ participant.nom }} {{ participant.prenom ?? '' }}
                                </span>
                                <span v-if="participant.matricule" class="text-xs text-gray-400 ml-2">({{ participant.matricule }})</span>
                            </div>
                        </div>
                    </div>
                    <div v-else class="py-6 text-center text-gray-400">
                        <font-awesome-icon icon="fa-solid fa-user-slash" class="text-2xl mb-2" />
                        <p class="text-sm">Aucun participant pour cette session</p>
                    </div>
                </div>
                <div v-else class="py-8 text-center text-gray-400">
                    <font-awesome-icon icon="fa-solid fa-calendar-xmark" class="text-3xl mb-3" />
                    <p class="text-sm font-medium">Aucune session enregistrée pour cette formation</p>
                </div>
            </div>
        </a-modal>

        <!-- Modal Choix de Session pour Impression -->
        <a-modal
            v-model:open="printModal"
            :title="`Imprimer la liste des participants : ${printingFormation?.nom ?? ''}`"
            width="500px"
            centered
            destroyOnClose
        >
            <div v-if="loadingSessions" class="py-10 text-center">
                <a-spin size="large" />
                <p class="text-xs text-gray-400 mt-2">Chargement des sessions...</p>
            </div>
            <div v-else class="space-y-4 py-4">
                <div v-if="sessionsList.length === 0" class="text-center py-6 text-gray-400">
                    <font-awesome-icon icon="fa-solid fa-calendar-xmark" class="text-3xl mb-2" />
                    <p class="text-sm">Aucune session enregistrée pour cette formation.</p>
                </div>
                <div v-else class="space-y-3">
                    <label class="text-sm font-bold text-gray-700 block">Choisir la session à imprimer :</label>
                    <a-select
                        v-model:value="selectedSessionId"
                        class="w-full"
                        size="large"
                        placeholder="Sélectionnez une session"
                    >
                        <a-select-option
                            v-for="session in sessionsList"
                            :key="session.id"
                            :value="session.id"
                        >
                            Session du {{ formatDate(session.date_formation) }} {{ session.observation ? ` - ${session.observation}` : '' }}
                        </a-select-option>
                    </a-select>
                </div>
            </div>
            <template #footer>
                <div class="flex justify-end gap-2">
                    <a-button @click="printModal = false">Annuler</a-button>
                    <a-button
                        type="primary"
                        :disabled="!selectedSessionId || sessionsList.length === 0"
                        @click="handlePrint"
                    >
                        <font-awesome-icon icon="fa-solid fa-print" class="mr-1" />
                        Imprimer
                    </a-button>
                </div>
            </template>
        </a-modal>

        <!-- Modal Formulaire Formation -->
        <FormationForm
            ref="formModal"
            :formations="data.data"
        />

        <!-- Modal Nouvelle Session -->
        <SessionFormationForm
            ref="sessionModal"
            :salaries="salaries"
        />
    </SousMenuPrincipale>
</template>

<style scoped>
</style>
