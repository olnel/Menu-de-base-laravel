<script setup>
import FormItem from "@/Components/FormItem.vue";
import FormModal from "@/Components/FormModal.vue";
import { DeleteOutlined, PlusOutlined } from "@ant-design/icons-vue";
import { useForm } from "@inertiajs/vue3";
import dayjs from "dayjs";
import { ref, computed, watch } from "vue";

const props = defineProps({
    vehicules: { type: Array, default: () => [] },
    remorques: { type: Array, default: () => [] },
    magasins:  { type: Array, default: () => [] },
});

const dateFormat = "DD/MM/YYYY";

const MOTIFS = [
    { value: "usure",     label: "Usure" },
    { value: "crevaison", label: "Crevaison" },
    { value: "vol",       label: "Vol" },
    { value: "fin_vie",   label: "Fin de vie" },
    { value: "autre",     label: "Autre" },
];

const POSITIONS = [
    "Avant gauche", "Avant droit",
    "Arrière gauche", "Arrière droit",
    "Arrière gauche intérieur", "Arrière droit intérieur",
    "Secours",
];

const SUPPORT_TYPES_PERMUTATION = [
    { value: "vehicule", label: "Véhicule" },
    { value: "remorque", label: "Remorque" },
    { value: "magasin",  label: "Magasin (stock)" },
];

// ─── State ────────────────────────────────────────────────────────────────────

const open  = ref(false);
const title = ref("");

// Shared source context
const supportSource    = ref("vehicule");
const vehiculeSourceId = ref(null);
const remorqueSourceId = ref(null);
const magasinSourceId  = ref(null);

// Remplacement: stock magasin for new pneu
const magasinStockId = ref(null);

// Permutation dest context
const supportDest    = ref("vehicule");
const vehiculeDestId = ref(null);
const remorqueDestId = ref(null);
const magasinDestId  = ref(null);

// Pneu option pools
const sourcePneusPool  = ref([]);
const montePneusPool   = ref([]);
const pneusDestOptions = ref([]);

const loadingSource = ref(false);
const loadingMonte  = ref(false);
const loadingDest   = ref(false);

let debounceSourceTimer = null;
let debounceMonteTimer  = null;
let debounceDestTimer   = null;

// ─── Form ─────────────────────────────────────────────────────────────────────

const newLigne = () => ({
    pneu_serie_retire_id: null,
    position:             null,   // remplacement: position unique
    position_retire:      null,   // permutation: position côté A
    pneu_serie_monte_id:  null,
    position_monte:       null,   // permutation: position côté B
    motif:                null,
    date_hors_service:    null,
});

const form = useForm({
    id:             null,
    type_operation: "remplacement",
    date_operation: dayjs().format("YYYY-MM-DD"),
    vehicule_id:    null,
    remorque_id:    null,
    technicien:     null,
    observations:   null,
    lignes: [newLigne()],
});

// ─── Computed ─────────────────────────────────────────────────────────────────

const isPermutation = computed(() => form.type_operation === "permutation");

const sourceOptionsPerRow = computed(() =>
    form.lignes.map((_, i) => {
        const used = form.lignes
            .filter((l, j) => j !== i && l.pneu_serie_retire_id)
            .map(l => l.pneu_serie_retire_id);
        return sourcePneusPool.value.filter(o => !used.includes(o.value));
    })
);

const monteOptionsPerRow = computed(() =>
    form.lignes.map((_, i) => {
        const used = form.lignes
            .filter((l, j) => j !== i && l.pneu_serie_monte_id)
            .map(l => l.pneu_serie_monte_id);
        return montePneusPool.value.filter(o => !used.includes(o.value));
    })
);

const destPermOptionsPerRow = computed(() =>
    form.lignes.map((_, i) => {
        const used = form.lignes
            .filter((l, j) => j !== i && l.pneu_serie_monte_id)
            .map(l => l.pneu_serie_monte_id);
        return pneusDestOptions.value.filter(o => !used.includes(o.value));
    })
);

// ─── Fetch helpers ────────────────────────────────────────────────────────────

