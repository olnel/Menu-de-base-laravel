<script setup>
import ButtonIcon from "@/Components/ButtonIcon.vue";
import DataTable from "@/Components/DataTable.vue";
import FilterBase from "@/Components/Filter/FilterBase.vue";
import SousMenuPrincipale from "@/Pages/Menu/SousMenuPrincipale.vue";
import PaieGenerationModal from "@/Pages/Paie/Form/PaieGenerationModal.vue";
import usePermissions from "@/UserPermissions/usePermissions.js";
import { router, useForm } from "@inertiajs/vue3";
import { useCurrency }  from "@/Composables/useCurrency.js";
import { computed, ref, reactive, watch } from "vue";
import { confirm_delete } from "../../../Utils/confirmation_modal.js";
import { createSearchFilter, gotoSearch } from "../../../Utils/FiltreUtils.js";
import { DownOutlined } from "@ant-design/icons-vue";
import { Space } from "ant-design-vue";

const { can } = usePermissions();

const props = defineProps({
    data: { type: Object, default: () => ({}) },
    tresoreries: { type: Array, default: () => [] },
    filters: { type: Object, default: () => ({}) },
});

const dropdownVisible = ref(false);
const months = [
    { value: 1, label: "Janvier" },
    { value: 2, label: "Février" },
    { value: 3, label: "Mars" },
    { value: 4, label: "Avril" },
    { value: 5, label: "Mai" },
    { value: 6, label: "Juin" },
    { value: 7, label: "Juillet" },
    { value: 8, label: "Août" },
    { value: 9, label: "Septembre" },
    { value: 10, label: "Octobre" },
    { value: 11, label: "Novembre" },
    { value: 12, label: "Décembre" },
];

// Utiliser createSearchFilter pour la compatibilité avec FilterBase
const filter = ref({
    ...createSearchFilter(),
    mois: Number(props.filters.mois || new Date().getMonth() + 1),
    annee: Number(props.filters.annee || new Date().getFullYear()),
    statut: props.filters.statut || null,
});

const title = computed(() => {
    const monthLabel =
        months.find((m) => m.value == filter.value.mois)?.label || "";
    return `Gestion de la Paie - ${monthLabel} ${filter.value.annee}`;
});

const applyFilters = (data) => {
    filter.value = data;
    const url = route("paie.index");
    gotoSearch(filter.value, url);
};

const resetFilters = () => {
    filter.value = {
        ...createSearchFilter(),
        mois: new Date().getMonth() + 1,
        annee: new Date().getFullYear(),
        statut: null,
    };
    applyFilters(filter.value);
};

const handlePageChange = (pag) => {
    router.get(
        route("paie.index"),
        {
            ...filter.value,
            page: pag.current,
        },
        {
            preserveState: true,
            preserveScroll: true,
        },
    );
};

// Référence pour le modal de génération
const genModal = ref();
const handleOpenGeneration = () => {
    genModal.value.show();
};

const handleExport = () => {
    const exportable = {
        "salarie.matricule": "Matricule",
        "salarie.nom": "Nom",
        "salarie.prenom": "Prénom",
        salaire_base: "Salaire Base",
        montant_primes: "Primes",
        montant_retenues: "Retenues",
        salaire_net: "Net à Payer",
        statut: "Statut",
        date_paiement: "Date Paiement",
        mode_paiement: "Mode Paiement",
    };

    const url = route("paie.export", {
        ...filter.value,
        type_export: "xlsx",
        exportable: JSON.stringify(exportable),
    });
    window.location.href = url;
};

const handleMassPrint = () => {
    const url = route("paie.mass-print", {
        ...filter.value,
    });
    window.open(url, "_blank");
};

const handlePrintList = () => {
    const url = route("paie.print-list", {
        ...filter.value,
    });
    window.open(url, "_blank");
};

const { formatCurrency: fmtCcy } = useCurrency();

