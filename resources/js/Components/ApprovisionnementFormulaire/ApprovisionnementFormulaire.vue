<script setup>
import {ref} from "vue";
import {router, useForm} from "@inertiajs/vue3";
import FormItem from "@/Components/FormItem.vue";
import FormModal from "@/Components/FormModal.vue";
import ButtonIcon from "@/Components/ButtonIcon.vue";
import {message} from "ant-design-vue";
import dayjs from "dayjs";
import InputNumberWithSepartor from "@/Components/InputNumberWithSepartor.vue";
import AutocompleteComponent from "@/Components/AutoCompleteComponent/AutocompleteComponent.vue";

const props = defineProps({
    flash: Object,
    magasins: {
        type: Array,
        default: []
    },
    fournisseurs: {
        type: Array,
        default: [],
    }
});
const dateFormat = 'DD/MM/YYYY';

const form = useForm({
    id: null,
    date_appro: dayjs().format('YYYY-MM-DD'),
    magasin_id: null,
    boncommande_fournisseur_id: null,
    numero_bon_livraison: null,
    fournisseur_id: null,
    nom_fournisseur: null,
    montant_ht_appro: 0,
    montant_tva_appro: 0,
    montant_ttc_appro: 0,
    montant_a_payer_appro: 0,
    montant_payer_appro: 0,
    montant_reste_a_payer_appro: 0,
    remise_general: 0,
    remise_general_ariary: 0,
    numero_bon_commande: null,
    taux_tva: 0,
    type_approvisionnement: null,
    details: []
});


const filtre = ref({search: null, magasin_id: ''});
const LIST_ARTICLE = ref([]);
const open = ref(false);
const title = ref("");

const close = () => {
    open.value = false;
    form.reset();
    form.clearErrors();
};

const add = () => {
    title.value = "Nouveau Approvisionnement";
    open.value = true;
    LIST_ARTICLE.value = [];
};

const newappro = (rowData) => {

    router.visit(`${route('article_boncommande.show', {article_boncommande: rowData.id})}`, {
        preserveState: true,
        preserveScroll: true,
        only: ['flash'],
        onSuccess: (reponse) => {
            const response = reponse.props.flash.data;
            console.log(response);
            form.nom_fournisseur = response.nom_fournisseur;
            form.boncommande_fournisseur_id = response.id;
            form.fournisseur_id = response.fournisseur_id;
            form.numero_bon_commande = response.numero_bon_commande;
            response.details.forEach((value) => {
                form.details.push({
                    article_id: value.article_id,
                    magasin_id: null,
                    prix_unitaire: value.prix_unitaire,
                    quantite: value.qte_commander,
                    montant: parseFloat(value.prix_unitaire) * parseFloat(value.qte_commander),
                    remise: 0,
                    remise_ariary: 0,
                    tva_detail: 0,
                    montant_tva: 0,
                    valeur_remise: 0,
                    valeur_ht: 0,
                    designation: value.designation,
                    reference: value.reference
                })
            });
            calculMontant();
            title.value = "Transformation Appro";
            open.value = true;
        },
    });
};

const submit = () => {
    form.clearErrors();
    const method = form.id ? 'put' : 'post';
    const url = form.id ? route('article_approvisionnement.update', form.id) : route('article_approvisionnement.store');

    form.transform(data => ({
        ...data,
        _method: method.toUpperCase()
    })).post(url, {
        onSuccess: () => close(),
        forceFormData: true
    });
};

const pushDetail = (article) => {
    // Vérification plus concise de l'existence de l'article
    const articleExists = form.details.some(detail => detail.article_id === article.id);

    if (articleExists) {
        message.warning('Cet article est déjà enregistré');
        return;
    }

    // Ajout de l'article avec calcul immédiat de l'écart
    form.details.push({
        article_id: article.id,
        magasin_id: form.magasin_id,
        prix_unitaire: article.valeur,
        quantite: 0,
        montant: 0,
        remise: 0,
        remise_ariary: 0,
        tva_detail: 0,
        montant_tva: 0,
        valeur_remise: 0,
        valeur_ht: 0,
        designation: article.designation,
        reference: article.reference
    });
}

