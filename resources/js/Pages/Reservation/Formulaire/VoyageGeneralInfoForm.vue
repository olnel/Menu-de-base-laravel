<template>
    <div class="p-4">
        <a-row :gutter="[24, 16]">
            <a-col :xs="24" :lg="7">
                <FormItem label="Date de Voyage" required :help="generalInfoForm.errors.date_voyage">
                    <a-date-picker v-model:value="generalInfoForm.date_voyage" size="large" format="DD/MM/YYYY" class="w-full" value-format="YYYY-MM-DD" />
                </FormItem>

                <FormItem label="Lieu de Départ" required :help="generalInfoForm.errors.depart">
                    <AutocompleteComponent v-model="generalInfoForm.depart" :options="props.lieuLivraisonOptions"
                        placeholder="Sélectionner ou saisir un lieu de départ"
                        :allow-add="true"
                        :field-config="{ label: 'label', value: 'value', search: 'label' }"
                        @search="handleDepartSearch"
                    />
                </FormItem>

                <FormItem label="Destination" required :help="generalInfoForm.errors.destination">
                    <AutocompleteComponent v-model="generalInfoForm.destination" :options="props.lieuLivraisonOptions"
                        placeholder="Sélectionner ou saisir une destination"
                        :allow-add="true"
                        :field-config="{ label: 'label', value: 'value', search: 'label' }"
                        @search="handleDestinationSearch"
                    />
                </FormItem>

                <FormItem label="Type de Trajet" required :help="generalInfoForm.errors.type_trajet">
                    <a-select v-model:value="generalInfoForm.type_trajet" size="large" placeholder="Sélectionner..." >
                        <a-select-option v-for="option in typeTrajetOptions" :key="option.value" :value="option.value">
                            {{ option.label }}
                        </a-select-option>
                    </a-select>
                </FormItem>

                <FormItem label="Commentaire" :help="generalInfoForm.errors.commentaire">
                    <a-textarea v-model:value="generalInfoForm.commentaire" :rows="3" class="!bg-white" />
                </FormItem>

                <FormItem label="Description" :help="generalInfoForm.errors.description" required >
                    <a-textarea v-model:value="generalInfoForm.description" :rows="3" @input="onDescriptionInput" class="text-base placeholder:text-lg !bg-white"
                        placeholder="Entrer une description"
                    />
                </FormItem>
            </a-col>

            <a-col :xs="24" :lg="10">
                <FormItem label="Nom du Tarif" required :help="generalInfoForm.errors.selected_tarif_id" >
                    <a-select v-model:value="generalInfoForm.selected_tarif_id" size="large" :options="allTariffsOptions" placeholder="Sélectionner un tarif"
                        class="w-full"
                    />
                </FormItem>

                <div v-if="generalInfoForm.selected_tarif_id" class="space-y-4">
                    <FormItem label="Détail du Tarif " :help="generalInfoForm.errors.tarif_ht">
                        <a-select v-model:value="generalInfoForm.tarif_ht" size="large" :options="filteredTarifDetailsOptions" placeholder="Sélectionner un détail de tarif"
                            allow-clear
                            class="w-full"
                        />
                    </FormItem>

                    <FormItem label="  " :help="generalInfoForm.errors.kilometrage">
                        <!-- Kilométrage -->
                        <div class="grid grid-cols-1 gap-3 mb-2">
                            <div class="relative">
                                <InputNumberWithSepartor v-model:modelValue="generalInfoForm.kilometrage" size="large" placeholder="Km"
                                    class="!w-full !bg-white"
                                />
                                <a-button type="button" @click="generalInfoForm.apply_kilometrage = !generalInfoForm.apply_kilometrage"
                                    :class="[
                                        'absolute left-1 top-1/2 transform -translate-y-1/2 px-3 py-1 rounded text-sm font-medium transition-colors',
                                        generalInfoForm.apply_kilometrage
                                            ? 'bg-primary text-white'
                                            : 'bg-gray-100 text-gray-400 hover:bg-gray-200 hover:text-white',
                                    ]"
                                >
                                    APPLIQUER KILOMETRAGE
                                </a-button>
                            </div>
                        </div>
                    </FormItem>
                    <FormItem  v-if="generalInfoForm.tarif_ht" label="Tarif" :help="generalInfoForm.errors.tarif_ht_total">
                        <InputNumberWithSepartor v-model:modelValue="generalInfoForm.tarif_ht_total" size="large" placeholder="0" :min="0" :disabled="true"
                            class="!w-full !bg-white"
                        />
                    </FormItem>
                </div>

                <!-- Configuration Jours et Mobilisation -->
                <div class="grid grid-cols-2 gap-2 mt-6">
                    <FormItem label="Nombre de Jours" :help="generalInfoForm.errors.nbr_jour">
                        <a-input-number v-model:value="generalInfoForm.nbr_jour" size="large" placeholder="0" :min="0"
                            class="w-full !bg-white"
                        />
                    </FormItem>

                    <FormItem label="Immobilisation" :help="generalInfoForm.errors.mobilisation">
                        <InputNumberWithSepartor v-model:modelValue="calculate_mobilisation_value" size="large" placeholder="0" :min="0" :disabled="true"
                            class="!w-full !bg-white"
                        />
                    </FormItem>
                    <FormItem v-if="facturer_tva_in_voyage" label="Remise" :help="generalInfoForm.errors.remise" class="col-span-full">
                        <InputNumberWithSepartor v-model:modelValue="generalInfoForm.remise" size="large" placeholder="0" :min="0"
                            class="!w-full !bg-white"
                        />
                    </FormItem>
                </div>
                <div v-if="facturer_tva_in_voyage" class="grid grid-cols-1 gap-3 mb-4">
                    <div class="relative">
                        <a-input-number v-model:value="generalInfoForm.valeur_tva" size="large" :min="0" :max="100"
                            class="!w-full !bg-white text content-end"
                        />
                        <a-button
                            type="button"
                            :class="[
                                'absolute right-1 top-1/2 transform -translate-y-1/2 px-3 py-1 rounded text-sm font-medium transition-colors',
                                generalInfoForm.valeur_tva && parseFloat(generalInfoForm.valeur_tva) > 0
                                    ? 'bg-primary text-white'
                                    : 'bg-gray-100 text-gray-600 ',
                            ]"
                        >
                            Valeur TVA %
                        </a-button>
                    </div>
                </div>

                <!-- Tableau des montants (HT, base, TVA, TTC, total) -->
                <FormItem>
                    <div class="overflow-hidden rounded-md border border-gray-200 mt-2">
                        <table class="w-full">
                            <tbody class="divide-y divide-gray-200">
                                <tr v-if="facturer_tva_in_voyage">
                                    <td class="px-3 py-2 text-sm font-medium text-gray-600">
                                        Montant HT
                                    </td>
                                    <td class="px-3 py-2 text-right">
                                        <InputNumberWithSepartor :value="MONTANT_HT" size="large" disabled
                                            class="w-full text-gray-800 !bg-white"
                                        />
                                    </td>
                                </tr>
                                <tr v-if="generalInfoForm.valeur_tva && parseFloat(generalInfoForm.valeur_tva) > 0 && facturer_tva_in_voyage">
                                    <td class="px-3 py-2 text-sm font-medium text-gray-600">
                                        Montant TVA
                                    </td>
                                    <td class="px-3 py-2 text-right">
                                        <InputNumberWithSepartor v-model:modelValue="generalInfoForm.montant_tva" size="large" disabled
                                            class="w-full text-gray-800 !bg-white"
                                        />
                                    </td>
                                </tr>
                                <tr>
                                    <td class="px-3 py-2 text-sm font-medium text-gray-600">
                                        Montant du Voyage
                                    </td>
                                    <td class="px-3 py-2 text-right">
                                        <InputNumberWithSepartor v-model:modelValue="generalInfoForm.montant" size="large" disabled
                                        class="w-full !text-gray-600 !bg-white"
                                        />
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </FormItem>
            </a-col>

            <a-col :xs="24" :lg="7">
                <FormItem label="Chauffeur" required :help="generalInfoForm.errors.chauffeur_id">
                    <a-select v-model:value="generalInfoForm.chauffeur_id" size="large" :options="allAvailableChauffeurs" placeholder="Sélectionner un chauffeur"
                              allow-clear
                              show-search
                              :filter-option="(input, option) => option.label.toLowerCase().includes(input.toLowerCase())"
                              option-filter-prop="label"
                    />
                </FormItem>

                <FormItem label="Véhicule" required :help="generalInfoForm.errors.vehicule_id">
                    <a-select v-model:value="generalInfoForm.vehicule_id" size="large" :options="vehiculeOptions" placeholder="Sélectionner un véhicule"
                        allow-clear
                        show-search
                    />
                </FormItem>



                <FormItem label="Aide Chauffeur" :help="generalInfoForm.errors.aide_chauffeur_id">
                    <a-select v-model:value="generalInfoForm.aide_chauffeur_id" size="large" :options="allAvailableAides" placeholder="Sélectionner un aide chauffeur"
                        allow-clear
                        show-search
                        :filter-option="(input, option) => option.label.toLowerCase().includes(input.toLowerCase())"
                        option-filter-prop="label"
                    />
                </FormItem>

                <FormItem label="Remorque" required :hel="generalInfoForm.errors.remorque_id">
                    <a-select v-model:value="generalInfoForm.remorque_id" size="large" :options="availableRemorques" placeholder="Sélectionner une remorque"
                        allow-clear
                        show-search
                        :filter-option="(input, option) => option.label.toLowerCase().includes(input.toLowerCase())"
                        option-filter-prop="label"
                    />
                </FormItem>

                <FormItem label="État de Réception" :help="generalInfoForm.errors.etat_reception">
                    <a-select v-model:value="generalInfoForm.etat_reception" size="large" placeholder="Sélectionner...">
                        <a-select-option v-for="option in etatReceptionOptions" :key="option.value" :value="option.value">
                            {{ option.label }}
                        </a-select-option>
                    </a-select>
                </FormItem>

                <FormItem label="État Véhicule Avant" :help="generalInfoForm.errors.etat_vehicule_avant">
                    <a-input v-model:value="generalInfoForm.etat_vehicule_avant" size="large" class="!bg-white"/>
                </FormItem>

                <FormItem label="État Véhicule Après" :help="generalInfoForm.errors.etat_vehicule_apres">
                    <a-input v-model:value="generalInfoForm.etat_vehicule_apres" size="large" class="!bg-white"/>
                </FormItem>
            </a-col>
        </a-row>

        <div class="flex justify-end" v-if="can('voyages.sync') || can('voyages.update')">
            <a-button type="primary" size="large" @click="handleSubmitGeneralInfo">
                Enregistrer les Informations Générales
            </a-button>
        </div>
    </div>
