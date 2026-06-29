<template>
    <div class="p-4">
        <div class="mb-4">
            <a-row :gutter="[16, 16]">
                <a-col :xs="24" :lg="12">
                    <FormItem label="Trésorerie" required :help="form.errors.tresorerie_id" >
                        <a-select
                            v-model:value="form.tresorerie_id"
                            placeholder="Sélectionner une trésorerie"
                            size="large"
                            class="w-full"
                            :options="tresoreries"
                            :status="form.errors.tresorerie_id ? 'error' : ''"
                        />
                    </FormItem>
                </a-col>
                <a-col :xs="24" :lg="12">
                    <FormItem label="Mode de Paiement" required :help="form.errors.mode_paiement">
                        <a-select
                            v-model:value="form.mode_paiement"
                            placeholder="Sélectionner un mode de paiement"
                            size="large"
                            class="w-full"
                            :options="modePaiementOptions"
                            :status="form.errors.mode_paiement ? 'error' : ''"
                        />
                    </FormItem>
                </a-col>
            </a-row>
        </div>

        <a-table
            :columns="chargeColumns"
            :data-source="form.voyage_charges"
            :pagination="false"
            size="small"
            bordered
        >
            <template #title>
                <a-button
                    type="dashed"
                    size="large"
                    class="w-full"
                    @click="addCharge"
                >
                    <PlusOutlined />
                    Ajouter une charge
                </a-button>
            </template>

            <template #bodyCell="{ column, record, index }">
                <template v-if="column.key === 'charge_item'">
                    <VoyageChargeItem
                        :item="record"
                        :status="
                            getChargeItemStatus(index, 'libelle', 'montant')
                        "
                        @remove="removeCharge(index)"
                    />
                </template>
            </template>
        </a-table>
        <div class="flex justify-end mt-4">
            <a-button
                type="primary"
                size="large"
                :loading="form.processing"
                @click="submitCharges"
            >
                Enregistrer les Charges
            </a-button>
        </div>
    </div>
</template>

<script setup>
import { confirm_delete } from "@/../Utils/confirmation_modal.js";
import FormItem from "@/Components/FormItem.vue";
import VoyageChargeItem from "@/Components/VoyageChargeItem.vue";
import { PlusOutlined } from "@ant-design/icons-vue";
import { useForm } from "@inertiajs/vue3";
import { ref, watch } from "vue";

const props = defineProps({
    voyageId: {
        type: Number,
        required: true,
    },
    initialCharges: {
        type: Array,
        default: () => [],
    },
    refreshVoyageDetails: {
        type: Function,
        required: true,
    },
    tresoreries: {
        type: Array,
        default: () => [],
    },
});

const form = useForm({
    voyage_charges: [],
    tresorerie_id: null,
    mode_paiement: null,
});

watch(
    () => props.initialCharges,
    (newVal) => {
        form.voyage_charges = newVal || [];
        // Initialiser tresorerie_id et mode_paiement si des charges existent
        if (newVal && newVal.length > 0) {
            form.tresorerie_id = newVal[0].tresorerie_id || null;
            form.mode_paiement = newVal[0].mode_paiement || null;
        }
    },
    { immediate: true, deep: true }
);

// Watch pour synchroniser les tresorerie et mode_paizmznt dans toutes les charges
watch(
    [() => form.tresorerie_id, () => form.mode_paiement],
    ([newTresorerieId, newModePaiement]) => {
        if (form.voyage_charges.length > 0) {
            form.voyage_charges = form.voyage_charges.map((charge) => ({
                ...charge,
                tresorerie_id: newTresorerieId,
                mode_paiement: newModePaiement,
            }));
        }
    }
);

const chargeColumns = [
    {
        title: "Charges",
        key: "charge_item",
    },
];

const getChargeItemStatus = (index, field1Key, field2Key) => {
    const errors = {
        [field1Key]: form.errors[`voyage_charges.${index}.${field1Key}`],
        [field2Key]: form.errors[`voyage_charges.${index}.${field2Key}`],
    };
    return errors;
};

const addCharge = () => {
    form.voyage_charges.push({
        libelle: "",
        montant: null,
        tresorerie_id: form.tresorerie_id,
        mode_paiement: form.mode_paiement,
    });
};

const removeCharge = (index) => {
    const charge = form.voyage_charges[index];

    // Si la charge existe d"jà, demande confirmation
    if (charge.id) {
        confirm_delete(
            () => {
                deleteCharge(charge.id);
            },
            "Confirmer la suppression",
            `Êtes-vous sûr de vouloir supprimer la charge "${charge.libelle}" ?`
        );
    } else {
        // Si c'est nouvelle charge, supprimer directement la ligns
        form.voyage_charges.splice(index, 1);
        form.isDirty = true;
    }
};

const deleteCharge = (chargeId) => {
    // Créer un formulaire temporaire pour la suppression
    const deleteForm = useForm({});

    deleteForm.delete(
        route("voyages.charges.destroy", {
            voyage: props.voyageId,
            voyage_charge: chargeId,
        }),
        {
            onSuccess: () => {
                // Rafraîchir les détails du voyage après suppression
                props.refreshVoyageDetails(props.voyageId);
            },
            onError: (errors) => {
                console.error("Erreur lors de la suppression:", errors);
            },
        }
    );
};

const submitCharges = () => {
    if (!props.voyageId) return;

    // Ajout de tresorerie_id et mode_paiement à chaque charge avant soumission
    const chargesWithInfoTresorerie = form.voyage_charges.map((charge) => ({
        ...charge,
        tresorerie_id: form.tresorerie_id,
        mode_paiement: form.mode_paiement,
    }));

    // Mettre à jour le form avec les données synchronisées
    form.voyage_charges = chargesWithInfoTresorerie;

    form.put(
        route("voyages.charges.sync", {
            voyage: props.voyageId,
            voyage_charges: chargesWithInfoTresorerie,
            tresorerie_id: form.tresorerie_id,
            mode_paiement: form.mode_paiement,
        }),
        {
            onSuccess: () => {
                props.refreshVoyageDetails(props.voyageId);
            },
            onError: (errors) => {
                console.error("Erreur:", errors);
            },
        }
    );
};

const modePaiementOptions = ref([
    { value: "espèce", label: "Espèce" },
    { value: "chèque", label: "Chèque" },
    { value: "mvola", label: "Mvola" },
    { value: "orangeMoney", label: "Orange Money" },
    { value: "airtelmoney", label: "Airtel Money" },
    { value: "carte_bancaire", label: "Carte Bancaire" },
]);
</script>

<style scoped>
/* Styles spécifiques au composant Charges */
</style>
