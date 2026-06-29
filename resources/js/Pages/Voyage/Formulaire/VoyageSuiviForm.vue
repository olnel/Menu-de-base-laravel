<template>
    <div class="p-4 space-y-6">
        <!-- Statut du voyage -->
        <div>
            <h3 class="text-sm font-semibold text-gray-600 uppercase tracking-wider mb-4 flex items-center gap-2">
                <font-awesome-icon icon="fa-solid fa-route" class="text-primary" />
                Statut de l'expédition
            </h3>

            <div class="grid grid-cols-2 sm:grid-cols-5 gap-3">
                <button
                    v-for="s in STATUTS"
                    :key="s.value"
                    type="button"
                    @click="suiviForm.statut = s.value"
                    :class="[
                        'flex flex-col items-center gap-2 p-3 rounded-xl border-2 transition-all text-sm font-medium',
                        suiviForm.statut === s.value
                            ? 'border-transparent'
                            : 'border-gray-200 bg-white text-gray-500 hover:border-gray-300',
                    ]"
                    :style="suiviForm.statut === s.value
                        ? { borderColor: s.hex, backgroundColor: s.bgHex, color: s.hex }
                        : {}"
                >
                    <font-awesome-icon :icon="s.icon" class="text-lg" />
                    <span>{{ s.label }}</span>
                </button>
            </div>

            <div class="mt-4 flex items-center gap-2">
                <span class="text-xs text-gray-400">Statut actuel :</span>
                <span
                    class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold border"
                    :style="currentStatutStyle"
                >
                    <font-awesome-icon :icon="currentStatut.icon" />
                    {{ currentStatut.label }}
                </span>
            </div>
        </div>

        <a-divider class="!my-2" />

        <!-- Données opérationnelles -->
        <div>
            <h3 class="text-sm font-semibold text-gray-600 uppercase tracking-wider mb-4 flex items-center gap-2">
                <font-awesome-icon icon="fa-solid fa-chart-simple" class="text-primary" />
                Données opérationnelles
            </h3>

            <a-row :gutter="[16, 16]">
                <a-col :xs="24" :sm="12">
                    <FormItem label="Km parcourus" :help="suiviForm.errors?.km_parcouru">
                        <a-input-number
                            v-model:value="suiviForm.km_parcouru"
                            size="large"
                            class="w-full !bg-white"
                            placeholder="0"
                            :min="0"
                            :step="1"
                        >
                            <template #addonAfter>km</template>
                        </a-input-number>
                    </FormItem>
                </a-col>

                <a-col :xs="24" :sm="12">
                    <FormItem label="Poids transporté" :help="suiviForm.errors?.poids_transporte">
                        <a-input-number
                            v-model:value="suiviForm.poids_transporte"
                            size="large"
                            class="w-full !bg-white"
                            placeholder="0"
                            :min="0"
                            :step="0.1"
                        >
                            <template #addonAfter>tonnes</template>
                        </a-input-number>
                    </FormItem>
                </a-col>

                <a-col :xs="24" :sm="12">
                    <FormItem label="Heures facturables" :help="suiviForm.errors?.heures_facturables">
                        <a-input-number
                            v-model:value="suiviForm.heures_facturables"
                            size="large"
                            class="w-full !bg-white"
                            placeholder="0"
                            :min="0"
                            :step="0.5"
                        >
                            <template #addonAfter>h</template>
                        </a-input-number>
                    </FormItem>
                </a-col>

                <a-col :xs="24" :sm="12">
                    <FormItem label="Heures non facturables" :help="suiviForm.errors?.heures_non_facturables">
                        <a-input-number
                            v-model:value="suiviForm.heures_non_facturables"
                            size="large"
                            class="w-full !bg-white"
                            placeholder="0"
                            :min="0"
                            :step="0.5"
                        >
                            <template #addonAfter>h</template>
                        </a-input-number>
                    </FormItem>
                </a-col>
            </a-row>
        </div>

        <!-- Récapitulatif -->
        <div v-if="hasSuiviData" class="bg-gray-50 rounded-xl p-4 border border-gray-100">
            <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Récapitulatif</h4>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                <div v-if="suiviForm.km_parcouru" class="text-center">
                    <p class="text-2xl font-bold text-blue-600">{{ suiviForm.km_parcouru }}</p>
                    <p class="text-xs text-gray-400 mt-0.5">km parcourus</p>
                </div>
                <div v-if="suiviForm.poids_transporte" class="text-center">
                    <p class="text-2xl font-bold text-emerald-600">{{ suiviForm.poids_transporte }}</p>
                    <p class="text-xs text-gray-400 mt-0.5">tonnes</p>
                </div>
                <div v-if="suiviForm.heures_facturables" class="text-center">
                    <p class="text-2xl font-bold text-violet-600">{{ suiviForm.heures_facturables }}</p>
                    <p class="text-xs text-gray-400 mt-0.5">h facturables</p>
                </div>
                <div v-if="suiviForm.heures_non_facturables" class="text-center">
                    <p class="text-2xl font-bold text-orange-500">{{ suiviForm.heures_non_facturables }}</p>
                    <p class="text-xs text-gray-400 mt-0.5">h non facturables</p>
                </div>
            </div>
        </div>

        <!-- Bouton enregistrer -->
        <div v-if="can('voyages.update')" class="flex justify-end pt-2">
            <a-button type="primary" size="large" :loading="suiviForm.processing" @click="submit">
                <font-awesome-icon icon="fa-solid fa-floppy-disk" class="mr-1.5" />
                Enregistrer le suivi
            </a-button>
        </div>
    </div>
