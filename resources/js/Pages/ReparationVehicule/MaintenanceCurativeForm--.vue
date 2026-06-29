<script setup>
import { ref, toRefs, watch, onMounted, computed  } from "vue";
import { useForm, router } from "@inertiajs/vue3";
import FormModal from "@/Components/FormModal.vue";
import FormItem from "@/Components/FormItem.vue";
import AutocompleteComponent from "@/Components/AutoCompleteComponent/AutocompleteComponent.vue";
import InputNumberWithSepartor from "@/Components/InputNumberWithSepartor.vue";
import dayjs from "dayjs";
import { message } from "ant-design-vue";
import axios from "axios";

const props = defineProps({
    vehicules: Array,
    magasins: Array,
    remorques: Array,
    salaries: Array,
    articles: Array, // All articles for "Article à Remplacer"
});

const { vehicules, remorques, magasins, salaries, articles } = toRefs(props);

const open = ref(false);
const title = ref("Nouvelle Maintenance Curative");
const activePieceKey = ref("vehicule"); // "vehicule", "remorque" or "consommation"
const activeCollapseKeys = ref([]);
const activeDetailCollapseKeys = ref({}); // { articleIndex: [detailIndexes] }

const articlesByMagasin = ref({}); // { magasin_id: [articles] }
const articlesConsommableByMagasin = ref({}); // { magasin_id: [consommable articles] }

const form = useForm({
    id: null,
    vehicule_id: null,
    immatriculation: null,
    responsable_id: null,
    date_reparation: dayjs().format('YYYY-MM-DD HH:mm:ss'),
    date_fin_reparation: null,
    observations: null,
    statut: 'en_cours',
    prix_total_pieces: 0,
    prix_total_main_oeuvre: 0,
    montant_total: 0,
    articles: [
        {
            id: null,
            is_remorque: false,
            is_consommable: false,
            numero_remorque: null,
            details: []
        }
    ]
});

const createNewDetail = () => ({
    id: null,

    // Left side: Article à Remplacer / Réparer (suffix _changer)
    article_changer_id: null,
    reference_article_changer: null,
    designation_article_changer: null,
    emplacement_article_changer: null,
    libelle_changer: null,
    numero_serie_changer: null,
    quantite_article_changer: 1,
    technicien_changer: null,
    tarifs_horaires_changer: 0,
    nbre_heure_changer: 0,
    total_main_oeuvre_changer: 0,

    // Right side: Nouvel Article / Pièce de Rechange (suffix _article)
    magasin_id: null,
    article_id: null,
    reference_article: null,
    designation_article: null,
    emplacement_article: null,
    libelle: null,
    numero_serie_article: null,
    quantite_article: 1,
    prix_unitaire_article: 0,
    montant_pieces_article: 0,
    technicien: null,
    tarifs_horaires: 0,
    nbre_heure: 0,
    total_main_oeuvre: 0,

    observations: null,
    stock_disponible: 0
});

const add = () => {
    form.reset();
    form.id = null;
    form.articles = [
        {
            id: null,
            is_remorque: false,
            numero_remorque: null,
            details: [createNewDetail()]
        }
    ];
    title.value = "Nouvelle Maintenance Curative";
    open.value = true;
};

const update = (rowData) => {
    title.value = "Modifier Maintenance Curative";
    router.visit(route('reparation_vehicule.show', rowData.id), {
        preserveState: true,
        only: ['flash'],
        onSuccess: (page) => {
            const data = page.props.flash.data;
            Object.assign(form, data);

            // Open all article panels
            activeCollapseKeys.value = form.articles.map((_, index) => index);

            // Open all detail panels inside each article
            form.articles.forEach((article, aIdx) => {
                activeDetailCollapseKeys.value[aIdx] = article.details.map((_, dIdx) => dIdx);

                article.details.forEach(detail => {
                    // Pre-fetch articles for the magasin
                    if (detail.magasin_id) {
                        if (article.is_consommable) {
                            fetchArticlesConsommableForMagasin(detail.magasin_id);
                        } else {
                            fetchArticlesForMagasin(detail.magasin_id);
                        }
                    }
                });
            });

            open.value = true;
        }
    });
};

const close = () => {
    open.value = false;
    form.reset();
};

const submit = () => {
    // Validate quantities cumulatively by (magasin, article)
    const consumption = {};
    let invalidQuantity = false;

    form.articles.forEach(article => {
        article.details.forEach(detail => {
            if (detail.article_id && detail.magasin_id) {
                const key = `${detail.magasin_id}-${detail.article_id}`;
                if (!consumption[key]) {
                    consumption[key] = {
                        total: 0,
                        stock: detail.stock_disponible,
                        designation: detail.designation_article
                    };
                }
                consumption[key].total += (parseFloat(detail.quantite_article) || 0);
            }
        });
    });

    for (const key in consumption) {
        const item = consumption[key];
        if (item.total > item.stock) {
            invalidQuantity = true;
            message.error(`La quantité totale pour "${item.designation}" (${item.total}) dépasse le stock disponible (${item.stock})`);
        }
    }

    if (invalidQuantity) return;

    const url = form.id ? route('reparation_vehicule.update', form.id) : route('reparation_vehicule.store');
    const method = form.id ? 'put' : 'post';

    form.submit(method, url, {
        onSuccess: () => close(),
    });
};

const getEffectiveMaxStock = (currentDetail, currentAIdx, currentDIdx) => {
    if (!currentDetail.article_id || !currentDetail.magasin_id) return undefined;

    let consumedByOthers = 0;
    form.articles.forEach((article, aIdx) => {
        article.details.forEach((detail, dIdx) => {
            // Skip the current field we are validating
            if (aIdx === currentAIdx && dIdx === currentDIdx) return;

            if (detail.article_id === currentDetail.article_id && detail.magasin_id === currentDetail.magasin_id) {
                consumedByOthers += (parseFloat(detail.quantite_article) || 0);
            }
        });
    });

    return Math.max(0, currentDetail.stock_disponible - consumedByOthers);
};

