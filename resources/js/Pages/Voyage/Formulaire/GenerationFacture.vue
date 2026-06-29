<script>
const STATUT = [
    { value: 'Brouillon', label: 'Brouillon' },
    { value: 'Envoyée', label: 'Envoyée' },
    { value: 'Payée', label: 'Payée' },
    { value: 'Partiellement payée', label: 'Partiellement payée' },
    { value: 'En retard', label: 'En retard' },
];
</script>

<script setup>
import { ref, watch } from "vue";
import { router, useForm } from "@inertiajs/vue3";
import FormItem from "@/Components/FormItem.vue";
import FormModal from "@/Components/FormModal.vue";
import { message } from "ant-design-vue";
import InputNumberWithSepartor from "@/Components/InputNumberWithSepartor.vue";
import AutocompleteComponent from "@/Components/AutoCompleteComponent/AutocompleteComponent.vue";
import dayjs from "dayjs";

const props = defineProps({
    flash: Object,
    tresoreries: {
        type: Array,
        default: [],
    }
});
const dateFormat = 'DD/MM/YYYY';

const form = useForm({
    id: null,
    numero_facture: null,
    count_facture: null,
    date_facture: dayjs().format('YYYY-MM-DD'),
    date_echeance: null,
    montant_ht: 0,
    taux_tva: 0,
    montant_tva: 0,
    montant_ttc: 0,
    mode_paiement: null,
    statut_facture: 'Brouillon',
    reservation_id: null,
    voyage_id: null,
    client_id: null,
    nom_client: null,
    remise: null,
    montant_remise: 0,
    montant_payer: null,
    montant_voyage: 0,
    montant_reste_a_payer: null,
    voyages: [],
});

const filtre = ref({ search: null });
const open = ref(false);
const title = ref("");

const close = () => {
    open.value = false;
    form.reset();
    form.clearErrors();
};

const add = (factureData) => {
    router.visit(`${route('factureclient.generatenumero')}`, {
        preserveState: true,
        preserveScroll: true,
        only: ['flash'],
        onSuccess: (reponse) => {
            form.numero_facture = reponse.props.flash.data.numero;
            form.nom_client = factureData.client_nom;
            form.client_id = factureData.client_id;
            form.voyages = factureData.voyage_ids;
            form.montant_voyage = factureData.montant_payer;
            calculMontant();
            title.value = `Facture N° ${form.numero_facture}`;
            open.value = true;
        },
    });
};

const update = (rowData) => {
    router.visit(`${route('factureclient.show', {factureclient: rowData.id})}`, {
        preserveState: true,
        preserveScroll: true,
        only: ['flash'],
        onSuccess: (reponse) => {
            const response = reponse.props.flash.data;
            Object.keys(response).forEach(key => {
                form[key] = response[key];
            });
            title.value = "Edition de la facture N° " + form.numero_facture;
            calculMontant();
            open.value = true;
        },
    });
};

/**
 * fonction pour calculer les montant
 */
const calculMontant = () => {
    const montantRemise = parseFloat(form.montant_remise) || 0;
    const montantVoyage = parseFloat(form.montant_voyage) || 0;
    const tauxTva = parseFloat(form.taux_tva) || 0;

    form.montant_ht = montantVoyage - montantRemise;
    form.montant_tva = form.montant_ht * tauxTva / 100;
    form.montant_ttc = form.montant_tva + form.montant_ht;
};


watch([() => form.montant_voyage, () => form.montant_remise, () => form.taux_tva], () => {
    calculMontant();
});

const submit = () => {
    form.clearErrors();

    const method = form.id ? 'put' : 'post';
    const url = form.id ? route('factureclient.update', form.id) : route('factureclient.store');

    form.transform(data => ({
        ...data,
        _method: method.toUpperCase()
    })).post(url, {
        onSuccess: () => close(),
        forceFormData: true
    });
};

const deleteVoyage = (index) => {
    form.voyages.splice(index, 1);
    message.success('Voyage supprimé avec succès.');
};

