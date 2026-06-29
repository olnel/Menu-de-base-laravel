<script>
    const etat_pneu = [
        { label: 'NEUF',    value: 'NEUF'    },
        { label: 'BON',     value: 'BON'     },
        { label: 'USÉ',     value: 'USÉ'     },
        { label: 'RECHAPÉ', value: 'RECHAPÉ' },
    ];
</script>
<script setup>
import AutocompleteComponent from "@/Components/AutoCompleteComponent/AutocompleteComponent.vue";
import FormItem from "@/Components/FormItem.vue";
import FormModal from "@/Components/FormModal.vue";
import InputNumberWithSepartor from "@/Components/InputNumberWithSepartor.vue";
import DynamicDocumentManager from "@/Components/DynamicDocumentManager.vue";
import { DeleteOutlined, PlusOutlined, SettingOutlined } from "@ant-design/icons-vue";
import { useForm } from "@inertiajs/vue3";
import { computed, nextTick, onMounted, ref, watch } from "vue";

const props = defineProps({
    flash: { type: Object, default: () => ({}) },
    LIST_COULEUR_VEHICULE:  { type: Array, default: () => [] },
    LIST_MODELE_VEHICULE:   { type: Array, default: () => [] },
    LIST_MARQUE_VEHICULE:   { type: Array, default: () => [] },
    LIST_REMORQUE:          { type: Array, default: () => [] },
    required_documents:     { type: Array, default: () => [] },
});

const localCouleurVehicule = ref([...props.LIST_COULEUR_VEHICULE]);
const localModeleVehicule  = ref([...props.LIST_MODELE_VEHICULE]);
const localMarqueVehicule  = ref([...props.LIST_MARQUE_VEHICULE]);
const localRemorque        = ref([...props.LIST_REMORQUE]);

watch(() => props.LIST_COULEUR_VEHICULE, v => { localCouleurVehicule.value = [...v]; }, { deep: true });
watch(() => props.LIST_MODELE_VEHICULE,  v => { localModeleVehicule.value  = [...v]; }, { deep: true });
watch(() => props.LIST_MARQUE_VEHICULE,  v => { localMarqueVehicule.value  = [...v]; }, { deep: true });
watch(() => props.LIST_REMORQUE,         v => { localRemorque.value        = [...v]; }, { deep: true });

const form = useForm({
    id: null,
    immatriculation: null,
    param_element_id: null,
    modele: null,
    marque: null,
    num_chassis: null,
    couleur: null,
    num_carte_grise: null,
    nbre_porte: null,
    valeur_initial: null,
    remorque_id: null,
    element_vehicules: [],
    documents: [],
});

const open      = ref(false);
const title     = ref('');
const activeTab = ref('vehicule');

const close = () => { open.value = false; form.reset(); form.clearErrors(); form.documents = []; };

const add = () => {
    title.value = 'Ajouter un Véhicule';
    activeTab.value = 'vehicule';
    form.documents = props.required_documents.map(doc => ({
        document_type_id: doc.document_type_id,
        document_type_name: doc.document_type.nom,
        expiration_required: !!doc.expiration_required,
        obligatoire: !!doc.obligatoire,
        fichier: null,
        date_expiration: null,
        observation: null
    }));
    open.value = true;
};

const update = (data) => {
    title.value = 'Modifier le Véhicule';
    activeTab.value = 'vehicule';
    Object.assign(form, {
        id: data.id,
        immatriculation:  data.immatriculation,
        marque:           data.marque,
        modele:           data.modele,
        num_chassis:      data.num_chassis,
        couleur:          data.couleur,
        num_carte_grise:  data.num_carte_grise,
        nbre_porte:       data.nbre_porte,
        valeur_initial:   data.valeur_initial,
        param_element_id: data.param_element_id,
        remorque_id:      data.remorque_id,
        element_vehicules: (data.element_vehicules ?? []).map(el => ({
            ...el,
            is_pneu:      el.is_pneu !== undefined ? !!el.is_pneu : false,
            is_first:     el.is_first !== undefined ? !!el.is_first : false,
            date_montage: el.date_montage ?? null,
        })),
        documents: props.required_documents.map(doc => ({
            document_type_id: doc.document_type_id,
            document_type_name: doc.document_type.nom,
            expiration_required: !!doc.expiration_required,
            obligatoire: !!doc.obligatoire,
            fichier: null,
            date_expiration: null,
            observation: null
        })),
    });
    open.value = true;
    nextTick(() => rebuildPneuOptions());
};

// ─── Pneu autocomplete (per-row) ──────────────────────────────────────────────

const pneuOptionsPerRow = ref({});
const debounceTimers    = {};