const getGlobalAllocated = (magasinId, articleId) => {
    if (!magasinId || !articleId) return 0;
    let total = 0;
    form.articles.forEach(article => {
        article.details.forEach(detail => {
            if (detail.article_id === articleId && detail.magasin_id === magasinId) {
                total += (parseFloat(detail.quantite_article) || 0);
            }
        });
    });
    return total;
};

const fetchArticlesForMagasin = async (magasinId) => {
    if (!magasinId || articlesByMagasin.value[magasinId]) return;

    try {
        const response = await axios.get(route('reparation_vehicule.get_articles_by_magasin'), {
            params: { magasin_id: magasinId }
        });
        articlesByMagasin.value[magasinId] = response.data;
    } catch (error) {
        console.error("Error fetching articles for magasin:", error);
    }
};

const fetchArticlesConsommableForMagasin = async (magasinId) => {
    if (!magasinId || articlesConsommableByMagasin.value[magasinId]) return;
    try {
        const response = await axios.get(route('reparation_vehicule.get_articles_by_magasin'), {
            params: { magasin_id: magasinId, type_article: 'CONSOMMABLE' }
        });
        articlesConsommableByMagasin.value[magasinId] = response.data;
    } catch (error) {
        console.error("Error fetching consommable articles for magasin:", error);
    }
};

const onMagasinChange = (magasinId, detail) => {
    detail.article_id = null;
    detail.reference_article = null;
    detail.designation_article = null;
    detail.stock_disponible = 0;
    detail.prix_unitaire_article = 0;
    fetchArticlesForMagasin(magasinId);
};

const onArticleNewChange = (articleId, detail) => {
    const articleList = articlesByMagasin.value[detail.magasin_id] || [];
    const article = articleList.find(a => a.id === articleId);
    if (article) {
        detail.reference_article = article.reference;
        detail.designation_article = article.designation;
        detail.stock_disponible = article.stock;
        detail.prix_unitaire_article = article.prix_unitaire || 0;
    }
};

const onMagasinConsommableChange = (magasinId, detail) => {
    detail.article_id = null;
    detail.reference_article = null;
    detail.designation_article = null;
    detail.stock_disponible = 0;
    detail.prix_unitaire_article = 0;
    fetchArticlesConsommableForMagasin(magasinId);
};

const onArticleConsommableChange = (articleId, detail) => {
    const articleList = articlesConsommableByMagasin.value[detail.magasin_id] || [];
    const article = articleList.find(a => a.id === articleId);
    if (article) {
        detail.reference_article = article.reference;
        detail.designation_article = article.designation;
        detail.stock_disponible = article.stock;
        detail.prix_unitaire_article = article.prix_unitaire || 0;
    }
};

const onArticleOldChange = (articleId, detail) => {
    const article = articles.value.find(a => a.id === articleId);
    if (article) {
        detail.reference_article_changer = article.reference;
        detail.designation_article_changer = article.designation;
    }
};

const addArticle = (isRemorque = false, isConsommable = false) => {
    form.articles.push({
        id: null,
        is_remorque: isRemorque,
        is_consommable: isConsommable,
        numero_remorque: null,
        details: [createNewDetail()]
    });

    // Automatically open the new article panel
    const newIdx = form.articles.length - 1;
    if (!activeCollapseKeys.value.includes(newIdx)) {
        activeCollapseKeys.value.push(newIdx);
    }

    // Automatically open the first detail panel of the new article
    activeDetailCollapseKeys.value[newIdx] = [0];
};

const addDetail = (articleIndex) => {
    form.articles[articleIndex].details.push(createNewDetail());

    // Automatically open the new detail panel
    const newDetailIdx = form.articles[articleIndex].details.length - 1;
    if (!activeDetailCollapseKeys.value[articleIndex]) {
        activeDetailCollapseKeys.value[articleIndex] = [];
    }
    activeDetailCollapseKeys.value[articleIndex].push(newDetailIdx);
};

const removeDetail = (articleIndex, detailIndex) => {
    form.articles[articleIndex].details.splice(detailIndex, 1);
};

const removeArticle = (index) => {
    form.articles.splice(index, 1);
};

const onParentCollapseChange = (keys) => {
    const activeKeys = Array.isArray(keys) ? keys : [keys];
    activeKeys.forEach(aIdx => {
        const article = form.articles[aIdx];
        if (article && article.details) {
            // Open all detail panels for this article
            activeDetailCollapseKeys.value[aIdx] = article.details.map((_, dIdx) => dIdx);
        }
    });
};

// Check if a primary vehicle repair already exists
const hasPrimaryVehicleRepair = computed(() => {
    return form.articles.some(article => !article.is_remorque && !article.is_consommable);
});

// Calculate global totals
watch(() => form.articles, () => {
    let totalPieces = 0;
    let totalMO = 0;

    form.articles.forEach(article => {
        let artPieces = 0;
        let artMO = 0;
        article.details.forEach(detail => {
            // Update individual line piece totals
            detail.montant_pieces_article = detail.quantite_article * detail.prix_unitaire_article;

            // Auto-calculate MO if both rate and hours are provided
            if (detail.nbre_heure > 0 && detail.tarifs_horaires > 0) {
                detail.total_main_oeuvre = detail.nbre_heure * detail.tarifs_horaires;
            }
            if (detail.nbre_heure_changer > 0 && detail.tarifs_horaires_changer > 0) {
                detail.total_main_oeuvre_changer = detail.nbre_heure_changer * detail.tarifs_horaires_changer;
            }

            artPieces += detail.montant_pieces_article;
            artMO += (parseFloat(detail.total_main_oeuvre) || 0) + (parseFloat(detail.total_main_oeuvre_changer) || 0);
        });
        article.prix_total_pieces = artPieces;
        article.prix_total_main_oeuvre = artMO;
        article.montant_total = artPieces + artMO;

        totalPieces += artPieces;
        totalMO += artMO;
    });

    form.prix_total_pieces = totalPieces;
    form.prix_total_main_oeuvre = totalMO;
    form.montant_total = totalPieces + totalMO;
}, { deep: true });