const fetchPneusRaw = async (searchText, vehiculeId, remorqueId, magasinId) => {
    try {
        const params = new URLSearchParams();
        if (searchText) params.append("search",      searchText);
        if (vehiculeId) params.append("vehicule_id", vehiculeId);
        if (remorqueId) params.append("remorque_id", remorqueId);
        if (magasinId)  params.append("magasin_id",  magasinId);
        const url = route("pneu_remplacement.search_pneus") + "?" + params.toString();
        const res = await fetch(url, {
            headers: { Accept: "application/json", "X-Requested-With": "XMLHttpRequest" },
        });
        const data = await res.json();
        return (data.pneus ?? []).map(p => ({
            value:           p.id,
            label:           [p.numero_serie, p.type, p.etat ? `(${p.etat})` : null].filter(Boolean).join(" — "),
            position_actuel: p.position_actuel ?? null,
        }));
    } catch {
        return [];
    }
};

const mergePool = (pool, results) => {
    const map = new Map(pool.map(o => [o.value, o]));
    results.forEach(p => map.set(p.value, p));
    return [...map.values()];
};

const loadSourcePneus = async (text = null) => {
    const vid = supportSource.value === "vehicule" ? vehiculeSourceId.value : null;
    const rid = supportSource.value === "remorque" ? remorqueSourceId.value : null;
    const mid = supportSource.value === "magasin"  ? magasinSourceId.value  : null;
    if (!vid && !rid && !mid) return;
    loadingSource.value = true;
    try {
        sourcePneusPool.value = mergePool(sourcePneusPool.value, await fetchPneusRaw(text, vid, rid, mid));
    } finally { loadingSource.value = false; }
};

const loadMontePneus = async (text = null) => {
    if (!magasinStockId.value) return;
    loadingMonte.value = true;
    try {
        montePneusPool.value = mergePool(montePneusPool.value, await fetchPneusRaw(text, null, null, magasinStockId.value));
    } finally { loadingMonte.value = false; }
};

const loadDestPneus = async (text = null) => {
    const vid = supportDest.value === "vehicule" ? vehiculeDestId.value : null;
    const rid = supportDest.value === "remorque" ? remorqueDestId.value : null;
    const mid = supportDest.value === "magasin"  ? magasinDestId.value  : null;
    if (!vid && !rid && !mid) return;
    loadingDest.value = true;
    try {
        pneusDestOptions.value = mergePool(pneusDestOptions.value, await fetchPneusRaw(text, vid, rid, mid));
    } finally { loadingDest.value = false; }
};

const triggerSourceSearch = (text = null) => {
    clearTimeout(debounceSourceTimer);
    debounceSourceTimer = setTimeout(() => loadSourcePneus(text), 300);
};
const triggerMonteSearch = (text = null) => {
    clearTimeout(debounceMonteTimer);
    debounceMonteTimer = setTimeout(() => loadMontePneus(text), 300);
};
const triggerDestSearch = (text = null) => {
    clearTimeout(debounceDestTimer);
    debounceDestTimer = setTimeout(() => loadDestPneus(text), 300);
};

// ─── Watchers ─────────────────────────────────────────────────────────────────

watch(supportSource, () => {
    vehiculeSourceId.value = null;
    remorqueSourceId.value = null;
    magasinSourceId.value  = null;
    sourcePneusPool.value  = [];
    form.lignes.forEach(l => {
        l.pneu_serie_retire_id = null;
        l.position             = null;
        l.position_retire      = null;
    });
});

watch(vehiculeSourceId, (val) => {
    sourcePneusPool.value = [];
    form.vehicule_id      = val;
    form.remorque_id      = null;
    form.lignes.forEach(l => {
        l.pneu_serie_retire_id = null;
        l.position             = null;
        l.position_retire      = null;
    });
    if (val) loadSourcePneus();
});

watch(remorqueSourceId, (val) => {
    sourcePneusPool.value = [];
    form.remorque_id      = val;
    form.vehicule_id      = null;
    form.lignes.forEach(l => {
        l.pneu_serie_retire_id = null;
        l.position             = null;
        l.position_retire      = null;
    });
    if (val) loadSourcePneus();
});

watch(magasinSourceId, (val) => {
    sourcePneusPool.value = [];
    form.lignes.forEach(l => {
        l.pneu_serie_retire_id = null;
        l.position_retire      = null;
    });
    if (val) loadSourcePneus();
});

watch(magasinStockId, (val) => {
    montePneusPool.value = [];
    form.lignes.forEach(l => { l.pneu_serie_monte_id = null; });
    if (val) loadMontePneus();
});