const columns = [
    {
        key: "matricule",
        title: "Matricule",
        dataIndex: ["salarie", "matricule"],
        width: "100px",
    },
    {
        key: "nom",
        title: "Nom & Prénom",
        customRender: ({ record }) =>
            `${record.salarie.nom} ${record.salarie.prenom ?? ""}`,
    },
    {
        key: "salaire_base",
        title: "Salaire Base",
        dataIndex: "salaire_base",
        align: "right",
        customRender: ({ text }) => fmtCcy(text),
    },
    {
        key: "primes",
        title: "Primes",
        dataIndex: "montant_primes",
        align: "right",
        customRender: ({ text }) =>
            text > 0
                ? "+" + fmtCcy(text)
                : "-",
    },
    {
        key: "retenues",
        title: "Retenues",
        dataIndex: "montant_retenues",
        align: "right",
        customRender: ({ text }) =>
            text > 0
                ? "-" + fmtCcy(text)
                : "-",
    },
    {
        key: "salaire_net",
        title: "Net à Payer",
        dataIndex: "salaire_net",
        align: "right",
        customCell: () => ({ class: "!font-bold !text-primary" }),
        customRender: ({ text }) =>
            new Intl.NumberFormat("fr-FR").format(text) + " Ar",
    },
    {
        key: "statut",
        title: "Statut",
        dataIndex: "statut",
        align: "center",
    },
];

const statutOptions = [
    { value: "paye", label: "PAYÉ" },
    { value: "brouillon", label: "BROUILLON" },
];

// Gestion des éléments (Primes/Retenues)
const elementModalOpen = ref(false);
const selectedPaie = ref(null);
const localElements = ref([]); // Liste temporaire locale
const localError = ref(""); // Message d'erreur local

const elementForm = useForm({
    type: "prime",
    libelle: "",
    montant: null,
});

const openElementModal = (record) => {
    selectedPaie.value = record;
    localElements.value = []; // Vider la liste locale au départ
    localError.value = "";
    elementModalOpen.value = true;
};

// Ajouter à la liste locale (avant enregistrement DB)
const addToLocalList = () => {
    if (!elementForm.libelle || !elementForm.montant) {
        localError.value =
            "Veuillez remplir le libellé et le montant avant d'ajouter.";
        return;
    }
    localError.value = "";
    localElements.value.push({
        type: elementForm.type,
        libelle: elementForm.libelle,
        montant: elementForm.montant,
    });
    // Réinitialiser les champs de saisie mais garder le type
    elementForm.libelle = "";
    elementForm.montant = null;
};

const removeFromLocalList = (index) => {
    localElements.value.splice(index, 1);
};

// Enregistrement final en base de données
const handleBatchSaveElements = () => {
    if (localElements.value.length === 0) return;

    router.post(
        route("paie.element.store", selectedPaie.value.id),
        {
            elements: localElements.value,
        },
        {
            onSuccess: () => {
                elementModalOpen.value = false;
                localElements.value = [];
            },
        },
    );
};

const deleteExistingElement = (id) => {
    confirm_delete(() => {
        router.delete(route("paie.element.destroy", id), {
            preserveScroll: true,
        });
    });
};

// Gestion du paiement
const payModalOpen = ref(false);
const payForm = useForm({
    mode_paiement: "Espèce",
    date_paiement: new Date().toISOString().substr(0, 10),
    reference_paiement: "",
    telephone_paiement: "",
});

const openPayModal = (record) => {
    selectedPaie.value = record;
    payForm.reset();
    payForm.date_paiement = new Date().toISOString().substr(0, 10);
    payForm.mode_paiement = "Espèce";
    payModalOpen.value = true;
};

const handlePay = () => {
    payForm.post(route("paie.pay", selectedPaie.value.id), {
        onSuccess: () => {
            payModalOpen.value = false;
        },
    });
};

// Gestion de la modification (Salaire de base)
const editModalOpen = ref(false);
const editForm = useForm({
    salaire_base: null,
});

const openEditModal = (record) => {
    selectedPaie.value = record;
    editForm.salaire_base = record.salaire_base;
    editModalOpen.value = true;
};

const handleUpdate = () => {
    editForm.put(route("paie.update", selectedPaie.value.id), {
        onSuccess: () => {
            editModalOpen.value = false;
        },
    });
};