</template>

<script setup>
import AutocompleteComponent from "@/Components/AutoCompleteComponent/AutocompleteComponent.vue";
import FormItem from "@/Components/FormItem.vue";
import InputNumberWithSepartor from "@/Components/InputNumberWithSepartor.vue";
import usePermissions from "@/UserPermissions/usePermissions";
import { message } from "ant-design-vue";
import { computed, ref, watch } from "vue";
const { can } = usePermissions();
const facturer_tva_in_voyage = ref(false);
const props = defineProps({
    generalInfoForm: { type: Object, required: true },
    allTariffsWithDetails: { type: Array, default: () => [] },
    lieuLivraisonOptions: { type: Array, default: () => [] },
    vehiculesData: { type: Array, default: () => [] },
    remorquesData: { type: Array, default: () => [] },
    chauffeurData: { type: Array, default: () => [] }, // pour ooption des chauffeurs
    isSubmitting: { type: Boolean, default: false },
    submitGeneralInfo: { type: Function, required: true },
    selectedVoyage: { type: Object, default: null }, //
});

// Références réactives
const addKilometrage = ref(false);
const descriptionManuallyModified = ref(false);
const searchDepartValue = ref("");
const searchDestinationValue = ref("");
const availableChauffeurs = ref([]);

// Options statiques
const typeTrajetOptions = [
    { value: "local", label: "Local" },
    { value: "regional", label: "Regional" },
    { value: "inter_region", label: "Inter-region" },
    { value: "express", label: "Express" },
];

