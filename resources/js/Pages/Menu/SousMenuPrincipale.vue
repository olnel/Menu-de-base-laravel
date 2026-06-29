<script setup>
import {computed, defineProps, onBeforeUnmount, onMounted, ref, watch} from 'vue';
import {router, usePage} from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";
import {getMenuData} from "../../../Utils/MenuData.js";

const props = defineProps({
    title: {
        type: String,
        required: true,
    },
    description: {
        type: String,
        default: 'Sélectionnez une option pour continuer',
    },
    selectedMenu: {
        type: String,
        required: true,
    },
    backTo: {
        type: String,
        default: null,
    },
});

const tenantModules = computed(() => usePage().props.auth?.tenant?.modules ?? [])
const isDNA = computed(() => usePage().props.auth?.user?.is_dna ?? false)

const currentMenu = ref({});
const isModalOpen = ref(false);
const currentPage = ref(1);
const itemsPerPage = 9;
const slideDirection = ref('next');
const searchQuery = ref('');

// Appel initial lors du montage
onMounted(() => {
    currentMenu.value = getMenuData().find(menu => menu.children?.some(child => child.key === props.selectedMenu));

    // Ajouter un gestionnaire pour fermer le modal en appuyant sur Escape
    document.addEventListener('keydown', handleKeyDown);
});

// Obtenir la clé du menu sélectionné pour le breadcrumb
const selectedMenuKey = computed(() => {
    return props.selectedMenu;
});

// Filtrage des boutons du menu par module (plan) puis par recherche
const filteredMenuButtons = computed(() => {
    const buttons = (currentMenu.value?.children ?? []).filter(btn => {
        if (btn.dnaOnly && !isDNA.value) return false;
        if (!btn.module) return true;
        return tenantModules.value.includes(btn.module);
    });

    if (!searchQuery.value) return buttons;
    return buttons.filter(btn =>
        btn.label.toLowerCase().includes(searchQuery.value.toLowerCase())
    );
});

// Obtenir tous les boutons du menu (filtrés)
const allMenuButtons = computed(() => {
    return filteredMenuButtons.value;
});

// Reset pagination when search changes
watch(searchQuery, () => {
    currentPage.value = 1;
});

onBeforeUnmount(() => {
    document.removeEventListener('keydown', handleKeyDown);
});

const handleKeyDown = (e) => {
    if (e.key === 'Escape' && isModalOpen.value) {
        closeModal();
    }
};

// Toggle du modal avec animation
const toggleModal = () => {
    isModalOpen.value = !isModalOpen.value;
    currentPage.value = 1;
    if (isModalOpen.value) {
        document.body.style.overflow = 'hidden'; // Empêcher le défilement du body
    } else {
        document.body.style.overflow = ''; // Restaurer le défilement
    }
};

// Fermer le modal
const closeModal = () => {
    isModalOpen.value = false;
    document.body.style.overflow = ''; // Restaurer le défilement
};

// Navigation vers une route (gère les URLs cross-domain pour les routes du domaine central)
const navigateTo = (smenu) => {
    try {
        const url = route(smenu.routeName);
        const isCrossDomain = url.startsWith('http') && !url.startsWith(window.location.origin);
        if (isCrossDomain) {
            window.location.href = url;
        } else {
            router.get(url);
        }
    } catch (e) {
        console.warn(`Navigation impossible vers "${smenu.routeName}": ${e.message}`);
    }
    closeModal();
};

// Pagination
const totalPages = computed(() => {
    return Math.ceil(allMenuButtons.value.length / itemsPerPage);
});

const paginatedMenuButtons = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage;
    const end = start + itemsPerPage;
    return allMenuButtons.value.slice(start, end);
});

const prevPage = () => {
    if (currentPage.value > 1) {
        slideDirection.value = 'prev';
        currentPage.value--;
    }
};

const nextPage = () => {
    if (currentPage.value < totalPages.value) {
        slideDirection.value = 'next';
        currentPage.value++;
    }
};

const goToPage = (page) => {
    if (page !== currentPage.value) {
        slideDirection.value = page > currentPage.value ? 'next' : 'prev';
        currentPage.value = page;
    }
};

const canGoToPrevious = computed(() => currentPage.value > 1);
const canGoToNext = computed(() => currentPage.value < totalPages.value);
</script>