defineExpose({add, update, close});
</script>

<template>
    <FormModal
        v-model:open="open"
        :loading="form.processing"
        @close="close"
        @submit="submit"
        :titre="title"
        size="lg"
        :show_champ_obligatoir="false"
    >
        <a-form layout="vertical">
            <a-form layout="vertical">
                <div class="grid xl:grid-cols-2 lg:grid-cols-2 md:grid-cols-1 sm:grid-cols-1 grid-cols-1 gap-4">
                    <div>
                        <form-item required has-feedback label="Date" :help="form.errors.date_facture">
                            <a-date-picker
                                v-model:value="form.date_facture"
                                :format="dateFormat"
                                size="large"
                                class="w-full text-center"
                                :value-format="'YYYY-MM-DD'"
                            />
                        </form-item>

                        <form-item has-feedback label="Date Echéance" :help="form.errors.date_echeance">
                            <a-date-picker
                                v-model:value="form.date_echeance"
                                :format="dateFormat"
                                size="large"
                                class="w-full text-center"
                                :value-format="'YYYY-MM-DD'"
                            />
                        </form-item>

                        <form-item required has-feedback label="Client" :help="form.errors.nom_client">
                            <a-input v-model:value="form.nom_client"
                                     size="large"
                                     class="w-full text-center"
                                     placeholder="Nom du client"
                                     readonly
                            />
                        </form-item>
                        <form-item required has-feedback label="Statut" :help="form.errors.statut_facture">
                            <a-input v-model:value="form.statut_facture"
                                     size="large"
                                     class="w-full text-center"
                                     placeholder=""
                                     readonly
                            />
                        </form-item>

                        <form-item required has-feedback label="Total Voyage" :help="form.errors.montant_voyage">
                            <InputNumberWithSepartor
                                v-model="form.montant_voyage"
                                size="large"
                                class="w-full text-center"
                                placeholder="Montant Voyage"
                            />
                        </form-item>
                    </div>
                    <div>
                        <form-item required has-feedback label="Remise" :help="form.errors.montant_remise">
                            <InputNumberWithSepartor
                                v-model="form.montant_remise"
                                size="large"
                                class="w-full text-center"
                                placeholder="Remise"
                            />
                        </form-item>

                        <form-item required has-feedback label="Total HT" :help="form.errors.montant_ht">
                            <InputNumberWithSepartor
                                v-model="form.montant_ht"
                                size="large"
                                class="w-full text-center"
                                placeholder="Total HT"
                                readonly
                            />
                        </form-item>

                        <form-item required has-feedback label="Taux TVA" :help="form.errors.taux_tva">
                            <InputNumberWithSepartor
                                v-model="form.taux_tva"
                                size="large"
                                class="w-full text-center"
                                placeholder="Taux TVA"
                            />
                        </form-item>
                        <form-item required has-feedback label="Montant TVA" :help="form.errors.montant_tva">
                            <InputNumberWithSepartor
                                v-model="form.montant_tva"
                                size="large"
                                class="w-full text-center"
                                placeholder="Montant TVA"
                                readonly
                            />
                        </form-item>

                        <form-item required has-feedback label="TOTAL TTC" :help="form.errors.montant_ttc">
                            <InputNumberWithSepartor
                                v-model="form.montant_ttc"
                                size="large"
                                class="w-full text-center"
                                placeholder="Total TTC"
                                readonly
                            />
                        </form-item>
                    </div>
                </div>
            </a-form>
        </a-form>
    </FormModal>
</template>

<style scoped>
:deep(.ant-upload) {
    width: 100%;
}

:deep(.ant-card) {
    border-radius: 8px;
    overflow: hidden;
    border: 1px solid #f0f0f0;
    transition: all 0.3s;
}

:deep(.ant-card:hover) {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

:deep(.ant-btn-circle) {
    width: 24px;
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.truncate {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

tfoot tr td {
    border: 1px solid #e2e8f0;
}
</style>