const spliceDetal = (index) => {
    form.details.splice(index, 1);
    calculMontant();
}


// Convertit la remise en pourcentage en valeur absolue (Ariary)
const calculValeurRemiseEnMontant = (index) => {
    const detail = form.details[index];
    detail.remise_ariary = (parseFloat(detail.montant) * parseFloat(detail.remise)) / 100;
    calculMontant();
}

// Convertit la remise en valeur absolue (Ariary) en pourcentage
const calculValeurRemiseEnPourcentage = (index) => {
    const detail = form.details[index];
    const montant = parseFloat(detail.montant);
    detail.remise = montant > 0 ? (parseFloat(detail.remise_ariary) * 100) / montant : 0;
    calculMontant();
}

// Calcule la remise générale en valeur absolue
const calcuRemiseGeneralValeur = () => {
    form.remise_general_ariary = (parseFloat(form.montant_ht_appro) * form.remise_general) / 100;
    calculMontant();
}

// Calcule la remise générale en pourcentage
const calcuRemiseGeneral = () => {
    const montantHT = parseFloat(form.montant_ht_appro);
    form.remise_general = montantHT > 0 ? (parseFloat(form.remise_general_ariary) * 100) / montantHT : 0;
    calculMontant();
}

// Calcule le montant de TVA
const calulMontantTva = () => {
    form.montant_tva_appro = (form.montant_ht_appro * form.taux_tva) / 100;
    calculMontant();
}

// Fonction principale de calcul des montants
const calculMontant = () => {
    if (!form?.details) return;

    let totalHT = 0;
    let totalTVA = 0;

    form.details.forEach((detail) => {
        // 1. Calcul du montant HT initial
        const prixUnitaire = parseFloat(detail.prix_unitaire) || 0;
        const quantite = parseFloat(detail.quantite) || 0;
        const montantHT = prixUnitaire * quantite;

        // 2. Application de la remise ligne par ligne
        const remiseAriary = parseFloat(detail.remise_ariary) || 0;
        const montantApresRemise = Math.max(0, montantHT - remiseAriary);

        // 3. Calcul TVA sur le montant après remise
        const tauxTVA = parseFloat(detail.tva_detail) || 0;
        const montantTVA = montantApresRemise * tauxTVA / 100;

        // Mise à jour des valeurs
        detail.montant = montantApresRemise;
        detail.montant_tva = montantTVA;
        detail.valeur_remise = montantApresRemise;

        totalHT += montantApresRemise;
        totalTVA += montantTVA;
    });

    // Calcul des totaux généraux
    form.montant_ht_appro = totalHT;

    // Si TVA par ligne non utilisée, on prend la TVA générale
    form.montant_tva_appro = totalTVA > 0 ? totalTVA : (form.montant_ht_appro * (form.taux_tva || 0)) / 100;

    // Calcul du TTC (HT + TVA - Remise générale)
    form.montant_ttc_appro = form.montant_ht_appro + form.montant_tva_appro - (parseFloat(form.remise_general_ariary) || 0);
};

const handleMagasin = () => {
    form.details = form.details.map(value => ({
        ...value,
        magasin_id: form.magasin_id
    }));
}

defineExpose({add, newappro, close});
</script>

