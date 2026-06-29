<template>
    <FormModal
        v-model:open="isModalVisible"
        titre="Modifier les détails du voyage"
        :loading="isLoadingDetails || isSubmitting"
        @submit="submitGeneralInfo"
        size="full_screen"
        :show_champ_obligatoir="false"
        :show-footer="false"
    >
        <div v-if="generalInfoForm.id" class="p-3">
            <a-tabs v-model:activeKey="activeTabKey">
                <a-tab-pane key="1" tab="Informations Générales">
                    <VoyageGeneralInfoForm
                        v-model:generalInfoForm="generalInfoForm"
                        :all-tariffs-with-details="allTariffsWithDetails"
                        :lieu-livraison-options="lieuLivraisonOptions"
                        :vehicules-data="vehiculesData"
                        :remorques-data="remorquesData"
                        :chauffeur-data="chauffeursData"
                        :is-submitting="isSubmitting"
                        :submit-general-info="submitGeneralInfo"
                        :tva-options="tvaOptions"
                    />
                </a-tab-pane>
                <a-tab-pane key="2" tab="Marchandises">
                    <VoyageMarchandisesForm
                        :key="`marchandises-${form.voyage_marchandises.length}-${updateCounter.value}`"
                        :voyage-id="generalInfoForm.id"
                        :initial-marchandises="form.voyage_marchandises"
                        :refresh-voyage-details="refreshCurrentDetail"
                    />
                </a-tab-pane>
                <a-tab-pane key="3" tab="Charges">
                    <VoyageChargesForm
                        :key="`charges-${form.voyage_charges.length}-${updateCounter.value}`"
                        :voyage-id="generalInfoForm.id"
                        :initial-charges="form.voyage_charges"
                        :refresh-voyage-details="refreshCurrentDetail"
                        :tresoreries="tresoreriesOptions"
                    />
                </a-tab-pane>
                <a-tab-pane key="4" tab="Carburants">
                    <VoyageCarburantForm
                        :key="`carburants-${form.carburant_transactions.length}-${updateCounter.value}`"
                        :voyage-id="generalInfoForm.id"
                        :initial-carburants="form.carburant_transactions"
                        :refresh-voyage-details="refreshCurrentDetail"
                        :vehicules="vehiculesData"
                        :chauffeurs="chauffeursData"
                        :carburant-cards="listeCarburantCards"
                        :vehicule-id="generalInfoForm.vehicule_id"
                        :chauffeur-id="generalInfoForm.chauffeur_id"
                        :remorque-id="generalInfoForm.remorque_id"
                    />
                </a-tab-pane>
                <a-tab-pane key="5" tab="Pneus">
                    <VoyagePneusForm
                        :key="`pneus-${voyagePneus.length}-${updateCounter.value}`"
                        :voyage-id="generalInfoForm.id"
                        :initial-pneus="voyagePneus"
                        :refresh-voyage-details="refreshCurrentDetail"
                    />
                </a-tab-pane>
                <a-tab-pane key="6" tab="Suivi Expédition" :disabled="!generalInfoForm.id">
                    <VoyageSuiviForm
                        :voyage-id="generalInfoForm.id"
                        :initial-suivi="currentSuivi"
                    />
                </a-tab-pane>
            </a-tabs>
        </div>
        <div v-else class="p-3 text-center text-gray-500">
            <a-spin size="large" />
            <p class="mt-3">Chargement des détails du voyage...</p>
        </div>
    </FormModal>
</template>

<script setup>
import FormModal from "@/Components/FormModal.vue";
import VoyageCarburantForm from "@/Pages/Reservation/Formulaire/VoyageCarburantForm.vue";
import VoyageChargesForm from "@/Pages/Reservation/Formulaire/VoyageChargesForm.vue";
import VoyageGeneralInfoForm from "@/Pages/Reservation/Formulaire/VoyageGeneralInfoForm.vue";
import VoyageMarchandisesForm from "@/Pages/Reservation/Formulaire/VoyageMarchandisesForm.vue";
import VoyagePneusForm from "@/Pages/Voyage/Formulaire/VoyagePneusForm.vue";
import VoyageSuiviForm from "@/Pages/Voyage/Formulaire/VoyageSuiviForm.vue";
import { router, useForm } from "@inertiajs/vue3";
import { nextTick, ref } from "vue";

