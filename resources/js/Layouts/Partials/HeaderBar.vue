<script setup>
import { Link, router, usePage } from "@inertiajs/vue3";
import { computed, onBeforeUnmount, onMounted, ref, watch, nextTick } from "vue";
import { findParentByChildKey } from "../../../Utils/MenuData.js";
import { filterMenuByPrivileges } from "../../../Utils/MenuFilter.js";
import NotificationBell from "@/Components/NotificationBell.vue";

const props = defineProps({
    title: String,
    toggleSidebar: Function,
    selectedMenu: {
        type: String,
        default: "",
    },
    backTo: {
        type: String,
        default: null,
    },
});

function normalizePrivileges(priv) {
    if (!priv) return [];
    if (Array.isArray(priv)) return priv;
    if (typeof priv === "object") return Object.values(priv);
    return [];
}

const user = usePage().props.auth.user;
const tenantModules = usePage().props.auth.modules || [];
const privileges = [
    ...normalizePrivileges(user.accepted_routes),
    ...normalizePrivileges(user.permissions),
];

// Variables réactives
const menuParent = ref(null);
const subMenuItems = ref([]);
const visibleItems = ref([]);
const hiddenItems = ref([]);
const menuContainer = ref(null);
const isCalculating = ref(false);

// Fonction pour sélectionner un bouton (gère les URLs cross-domain pour les routes centrales)
function selectButton(item) {
    try {
        const url = route(item.routeName);
        const isCrossDomain = url.startsWith('http') && !url.startsWith(window.location.origin);
        if (isCrossDomain) {
            window.location.href = url;
        } else {
            router.get(url, {});
        }
    } catch (e) {
        console.warn(`Navigation impossible vers "${item.routeName}": ${e.message}`);
    }
}

// Fonction de diagnostic (temporaire pour debug)
const diagnoseLayout = () => {
    if (!menuContainer.value) return;

    const container = menuContainer.value;
    const containerWidth = container.offsetWidth;
    const containerStyles = window.getComputedStyle(container);

    console.log('=== DIAGNOSTIC LAYOUT ===');
    console.log('Container width:', containerWidth);
    console.log('Container styles:', {
        paddingLeft: containerStyles.paddingLeft,
        paddingRight: containerStyles.paddingRight,
        marginLeft: containerStyles.marginLeft,
        marginRight: containerStyles.marginRight
    });
    console.log('SubMenu items:', subMenuItems.value.map(item => item.label));
    console.log('Current visible:', visibleItems.value.map(item => item.label));
    console.log('Current hidden:', hiddenItems.value.map(item => item.label));

    // Mesurer chaque bouton individuellement
    const actualButtons = container.querySelectorAll('.ant-btn');
    console.log('Actual buttons in DOM:', actualButtons.length);
    let totalActualWidth = 0;
    actualButtons.forEach((btn, index) => {
        const width = btn.offsetWidth;
        totalActualWidth += width;
        console.log(`Button ${index}: "${btn.textContent.trim()}" = ${width}px`);
    });
    console.log('Total actual width:', totalActualWidth);
    console.log('========================');
};