const actions = [
    {
        text: "Imprimer",
        action: (record) => window.open(route("paie.print", record.id)),
        icon: "fa-print",
        class: "!text-gray-600",
        privilege: "paie.print",
    },
    {
        text: "Modifier",
        action: openEditModal,
        icon: "fa-pen-to-square",
        class: "!text-blue-500",
        privilege: "paie.update",
    },
    {
        text: "Éléments",
        action: openElementModal,
        icon: "fa-list-check",
        class: "!text-amber-500",
        privilege: "paie.update",
    },
    {
        text: "Règlement",
        action: openPayModal,
        icon: "fa-money-bill-wave",
        class: "!text-green-500",
        privilege: "paie.pay",
    },
    {
        text: "Supprimer",
        action: (record) => {
            confirm_delete(() => {
                router.delete(route("paie.destroy", record.id));
            });
        },
        icon: "fa-trash",
        class: "!text-red-500",
        privilege: "paie.destroy",
    },
];
</script>

<template>
    <SousMenuPrincipale
        :title="title"
        selectedMenu="paie"
        v-if="can('paie.index')"
    >
        <template #top>
            <FilterBase
                v-model="filter"
                @search="applyFilters"
                @reset="resetFilters"
            >
                <template #otherFilter>
                    <a-popover
                        placement="bottomRight"
                        trigger="click"
                        :visible="dropdownVisible"
                        @visibleChange="(val) => (dropdownVisible = val)"
                    >
                        <template #content>
                            <div class="bg-white p-4 w-80 space-y-4 rounded-md">
                                <div class="space-y-1">
                                    <label
                                        class="text-xs font-bold text-gray-400 uppercase"
                                        >Période</label
                                    >
                                    <div class="grid grid-cols-2 gap-2">
                                        <a-select
                                            v-model:value="filter.mois"
                                            :options="months"
                                            placeholder="Mois"
                                            size="large"
                                            class="w-full"
                                        />
                                        <a-input-number
                                            v-model:value="filter.annee"
                                            placeholder="Année"
                                            size="large"
                                            class="w-full"
                                        />
                                    </div>
                                </div>

                                <div class="space-y-1">
                                    <label
                                        class="text-xs font-bold text-gray-400 uppercase"
                                        >Statut</label
                                    >
                                    <a-select
                                        v-model:value="filter.statut"
                                        placeholder="Filtrer par statut"
                                        :options="statutOptions"
                                        class="w-full"
                                        size="large"
                                        allow-clear
                                    />
                                </div>

                                <div class="flex space-x-2 !mt-6">
                                    <a-button
                                        block
                                        type="primary"
                                        size="middle"
                                        @click="applyFilters(filter)"
                                        >Appliquer</a-button
                                    >
                                    <a-button
                                        block
                                        type="default"
                                        size="middle"
                                        @click="dropdownVisible = false"
                                        >Fermer</a-button
                                    >
                                </div>
                            </div>
                        </template>
                        <a-button
                            size="large"
                            type="default"
                            class="!rounded-none border-r-0 focus:z-10"
                        >
                            <Space>
                                <font-awesome-icon
                                    class="text-[15px]"
                                    icon="fa-filter"
                                />
                                Filtres
                                <DownOutlined />
                            </Space>
                        </a-button>
                    </a-popover>
                </template>

                <template
                    #import
                    v-if="can('paie.export') || can('paie.print')"
                >
                    <a-dropdown>
                        <template #overlay>
                            <a-menu>
                                <a-menu-item
                                    v-if="can('paie.print')"
                                    key="print_bulletins"
                                    @click="handleMassPrint"
                                >
                                    <font-awesome-icon
                                        icon="fa-file-pdf"
                                        class="mr-2 text-red-500"
                                    />
                                    Imprimer les bulletins
                                </a-menu-item>
                                <a-menu-item
                                    v-if="can('paie.print')"
                                    key="print_list"
                                    @click="handlePrintList"
                                >
                                    <font-awesome-icon
                                        icon="fa-print"
                                        class="mr-2 text-blue-500"
                                    />
                                    Imprimer le tableau
                                </a-menu-item>
                                <a-menu-item
                                    v-if="can('paie.export')"
                                    key="excel"
                                    @click="handleExport"
                                >
                                    <font-awesome-icon
                                        icon="fa-file-excel"
                                        class="mr-2 text-green-600"
                                    />
                                    Exporter en Excel
                                </a-menu-item>
                            </a-menu>
                        </template>
                        <a-button
                            size="large"
                            class="!rounded-l-none !rounded-r-md border-l-0"
                        >
                            <font-awesome-icon icon="fa-download" />
                        </a-button>
                    </a-dropdown>
                </template>

                <template #add>
                    <ButtonIcon
                        v-if="can('paie.store')"
                        @click="handleOpenGeneration"
                        type="primary"
                        text="Générer les Fiches"
                        icon="fa-sync"
                        class="!rounded-md"
                    />
                </template>
            </FilterBase>
        </template>

        <DataTable
            :columns="columns"
            :data="data"
            :actions="actions"
            class="main-shadow"
            :show-index="true"
            :btn_action="true"
            @page-change="handlePageChange"
        >
            <template #bodyCell="{ column, record, text }">
                <template v-if="column.key === 'statut'">
                    <a-tag
                        :color="
                            record.statut === 'paye'
                                ? 'green'
                                : record.statut === 'valide'
                                  ? 'blue'
                                  : 'orange'
                        "
                    >
                        {{ record.statut?.toUpperCase() }}
                    </a-tag>
                </template>
                <template v-else-if="column.key === 'nom'">
                    {{ record.salarie?.nom }} {{ record.salarie?.prenom ?? "" }}
                </template>
                <template v-else-if="column.key === 'matricule'">
                    <span class="font-bold text-primary">{{
                        record.salarie?.matricule
                    }}</span>
                </template>
                <template
                    v-else-if="
                        ['salaire_base', 'salaire_net'].includes(column.key)
                    "
                >
                    {{ fmtCcy(text || 0) }}
                </template>
                <template v-else-if="column.key === 'primes'">
                    <span
                        class="text-green-600"
                        v-if="record.montant_primes > 0"
                    >
                        +{{ fmtCcy(record.montant_primes) }}
                    </span>
                    <span v-else>-</span>
                </template>
                <template v-else-if="column.key === 'retenues'">
                    <span
                        class="text-red-600"
                        v-if="record.montant_retenues > 0"
                    >
                        -{{ fmtCcy(record.montant_retenues) }}
                    </span>
                    <span v-else>-</span>
                </template>
            </template>
        </DataTable>

        <PaieGenerationModal
            ref="genModal"
            :mois="filter.mois"
            :annee="filter.annee"
            :months="months"
            @success="applyFilters(filter)"
        />

        <!-- Modal Modification Salaire -->
        <a-modal
            v-model:open="editModalOpen"
            title="Modifier le salaire de base"
            @ok="handleUpdate"
            :confirmLoading="editForm.processing"
        >
            <div class="py-4" v-if="selectedPaie">
                <p class="mb-4">
                    Salarié :
                    <strong
                        >{{ selectedPaie.salarie.nom }}
                        {{ selectedPaie.salarie.prenom }}</strong
                    >
                </p>
                <form-item
                    label="Salaire de Base (Ar)"
                    :help="editForm.errors.salaire_base"
                >
                    <a-input-number
                        v-model:value="editForm.salaire_base"
                        class="w-full"
                        :min="0"
                    />
                </form-item>
            </div>
        </a-modal>

        <!-- Modal Éléments (Primes/Retenues) -->
        <a-modal
            v-model:open="elementModalOpen"
            title="Primes et Retenues"
            width="700px"
            @cancel="elementModalOpen = false"
            :footer="null"
        >
            <div class="space-y-4 py-4">
                <!-- Formulaire de saisie locale -->
                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                    <h4 class="font-bold mb-3 text-primary">
                        <font-awesome-icon
                            icon="fa-plus-circle"
                            class="me-2"
                        />Saisir un nouvel élément
                    </h4>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label
                                class="block text-xs font-medium text-gray-500 mb-1"
                                >Type d'élément</label
                            >
                            <a-select
                                v-model:value="elementForm.type"
                                class="w-full"
                            >
                                <a-select-option value="prime"
                                    >Prime (+)</a-select-option
                                >
                                <a-select-option value="retenue"
                                    >Retenue (-)</a-select-option
                                >
                            </a-select>
                        </div>
                        <div>
                            <label
                                class="block text-xs font-medium text-gray-500 mb-1"
                                >Montant (Ar)</label
                            >
                            <a-input-number
                                v-model:value="elementForm.montant"
                                placeholder="Montant"
                                class="w-full"
                                :min="0"
                            />
                        </div>
                        <div class="col-span-2">
                            <label
                                class="block text-xs font-medium text-gray-500 mb-1"
                                >Libellé / Description</label
                            >
                            <a-input
                                v-model:value="elementForm.libelle"
                                placeholder="Ex: Prime de rendement, Avance sur salaire..."
                            />
                        </div>
                        <a-button
                            type="dashed"
                            @click="addToLocalList"
                            class="col-span-2 mt-2"
                            block
                        >
                            <font-awesome-icon icon="fa-plus" class="me-2" />
                            Ajouter à la liste temporaire
                        </a-button>
                        <div
                            v-if="localError"
                            class="col-span-2 text-red-500 text-xs font-medium mt-1 animate-pulse"
                        >
                            <font-awesome-icon
                                icon="fa-exclamation-triangle"
                                class="me-1"
                            />
                            {{ localError }}
                        </div>
                    </div>
                </div>

                <!-- Liste des éléments à enregistrer (Local) -->
                <div
                    v-if="localElements.length > 0"
                    class="border border-amber-200 rounded-lg overflow-hidden"
                >
                    <div
                        class="bg-amber-50 px-3 py-2 border-b border-amber-200 text-amber-800 text-xs font-bold uppercase"
                    >
                        Éléments à enregistrer
                    </div>
                    <table class="w-full text-sm">
                        <tbody class="bg-white">
                            <tr
                                v-for="(el, index) in localElements"
                                :key="index"
                                class="border-b last:border-0"
                            >
                                <td class="p-2 w-8">
                                    <a-tag
                                        :color="
                                            el.type === 'prime'
                                                ? 'green'
                                                : 'red'
                                        "
                                        >{{
                                            el.type === "prime" ? "+" : "-"
                                        }}</a-tag
                                    >
                                </td>
                                <td class="p-2 font-medium">
                                    {{ el.libelle }}
                                </td>
                                <td class="p-2 text-right font-bold">
                                    {{ fmtCcy(el.montant) }}
                                </td>
                                <td class="p-2 text-center w-10">
                                    <button
                                        @click="removeFromLocalList(index)"
                                        class="text-gray-400 hover:text-red-500 transition-colors"
                                    >
                                        <font-awesome-icon icon="fa-times" />
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Liste des éléments déjà existants en DB -->
                <div
                    v-if="selectedPaie?.elements?.length > 0"
                    class="border border-gray-100 rounded-lg overflow-hidden"
                >
                    <div
                        class="bg-gray-100 px-3 py-2 border-b border-gray-200 text-gray-600 text-xs font-bold uppercase"
                    >
                        Éléments enregistrés
                    </div>
                    <div class="max-h-40 overflow-y-auto">
                        <table class="w-full text-sm">
                            <tbody class="bg-white">
                                <tr
                                    v-for="el in selectedPaie.elements"
                                    :key="el.id"
                                    class="border-b last:border-0 opacity-70"
                                >
                                    <td class="p-2 w-8">
                                        <a-tag
                                            :color="
                                                el.type === 'prime'
                                                    ? 'green'
                                                    : 'red'
                                            "
                                            >{{
                                                el.type === "prime" ? "+" : "-"
                                            }}</a-tag
                                        >
                                    </td>
                                    <td class="p-2">{{ el.libelle }}</td>
                                    <td class="p-2 text-right">
                                        {{ fmtCcy(el.montant) }}
                                    </td>
                                    <td class="p-2 text-center w-10">
                                        <button
                                            @click="
                                                deleteExistingElement(el.id)
                                            "
                                            class="text-red-300 hover:text-red-600 transition-colors"
                                        >
                                            <font-awesome-icon
                                                icon="fa-trash"
                                            />
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Boutons d'action du Modal -->
                <div
                    class="flex justify-end gap-3 pt-4 mt-4 border-t border-gray-100"
                >
                    <a-button @click="elementModalOpen = false"
                        >Fermer</a-button
                    >
                    <a-button
                        type="primary"
                        :disabled="localElements.length === 0"
                        @click="handleBatchSaveElements"
                        class="px-8"
                    >
                        Valider et Enregistrer
                    </a-button>
                </div>
            </div>
        </a-modal>

        <!-- Modal Paiement -->
        <a-modal
            v-model:open="payModalOpen"
            title="Détails du Règlement"
            @ok="handlePay"
            :confirmLoading="payForm.processing"
            width="500px"
        >
            <div class="space-y-4 py-4" v-if="selectedPaie">
                <div
                    class="p-3 bg-blue-50 border border-blue-100 rounded text-blue-800"
                >
                    <p class="mb-0">
                        Règlement pour :
                        <strong
                            >{{ selectedPaie.salarie.nom }}
                            {{ selectedPaie.salarie.prenom }}</strong
                        >
                    </p>
                    <p class="text-lg font-bold mb-0 text-primary">
                        Net à payer :
                        {{
                            fmtCcy(selectedPaie.salaire_net)
                        }}
                    </p>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-2">
                        <label
                            class="block text-sm font-medium mb-1 text-gray-600"
                            >Mode de paiement :</label
                        >
                        <a-select
                            v-model:value="payForm.mode_paiement"
                            class="w-full"
                        >
                            <a-select-option value="Espèce"
                                >Espèce</a-select-option
                            >
                            <a-select-option value="Virement"
                                >Virement</a-select-option
                            >
                            <a-select-option value="Chèque"
                                >Chèque</a-select-option
                            >
                            <a-select-option value="AIRTEL MONEY"
                                >AIRTEL MONEY</a-select-option
                            >
                            <a-select-option value="MVOLA"
                                >MVOLA</a-select-option
                            >
                            <a-select-option value="ORANGE MONEY"
                                >ORANGE MONEY</a-select-option
                            >
                        </a-select>
                        <div
                            v-if="payForm.errors.mode_paiement"
                            class="text-red-500 text-xs mt-1"
                        >
                            {{ payForm.errors.mode_paiement }}
                        </div>
                    </div>

                    <div
                        v-if="
                            ['AIRTEL MONEY', 'MVOLA', 'ORANGE MONEY'].includes(
                                payForm.mode_paiement,
                            )
                        "
                        class="col-span-2"
                    >
                        <label
                            class="block text-sm font-medium mb-1 text-gray-600"
                            >Numéro de téléphone :</label
                        >
                        <a-input
                            v-model:value="payForm.telephone_paiement"
                            placeholder="03X XX XXX XX"
                        />
                        <div
                            v-if="payForm.errors.telephone_paiement"
                            class="text-red-500 text-xs mt-1"
                        >
                            {{ payForm.errors.telephone_paiement }}
                        </div>
                    </div>

                    <div
                        v-if="
                            [
                                'Virement',
                                'Chèque',
                                'AIRTEL MONEY',
                                'MVOLA',
                                'ORANGE MONEY',
                            ].includes(payForm.mode_paiement)
                        "
                        class="col-span-2"
                    >
                        <label
                            class="block text-sm font-medium mb-1 text-gray-600"
                            >Référence de transaction / N° Pièce :</label
                        >
                        <a-input
                            v-model:value="payForm.reference_paiement"
                            placeholder="Ex: Ref 123456"
                        />
                        <div
                            v-if="payForm.errors.reference_paiement"
                            class="text-red-500 text-xs mt-1"
                        >
                            {{ payForm.errors.reference_paiement }}
                        </div>
                    </div>

                    <div class="col-span-2">
                        <label
                            class="block text-sm font-medium mb-1 text-gray-600"
                            >Date de paiement :</label
                        >
                        <a-input
                            type="date"
                            v-model:value="payForm.date_paiement"
                            class="w-full"
                        />
                        <div
                            v-if="payForm.errors.date_paiement"
                            class="text-red-500 text-xs mt-1"
                        >
                            {{ payForm.errors.date_paiement }}
                        </div>
                    </div>
                </div>
            </div>
        </a-modal>
    </SousMenuPrincipale>
</template>