const etatReceptionOptions = [
    { value: "complet", label: "Complet" },
    { value: "partiel", label: "Partiel" },
    { value: "rejete", label: "Rejeté" },
];

// Computed Properties
const allTariffsOptions = computed(() =>
    props.allTariffsWithDetails.map((tarif) => ({ value: tarif.id, label: tarif.nom_tarif,}))
);

const filteredTarifDetailsOptions = computed(() => {
    const selectedTarif = props.allTariffsWithDetails.find((tarif) => tarif.id === props.generalInfoForm.selected_tarif_id);
    return selectedTarif ? selectedTarif.details.map((d) => ({...d,value: parseFloat(d.value),})) : [];
});

// Computed pour tous les chauffeurs disponibles
const allAvailableChauffeurs = computed(() => {
    const vehiculeChauffeurs = availableChauffeurs.value || [];
    const allChauffeurs = props.chauffeurData || [];
    // Filtrer pour n'avoir que les chauffeurs titulaires (pas les aides)
    const titulars = allChauffeurs.filter(c => !c.is_aide_chauffeur);

    // Combiner les chauffeurs du véhicule avec tout chauffeurs
    const combinedChauffeurs = [...vehiculeChauffeurs];
    // Ajouter les chauffeurs qui ne sont pas encore dans la liste du véhicule
    titulars.forEach((chauffeur) => {
        const exists = combinedChauffeurs.some((vc) => vc.value === chauffeur.value );
        if (!exists) { combinedChauffeurs.push(chauffeur);}
    });
    return combinedChauffeurs;
});

