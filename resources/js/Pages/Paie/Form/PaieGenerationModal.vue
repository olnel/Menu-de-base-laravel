<script setup>
import FormModal from "@/Components/FormModal.vue";
import InputNumberWithSepartor from "@/Components/InputNumberWithSepartor.vue";
import { useForm } from "@inertiajs/vue3";
import { ref, computed, watch } from "vue";
import axios from "axios";
import { message } from "ant-design-vue";

const props = defineProps({
    mois: Number,
    annee: Number,
    months: Array
});

const emit = defineEmits(['success']);

const open = ref(false);
const loading = ref(false);
const previewData = ref([]);
const globalPrimeConfigs = ref([]);
const selectedGlobalPrimes = ref([]); // IDs des primes globales sélectionnées par défaut pour tous

const show = async () => {
    loading.value = true;
    try {
        const response = await axios.get(route('paie.preview'), {
            params: { mois: props.mois, annee: props.annee }
        });
        
        // Initialiser les données éditables
        previewData.value = response.data.salaries.map(s => ({
            ...s,
            selected_global_primes: [] // Primes choisies spécifiquement pour ce salarié
        }));
        
        globalPrimeConfigs.value = response.data.prime_configs;
        selectedGlobalPrimes.value = []; // Reset global selection
        
        if (previewData.value.length === 0) {
            message.info("Aucun nouveau salarié à traiter pour cette période.");
            return;
        }
        
        open.value = true;
    } catch (e) {
        message.error("Erreur lors du chargement de la prévisualisation.");
    } finally {
        loading.value = false;
    }
};

const form = useForm({
    mois: null,
    annee: null,
    items: [] // Utilisation de 'items' au lieu de 'data' pour éviter le conflit Inertia
});

// Watcher pour appliquer la sélection globale à tous les salariés
watch(selectedGlobalPrimes, (newVal) => {
    const selectedConfigs = globalPrimeConfigs.value.filter(c => newVal.includes(c.id));
    previewData.value.forEach(s => {
        s.selected_global_primes = [...selectedConfigs];
    });
});

const calculateNet = (record) => {
    const base = parseFloat(record.salaire_base) || 0;
    const autoPrimes = record.automatic_primes.reduce((sum, p) => sum + parseFloat(p.montant), 0);
    const globalPrimes = record.selected_global_primes.reduce((sum, p) => sum + parseFloat(p.montant), 0);
    return base + autoPrimes + globalPrimes;
};

const totalNetGlobal = computed(() => {
    return previewData.value.reduce((sum, s) => sum + calculateNet(s), 0);
});

const submit = () => {
    form.mois = props.mois;
    form.annee = props.annee;
    form.items = previewData.value;

    // Utilisation de transform pour envoyer la clé 'data' attendue par le backend
    form.transform((data) => ({
        mois: data.mois,
        annee: data.annee,
        data: data.items,
    })).post(route('paie.generate'), {
        onSuccess: () => {
            open.value = false;
            emit('success');
        }
    });
};

const monthLabel = computed(() => props.months.find(m => m.value == props.mois)?.label);

defineExpose({ show });
</script>