<template>
    <AuthenticatedLayout :title="title" :pageKey="selectedMenuKey" :backTo="backTo">
        <div class="overflow-hidden flex flex-col gap-2">

            <div class="flex-1 flex flex-col">
                <transition name="slide-fade" appear>
                    <div class="mb-0">
                        <div v-if="$slots.top" class="mb-2">
                            <slot name="top"/>
                        </div>
                        <div class="rounded-md overflow-hidden" style="min-height: calc(100vh - 200px)">
                            <slot></slot>
                        </div>
                    </div>
                </transition>
            </div>

            <!-- Barre de navigation du bas avec l'icône au centre -->
            <div class="fixed bottom-0 left-0 right-0 h-20 z-50 flex items-center justify-center" v-if="$page.props.auth.user">
                <div class="flex justify-center items-center -mt-16">
                    <div
                        class="relative menu-button-container bg-gradient-to-t from-blue-800 to-blue-500/30 rounded-full p-1
                        before:content-[''] before:block before:bg-gray-100 before:rounded-full before:absolute before:inset-0 before:m-auto before:-z-10 before:scale-125
                    ">
                        <button type="button"
                            @click="toggleModal"
                            class="nav-button bg-gradient-to-t from-primary via-primary/70 to-primary rounded-full p-2 hover:scale-105 transition-transform duration-300 shadow-lg outline-none focus:outline-none"
                        >
                            <svg class="text-white w-5 h-5 lg:w-6 lg:h-6" viewBox="0 0 24 24" fill="currentColor">
                                <rect x="3" y="3" width="8" height="8" rx="1"></rect>
                                <rect x="14" y="3" width="8" height="8" rx="1"></rect>
                                <rect x="3" y="14" width="8" height="8" rx="1"></rect>
                                <rect x="14" y="14" width="8" height="8" rx="1"></rect>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Modal pour les options du menu avec fond flouté -->
            <transition name="modal-fade">
                <div v-if="isModalOpen" class="fixed inset-0 flex items-end justify-center z-50" @click="closeModal">
                    <div class="absolute inset-0 bg-black bg-opacity-60 backdrop-blur-sm"></div>
                    <div class="relative w-full max-w-sm mx-auto px-4 mb-20 pb-4" @click.stop>
                        <transition
                            enter-active-class="transition-all duration-300 ease-out"
                            enter-from-class="transform scale-95 opacity-0"
                            enter-to-class="transform scale-100 opacity-100"
                            leave-active-class="transition-all duration-200 ease-in"
                            leave-from-class="transform scale-100 opacity-100"
                            leave-to-class="transform scale-95 opacity-0"
                        >
                            <div v-if="isModalOpen"
                                 class="bg-gradient-to-b from-white p-1 to-primary rounded-3xl overflow-hidden w-full max-w-sm shadow-2xl border-t-2 border-white">
                                <div class="relative bg-gray-900 rounded-[20px]">

                                    <!-- Indicateur de défilement -->
                                    <div class="flex justify-center mb-4">
                                        <div class="w-14 h-1 bg-white p-0.5 rounded-b-[20px] opacity-100"></div>
                                    </div>

                                    <div class="relative px-6 py-2 text-white">
                                        <div class="flex flex-col space-y-3">
                                            <div class="flex justify-between items-center">
                                                <div>
                                                    <h3 class="font-bold text-base mb-1 truncate">{{ title }}</h3>
                                                    <p class="text-xs opacity-80">{{ description }}</p>
                                                </div>

                                                <button type="button"
                                                    @click="closeModal"
                                                    class="bg-transparent hover:bg-white/15 text-white p-2 rounded-full transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-primary"
                                                >
                                                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                        <path
                                                            fill-rule="evenodd"
                                                            d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                                            clip-rule="evenodd"
                                                        />
                                                    </svg>
                                                </button>
                                            </div>

                                            <!-- Barre de recherche -->
                                            <div class="relative group">
                                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                    <font-awesome-icon icon="fa-solid fa-search" class="text-gray-400 group-focus-within:text-white transition-colors" />
                                                </div>
                                                <input
                                                    v-model="searchQuery"
                                                    type="text"
                                                    ref="searchInput"
                                                    class="block w-full pl-10 pr-10 py-2.5 border border-transparent rounded-xl leading-5 bg-white/10 text-white placeholder-gray-400 focus:outline-none focus:bg-white/20 focus:border-white focus:ring focus:ring-white/50 sm:text-sm transition-all duration-200"
                                                    placeholder="Rechercher une option..."
                                                >
                                                <button type="button"
                                                    v-if="searchQuery"
                                                    @click="searchQuery = ''"
                                                    class="absolute inset-y-0 right-0 pr-3 flex items-center cursor-pointer text-gray-400 hover:text-white transition-colors focus:outline-none"
                                                >
                                                    <font-awesome-icon icon="fa-solid fa-times-circle" />
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Contenu Grille avec navigation améliorée -->
                                    <div class="relative px-3 mt-2">
                                        <div class="overflow-hidden rounded-2xl min-h-[200px]">
                                            <transition :name="slideDirection === 'next' ? 'slide-left' : 'slide-right'" mode="out-in">
                                                <div :key="currentPage" class="grid grid-cols-3 gap-2 p-2">
                                                            <div
                                                                v-for="(smenu, index) in paginatedMenuButtons"
                                                                :key="index"
                                                                class="group relative flex flex-col items-center justify-center p-4 rounded-lg hover:bg-primary/80 cursor-pointer transition-all duration-500 hover:scale-105 hover:-translate-y-0.5 overflow-hidden"
                                                                :class="{
                                                                'bg-primary/80': smenu.key === selectedMenuKey,
                                                                'bg-black/80': smenu.key !== selectedMenuKey
                                                            }"
                                                                :style="{ animationDelay: `${index * 0.05}s` }"
                                                                @click="navigateTo(smenu)"
                                                            >
                                                                <!-- Content -->
                                                                <div class="relative z-10 flex flex-col items-center">
                                                                    <font-awesome-icon
                                                                        :icon="smenu.icon"
                                                                        class="text-lg mb-2 text-white transition-transform duration-500 group-hover:scale-110 group-hover:rotate-6"
                                                                    />
                                                                    <span class="font-normal text-center text-[11px] text-white leading-tight break-words w-full">
                                                                    {{ smenu.label }}
                                                                </span>
                                                                </div>

                                                        <!-- Decorative corner accent -->
                                                        <div class="absolute top-0 right-0 w-16 h-16 bg-white/5 rounded-bl-full transform translate-x-8 -translate-y-8 group-hover:translate-x-0 group-hover:translate-y-0 transition-transform duration-500"></div>
                                                    </div>