watch(supportDest, () => {
    vehiculeDestId.value   = null;
    remorqueDestId.value   = null;
    magasinDestId.value    = null;
    pneusDestOptions.value = [];
    form.lignes.forEach(l => { l.pneu_serie_monte_id = null; l.position_monte = null; });
});
watch(vehiculeDestId, (val) => {
    pneusDestOptions.value = [];
    form.lignes.forEach(l => { l.pneu_serie_monte_id = null; l.position_monte = null; });
    if (val) triggerDestSearch();
});
watch(remorqueDestId, (val) => {
    pneusDestOptions.value = [];
    form.lignes.forEach(l => { l.pneu_serie_monte_id = null; l.position_monte = null; });
    if (val) triggerDestSearch();
});
watch(magasinDestId, (val) => {
    pneusDestOptions.value = [];
    form.lignes.forEach(l => { l.pneu_serie_monte_id = null; l.position_monte = null; });
    if (val) triggerDestSearch();
});

watch(() => form.type_operation, resetSupportState);

// ─── Ligne handlers ──────────────────────────────────────────────────────────

const onRetireChange = (i, val, option) => {
    const pos = option?.position_actuel
        ?? sourcePneusPool.value.find(o => o.value === val)?.position_actuel
        ?? null;
    form.lignes[i].position = pos;
};

const onRetirePermChange = (i, val, option) => {
    const pos = option?.position_actuel
        ?? sourcePneusPool.value.find(o => o.value === val)?.position_actuel
        ?? null;
    form.lignes[i].position_retire = pos;
};

const onMontePermChange = (i, val, option) => {
    const pos = option?.position_actuel
        ?? pneusDestOptions.value.find(o => o.value === val)?.position_actuel
        ?? null;
    form.lignes[i].position_monte = pos;
};

const onMotifChange = (i, val) => {
    if (val === 'fin_vie') {
        if (!form.lignes[i].date_hors_service)
            form.lignes[i].date_hors_service = dayjs().format('YYYY-MM-DD');
    } else {
        form.lignes[i].date_hors_service = null;
    }
};

const addLigne    = () => form.lignes.push(newLigne());
const removeLigne = (i) => { if (form.lignes.length > 1) form.lignes.splice(i, 1); };

// ─── Helpers ──────────────────────────────────────────────────────────────────

function resetSupportState() {
    supportSource.value    = "vehicule";
    vehiculeSourceId.value = null;
    remorqueSourceId.value = null;
    magasinSourceId.value  = null;
    magasinStockId.value   = null;
    supportDest.value      = "vehicule";
    vehiculeDestId.value   = null;
    remorqueDestId.value   = null;
    magasinDestId.value    = null;
    sourcePneusPool.value  = [];
    montePneusPool.value   = [];
    pneusDestOptions.value = [];
    form.vehicule_id       = null;
    form.remorque_id       = null;
    form.lignes            = [newLigne()];
}

const close = () => {
    open.value = false;
    form.reset();
    form.clearErrors();
    resetSupportState();
};

const add = () => {
    title.value = "Nouveau Remplacement / Permutation";
    open.value  = true;
};

const edit = (rowData) => {
    title.value         = "Modifier l'opération";
    form.id             = rowData.id;
    form.type_operation = rowData.type_operation;
    form.date_operation = rowData.date_operation;
    form.vehicule_id    = rowData.vehicule_id;
    form.remorque_id    = rowData.remorque_id;
    form.technicien     = rowData.technicien;
    form.observations   = rowData.observations;

    if (rowData.type_operation === 'remplacement') {
        form.lignes = [{
            pneu_serie_retire_id: rowData.pneu_serie_retire_id,
            position:             rowData.position_retire,
            position_retire:      null,
            pneu_serie_monte_id:  rowData.pneu_serie_monte_id,
            position_monte:       rowData.position_monte,
            motif:                rowData.motif,
            date_hors_service:    rowData.date_hors_service,
        }];
        if (rowData.vehicule_id) { supportSource.value = "vehicule"; vehiculeSourceId.value = rowData.vehicule_id; }
        else if (rowData.remorque_id) { supportSource.value = "remorque"; remorqueSourceId.value = rowData.remorque_id; }
        if (rowData.pneu_serie_retire) {
            const p = rowData.pneu_serie_retire;
            sourcePneusPool.value = [{ value: p.id, label: [p.numero_serie, p.type, p.etat ? `(${p.etat})` : null].filter(Boolean).join(" — "), position_actuel: rowData.position_retire }];
        }
        if (rowData.pneu_serie_monte) {
            const p = rowData.pneu_serie_monte;
            montePneusPool.value = [{ value: p.id, label: [p.numero_serie, p.type, p.etat ? `(${p.etat})` : null].filter(Boolean).join(" — "), position_actuel: null }];
        }
    } else {
        form.lignes = [{
            pneu_serie_retire_id: rowData.pneu_serie_retire_id,
            position:             null,
            position_retire:      rowData.position_retire,
            pneu_serie_monte_id:  rowData.pneu_serie_monte_id,
            position_monte:       rowData.position_monte,
            motif:                rowData.motif,
            date_hors_service:    rowData.date_hors_service,
        }];
        if (rowData.vehicule_id) { supportSource.value = "vehicule"; vehiculeSourceId.value = rowData.vehicule_id; }
        else if (rowData.remorque_id) { supportSource.value = "remorque"; remorqueSourceId.value = rowData.remorque_id; }
        if (rowData.pneu_serie_retire) {
            const p = rowData.pneu_serie_retire;
            sourcePneusPool.value = [{ value: p.id, label: [p.numero_serie, p.type, p.etat ? `(${p.etat})` : null].filter(Boolean).join(" — "), position_actuel: rowData.position_retire }];
        }
        if (rowData.pneu_serie_monte) {
            const p = rowData.pneu_serie_monte;
            pneusDestOptions.value = [{ value: p.id, label: [p.numero_serie, p.type, p.etat ? `(${p.etat})` : null].filter(Boolean).join(" — "), position_actuel: rowData.position_monte }];
        }
    }
    open.value = true;
};