<template>
    <FormModal
        v-model:open="open"
        :loading="form.processing"
        @close="open = false"
        @submit="submit"
        :titre="`Prévisualisation de la Paie - ${monthLabel} ${annee}`"
        size="full_screen"
    >
        <div class="flex flex-col h-full">
            <!-- En-tête : Primes Globales Facultatives -->
            <div class="bg-primary/5 p-6 rounded-2xl border border-primary/10 mb-6">
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center text-primary">
                        <font-awesome-icon icon="fa-solid fa-gift" />
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-800 mb-0">Primes Globales Facultatives</h3>
                        <p class="text-xs text-gray-500 italic mb-0">Sélectionnez les primes à appliquer à l'ensemble des salariés pour ce mois.</p>
                    </div>
                </div>
                
                <div class="flex flex-wrap gap-4">
                    <div v-for="config in globalPrimeConfigs" :key="config.id" 
                        class="bg-white px-4 py-3 rounded-xl border border-gray-200 hover:border-primary/30 transition-all flex items-center gap-3 shadow-sm">
                        <a-checkbox-group v-model:value="selectedGlobalPrimes">
                             <a-checkbox :value="config.id">
                                <span class="font-bold text-gray-700">{{ config.libelle }}</span>
                                <span class="ml-2 text-primary text-xs font-black">{{ new Intl.NumberFormat('fr-FR').format(config.montant) }} Ar</span>
                             </a-checkbox>
                        </a-checkbox-group>
                    </div>
                    <div v-if="globalPrimeConfigs.length === 0" class="text-gray-400 text-sm italic">
                        Aucune prime globale configurée dans les paramètres.
                    </div>
                </div>
            </div>

            <!-- Tableau Editable -->
            <div class="flex-1 overflow-auto rounded-2xl border border-gray-200 shadow-sm bg-white">
                <table class="w-full border-collapse">
                    <thead class="bg-gray-50 sticky top-0 z-10 shadow-sm">
                        <tr>
                            <th class="px-4 py-4 text-left text-xs font-black text-gray-500 uppercase tracking-widest border-b" width="120">Matricule</th>
                            <th class="px-4 py-4 text-left text-xs font-black text-gray-500 uppercase tracking-widest border-b">Salarié</th>
                            <th class="px-4 py-4 text-center text-xs font-black text-gray-500 uppercase tracking-widest border-b" width="180">Salaire de Base</th>
                            <th class="px-4 py-4 text-left text-xs font-black text-gray-500 uppercase tracking-widest border-b">Primes Auto / Voyage</th>
                            <th class="px-4 py-4 text-left text-xs font-black text-gray-500 uppercase tracking-widest border-b">Primes Facultatives</th>
                            <th class="px-4 py-4 text-right text-xs font-black text-gray-500 uppercase tracking-widest border-b" width="200">Net à Payer</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <tr v-for="(salarie, index) in previewData" :key="salarie.salarie_id" class="hover:bg-primary/5 transition-colors group">
                            <td class="px-4 py-3 border-r border-gray-50">
                                <span class="font-black text-primary">{{ salarie.matricule }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="font-bold text-gray-800">{{ salarie.nom_complet }}</div>
                                <div v-if="salarie.nb_voyages > 0" class="text-[10px] text-blue-500 font-bold uppercase mt-1">
                                    <font-awesome-icon icon="fa-solid fa-truck" class="mr-1" /> {{ salarie.nb_voyages }} voyages effectués
                                </div>
                            </td>
                            <td class="px-4 py-3 bg-gray-50/50">
                                <InputNumberWithSepartor 
                                    v-model:modelValue="salarie.salaire_base" 
                                    class="!w-full font-bold !bg-transparent border-transparent group-hover:border-primary/20 focus:!bg-white"
                                    :min="0"
                                />
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex flex-wrap gap-1">
                                    <a-tooltip v-for="p in salarie.automatic_primes" :key="p.config_id">
                                        <template #title>{{ p.libelle }}</template>
                                        <a-tag color="blue" class="m-0 !rounded-full border-blue-100 px-3">
                                            + {{ new Intl.NumberFormat('fr-FR').format(p.montant) }} Ar
                                        </a-tag>
                                    </a-tooltip>
                                    <span v-if="salarie.automatic_primes.length === 0" class="text-gray-300 text-[10px]">Aucune</span>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex flex-wrap gap-1">
                                    <a-tag v-for="p in salarie.selected_global_primes" :key="p.id" color="green" class="m-0 !rounded-full border-green-100 px-3">
                                        {{ p.libelle }}
                                    </a-tag>
                                    <span v-if="salarie.selected_global_primes.length === 0" class="text-gray-300 text-[10px]">Aucune</span>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-right bg-primary/5">
                                <span class="text-lg font-black text-primary tracking-tighter">
                                    {{ new Intl.NumberFormat('fr-FR').format(calculateNet(salarie)) }}
                                </span>
                                <span class="text-[10px] text-primary/60 ml-1 font-bold">Ar</span>
                            </td>
                        </tr>
                    </tbody>
                    <tfoot class="bg-gray-900 text-white">
                        <tr>
                            <td colspan="5" class="px-4 py-4 text-right font-bold uppercase tracking-widest">Masse Salariale Totale Estimée</td>
                            <td class="px-4 py-4 text-right font-black text-xl tracking-tighter text-amber-400">
                                {{ new Intl.NumberFormat('fr-FR').format(totalNetGlobal) }} <span class="text-xs">Ar</span>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </FormModal>
</template>

<style scoped>
:deep(.ant-input-number-input) {
    text-align: center !important;
}
</style>
