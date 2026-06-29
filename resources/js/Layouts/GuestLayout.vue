<script setup>
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import Layout from "@/Layouts/Layout.vue";
import { CalendarOutlined, CarOutlined } from "@ant-design/icons-vue";
import { Link } from "@inertiajs/vue3";
</script>

<template>
    <layout>
        <div
            class="min-h-screen bg-cover bg-center bg-no-repeat relative overflow-hidden"
            style="background-image: url(&quot;/img/bg_login.png&quot;)"
        >
            <!-- Overlay animé avec gradient -->
            <!--            <div class="absolute inset-0 bg-gradient-to-br from-blue-950/50 via-black/50 to-yellow-950/60 animate-pulse duration-[3000ms]"></div>-->

            <!-- Particules flottantes -->
            <div class="absolute inset-0 overflow-hidden pointer-events-none">
                <div
                    class="absolute top-1/4 left-1/4 w-2 h-2 bg-white/20 rounded-full animate-bounce duration-[2000ms]"
                ></div>
                <div
                    class="absolute top-1/3 right-1/3 w-1 h-1 bg-blue-300/30 rounded-full animate-ping duration-[3000ms]"
                ></div>
                <div
                    class="absolute bottom-1/4 left-1/3 w-3 h-3 bg-white/10 rounded-full animate-pulse duration-[4000ms]"
                ></div>
                <div
                    class="absolute top-1/2 right-1/4 w-1.5 h-1.5 bg-blue-200/25 rounded-full animate-bounce duration-[2500ms]"
                ></div>
            </div>

            <!-- Contenu principal -->
            <div
                class="container mx-auto relative z-10 min-h-screen flex items-center justify-between px-8"
            >
                <!-- Section gauche - Informations avec animations -->
                <div
                    class="hidden lg:flex lg:w-2/4 items-center justify-center relative text-white"
                >
                    <div class="space-y-8 animate-fade-in-up">
                        <h1
                            class="text-5xl lg:text-7xl font-bold mb-6 leading-tight bg-gradient-to-r from-white via-blue-100 to-white bg-clip-text text-transparent animate-slide-in-left"
                        >
                            Transport<br />
                            <span
                                class="text-transparent bg-gradient-to-r from-secondary to-cyan-300 bg-clip-text animate-pulse"
                            >
                                Management </span
                            ><br />
                            System
                        </h1>

                        <p
                            class="text-xl lg:text-2xl mb-8 leading-relaxed opacity-90 animate-slide-in-left delay-300"
                        >
                            <span class="text-secondary font-semibold"
                                >Pour une logistique professionnelle et
                                maîtrisée.</span
                            >
                        </p>

                        <!-- Nouvelle section de texte stylée -->
                        <div
                            class="mt-8 animate-slide-in-left delay-700 space-y-4"
                        >
                            <p
                                class="text-md lg:text-lg text-white opacity-80 leading-relaxed"
                            >
                                Notre
                                <span class="text-secondary font-semibold">
                                    logiciel de gestion du transport (TMS)
                                </span>
                                vous offre une solution complète pour
                                <span>planifier, suivre et optimiser</span>
                                l’ensemble de vos opérations de transport. Que
                                vous soyez un transporteur, une entreprise de
                                distribution ou un logisticien, notre plateforme
                                s’adapte à vos besoins pour vous faire gagner du
                                temps, réduire vos coûts et garantir une
                                traçabilité totale.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Section droite - Formulaire de connexion avec animations -->
                <div class="w-full lg:max-w-md ml-auto animate-slide-in-right">
                    <div
                        class="bg-white/95 backdrop-blur-sm rounded-2xl shadow-2xl p-8 transform transition-all duration-500 hover:shadow-3xl hover:scale-[1.02] border border-white/20"
                    >
                        <!-- Logo avec animation -->
                        <div class="mb-8 text-center animate-fade-in-down">
                            <Link href="/" class="group">
                                <div
                                    class="mx-auto transform transition-all duration-300"
                                >
                                    <ApplicationLogo />
                                </div>
                            </Link>
                        </div>

                        <!-- Contenu du slot avec animation -->
                        <div class="animate-fade-in-up delay-200">
                            <slot />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section des avantages en bas avec animations améliorées -->
            <div
                class="absolute bottom-0 left-0 right-0 z-10 animate-slide-in-up"
            >
                <div
                    class="bg-gradient-to-r from-blue-950/70 via-blue-900/60 to-blue-950/70 backdrop-blur-sm border-t border-white/10"
                >
                    <div class="container mx-auto px-8 lg:px-16 py-8">
                        <div
                            class="flex items-center justify-between gap-8 text-white"
                        >
                            <!-- Avantage 1 -->
                            <div
                                class="flex items-center space-x-4 group cursor-pointer transform transition-all duration-300 hover:scale-105 animate-fade-in-up delay-100"
                            >
                                <div
                                    class="flex-shrink-0 p-3 bg-white/10 rounded-full group-hover:bg-blue-500/20 transition-all duration-300"
                                >
                                    <svg
                                        viewBox="64 64 896 896"
                                        focusable="false"
                                        class="w-8 h-8 text-blue-300 group-hover:text-secondary transition-colors duration-300"
                                        fill="currentColor"
                                        xmlns="http://www.w3.org/2000/svg"
                                    >
                                        <path
                                            d="M976 432h-88v-56c0-4.4-3.6-8-8-8H728c-4.4 0-8 3.6-8 8v160H144c-4.4 0-8 3.6-8 8v160c0 4.4 3.6 8 8 8h48.4c8.1 42.3 45 74.4 89.6 74.4s81.5-32.1 89.6-74.4h348.8c8.1 42.3 45 74.4 89.6 74.4s81.5-32.1 89.6-74.4H976c4.4 0 8-3.6 8-8V440c0-4.4-3.6-8-8-8zM728 432h152v48H728v-48zM282 752c-30.9 0-56-25.1-56-56s25.1-56 56-56 56 25.1 56 56-25.1 56-56 56zm528 0c-30.9 0-56-25.1-56-56s25.1-56 56-56 56 25.1 56 56-25.1 56-56 56z"
                                        />
                                    </svg>
                                </div>
                                <div class="hidden lg:block">
                                    <h3
                                        class="text-lg font-semibold group-hover:text-secondary transition-colors duration-300"
                                    >
                                        Gestion de flotte
                                    </h3>
                                    <p class="text-sm opacity-90">
                                        Suivi des véhicules, entretiens et
                                        affectations en temps réel.
                                    </p>
                                </div>
                            </div>

                            <!-- Avantage 2 -->
                            <div
                                class="flex items-center space-x-4 group cursor-pointer transform transition-all duration-300 hover:scale-105 animate-fade-in-up delay-200"
                            >
                                <div
                                    class="flex-shrink-0 p-3 bg-white/10 rounded-full group-hover:bg-cyan-500/20 transition-all duration-300"
                                >
                                    <CarOutlined
                                        class="text-3xl text-cyan-300 group-hover:text-cyan-200 transition-colors duration-300"
                                    />
                                </div>
                                <div class="hidden lg:block">
                                    <h3
                                        class="text-lg font-semibold group-hover:text-cyan-300 transition-colors duration-300"
                                    >
                                        Planification et livraisons
                                    </h3>
                                    <p class="text-sm opacity-90">
                                        Organisation optimisée des trajets et
                                        suivi complet des livraisons.
                                    </p>
                                </div>
                            </div>

                            <!-- Avantage 3 -->
                            <div
                                class="flex items-center space-x-4 group cursor-pointer transform transition-all duration-300 hover:scale-105 animate-fade-in-up delay-300"
                            >
                                <div
                                    class="flex-shrink-0 p-3 bg-white/10 rounded-full group-hover:bg-green-500/20 transition-all duration-300"
                                >
                                    <font-awesome-icon
                                        :icon="['fas', 'truck-fast']"
                                        class="text-3xl text-green-300 group-hover:text-green-200 transition-colors duration-300"
                                    />
                                </div>
                                <div class="hidden lg:block">
                                    <h3
                                        class="text-lg font-semibold group-hover:text-green-300 transition-colors duration-300"
                                    >
                                        Suivi des conducteurs
                                    </h3>
                                    <p class="text-sm opacity-90">
                                        Affectation, planning et performance des
                                        chauffeurs sous contrôle.
                                    </p>
                                </div>
                            </div>

                            <!-- Avantage 4 -->
                            <div
                                class="flex items-center space-x-4 group cursor-pointer transform transition-all duration-300 hover:scale-105 animate-fade-in-up delay-400"
                            >
                                <div
                                    class="flex-shrink-0 p-3 bg-white/10 rounded-full group-hover:bg-yellow-500/20 transition-all duration-300"
                                >
                                    <CalendarOutlined
                                        class="text-3xl text-yellow-300 group-hover:text-yellow-200 transition-colors duration-300"
                                    />
                                </div>
                                <div class="hidden lg:block">
                                    <h3
                                        class="text-lg font-semibold group-hover:text-yellow-300 transition-colors duration-300"
                                    >
                                        Alertes & reporting
                                    </h3>
                                    <p class="text-sm opacity-90">
                                        Notifications automatiques et tableaux
                                        de bord pour une gestion réactive.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </layout>