<!--                                                    &lt;!&ndash; Message si aucun résultat &ndash;&gt;-->
<!--                                                    <div v-if="filteredMenuItems.length === 0" class="col-span-3 flex flex-col items-center justify-center py-10 text-white/60">-->
<!--                                                        <font-awesome-icon icon="fa-solid fa-ghost" class="text-4xl mb-3 opacity-50" />-->
<!--                                                        <p class="text-sm">Aucun résultat trouvé pour "{{ searchQuery }}"</p>-->
<!--                                                    </div>-->
                                                </div>
                                            </transition>
                                        </div>
                                    </div>

                                    <!-- Pagination dots améliorés -->
                                    <div v-if="totalPages > 1" class="flex justify-center items-center pb-6 pt-2 px-5 space-x-2">
                                        <!-- Navigation Gauche -->
                                        <div class="w-8 h-8">
                                            <button type="button"
                                                v-if="totalPages > 1 && canGoToPrevious"
                                                @click="prevPage"
                                                class="w-full h-full flex justify-center items-center bg-white/15 hover:bg-primary/90 rounded-full text-white transition-all duration-300 focus:outline-none"
                                            >
                                                <font-awesome-icon icon="fa-solid fa-chevron-left" class="h-4 w-4"/>
                                            </button>
                                        </div>

                                        <div class="flex-1 flex justify-center items-center space-x-2">
                                            <button type="button"
                                                v-for="page in totalPages"
                                                :key="page"
                                                @click="goToPage(page)"
                                                class="transition-all duration-300 rounded-full focus:outline-none focus:ring-2 focus:ring-white/50"
                                                :class="[
                                                    currentPage === page
                                                        ? 'w-8 h-1 bg-white shadow-lg'
                                                        : 'w-3 h-1 bg-white/40 hover:bg-white/60'
                                                ]"
                                                :aria-label="`Page ${page}`"
                                                :aria-current="currentPage === page ? 'page' : undefined"
                                            ></button>
                                        </div>

                                        <!-- Navigation Droite -->
                                        <div class="w-8 h-8">
                                            <button type="button"
                                                v-if="totalPages > 1 && canGoToNext"
                                                @click="nextPage"
                                                class="w-full h-full flex justify-center items-center bg-white/15 hover:bg-primary/90 rounded-full p-3 text-white transition-all duration-300 focus:outline-none"
                                            >
                                                <font-awesome-icon icon="fa-solid fa-chevron-right" class="h-4 w-4"/>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </transition>
                    </div>
                </div>
            </transition>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
