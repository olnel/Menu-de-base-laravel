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
import usePermissions from "@/UserPermissions/usePermissions.js";
import { DeleteOutlined, PlusOutlined, SettingOutlined } from "@ant-design/icons-vue";
import { useForm } from "@inertiajs/vue3";
import { computed, nextTick, onMounted, ref, watch } from "vue";

const { can } = usePermissions();

const props = defineProps({
    elementVehicules: { type: Array,  required: false, default: [] },
    showElementVehicule: { required: false, default: true, type: Boolean },
    formulaire: { required: false, default: {}, type: Object },
    tab_element_vehicule: { type: Array, required: true, default: [] },
    LIST_ELEMENT: { type: Array, default: [] },
});

const form = useForm(props.formulaire);
const localElement = ref([...props.LIST_ELEMENT]);

if (form.element_vehicules?.length) {
    form.element_vehicules = form.element_vehicules.map(el => ({
        ...el,
        is_pneu: el.is_pneu !== undefined ? !!el.is_pneu : false,
        date_montage: el.date_montage ?? null,
    }));
}

watch(() => props.LIST_ELEMENT, (v) => { localElement.value = [...v]; }, { deep: true });

// ─── Pneu autocomplete (per-row) ──────────────────────────────────────────────

const pneuOptionsPerRow = ref({});
const debounceTimers = {};

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

onMounted(() => form.element_vehicules.forEach((el, i) => { if (el.is_pneu) fetchPneusForRow(i); }));

// ─── Handlers ─────────────────────────────────────────────────────────────────

const onPneuToggle = (index, val) => {
    form.element_vehicules[index].numero_serie = '';
    form.element_vehicules[index].etat_piece   = '';
    form.element_vehicules[index].date_montage = null;
    if (val) fetchPneusForRow(index);
    else pneuOptionsPerRow.value[index] = [];
};

// Met à jour date_montage depuis l'option sélectionnée (ou null si effacé)
const onPneuChange = (index, val) => {
    const opt = (pneuOptionsPerRow.value[index] ?? []).find(o => o.value === val);
    form.element_vehicules[index].date_montage = opt?.date_montage ?? null;
};

const submit = () => {
    form.clearErrors();
    const method = form.id ? 'put' : 'post';
    const url = form.id ? route('vehicule.update', form.id) : route('vehicule.store');
    form.transform(data => ({ ...data, _method: method.toUpperCase() }))
        .post(url, { onSuccess: () => { close(); }, forceFormData: true });
};

const pushElement = () => {
    const sel = props.tab_element_vehicule.find(el => el.id === form.param_element_id);
    form.element_vehicules.unshift({
        id: '', emplacement: '', libelle: '', reference: '',
        numero_serie: '', etat_piece: '', date_montage: null,
        is_pneu: sel ? !!sel.is_pneu : false,
    });
    rebuildPneuOptions();
};

const spliceElement = (index) => {
    form.element_vehicules.splice(index, 1);
    rebuildPneuOptions();
};
</script>

<template>
    <div class="grid grid-cols-1 gap-8">
        <a-form layout="vertical">
            <div class="max-h-[600px] overflow-y-auto">
                <table class="min-w-full divide-y divide-green-200">
                    <thead class="bg-primary/80 sticky top-0 z-10 text-nowrap text-white">
                        <tr>
                            <th class="py-1.5">Emplacement</th>
                            <th>Libellé</th>
                            <th>Référence</th>
                            <th width="220px" class="px-4 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">N° Série</th>
                            <th class="text-center">Pneu ?</th>
                            <th width="100px">État</th>
                            <th width="140px" class="px-2 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Date montage</th>
                            <th v-if="can('vehicule.store_element_vehicule')">
                                <a-button type="primary" class="!bg-white text-primary hover:!text-primary" size="small" @click="pushElement" icon>
                                    <PlusOutlined />
                                </a-button>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-green-900 border">
                        <tr v-for="(item, index) in form.element_vehicules" :key="index">
                            <td class="border border-primary/25">
                                <autocomplete-component
                                    :options="localElement"
                                    v-model="item.emplacement"
                                    class="!rounded-none custom_autocomplete !border-0 focus:!border-b-green-600 !w-full"
                                    size="large" placeholder=""
                                />
                            </td>
                            <td class="border border-primary/25">
                                <a-input v-model:value="item.libelle" size="large" class="rounded-none border-0" />
                            </td>
                            <td class="border border-primary/25">
                                <a-input v-model:value="item.reference" size="large" class="rounded-none border-0" />
                            </td>
                            <!-- N° Série -->
                            <td class="border border-primary/25 text-center" style="min-height:40px;">
                                <a-input
                                    v-if="!item.is_pneu"
                                    v-model:value="item.numero_serie"
                                    size="large" class="w-full rounded-none border-0"
                                />
                                <AutocompleteComponent
                                    v-else
                                    :options="filteredPneuOptions[index] ?? []"
                                    v-model="item.numero_serie"
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
                            <!-- État -->
                            <td class="border border-primary/25">
                                <AutocompleteComponent
                                    :options="etat_pneu"
                                    v-model="item.etat_piece"
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
                            <td class="border border-primary/25 text-center" v-if="can('vehicule.store_element_vehicule')">
                                <a-button v-if="form.element_vehicules.length > 0" danger type="text" size="small" @click="spliceElement(index)">
                                    <DeleteOutlined />
                                </a-button>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <div v-if="form.element_vehicules.length === 0" class="flex flex-col items-center justify-center py-12 text-gray-500">
                    <div class="bg-blue-50 rounded-full p-4 mb-4">
                        <SettingOutlined class="text-3xl text-blue-400" />
                    </div>
                    <p class="text-sm font-medium text-gray-600 mb-1">Aucun élément ajouté</p>
                    <p class="text-xs text-gray-400">Veuillez insérer des éléments pour commencer</p>
                </div>
            </div>
        </a-form>

        <div class="col-span-2 flex justify-end" v-if="can('vehicule.store_element_vehicule')">
            <a-button type="primary" @click="submit">Enregistrer Les Informations</a-button>
        </div>
    </div>
</template>

<style scoped>
.ant-select-selector {
    background: red;
}
</style>