watch(activePieceKey, (newKey) => {
    const getFilter = () => {
        if (newKey === 'remorque') return a => a.is_remorque;
        if (newKey === 'consommation') return a => a.is_consommable;
        return a => !a.is_remorque && !a.is_consommable;
    };
    const filter = getFilter();
    const hasItems = form.articles.some(filter);
    if (!hasItems) {
        if (newKey === 'remorque') addArticle(true);
        else if (newKey === 'consommation') addArticle(false, true);
        else addArticle();
    } else {
        form.articles.forEach((article, aIdx) => {
            if (filter(article)) {
                if (!activeCollapseKeys.value.includes(aIdx)) {
                    activeCollapseKeys.value.push(aIdx);
                }
                activeDetailCollapseKeys.value[aIdx] = article.details.map((_, dIdx) => dIdx);
            }
        });
    }
});

defineExpose({ add, update });
</script>

<template>
    <FormModal
        v-model:open="open"
        :titre="title"
        @close="close"
        @submit="submit"
        :loading="form.processing"
        size="full_screen"
        :show_champ_obligatoir="false"
    >
        <!-- Section: Info Véhicule & Recap -->
        <div class="p-4 grid grid-cols-1 xl:grid-cols-3 gap-4 bg-gray-100">
            <!-- Vehicle Information Card -->
            <div class="xl:col-span-2 bg-white rounded-lg border border-gray-100 overflow-hidden transition-all">
                <div class="px-6 py-4 border-b border-gray-50 bg-gray-50/50 flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-xl bg-blue-500/10 flex items-center justify-center text-blue-600">
                        <font-awesome-icon icon="fa-truck-moving" size="lg" />
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-800 m-0 text-base leading-tight">Informations de Maintenance</h3>
                        <p class="text-xs text-gray-500 m-0">Détails du véhicule et planning</p>
                    </div>
                </div>

                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-5">
                    <form-item label="Véhicule" :help="form.errors.vehicule_id" required class="!m-0">
                        <a-select
                            v-model:value="form.vehicule_id"
                            show-search
                            placeholder="Sélectionner un véhicule"
                            :options="vehicules.map(v => ({ label: v.immatriculation, value: v.id }))"
                            @change="(val, opt) => form.immatriculation = opt.label"
                            class="w-full"
                        />
                    </form-item>
                    <form-item label="Responsable" class="!m-0">
                        <a-select
                            v-model:value="form.responsable_id"
                            show-search
                            placeholder="Sélectionner un responsable"
                            :options="(salaries || []).map(s => ({ label: s.nom + ' ' + (s.prenom || ''), value: s.id }))"
                            allow-clear
                            :filter-option="(input, option) => option.label.toLowerCase().includes(input.toLowerCase())"
                            class="w-full"
                        />
                    </form-item>
                    <form-item label="Date Début" :help="form.errors.date_reparation" required class="!m-0">
                        <a-date-picker
                            v-model:value="form.date_reparation"
                            class="w-full"
                            show-time
                            value-format="YYYY-MM-DD HH:mm:ss"
                            format="DD/MM/YYYY HH:mm"
                        />
                    </form-item>
                    <form-item label="Date Fin" :help="form.errors.date_fin_reparation" class="!m-0">
                        <a-date-picker
                            v-model:value="form.date_fin_reparation"
                            class="w-full"
                            show-time
                            value-format="YYYY-MM-DD HH:mm:ss"
                            format="DD/MM/YYYY HH:mm"
                        />
                    </form-item>
                    <div class="col-span-full">
                        <form-item label="Observations" :help="form.errors.observations" class="!m-0">
                            <a-textarea v-model:value="form.observations" :rows="3" placeholder="Saisir des notes ou observations..." />
                        </form-item>
                    </div>
                </div>
            </div>

            <!-- Financial Summary Card -->
            <div class="bg-white rounded-lg border border-gray-100 overflow-hidden flex flex-col transition-all">
                <div class="px-6 py-4 border-b border-gray-50 bg-gray-50/50 flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-500/10 flex items-center justify-center text-emerald-600">
                        <font-awesome-icon icon="fa-wallet" size="lg" />
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-800 m-0 text-base leading-tight">Récapitulatif Financier</h3>
                        <p class="text-xs text-gray-500 m-0">Estimation des coûts</p>
                    </div>
                </div>

                <div class="p-6 space-y-3 flex-grow">

                    <!-- Pièces Détachées -->
                    <div class="relative overflow-hidden rounded-lg bg-gradient-to-br from-sky-500 via-blue-600 to-indigo-700 px-5 py-3">
                        <div class="absolute -top-12 right-16 w-24 h-24 bg-white/5 rounded-full "></div>

                        <div class="relative flex items-center justify-between mb-2">
                            <span class="text-xs font-bold uppercase text-white/80">
                                Pièces Détachées
                            </span>

                            <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center backdrop-blur-sm">
                                <font-awesome-icon icon="fa-box" class="text-white text-sm" />
                            </div>
                        </div>

                        <div class="relative flex items-end justify-between">
                            <span class="text-3xl font-black text-white">
                                {{ new Intl.NumberFormat().format(form.prix_total_pieces) }}
                            </span>

                            <span class="text-xs font-bold text-white/70">
                                MGA
                            </span>
                        </div>
                    </div>

                    <!-- Main d'œuvre -->
                    <div class="relative overflow-hidden rounded-lg bg-gradient-to-br from-amber-400 via-orange-500 to-red-600 px-5 py-3">
                        <div class="absolute top-12 right-8 w-24 h-24 bg-white/10 rounded-full blur-xs"></div>

                        <div class="relative flex items-center justify-between mb-2">
                            <span class="text-xs font-bold uppercase text-white/80">
                                Main d'œuvre
                            </span>

                            <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center backdrop-blur-sm">
                                <font-awesome-icon icon="fa-user-gear" class="text-white text-sm" />
                            </div>
                        </div>

                        <div class="relative flex items-end justify-between">
                            <span class="text-3xl font-black text-white">
                                {{ new Intl.NumberFormat().format(form.prix_total_main_oeuvre) }}
                            </span>

                            <span class="text-xs font-bold text-white/70">
                                MGA
                            </span>
                        </div>
                    </div>

                    <!-- Total -->
                    <div class="relative overflow-hidden rounded-lg bg-gradient-to-br from-fuchsia-600 via-pink-600 to-rose-600 p-6 border border-white/10">
                        <div class="absolute inset-0 bg-white/[0.03]"></div>
                        <div class="absolute -top-10 -right-10 w-40 h-40 rounded-full bg-white/10 blur-3xl"></div>

                        <div class="relative flex items-center justify-between mb-2">
                            <span class="text-sm font-black uppercase text-white/80">
                                Montant Total
                            </span>

                            <span class="px-3 py-1 rounded-full bg-white/15 backdrop-blur-md text-[10px] font-bold text-white">
                                ESTIMATION
                            </span>
                        </div>

                        <div class="relative flex items-end justify-between">
                            <span class="text-4xl font-black text-white tracking-tight">
                                {{ new Intl.NumberFormat().format(form.montant_total) }}
                            </span>

                            <span class="text-sm font-black text-white/80">
                                MGA
                            </span>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <!-- Section Pièces de rechange -->
        <div class="mt-6 px-4">
            <h3 class="font-bold text-lg mb-2 text-gray-700">Pièces de rechange</h3>

            <a-tabs v-model:activeKey="activePieceKey" class="piece-tabs custom-tabs">
                <!-- Tab: Réparation Véhicule Principal -->
                <a-tab-pane key="vehicule" tab="Véhicule Principal" class="bg-gray-100 px-4 rounded-md">
                    <div class="py-4">
                        <div
                            v-if="!hasPrimaryVehicleRepair"
                            class="relative overflow-hidden rounded-lg border border-dashed border-gray-200 bg-white p-10"
                        >
                            <!-- Decoration -->
                            <div class="absolute -top-10 -right-10 h-32 w-32 rounded-full bg-blue-200/20 blur-3xl"></div>
                            <div class="absolute -bottom-10 -left-10 h-32 w-32 rounded-full bg-indigo-200/20 blur-3xl"></div>

                            <div class="relative flex flex-col items-center text-center">
                                <!-- Icon -->
                                <div
                                    class="mb-5 flex h-16 w-16 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-600 text-white"
                                >
                                    <font-awesome-icon
                                        icon="fa-screwdriver-wrench"
                                        class="text-3xl"
                                    />
                                </div>

                                <!-- Title -->
                                <h3 class="text-xl font-bold text-gray-900">
                                    Aucune réparation enregistrée
                                </h3>

                                <!-- Description -->
                                <p class="mt-2 max-w-md text-sm text-gray-500">
                                    Aucune intervention n'est actuellement associée au véhicule principal.
                                    Ajoutez une réparation pour commencer le suivi des opérations de maintenance.
                                </p>

                                <!-- Button -->
                                <a-button
                                    type="primary"
                                    size="large"
                                    @click="addArticle(false)"
                                    class="mt-6 !h-12 !rounded-lg !border-0 !bg-gradient-to-r !from-blue-600 !to-indigo-600 !px-6 !font-semibold hover:shadow-xl"
                                >
                                    <font-awesome-icon icon="fa-plus" class="mr-2" />
                                    Ajouter une réparation
                                </a-button>
                            </div>
                        </div>

                        <a-collapse v-model:activeKey="activeCollapseKeys" @change="onParentCollapseChange" v-else>
                            <template v-for="(article, aIdx) in form.articles" :key="aIdx">
                                <a-collapse-panel v-if="!article.is_remorque && !article.is_consommable" :key="aIdx" header="Réparation Véhicule Principal">
                                    <template #extra>
                                        <div class="flex items-center space-x-4">
                                            <span class="font-bold text-blue-600">{{ new Intl.NumberFormat().format(article.montant_total) }} MGA</span>
                                            <font-awesome-icon icon="fa-trash" class="text-red-500 cursor-pointer" @click.stop="removeArticle(aIdx)" />
                                        </div>
                                    </template>

                                    <a-collapse ghost v-model:activeKey="activeDetailCollapseKeys[aIdx]">
                                        <a-collapse-panel v-for="(detail, dIdx) in article.details" :key="dIdx" :header="detail.designation_article || 'Nouvel Article'">
                                            <template #extra>
                                                <font-awesome-icon icon="fa-trash" class="text-red-400 cursor-pointer" @click.stop="removeDetail(aIdx, dIdx)" />
                                            </template>

                                            <div class="grid grid-cols-2 gap-8 bg-white p-4 rounded border">
                                                <!-- Left Column: Article à Remplacer / Réparer -->
                                                <div class="space-y-4">
                                                    <h4 class="font-bold text-orange-600 border-b pb-1">Article à Remplacer / Réparer</h4>
                                                    <div class="grid grid-cols-1 gap-2">
                                                        <form-item label="Sélectionner l'article existant" class="!mb-2">
                                                            <a-select
                                                                v-model:value="detail.article_changer_id"
                                                                show-search
                                                                placeholder="Choisir l'article à remplacer"
                                                                :options="(articles || []).map(a => ({ label: a.designation + ' (' + a.reference + ')', value: a.id }))"
                                                                @change="(val) => onArticleOldChange(val, detail)"
                                                                allow-clear
                                                            />
                                                        </form-item>
                                                    </div>
                                                    <div class="grid grid-cols-2 gap-2">
                                                        <form-item label="Référence" class="!mb-2">
                                                            <a-input v-model:value="detail.reference_article_changer" />
                                                        </form-item>
                                                        <form-item label="Désignation" class="!mb-2">
                                                            <a-input v-model:value="detail.designation_article_changer" />
                                                        </form-item>
                                                        <form-item label="Emplacement" class="!mb-2">
                                                            <a-input v-model:value="detail.emplacement_article_changer" />
                                                        </form-item>
                                                        <form-item label="Libellé" class="!mb-2">
                                                            <a-input v-model:value="detail.libelle_changer" />
                                                        </form-item>
                                                        <form-item label="N° Série" class="!mb-2">
                                                            <a-input v-model:value="detail.numero_serie_changer" />
                                                        </form-item>
                                                        <form-item label="Quantité" class="!mb-2">
                                                            <a-input-number v-model:value="detail.quantite_article_changer" class="w-full" />
                                                        </form-item>
                                                    </div>
                                                    <div class="bg-orange-50 p-2 rounded">
                                                        <h5 class="text-xs font-bold mb-2">Main d'œuvre associée</h5>
                                                        <div class="grid grid-cols-2 gap-2">
                                                            <form-item label="Technicien" class="!mb-0">
                                                                <a-input v-model:value="detail.technicien_changer" size="small" />
                                                            </form-item>
                                                            <form-item label="Tarif Horaire" class="!mb-0">
                                                                <InputNumberWithSepartor v-model:value="detail.tarifs_horaires_changer" size="small" />
                                                            </form-item>
                                                            <form-item label="Nb Heures" class="!mb-0">
                                                                <a-input-number v-model:value="detail.nbre_heure_changer" class="w-full" size="small" />
                                                            </form-item>
                                                            <form-item label="Total MO" class="!mb-0">
                                                                <InputNumberWithSepartor v-model:value="detail.total_main_oeuvre_changer" size="small" />
                                                            </form-item>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Right Column: Nouvel Article / Pièce de rechange -->
                                                <div class="space-y-4 border-l pl-8">
                                                    <h4 class="font-bold text-green-600 border-b pb-1">Nouvel Article / Pièce de Rechange</h4>
                                                    <div class="grid grid-cols-2 gap-2">
                                                        <form-item label="Magasin" class="!mb-2 col-span-2">
                                                            <a-select
                                                                v-model:value="detail.magasin_id"
                                                                placeholder="Sélectionner le magasin"
                                                                :options="magasins"
                                                                @change="(val) => onMagasinChange(val, detail)"
                                                            />
                                                        </form-item>
                                                        <form-item label="Sélectionner l'article" class="!mb-2 col-span-2">
                                                            <a-select
                                                                v-model:value="detail.article_id"
                                                                show-search
                                                                placeholder="Choisir l'article"
                                                                :options="articlesByMagasin[detail.magasin_id] || []"
                                                                @change="(val) => onArticleNewChange(val, detail)"
                                                                :disabled="!detail.magasin_id"
                                                            />
                                                            <div v-if="detail.article_id" class="text-xs mt-1">
                                                                Stock Magasin: <span class="font-bold">{{ detail.stock_disponible }}</span> |
                                                                Libre: <span class="font-bold" :class="(detail.stock_disponible - getGlobalAllocated(detail.magasin_id, detail.article_id)) >= 0 ? 'text-green-600' : 'text-red-600'">{{ detail.stock_disponible - getGlobalAllocated(detail.magasin_id, detail.article_id) }}</span>
                                                            </div>
                                                        </form-item>
                                                        <form-item label="Référence" class="!mb-2">
                                                            <a-input v-model:value="detail.reference_article" />
                                                        </form-item>
                                                        <form-item label="Désignation" class="!mb-2">
                                                            <a-input v-model:value="detail.designation_article" />
                                                        </form-item>
                                                        <form-item label="Emplacement" class="!mb-2">
                                                            <a-input v-model:value="detail.emplacement_article" />
                                                        </form-item>
                                                        <form-item label="Libellé" class="!mb-2">
                                                            <a-input v-model:value="detail.libelle" />
                                                        </form-item>
                                                        <form-item label="N° Série" class="!mb-2">
                                                            <a-input v-model:value="detail.numero_serie_article" />
                                                        </form-item>
                                                        <form-item label="Quantité" class="!mb-2">
                                                            <a-input-number v-model:value="detail.quantite_article" class="w-full" :max="getEffectiveMaxStock(detail, aIdx, dIdx)" />
                                                        </form-item>
                                                        <form-item label="Prix Unitaire" class="!mb-2">
                                                            <InputNumberWithSepartor v-model:value="detail.prix_unitaire_article" class="w-full" />
                                                        </form-item>
                                                        <form-item label="Total Pièce" class="!mb-2">
                                                            <div class="font-bold pt-2">{{ new Intl.NumberFormat().format(detail.quantite_article * detail.prix_unitaire_article) }}</div>
                                                        </form-item>
                                                    </div>

                                                    <div class="bg-green-50 p-2 rounded">
                                                        <h5 class="text-xs font-bold mb-2">Main d'œuvre (Installation/Réparation)</h5>
                                                        <div class="grid grid-cols-2 gap-2">
                                                            <form-item label="Technicien" class="!mb-0">
                                                                <a-input v-model:value="detail.technicien" size="small" />
                                                            </form-item>
                                                            <form-item label="Tarif Horaire" class="!mb-0">
                                                                <InputNumberWithSepartor v-model:value="detail.tarifs_horaires" size="small" />
                                                            </form-item>
                                                            <form-item label="Nb Heures" class="!mb-0">
                                                                <a-input-number v-model:value="detail.nbre_heure" class="w-full" size="small" />
                                                            </form-item>
                                                            <form-item label="Total MO" class="!mb-0">
                                                                <InputNumberWithSepartor v-model:value="detail.total_main_oeuvre" size="small" />
                                                            </form-item>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="mt-4">
                                                <form-item label="Observations sur cette ligne">
                                                    <a-textarea v-model:value="detail.observations" :rows="2" />
                                                </form-item>
                                            </div>
                                        </a-collapse-panel>
                                    </a-collapse>

                                    <div class="mt-4">
                                        <a-button type="dashed" block @click="addDetail(aIdx)">+ Ajouter une ligne d'article</a-button>
                                    </div>
                                </a-collapse-panel>
                            </template>
                        </a-collapse>
                    </div>
                </a-tab-pane>

                <!-- Tab: Réparation Remorques -->
                <a-tab-pane key="remorque" tab="Réparation Remorque" class="bg-gray-100 p-4 rounded-md">
                    <div class="py-4">
                        <div class="mb-4 flex justify-end">
                            <a-button type="primary" @click="addArticle(true)">
                                <font-awesome-icon icon="fa-plus" class="mr-2" />
                                Ajouter Réparation Remorque
                            </a-button>
                        </div>

                        <a-collapse v-model:activeKey="activeCollapseKeys" @change="onParentCollapseChange">
                            <template v-for="(article, aIdx) in form.articles" :key="aIdx">
                                <a-collapse-panel v-if="article.is_remorque" :key="aIdx" :header="article.numero_remorque || 'Réparation Remorque'">
                                    <template #extra>
                                        <div class="flex items-center space-x-4">
                                            <span class="font-bold text-blue-600">{{ new Intl.NumberFormat().format(article.montant_total) }} MGA</span>
                                            <font-awesome-icon icon="fa-trash" class="text-red-500 cursor-pointer" @click.stop="removeArticle(aIdx)" />
                                        </div>
                                    </template>

                                    <div class="mb-4 bg-white p-4 rounded border">
                                        <form-item label="Sélectionner la Remorque" required>
                                            <a-select
                                                v-model:value="article.numero_remorque"
                                                show-search
                                                placeholder="Choisir une remorque"
                                                :options="remorques.map(r => ({ label: r.numero_remorque, value: r.numero_remorque }))"
                                            />
                                        </form-item>
                                    </div>

                                    <a-collapse ghost v-model:activeKey="activeDetailCollapseKeys[aIdx]">
                                        <a-collapse-panel v-for="(detail, dIdx) in article.details" :key="dIdx" :header="detail.designation_article || 'Nouvel Article'">
                                            <template #extra>
                                                <font-awesome-icon icon="fa-trash" class="text-red-400 cursor-pointer" @click.stop="removeDetail(aIdx, dIdx)" />
                                            </template>

                                            <div class="grid grid-cols-2 gap-8 bg-white p-4 rounded border">
                                                <!-- Left Column: Article à Remplacer / Réparer -->
                                                <div class="space-y-4">
                                                    <h4 class="font-bold text-orange-600 border-b pb-1">Article à Remplacer / Réparer</h4>
                                                    <div class="grid grid-cols-1 gap-2">
                                                        <form-item label="Sélectionner l'article existant" class="!mb-2">
                                                            <a-select
                                                                v-model:value="detail.article_changer_id"
                                                                show-search
                                                                placeholder="Choisir l'article à remplacer"
                                                                :options="(articles || []).map(a => ({ label: a.designation + ' (' + a.reference + ')', value: a.id }))"
                                                                @change="(val) => onArticleOldChange(val, detail)"
                                                                allow-clear
                                                            />
                                                        </form-item>
                                                    </div>
                                                    <div class="grid grid-cols-2 gap-2">
                                                        <form-item label="Référence" class="!mb-2">
                                                            <a-input v-model:value="detail.reference_article_changer" />
                                                        </form-item>
                                                        <form-item label="Désignation" class="!mb-2">
                                                            <a-input v-model:value="detail.designation_article_changer" />
                                                        </form-item>
                                                        <form-item label="Emplacement" class="!mb-2">
                                                            <a-input v-model:value="detail.emplacement_article_changer" />
                                                        </form-item>
                                                        <form-item label="Libellé" class="!mb-2">
                                                            <a-input v-model:value="detail.libelle_changer" />
                                                        </form-item>
                                                        <form-item label="N° Série" class="!mb-2">
                                                            <a-input v-model:value="detail.numero_serie_changer" />
                                                        </form-item>
                                                        <form-item label="Quantité" class="!mb-2">
                                                            <a-input-number v-model:value="detail.quantite_article_changer" class="w-full" />
                                                        </form-item>
                                                    </div>
                                                    <div class="bg-orange-50 p-2 rounded">
                                                        <h5 class="text-xs font-bold mb-2">Main d'œuvre associée</h5>
                                                        <div class="grid grid-cols-2 gap-2">
                                                            <form-item label="Technicien" class="!mb-0">
                                                                <a-input v-model:value="detail.technicien_changer" size="small" />
                                                            </form-item>
                                                            <form-item label="Tarif Horaire" class="!mb-0">
                                                                <InputNumberWithSepartor v-model:value="detail.tarifs_horaires_changer" size="small" />
                                                            </form-item>
                                                            <form-item label="Nb Heures" class="!mb-0">
                                                                <a-input-number v-model:value="detail.nbre_heure_changer" class="w-full" size="small" />
                                                            </form-item>
                                                            <form-item label="Total MO" class="!mb-0">
                                                                <InputNumberWithSepartor v-model:value="detail.total_main_oeuvre_changer" size="small" />
                                                            </form-item>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Right Column: Nouvel Article / Pièce de rechange -->
                                                <div class="space-y-4 border-l pl-8">
                                                    <h4 class="font-bold text-green-600 border-b pb-1">Nouvel Article / Pièce de Rechange</h4>
                                                    <div class="grid grid-cols-2 gap-2">
                                                        <form-item label="Magasin" class="!mb-2 col-span-2">
                                                            <a-select
                                                                v-model:value="detail.magasin_id"
                                                                placeholder="Sélectionner le magasin"
                                                                :options="magasins"
                                                                @change="(val) => onMagasinChange(val, detail)"
                                                            />
                                                        </form-item>
                                                        <form-item label="Sélectionner l'article" class="!mb-2 col-span-2">
                                                            <a-select
                                                                v-model:value="detail.article_id"
                                                                show-search
                                                                placeholder="Choisir l'article"
                                                                :options="articlesByMagasin[detail.magasin_id] || []"
                                                                @change="(val) => onArticleNewChange(val, detail)"
                                                                :disabled="!detail.magasin_id"
                                                            />
                                                            <div v-if="detail.article_id" class="text-xs mt-1">
                                                                Stock Magasin: <span class="font-bold">{{ detail.stock_disponible }}</span> |
                                                                Libre: <span class="font-bold" :class="(detail.stock_disponible - getGlobalAllocated(detail.magasin_id, detail.article_id)) >= 0 ? 'text-green-600' : 'text-red-600'">{{ detail.stock_disponible - getGlobalAllocated(detail.magasin_id, detail.article_id) }}</span>
                                                            </div>
                                                        </form-item>
                                                        <form-item label="Référence" class="!mb-2">
                                                            <a-input v-model:value="detail.reference_article" />
                                                        </form-item>
                                                        <form-item label="Désignation" class="!mb-2">
                                                            <a-input v-model:value="detail.designation_article" />
                                                        </form-item>
                                                        <form-item label="Emplacement" class="!mb-2">
                                                            <a-input v-model:value="detail.emplacement_article" />
                                                        </form-item>
                                                        <form-item label="Libellé" class="!mb-2">
                                                            <a-input v-model:value="detail.libelle" />
                                                        </form-item>
                                                        <form-item label="N° Série" class="!mb-2">
                                                            <a-input v-model:value="detail.numero_serie_article" />
                                                        </form-item>
                                                        <form-item label="Quantité" class="!mb-2">
                                                            <a-input-number v-model:value="detail.quantite_article" class="w-full" :max="getEffectiveMaxStock(detail, aIdx, dIdx)" />
                                                        </form-item>
                                                        <form-item label="Prix Unitaire" class="!mb-2">
                                                            <InputNumberWithSepartor v-model:value="detail.prix_unitaire_article" class="w-full" />
                                                        </form-item>
                                                        <form-item label="Total Pièce" class="!mb-2">
                                                            <div class="font-bold pt-2">{{ new Intl.NumberFormat().format(detail.quantite_article * detail.prix_unitaire_article) }}</div>
                                                        </form-item>
                                                    </div>

                                                    <div class="bg-green-50 p-2 rounded">
                                                        <h5 class="text-xs font-bold mb-2">Main d'œuvre (Installation/Réparation)</h5>
                                                        <div class="grid grid-cols-2 gap-2">
                                                            <form-item label="Technicien" class="!mb-0">
                                                                <a-input v-model:value="detail.technicien" size="small" />
                                                            </form-item>
                                                            <form-item label="Tarif Horaire" class="!mb-0">
                                                                <InputNumberWithSepartor v-model:value="detail.tarifs_horaires" size="small" />
                                                            </form-item>
                                                            <form-item label="Nb Heures" class="!mb-0">
                                                                <a-input-number v-model:value="detail.nbre_heure" class="w-full" size="small" />
                                                            </form-item>
                                                            <form-item label="Total MO" class="!mb-0">
                                                                <InputNumberWithSepartor v-model:value="detail.total_main_oeuvre" size="small" />
                                                            </form-item>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="mt-4">
                                                <form-item label="Observations sur cette ligne">
                                                    <a-textarea v-model:value="detail.observations" :rows="2" />
                                                </form-item>
                                            </div>
                                        </a-collapse-panel>
                                    </a-collapse>

                                    <div class="mt-4">
                                        <a-button type="dashed" block @click="addDetail(aIdx)">+ Ajouter une ligne d'article</a-button>
                                    </div>
                                </a-collapse-panel>
                            </template>
                        </a-collapse>
                    </div>
                </a-tab-pane>

                <!-- Tab: Consommation -->
                <a-tab-pane key="consommation" tab="Consommation" class="bg-gray-100 p-4 rounded-md">
                    <div class="py-4">
                        <div class="mb-4 flex justify-end">
                            <a-button type="primary" @click="addArticle(false, true)">
                                <font-awesome-icon icon="fa-plus" class="mr-2" />
                                Ajouter Consommation
                            </a-button>
                        </div>

                        <a-collapse v-model:activeKey="activeCollapseKeys" @change="onParentCollapseChange">
                            <template v-for="(article, aIdx) in form.articles" :key="aIdx">
                                <a-collapse-panel v-if="article.is_consommable" :key="aIdx" header="Consommation">
                                    <template #extra>
                                        <div class="flex items-center space-x-4">
                                            <span class="font-bold text-blue-600">{{ new Intl.NumberFormat().format(article.montant_total) }} MGA</span>
                                            <font-awesome-icon icon="fa-trash" class="text-red-500 cursor-pointer" @click.stop="removeArticle(aIdx)" />
                                        </div>
                                    </template>

                                    <a-collapse ghost v-model:activeKey="activeDetailCollapseKeys[aIdx]">
                                        <a-collapse-panel v-for="(detail, dIdx) in article.details" :key="dIdx" :header="detail.designation_article || 'Nouvel Article'">
                                            <template #extra>
                                                <font-awesome-icon icon="fa-trash" class="text-red-400 cursor-pointer" @click.stop="removeDetail(aIdx, dIdx)" />
                                            </template>

                                            <div class="bg-white p-4 rounded border">
                                                <!-- Only Right Column: Nouvel Article / Pièce de rechange (Consommable) -->
                                                <div class="space-y-4">
                                                    <h4 class="font-bold text-green-600 border-b pb-1">Nouvel Article / Pièce de Rechange</h4>
                                                    <div class="grid grid-cols-2 gap-2">
                                                        <form-item label="Magasin" class="!mb-2 col-span-2">
                                                            <a-select
                                                                v-model:value="detail.magasin_id"
                                                                placeholder="Sélectionner le magasin"
                                                                :options="magasins"
                                                                @change="(val) => onMagasinConsommableChange(val, detail)"
                                                            />
                                                        </form-item>
                                                        <form-item label="Sélectionner l'article" class="!mb-2 col-span-2">
                                                            <a-select
                                                                v-model:value="detail.article_id"
                                                                show-search
                                                                placeholder="Choisir l'article"
                                                                :options="articlesConsommableByMagasin[detail.magasin_id] || []"
                                                                @change="(val) => onArticleConsommableChange(val, detail)"
                                                                :disabled="!detail.magasin_id"
                                                            />
                                                            <div v-if="detail.article_id" class="text-xs mt-1">
                                                                Stock Magasin: <span class="font-bold">{{ detail.stock_disponible }}</span> |
                                                                Libre: <span class="font-bold" :class="(detail.stock_disponible - getGlobalAllocated(detail.magasin_id, detail.article_id)) >= 0 ? 'text-green-600' : 'text-red-600'">{{ detail.stock_disponible - getGlobalAllocated(detail.magasin_id, detail.article_id) }}</span>
                                                            </div>
                                                        </form-item>
                                                        <form-item label="Référence" class="!mb-2">
                                                            <a-input v-model:value="detail.reference_article" />
                                                        </form-item>
                                                        <form-item label="Désignation" class="!mb-2">
                                                            <a-input v-model:value="detail.designation_article" />
                                                        </form-item>
                                                        <form-item label="Emplacement" class="!mb-2">
                                                            <a-input v-model:value="detail.emplacement_article" />
                                                        </form-item>
                                                        <form-item label="Libellé" class="!mb-2">
                                                            <a-input v-model:value="detail.libelle" />
                                                        </form-item>
                                                        <form-item label="N° Série" class="!mb-2">
                                                            <a-input v-model:value="detail.numero_serie_article" />
                                                        </form-item>
                                                        <form-item label="Quantité" class="!mb-2">
                                                            <a-input-number v-model:value="detail.quantite_article" class="w-full" :max="getEffectiveMaxStock(detail, aIdx, dIdx)" />
                                                        </form-item>
                                                        <form-item label="Prix Unitaire" class="!mb-2">
                                                            <InputNumberWithSepartor v-model:value="detail.prix_unitaire_article" class="w-full" />
                                                        </form-item>
                                                        <form-item label="Total Pièce" class="!mb-2">
                                                            <div class="font-bold pt-2">{{ new Intl.NumberFormat().format(detail.quantite_article * detail.prix_unitaire_article) }}</div>
                                                        </form-item>
                                                    </div>

                                                    <div class="bg-green-50 p-2 rounded">
                                                        <h5 class="text-xs font-bold mb-2">Main d'œuvre (Installation/Réparation)</h5>
                                                        <div class="grid grid-cols-2 gap-2">
                                                            <form-item label="Technicien" class="!mb-0">
                                                                <a-input v-model:value="detail.technicien" size="small" />
                                                            </form-item>
                                                            <form-item label="Tarif Horaire" class="!mb-0">
                                                                <InputNumberWithSepartor v-model:value="detail.tarifs_horaires" size="small" />
                                                            </form-item>
                                                            <form-item label="Nb Heures" class="!mb-0">
                                                                <a-input-number v-model:value="detail.nbre_heure" class="w-full" size="small" />
                                                            </form-item>
                                                            <form-item label="Total MO" class="!mb-0">
                                                                <InputNumberWithSepartor v-model:value="detail.total_main_oeuvre" size="small" />
                                                            </form-item>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="mt-4">
                                                <form-item label="Observations sur cette ligne">
                                                    <a-textarea v-model:value="detail.observations" :rows="2" />
                                                </form-item>
                                            </div>
                                        </a-collapse-panel>
                                    </a-collapse>

                                    <div class="mt-4">
                                        <a-button type="dashed" block @click="addDetail(aIdx)">+ Ajouter une ligne d'article</a-button>
                                    </div>
                                </a-collapse-panel>
                            </template>
                        </a-collapse>
                    </div>
                </a-tab-pane>
            </a-tabs>
        </div>
    </FormModal>
</template>

<style scoped>
:deep(.ant-tabs-nav) {
    margin-bottom: 0;
}
:deep(.ant-collapse-content-box) {
    background-color: #f9fafb;
}
:deep(.ant-collapse > .ant-collapse-item) {
    margin-bottom: 16px;
    border: 1px solid #d9d9d9;
    border-radius: 8px !important;
    overflow: hidden;
    background-color: white;
}
:deep(.ant-collapse) {
    background-color: transparent;
    border: none;
}

.custom-tabs :deep(.ant-tabs-nav) {
    @apply mb-0 px-4 bg-white border-b border-gray-200;
}

.custom-tabs :deep(.ant-tabs-tab) {
    @apply py-4 px-2 font-semibold text-gray-500 transition-all hover:text-primary;
}

.custom-tabs :deep(.ant-tabs-tab-active) {
    @apply text-primary;
}
</style>