// Fonction optimisée pour calculer les boutons visibles
const calculateVisibleButtons = async () => {
    if (isCalculating.value) return;
    isCalculating.value = true;

    try {
        // Attendre que le DOM soit mis à jour
        await nextTick();

        // Vérifications initiales
        if (!subMenuItems.value.length || !menuContainer.value) {
            visibleItems.value = [...subMenuItems.value];
            hiddenItems.value = [];
            isCalculating.value = false;
            return;
        }

        const container = menuContainer.value;
        const containerRect = container.getBoundingClientRect();
        const containerWidth = containerRect.width;
        const containerStyles = window.getComputedStyle(container);
        const containerPadding = parseFloat(containerStyles.paddingLeft) + parseFloat(containerStyles.paddingRight);
        const availableWidth = containerWidth - containerPadding;

        // Largeur du bouton dropdown (mesure plus précise)
        const dropdownButtonWidth = 50;
        const safetyMargin = 5; // Marge de sécurité minimale
        const effectiveWidth = availableWidth - safetyMargin;

        // Créer un élément temporaire pour mesurer les largeurs avec styles exacts
        const measureElement = document.createElement('button');
        measureElement.style.cssText = `
            position: absolute;
            visibility: hidden;
            pointer-events: none;
            white-space: nowrap;
            padding: 0.5rem 1rem;
            font-size: ${containerStyles.fontSize};
            font-family: ${containerStyles.fontFamily};
            font-weight: ${containerStyles.fontWeight};
            border: 1px solid transparent;
            margin: 0;
            box-sizing: border-box;
            display: inline-block;
        `;
        measureElement.className = 'ant-btn ant-btn-text';
        document.body.appendChild(measureElement);

        const buttonWidths = [];
        let totalWidth = 0;

        // Mesurer chaque bouton avec plus de précision
        subMenuItems.value.forEach((item, index) => {
            measureElement.innerHTML = `<span class="hidden sm:inline whitespace-nowrap">${item.label}</span>`;
            const rect = measureElement.getBoundingClientRect();
            const buttonWidth = Math.ceil(rect.width) + 2; // +2 pour les micro-espacements
            buttonWidths.push(buttonWidth);
            totalWidth += buttonWidth;
        });

        document.body.removeChild(measureElement);

        const newVisible = [];
        const newHidden = [];

        // Logique principale : essayer de maximiser les éléments visibles
        if (totalWidth <= effectiveWidth) {
            // Tous les boutons rentrent sans dropdown
            newVisible.push(...subMenuItems.value);
        } else {
            // Il faut un dropdown, calculer le maximum d'éléments visibles
            const availableForButtons = effectiveWidth - dropdownButtonWidth;
            let currentWidth = 0;

            for (let i = 0; i < subMenuItems.value.length; i++) {
                const buttonWidth = buttonWidths[i];

                // Vérifier si ce bouton peut encore rentrer
                if (currentWidth + buttonWidth <= availableForButtons) {
                    newVisible.push(subMenuItems.value[i]);
                    currentWidth += buttonWidth;
                } else {
                    // Le reste va dans le dropdown
                    newHidden.push(...subMenuItems.value.slice(i));
                    break;
                }
            }

            // Assurer qu'au moins un élément est visible
            if (newVisible.length === 0 && subMenuItems.value.length > 0) {
                newVisible.push(subMenuItems.value[0]);
                newHidden.push(...subMenuItems.value.slice(1));
            }
        }

        // Mise à jour atomique pour éviter les états intermédiaires
        visibleItems.value = [...newVisible];
        hiddenItems.value = [...newHidden];

        // Debug détaillé
        console.log('📊 Menu Layout Calculation:', {
            totalItems: subMenuItems.value.length,
            containerWidth: Math.round(containerWidth),
            availableWidth: Math.round(availableWidth),
            effectiveWidth: Math.round(effectiveWidth),
            totalButtonsWidth: totalWidth,
            dropdownNeeded: newHidden.length > 0,
            dropdownWidth: dropdownButtonWidth,
            visible: {
                count: newVisible.length,
                items: newVisible.map((item, i) => ({
                    label: item.label,
                    width: buttonWidths[subMenuItems.value.indexOf(item)]
                })),
                totalWidth: newVisible.reduce((sum, item) => sum + buttonWidths[subMenuItems.value.indexOf(item)], 0)
            },
            hidden: {
                count: newHidden.length,
                items: newHidden.map(item => item.label)
            }
        });

    } catch (error) {
        console.error('❌ Error calculating visible buttons:', error);
        // Fallback sécurisé
        visibleItems.value = [...subMenuItems.value];
        hiddenItems.value = [];
    } finally {
        isCalculating.value = false;
    }
};

// Fonction de debounce pour éviter les calculs trop fréquents
let resizeTimeout = null;
const debouncedCalculate = () => {
    if (resizeTimeout) {
        clearTimeout(resizeTimeout);
    }
    resizeTimeout = setTimeout(calculateVisibleButtons, 150);
};

// Observer pour détecter les changements de taille du container
let resizeObserver = null;

// Initialisation et mise à jour des sous-menus
const updateSubMenuItems = async () => {
    const parent = findParentByChildKey(props.selectedMenu);
    menuParent.value = parent;
    const newItems = parent ? filterMenuByPrivileges(parent.children || [], privileges, user.is_dna, tenantModules) : [];

    // Vérifier si les items ont changé
    const itemsChanged = JSON.stringify(subMenuItems.value.map(i => i.key)) !== JSON.stringify(newItems.map(i => i.key));

    subMenuItems.value = newItems;

    // Si les items ont changé, forcer un recalcul immédiat
    if (itemsChanged) {
        await nextTick();
        setTimeout(calculateVisibleButtons, 10);
    }
};

