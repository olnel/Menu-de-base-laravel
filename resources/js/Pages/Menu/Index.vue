<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { router, usePage } from "@inertiajs/vue3";
import { computed, ref, onMounted } from "vue";
import { useWindowSize } from "@vueuse/core";
import { getMenuData } from "../../../Utils/MenuData.js";
import { filterMenuByPrivileges } from "../../../Utils/MenuFilter.js";

function normalizePrivileges(priv) {
    if (!priv) return [];
    if (Array.isArray(priv)) return priv;
    if (typeof priv === "object") return Object.values(priv);
    return [];
}

const user = usePage().props.auth.user;
const tenantModules = usePage().props.auth.tenant?.modules || [];
const privileges = [
    ...normalizePrivileges(user.accepted_routes),
    ...normalizePrivileges(user.permissions),
];

const ALL_MENU_DATA = filterMenuByPrivileges(getMenuData(), privileges, user.is_dna, tenantModules);

const { width } = useWindowSize();
const currentPage = ref(0);
const itemsPerPage = computed(() => (width.value < 1024 ? 8 : 9));

// Animation state
const isMounted = ref(false);
onMounted(() => {
    setTimeout(() => {
        isMounted.value = true; // Not strictly needed for CSS anims but good for coordination
        document.querySelectorAll('.animate-on-load').forEach((el, i) => {
            setTimeout(() => el.classList.add('is-visible'), i * 100);
        });
    }, 100);
});

const menuPages = computed(() => {
    const pages = [];
    const total = ALL_MENU_DATA.length;
    if (total === 0) return [];
    const ipp = itemsPerPage.value;
    const numPages = Math.ceil(total / ipp);
    for (let i = 0; i < numPages; i++) {
        let start = i * ipp;
        if (start + ipp > total && total > ipp) {
            start = Math.max(0, total - ipp);
        }
        pages.push(ALL_MENU_DATA.slice(start, start + ipp));
    }
    return pages;
});

const totalPages = computed(() => menuPages.value.length);
const visibleItems = computed(() => menuPages.value[currentPage.value] || []);

const nextPage = () => { if (currentPage.value < totalPages.value - 1) currentPage.value++; };
const prevPage = () => { if (currentPage.value > 0) currentPage.value--; };
const goToPage = (page) => { currentPage.value = page; };

const handleMenuClick = (item) => {
    const children = item.children || [];
    const routeName = children.length > 0 ? children[0].routeName : item.routeName;
    if (!routeName) return;
    try {
        const url = route(routeName);
        const isCrossDomain = url.startsWith('http') && !url.startsWith(window.location.origin);
        if (isCrossDomain) {
            window.location.href = url;
        } else {
            router.get(url, {});
        }
    } catch (e) {
        console.warn(`Navigation impossible vers "${routeName}": ${e.message}`);
    }
};

const year = new Date().getFullYear();
</script>

