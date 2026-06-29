<script setup>
import { router, usePage } from "@inertiajs/vue3";
import { computed, defineProps, ref } from "vue";
import MonCompteModal from "@/Pages/Portail/Components/MonCompteModal.vue";

const client = computed(() => usePage().props.portail_client);

const props = defineProps({
    title:    String,
    subtitle: String,
    selectedMenu: {
        type: String,
        default: ''
    }
})

const clientInitials = computed(() => {
    const name = client.value?.nom_client || '';
    const parts = name.trim().split(/\s+/);
    if (parts.length >= 2) return (parts[0][0] + parts[1][0]).toUpperCase();
    return (name.slice(0, 2) || 'CL').toUpperCase();
});

function logout() {
    router.post(route('portail.logout'));
}

const showMonCompte = ref(false);
const openMonCompte = () => {
    showMonCompte.value = true;
};
</script>

<template>
    <a-layout-header
        class="sticky top-0 z-50 h-14 bg-gradient-to-r from-[#172A6C] to-primary border-b border-white/10 flex justify-between items-center px-8 shadow-sm"
    >
        <!-- Left: Logo + Home + séparateur -->
        <div class="flex items-center gap-3 flex-shrink-0">
            <a-button
                type="text"
                size="large"
                class="group flex items-center justify-center bg-white/10 text-white hover:!text-secondary hover:!bg-white/5 border-white/20 hover:border-white/50 !rounded-md transition-all duration-500 ease-in-out p-2 h-10 w-10"
                @click="router.get(route('portail.menu'))"
            >
                <div class="relative w-4 h-4 flex items-center justify-center">
                    <font-awesome-icon
                        icon="fa-home"
                        class="!me-0 text-xl text-white group-hover:text-secondary transition-all duration-300"
                    />
                </div>
            </a-button>

            <div class="h-6 w-px bg-white/20 hidden lg:block"></div>

            <!-- Logo moderne en ligne avec SVG -->
            <div class="hidden sm:flex flex-col -mt-1">
                <span class="text-white font-['Arial'] !font-extrabold text-base lg:text-3xl leading-tight flex items-center">
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
                    Espace Client
                </span>
            </div>

            <div v-if="title" class="flex flex-col leading-tight ml-2">
                <span class="text-white font-semibold text-sm lg:text-base truncate">{{ title }}</span>
                <span v-if="subtitle" class="text-white/70 text-xs truncate">{{ subtitle }}</span>
            </div>
        </div>

        <!-- Right Section: Icons + User -->
        <div class="flex items-center space-x-3 flex-shrink-0">
            <!-- Séparateur -->
            <div class="h-5 w-px bg-white/20"></div>

            <!-- Mobile version: simple avatar button -->
            <div class="block md:hidden">
                <a-dropdown placement="bottomRight" :trigger="['click']" class="relative" arrow>
                    <template #overlay>
                        <a-menu class="w-56 !rounded-none shadow-lg border-0 overflow-hidden">
                            <div class="px-4 py-3 bg-gradient-to-r from-[#172A6C] to-secondary rounded-b-3xl border-b border-gray-100">
                                <p class="text-sm text-gray-300">Espace Client</p>
                                <p class="text-sm font-medium text-gray-50">{{ client?.nom_client || 'Client' }}</p>
                            </div>
                            <a-menu-item key="compte" class="group text-sm hover:bg-blue-50 py-3" @click="openMonCompte">
                                <font-awesome-icon class="me-1 group-hover:text-primary" icon="fa-solid fa-user-pen" />
                                <span class="text-gray-700 group-hover:text-primary">Mon compte</span>
                            </a-menu-item>
                            <a-menu-item key="logout" class="group text-sm hover:bg-red-50 py-3" @click="logout">
                                <font-awesome-icon class="me-1 group-hover:text-secondary" icon="fa-solid fa-right-from-bracket" />
                                <span class="text-gray-700 group-hover:text-secondary">Déconnexion</span>
                            </a-menu-item>
                        </a-menu>
                    </template>
                    <a-avatar
                        :size="40"
                        class="border border-white/70 shadow-lg hover:ring-2 hover:ring-secondary transition-all duration-300 cursor-pointer !bg-secondary !text-white font-bold"
                    >{{ clientInitials }}</a-avatar>
                </a-dropdown>
            </div>

            <!-- Desktop version: dropdown complet -->
            <div class="hidden lg:block">
                <a-dropdown placement="bottomRight" :trigger="['click']" class="relative" arrow>
                    <template #overlay>
                        <a-menu class="w-56 !rounded-none shadow-lg border-0 overflow-hidden">
                            <div class="px-4 py-3 mb-4 bg-gradient-to-r from-[#172A6C] to-secondary rounded-b-3xl border-b border-gray-100">
                                <p class="text-sm text-gray-300">Espace Client</p>
                                <p class="text-sm font-medium text-gray-50">{{ client?.nom_client || 'Client' }}</p>
                            </div>
                            <a-menu-item key="compte" class="group text-sm hover:bg-blue-50 py-3" @click="openMonCompte">
                                <font-awesome-icon class="me-1 group-hover:text-primary" icon="fa-solid fa-user-pen" />
                                <span class="text-gray-700 group-hover:text-primary">Mon compte</span>
                            </a-menu-item>
                            <a-menu-item key="logout" class="group text-sm hover:bg-red-50 py-3" @click="logout">
                                <font-awesome-icon class="me-1 group-hover:text-secondary" icon="fa-solid fa-right-from-bracket" />
                                <span class="text-gray-700 group-hover:text-secondary">Déconnexion</span>
                            </a-menu-item>
                        </a-menu>
                    </template>

                    <div class="flex items-center gap-2 px-2.5 py-1 rounded-lg backdrop-blur-sm border border-white/30 hover:border-secondary hover:bg-white/10 transition-all duration-200 cursor-pointer group">
                        <a-avatar
                            :size="28"
                            class="border-2 border-white/50 flex-shrink-0 !bg-secondary !text-white font-bold text-[10px]"
                        >{{ clientInitials }}</a-avatar>
                        <div class="hidden lg:flex flex-col max-w-24">
                            <span class="font-medium text-white leading-tight text-sm truncate group-hover:text-secondary transition-colors duration-200">
                                {{ client?.nom_client || 'Client' }}
                            </span>
                            <span class="text-[11px] text-white/70 truncate">
                                {{ client?.login || '' }}
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

    <MonCompteModal v-model:open="showMonCompte" />
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

@keyframes truck-drive {
    0%, 100% { transform: translateX(0); }
    50%       { transform: translateX(3px); }
}
.truck-move { animation: truck-drive 2s ease-in-out infinite; }

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
