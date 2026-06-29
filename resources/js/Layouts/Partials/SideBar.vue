<script setup>
import { Link, router, usePage } from "@inertiajs/vue3";
import { defineProps, ref, h, computed } from "vue";
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { PerfectScrollbar } from "vue3-perfect-scrollbar";
import { getMenuData } from "../../../Utils/MenuData.js";

const collapsed = defineModel("collapsed");

const props = defineProps({
    selectedMenu: {
        type: String,
        default: "",
    },
});

const searchMenu = ref("");

// Récupération de l'utilisateur et de ses permissions
const user = computed(() => usePage().props.auth.user);
const acceptedRoutes = computed(() => {
    const routes = user.value?.accepted_routes;

    if (routes && typeof routes === "object" && !Array.isArray(routes)) {
        return Object.values(routes);
    }

    return Array.isArray(routes) ? routes : [];
});

const tenantModules = computed(() => usePage().props.auth.modules || []);

function hasPermission(routeName, moduleName) {
    // Vérification du module (Plan)
    if (moduleName && !tenantModules.value.includes(moduleName)) {
        return false;
    }

    // Vérification de la route (Permissions utilisateur)
    if (!routeName) return true;
    if (user.value?.is_dna) return true;
    return acceptedRoutes.value.includes(routeName);
}

function getItem(label, key, icon, routeName, module = null, children = null) {
    const iconNode = icon ? h(FontAwesomeIcon, { icon }) : null;
    return {
        key,
        label,
        icon: iconNode,
        routeName,
        module,
        children: children
            ? children.map((child) => ({
                  ...child,
                  class: "menu-item-child",
              }))
            : null,
    };
}

// Transformer getMenuData() pour qu'il soit compatible avec le format attendu par Ant Design Menu dans SideBar
function transformMenuData(items) {
    return items.map(item => {
        return getItem(
            item.label,
            item.key,
            item.icon,
            item.routeName,
            item.module,
            item.children ? transformMenuData(item.children) : null
        );
    });
}

// Configuration des menus basée sur MenuData.js
const ALL_MENU_DATA = transformMenuData(getMenuData());

// Fonction pour normaliser le texte (enlever les accents et convertir en minuscules)
function normalizeText(text) {
    return text
        .toLowerCase()
        .normalize("NFD")
        .replace(/[\u0300-\u036f]/g, "");
}

// Fonction pour rechercher dans un élément de menu
function searchInMenuItem(item, searchTerm) {
    const normalizedSearch = normalizeText(searchTerm);
    const normalizedLabel = normalizeText(item.label);

    // Vérifier si le terme de recherche correspond au label de l'élément
    const labelMatches = normalizedLabel.includes(normalizedSearch);

    // Si l'élément a des enfants, rechercher dans les enfants
    let matchingChildren = [];
    if (item.children) {
        matchingChildren = item.children.filter((child) =>
            searchInMenuItem(child, searchTerm)
        );
    }

    // Retourner true si le label correspond ou si au moins un enfant correspond
    return labelMatches || matchingChildren.length > 0;
}

// Fonction pour filtrer les éléments de menu selon la recherche
function filterMenuBySearch(items, searchTerm) {
    if (!searchTerm.trim()) {
        return items;
    }

    return items.reduce((acc, item) => {
        const newItem = { ...item };

        // Si l'élément a des enfants, filtrer les enfants
        if (newItem.children) {
            const filteredChildren = newItem.children.filter((child) =>
                searchInMenuItem(child, searchTerm)
            );

            // Si le parent correspond à la recherche, garder tous les enfants visibles
            if (searchInMenuItem({ label: newItem.label }, searchTerm)) {
                newItem.children = newItem.children;
                acc.push(newItem);
            }
            // Sinon, ne garder que les enfants qui correspondent
            else if (filteredChildren.length > 0) {
                newItem.children = filteredChildren;
                acc.push(newItem);
            }
        } else {
            // Pour les éléments sans enfants, vérifier s'ils correspondent
            if (searchInMenuItem(newItem, searchTerm)) {
                acc.push(newItem);
            }
        }

        return acc;
    }, []);
}

// Fonction optimisée pour filtrer les menus par permissions
function filterMenuItems(items) {
    return items.reduce((acc, item) => {
        const newItem = { ...item };

        // Vérification du module (Plan) pour l'item (parent ou enfant)
        if (newItem.module && !tenantModules.value.includes(newItem.module)) {
            return acc;
        }

        // AFFICHER le calendrier quelque choix le privilege
        if (newItem.key === "dashboard") {
            acc.push(newItem);
            return acc;
        }

        if (newItem.children) {
            newItem.children = filterMenuItems(newItem.children);
            if (newItem.children.length > 0) {
                acc.push(newItem);
            }
        } else if (hasPermission(newItem.routeName, newItem.module)) {
            acc.push(newItem);
        }

        return acc;
    }, []);
}

// Menu filtré par permissions ET par recherche
const MENU_DATA = computed(() => {
    const filteredByPermissions = filterMenuItems(ALL_MENU_DATA);
    return filterMenuBySearch(filteredByPermissions, searchMenu.value);
});

// Gestion de l'état ouvert des menus
const defaultOpenKeys = [
    "accueil",
    "dashboard",
    "settings",
    "users",
    "planning",
    "magasin",
    "carburant",
    "article",
    "tiers",
    "voyages",
];

const savedOpenKeys = localStorage.getItem("openKeys");
const openKeys = ref(
    savedOpenKeys ? JSON.parse(savedOpenKeys) : defaultOpenKeys
);

