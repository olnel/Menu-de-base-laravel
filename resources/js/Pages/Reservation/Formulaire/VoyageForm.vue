<template>
    <FormModal v-model:open="isModalVisible" titre="Gérer les détails du voyage"
        size="full_screen"
        :show_champ_obligatoir="false"
        :show-footer="false"
    >
        <div v-if="currentReservation">
            <div class="relative mb-4 bg-gray-50 shadow-inner">
                <div class="flex flex-wrap justify-start items-center">
                    <a-button-group>
                        <a-button v-for="voyage in displayedVoyages"
                            :key="voyage.id || voyage.numero_voyage"
                            size="large"
                            :type="selectedVoyage?.id === voyage.id && selectedVoyage?.numero_voyage === voyage.numero_voyage
                                    ? 'primary'
                                    : 'default'
                            "
                            :class="{'voyage-fictif': voyage.is_fictif,'voyage-reel': !voyage.is_fictif,}"
                            @click="handleVoyageChange(voyage)"
                        >
                            <font-awesome-icon :icon="voyage.is_fictif ? 'fa-solid fa-hourglass-half' : 'fa-solid fa-truck'" class="mr-2"/>
                            {{ voyage.numero_voyage }}
                        </a-button>
                        <a-dropdown v-if="dropdownVoyages.length > 0" :trigger="['click']">
                            <a-button size="large" :type="isDropdownActive ? 'primary' : 'default'">
                                <font-awesome-icon icon="fa-solid fa-ellipsis"/>
                            </a-button>
                            <template #overlay>
                                <div class="dropdown-menu-container">
                                    <a-button v-for="voyage in dropdownVoyages"
                                        :key="voyage.id || voyage.numero_voyage"
                                        size="large"
                                        :type=" selectedVoyage?.id === voyage.id && selectedVoyage?.numero_voyage === voyage.numero_voyage
                                                ? 'primary'
                                                : 'default'
                                        "
                                        :class="{'voyage-fictif': voyage.is_fictif,'voyage-reel': !voyage.is_fictif,}"
                                        @click="handleVoyageChange(voyage)"
                                    >
                                        <font-awesome-icon :icon="voyage.is_fictif ? 'fa-solid fa-hourglass-half' : 'fa-solid fa-truck'" class="mr-2"/>
                                        {{ voyage.numero_voyage }}
                                    </a-button>
                                </div>
                            </template>
                        </a-dropdown>
                    </a-button-group>
                </div>
            </div>
            <a-tabs v-if="selectedVoyage" v-model:activeKey="activeTabKey">
                <a-tab-pane key="1" tab="Informations Générales">
                    <VoyageGeneralInfoForm
                        v-model:generalInfoForm="generalInfoForm"
                        :all-tariffs-with-details="allTariffsWithDetails"
                        :lieu-livraison-options="lieuLivraisonOptions"
                        :vehicules-data="vehiculesData"
                        :remorques-data="remorquesData"
                        :is-submitting="isSubmitting"
                        :submit-general-info="submitGeneralInfo"
                        :chauffeur-data="chauffeursData"
                        :tva-options="tvaOptions"
                        :selected-voyage="selectedVoyage"
                    />
                </a-tab-pane>
                <a-tab-pane
                    key="2"
                    tab="Marchandises"
                    :disabled="selectedVoyage?.is_fictif"
                >
                    <VoyageMarchandisesForm
                        :voyage-id="form.id"
                        :initial-marchandises="form.voyage_marchandises"
                        :refresh-voyage-details="refreshCurrentDetail"
                    />
                </a-tab-pane>
                <a-tab-pane
                    key="3"
                    tab="Charges"
                    :disabled="selectedVoyage?.is_fictif"
                >
                    <VoyageChargesForm
                        :voyage-id="generalInfoForm.id"
                        :initial-charges="form.voyage_charges"
                        :refresh-voyage-details="refreshCurrentDetail"
                        :tresoreries="tresoreriesOptions"
                    />
                </a-tab-pane>
                <a-tab-pane
                    key="4"
                    tab="Carburants"
                    :disabled="selectedVoyage?.is_fictif"
                >
                    <VoyageCarburantForm
                        :voyage-id="form.id"
                        :initial-carburants="form.carburant_transactions"
                        :refresh-voyage-details="refreshCurrentDetail"
                        :vehicules="vehiculesData"
                        :chauffeurs="chauffeursData"
                        :carburant-cards="carburantCards"
                        :vehicule-id="form.vehicule_id"
                        :chauffeur-id="generalInfoForm.chauffeur_id"
                        :remorque-id="generalInfoForm.remorque_id"
                    />
                </a-tab-pane>
            </a-tabs>
        </div>
    </FormModal>
