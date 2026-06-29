<script setup>
import FormItem from "@/Components/FormItem.vue";
import FormModal from "@/Components/FormModal.vue";
import { useForm } from "@inertiajs/vue3";
import { ref, computed } from "vue";
import dayjs from "dayjs";
import axios from "axios";

const props = defineProps({
    salaries: { type: Array, default: () => [] },
});

const form = useForm({
    formation_id: null,
    date_formation: dayjs().format("YYYY-MM-DD"),
    observation: null,
    salarie_ids: [],
});

const open = ref(false);
const title = ref("");
const selectedFormation = ref(null);
const loadingPreselect = ref(false);

const filteredSalaries = computed(() => {
    return props.salaries;
});

const close = () => {
    open.value = false;
    form.reset();
    form.clearErrors();
    form.date_formation = dayjs().format("YYYY-MM-DD");
    selectedFormation.value = null;
};

const openForm = (formation) => {
    selectedFormation.value = formation;
    form.formation_id = formation.id;
    form.date_formation = dayjs().format("YYYY-MM-DD");
    form.salarie_ids = [];

    // 🔁 Pré-sélection : chercher la formation PRÉCÉDENTE (A) pour récupérer ses participants
    preselectParticipants(formation);
    title.value = `Nouvelle Session : ${formation.nom}`;
    open.value = true;
};

const preselectParticipants = async (formation) => {
    loadingPreselect.value = true;
    try {
        // Récupérer les participants de la formation PRÉCÉDENTE (A → B)
        const { data } = await axios.get(route("formations.participants.precedente", formation.id));
        if (data?.participants?.length > 0) {
            form.salarie_ids = data.participants.map((p) => p.id);
        }
    } catch (e) {
        // Silencieux
    } finally {
        loadingPreselect.value = false;
    }
};

const submit = () => {
    form.clearErrors();

    form.post(route("formations.sessions.store"), {
        onSuccess: () => close(),
    });
};

const selectAll = () => {
    form.salarie_ids = props.salaries.map((s) => s.id);
};

const deselectAll = () => {
    form.salarie_ids = [];
};

defineExpose({ open: openForm, close });
</script>

<template>
    <FormModal
        v-model:open="open"
        :loading="form.processing"
        @close="close"
        @submit="submit"
        :titre="title"
        size="lg"
        okText="Enregistrer la Session"
    >
        <div class="space-y-4 px-2">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <form-item
                    required
                    has-feedback
                    label="Date de la Formation"
                    :help="form.errors.date_formation"
                >
                    <a-date-picker
                        v-model:value="form.date_formation"
                        class="w-full"
                        size="large"
                        value-format="YYYY-MM-DD"
                    />
                </form-item>

                <form-item
                    has-feedback
                    label="Observation"
                    :help="form.errors.observation"
                >
                    <a-input
                        v-model:value="form.observation"
                        placeholder="Observation éventuelle"
                        size="large"
                    />
                </form-item>
            </div>

            <div class="border-t border-gray-100 pt-4">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-2">
                        <font-awesome-icon icon="fa-solid fa-users" class="text-primary" />
                        <h4 class="text-base font-bold text-gray-800">
                            Participants ({{ form.salarie_ids.length }} sélectionné(s))
                        </h4>
                    </div>
                    <div class="flex gap-2">
                        <a-button size="small" @click="selectAll">
                            <font-awesome-icon icon="fa-solid fa-check-double" class="mr-1" />
                            Tout sélectionner
                        </a-button>
                        <a-button size="small" @click="deselectAll">
                            <font-awesome-icon icon="fa-solid fa-times" class="mr-1" />
                            Tout désélectionner
                        </a-button>
                    </div>
                </div>

                <div v-if="loadingPreselect" class="py-4 text-center">
                    <a-spin size="small" />
                    <span class="ml-2 text-sm text-gray-400">Pré-sélection des participants...</span>
                </div>

                <div v-else class="border border-gray-200 rounded-lg max-h-80 overflow-y-auto">
                    <div v-if="filteredSalaries.length === 0" class="py-8 text-center text-gray-400">
                        <font-awesome-icon icon="fa-solid fa-users-slash" class="text-2xl mb-2" />
                        <p>Aucun salarié disponible</p>
                    </div>

                    <div
                        v-for="salarie in filteredSalaries"
                        :key="salarie.id"
                        class="flex items-center gap-3 px-4 py-2.5 hover:bg-gray-50 border-b border-gray-50 last:border-b-0 transition-colors cursor-pointer"
                        @click="() => {
                            const idx = form.salarie_ids.indexOf(salarie.id);
                            if (idx === -1) form.salarie_ids.push(salarie.id);
                            else form.salarie_ids.splice(idx, 1);
                        }"
                    >
                        <a-checkbox :checked="form.salarie_ids.includes(salarie.id)" />
                        <div class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center text-primary text-xs font-bold">
                            {{ salarie.prenom?.charAt(0) ?? '?' }}{{ salarie.nom?.charAt(0) ?? '' }}
                        </div>
                        <div class="flex-1">
                            <span class="text-sm font-semibold text-gray-700">
                                {{ salarie.nom }} {{ salarie.prenom ?? '' }}
                            </span>
                            <span class="text-xs text-gray-400 ml-2">({{ salarie.matricule }})</span>
                        </div>
                    </div>
                </div>
            </div>

            <div v-if="selectedFormation" class="bg-blue-50 p-3 rounded-lg border border-blue-100">
                <p class="text-xs text-blue-700">
                    <font-awesome-icon icon="fa-solid fa-info-circle" class="mr-1" />
                    La prochaine échéance sera automatiquement calculée : 
                    <strong>{{ selectedFormation.periode_renouvellement_mois }} mois</strong> après la date de formation.
                    Une alerte sera envoyée <strong>{{ selectedFormation.alerte_avant_jours }} jours</strong> avant.
                </p>
            </div>
        </div>
    </FormModal>
</template>
