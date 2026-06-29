<script setup>
import { ref, computed } from "vue";
import { SearchOutlined } from "@ant-design/icons-vue";

// ─── State ────────────────────────────────────────────────────────────────────
const open      = ref(false);
const loading   = ref(false);
const error     = ref(null);
const article   = ref(null);
const stats     = ref(null);
const series    = ref([]);
const search    = ref("");
const activeTab = ref("tous");

// ─── Config ───────────────────────────────────────────────────────────────────
const ETAT_CONFIG = {
    neuf:        { color: "green",   label: "Neuf",        icon: "fa-circle-check" },
    bon:         { color: "blue",    label: "Bon état",    icon: "fa-circle-check" },
    rechappe:    { color: "orange",  label: "Rechappé",    icon: "fa-circle-half-stroke" },
    a_remplacer: { color: "red",     label: "À remplacer", icon: "fa-circle-xmark" },
};

const ALERT_CONFIG = {
    critique:      { color: "#ef4444", bg: "bg-red-50 border-red-200",       badge: "error",   label: "Fin de vie dépassée" },
    alerte:        { color: "#f97316", bg: "bg-orange-50 border-orange-200", badge: "warning", label: "≥ 80% durée de vie" },
    avertissement: { color: "#eab308", bg: "bg-yellow-50 border-yellow-200", badge: "warning", label: "≥ 60% durée de vie" },
};

// ─── Computed ─────────────────────────────────────────────────────────────────
const filtered = computed(() => {
    let list = series.value;

    if (activeTab.value === "disponible") list = list.filter(s => s.vehicule === null && s.remorque === null);
    if (activeTab.value === "en_service") list = list.filter(s => s.vehicule !== null || s.remorque !== null);
    if (activeTab.value === "a_remplacer") list = list.filter(s => s.etat === "a_remplacer");

    const q = search.value.trim().toLowerCase();
    if (q) {
        list = list.filter(s =>
            s.numero_serie?.toLowerCase().includes(q) ||
            s.vehicule?.immatriculation?.toLowerCase().includes(q) ||
            s.remorque?.numero_remorque?.toLowerCase().includes(q) ||
            s.position_actuel?.toLowerCase().includes(q) ||
            s.etat?.toLowerCase().includes(q)
        );
    }
    return list;
});

const tabs = computed(() => [
    { key: "tous",         label: "Tous",         count: stats.value?.total       ?? 0 },
    { key: "disponible",   label: "Disponibles",  count: stats.value?.disponible  ?? 0 },
    { key: "en_service",   label: "En service",   count: stats.value?.en_service  ?? 0 },
    { key: "a_remplacer",  label: "À remplacer",  count: stats.value?.a_remplacer ?? 0 },
]);

const columns = [
    { key: "numero_serie",    title: "N° Série",     width: 160 },
    { key: "statut",          title: "Statut",       width: 120 },
    { key: "etat",            title: "État",         width: 120 },
    { key: "affectation",     title: "Affectation",  width: 190 },
    { key: "position_actuel", title: "Position",     width: 155 },
    { key: "kilometrage",     title: "Km / Durée vie", width: 150 },
    { key: "date_montage",    title: "Monté le",     width: 105 },
    { key: "origine",         title: "Provenance",   width: 170 },
];

// ─── Helpers ──────────────────────────────────────────────────────────────────
const pct = (serie) => {
    if (!serie.duree_vie_previsionnel || serie.duree_vie_previsionnel <= 0) return null;
    return Math.min(100, Math.round((serie.total_km / serie.duree_vie_previsionnel) * 100));
};

const progressColor = (serie) => {
    const n = serie.alert_niveau;
    if (n === "critique")      return "#ef4444";
    if (n === "alerte")        return "#f97316";
    if (n === "avertissement") return "#eab308";
    return "#22c55e";
};

const rowClass = (record) => {
    if (record.alert_niveau === "critique")  return "row-critique";
    if (record.alert_niveau === "alerte")   return "row-alerte";
    return "";
};