const props = defineProps({
    vehicules: { type: Array, default: () => [] },
    chauffeurs: { type: Array, default: () => [] },
    carburantCards: { type: Array, default: () => [] },
});

const isModalVisible = ref(false);
const listeCarburantCards = ref([]);
const activeTabKey = ref("1");
const isLoadingDetails = ref(false);
const isSubmitting = ref(false);
const updateCounter = ref(0); // compteur pour rerendre les comprensant

// rempli par flash data
const allTariffsWithDetails = ref([]);
const lieuLivraisonOptions = ref([]);
const tresoreriesOptions = ref([]);
const vehiculesData = ref([]);
const remorquesData = ref([]);
const chauffeursData = ref(props.chauffeurs || []);

const generalInfoForm = useForm({
    id: null,
    chauffeur_id: null,
    destination: null,
    montant: null,
    commentaire: null,
    type_trajet: null,
    mobilisation: null,
    etat_reception: null,
    etat_vehicule_avant: null,
    etat_vehicule_apres: null,
    tarif_ht: null,
    valeur_tva: null,
    selected_tarif_id: null,
    nbr_jour: null,
    date_voyage: null,
    remorque_id: null,
    vehicule_id: null,
    numero_voyage: null,
    reservation_id: null,
    montant_tva: null,
    tarif_ttc: null,
    depart: null,
    kilometrage: null,
    apply_kilometrage: false,
    tarif_ht_total: null,
    description: null,
    remise: null,
});

// Données suivi séparées du formulaire principal
const currentSuivi = ref({});

const voyagePneus = ref([]);

const form = useForm({
    id: null,
    vehicule_id: null,
    voyage_marchandises: [],
    voyage_charges: [],
    carburant_transactions: [],
});

