<script setup>
import FormItem from "@/Components/FormItem.vue";
import FormModal from "@/Components/FormModal.vue";
import { router, useForm } from "@inertiajs/vue3";
import dayjs from "dayjs";
import { ref } from "vue";

const props = defineProps({
    flash:     Object,
    magasins:  { type: Array, default: [] },
    articles:  { type: Array, default: [] },
    vehicules: { type: Array, default: [] },
    remorques: { type: Array, default: [] },
});

const dateFormat = "DD/MM/YYYY";

const form = useForm({
    id:              null,
    date_inventaire: dayjs().format("YYYY-MM-DD"),
    magasin_id:      null,
    remarque:        null,
    details:         [],
});

const OPTION_LIMIT = 15;

const vehiculeSearchMap = ref({});
const remorqueSearchMap = ref({});

const getVehiculeOptions = (idx) => {
    const usedIds = form.details
        .filter((_, i) => i !== idx)
        .map((d) => d.vehicule_id)
        .filter(Boolean);

    const available = props.vehicules.filter((v) => !usedIds.includes(v.value));
    const search    = (vehiculeSearchMap.value[idx] || "").toLowerCase();
    const filtered  = search
        ? available.filter((v) => v.label?.toLowerCase().includes(search))
        : available;

    const result    = filtered.slice(0, OPTION_LIMIT);
    const currentId = form.details[idx]?.vehicule_id;
    if (currentId && !result.find((v) => v.value === currentId)) {
        const current = props.vehicules.find((v) => v.value === currentId);
        if (current) result.unshift(current);
    }
    return result;
};

const getRemorqueOptions = (idx) => {
    const usedIds = form.details
        .filter((_, i) => i !== idx)
        .map((d) => d.remorque_id)
        .filter(Boolean);

    const available = props.remorques.filter((r) => !usedIds.includes(r.value));
    const search    = (remorqueSearchMap.value[idx] || "").toLowerCase();
    const filtered  = search
        ? available.filter((r) => r.label?.toLowerCase().includes(search))
        : available;

    const result    = filtered.slice(0, OPTION_LIMIT);
    const currentId = form.details[idx]?.remorque_id;
    if (currentId && !result.find((r) => r.value === currentId)) {
        const current = props.remorques.find((r) => r.value === currentId);
        if (current) result.unshift(current);
    }
    return result;
};

const onVehiculeSearch = (idx, value) => {
    vehiculeSearchMap.value = { ...vehiculeSearchMap.value, [idx]: value };
};

const onRemorqueSearch = (idx, value) => {
    remorqueSearchMap.value = { ...remorqueSearchMap.value, [idx]: value };
};

const open  = ref(false);
const title = ref("");

const close = () => {
    open.value = false;
    form.reset();
    form.clearErrors();
};

const add = () => {
    title.value     = "Nouvel Inventaire Pneu";
    open.value      = true;
    form.magasin_id = null;
    form.details    = [];
    fetchData();
};

const update = (rowData) => {
    router.visit(
        `${route("pneu_inventaire.show", { pneu_inventaire: rowData.id })}`,
        {
            preserveState: true,
            preserveScroll: true,
            only: ["flash"],
            onSuccess: () => {
                Object.keys(props.flash.data).forEach((key) => {
                    form[key] = props.flash.data[key];
                });
                title.value = "Modifier";
                open.value  = true;
            },
        }
    );
};

const submit = () => {
    form.clearErrors();
    const method = form.id ? "put" : "post";
    const url    = form.id
        ? route("pneu_inventaire.update", form.id)
        : route("pneu_inventaire.store");
    form.transform((data) => ({ ...data, _method: method.toUpperCase() })).post(url, {
        onSuccess: () => close(),
        forceFormData: true,
    });
};

const addRow = () => {
    form.details.push({
        numero_serie:    "",
        etat:            "neuf",
        article_id:      null,
        is_new:          true,
        is_existe:       true,
        occupe:          null,
        vehicule_id:     null,
        remorque_id:     null,
        occupation_type: null,
    });
};

const removeRow = (index) => {
    form.details.splice(index, 1);
    vehiculeSearchMap.value = {};
    remorqueSearchMap.value = {};
};

const onIsExisteChange = (row) => {
    if (!row.is_existe) {
        row.vehicule_id     = null;
        row.remorque_id     = null;
        row.occupation_type = null;
    }
};