</template>

<style scoped>
/* Animations personnalisées */
@keyframes fade-in-up {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes fade-in-down {
    from {
        opacity: 0;
        transform: translateY(-30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes slide-in-left {
    from {
        opacity: 0;
        transform: translateX(-50px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

@keyframes slide-in-right {
    from {
        opacity: 0;
        transform: translateX(50px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

@keyframes slide-in-up {
    from {
        opacity: 0;
        transform: translateY(50px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes counter {
    from {
        opacity: 0;
        transform: scale(0.5);
    }
    to {
        opacity: 1;
        transform: scale(1);
    }
}

.animate-fade-in-up {
    animation: fade-in-up 0.8s ease-out forwards;
}

.animate-fade-in-down {
    animation: fade-in-down 0.6s ease-out forwards;
}

.animate-slide-in-left {
    animation: slide-in-left 0.8s ease-out forwards;
}

.animate-slide-in-right {
    animation: slide-in-right 0.8s ease-out forwards;
}

.animate-slide-in-up {
    animation: slide-in-up 0.6s ease-out forwards;
}

.animate-counter {
    animation: counter 1s ease-out forwards;
}

/* Delays pour animations séquentielles */
.delay-100 {
    animation-delay: 0.1s;
}

.delay-200 {
    animation-delay: 0.2s;
}

.delay-300 {
    animation-delay: 0.3s;
}

.delay-400 {
    animation-delay: 0.4s;
}

.delay-500 {
    animation-delay: 0.5s;
}

.delay-700 {
    animation-delay: 0.7s;
}

/* Styles pour les composants Ant Design */
:deep(.ant-input) {
    border-radius: 8px;
    border-color: #d1d5db;
    font-size: 16px;
}

:deep(.ant-input:focus) {
    border-color: #1f2937;
    box-shadow: 0 0 0 2px rgba(31, 41, 55, 0.1);
}

:deep(.ant-input-password) {
    border-radius: 8px;
}

:deep(.ant-btn-primary) {
    border-radius: 8px;
}

:deep(.ant-form-item-label) {
    font-weight: 500;
    color: #374151;
}
</style>