// ─── API ──────────────────────────────────────────────────────────────────────
const openDrawer = async (articleData) => {
    article.value   = articleData;
    open.value      = true;
    loading.value   = true;
    error.value     = null;
    series.value    = [];
    stats.value     = null;
    search.value    = "";
    activeTab.value = "tous";

    try {
        const res  = await fetch(route("article.pneu_series", articleData.id), {
            headers: { Accept: "application/json", "X-Requested-With": "XMLHttpRequest" },
        });
        const data = await res.json();
        if (!res.ok || data.error) {
            error.value = data.message ?? data.error ?? "Erreur lors du chargement des séries.";
            return;
        }
        series.value = data.series ?? [];
        stats.value  = data.stats  ?? null;
    } catch (e) {
        error.value = "Impossible de contacter le serveur.";
    } finally {
        loading.value = false;
    }
};

const close = () => { open.value = false; };

defineExpose({ openDrawer, close });
</script>

<template>
    <a-drawer
        v-model:open="open"
        placement="right"
        :width="1100"
        :body-style="{ padding: '0', background: '#f8fafc' }"
        :header-style="{ background: '#1e3a5f', borderBottom: 'none' }"
        @close="close"
    >
        <!-- ── En-tête personnalisé ──────────────────────────────────────── -->
        <template #title>
            <div class="flex items-center gap-3 py-1">
                <div class="w-9 h-9 rounded-full bg-white/20 flex items-center justify-center flex-shrink-0">
                    <font-awesome-icon icon="fa-tire" class="text-white text-lg" />
                </div>
                <div>
                    <p class="text-white font-bold text-base leading-tight m-0">
                        {{ article?.designation ?? 'Séries de pneus' }}
                    </p>
                    <p class="text-blue-200 text-xs m-0">
                        Réf. {{ article?.reference }}
                        <span v-if="article?.marque"> · {{ article.marque }}</span>
                    </p>
                </div>
            </div>
        </template>

        <a-spin :spinning="loading" class="h-full">
            <a-alert
                v-if="error && !loading"
                type="error"
                :message="error"
                show-icon
                class="m-5"
            />
            <div v-if="!loading && !error" class="p-5 space-y-5">

                <!-- ── KPI cards ─────────────────────────────────────────── -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                    <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-slate-100 flex items-center justify-center flex-shrink-0">
                            <font-awesome-icon icon="fa-layer-group" class="text-slate-500 text-sm" />
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-slate-800 leading-none">{{ stats?.total ?? 0 }}</p>
                            <p class="text-xs text-slate-400 mt-0.5">Total séries</p>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl p-4 shadow-sm border border-emerald-100 flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-emerald-50 flex items-center justify-center flex-shrink-0">
                            <font-awesome-icon icon="fa-box-open" class="text-emerald-500 text-sm" />
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-emerald-700 leading-none">{{ stats?.disponible ?? 0 }}</p>
                            <p class="text-xs text-emerald-400 mt-0.5">Disponibles</p>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl p-4 shadow-sm border border-blue-100 flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center flex-shrink-0">
                            <font-awesome-icon icon="fa-truck" class="text-blue-500 text-sm" />
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-blue-700 leading-none">{{ stats?.en_service ?? 0 }}</p>
                            <p class="text-xs text-blue-400 mt-0.5">En service</p>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl p-4 shadow-sm border border-red-100 flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-red-50 flex items-center justify-center flex-shrink-0">
                            <font-awesome-icon icon="fa-triangle-exclamation" class="text-red-400 text-sm" />
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-red-600 leading-none">{{ stats?.a_remplacer ?? 0 }}</p>
                            <p class="text-xs text-red-400 mt-0.5">À remplacer</p>
                        </div>
                    </div>
                </div>

                <!-- ── Filtres ────────────────────────────────────────────── -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="flex items-center justify-between px-4 pt-3 pb-0 gap-4 flex-wrap">
                        <!-- Tabs -->
                        <div class="flex gap-0.5">
                            <button
                                v-for="tab in tabs"
                                :key="tab.key"
                                @click="activeTab = tab.key"
                                :class="[
                                    'px-3.5 py-2 text-sm font-medium rounded-t-lg border-b-2 transition-colors flex items-center gap-1.5',
                                    activeTab === tab.key
                                        ? 'border-blue-500 text-blue-600 bg-blue-50/60'
                                        : 'border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-50'
                                ]"
                            >
                                {{ tab.label }}
                                <span :class="[
                                    'inline-flex items-center justify-center min-w-[20px] h-5 px-1.5 text-xs rounded-full font-semibold',
                                    activeTab === tab.key ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-500'
                                ]">{{ tab.count }}</span>
                            </button>
                        </div>

                        <!-- Recherche -->
                        <div class="pb-2">
                            <a-input
                                v-model:value="search"
                                placeholder="N° série, véhicule, position…"
                                allow-clear
                                size="middle"
                                class="w-64"
                            >
                                <template #prefix>
                                    <SearchOutlined class="text-gray-300" />
                                </template>
                            </a-input>
                        </div>
                    </div>

                    <div class="border-t border-gray-100" />

                    <!-- ── Table ──────────────────────────────────────────── -->
                    <a-table
                        :columns="columns"
                        :data-source="filtered"
                        :pagination="{ pageSize: 15, size: 'small', showTotal: (t) => `${t} pneu(s)` }"
                        size="small"
                        row-key="id"
                        :row-class-name="rowClass"
                        :scroll="{ x: 1050 }"
                    >
                        <template #bodyCell="{ column, record }">

                            <!-- N° Série -->
                            <template v-if="column.key === 'numero_serie'">
                                <div class="flex items-center gap-1.5">
                                    <div
                                        v-if="record.alert_niveau"
                                        :title="ALERT_CONFIG[record.alert_niveau]?.label"
                                        class="w-1.5 h-1.5 rounded-full flex-shrink-0"
                                        :style="{ background: ALERT_CONFIG[record.alert_niveau]?.color }"
                                    />
                                    <code class="text-xs font-mono font-semibold text-gray-800 bg-gray-100 px-1.5 py-0.5 rounded">
                                        {{ record.numero_serie }}
                                    </code>
                                </div>
                            </template>

                            <!-- Statut disponible / en service -->
                            <template v-else-if="column.key === 'statut'">
                                <a-tag
                                    v-if="record.vehicule !== null || record.remorque !== null"
                                    color="blue"
                                    :bordered="false"
                                    class="!text-xs !font-medium"
                                >
                                    <font-awesome-icon icon="fa-truck" class="mr-1 text-[10px]" />
                                    En service
                                </a-tag>
                                <a-tag
                                    v-else
                                    color="green"
                                    :bordered="false"
                                    class="!text-xs !font-medium"
                                >
                                    <font-awesome-icon icon="fa-box-open" class="mr-1 text-[10px]" />
                                    Disponible
                                </a-tag>
                            </template>

                            <!-- État -->
                            <template v-else-if="column.key === 'etat'">
                                <a-tooltip :title="ETAT_CONFIG[record.etat]?.label">
                                    <a-tag
                                        :color="ETAT_CONFIG[record.etat]?.color ?? 'default'"
                                        :bordered="false"
                                        class="!text-xs"
                                    >
                                        <font-awesome-icon
                                            :icon="ETAT_CONFIG[record.etat]?.icon ?? 'fa-circle'"
                                            class="mr-1 text-[10px]"
                                        />
                                        {{ ETAT_CONFIG[record.etat]?.label ?? record.etat ?? '—' }}
                                    </a-tag>
                                </a-tooltip>
                            </template>

                            <!-- Affectation véhicule/remorque -->
                            <template v-else-if="column.key === 'affectation'">
                                <div v-if="record.vehicule" class="flex items-center gap-1.5 text-xs">
                                    <div class="w-6 h-6 rounded bg-blue-50 flex items-center justify-center flex-shrink-0">
                                        <font-awesome-icon icon="fa-truck" class="text-blue-400 text-[10px]" />
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-semibold text-gray-800 truncate leading-none">
                                            {{ record.vehicule.immatriculation }}
                                        </p>
                                        <p class="text-gray-400 truncate text-[10px] leading-none mt-0.5">
                                            {{ record.vehicule.marque }} {{ record.vehicule.modele }}
                                        </p>
                                    </div>
                                </div>
                                <div v-else-if="record.remorque" class="flex items-center gap-1.5 text-xs">
                                    <div class="w-6 h-6 rounded bg-indigo-50 flex items-center justify-center flex-shrink-0">
                                        <font-awesome-icon icon="fa-trailer" class="text-indigo-400 text-[10px]" />
                                    </div>
                                    <p class="font-semibold text-gray-800 leading-none">{{ record.remorque.numero_remorque }}</p>
                                </div>
                                <div v-else class="flex items-center gap-1.5 text-xs text-gray-400">
                                    <div class="w-6 h-6 rounded bg-gray-50 flex items-center justify-center flex-shrink-0">
                                        <font-awesome-icon icon="fa-warehouse" class="text-gray-300 text-[10px]" />
                                    </div>
                                    <span>Stock – {{ record.nom_magasin ?? '—' }}</span>
                                </div>
                            </template>

                            <!-- Position -->
                            <template v-else-if="column.key === 'position_actuel'">
                                <a-tag
                                    v-if="record.position_actuel"
                                    :bordered="false"
                                    class="!text-xs !bg-slate-100 !text-slate-600"
                                >
                                    <font-awesome-icon icon="fa-location-dot" class="mr-1 text-[10px]" />
                                    {{ record.position_actuel }}
                                </a-tag>
                                <span v-else class="text-gray-300 text-xs">—</span>
                            </template>

                            <!-- Km / Durée de vie -->
                            <template v-else-if="column.key === 'kilometrage'">
                                <div v-if="record.total_km > 0 || record.duree_vie_previsionnel" class="space-y-1">
                                    <div class="flex items-center justify-between gap-2 text-xs">
                                        <span class="font-mono font-semibold text-gray-700">
                                            {{ record.total_km?.toLocaleString() ?? 0 }} km
                                        </span>
                                        <span v-if="pct(record) !== null" class="text-gray-400">
                                            {{ pct(record) }}%
                                        </span>
                                    </div>
                                    <a-progress
                                        v-if="pct(record) !== null"
                                        :percent="pct(record)"
                                        :show-info="false"
                                        :stroke-color="progressColor(record)"
                                        :stroke-width="5"
                                        stroke-linecap="round"
                                    />
                                    <p v-if="record.alert_niveau" class="text-[10px] leading-none" :style="{ color: ALERT_CONFIG[record.alert_niveau]?.color }">
                                        {{ ALERT_CONFIG[record.alert_niveau]?.label }}
                                    </p>
                                </div>
                                <span v-else class="text-gray-300 text-xs">—</span>
                            </template>

                            <!-- Date montage -->
                            <template v-else-if="column.key === 'date_montage'">
                                <span v-if="record.date_montage" class="text-xs text-gray-600">
                                    {{ new Date(record.date_montage).toLocaleDateString('fr-FR') }}
                                </span>
                                <span v-else class="text-gray-300 text-xs">—</span>
                            </template>

                            <!-- Provenance -->
                            <template v-else-if="column.key === 'origine'">
                                <div class="text-xs space-y-0.5">
                                    <div v-if="record.date_appro" class="text-gray-500">
                                        {{ new Date(record.date_appro).toLocaleDateString('fr-FR') }}
                                    </div>
                                    <div v-if="record.nom_fournisseur" class="text-gray-700 font-medium truncate max-w-[160px]">
                                        {{ record.nom_fournisseur }}
                                    </div>
                                    <div v-if="record.voyage_count" class="text-blue-400 text-[10px]">
                                        {{ record.voyage_count }} voyage(s)
                                    </div>
                                </div>
                            </template>

                        </template>

                        <template #emptyText>
                            <a-empty
                                :description="search ? 'Aucun pneu correspond à votre recherche' : 'Aucune série trouvée'"
                                class="py-10"
                            />
                        </template>
                    </a-table>
                </div>

            </div>
        </a-spin>
    </a-drawer>
</template>

<style scoped>
:deep(.row-critique) { background-color: #fff5f5; }
:deep(.row-critique:hover > td) { background-color: #fee2e2 !important; }
:deep(.row-alerte) { background-color: #fff7ed; }
:deep(.row-alerte:hover > td) { background-color: #ffedd5 !important; }

:deep(.ant-table-thead > tr > th) {
    background: #f8fafc;
    font-size: 11px;
    font-weight: 600;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    padding: 10px 12px;
}
:deep(.ant-table-tbody > tr > td) {
    padding: 8px 12px;
    vertical-align: middle;
}
:deep(.ant-drawer-header-title) {
    flex-direction: row-reverse;
}
:deep(.ant-drawer-close) {
    color: white;
    opacity: 0.7;
}
:deep(.ant-drawer-close:hover) {
    opacity: 1;
    background: rgba(255,255,255,0.1);
}
</style>