</template>

<script setup>
import FormItem from "@/Components/FormItem.vue";
import usePermissions from "@/UserPermissions/usePermissions";
import { useForm } from "@inertiajs/vue3";
import { computed, watch } from "vue";

const { can } = usePermissions();

const props = defineProps({
    voyageId:    { type: Number, default: null },
    initialSuivi:{ type: Object, default: () => ({}) },
});

const STATUTS = [
    { value: 'planifie', label: 'Planifié',  icon: 'fa-solid fa-clock',        hex: '#6366F1', bgHex: '#EEF2FF' },
    { value: 'en_route', label: 'En route',  icon: 'fa-solid fa-truck-moving',  hex: '#3B82F6', bgHex: '#EFF6FF' },
    { value: 'arrive',   label: 'Arrivé',    icon: 'fa-solid fa-location-dot',  hex: '#F59E0B', bgHex: '#FFFBEB' },
    { value: 'livre',    label: 'Livré',     icon: 'fa-solid fa-circle-check',  hex: '#10B981', bgHex: '#ECFDF5' },
    { value: 'annule',   label: 'Annulé',    icon: 'fa-solid fa-circle-xmark',  hex: '#EF4444', bgHex: '#FEF2F2' },
];

const suiviForm = useForm({
    statut:                 null,
    km_parcouru:            null,
    poids_transporte:       null,
    heures_facturables:     null,
    heures_non_facturables: null,
});

// Synchronise le formulaire quand les données initiales changent (ouverture d'un nouveau voyage)
watch(
    () => props.initialSuivi,
    (val) => {
        suiviForm.statut                 = val?.statut                 ?? 'planifie';
        suiviForm.km_parcouru            = val?.km_parcouru            ?? null;
        suiviForm.poids_transporte       = val?.poids_transporte       ?? null;
        suiviForm.heures_facturables     = val?.heures_facturables     ?? null;
        suiviForm.heures_non_facturables = val?.heures_non_facturables ?? null;
    },
    { immediate: true, deep: true }
);

const currentStatut = computed(() => STATUTS.find(s => s.value === suiviForm.statut) ?? STATUTS[0]);

const currentStatutStyle = computed(() => ({
    backgroundColor: currentStatut.value.bgHex,
    color:           currentStatut.value.hex,
    borderColor:     currentStatut.value.hex + '66',
}));

const hasSuiviData = computed(() =>
    suiviForm.km_parcouru || suiviForm.poids_transporte ||
    suiviForm.heures_facturables || suiviForm.heures_non_facturables
);

const submit = () => {
    suiviForm.patch(route('voyages.suivi', props.voyageId), {
        preserveScroll: true,
    });
};
</script>