<template>
    <FormModal
        v-model:open="open"
        :loading="form.processing"
        @close="close"
        @submit="submit"
        :titre="title"
        size="full_screen"
        :show_champ_obligatoir="false"
    >

        <div class="grid lg:grid-cols-12 md:grid-cols-12 grid-cols-12 gap-4">
            <div class="lg:col-span-4 col-span-12">
                <a-form layout="vertical">
                    <div class="grid grid-cols-1  xl:grid-cols-2 gap-0 xl:gap-4">
                        <div>
                            <form-item required has-feedback label="Date" :help="form.errors.date_appro">
                                <a-date-picker
                                    v-model:value="form.date_appro"
                                    :format="dateFormat"
                                    size="large"
                                    class="w-full text-center"
                                    :value-format="'YYYY-MM-DD'"
                                />
                            </form-item>

                            <form-item required has-feedback label="Fournisseur" :help="form.errors.nom_fournisseur">
                                <autocomplete-component :options="fournisseurs"
                                                        v-model="form.nom_fournisseur"
                                                        class=" !w-full"
                                                        size="large"
                                                        :disabled="true"
                                                        placeholder=""
                                />
                            </form-item>
                        </div>

                        <div>
                            <form-item has-feedback required label="N° Bon de Livraison" :help="form.errors.numero_bon_livraison">
                                <a-input v-model:value="form.numero_bon_livraison" size="large"/>
                            </form-item>

                            <form-item has-feedback label="N° Bon de Commande" :help="form.errors.numero_bon_commande">
                                <a-input readonly v-model:value="form.numero_bon_commande" size="large"/>
                            </form-item>
                        </div>
                    </div>

                    <form-item required has-feedback label="Magasin" :help="form.errors.magasin_id">
                        <a-select
                            v-model:value="form.magasin_id"
                            placeholder=""
                            class="w-full"
                            size="large"
                            allow-clear
                            :options="magasins"
                            @change="handleMagasin"
                        >
                        </a-select>
                    </form-item>


                </a-form>

            </div>
            <div class="col-span-8">
                <table class="min-w-full divide-y ">
                    <thead class="bg-primary text-white">
                    <tr>
                        <th class="py-1.5">Désignation</th>
                        <th>Référence</th>
                        <th>PU</th>
                        <th>Qte</th>
                        <th>Remise(%)</th>
                        <th>Remise(Ariary)</th>
                        <th>TVA</th>
                        <th>Montant HT</th>
                        <th>Montant TVA</th>
                        <th width="40px">
                            -
                        </th>
                    </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-green-900 border ">
                    <tr v-for="(a, index_article) in form.details" :key="index_article">
                        <td class="border border-primary/25">
                            <a-input readonly v-model:value="a.designation"
                                     class="rounded-none border-0 focus:border-b-green-600" size="large"/>
                        </td>
                        <td class="border border-primary/25">
                            <a-input readonly v-model:value="a.reference"
                                     class="rounded-none border-0 focus:border-b-green-600" size="large"/>
                        </td>
                        <td class="border border-primary/25 !text-center">
                            <InputNumberWithSepartor
                                v-model="a.prix_unitaire"
                                size="large"
                                @input.native="calculMontant"
                                class="rounded-none w-full !text-right border-0 focus:border-b-green-600"
                            />

                        </td>
                        <td class="border border-primary/25">
                            <InputNumberWithSepartor
                                min="0"
                                v-model="a.quantite"
                                @input.native="calculMontant"
                                class="rounded-none w-full !text-center border-0 focus:border-b-green-600" size="large"
                            />
                        </td>
                        <td class="border border-primary/25">
                            <a-input-number
                                min="0"
                                v-model:value="a.remise"
                                :precision="2"
                                @input.native="calculValeurRemiseEnMontant(index_article)"
                                class="rounded-none w-full !text-right border-0 focus:border-b-green-600" size="large"
                            />
                        </td>
                        <td class="border border-primary/25">
                            <InputNumberWithSepartor
                                min="0"
                                v-model="a.remise_ariary"
                                @input.native="calculValeurRemiseEnPourcentage(index_article)"
                                class="rounded-none w-full !text-right border-0 focus:border-b-green-600" size="large"
                            />
                        </td>
                        <td class="border border-primary/25">
                            <InputNumberWithSepartor
                                min="0"
                                v-model="a.tva_detail"
                                :precision="2"
                                @input.native="calculMontant"
                                class="rounded-none w-full !text-center border-0 focus:border-b-green-600" size="large"
                            />
                        </td>

                        <td class="border border-primary/25">
                            <InputNumberWithSepartor
                                min="0"
                                readonly
                                v-model="a.montant"
                                class="rounded-none w-full !text-right border-0 focus:border-b-green-600" size="large"
                            />
                        </td>
                        <td class="border border-primary/25">
                            <InputNumberWithSepartor
                                min="0"
                                readonly
                                v-model="a.montant_tva"
                                class="rounded-none w-full !text-right border-0 focus:border-b-green-600" size="large"
                            />
                        </td>

                        <td class="text-center cursor-pointer hover:!bg-red-500/20" @click="spliceDetal(index_article)">
                            <font-awesome-icon

                                class="text-red-500 cursor-pointer"
                                icon="fa-trash"
                            />
                            <!--                            <ButtonIcon size="small" @click="pushDetail(a)" icon="fa-trash" type="primary" />-->
                        </td>
                    </tr>
                    </tbody>
                    <tfoot>
                    <tr class="">
                        <td colspan="8">
                            <a-input value="Remise général (%)" readonly class=" !text-right !border-none"
                                     size="large"/>
                        </td>
                        <td>
                            <InputNumberWithSepartor
                                min="0"
                                v-model="form.remise_general"
                                @input.native="calcuRemiseGeneralValeur"
                                class="rounded-none w-full !text-right border-0 focus:border-b-green-600" size="large"
                            />
                        </td>
                    </tr>
                    <tr>
                        <td colspan="8">
                            <a-input value="Remise général (Ar)" readonly class=" !text-right !border-none"
                                     size="large"/>
                        </td>
                        <td>
                            <InputNumberWithSepartor
                                min="0"
                                v-model="form.remise_general_ariary"
                                @input.native="calcuRemiseGeneral"
                                class="rounded-none w-full !text-right border-0 focus:border-b-green-600" size="large"
                            />
                        </td>
                    </tr>
                    <tr>
                        <td colspan="8">
                            <a-input value="Montant Total HT" readonly class=" !text-right !border-none" size="large"/>
                        </td>
                        <td>
                            <InputNumberWithSepartor
                                min="0"
                                readonly
                                v-model="form.montant_ht_appro"
                                class="rounded-none w-full !text-right border-0 focus:border-b-green-600" size="large"
                            />
                        </td>
                    </tr>
                    <tr>
                        <td colspan="8">
                            <a-input value="TVA" readonly class=" !text-right !border-none" size="large"/>
                        </td>
                        <td>
                            <InputNumberWithSepartor
                                min="0"
                                @input.native="calulMontantTva"
                                v-model="form.taux_tva"
                                class="rounded-none w-full !text-right border-0 focus:border-b-green-600" size="large"
                            />
                        </td>
                    </tr>
                    <tr>
                        <td colspan="8">
                            <a-input value="Montant TVA" readonly class=" !text-right !border-none" size="large"/>
                        </td>
                        <td>
                            <InputNumberWithSepartor
                                min="0"
                                readonly
                                v-model="form.montant_tva_appro"
                                class="rounded-none w-full !text-right border-0 focus:border-b-green-600" size="large"
                            />
                        </td>
                    </tr>
                    <tr>
                        <td colspan="8">
                            <a-input value="Montant TTC" readonly class=" !text-right !border-none" size="large"/>
                        </td>
                        <td>
                            <InputNumberWithSepartor
                                min="0"
                                readonly
                                v-model="form.montant_ttc_appro"
                                class="rounded-none w-full !text-right border-0 focus:border-b-green-600" size="large"
                            />
                        </td>
                    </tr>

                    </tfoot>
                </table>
            </div>
        </div>

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
    border: 1px solid #c9c9c9c9;
}
</style>
