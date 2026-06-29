<template>
    <SousMenuPrincipale
        :title="title"
        selectedMenu="user-groups"
        v-if="can('group_user.index')"
    >
        <template #top>
            <FilterBase
                v-model="props.filters"
                @search="applyFilters"
                @reset="resetFilters"
            >
                <template #add>
                    <ButtonIcon
                        v-if="can('group_user.store')"
                        @click="addRow()"
                        type="primary"
                        text="Nouveau Groupe"
                        icon="fa-plus"
                    />
                </template>
            </FilterBase>
        </template>

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <DataTable
                :columns="columns"
                :actions="actions"
                :data="data"
                :scroll="{ x: 500 }"
                :btn_action="false"
            />
        </div>

        <FormModal
            v-model:open="visible_modal"
            :titre="modal_title"
            :loading="form.processing"
            @close="closeModal"
            @submit="onSubmit"
            size="full_screen"
            :show_champ_obligatoir="false"
        >
            <div class="w-full mx-auto space-y-4">
                <!-- Informations de base -->
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
                    <div class="flex items-center gap-3 mb-5">
                        <div class="w-9 h-9 rounded-lg bg-indigo-50 flex items-center justify-center">
                            <font-awesome-icon icon="fa-users-gear" class="text-indigo-500 text-sm" />
                        </div>
                        <div>
                            <h3 class="text-base font-semibold text-gray-800 leading-none">Informations de base</h3>
                            <p class="text-xs text-gray-400 mt-0.5">Nom du groupe d'utilisateurs</p>
                        </div>
                    </div>

                    <form-item
                        label="Nom du groupe"
                        required
                        :help="form.errors.name"
                        class="mb-0"
                    >
                        <a-input
                            v-model:value="form.name"
                            placeholder="Ex: Comptable, Chauffeur, Administrateur..."
                            size="large"
                            class="!border-gray-300 focus:!border-indigo-400 !rounded-lg"
                        />
                    </form-item>
                </div>

                <!-- Section Gestion des autorisations -->
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                    <!-- En-tête sticky -->
                    <div class="sticky top-0 z-10 bg-white border-b border-gray-100 px-6 pt-5 pb-4">
                        <div class="flex items-start justify-between gap-4 mb-4">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-lg bg-amber-50 flex items-center justify-center">
                                    <font-awesome-icon icon="fa-shield-halved" class="text-amber-500 text-sm" />
                                </div>
                                <div>
                                    <h3 class="text-base font-semibold text-gray-800 leading-none">Gestion des autorisations</h3>
                                    <p class="text-xs text-gray-400 mt-0.5">Configurez les accès pour ce groupe</p>
                                </div>
                            </div>
                            <!-- Compteur global -->
                            <div class="flex items-center gap-2 shrink-0">
                                <div class="flex items-center gap-1.5 bg-gray-50 border border-gray-200 rounded-lg px-3 py-1.5">
                                    <div
                                        class="w-2 h-2 rounded-full"
                                        :class="totalSelected > 0 ? 'bg-green-500' : 'bg-gray-300'"
                                    ></div>
                                    <span class="text-xs font-medium text-gray-600">
                                        {{ totalSelected }} / {{ totalPrivileges }} permissions
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Barre de recherche + boutons rapides -->
                        <div class="flex gap-2">
                            <a-input
                                v-model:value="searchPrivilege"
                                placeholder="Rechercher un module ou une permission..."
                                allow-clear
                                size="large"
                                class="flex-1 !rounded-lg"
                            >
                                <template #prefix>
                                    <font-awesome-icon icon="fa-magnifying-glass" class="text-gray-400 text-sm" />
                                </template>
                            </a-input>
                            <a-button
                                size="large"
                                class="!rounded-lg !border-gray-200 !text-gray-600 hover:!border-green-400 hover:!text-green-600"
                                @click="selectAllPrivileges"
                                title="Tout sélectionner"
                            >
                                <font-awesome-icon icon="fa-square-check" />
                            </a-button>
                            <a-button
                                size="large"
                                class="!rounded-lg !border-gray-200 !text-gray-600 hover:!border-red-400 hover:!text-red-500"
                                @click="clearAllPrivileges"
                                title="Tout désélectionner"
                            >
                                <font-awesome-icon icon="fa-square-minus" />
                            </a-button>
                        </div>

                        <!-- Badge "aucun résultat" -->
                        <div v-if="searchPrivilege && filteredPrivilege.length === 0" class="mt-3 flex items-center gap-2 text-sm text-gray-400">
                            <font-awesome-icon icon="fa-circle-exclamation" />
                            Aucun module ne correspond à "{{ searchPrivilege }}"
                        </div>
                    </div>

                    <!-- Arbre des privilèges -->
                    <div class="p-6">
                        <PrivilegeTree
                            :privilege="filteredPrivilege"
                            v-model:selectedPrivileges="form.privileges"
                        />
                    </div>
                </div>
            </div>
        </FormModal>
    </SousMenuPrincipale>
</template>