<template>
    <AuthenticatedLayout no-padding>

        <!-- ROOT -->
        <div class="relative min-h-screen flex flex-col overflow-hidden font-sans bg-[#020617]">

            <!-- BG IMAGE + OVERLAY -->
            <div class="absolute inset-0 z-0">
                <div class="absolute inset-0 bg-[url('/img/bg_login.png')] bg-cover bg-center bg-no-repeat opacity-100"></div>
                <div class="absolute inset-0 bg-gradient-to-br from-[#172A6C]/95 via-[#020617]/90 to-transparent"></div>
                <!-- Animated accent circles -->
                <div class="absolute -top-[10%] -left-[10%] w-[40%] h-[40%] bg-blue-600/10 blur-[120px] rounded-full"></div>
                <div class="absolute -bottom-[10%] -right-[10%] w-[40%] h-[40%] bg-purple-600/10 blur-[120px] rounded-full"></div>
            </div>

            <!-- MAIN LAYOUT -->
            <div class="relative z-10 flex flex-1 w-full max-w-screen-2xl mx-auto px-6 lg:px-12">

                <!-- ===== COLONNE GAUCHE (RE-DESIGNED) ===== -->
                <aside class="hidden lg:flex w-[500px] shrink-0 flex-col justify-center py-10 pr-12">

                    <div class="animate-on-load space-y-8">
                        <!-- Branding -->
                        <div class="relative inline-block">
                            <div class="absolute inset-0 bg-blue-500 blur-2xl opacity-20 scale-150"></div>
                            <div
                                class="relative w-16 h-16 rounded-2xl flex items-center justify-center shadow-2xl
                                bg-gradient-to-br from-blue-500 to-purple-600 shadow-indigo-500/40"
                            >
                                <font-awesome-icon icon="fa-solid fa-truck-fast" class="text-3xl text-white" />
                            </div>
                        </div>

                        <!-- Header -->
                        <div>
                            <h1 class="text-4xl font-black text-white tracking-tight leading-[1.1] mb-4">
                                TMS <span class="text-blue-400">DNA</span><br/>
                                <span class="text-2xl font-bold opacity-80">Espace de Pilotage</span>
                            </h1>
                            <p class="text-base leading-relaxed text-blue-200/60 max-w-md space-y-1">
                                Accédez en un clic à l'ensemble des modules de votre écosystème de transport.
                                Supervisez vos opérations en temps réel avec une interface centralisée.
                                Optimisez la gestion des flux, des ressources et des performances logistiques.
                                Bénéficiez d’une expérience fluide, moderne et entièrement sécurisée.
                            </p>
                        </div>

                        <!-- Stats Cards -->
                        <div class="grid grid-cols-1 gap-3">
                            <div class="group p-4 rounded-lg bg-white/[0.03] border border-white/[0.06] hover:bg-white/[0.05] transition-colors">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 rounded-xl flex items-center justify-center bg-blue-500/10 text-blue-400 group-hover:scale-110 transition-transform">
                                        <font-awesome-icon icon="fa-solid fa-layer-group" />
                                    </div>
                                    <div>
                                        <p class="text-xs text-white/30 uppercase tracking-widest font-bold">Modules</p>
                                        <p class="text-lg font-bold text-white">{{ ALL_MENU_DATA.length }} actifs</p>
                                    </div>
                                </div>
                            </div>

                            <div class="group p-4 rounded-lg bg-white/[0.03] border border-white/[0.06] hover:bg-white/[0.05] transition-colors">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 rounded-xl flex items-center justify-center bg-emerald-500/10 text-emerald-400 group-hover:scale-110 transition-transform">
                                        <font-awesome-icon icon="fa-solid fa-calendar-check" />
                                    </div>
                                    <div>
                                        <p class="text-xs text-white/30 uppercase tracking-widest font-bold">Session</p>
                                        <p class="text-lg font-bold text-white">{{ year }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </aside>

                <div class="flex items-center">
                    <div class="h-2/3 w-px bg-white/5"></div>
                </div>

                <!-- ===== COLONNE DROITE (CLEANER & MODERN) ===== -->
                <div class="flex-1 min-w-0 flex flex-col py-8 lg:pl-12">

                    <!-- Grid Container -->
                    <div class="flex-1 relative flex flex-col justify-center">

                        <!-- Page Title (Mobile Only) -->
                        <div class="lg:hidden mb-8">
                            <h1 class="text-3xl font-black text-white">Menu Principal</h1>
                            <div class="h-1 w-12 bg-blue-500 mt-2 rounded-full"></div>
                        </div>

                        <div class="relative">
                            <TransitionGroup
                                tag="div"
                                name="menu-stagger"
                                class="grid grid-cols-2 lg:grid-cols-3 gap-2 sm:gap-4 w-full"
                            >
                                <div
                                    v-for="(item) in visibleItems"
                                    :key="item.key"
                                    class="menu-card group relative aspect-[4/3] sm:aspect-square lg:aspect-video rounded-lg cursor-pointer overflow-hidden border border-white/10"
                                    @click="handleMenuClick(item)"
                                >
                                    <!-- Dynamic Gradient Background -->
                                    <div
                                        class="absolute inset-0 transition-transform duration-700 group-hover:scale-110"
                                        :style="{
                                            background: `linear-gradient(135deg, ${item.gradient?.[0] ?? '#1e293b'}, ${item.gradient?.[1] ?? '#0f172a'})`
                                        }"
                                    ></div>

                                    <!-- Patterns/Noise -->
                                    <div class="absolute inset-0 opacity-20 mix-blend-overlay bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')]"></div>

                                    <!-- Glass & Hover Effects -->
                                    <div class="absolute inset-0 bg-black/20 group-hover:bg-transparent transition-colors duration-500"></div>
                                    <div class="absolute inset-0 opacity-0 group-hover:opacity-100 transition-opacity duration-500 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>

                                    <!-- Content -->
                                    <div class="relative h-full flex flex-col items-center justify-center p-6 text-center">
                                        <div class="mb-3 transform transition-all duration-500 group-hover:scale-110 group-hover:-translate-y-1">
                                            <font-awesome-icon
                                                :icon="item.icon"
                                                class="text-4xl sm:text-5xl text-white drop-shadow-[0_8px_15px_rgba(0,0,0,0.3)]"
                                            />
                                        </div>
                                        <h3 class="text-sm sm:text-[12px] text-white tracking-wide uppercase leading-tight group-hover:text-indigo-200 transition-colors">
                                            {{ item.label }}
                                        </h3>
                                    </div>

                                    <!-- Border Highlight -->
                                    <div class="absolute inset-0 border-2 border-white/0 group-hover:border-white/20 rounded-lg transition-all duration-500 scale-95 group-hover:scale-100"></div>
                                </div>

                                <!-- Empty states (placeholders) to keep grid stable -->
                                <div
                                    v-for="n in (itemsPerPage - visibleItems.length)"
                                    :key="'empty-' + n"
                                    class="hidden lg:block aspect-video rounded-3xl border border-white/[0.02] bg-white/[0.01]"
                                ></div>
                            </TransitionGroup>
                        </div>

                        <!-- PAGINATION (MODERN & ELEVATED) -->
                        <div v-if="totalPages > 1" class="flex items-center justify-center gap-8 mt-12 animate-on-load">

                            <button
                                :disabled="currentPage === 0"
                                @click="prevPage"
                                class="nav-btn group"
                            >
                                <font-awesome-icon icon="fa-solid fa-arrow-left" class="group-hover:-translate-x-1 transition-transform" />
                            </button>

                            <div class="flex items-center gap-3">
                                <button
                                    v-for="(_, idx) in menuPages"
                                    :key="idx"
                                    @click="goToPage(idx)"
                                    class="relative h-1.5 transition-all duration-500 rounded-full"
                                    :class="currentPage === idx ? 'w-10 bg-blue-500 shadow-[0_0_15px_rgba(99,102,241,0.6)]' : 'w-4 bg-white/10 hover:bg-white/30'"
                                >
                                </button>
                            </div>

                            <button
                                :disabled="currentPage >= totalPages - 1"
                                @click="nextPage"
                                class="nav-btn group"
                            >
                                <font-awesome-icon icon="fa-solid fa-arrow-right" class="group-hover:translate-x-1 transition-transform" />
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- FOOTER -->
            <footer class="relative z-10 py-6 px-12 border-t border-white/[0.03]">
                <div class="max-w-7xl mx-auto flex justify-between items-center text-[10px] font-bold uppercase tracking-[0.2em] text-white/20">
                    <span>System Version 2.0.4</span>
                    <span>&copy; {{ year }} TMS DNA - All Rights Reserved</span>
                </div>
            </footer>
        </div>

    </AuthenticatedLayout>