const open = (voyage) => {
    isModalVisible.value = true;
    activeTabKey.value = "1";
    generalInfoForm.reset();
    form.reset();
    currentSuivi.value = {};

    isLoadingDetails.value = true;
    router.visit(route("voyages.details", voyage.id), {preserveState: true,preserveScroll: true,only: ["flash"],
        onSuccess: (page) => {
            const flashVoyage = page.props.flash.voyage;
            if (flashVoyage) {
                // Remplir general info form
                Object.assign(generalInfoForm, {
                    id: flashVoyage.id,
                    chauffeur_id: flashVoyage.chauffeur_id,
                    destination: flashVoyage.destination,
                    montant: flashVoyage.montant,
                    commentaire: flashVoyage.commentaire,
                    type_trajet: flashVoyage.type_trajet,
                    mobilisation: flashVoyage.mobilisation,
                    etat_reception: flashVoyage.etat_reception,
                    etat_vehicule_avant: flashVoyage.etat_vehicule_avant,
                    etat_vehicule_apres: flashVoyage.etat_vehicule_apres,
                    valeur_tva: flashVoyage.valeur_tva ? parseFloat(flashVoyage.valeur_tva) : null,
                    nbr_jour: flashVoyage.nbr_jour,
                    date_voyage: flashVoyage.date_voyage ?? null,
                    remorque_id: flashVoyage.remorque_id,
                    vehicule_id: flashVoyage.vehicule_id,
                    numero_voyage: flashVoyage.numero_voyage,
                    reservation_id: flashVoyage.reservation_id,
                    montant_tva: flashVoyage.montant_tva,
                    tarif_ttc: flashVoyage.tarif_ttc,
                    depart: flashVoyage.depart,
                    kilometrage: flashVoyage.kilometrage,
                    apply_kilometrage: !!flashVoyage.apply_kilometrage,
                    tarif_ht_total: flashVoyage.tarif_ht_total,
                    description: flashVoyage.description,
                    tarif_ht: parseFloat(flashVoyage.tarif_ht),
                    remise: parseFloat(flashVoyage.remise),
                });

                currentSuivi.value = {
                    statut:                 flashVoyage.statut                 ?? 'planifie',
                    km_parcouru:            flashVoyage.km_parcouru            ?? null,
                    poids_transporte:       flashVoyage.poids_transporte       ?? null,
                    heures_facturables:     flashVoyage.heures_facturables     ?? null,
                    heures_non_facturables: flashVoyage.heures_non_facturables ?? null,
                };

                nextTick(() => {
                    const foundTarifDetail = (allTariffsWithDetails.value || []).reduce((acc, tarif) => {
                        const detail = (tarif.details || []).find((d) =>parseFloat(d.value) === generalInfoForm.tarif_ht);
                        return acc || (detail ? { tarifId: tarif.id } : null);
                    }, null);
                    generalInfoForm.selected_tarif_id =
                        foundTarifDetail?.tarifId || null;
                });

                Object.assign(form, {
                    id: flashVoyage.id,
                    vehicule_id: flashVoyage.vehicule_id,
                    voyage_marchandises: flashVoyage.voyage_marchandises || [],
                    voyage_charges: flashVoyage.voyage_charges || [],
                    carburant_transactions: flashVoyage.carburant_transactions || [],
                });

                // Update options avecflash messages
                listeCarburantCards.value = page.props.flash.carburant_cards || [];
                allTariffsWithDetails.value = page.props.flash.all_tariffs_with_details || [];
                lieuLivraisonOptions.value = page.props.flash.lieu_livraison_options || [];
                tresoreriesOptions.value = page.props.flash.tresoreries || [];
                vehiculesData.value = page.props.flash.vehicules || [];
                remorquesData.value = page.props.flash.remorques || [];
                chauffeursData.value = page.props.flash.chauffeurs || [];
                voyagePneus.value = page.props.flash.voyage_pneus || [];
            }
        },
        onError: (errors) => {generalInfoForm.reset();form.reset();},
        onFinish: () => {isLoadingDetails.value = false;},
    });
};

const submitGeneralInfo = () => {
    isSubmitting.value = true;
    generalInfoForm.put(route("voyages.update", generalInfoForm.id), {
        onSuccess: () => {isModalVisible.value = false;router.reload();},
        onFinish: () => {isSubmitting.value = false;},
    });
};