<script setup>
import ButtonIcon from "@/Components/ButtonIcon.vue";
import DataTable from "@/Components/DataTable.vue";
import FilterBase from "@/Components/Filter/FilterBase.vue";
import FormItem from "@/Components/FormItem.vue";
import FormModal from "@/Components/FormModal.vue";
import PrivilegeTree from "@/Components/PrivilegeTree.vue";
import SousMenuPrincipale from "@/Pages/Menu/SousMenuPrincipale.vue";
import usePermissions from "@/UserPermissions/usePermissions.js";
import { router, useForm } from "@inertiajs/vue3";
import { computed, ref } from "vue";
import { confirm_delete } from "../../../Utils/confirmation_modal.js";
import { createSearchFilter, gotoSearch } from "../../../Utils/FiltreUtils.js";

const { can } = usePermissions();

const props = defineProps({
    data: { type: Object, default: () => ({}) },
    privilege: { type: Array, default: () => [] },
    selectedgroup: { type: Object, default: () => ({}) },
    filters: { type: Object, default: () => ({}) },
});

const columns = [{ key: "name", title: "Nom du groupe", dataIndex: "name" }];
const DEFAULT_DATA = () => ({ id: null, name: "", privileges: [] });
const form = useForm(DEFAULT_DATA());
const modal_title = ref("");
const visible_modal = ref(false);
const filter = ref(createSearchFilter());
const searchPrivilege = ref("");

const title = computed(
    () => `Liste des Groupes d'Utilisateurs (${props.data?.total ?? 0})`
);

// Filtre de l'arbre de privilèges
const filteredPrivilege = computed(() => {
    const q = searchPrivilege.value.trim().toLowerCase();
    if (!q) return props.privilege;

    return props.privilege.reduce((acc, module) => {
        if (module.title.toLowerCase().includes(q)) {
            acc.push(module);
            return acc;
        }
        if (module.children) {
            const matchingChildren = module.children.filter((child) => {
                if (child.title.toLowerCase().includes(q)) return true;
                if (child.children) {
                    return child.children.some((gc) =>
                        gc.title.toLowerCase().includes(q)
                    );
                }
                return false;
            });
            if (matchingChildren.length > 0) {
                acc.push({ ...module, children: matchingChildren });
            }
        }
        return acc;
    }, []);
});

// Compteurs globaux
const getAllRoutes = (nodes) => {
    let routes = [];
    (nodes || []).forEach((n) => {
        if (n.routes) routes.push(...n.routes);
        if (n.children) routes.push(...getAllRoutes(n.children));
    });
    return routes;
};

const totalPrivileges = computed(() => new Set(getAllRoutes(props.privilege)).size);
const totalSelected = computed(() => form.privileges?.length ?? 0);

// Tout sélectionner / désélectionner
const selectAllPrivileges = () => {
    const all = [...new Set(getAllRoutes(props.privilege))];
    form.privileges = all;
};
const clearAllPrivileges = () => {
    form.privileges = [];
};

const deleteRow = (record) => {
    confirm_delete(() => {
        router.delete(route("group_user.destroy", record.id), {
            preserveScroll: true,
        });
    });
};

const actions = [
    {
        text: "Modifier",
        action: (r) => editRow(r),
        icon: "fa-pen-to-square",
        privilege: "group_user.update",
    },
    {
        text: "Supprimer",
        action: deleteRow,
        icon: "fa-trash",
        class: "text-red-500 hover:!text-red-700",
        disabled: (r) => r.is_you || props.data.total < 1,
        privilege: "group_user.destroy",
    },
];

const applyFilters = (data) => {
    filter.value = data;
    gotoSearch(filter.value, route("group_user.index"));
};

const resetFilters = () => {
    filter.value.search = "";
    applyFilters();
};

const addRow = () => {
    form.reset();
    form.clearErrors();
    searchPrivilege.value = "";
    modal_title.value = "Nouveau groupe d'utilisateurs";
    visible_modal.value = true;
};

const closeModal = () => {
    visible_modal.value = false;
    searchPrivilege.value = "";
    form.reset();
    form.clearErrors();
};

const editRow = (rowData) => {
    form.reset();
    form.clearErrors();
    searchPrivilege.value = "";
    modal_title.value = "Editer un groupe d'utilisateurs";
    visible_modal.value = true;

    router.visit(`${route("group_user.show", rowData.id)}`, {
        preserveState: true,
        preserveScroll: true,
        only: ["selectedgroup"],
        onSuccess: () => {
            Object.assign(form, props.selectedgroup);
        },
    });
};

const onSubmit = () => {
    if (!form.id) {
        form.post(route("group_user.store"), { preserveScroll: true, onSuccess: closeModal });
    } else {
        form.put(route("group_user.update", form.id), {
            preserveScroll: true,
            preserveState: true,
            onSuccess: closeModal,
        });
    }
};
</script>

<style scoped>
:deep(.ant-input) {
    border-radius: 8px;
    transition: all 0.2s ease;
}
:deep(.ant-input:focus),
:deep(.ant-input-focused) {
    box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.15);
}
:deep(.ant-checkbox-checked .ant-checkbox-inner) {
    background-color: #6366f1;
    border-color: #6366f1;
}
:deep(.ant-checkbox-indeterminate .ant-checkbox-inner) {
    background-color: #a5b4fc;
    border-color: #a5b4fc;
}
</style>