</template>

<style scoped>
/* Base Load Animations */
.animate-on-load {
    opacity: 0;
    transform: translateY(20px);
    transition: all 0.8s cubic-bezier(0.22, 1, 0.36, 1);
}

.animate-on-load.is-visible {
    opacity: 1;
    transform: translateY(0);
}

/* Navigation Buttons */
.nav-btn {
    @apply w-10 h-10 rounded-lg flex items-center justify-center text-white/50 border border-white/5 bg-white/[0.02]
           transition-all duration-300 disabled:opacity-10 disabled:cursor-not-allowed hover:bg-indigo-600 hover:text-white hover:border-indigo-400 hover:shadow-[0_10px_20px_rgba(99,102,241,0.3)];
}

/* Staggered Grid Transitions */
.menu-stagger-move {
    transition: all 0.6s cubic-bezier(0.22, 1, 0.36, 1);
}

.menu-stagger-enter-active {
    transition: all 0.5s cubic-bezier(0.22, 1, 0.36, 1);
}

.menu-stagger-leave-active {
    transition: all 0.4s cubic-bezier(0.22, 1, 0.36, 1);
    position: absolute;
    width: calc(33.333% - 1.33rem); /* Grid gap adjustment */
}

.menu-stagger-enter-from {
    opacity: 0;
    transform: scale(0.8) translateY(40px);
}

.menu-stagger-leave-to {
    opacity: 0;
    transform: scale(1.1) translateY(-20px);
}

/* Hover Shine on Cards */
.menu-card::after {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: linear-gradient(45deg, transparent, rgba(255,255,255,0.1), transparent);
    transform: rotate(45deg);
    transition: all 0.7s;
    opacity: 0;
}

.menu-card:hover::after {
    opacity: 1;
    left: 100%;
    top: 100%;
}

@media (max-width: 1024px) {
    .menu-stagger-leave-active {
        width: calc(50% - 1.5rem);
    }
}
</style>


