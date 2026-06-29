<script setup>
import { router } from "@inertiajs/vue3";
import { computed, ref, watch } from "vue";

const props = defineProps({
    voyageId:      { type: Number,  required: true },
    initialPneus:  { type: Array,   default: () => [] },
    refreshVoyageDetails: { type: Function, required: true },
});

const pneus     = ref(props.initialPneus.map(p => ({ ...p })));
const saving    = ref(false);
const hasChange = ref(false);

watch(() => props.initialPneus, v => {
    pneus.value = v.map(p => ({ ...p }));
    hasChange.value = false;
}, { deep: true });

const nbActifs   = computed(() => pneus.value.filter(p => !p.is_secours).length);
const nbSecours  = computed(() => pneus.value.filter(p =>  p.is_secours).length);

const toggleSecours = (index) => {
    pneus.value[index].is_secours = !pneus.value[index].is_secours;
    hasChange.value = true;
};

const etatColor = (etat) => {
    const map = { NEUF: 'success', BON: 'processing', USÉ: 'warning', RECHAPÉ: 'default' };
    return map[etat?.toUpperCase()] ?? 'default';
};

const save = () => {
    saving.value = true;
    router.put(
        route('voyages.pneus.sync', props.voyageId),
        { pneus: pneus.value },
        {
            preserveState: true,
            preserveScroll: true,
            onSuccess: () => {
                hasChange.value = false;
                props.refreshVoyageDetails(props.voyageId);
            },
            onFinish: () => { saving.value = false; },
        }
    );
};
</script>

<template>
    <div class="space-y-4">
        <!-- Statistiques -->
        <div class="grid grid-cols-3 gap-4">
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-blue-50 flex items-center justify-center">
                    <span class="text-blue-500 text-lg font-bold">{{ pneus.length }}</span>
                </div>
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wide">Total pneus</p>
                    <p class="text-sm font-semibold text-gray-700">Chargés sur le voyage</p>
                </div>
            </div>
            <div class="bg-white rounded-xl border border-green-100 shadow-sm p-4 flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-green-50 flex items-center justify-center">
                    <span class="text-green-600 text-lg font-bold">{{ nbActifs }}</span>
                </div>
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wide">Actifs</p>
                    <p class="text-sm font-semibold text-gray-700">Comptabilisés</p>
                </div>
            </div>
            <div class="bg-white rounded-xl border border-orange-100 shadow-sm p-4 flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-orange-50 flex items-center justify-center">
                    <span class="text-orange-500 text-lg font-bold">{{ nbSecours }}</span>
                </div>
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wide">De secours</p>
                    <p class="text-sm font-semibold text-gray-700">Non comptabilisés</p>
                </div>
            </div>
        </div>

        <!-- Tableau des pneus -->
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-sm font-semibold text-gray-700">Liste des pneus du voyage</h3>
                <p class="text-xs text-gray-400">Activez « Secours » pour exclure un pneu des statistiques</p>
            </div>

            <div v-if="pneus.length === 0" class="flex flex-col items-center justify-center py-16 text-gray-400">
                <svg class="w-12 h-12 mb-3 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="9" stroke-width="2"/>
                    <circle cx="12" cy="12" r="3" stroke-width="2"/>
                </svg>
                <p class="text-sm font-medium">Aucun pneu trouvé</p>
                <p class="text-xs mt-1">Le véhicule ou la remorque n'a pas de pneus enregistrés</p>
            </div>

            <table v-else class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Position</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">N° Série</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Désignation</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">État</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Pneu de secours</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Statut</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <tr
                        v-for="(pneu, index) in pneus"
                        :key="index"
                        :class="pneu.is_secours ? 'bg-orange-50/40' : 'hover:bg-gray-50'"
                        class="transition-colors duration-150"
                    >
                        <!-- Position -->
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full" :class="pneu.is_secours ? 'bg-orange-400' : 'bg-green-400'"></span>
                                <span class="text-sm font-medium text-gray-700">{{ pneu.position || '—' }}</span>
                            </span>
                        </td>
                        <!-- N° Série -->
                        <td class="px-4 py-3">
                            <span class="text-sm font-mono text-gray-600">{{ pneu.numero_serie || '—' }}</span>
                        </td>
                        <!-- Désignation -->
                        <td class="px-4 py-3">
                            <span class="text-sm text-gray-600">{{ pneu.designation || '—' }}</span>
                        </td>
                        <!-- État -->
                        <td class="px-4 py-3 text-center">
                            <a-tag v-if="pneu.etat" :color="etatColor(pneu.etat)" class="!text-xs">
                                {{ pneu.etat }}
                            </a-tag>
                            <span v-else class="text-gray-400 text-xs">—</span>
                        </td>
                        <!-- Toggle secours -->
                        <td class="px-4 py-3 text-center">
                            <a-switch
                                :checked="pneu.is_secours"
                                size="small"
                                @change="toggleSecours(index)"
                                :checked-children="'Oui'"
                                :un-checked-children="'Non'"
                            />
                        </td>
                        <!-- Badge statut -->
                        <td class="px-4 py-3 text-center">
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                :class="pneu.is_secours
                                    ? 'bg-orange-100 text-orange-700'
                                    : 'bg-green-100 text-green-700'"
                            >
                                {{ pneu.is_secours ? 'Secours' : 'Actif' }}
                            </span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Bouton enregistrer -->
        <div class="flex justify-end pt-2">
            <a-button
                type="primary"
                :loading="saving"
                :disabled="!hasChange"
                @click="save"
                size="large"
            >
                Enregistrer les pneus
            </a-button>
        </div>
    </div>
</template>