const submit = () => {
    form.clearErrors();
    const method = form.id ? "put" : "post";
    const url    = form.id
        ? route("pneu_remplacement.update", form.id)
        : route("pneu_remplacement.store");

    form.transform((data) => {
        const isUpdate = !!data.id;
        const payload  = { ...data, _method: method.toUpperCase() };

        if (isUpdate) {
            // Edition : on aplatit toujours la première ligne
            const ligne = data.lignes?.[0] ?? {};
            if (data.type_operation === 'remplacement') {
                payload.pneu_serie_retire_id = ligne.pneu_serie_retire_id ?? null;
                payload.pneu_serie_monte_id  = ligne.pneu_serie_monte_id  ?? null;
                payload.position_retire      = ligne.position ?? null;
                payload.position_monte       = ligne.position ?? null;
                payload.motif                = ligne.motif    ?? null;
                payload.date_hors_service    = ligne.date_hors_service ?? null;
            } else {
                payload.pneu_serie_retire_id = ligne.pneu_serie_retire_id ?? null;
                payload.pneu_serie_monte_id  = ligne.pneu_serie_monte_id  ?? null;
                payload.position_retire      = ligne.position_retire ?? null;
                payload.position_monte       = ligne.position_monte  ?? null;
                payload.motif                = ligne.motif    ?? null;
                payload.date_hors_service    = ligne.date_hors_service ?? null;
            }
            delete payload.lignes;
        }

        return payload;
    }).post(url, { onSuccess: () => close(), forceFormData: true });
};