// Computed pour tous les aides chauffeurs disponibles
const allAvailableAides = computed(() => {
    return (props.chauffeurData || []).filter(c => c.is_aide_chauffeur);
});

const vehiculeOptions = computed(() =>
    props.vehiculesData.map((v) => ({
        value: v.id || v.value,
        label: `${v.marque ?? ""} ${v.modele ?? ""} (${
            v.immatriculation ?? ""
        })`,
        chauffeurs: v.chauffeurs || [],
        remorque: v.remorque || null,
    }))
);

const availableRemorques = computed(() =>
    props.remorquesData.map((r) => ({
        value: r.value || r.id,
        label: r.label || r.numero_remorque || `Remorque ${r.value || r.id}`,
    }))
);

const autoGeneratedDescription = computed(() => {
    const date = props.generalInfoForm.date_voyage
        ? typeof props.generalInfoForm.date_voyage === "string"
            ? props.generalInfoForm.date_voyage
            : props.generalInfoForm.date_voyage.format?.("DD/MM/YYYY") ||
              props.generalInfoForm.date_voyage
        : "";
    const depart = props.generalInfoForm.depart || "";
    const destination = props.generalInfoForm.destination || "";

    const getLabel = (options, value) => options.find((o) => o.value === value)?.label || "";
    const vehiculeLabel = getLabel(vehiculeOptions.value,props.generalInfoForm.vehicule_id);
    const chauffeurLabel = getLabel(allAvailableChauffeurs.value,props.generalInfoForm.chauffeur_id);
    const aideLabel = getLabel(allAvailableAides.value,props.generalInfoForm.aide_chauffeur_id);

    let desc = `Voyage${date ? " prévu pour le " + date : ""}`;
    if (depart || destination) {
        desc += `${date ? ". " : ""} qui partira${
            depart ? " de " + depart : ""
        }${destination ? " vers " + destination : ""}`;
    }
    if (vehiculeLabel) desc += `, avec le camion ${vehiculeLabel}`;
    if (chauffeurLabel) desc += `, conduit par Mr ${chauffeurLabel}`;
    if (aideLabel) desc += `, assisté par Mr ${aideLabel}`;
    return desc + ".";
});

// Ajout d'une computed pour la base HT
const MONTANT_HT = computed(() => {
    const tarif_ht_total = parseFloat(props.generalInfoForm.tarif_ht_total) || 0;
    const mobilisation = parseFloat(props.generalInfoForm.mobilisation) || 0;
    const nbrJour = parseFloat(props.generalInfoForm.nbr_jour) || 0;
    return tarif_ht_total + mobilisation * nbrJour;
});

// Functions
const handleDepartSearch = (value) => (searchDepartValue.value = value);
const handleDestinationSearch = (value) =>(searchDestinationValue.value = value);
const onDescriptionInput = () => (descriptionManuallyModified.value = true);

// Ajoutlogique de calcul pour intégrer la remuse :
const calculateMontant = () => {
    const tarif_ht = parseFloat(props.generalInfoForm.tarif_ht) || 0;
    const kilometrage = parseFloat(props.generalInfoForm.kilometrage) || 0;
    const nbrJour = parseFloat(props.generalInfoForm.nbr_jour) || 0;
    const fixedMobilisation = parseFloat(props.generalInfoForm.mobilisation) || 0;
    const valeurTva = parseFloat(props.generalInfoForm.valeur_tva) || 0;
    const remise = parseFloat(props.generalInfoForm.remise) || 0;
//calcul du tarif selon l'etat du boutton appliquer kilometrage
    const tarif_ht_calculated = props.generalInfoForm.apply_kilometrage && kilometrage > 0 ? tarif_ht * kilometrage : tarif_ht;
    // Montant HT avant remise
    const montant_ht_avant_remise = tarif_ht_calculated + fixedMobilisation * nbrJour;
    // Montant HT après remise
    const montant_ht = Math.max(montant_ht_avant_remise - remise, 0);
    const montantTva_calculated = valeurTva > 0 ? montant_ht * parseFloat(valeurTva / 100) : 0;
    const tarifTtc_calculated = montant_ht + montantTva_calculated;
    const finalTarif = tarifTtc_calculated;

    props.generalInfoForm.tarif_ht_total = parseFloat(tarif_ht_calculated);
    props.generalInfoForm.montant_ht = parseFloat(montant_ht);
    props.generalInfoForm.montant_tva = Math.round(montantTva_calculated);
    props.generalInfoForm.tarif_ttc = parseFloat(tarifTtc_calculated);
    props.generalInfoForm.montant = parseFloat(finalTarif);
};