</template>

<script setup>
import FormModal from "@/Components/FormModal.vue";
import { router, useForm } from "@inertiajs/vue3";
import dayjs from "dayjs";
import { computed, nextTick, onBeforeUnmount, onMounted, ref } from "vue";
import VoyageCarburantForm from "./VoyageCarburantForm.vue";
import VoyageChargesForm from "./VoyageChargesForm.vue";
import VoyageGeneralInfoForm from "./VoyageGeneralInfoForm.vue";
import VoyageMarchandisesForm from "./VoyageMarchandisesForm.vue";

const props = defineProps({
    vehicules: { type: Array, default: () => [] },
    chauffeurs: { type: Array, default: () => [] },
    remorques: { type: Array, default: () => [] },
    lieuLivraisonOptions: { type: Array, default: () => [] },
    tresoreries: { type: Array, default: () => [] },
});

const isModalVisible = ref(false);
const currentReservation = ref(null);
const selectedVoyage = ref(null);
const activeTabKey = ref("1");
const isLoadingDetails = ref(false);
const carburantCards = ref([]);
const allTariffsWithDetails = ref([]);
const lieuLivraisonOptions = ref([]);
const tresoreriesOptions = ref([]);
const vehiculesData = ref(props.vehicules || []);
const remorquesData = ref(props.remorques || []);
const chauffeursData = ref(props.chauffeurs || []);
const isSubmitting = ref(false);
const visibleVoyages = ref(2); // nombre de voyages visibles par défaut

// Fonction pour mettre à jour le nombre de voyages visibles selon taille de l'écran
const updateVisibleVoyages = () => {
    const width = window.innerWidth;
    if (width >= 1280) {
        visibleVoyages.value = Math.min(6,currentReservation.value?.voyages?.length || 0);
    } else if (width >= 1024) {
        visibleVoyages.value = Math.min(4,currentReservation.value?.voyages?.length || 0);
    } else if (width >= 768) {
        visibleVoyages.value = Math.min(3,currentReservation.value?.voyages?.length || 0);
    } else if (width >= 640) {
        visibleVoyages.value = Math.min(2,currentReservation.value?.voyages?.length || 0);
    } else {
        visibleVoyages.value = Math.min(1,currentReservation.value?.voyages?.length || 0);
    }
};

const displayedVoyages = computed(
    () =>
        currentReservation.value?.voyages?.slice(0, visibleVoyages.value) || []
);
const dropdownVoyages = computed(
    () => currentReservation.value?.voyages?.slice(visibleVoyages.value) || []
);
const isDropdownActive = computed(
    () =>
        selectedVoyage.value &&
        dropdownVoyages.value.some(
            (v) =>
                v.id === selectedVoyage.value.id &&
                v.numero_voyage === selectedVoyage.value.numero_voyage
        )
);