defineExpose({ add, edit, close });
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
        <div class="space-y-4">

            <!-- ── En-tête : Type + Date ──────────────────────────────────── -->
            <div class="flex flex-col md:flex-row gap-4 p-4 bg-gray-50 rounded-xl border border-gray-200">
                <FormItem :help="form.errors.type_operation" label="Type d'opération" class="flex-1">
                    <a-radio-group v-model:value="form.type_operation" size="large" class="w-full" button-style="solid">
                        <a-radio-button value="remplacement" class="w-1/2 text-center">
                            <font-awesome-icon icon="fa-rotate" class="mr-1.5" /> Remplacement
                        </a-radio-button>
                        <a-radio-button value="permutation" class="w-1/2 text-center">
                            <font-awesome-icon icon="fa-arrows-left-right" class="mr-1.5" /> Permutation
                        </a-radio-button>
                    </a-radio-group>
                </FormItem>
                <FormItem :help="form.errors.date_operation" label="Date" class="md:w-52">
                    <a-date-picker v-model:value="form.date_operation" :format="dateFormat" size="large" value-format="YYYY-MM-DD" class="w-full" />
                </FormItem>
            </div>

            <!-- ══════════════════════════════════════════════════════════════
                 MODE REMPLACEMENT — table multi-lignes
                 ══════════════════════════════════════════════════════════════ -->
            <template v-if="!isPermutation">

                <!-- Contexte source + stock -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-4 bg-slate-50 rounded-xl border border-slate-200">
                    <!-- Source -->
                    <div class="flex flex-col gap-2">
                        <div class="flex items-center gap-2 flex-wrap">
                            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-red-400 inline-block"></span>
                                Pneus à retirer
                            </p>
                            <a-radio-group v-model:value="supportSource" size="small" button-style="outline">
                                <a-radio-button value="vehicule">Véhicule</a-radio-button>
                                <a-radio-button value="remorque">Remorque</a-radio-button>
                            </a-radio-group>
                        </div>
                        <a-select
                            v-if="supportSource === 'vehicule'"
                            v-model:value="vehiculeSourceId"
                            class="w-full" size="large" :options="vehicules"
                            show-search allow-clear placeholder="Choisir un véhicule"
                            :filter-option="(i,o) => o.label?.toLowerCase().includes(i.toLowerCase())"
                        />
                        <a-select
                            v-else
                            v-model:value="remorqueSourceId"
                            class="w-full" size="large" :options="remorques"
                            show-search allow-clear placeholder="Choisir une remorque"
                            :filter-option="(i,o) => o.label?.toLowerCase().includes(i.toLowerCase())"
                        />
                    </div>

                    <!-- Stock magasin -->
                    <div class="flex flex-col gap-2">
                        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-green-400 inline-block"></span>
                            Pneus de remplacement (stock)
                        </p>
                        <a-select
                            v-model:value="magasinStockId"
                            class="w-full" size="large" :options="magasins"
                            show-search allow-clear placeholder="Choisir un magasin (optionnel)"
                            :filter-option="(i,o) => o.label?.toLowerCase().includes(i.toLowerCase())"
                        />
                    </div>
                </div>

                <!-- Table des lignes -->
                <div class="rounded-xl border border-gray-200 overflow-hidden shadow-sm">
                    <div class="flex items-center justify-between px-4 py-2.5 bg-gray-50 border-b border-gray-200">
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-semibold text-gray-700">Lignes de remplacement</span>
                            <a-tag color="blue" :bordered="false">{{ form.lignes.length }} pneu{{ form.lignes.length > 1 ? 's' : '' }}</a-tag>
                        </div>
                        <a-button size="small" type="dashed" @click="addLigne">
                            <PlusOutlined /> Ajouter une ligne
                        </a-button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="remplacement-table min-w-full">
                            <thead class="bg-primary/80 text-white text-xs">
                                <tr>
                                    <th class="px-3 py-2.5 text-left font-semibold w-[280px]">N° Série retiré *</th>
                                    <th class="px-3 py-2.5 text-left font-semibold w-[210px]">Position</th>
                                    <th class="px-3 py-2.5 text-left font-semibold w-[280px]">N° Série monté (stock)</th>
                                    <th class="px-3 py-2.5 text-left font-semibold w-[160px]">Motif</th>
                                    <th class="px-3 py-2.5 text-left font-semibold w-[155px]">Date hors service</th>
                                    <th class="px-3 py-2.5 w-10"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <tr v-for="(ligne, i) in form.lignes" :key="i" class="hover:bg-slate-50/60">

                                    <!-- N° Série retiré -->
                                    <td class="p-0 align-middle border-r border-gray-100">
                                        <a-select
                                            v-model:value="ligne.pneu_serie_retire_id"
                                            class="cell-select"
                                            :options="sourceOptionsPerRow[i]"
                                            :loading="loadingSource"
                                            show-search
                                            :filter-option="false"
                                            allow-clear
                                            :placeholder="vehiculeSourceId || remorqueSourceId ? 'Rechercher…' : 'Sélectionner un véhicule'"
                                            :not-found-content="loadingSource ? 'Chargement…' : (vehiculeSourceId || remorqueSourceId ? 'Aucun pneu trouvé' : 'Sélectionner un véhicule')"
                                            :status="form.errors['lignes.' + i + '.pneu_serie_retire_id'] ? 'error' : undefined"
                                            @search="triggerSourceSearch"
                                            @change="(v, opt) => onRetireChange(i, v, opt)"
                                        />
                                        <div v-if="form.errors['lignes.' + i + '.pneu_serie_retire_id']" class="text-red-500 text-[11px] px-2 pb-1">
                                            {{ form.errors['lignes.' + i + '.pneu_serie_retire_id'] }}
                                        </div>
                                    </td>

                                    <!-- Position -->
                                    <td class="p-0 align-middle border-r border-gray-100">
                                        <a-select
                                            v-model:value="ligne.position"
                                            class="cell-select"
                                            :options="POSITIONS.map(p => ({ value: p, label: p }))"
                                            allow-clear
                                            placeholder="Position"
                                        />
                                    </td>

                                    <!-- N° Série monté -->
                                    <td class="p-0 align-middle border-r border-gray-100">
                                        <a-select
                                            v-model:value="ligne.pneu_serie_monte_id"
                                            class="cell-select"
                                            :options="monteOptionsPerRow[i]"
                                            :loading="loadingMonte"
                                            show-search
                                            :filter-option="false"
                                            allow-clear
                                            :placeholder="magasinStockId ? 'Rechercher…' : 'Sélectionner un magasin'"
                                            :not-found-content="loadingMonte ? 'Chargement…' : (magasinStockId ? 'Aucun pneu en stock' : 'Sélectionner un magasin')"
                                            @search="triggerMonteSearch"
                                        />
                                    </td>

                                    <!-- Motif -->
                                    <td class="p-0 align-middle border-r border-gray-100">
                                        <a-select
                                            v-model:value="ligne.motif"
                                            class="cell-select"
                                            :options="MOTIFS"
                                            allow-clear
                                            placeholder="Motif"
                                            @change="(v) => onMotifChange(i, v)"
                                        />
                                    </td>

                                    <!-- Date hors service -->
                                    <td class="p-0 align-middle border-r border-gray-100">
                                        <a-date-picker
                                            v-if="ligne.motif === 'fin_vie'"
                                            v-model:value="ligne.date_hors_service"
                                            :format="dateFormat"
                                            value-format="YYYY-MM-DD"
                                            class="cell-picker"
                                            placeholder="jj/mm/aaaa"
                                            :status="form.errors['lignes.' + i + '.date_hors_service'] ? 'error' : undefined"
                                        />
                                        <span v-else class="flex items-center justify-center h-full text-gray-300 text-sm">—</span>
                                    </td>

                                    <!-- Supprimer -->
                                    <td class="p-0 text-center align-middle w-10">
                                        <a-button
                                            danger type="text" size="small"
                                            :disabled="form.lignes.length <= 1"
                                            @click="removeLigne(i)"
                                        >
                                            <DeleteOutlined />
                                        </a-button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </template>

            <!-- ══════════════════════════════════════════════════════════════
                 MODE PERMUTATION — table multi-lignes
                 ══════════════════════════════════════════════════════════════ -->
            <template v-else>

                <!-- Contexte source (A) + destination (B) -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-4 bg-slate-50 rounded-xl border border-slate-200">
                    <!-- Côté A -->
                    <div class="flex flex-col gap-2">
                        <div class="flex items-center gap-2 flex-wrap">
                            <div class="w-5 h-5 rounded-full bg-orange-500 flex items-center justify-center font-bold text-white text-[11px]">A</div>
                            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Pneu Origine</p>
                            <a-segmented v-model:value="supportSource" :options="SUPPORT_TYPES_PERMUTATION" class="!text-xs" size="small" />
                        </div>
                        <a-select
                            v-if="supportSource === 'vehicule'"
                            v-model:value="vehiculeSourceId"
                            class="w-full" size="large" :options="vehicules"
                            show-search allow-clear placeholder="Choisir un véhicule"
                            :filter-option="(i,o) => o.label?.toLowerCase().includes(i.toLowerCase())"
                        />
                        <a-select
                            v-else-if="supportSource === 'remorque'"
                            v-model:value="remorqueSourceId"
                            class="w-full" size="large" :options="remorques"
                            show-search allow-clear placeholder="Choisir une remorque"
                            :filter-option="(i,o) => o.label?.toLowerCase().includes(i.toLowerCase())"
                        />
                        <a-select
                            v-else
                            v-model:value="magasinSourceId"
                            class="w-full" size="large" :options="magasins"
                            show-search allow-clear placeholder="Choisir un magasin"
                            :filter-option="(i,o) => o.label?.toLowerCase().includes(i.toLowerCase())"
                        />
                    </div>
                    <!-- Côté B -->
                    <div class="flex flex-col gap-2">
                        <div class="flex items-center gap-2 flex-wrap">
                            <div class="w-5 h-5 rounded-full bg-blue-500 flex items-center justify-center font-bold text-white text-[11px]">B</div>
                            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Pneu Destination</p>
                            <a-segmented v-model:value="supportDest" :options="SUPPORT_TYPES_PERMUTATION" class="!text-xs" size="small" />
                        </div>
                        <a-select
                            v-if="supportDest === 'vehicule'"
                            v-model:value="vehiculeDestId"
                            class="w-full" size="large" :options="vehicules"
                            show-search allow-clear placeholder="Choisir un véhicule"
                            :filter-option="(i,o) => o.label?.toLowerCase().includes(i.toLowerCase())"
                        />
                        <a-select
                            v-else-if="supportDest === 'remorque'"
                            v-model:value="remorqueDestId"
                            class="w-full" size="large" :options="remorques"
                            show-search allow-clear placeholder="Choisir une remorque"
                            :filter-option="(i,o) => o.label?.toLowerCase().includes(i.toLowerCase())"
                        />
                        <a-select
                            v-else
                            v-model:value="magasinDestId"
                            class="w-full" size="large" :options="magasins"
                            show-search allow-clear placeholder="Choisir un magasin"
                            :filter-option="(i,o) => o.label?.toLowerCase().includes(i.toLowerCase())"
                        />
                    </div>
                </div>

                <!-- Table des lignes de permutation -->
                <div class="rounded-xl border border-gray-200 overflow-hidden shadow-sm">
                    <div class="flex items-center justify-between px-4 py-2.5 bg-gray-50 border-b border-gray-200">
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-semibold text-gray-700">Lignes de permutation</span>
                            <a-tag color="blue" :bordered="false">{{ form.lignes.length }} paire{{ form.lignes.length > 1 ? 's' : '' }}</a-tag>
                        </div>
                        <a-button size="small" type="dashed" @click="addLigne">
                            <PlusOutlined /> Ajouter une ligne
                        </a-button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="permutation-table min-w-full">
                            <thead class="bg-primary/80 text-white text-xs">
                                <tr>
                                    <th class="px-3 py-2.5 text-left font-semibold w-[280px]">N° Série A *</th>
                                    <th class="px-3 py-2.5 text-left font-semibold w-[180px]">Position A</th>
                                    <th class="px-3 py-2.5 text-left font-semibold w-[280px]">N° Série B</th>
                                    <th class="px-3 py-2.5 text-left font-semibold w-[180px]">Position B</th>
                                    <th class="px-3 py-2.5 text-left font-semibold w-[160px]">Motif</th>
                                    <th class="px-3 py-2.5 text-left font-semibold w-[155px]">Date hors service</th>
                                    <th class="px-3 py-2.5 w-10"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <tr v-for="(ligne, i) in form.lignes" :key="i" class="hover:bg-slate-50/60">

                                    <!-- N° Série A -->
                                    <td class="p-0 align-middle border-r border-gray-100">
                                        <a-select
                                            v-model:value="ligne.pneu_serie_retire_id"
                                            class="cell-select"
                                            :options="sourceOptionsPerRow[i]"
                                            :loading="loadingSource"
                                            show-search :filter-option="false" allow-clear
                                            :placeholder="vehiculeSourceId || remorqueSourceId || magasinSourceId ? 'Rechercher…' : 'Sélectionner source A'"
                                            :not-found-content="loadingSource ? 'Chargement…' : 'Aucun pneu trouvé'"
                                            :status="form.errors['lignes.' + i + '.pneu_serie_retire_id'] ? 'error' : undefined"
                                            @search="triggerSourceSearch"
                                            @change="(v, opt) => onRetirePermChange(i, v, opt)"
                                        />
                                        <div v-if="form.errors['lignes.' + i + '.pneu_serie_retire_id']" class="text-red-500 text-[11px] px-2 pb-1">
                                            {{ form.errors['lignes.' + i + '.pneu_serie_retire_id'] }}
                                        </div>
                                    </td>

                                    <!-- Position A -->
                                    <td class="p-0 align-middle border-r border-gray-100">
                                        <a-select
                                            v-model:value="ligne.position_retire"
                                            class="cell-select"
                                            :options="POSITIONS.map(p => ({ value: p, label: p }))"
                                            allow-clear placeholder="Position A"
                                        />
                                    </td>

                                    <!-- N° Série B -->
                                    <td class="p-0 align-middle border-r border-gray-100">
                                        <a-select
                                            v-model:value="ligne.pneu_serie_monte_id"
                                            class="cell-select"
                                            :options="destPermOptionsPerRow[i]"
                                            :loading="loadingDest"
                                            show-search :filter-option="false" allow-clear
                                            :placeholder="vehiculeDestId || remorqueDestId || magasinDestId ? 'Rechercher…' : 'Sélectionner source B'"
                                            :not-found-content="loadingDest ? 'Chargement…' : 'Aucun pneu trouvé'"
                                            @search="triggerDestSearch"
                                            @change="(v, opt) => onMontePermChange(i, v, opt)"
                                        />
                                    </td>

                                    <!-- Position B -->
                                    <td class="p-0 align-middle border-r border-gray-100">
                                        <a-select
                                            v-model:value="ligne.position_monte"
                                            class="cell-select"
                                            :options="POSITIONS.map(p => ({ value: p, label: p }))"
                                            allow-clear placeholder="Position B"
                                        />
                                    </td>

                                    <!-- Motif -->
                                    <td class="p-0 align-middle border-r border-gray-100">
                                        <a-select
                                            v-model:value="ligne.motif"
                                            class="cell-select"
                                            :options="MOTIFS"
                                            allow-clear placeholder="Motif"
                                            @change="(v) => onMotifChange(i, v)"
                                        />
                                    </td>

                                    <!-- Date hors service -->
                                    <td class="p-0 align-middle border-r border-gray-100">
                                        <a-date-picker
                                            v-if="ligne.motif === 'fin_vie'"
                                            v-model:value="ligne.date_hors_service"
                                            :format="dateFormat" value-format="YYYY-MM-DD"
                                            class="cell-picker" placeholder="jj/mm/aaaa"
                                        />
                                        <span v-else class="flex items-center justify-center h-full text-gray-300 text-sm">—</span>
                                    </td>

                                    <!-- Supprimer -->
                                    <td class="p-0 text-center align-middle w-10">
                                        <a-button
                                            danger type="text" size="small"
                                            :disabled="form.lignes.length <= 1"
                                            @click="removeLigne(i)"
                                        >
                                            <DeleteOutlined />
                                        </a-button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="flex items-center justify-center gap-3 py-1">
                    <div class="flex-1 h-px bg-orange-200"></div>
                    <span class="text-xs text-gray-400 bg-gray-50 px-3 py-1 rounded-full border">Les positions de chaque paire A ↔ B seront échangées automatiquement</span>
                    <div class="flex-1 h-px bg-blue-200"></div>
                </div>
            </template>

            <!-- ── Intervenant & Remarques ────────────────────────────────── -->
            <div class="rounded-xl border border-gray-200 overflow-hidden shadow-sm">
                <div class="flex p-2 items-center gap-2 bg-gray-50 px-4 py-2.5 border-b border-gray-100">
                    <font-awesome-icon icon="fa-user-wrench" class="text-gray-400" />
                    <span class="text-sm font-semibold text-gray-600">Intervenant & Remarques</span>
                </div>
                <div class="p-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <FormItem :help="form.errors.technicien" label="Technicien intervenant">
                        <a-input v-model:value="form.technicien" size="large" placeholder="Nom du technicien" />
                    </FormItem>
                    <FormItem :help="form.errors.observations" label="Observations">
                        <a-textarea v-model:value="form.observations" :rows="2" placeholder="Constats, remarques…" />
                    </FormItem>
                </div>
            </div>

        </div>
    </FormModal>