const calculate_mobilisation_value = ref(0);
watch(
    () => props.generalInfoForm.nbr_jour,
    (val) => (calculate_mobilisation_value.value = val * parseFloat(props.generalInfoForm.mobilisation)),
    { immediate: true }
);

// Watchers
watch(
    () => props.generalInfoForm.kilometrage,
    (val) => (addKilometrage.value = !!val && parseFloat(val) > 0),
    { immediate: true }
);

watch(
    () => props.generalInfoForm.selected_tarif_id,
    (newTarifId) => { if (newTarifId === null) props.generalInfoForm.tarif_ht = null;}
);

watch(
    [
        () => props.generalInfoForm.tarif_ht,
        () => props.generalInfoForm.nbr_jour,
        () => props.generalInfoForm.valeur_tva,
        () => props.generalInfoForm.mobilisation,
        () => props.generalInfoForm.kilometrage,
        () => props.generalInfoForm.remise,
        () => props.generalInfoForm.apply_kilometrage,
    ],
    calculateMontant,
    { immediate: true }
);

watch(
    () => props.generalInfoForm.chauffeur_id,
    (chauffeurId) => {
        if (!chauffeurId) return;
        const chauffeur = props.chauffeurData.find((c) => c.value === chauffeurId);
        if (!chauffeur) return;

        if (chauffeur.vehicule_id) {
            props.generalInfoForm.vehicule_id = chauffeur.vehicule_id;
        }

        if (!chauffeur.is_aide_chauffeur) {
            const aide = props.chauffeurData.find(
                (c) => c.is_aide_chauffeur && c.parent_chauffeur_id === chauffeurId
            );
            if (aide) {
                props.generalInfoForm.aide_chauffeur_id = aide.value;
            }
        }
    }
);

watch(
    () => props.generalInfoForm.vehicule_id,
    (vehiculeId) => {
        const vehicule = vehiculeOptions.value.find((v) => v.value === vehiculeId);
        availableChauffeurs.value = vehicule?.chauffeurs?.map((ch) => ({value: ch.id,label: `${ch.nom} ${ch.prenom}`})) || [];
        if (vehicule?.remorque)
            props.generalInfoForm.remorque_id = vehicule.remorque.id;
        if (!props.generalInfoForm.chauffeur_id && availableChauffeurs.value.length > 0) {
            props.generalInfoForm.chauffeur_id = availableChauffeurs.value[0].value;
        }
    },
    { immediate: true }
);

watch(
    [() => props.generalInfoForm.id, () => props.generalInfoForm.description],
    ([newId, newDescription]) => { descriptionManuallyModified.value = !!(newId && newDescription);},
    { immediate: true }
);

watch(
    [
        () => props.generalInfoForm.date_voyage,
        () => props.generalInfoForm.depart,
        () => props.generalInfoForm.destination,
        () => props.generalInfoForm.vehicule_id,
        () => props.generalInfoForm.chauffeur_id,
        () => props.generalInfoForm.aide_chauffeur_id,
    ],
    () => {
        if (!descriptionManuallyModified.value) {
            props.generalInfoForm.description = autoGeneratedDescription.value;
        }
    },
    { immediate: true }
);

// Synchronize search values with form values on initial load
watch(
    () => props.generalInfoForm.depart,
    (val) => { if (val) searchDepartValue.value = val;},
    { immediate: true }
);
watch(
    () => props.generalInfoForm.destination,
    (val) => { if (val) searchDestinationValue.value = val;},
    { immediate: true }
);

function handleSubmitGeneralInfo() {
    if ( props.selectedVoyage && parseInt(props.selectedVoyage.facture_client_id) > 0) {
        message.error("VEUILLEZ ANNULER LA FACTURE AVANT DE POUVOIR MODIFIER CE VOYAGE.");
        return;
    }
    props.submitGeneralInfo();
}
</script>

<style scoped></style>