const refreshCurrentDetail = (voyageId = null) => {
    const targetVoyageId = voyageId || generalInfoForm.id;
    if (!targetVoyageId) return;

    isLoadingDetails.value = true;
    generalInfoForm.clearErrors();

    router.visit(route("voyages.details", targetVoyageId), {preserveState: true,preserveScroll: true,only: ["flash"],
        onSuccess: (page) => {
            const fetchedVoyage = page.props.flash.voyage;
            if (fetchedVoyage) {
                Object.assign(generalInfoForm, {
                    id: fetchedVoyage.id,
                    chauffeur_id: fetchedVoyage.chauffeur_id,
                    destination: fetchedVoyage.destination,
                    montant: fetchedVoyage.montant,
                    commentaire: fetchedVoyage.commentaire,
                    type_trajet: fetchedVoyage.type_trajet,
                    mobilisation: fetchedVoyage.mobilisation,
                    etat_reception: fetchedVoyage.etat_reception,
                    etat_vehicule_avant: fetchedVoyage.etat_vehicule_avant,
                    etat_vehicule_apres: fetchedVoyage.etat_vehicule_apres,
                    valeur_tva: fetchedVoyage.valeur_tva !== null ? parseFloat(fetchedVoyage.valeur_tva) : null,
                    nbr_jour: fetchedVoyage.nbr_jour,
                    date_voyage: fetchedVoyage.date_voyage ?? null,
                    remorque_id: fetchedVoyage.remorque_id,
                    vehicule_id: fetchedVoyage.vehicule_id,
                    numero_voyage: fetchedVoyage.numero_voyage,
                    reservation_id: fetchedVoyage.reservation_id,
                    montant_tva: fetchedVoyage.montant_tva,
                    tarif_ttc: fetchedVoyage.tarif_ttc,
                    depart: fetchedVoyage.depart,
                    kilometrage: fetchedVoyage.kilometrage,
                    apply_kilometrage: !!fetchedVoyage.apply_kilometrage,
                    tarif_ht_total: fetchedVoyage.tarif_ht_total,
                    description: fetchedVoyage.description,
                    tarif_ht: parseFloat(fetchedVoyage.tarif_ht),
                });

                currentSuivi.value = {
                    statut:                 fetchedVoyage.statut                 ?? 'planifie',
                    km_parcouru:            fetchedVoyage.km_parcouru            ?? null,
                    poids_transporte:       fetchedVoyage.poids_transporte       ?? null,
                    heures_facturables:     fetchedVoyage.heures_facturables     ?? null,
                    heures_non_facturables: fetchedVoyage.heures_non_facturables ?? null,
                };

                nextTick(() => {
                    const foundTarifDetail = (allTariffsWithDetails.value || []).reduce((acc, tarif) => {
                        const detail = (tarif.details || []).find((d) =>parseFloat(d.value) === generalInfoForm.tarif_ht);
                        return acc || (detail ? { tarifId: tarif.id } : null);
                    }, null);

                    generalInfoForm.selected_tarif_id = foundTarifDetail?.tarifId || null;
                });

                Object.assign(form, {
                    id: fetchedVoyage.id,
                    vehicule_id: fetchedVoyage.vehicule_id,
                    voyage_marchandises: fetchedVoyage.voyage_marchandises || [],
                    voyage_charges: fetchedVoyage.voyage_charges || [],
                    carburant_transactions: fetchedVoyage.carburant_transactions || [],
                });

                // Re-update options flash messages
                allTariffsWithDetails.value = page.props.flash.all_tariffs_with_details || [];
                lieuLivraisonOptions.value = page.props.flash.lieu_livraison_options || [];
                tresoreriesOptions.value = page.props.flash.tresoreries || [];
                vehiculesData.value = page.props.flash.vehicules || [];
                remorquesData.value = page.props.flash.remorques || [];
                listeCarburantCards.value = page.props.flash.carburant_cards || [];
                chauffeursData.value = page.props.flash.chauffeurs || [];
                voyagePneus.value = page.props.flash.voyage_pneus || [];

                // Pour les composant enfant charge marchandise e carvurant
                nextTick(() => {
                    // re
                    form.voyage_marchandises = [...(fetchedVoyage.voyage_marchandises || []),];
                    form.voyage_charges = [...(fetchedVoyage.voyage_charges || []),];
                    form.carburant_transactions = [...(fetchedVoyage.carburant_transactions || []),];
                    // Increment compteur pour rerender compos
                    updateCounter.value++;
                });
            }
        },
        onError: (errors) => {
            generalInfoForm.reset();
            form.reset();
            // console.error("Erreur lors du rechargement des détails:", errors);
        },
        onFinish: () => {isLoadingDetails.value = false;},
    });
};

defineExpose({ open });
</script>

<style scoped>
/* Scoped styles specific to this component */
:deep(.ant-tabs-tab) {
    @apply text-base whitespace-normal;
}

.type-segmented .ant-segmented-item-label {
    @apply py-2 px-6 text-base font-medium;
}

.type-segmented .ant-segmented-item-selected {
    @apply bg-primary text-white shadow-md !important;
}

:deep(.ant-tabs-nav) {
    margin-bottom: 0 !important;
}

:deep(.ant-tabs-tab) {
    @apply text-base whitespace-normal;
    padding: 12px 20px !important;
}

:deep(.ant-tabs-content-holder) {
    @apply bg-gray-50 shadow-inner;
    padding: 16px;
    border-bottom-left-radius: 8px;
    border-bottom-right-radius: 8px;
    border: 1px solid #f0f0f0;
    border-top: none;
    margin-top: -1px;
}
</style>