// Lifecycle hooks
onMounted(async () => {
    // Initialiser les sous-menus
    updateSubMenuItems();

    // Attendre que le DOM soit prêt
    await nextTick();

    // Configurer l'observer pour le redimensionnement
    if (window.ResizeObserver && menuContainer.value) {
        resizeObserver = new ResizeObserver(debouncedCalculate);
        resizeObserver.observe(menuContainer.value);
    } else {
        // Fallback pour les navigateurs plus anciens
        window.addEventListener("resize", debouncedCalculate);
    }

    // Calcul initial
    setTimeout(calculateVisibleButtons, 100);
});

onBeforeUnmount(() => {
    // Nettoyer les événements et observers
    if (resizeObserver) {
        resizeObserver.disconnect();
    } else {
        window.removeEventListener("resize", debouncedCalculate);
    }

    if (resizeTimeout) {
        clearTimeout(resizeTimeout);
    }
});

// Watchers
watch(() => props.selectedMenu, async () => {
    await updateSubMenuItems();
}, {immediate: false});

watch(subMenuItems, async () => {
    await nextTick();
    calculateVisibleButtons();
}, {deep: true, immediate: false});

// Computed pour vérifier si on doit afficher le dropdown
const shouldShowDropdown = computed(() => hiddenItems.value.length > 0);

const isAdmin = computed(() => usePage().url.startsWith('/admin'));

// Computed pour les classes du container
const menuContainerClasses = computed(() => [
    'flex-1 flex items-center space-x-1 overflow-hidden',
    isCalculating.value ? 'opacity-50' : ''
]);
</script>