const generalInfoForm = useForm({
    id: null,
    chauffeur_id: null,
    aide_chauffeur_id: null,
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

const form = useForm({
    id: null,
    vehicule_id: null,
    voyage_marchandises: [],
    voyage_charges: [],
    carburant_transactions: [],
});

const open = (reservation) => {
    router.visit(route("reservation.show", reservation.id), {preserveState: true,preserveScroll: true,only: ["flash"],
        onSuccess: (page) => {
            generalInfoForm.reset();
            form.reset();
            activeTabKey.value = "1";
            currentReservation.value = {
                ...page.props.flash.data,
                voyages: page.props.flash.voyages || [],
                immobilisation_value: page.props.flash.immobilisation_value || 0,
            };
            carburantCards.value = page.props.flash.carburant_cards || [];
            allTariffsWithDetails.value = page.props.flash.all_tariffs_with_details || [];
            lieuLivraisonOptions.value = page.props.flash.lieu_livraison_options || [];
            tresoreriesOptions.value = page.props.flash.tresoreries || [];
            vehiculesData.value = page.props.flash.vehicules;
            remorquesData.value = page.props.flash.remorques;
            chauffeursData.value = page.props.flash.chauffeurs || [];
            selectedVoyage.value = null;
            // Mettre à jour le nombre de voyages visibles selon la taille de l'écran
            updateVisibleVoyages();

            if (currentReservation.value?.voyages?.length > 0) {
                handleVoyageChange(currentReservation.value.voyages[0]);
            }
            isModalVisible.value = true;
        },
    });
};

// pour gérer la taille de l'écrzn
onMounted(() => {
    updateVisibleVoyages();
    window.addEventListener("resize", updateVisibleVoyages);
});
onBeforeUnmount(() => {
    window.removeEventListener("resize", updateVisibleVoyages);
});

const handleVoyageChange = (voyage) => {
    generalInfoForm.clearErrors();
    if (voyage.is_fictif) {
        generalInfoForm.reset();
        Object.assign(generalInfoForm, {
            numero_voyage: voyage.numero_voyage,
            reservation_id: currentReservation.value.id,
            destination: currentReservation.value.lieu_livraison || null,
            mobilisation: currentReservation.value.immobilisation_value || 0,
            depart: currentReservation.value.lieu_chargement || null,
            description: "",
            tarif_ht: null,
            kilometrage: null,
            apply_kilometrage: false,
            tarif_ht_total: null,
            selected_tarif_id: null,
        });
        if (allTariffsWithDetails.value.length > 0) {
            generalInfoForm.selected_tarif_id =
                allTariffsWithDetails.value[0].id;
            nextTick(() => {
                const selectedTarifDetails = allTariffsWithDetails.value.find(
                    (tarif) => tarif.id === generalInfoForm.selected_tarif_id
                )?.details;
                if (selectedTarifDetails && selectedTarifDetails.length > 0) {
                    generalInfoForm.tarif = selectedTarifDetails[0].value;
                }
            });
        }
        form.reset();
    } else {
        generalInfoForm.reset();
        form.reset();
        refreshCurrentDetail(voyage.id);
    }
    selectedVoyage.value = voyage;
    activeTabKey.value = "1";
};

const refreshCurrentDetail = (voyageId) => {
    if (!voyageId) return;

    isLoadingDetails.value = true;
    generalInfoForm.reset();

    router.visit(route("voyages.details", voyageId), { preserveState: true,preserveScroll: true,only: ["flash"],
        onSuccess: (page) => {
            const voyage = page.props.flash.voyage;
            if (voyage) {
                Object.assign(generalInfoForm, {
                    id: voyage.id,
                    chauffeur_id: voyage.chauffeur_id,
                    aide_chauffeur_id: voyage.aide_chauffeur_id,
                    destination: voyage.destination,
                    montant: voyage.montant,
                    commentaire: voyage.commentaire,
                    type_trajet: voyage.type_trajet,
                    mobilisation: voyage.mobilisation ?? page.props.flash.immobilisation_value ??0,
                    etat_reception: voyage.etat_reception,
                    etat_vehicule_avant: voyage.etat_vehicule_avant,
                    etat_vehicule_apres: voyage.etat_vehicule_apres,
                    valeur_tva: voyage.valeur_tva,
                    kilometrage: voyage.kilometrage,
                    apply_kilometrage: !!voyage.apply_kilometrage,
                    tarif_ht_total: voyage.tarif_ht_total,
                    remise: voyage.remise,
                    description: voyage.description,
                    nbr_jour: voyage.nbr_jour,
                    date_voyage: voyage.date_voyage ? dayjs(voyage.date_voyage) : null,
                    remorque_id: voyage.remorque_id,
                    vehicule_id: voyage.vehicule_id,
                    depart: voyage.depart,
                });

                const foundTarifDetail = allTariffsWithDetails.value.reduce(
                (acc, tarif) => acc || (tarif.details.find((d) =>parseFloat(d.value) === parseFloat(voyage.tarif_ht)) ? { tarifId: tarif.id } : null),
                null);
                generalInfoForm.selected_tarif_id = foundTarifDetail?.tarifId || null;
                generalInfoForm.tarif_ht = voyage.tarif_ht ? parseFloat(voyage.tarif_ht) : null;

                Object.assign(form, {
                    id: voyage.id,
                    vehicule_id: voyage.vehicule_id,
                    voyage_marchandises: voyage.voyage_marchandises || [],
                    voyage_charges: voyage.voyage_charges || [],
                    carburant_transactions: voyage.carburant_transactions || [],
                });

                vehiculesData.value = page.props.flash.vehicules || vehiculesData.value;
                remorquesData.value = page.props.flash.remorques || remorquesData.value;
            }
        },
        onError: (errors) => {
            generalInfoForm.reset();
            form.reset();
        },
        onFinish: () => {
            isLoadingDetails.value = false;
        },
    });
};

const submitGeneralInfo = () => {
    if (isSubmitting.value) return;
    isSubmitting.value = true;
    const routeName = selectedVoyage.value.is_fictif ? "voyages.store" : `voyages.update`;
    const method = selectedVoyage.value.is_fictif ? "post" : "put";
    const routeParams = selectedVoyage.value.is_fictif ? {} : generalInfoForm.id;

    generalInfoForm[method](route(routeName, routeParams), {
        onSuccess: (page) => {
            if (selectedVoyage.value.is_fictif) {
                const newVoyage = page.props.flash.data;
                const index = currentReservation.value.voyages.findIndex((v) => v.numero_voyage === generalInfoForm.numero_voyage);
                if (index !== -1) {
                    currentReservation.value.voyages.splice(index, 1, {...newVoyage,is_fictif: false,});
                    selectedVoyage.value = currentReservation.value.voyages[index];
                    form.id = newVoyage.id; // Update form.id pour enable les autre tab-pane
                    refreshCurrentDetail(newVoyage.id);
                }
            } else {
                refreshCurrentDetail(generalInfoForm.id);
            }
        },
        onFinish: () => {isSubmitting.value = false;},
    });
};

defineExpose({ open });
</script>

<style scoped>
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
:deep(.ant-btn-group) {
    box-shadow: none !important;
    border-radius: 0 !important;
    border-top-left-radius: 8px !important;
    border-top-right-radius: 8px !important;
    overflow: hidden;
}
:deep(.ant-btn-group .ant-btn) {
    height: 60px;
    padding: 0 15px;
    line-height: 45px;
    border-radius: 0 !important;
}
:deep(.ant-btn-group > .ant-btn:not(:first-child)) {
    margin-left: -1px;
}

.voyage-fictif.ant-btn-default {
    background-color: #f9fafb !important;
    color: #4b5563 !important;
    border-color: #e5e7eb !important;
}
.voyage-fictif.ant-btn-default:hover {
    background-color: #f3f4f6 !important;
    border-color: #d1d5db !important;
}
.voyage-reel.ant-btn-default {
    background-color: #e0f2fe !important;
    color: #1d4ed8 !important;
    border-color: #93c5fd !important;
}
.voyage-reel.ant-btn-default:hover {
    background-color: #bfdbfe !important;
    border-color: #93c5fd !important;
}

/* Dropdown menu styles */
.dropdown-menu-container {
    display: flex;
    flex-direction: column;
    background-color: white;
    border-radius: 8px;
    box-shadow: 0 3px 6px -4px rgba(0, 0, 0, 0.12),
        0 6px 16px 0 rgba(0, 0, 0, 0.08), 0 9px 28px 8px rgba(0, 0, 0, 0.05);
    overflow: hidden;
}
.dropdown-menu-container .ant-btn {
    height: 60px;
    line-height: 45px;
    padding: 0 15px;
    width: 100%;
    border: none !important;
    text-align: left;
    border-radius: 0 !important;
}
.dropdown-menu-container .ant-btn:not(:last-child) {
    border-bottom: 1px solid #f0f0f0;
}
.dropdown-menu-container .ant-btn.ant-btn-primary {
    @apply bg-primary text-white;
}
.dropdown-menu-container .ant-btn.ant-btn-primary .mr-2 {
    color: white !important;
}
</style>