</template>

<style scoped>
/* ── Selects & date-pickers seamlessly fill table cells ─────────────────── */

.remplacement-table :deep(.cell-select),
.remplacement-table :deep(.cell-select.ant-select),
.permutation-table :deep(.cell-select),
.permutation-table :deep(.cell-select.ant-select) {
    display: block;
    width: 100%;
}

.remplacement-table :deep(.ant-select-selector),
.permutation-table :deep(.ant-select-selector) {
    border: none !important;
    border-radius: 0 !important;
    box-shadow: none !important;
    min-height: 42px !important;
    display: flex !important;
    align-items: center !important;
    padding: 0 10px !important;
}

.remplacement-table :deep(.ant-select-focused .ant-select-selector),
.permutation-table :deep(.ant-select-focused .ant-select-selector) {
    border: none !important;
    box-shadow: inset 0 -2px 0 0 #16a34a !important;
}

.remplacement-table :deep(.ant-select-status-error .ant-select-selector),
.permutation-table :deep(.ant-select-status-error .ant-select-selector) {
    box-shadow: inset 0 -2px 0 0 #ef4444 !important;
}

.remplacement-table :deep(.cell-picker.ant-picker),
.permutation-table :deep(.cell-picker.ant-picker) {
    display: block;
    width: 100%;
    border: none !important;
    border-radius: 0 !important;
    box-shadow: none !important;
    min-height: 42px !important;
    padding: 0 10px !important;
}

.remplacement-table :deep(.ant-picker-focused),
.permutation-table :deep(.ant-picker-focused) {
    border: none !important;
    box-shadow: inset 0 -2px 0 0 #16a34a !important;
}
</style>
