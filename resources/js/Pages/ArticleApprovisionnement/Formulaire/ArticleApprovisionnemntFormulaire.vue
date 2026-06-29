<script setup>
import AutocompleteComponent from "@/Components/AutoCompleteComponent/AutocompleteComponent.vue";
import FormItem from "@/Components/FormItem.vue";
import FormModal from "@/Components/FormModal.vue";
import InputNumberWithSepartor from "@/Components/InputNumberWithSepartor.vue";
import { router, useForm } from "@inertiajs/vue3";
import { message } from "ant-design-vue";
import dayjs from "dayjs";
import { ref } from "vue";

const props = defineProps({
    flash: Object,
    magasins: { type: Array, default: [] },
    fournisseurs: { type: Array, default: [] }
});
const dateFormat = 'DD/MM/YYYY';


// Options pour le type d'article
const TYPE_ARTICLE_OPTIONS = [
    { label: "PNEU", value: "PNEU" },
    { label: "PIÈCE MAJEURE", value: "PIÈCE MAJEURE" },
    { label: "CONSOMMMABLE", value: "CONSOMMMABLE" },
];

// Options d'état pour les pneus
const ETAT_PNEU_OPTIONS = [
    { label: "Bon", value: "bon" },
    { label: "Neuf", value: "neuf" },
    { label: "Rechapé", value: "rechape" },
];

const form = useForm({
    id: null,
    date_appro: dayjs().format('YYYY-MM-DD'),
    magasin_id: null,
    fournisseur_id: null,
    nom_fournisseur: null,
    boncommande_fournisseur_id: null,
    numero_bon_livraison: null,
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
    details: [],
    maj_prix_article: false,
});

const filtre = ref({
    search: null,
    magasin_id: "",
    type_article: null,
    article_famille_id: null,
});
const LIST_ARTICLE = ref([]);
const open = ref(false);
const title = ref("");
const leftPanelVisible = ref(true);

const close = () => {
    open.value = false;
    form.reset();
    form.clearErrors();
};

const add = () => {
    title.value = "Nouveau Approvisionnement";
    open.value = true;
    LIST_ARTICLE.value = [];

    if (props.magasins.length > 0) {
        form.magasin_id = props.magasins[0].value;
        fetchArticle();
    }
};

const update = (rowData) => {

    router.visit(`${route('article_approvisionnement.show', {article_approvisionnement: rowData.id})}`, {
        preserveState: true,
        preserveScroll: true,
        only: ['flash'],
        onSuccess: (reponse) => {
            const response = reponse.props.flash.data;
            Object.keys(response).forEach(key => {
                form[key] = response[key];
            });
            fetchArticle();
            title.value = "Modification Approvisionnement";
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
        reference: article.reference,
        type_article: article.type_article,
        numeros_serie: [],
        _serialOpen: false,
    });
}

const spliceDetal = (index) => { form.details.splice(index, 1) }

const calculEcartStock = () => {
    form.details.forEach(value => { value.ecart_stock = parseFloat(value.stock_reel) - parseFloat(value.stock_theorique)})
}

const fetchArticle = () => {
    const search = "";
    const url = route('article.getarticle',{magasin_id: form.magasin_id, search: filtre.value.search,type_article: filtre.value.type_article});
    // const url = route('article.bymagasin_info', {magasin: form.magasin_id, search: filtre.value.search});
    router.visit(url, {
        preserveState: true,
        preserveScroll: true,
        only: ['flash'],
        onSuccess: (response) => {
            console.log(response.props.flash.data.data)
            LIST_ARTICLE.value = response.props.flash.data.data
        },
    });
}

const ensureSerialInputs = (detail) => {
    if ((detail.type_article || "").toString().toLowerCase() !== "pneu") return;
    const qte = parseInt(detail.quantite || 0);
    if (!Array.isArray(detail.numeros_serie)) detail.numeros_serie = [];
    if (qte > detail.numeros_serie.length) {
        for (let i = detail.numeros_serie.length; i < qte; i++) { detail.numeros_serie.unshift({ numero_serie: "", etat: null }) }
    } else if (qte < detail.numeros_serie.length) { detail.numeros_serie = detail.numeros_serie.slice(0, qte); }
};

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
};

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

const handleSearch = () => { fetchArticle() }

// Ajouter une ligne manuellement
const addSerialLine = (detail) => {
    if (!Array.isArray(detail.numeros_serie)) { detail.numeros_serie = [] }
    detail.numeros_serie.unshift({ numero_serie: "", etat: null });
    detail.quantite = detail.numeros_serie.length; // synchro avec Qte
    calculMontant();
};