<template>
    <a-layout-header class="sticky top-0 z-50 h-14 bg-gradient-to-r from-[#172A6C] to-primary border-b border-white/10 flex justify-between items-center px-8 shadow-sm">
        <!-- Left: Logo compact -->
        <div class="flex items-center gap-3 flex-shrink-0">
            <a-button
                v-if="backTo"
                type="text"
                size="large"
                class="group flex items-center gap-1.5 bg-white/10 text-white hover:!text-secondary hover:!bg-white/5 border-white/20 hover:border-white/50 !rounded-md transition-all duration-500 ease-in-out px-3 h-10"
                @click="router.get(backTo)"
            >
                <font-awesome-icon icon="fa-solid fa-arrow-left" class="text-sm group-hover:text-secondary transition-all duration-300" />
                <span class="hidden sm:inline text-sm font-medium">Retour</span>
            </a-button>
            <a-button
                v-else
                type="text"
                size="large"
                class="group flex items-center justify-center bg-white/10 text-white hover:!text-secondary hover:!bg-white/5 border-white/20 hover:border-white/50 !rounded-md transition-all duration-500 ease-in-out p-2 h-10 w-10"
                @click="toggleSidebar"
            >
                <div class="relative w-4 h-4 flex items-center justify-center">
                    <font-awesome-icon
                        icon="fa-home"
                        class="!me-0 text-xl text-white group-hover:text-secondary transition-all duration-300"
                    />
                </div>
            </a-button>

            <div class="h-6 w-px bg-white/20 hidden  lg:block"></div>

            <!-- Logo moderne en ligne avec SVG -->
            <div class="hidden sm:flex flex-col -mt-1">
                <span
                    class="text-white font-['Arial'] !font-extrabold text-base lg:text-3xl leading-tight flex items-center">
                    T
                    <svg class="w-8 h-8 text-white truck-move" viewBox="0 0 24 24" fill="currentColor">
                        <!-- Corps du camion -->
                        <path d="M3 17h2c0 1.1.9 2 2 2s2-.9 2-2h6c0 1.1.9 2 2 2s2-.9 2-2h2v-2H3v2z"/>
                        <path d="M3 13h12v-2H3v2z"/>
                        <!-- Cabine du camion -->
                        <path d="M16 13h5l-3-4h-2v4z"/>
                        <path d="M3 11h12V8H3v3z"/>
                        <!-- Roues -->
                        <circle cx="7" cy="17" r="1"/>
                        <circle cx="17" cy="17" r="1"/>
                    </svg>
                    <span class="text-secondary">M </span>S
                </span>
                <span class="text-white/70 text-[10px] font-medium tracking-wide -mt-1">
                    Transport Management System
                </span>
            </div>
        </div>

        <!-- Center Section: Dynamic Menu Buttons -->
        <div :class="menuContainerClasses" ref="menuContainer">
            <!-- Boutons visibles -->
            <a-button
                v-for="(item, index) in visibleItems"
                :key="`visible-${item.key}-${index}`"
                type="text"
                @click="selectButton(item)"
                :class="[
                    'flex items-center justify-center gap-2 !rounded-none transition-all duration-300 hover:!text-secondary flex-shrink-0',
                    selectedMenu === item.key ? 'text-secondary' : 'text-white',
                    index !== visibleItems.length - 1 && !shouldShowDropdown ? '!border-r !border-r-white/30' : '',
                    shouldShowDropdown && index === visibleItems.length - 1 ? '!border-r !border-r-white/30' : ''
                ]"
            >
                <span class="hidden sm:inline whitespace-nowrap">{{ item.label }}</span>
            </a-button>

            <!-- Bouton dropdown pour les éléments cachés -->
            <a-dropdown
                v-if="shouldShowDropdown"
                placement="bottomLeft"
                :trigger="['click']"
                overlayClassName="submenu-dropdown"
            >
                <a-button
                    type="text"
                    class="flex items-center justify-center text-white hover:!text-secondary transition-all duration-300 flex-shrink-0"
                >
                    <font-awesome-icon icon="fa-solid fa-ellipsis-vertical"/>
                </a-button>
                <template #overlay>
                    <a-menu class="min-w-48">
                        <a-menu-item
                            v-for="(item, index) in hiddenItems"
                            :key="`hidden-${item.key}-${index}`"
                            @click="selectButton(item)"
                            :class="selectedMenu === item.key ? 'bg-blue-50 text-blue-600' : ''"
                        >
                            <div class="flex items-center space-x-2">
                                <span>{{ item.label }}</span>
                                <font-awesome-icon
                                    v-if="selectedMenu === item.key"
                                    icon="fa-solid fa-check"
                                    class="text-blue-600 text-sm"
                                />
                            </div>
                        </a-menu-item>
                    </a-menu>
                </template>
            </a-dropdown>
        </div>

        <!-- Right Section: Icons + User -->
        <div class="flex items-center space-x-3 flex-shrink-0">
            <div class="flex items-center gap-2 flex-shrink-0">
                <a-tooltip title="Panier">
                    <a-button
                        type="text"
                        class="group flex items-center justify-center text-white hover:!text-secondary hover:!bg-white/5 border-white/20 hover:border-white/50 rounded-md transition-all duration-500 ease-in-out !p-3 !h-9 !w-9"
                    >
                        <font-awesome-icon
                            icon="fa-solid fa-shopping-cart"
                            class="text-[16px] group-hover:scale-110 transition-transform duration-300 group-hover:drop-shadow-lg"
                        />
                    </a-button>
                </a-tooltip>

                <NotificationBell />
            </div>

            <!-- Séparateur -->
            <div class="h-5 w-px bg-white/20"></div>

            <!-- Mobile version: simple avatar button -->
            <div class="block md:hidden">
                <a-dropdown placement="bottomRight" trigger="click" class="relative" arrow>
                    <template #overlay>
                        <a-menu class="w-56 !rounded-none shadow-lg border-0 overflow-hidden">
                            <div
                                class="px-4 py-3 bg-gradient-to-r from-[#172A6C] to-secondary rounded-b-3xl border-b border-gray-100">
                                <p class="text-sm text-gray-300">Connecté en tant que</p>
                                <p class="text-sm font-medium text-gray-50">
                                    {{ user?.name || "Utilisateur" }}
                                </p>
                            </div>

                            <a-menu-item key="0" class="group hover:bg-gray-50 py-3">
                                <Link class="flex items-center space-x-3">
                                    <font-awesome-icon
                                        icon="fa-solid fa-circle-user"
                                        class="me-2 text-gray-600 group-hover:text-secondary"
                                    />
                                    <span class="text-gray-700 group-hover:text-secondary">Mon Profil</span>
                                </Link>
                            </a-menu-item>

                            <a-menu-divider class="my-1"/>

                            <a-menu-item key="2" class="group text-sm hover:bg-red-50 py-3">
                                <Link :href="isAdmin ? route('admin.logout') : route('logout')" method="post" as="button">
                                    <font-awesome-icon
                                        class="me-1 group-hover:text-secondary"
                                        icon="fa-solid fa-right-from-bracket"
                                    />
                                    <span class="text-gray-700 group-hover:text-secondary">Déconnexion</span>
                                </Link>
                            </a-menu-item>
                        </a-menu>
                    </template>

                    <a-avatar
                        :src="user.thumb_img ?? 'https://i.pravatar.cc/40'"
                        :size="40"
                        class="border border-white/70 shadow-lg hover:ring-2 hover:ring-secondary transition-all duration-300 cursor-pointer"
                    />
                </a-dropdown>
            </div>

            <!-- Desktop version: dropdown complet -->
            <div class="hidden lg:block">
                <a-dropdown placement="bottomRight" trigger="click" class="relative" arrow>
                    <template #overlay>
                        <a-menu class="w-56 !rounded-none shadow-lg border-0 overflow-hidden">
                            <div
                                class="px-4 py-3 mb-4 bg-gradient-to-r from-[#172A6C] to-secondary rounded-b-3xl border-b border-gray-100">
                                <p class="text-sm text-gray-300">Connecté en tant que</p>
                                <p class="text-sm font-medium text-gray-50">
                                    {{ user?.name || "Utilisateur" }}
                                </p>
                            </div>

                            <a-menu-item key="0" class="group hover:bg-gray-50 py-3">
                                <Link class="flex items-center space-x-2">
                                    <font-awesome-icon
                                        icon="fa-solid fa-circle-user"
                                        class="text-gray-600 group-hover:text-secondary"
                                    />
                                    <span class="text-gray-700 group-hover:text-secondary">Mon Profil</span>
                                </Link>
                            </a-menu-item>

                            <a-menu-divider class="my-1"/>

                            <a-menu-item key="2" class="group text-sm hover:bg-red-50 py-3">
                                <Link :href="isAdmin ? route('admin.logout') : route('logout')" method="post" as="button">
                                    <font-awesome-icon
                                        class="me-1 group-hover:text-secondary"
                                        icon="fa-solid fa-right-from-bracket"
                                    />
                                    <span class="text-gray-700 group-hover:text-secondary">Déconnexion</span>
                                </Link>
                            </a-menu-item>
                        </a-menu>
                    </template>

                    <div class="flex items-center gap-2 px-2.5 py-1 rounded-lg backdrop-blur-sm border border-white/30 hover:border-secondary hover:bg-white/10 transition-all duration-200 cursor-pointer group">
                        <a-avatar
                            :src="user?.thumb_img ?? 'https://i.pravatar.cc/40'"
                            :size="28"
                            class="border-2 border-white/50 flex-shrink-0"
                        />
                        <div class="hidden lg:flex flex-col max-w-24">
                            <span class="font-medium text-white leading-tight text-sm truncate group-hover:text-secondary transition-colors duration-200">
                                {{ user.name }}
                            </span>
                            <span class="text-[11px] text-white/70 truncate">
                                {{ user.email }}
                            </span>
                        </div>
                        <font-awesome-icon
                            icon="fa-solid fa-chevron-down"
                            class="text-white/40 group-hover:text-secondary text-[10px] transition-all duration-200"
                        />
                    </div>
                </a-dropdown>
            </div>
        </div>
    </a-layout-header>
</template>

<style scoped>
/* Animation pour le logo */
@keyframes pulse-glow {
    0%, 100% {
        box-shadow: 0 0 0 0 rgba(220, 38, 38, 0.4);
    }
    50% {
        box-shadow: 0 0 0 4px rgba(220, 38, 38, 0.1);
    }
}

.logo-container:hover {
    animation: pulse-glow 2s infinite;
}

/* Transitions fluides */
.transition-sidebar {
    transition: margin-left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Effet de survol pour les boutons */
.hover-lift:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

</style>