const onOccupationTypeChange = (row) => {
    if (row.occupation_type === "vehicule") {
        row.remorque_id = null;
    } else if (row.occupation_type === "remorque") {
        row.vehicule_id = null;
    } else {
        row.vehicule_id = null;
        row.remorque_id = null;
    }
};

const onMagasinChange = () => {
    if (!form.magasin_id) {
        form.details = [];
        return;
    }
    router.visit(
        route("pneu_inventaire.get_sup_data_by_magasin", { magasin_id: form.magasin_id }),
        {
            only: ["flash"],
            preserveState: true,
            preserveScroll: true,
            onSuccess: () => {
                const series = props.flash?.pneus_series || [];
                form.details = Array.isArray(series)
                    ? series.map((pneu) => ({
                          numero_serie:    pneu.numero_serie,
                          etat:            pneu.etat || "neuf",
                          article_id:      pneu.article_id ?? null,
                          article_label:   [pneu.article_reference, pneu.article_designation]
                              .filter(Boolean)
                              .join(" - "),
                          is_existing:     true,
                          pneu_id:         pneu.id,
                          is_existe:       true,
                          occupe:          null,
                          vehicule_id:     null,
                          remorque_id:     null,
                          occupation_type: null,
                      }))
                    : [];
            },
        }
    );
};

const fetchData = () => {
    router.visit(route("pneu_inventaire.getSupData", { id: form.id }), {
        only: ["flash"],
        preserveState: true,
        preserveScroll: true,
        onSuccess: () => {},
    });
};