const fetchPneusForRow = async (index, searchText = '') => {
    const params = new URLSearchParams();
    if (searchText) params.append('search', searchText);
    try {
        const url = route('numero-serie.search') + '?' + params.toString();
        const res = await fetch(url, { headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
        const data = await res.json();
        pneuOptionsPerRow.value[index] = (data.pneus ?? []).map(p => ({
            label: p.numero_serie,
            value: p.numero_serie,
            date_montage: p.date_montage ?? null,
        }));
    } catch {
        pneuOptionsPerRow.value[index] = [];
    }
};

const searchPneuForRow = (index, text) => {
    clearTimeout(debounceTimers[index]);
    debounceTimers[index] = setTimeout(() => fetchPneusForRow(index, text), 300);
};

const filteredPneuOptions = computed(() =>
    form.element_vehicules.map((el, index) => {
        if (!el.is_pneu) return [];
        const used = form.element_vehicules
            .filter((e, i) => i !== index && e.is_pneu && e.numero_serie)
            .map(e => e.numero_serie);
        return (pneuOptionsPerRow.value[index] ?? []).filter(o => !used.includes(o.value));
    })
);

const rebuildPneuOptions = () => {
    pneuOptionsPerRow.value = {};
    nextTick(() => form.element_vehicules.forEach((el, i) => { if (el.is_pneu) fetchPneusForRow(i); }));
};

// ─── Handlers ─────────────────────────────────────────────────────────────────

const onPneuToggle = (index, val) => {
    form.element_vehicules[index].numero_serie = '';
    form.element_vehicules[index].etat_piece   = '';
    form.element_vehicules[index].date_montage = null;
    if (!val) form.element_vehicules[index].is_first = false;
    if (val) fetchPneusForRow(index);
    else pneuOptionsPerRow.value[index] = [];
};

const onPneuChange = (index, val) => {
    const opt = (pneuOptionsPerRow.value[index] ?? []).find(o => o.value === val);
    form.element_vehicules[index].date_montage = opt?.date_montage ?? null;
};

const handleElementVehicule = () => {
    const selected = props.flash.element_vehicule?.find(el => el.id === form.param_element_id);
    const isPneu = selected ? !!selected.is_pneu : false;
    form.element_vehicules = (selected?.details ?? []).map(detail => ({
        ...detail,
        numero_serie: '',
        etat_piece:   '',
        date_montage: null,
        is_pneu:  detail.is_pneu ? !!detail.is_pneu : isPneu,
        is_first: false,
    }));
    rebuildPneuOptions();
};

const pushElement = () => {
    const sel = props.flash.element_vehicule?.find(el => el.id === form.param_element_id);
    form.element_vehicules.push({
        id: '', emplacement: '', libelle: '', reference: '',
        numero_serie: '', etat_piece: '', date_montage: null,
        is_pneu: sel ? !!sel.is_pneu : false,
        is_first: false,
    });
    rebuildPneuOptions();
};

const spliceElement = (index) => {
    form.element_vehicules.splice(index, 1);
    rebuildPneuOptions();
};

const submit = () => {
    form.clearErrors();
    const method = form.id ? 'put' : 'post';
    const url = form.id ? route('vehicule.update', form.id) : route('vehicule.store');
    form.transform(data => ({ ...data, _method: method.toUpperCase() }))
        .post(url, { onSuccess: () => close(), forceFormData: true });
};

defineExpose({ add, update, close });
</script>

<template>
    <FormModal
        v-model:open="open"
        :titre="title"
        :loading="form.processing"
        @close="close"
        @submit="submit"
        size="lg"
        :show_champ_obligatoir="false"
    >
        <!-- Onglets -->
        <div class="border-b border-gray-200 mb-4">
            <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                <button
                    @click="activeTab = 'vehicule'"
                    :class="[activeTab === 'vehicule' ? 'border-primary text-primary' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300', 'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm']"
                >Informations du Véhicule</button>
                <button
                    @click="activeTab = 'elements'"
                    :class="[activeTab === 'elements' ? 'border-primary text-primary' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300', 'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm']"
                >Éléments du Véhicule</button>
                <button
                    @click="activeTab = 'docs'"
                    :class="[activeTab === 'docs' ? 'border-primary text-primary' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300', 'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm']"
                >Documents Administratifs</button>
            </nav>
        </div>

        <!-- Onglet infos véhicule -->
        <div v-if="activeTab === 'vehicule'" class="space-y-6">
            <a-row :gutter="[16, 0]">
                <a-col :xs="24">
                    <form-item label="Immatriculation" required :help="form.errors.immatriculation">
                        <a-input v-model:value="form.immatriculation" size="large" />
                    </form-item>
                </a-col>
                <a-col :xs="24" :lg="12">
                    <form-item label="Marque" required :help="form.errors.marque">
                        <AutocompleteComponent v-model="form.marque" :options="localMarqueVehicule" placeholder="Choisissez une marque" />
                    </form-item>
                </a-col>
                <a-col :xs="24" :lg="12">
                    <form-item label="Modèle" :help="form.errors.modele">
                        <AutocompleteComponent v-model="form.modele" :options="localModeleVehicule" placeholder="Choisissez un modèle" />
                    </form-item>
                </a-col>
                <a-col :xs="24" :lg="12">
                    <form-item label="N° Châssis" required :help="form.errors.num_chassis">
                        <a-input v-model:value="form.num_chassis" size="large" />
                    </form-item>
                </a-col>
                <a-col :xs="24" :lg="12">
                    <form-item label="Couleur" :help="form.errors.couleur">
                        <AutocompleteComponent v-model="form.couleur" :options="localCouleurVehicule" placeholder="Choisissez une couleur" />
                    </form-item>
                </a-col>
                <a-col :xs="24" :lg="12">
                    <form-item label="N° Carte Grise" :help="form.errors.num_carte_grise">
                        <a-input v-model:value="form.num_carte_grise" size="large" />
                    </form-item>
                </a-col>
                <a-col :xs="24" :lg="12">
                    <form-item label="Nombre de portes" :help="form.errors.nbre_porte">
                        <a-input-number v-model:value="form.nbre_porte" :min="2" :max="5" size="large" class="w-full" />
                    </form-item>
                </a-col>
                <a-col :xs="24" :lg="12">
                    <form-item label="Valeur Initiale" :help="form.errors.valeur_initial">
                        <InputNumberWithSepartor v-model="form.valeur_initial" size="large" class="w-full !text-right" />
                    </form-item>
                </a-col>
                <a-col :xs="24" :lg="12">
                    <form-item label="Remorque" :help="form.errors.remorque_id">
                        <a-select v-model:value="form.remorque_id" :options="localRemorque" :allowClear="true" size="large" placeholder="Choisissez un remorque" />
                    </form-item>
                </a-col>
            </a-row>
        </div>

        <!-- Onglet éléments véhicule -->
        <div v-if="activeTab === 'elements'" class="space-y-6">
            <form-item label="Elément Véhicule" :help="form.errors.param_element_id">
                <a-select v-model:value="form.param_element_id" @change="handleElementVehicule" :allowClear="true" size="large">
                    <a-select-option v-for="(e, i) in flash.element_vehicule" :key="i" :value="e.id">{{ e.type_model }}</a-select-option>
                </a-select>
            </form-item>

            <div class="bg-white rounded-t-lg border border-gray-100 overflow-hidden">
                <div class="max-h-96 overflow-y-auto">
                    <table class="min-w-full">
                        <thead class="bg-primary/80 sticky top-0 z-10 text-nowrap">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Emplacement</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Libellé</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Référence</th>
                                <th width="180px" class="px-4 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">N° Série</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-white uppercase tracking-wider">Pneu ?</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-white uppercase tracking-wider">Pos. initiale ?</th>
                                <th width="100px" class="px-4 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">État</th>
                                <th width="140px" class="px-2 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Date montage</th>
                                <th class="px-4 py-2 text-center text-xs font-semibold text-white uppercase tracking-wider">
                                    <a-button type="default" class="!rounded-none" size="small" @click="pushElement" icon>
                                        <PlusOutlined />
                                    </a-button>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            <tr v-for="(item, index) in form.element_vehicules" :key="index" class="hover:bg-gray-50 transition-colors duration-150">
                                <td class="border border-primary/25">
                                    <a-input v-model:value="item.emplacement" class="rounded-none border-0 focus:border-b-green-600" size="large" />
                                </td>
                                <td class="border border-primary/25">
                                    <a-input v-model:value="item.libelle" size="large" placeholder="Libellé" class="rounded-none border-0 focus:border-b-green-600" />
                                </td>
                                <td class="border border-primary/25">
                                    <a-input v-model:value="item.reference" size="large" placeholder="Référence" class="rounded-none border-0 focus:border-b-green-600" />
                                </td>
                                <!-- N° Série -->
                                <td class="border border-primary/25" style="min-height:40px;">
                                    <a-input
                                        v-if="!item.is_pneu"
                                        v-model:value="item.numero_serie"
                                        size="large" placeholder="N° Série"
                                        class="w-full rounded-none border-0 focus:border-b-green-600"
                                    />
                                    <AutocompleteComponent
                                        v-else
                                        v-model="item.numero_serie"
                                        :options="filteredPneuOptions[index] ?? []"
                                        placeholder="Rechercher N° Série…"
                                        class="w-full !rounded-none !border-0 !border-none"
                                        :field-config="{ label: 'label', value: 'value' }"
                                        :filter-option="false"
                                        :allow-add="false"
                                        @search="(text) => searchPneuForRow(index, text)"
                                        @change="(val) => onPneuChange(index, val)"
                                        :border="false"
                                    />
                                </td>
                                <!-- Pneu ? -->
                                <td class="border border-primary/25 text-center">
                                    <a-switch v-model:checked="item.is_pneu" size="small" @change="(val) => onPneuToggle(index, val)" />
                                </td>
                                <!-- Position initiale ? -->
                                <td class="border border-primary/25 text-center">
                                    <a-switch v-model:checked="item.is_first" size="small" :disabled="!item.is_pneu" />
                                </td>
                                <!-- État -->
                                <td class="border border-primary/25">
                                    <AutocompleteComponent
                                        v-model="item.etat_piece"
                                        :options="etat_pneu"
                                        placeholder="État"
                                        class="w-full !rounded-none !border-0 !border-none"
                                        :field-config="{ label: 'label', value: 'value' }"
                                        :disabled="!item.is_pneu"
                                        :border="false"
                                    />
                                </td>
                                <!-- Date de montage -->
                                <td class="border border-primary/25 px-2">
                                    <a-date-picker
                                        v-if="item.is_pneu"
                                        v-model:value="item.date_montage"
                                        format="DD/MM/YYYY"
                                        value-format="YYYY-MM-DD"
                                        size="small"
                                        class="!w-full !rounded-none !border-0 !border-b !border-primary/30"
                                        placeholder="jj/mm/aaaa"
                                    />
                                </td>
                                <!-- Supprimer -->
                                <td class="text-center border border-primary/25">
                                    <a-button v-if="form.element_vehicules.length > 0" danger @click="spliceElement(index)" type="text" size="small">
                                        <DeleteOutlined />
                                    </a-button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div v-if="form.element_vehicules.length === 0" class="flex flex-col items-center justify-center py-12 text-gray-500">
                    <div class="bg-blue-50 rounded-full p-4 mb-4"><SettingOutlined class="text-3xl text-blue-400" /></div>
                    <p class="text-sm font-medium text-gray-600 mb-1">Aucun élément ajouté</p>
                    <p class="text-xs text-gray-400">Sélectionnez un type d'élément pour commencer</p>
                </div>
            </div>
        </div>

        <!-- Onglet documents administratifs -->
        <div v-if="activeTab === 'docs'" class="space-y-6">
            <div class="p-6 bg-white rounded-3xl border border-gray-100 shadow-sm min-h-[400px]">
                <div class="flex items-center mb-6 pb-2 border-b border-gray-50">
                    <font-awesome-icon icon="fa-solid fa-file-shield" class="text-primary text-xl mr-3" />
                    <h3 class="text-lg font-bold text-gray-800">Documents à fournir</h3>
                </div>
                
                <div class="grid grid-cols-1 gap-4">
                    <div v-for="(doc, index) in form.documents" :key="doc.document_type_id" 
                        class="p-4 bg-gray-50 rounded-2xl border border-gray-100 hover:border-primary/30 transition-colors">
                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="font-bold text-gray-700">{{ doc.document_type_name }}</span>
                                    <a-tag v-if="doc.obligatoire" color="error" class="text-[10px] uppercase">Obligatoire</a-tag>
                                </div>
                                <p class="text-xs text-gray-400">Uploader le document numérisé</p>
                            </div>
                            
                            <div class="flex flex-col md:flex-row gap-4 flex-1 items-end md:items-center">
                                <div class="w-full md:w-auto flex-1">
                                    <a-input 
                                        type="file" 
                                        @change="e => form.documents[index].fichier = e.target.files[0]"
                                        class="!rounded-lg"
                                    />
                                </div>
                                <div v-if="doc.expiration_required" class="w-full md:w-48">
                                    <a-date-picker
                                        v-model:value="form.documents[index].date_expiration"
                                        placeholder="Date d'expiration"
                                        class="w-full"
                                        value-format="YYYY-MM-DD"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div v-if="form.documents.length === 0" class="text-center py-10">
                        <p class="text-gray-400 italic">Aucun document spécifique configuré pour les véhicules.</p>
                    </div>
                </div>

                <div v-if="form.id" class="mt-10 pt-10 border-t border-gray-100">
                    <div class="flex items-center mb-6">
                        <font-awesome-icon icon="fa-solid fa-folder-open" class="text-primary/40 text-lg mr-3" />
                        <h3 class="text-lg font-bold text-gray-800">Explorateur de Documents</h3>
                    </div>
                    <DynamicDocumentManager modelClass="App\Models\Vehicule" :modelId="form.id" />
                </div>
            </div>
        </div>
    </FormModal>
</template>

<style scoped>
.ant-upload-hint { font-size: 12px; color: #888; }
</style>