// Computed pour gérer l'ouverture automatique lors de la recherche
const computedOpenKeys = computed(() => {
    if (searchMenu.value.trim()) {
        // Si une recherche est active, ouvrir tous les menus qui ont des résultats
        const keysToOpen = [];
        MENU_DATA.value.forEach((item) => {
            if (item.children && item.children.length > 0) {
                keysToOpen.push(item.key);
            }
        });
        return keysToOpen;
    }
    return openKeys.value;
});

function handleOpenChange(keys) {
    // Ne pas sauvegarder les clés ouvertes pendant une recherche
    if (!searchMenu.value.trim()) {
        openKeys.value = keys;
        localStorage.setItem("openKeys", JSON.stringify(keys));
    }
}

function handleMenuClick({ item }) {
    if (item.routeName) {
        router.get(route(item.routeName), {}, { preserveState: true });
    }
}

// Fonction pour effacer la recherche
function clearSearch() {
    searchMenu.value = "";
}
</script>

<template>
    <a-layout-sider
        id="side-bar"
        v-model:collapsed="collapsed"
        theme="light"
        :width="260"
        breakpoint="lg"
        :collapsed-width="0"
        :trigger="null"
        class="!fixed h-full shadow-lg z-50"
    >
        <div class="flex flex-col bg-primary h-full py-3">
            <!-- Champ de recherche avec bouton clear -->
            <div class="relative px-2 mb-2">
                <a-input
                    class="w-full rounded-md pr-8"
                    placeholder="Rechercher dans le menu..."
                    v-model:value="searchMenu"
                    allow-clear
                >
                    <template #prefix>
                        <FontAwesomeIcon
                            icon="fa-solid fa-search"
                            class="text-gray-400"
                        />
                    </template>
                </a-input>

                <!-- Message si aucun résultat -->
                <div
                    v-if="searchMenu.trim() && MENU_DATA.length === 0"
                    class="text-center text-gray-300 mt-4 px-4"
                >
                    <FontAwesomeIcon
                        icon="fa-solid fa-search"
                        class="text-2xl mb-2 opacity-50"
                    />
                    <p class="text-sm">
                        Aucun résultat trouvé pour "{{ searchMenu }}"
                    </p>
                </div>
            </div>

            <PerfectScrollbar class="h-[calc(100vh-4rem)] mb-16">
                <a-menu
                    :selectedKeys="[selectedMenu]"
                    :openKeys="computedOpenKeys"
                    @openChange="handleOpenChange"
                    class="!border-0 bg-transparent custom-menu"
                    theme="light"
                    mode="inline"
                    :items="MENU_DATA"
                    @click="handleMenuClick"
                />
            </PerfectScrollbar>
        </div>
    </a-layout-sider>
</template>

<style scoped>
.custom-menu :deep(.ant-menu-item) {
    @apply relative flex items-center text-white !pl-6 h-11 leading-10 !outline-none rounded-md transition-all duration-200 ease-in-out;
}

.custom-menu :deep(.ant-menu-submenu-title) {
    @apply relative flex items-center !pl-6 h-11 leading-10 rounded-md !outline-none text-white transition-all duration-200 ease-in-out;
}

.custom-menu :deep(.ant-menu-item-selected) {
    @apply bg-dark-gray !text-white;
}

.custom-menu :deep(.ant-menu-submenu-selected > .ant-menu-submenu-title) {
    @apply bg-primary !text-secondary;
}

.custom-menu :deep(.ant-menu-submenu-selected .anticon),
.custom-menu :deep(.ant-menu-submenu-open .anticon) {
    @apply !text-primary;
}

.custom-menu
    :deep(
        .ant-menu-submenu-selected > .ant-menu-submenu-title .ant-menu-item-icon
    ),
.custom-menu
    :deep(
        .ant-menu-submenu-selected
            > .ant-menu-submenu-title
            .ant-menu-submenu-arrow
    ) {
    @apply text-secondary;
}

.custom-menu :deep(.ant-menu-item:hover),
.custom-menu :deep(.ant-menu-submenu-title:hover) {
    @apply bg-transparent !text-secondary;
}

.custom-menu :deep(.ant-menu-item:focus),
.custom-menu :deep(.ant-menu-submenu-title:focus) {
    @apply outline-none shadow-none;
}

.custom-menu :deep(.ant-menu-sub.ant-menu-inline) {
    @apply pl-9 !bg-secondary/5 py-2;
}

.custom-menu :deep(.ant-menu-sub .ant-menu-item) {
    @apply relative my-0 rounded-none font-normal text-gray-300 !pl-8 transition-all duration-200;
}

.custom-menu :deep(.ant-menu-sub .ant-menu-item::before) {
    @apply content-[""] absolute left-0 top-0 bottom-0 w-[1px] bg-secondary/50;
}

.custom-menu :deep(.ant-menu-sub .ant-menu-item-selected) {
    @apply bg-transparent !text-secondary transition-all duration-300 hover:brightness-110;
}

.custom-menu :deep(.ant-menu-sub .ant-menu-item-selected .anticon) {
    @apply !text-white;
}

/* Style pour le champ de recherche */
.custom-menu :deep(.ant-input-affix-wrapper) {
    @apply bg-white/10 border-white/20 text-white;
}

.custom-menu :deep(.ant-input-affix-wrapper:focus),
.custom-menu :deep(.ant-input-affix-wrapper:hover) {
    @apply border-secondary bg-white/15;
}

.custom-menu :deep(.ant-input) {
    @apply bg-transparent text-white placeholder-gray-300;
}
</style>