defineExpose({ add, update, close });
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
        <!-- En-tête : date, magasin, remarque -->
        <div class="grid xl:grid-cols-3 md:grid-cols-3 grid-cols-1 gap-4 mb-4">
            <FormItem :help="form.errors.date_inventaire" label="Date Inventaire">
                <a-date-picker
                    v-model:value="form.date_inventaire"
                    :format="dateFormat"
                    size="large"
                    :value-format="'YYYY-MM-DD'"
                    class="w-full text-center"
                />
            </FormItem>

            <FormItem :help="form.errors.magasin_id" label="Magasin">
                <a-select
                    v-model:value="form.magasin_id"
                    class="w-full"
                    size="large"
                    :options="magasins"
                    @change="onMagasinChange"
                    show-search
                    allow-clear
                />
            </FormItem>

            <FormItem :help="form.errors.remarque" label="Remarque">
                <a-textarea
                    v-model:value="form.remarque"
                    class="w-full"
                    :rows="1"
                    placeholder="Note d'inventaire (optionnel)"
                />
            </FormItem>
        </div>

        <!-- Tableau des séries de pneus -->
        <div>
            <div class="flex justify-between items-center mb-2">
                <div class="font-semibold">
                    Séries de Pneus
                    <a-badge
                        :count="form.details.length"
                        :overflow-count="9999"
                        color="blue"
                        class="ml-2"
                    />
                </div>
                <a-button
                    type="dashed"
                    size="large"
                    @click="addRow"
                    class="!rounded-md"
                    :disabled="!form.magasin_id"
                >
                    <font-awesome-icon class="mr-2" icon="fa-plus" />
                    Ajouter une série
                </a-button>
            </div>

            <div class="overflow-auto border rounded-md" style="max-height: 520px">
                <table class="min-w-full text-sm">
                    <thead class="bg-primary text-white sticky top-0 z-10">
                        <tr>
                            <th class="py-1.5 text-left px-2 min-w-[140px]">N° Série</th>
                            <th class="text-left px-2 min-w-[110px]">État</th>
                            <th class="text-left px-2 min-w-[160px]">Article</th>
                            <th class="text-center px-2 min-w-[80px]">Existe</th>
                            <th class="text-center px-2 min-w-[120px]">Occupé</th>
                            <th class="text-left px-2 min-w-[180px]">Véhicule / Remorque</th>
                            <th class="text-center px-2 min-w-[80px]">Type</th>
                            <th width="40px" class="text-center">-</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-green-900">
                        <tr
                            v-for="(row, idx) in form.details"
                            :key="idx"
                            class="hover:bg-primary/5"
                            :class="{
                                'bg-red-50':    !row.is_existe,
                                'bg-green-50':  row.is_existe && row.is_existing,
                                'bg-yellow-50': row.is_existe && !row.is_existing,
                            }"
                        >
                            <!-- Numéro de série -->
                            <td class="border border-primary/25">
                                <a-input
                                    v-model:value="row.numero_serie"
                                    :readonly="row.is_existing"
                                    size="large"
                                    class="rounded-none !border-0 !border-none"
                                    placeholder="Saisir le n° de série"
                                />
                            </td>

                            <!-- État -->
                            <td class="border border-primary/25">
                                <a-select
                                    v-model:value="row.etat"
                                    size="large"
                                    allow-clear
                                    show-search
                                    :options="[
                                        { value: 'rechappe', label: 'Rechappé' },
                                        { value: 'neuf',     label: 'Neuf' },
                                        { value: 'bon',      label: 'Bon' },
                                    ]"
                                    class="w-full !border-none !border-0"
                                    placeholder="État"
                                />
                            </td>

                            <!-- Article -->
                            <td class="border border-primary/25 bg-white">
                                <template v-if="row.is_existing">
                                    <span class="px-2 text-sm">{{ row.article_label || "—" }}</span>
                                </template>
                                <template v-else>
                                    <a-select
                                        v-model:value="row.article_id"
                                        :options="props.articles"
                                        size="large"
                                        show-search
                                        class="!w-full !rounded-none !border-none !border-0"
                                        :filter-option="
                                            (input, option) =>
                                                option.label?.toLowerCase().includes(input.toLowerCase())
                                        "
                                        placeholder="Article"
                                    />
                                </template>
                            </td>

                            <!-- Existe -->
                            <td class="border border-primary/25 text-center">
                                <a-switch
                                    v-model:checked="row.is_existe"
                                    @change="onIsExisteChange(row)"
                                    checked-children="Oui"
                                    un-checked-children="Non"
                                    :checked-value="true"
                                    :un-checked-value="false"
                                />
                            </td>

                            <!-- Occupé : type (Véhicule / Remorque) -->
                            <td class="border border-primary/25 text-center px-1">
                                <template v-if="row.is_existe">
                                    <a-select
                                        v-model:value="row.occupation_type"
                                        size="small"
                                        allow-clear
                                        @change="onOccupationTypeChange(row)"
                                        :options="[
                                            { value: 'vehicule', label: 'Véhicule' },
                                            { value: 'remorque', label: 'Remorque' },
                                        ]"
                                        placeholder="Occupé ?"
                                        class="w-full"
                                    />
                                </template>
                                <a-tag v-else color="red" :bordered="false">Absent</a-tag>
                            </td>

                            <!-- Sélecteur véhicule ou remorque -->
                            <td class="border border-primary/25 px-1">
                                <a-select
                                    v-if="row.occupation_type === 'vehicule'"
                                    v-model:value="row.vehicule_id"
                                    size="small"
                                    show-search
                                    allow-clear
                                    :options="getVehiculeOptions(idx)"
                                    :filter-option="false"
                                    @search="(val) => onVehiculeSearch(idx, val)"
                                    placeholder="Rechercher immatriculation…"
                                    class="w-full"
                                />
                                <a-select
                                    v-else-if="row.occupation_type === 'remorque'"
                                    v-model:value="row.remorque_id"
                                    size="small"
                                    show-search
                                    allow-clear
                                    :options="getRemorqueOptions(idx)"
                                    :filter-option="false"
                                    @search="(val) => onRemorqueSearch(idx, val)"
                                    placeholder="Rechercher n° remorque…"
                                    class="w-full"
                                />
                                <span v-else class="px-2 text-gray-400 text-xs">—</span>
                            </td>

                            <!-- Type existant / nouveau -->
                            <td class="border border-primary/25 px-2 text-center">
                                <a-tag v-if="row.is_existing" color="green" :bordered="false">Existant</a-tag>
                                <a-tag v-else color="orange" :bordered="false">Nouveau</a-tag>
                            </td>

                            <!-- Supprimer -->
                            <td
                                @click="removeRow(idx)"
                                class="text-center cursor-pointer hover:!bg-red-100"
                            >
                                <font-awesome-icon class="text-red-500" icon="fa-trash" />
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div
                v-if="form.details.length === 0 && form.magasin_id"
                class="text-center py-8 text-gray-500"
            >
                <font-awesome-icon icon="fa-info-circle" class="text-2xl mb-2" />
                <p>Aucun pneu trouvé pour ce magasin. Cliquez sur "Ajouter une série" pour commencer l'inventaire.</p>
            </div>
        </div>
    </FormModal>
</template>

<style scoped></style>