/* ===== Slide Fade ===== */
.slide-fade-enter-active,
.slide-fade-leave-active {
    @apply transition-all duration-[600ms] ease-[cubic-bezier(0.22,1,0.36,1)];
}

.slide-fade-enter-from,
.slide-fade-leave-to {
    @apply opacity-0 translate-x-[100px];
}

/* ===== Menu Button Container ===== */
.menu-button-container {
    @apply relative top-[18px] shadow-[0_4px_15px_rgba(0,0,0,0.2)];
}

/* ===== Nav Button ===== */
.nav-button {
    @apply transition-all duration-300 ease-in-out;
}

.nav-button:active {
    @apply scale-95;
}

/* ===== Modal Fade ===== */
.modal-fade-enter-active {
    @apply transition-opacity duration-200 ease-in-out;
}

.modal-fade-leave-active {
    @apply transition-opacity duration-300 ease-in-out;
}

.modal-fade-enter-from,
.modal-fade-leave-to {
    @apply opacity-0;
}

/* ===== Slide Up ===== */
.slide-up-enter-active {
    @apply transition-transform duration-[250ms] ease-[cubic-bezier(0.16,1,0.3,1)];
}

.slide-up-leave-active {
    @apply transition-transform duration-[450ms] ease-[cubic-bezier(0.16,1,0.3,1)];
}

.slide-up-enter-from,
.slide-up-leave-to {
    @apply translate-y-full;
}

/* ===== Icon Container ===== */
.icon-container {
    @apply bg-white/10 rounded-full w-[44px] h-[44px] flex items-center justify-center transition-all duration-300 ease-in-out;
}

/* ===== Menu Item ===== */
.menu-item {
    @apply bg-white/5 relative overflow-hidden transition-all duration-300 ease-in-out;
}

.menu-item:hover {
    @apply bg-white/10 -translate-y-[3px];
}

.menu-item:hover .icon-container {
    @apply bg-white/20;
}

/* ===== Active Item ===== */
.menu-item-active {
    @apply bg-[linear-gradient(145deg,rgba(139,92,246,0.3),rgba(79,70,229,0.3))]
    shadow-[0_4px_12px_rgba(139,92,246,0.2)];
}

.menu-item-active .icon-container {
    @apply bg-white/20;
}

/* ===== Inactive Item ===== */
.menu-item-inactive {
    @apply opacity-80;
}

/* ===== Slide Left / Right ===== */
.slide-left-enter-active,
.slide-left-leave-active,
.slide-right-enter-active,
.slide-right-leave-active {
    @apply transition-all duration-300 ease-in-out;
}

/* Left */
.slide-left-enter-from {
    @apply opacity-0 translate-x-[30px];
}

.slide-left-leave-to {
    @apply opacity-0 -translate-x-[30px];
}

/* Right */
.slide-right-enter-from {
    @apply opacity-0 -translate-x-[30px];
}

.slide-right-leave-to {
    @apply opacity-0 translate-x-[30px];
}

/* ===== Pagination Button Animation ===== */
button[aria-current="page"] {
    @apply animate-pulse-light;
}

@keyframes pulse-light {
    0%, 100% {
        box-shadow: 0 0 0 0 rgba(255, 255, 255, 0.4);
    }
    50% {
        box-shadow: 0 0 0 4px rgba(255, 255, 255, 0);
    }
}

.animate-pulse-light {
    animation: pulse-light 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}
</style>