// Supprimer une ligne manuellement
const removeSerialLine = (detail, index) => {
    if (Array.isArray(detail.numeros_serie) && detail.numeros_serie.length > index) {
        detail.numeros_serie.splice(index, 1);
        detail.quantite = detail.numeros_serie.length; // synchro avec Qte
        calculMontant();
    }
};

defineExpose({add, update, close});
</script>

<template>
    <FormModal v-model:open="open" :loading="form.processing" @close="close" @submit="submit" :titre="title" size="full_screen" :show_champ_obligatoir="false">
        <div class="grid lg:grid-cols-12 md:grid-cols-12 grid-cols-12 gap-4">
            <div class="lg:col-span-4 col-span-12 transition-all duration-300 ease-in-out" v-show="leftPanelVisible">
                <a-form layout="vertical">
                    <div class="grid grid-cols-1 xl:grid-cols-2 gap-0 xl:gap-4">
                        <div>
                            <form-item required has-feedback label="Date" :help="form.errors.date_appro" >
                                <a-date-picker v-model:value="form.date_appro" :format="dateFormat" size="large" :value-format="'YYYY-MM-DD'" class="w-full text-center"/>
                            </form-item>

                            <form-item required has-feedback label="Fournisseur" :help="form.errors.nom_fournisseur">
                                <autocomplete-component :options="fournisseurs" v-model="form.nom_fournisseur" class="!w-full" size="large" placeholder="" />
                            </form-item>
                        </div>

                        <div>
                            <form-item has-feedback required label="N° Bon de Livraison" :help="form.errors.numero_bon_livraison" >
                                <a-input v-model:value="form.numero_bon_livraison" size="large" />
                            </form-item>

                            <form-item has-feedback label="N° Bon de Commande" :help="form.errors.numero_bon_commande" >
                                <a-input v-model:value="form.numero_bon_commande" size="large" />
                            </form-item>
                        </div>
                    </div>

                    <form-item required has-feedback label="Magasin" :help="form.errors.magasin_id" >
                        <a-select v-model:value="form.magasin_id" :options="magasins" @change="fetchArticle" placeholder="" class="w-full" size="large" allow-clear >
                        </a-select>
                    </form-item>

                    <form-item label="">
                        <a-select v-model:value="filtre.type_article"@change="handleSearch" placeholder="Type d'article" size="large" allow-clear class="w-full bg-white !rounded-lg !border-none" >
                            <a-select-option v-for="opt in TYPE_ARTICLE_OPTIONS" :key="opt.value" :value="opt.value" >
                                {{ opt.label }}
                            </a-select-option>
                        </a-select>
                    </form-item>

                    <table class="w-full border-separate border-spacing-0">
                    <thead>
                        <tr>
                            <th colspan="4" class="p-0">
                                <div class="flex flex-wrap items-center justify-between gap-3 bg-transparent text-white  shadow-sm">
                                    <!-- Search -->
                                    <a-input-search v-model:value="filtre.search" @search="handleSearch" size="large" enter-button  class=" w-full"/>
                                </div>
                            </th>
                        </tr>
                        <!-- En-têtes du tableau -->
                        <tr class="bg-gray-100 text-gray-700 text-sm uppercase tracking-wider">
                            <th class="py-3 px-2 text-left">Désignation</th>
                            <th class="py-3 px-2 text-left">Référence</th>
                            <th class="py-3 px-2 text-right w-28">Stock</th>
                            <th class="py-3 px-2 text-center w-12">-</th>
                        </tr>
                    </thead>

                    <!-- Corps du tableau -->
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="(a, index_article) in LIST_ARTICLE" :key="index_article" class="hover:bg-primary/5 transition" >
                            <td class="px-4 py-2">
                                <a-input readonly v-model:value="a.designation" size="large" class="w-full border-none bg-transparent focus:!border-b focus:!border-primary/50"/>
                            </td>

                            <td class="px-4 py-2">
                                <a-input v-model:value="a.reference" readonly size="large" class="w-full border-none bg-transparent focus:!border-b focus:!border-primary/50"/>
                            </td>

                            <td class="px-4 py-2 text-right">
                                <InputNumberWithSepartor v-model="a.stock" size="large" readonly class="w-full text-right border-none bg-transparent focus:!border-b focus:!border-primary/50"/>
                            </td>

                            <td @click="pushDetail(a)" class="px-1 py-2 text-center cursor-pointer hover:bg-primary/10 rounded-md" >
                                <font-awesome-icon class="text-primary text-base" icon="fa-plus" />
                            </td>
                        </tr>
                    </tbody>
                    </table>
                </a-form>
            </div>
            <div :class=" leftPanelVisible ? 'lg:col-span-8 col-span-12 duration' : 'lg:col-span-12 col-span-12'" class="transition-all duration-500 ease-in-out ">
                <!-- Bouton toggle pour masquer/afficher la partie gauche -->
                <div class="flex items-start mb-4">
                    <a-button type="default" size="large" @click="leftPanelVisible =!leftPanelVisible" class="flex items-center gap-2" >
                        <font-awesome-icon :icon="leftPanelVisible ? 'fa-outdent' : 'fa-indent'" class="mr-1" />
                        <!-- {{ leftPanelVisible ? "Masquer articles" : "Afficher articles"}} -->
                    </a-button>
                </div>
            <div class="relative">
                <!-- Bloc scrollable -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y">
                        <thead class="bg-primary/90 text-white">
                            <tr>
                                <th class="py-1.5 px-2 min-w-[120px]">Désignation</th>
                                <th class="py-1.5 px-2 min-w-[100px]">Référence</th>
                                <th class="py-1.5 px-2 min-w-[100px]">PU</th>
                                <th class="py-1.5 px-2 min-w-[120px]">Qte</th>
                                <th class="py-1.5 px-2 min-w-[100px]">Remise(%)</th>
                                <th class="py-1.5 px-2 min-w-[100px]">Remise(Ariary)</th>
                                <th class="py-1.5 px-2 min-w-[100px]">TVA</th>
                                <th class="py-1.5 px-2 min-w-[100px]">Montant HT</th>
                                <th class="py-1.5 px-2 min-w-[100px]">Montant TVA</th>
                                <!-- Colonne fixée -->
                                <th class="py-1.5 px-2 min-w-[65px] sticky right-0 bg-primary/90">-</th>
                            </tr>
                        </thead>

                        <tbody class="bg-white divide-y divide-green-900 border">
                        <tr v-for="(a, index_article) in form.details" :key="index_article">
                            <td class="border border-primary/25 px-2 text-base" >{{ a.designation }}</td>
                            <td class="border border-primary/25 px-2 text-base" >{{ a.reference }}</td>

                            <td class="border border-primary/25 !text-center" >
                                <InputNumberWithSepartor v-model="a.prix_unitaire" size="large" @input.native="calculMontant" class="rounded-none w-full !text-right border-0 focus:border-b-green-600"/>
                            </td>

                            <td class=" flex items-center justify-center border-primary/25">
                                <div class="flex items-center justify-center w-full">
                                    <InputNumberWithSepartor min="0" v-model="a.quantite" @input.native="(e) => { calculMontant(); }" size="large" class="rounded-none w-full text-center border-0 focus:border-b-green-600"/>
                                    <a-button v-if="(a.type_article || '').toString().toUpperCase() === 'PNEU'" size="large" class="!rounded-none" @click="() => { ensureSerialInputs(a); a._serialOpen = true; }">
                                        <font-awesome-icon icon="fa-plus"/>
                                    </a-button>
                                </div>

                                <!-- gestion série PNEU -->
                                <div v-if="(a.type_article || '').toString().toUpperCase() === 'PNEU'" class="mt-1 flex items-center gap-1">
                                <a-popover v-model:open="a._serialOpen" placement="bottomLeft" trigger="click">
                                    <template #content>
                                        <div class="p-4 w-full max-h-80 overflow-y-auto">
                                            <div class="flex justify-between items-center mb-6 gap-4">
                                                <div class="text-sm font-semibold">Numéros de série ({{ a.quantite || 0 }})</div>
                                                <div class="flex justify-center items-center">
                                                    <a-button type="dashed" size="large" @click="addSerialLine(a)">
                                                        <font-awesome-icon icon="fa-plus" class="mr-1"/> Ajouter série
                                                    </a-button>
                                                </div>
                                            </div>

                                            <div class="space-y-2 overflow-auto">
                                                <div v-for="(sn, snIdx) in a.numeros_serie || []" :key="snIdx" class="grid grid-cols-12 gap-2 items-center">
                                                    <div class="col-span-6">
                                                        <a-input v-model:value="a.numeros_serie[snIdx].numero_serie" size="large" :placeholder="`N° série ${snIdx + 1}`"/>
                                                    </div>
                                                    <div class="col-span-4">
                                                        <a-select v-model:value="a.numeros_serie[snIdx].etat" size="large" class="w-full" :options="ETAT_PNEU_OPTIONS" allow-clear placeholder="Etat"/>
                                                    </div>
                                                    <div class="col-span-2 flex justify-center">
                                                        <a-button type="text" danger @click="removeSerialLine(a, snIdx)" size="small">
                                                            <font-awesome-icon icon="fa-trash"/>
                                                        </a-button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                </a-popover>
                                </div>
                            </td>

                            <td class="border border-primary/25" >
                                <a-input-number min="0" v-model:value="a.remise" :precision="2" @input.native="calculValeurRemiseEnMontant(index_article)" size="large"
                                class="rounded-none w-full !text-right border-0 focus:border-b-green-600"/>
                            </td>

                            <td class="border border-primary/25" >
                                <InputNumberWithSepartor min="0" v-model="a.remise_ariary" @input.native="calculValeurRemiseEnPourcentage(index_article)" size="large"
                                class="rounded-none w-full !text-right border-0 focus:border-b-green-600"/>
                            </td>

                            <td class="border border-primary/25">
                                <InputNumberWithSepartor v-model="a.tva_detail" min="0" :precision="2" @input.native="calculMontant" size="large"
                                class="rounded-none w-full !text-center border-0 focus:border-b-green-600"/>
                            </td>

                            <td class="border border-primary/25" >
                                <InputNumberWithSepartor min="0" readonly v-model="a.montant" size="large" class="rounded-none w-full !text-right border-0 focus:border-b-green-600"/>
                            </td>

                            <td class="border border-primary/25" >
                                <InputNumberWithSepartor min="0" readonly v-model="a.montant_tva" class="rounded-none w-full !text-right border-0 focus:border-b-green-600" size="large"/>
                            </td>

                            <!-- colonne "-" sticky -->
                            <td class="text-center cursor-pointer hover:bg-red-500/20 sticky right-0 bg-white" >
                                <font-awesome-icon @click="spliceDetal(index_article)" class="text-red-500 cursor-pointer" icon="fa-trash"/>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>

                <!-- FOOTER en dehors du scroll -->
                <div class="overflow-hidden">
                    <table class="min-w-full">
                        <tfoot>
                            <tr>
                                <td colspan="8">
                                    <a-input value="Remise général (%)" readonly class="!text-right !border-none" size="large"/>
                                </td>
                                <td>
                                    <InputNumberWithSepartor min="0" v-model="form.remise_general" @input.native="calcuRemiseGeneralValeur" class="rounded-none w-full !text-right border-0 focus:border-b-green-600" size="large"/>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="8">
                                    <a-input value="Remise général (Ar)" readonly class="!text-right !border-none border-0 right-0" size="large"/>
                                </td>
                                <td>
                                    <InputNumberWithSepartor min="0" v-model="form.remise_general_ariary" @input.native="calcuRemiseGeneral" class="rounded-none w-full !text-right border-0 focus:border-b-green-600" size="large"/>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="8">
                                    <a-input value="Montant Total HT" readonly class="!text-right !border-none" size="large"/>
                                </td>
                                <td>
                                    <InputNumberWithSepartor min="0" readonly v-model="form.montant_ht_appro" class="rounded-none w-full !text-right border-0 focus:border-b-green-600" size="large"/>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="8">
                                    <a-input value="TVA" readonly class="!text-right !border-none" size="large"/>
                                </td>
                                <td>
                                    <InputNumberWithSepartor min="0" @input.native="calulMontantTva" v-model="form.taux_tva" class="rounded-none w-full !text-right border-0 focus:border-b-green-600" size="large"/>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="8">
                                    <a-input value="Montant TVA" readonly class="!text-right !border-none" size="large"/>
                                </td>
                                <td>
                                    <InputNumberWithSepartor min="0" readonly v-model="form.montant_tva_appro" class="rounded-none w-full !text-right border-0 focus:border-b-green-600" size="large"/>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="8">
                                    <a-input value="Montant TTC" readonly class="!text-right !border-none" size="large"/>
                                </td>
                                <td>
                                    <InputNumberWithSepartor min="0" readonly v-model="form.montant_ttc_appro" class="rounded-none w-full !text-right border-0 focus:border-b-green-600" size="large"/>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <form-item class="mt-4">
                    <a-tag color="default" class="!text-base py-2 px-2">
                        <a-checkbox v-model:checked="form.maj_prix_article" class="!text-base">
                            Appliquer les nouveaux prix unitaire aux articles
                        </a-checkbox>
                    </a-tag>

                    </form-item>
            </div>
        </div>
    </div>
</FormModal>
</template>

<style scoped>
:deep(.ant-upload) {
    width: 100%;
}
:deep(.ant-input-group-wrapper > .ant-input-wrapper > .ant-input),
:deep(
        .ant-input-group-wrapper
            > .ant-input-wrapper
            > .ant-input-group-addon
            > .ant-btn
    ) {
    @apply !rounded-none;
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
:deep(.ant-btn-primary) { @apply !shadow-none}
tfoot tr td {
    border: 1px solid #c9c9c9c9;
}
</style>
