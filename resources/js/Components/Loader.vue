<template>
    <div v-if="loading"
         class="fixed inset-0 flex items-center justify-center z-[9999] bg-[#020617] font-sans transition-all duration-500 overflow-hidden">

        <!-- BG IMAGE + OVERLAY (Matching Menu/Index.vue) -->
        <div class="absolute inset-0 z-0">
            <div class="absolute inset-0 bg-[url('/img/bg_login.png')] bg-cover bg-center bg-no-repeat opacity-100"></div>
            <div class="absolute inset-0 bg-gradient-to-br from-[#172A6C]/95 via-[#020617]/90 to-[#020617]/50"></div>
            <!-- Animated accent circles -->
            <div class="absolute -top-[10%] -left-[10%] w-[40%] h-[40%] bg-blue-600/10 blur-[120px] rounded-full animate-pulse-slow"></div>
            <div class="absolute -bottom-[10%] -right-[10%] w-[40%] h-[40%] bg-purple-600/10 blur-[120px] rounded-full animate-pulse-slow" style="animation-delay: 2s"></div>
        </div>

        <div class="relative z-10 flex flex-col items-center">
            <!-- Logo "TMS" avec animation de transport -->
            <div class="flex items-center logo-container">
                <!-- "T" -->
                <span class="logo-letter">T</span>

                <!-- Container avec icône de camion animé -->
                <div class="relative inline-block mx-4 truck-bounce" style="--delay: 300ms">
                    <!-- Container avec la couleur secondaire -->
                    <div
                        class="w-20 h-16 flex items-center justify-center rounded-2xl relative overflow-hidden shadow-2xl
                               bg-gradient-to-br from-blue-500 to-indigo-600 shadow-blue-500/20">
                        <!-- Effet brillant qui se déplace -->
                        <div class="shimmer"></div>

                        <!-- SVG du camion avec animation -->
                        <svg class="w-14 h-10 text-white truck-move" viewBox="0 0 24 24" fill="currentColor">
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

                        <!-- Particules de mouvement -->
                        <div class="absolute -left-2 top-1/2 transform -translate-y-1/2">
                            <div class="w-1 h-1 bg-white rounded-full opacity-60 animate-ping"
                                 style="animation-delay: 0ms;"></div>
                        </div>
                    </div>

                    <!-- Route animée sous le camion -->
                    <div class="absolute -bottom-2 left-0 right-0 h-1 bg-white/10 rounded-full overflow-hidden">
                        <div class="road-line"></div>
                    </div>
                </div>

                <!-- "M" -->
                <span class="logo-letter">M</span>

                <!-- "S" -->
                <span class="logo-letter">S</span>
            </div>

            <!-- Texte descriptif -->
            <!-- Indicateur de progression -->
            <div class="mt-6 flex flex-col items-center">
                <p class="text-xs font-bold text-blue-200/40 uppercase tracking-[0.5em] animate-pulse mb-1">
                    Chargement du système
                </p>
                <!-- Barre de progression -->
                <div class="w-72 h-1.5 bg-white/5 rounded-full overflow-hidden mb-8 backdrop-blur-sm border border-white/5">
                    <div class="progress-bar h-full rounded-full bg-gradient-to-r from-blue-500 via-purple-500 to-blue-500 bg-[length:200%_100%]"></div>
                </div>
            </div>

            <!-- Icônes de transport en arrière-plan (plus discrètes) -->
            <div class="absolute inset-0 pointer-events-none overflow-hidden">
                <div class="absolute top-1/4 right-1/4 opacity-[0.03]">
                    <svg class="w-32 h-32 float-icon" viewBox="0 0 24 24" fill="white" style="animation-delay: 0s;">
                        <path d="M2 20h20v-4H2v4zm2-6h16V6H4v8z"/>
                    </svg>
                </div>
                <div class="absolute bottom-1/4 left-1/4 opacity-[0.03]">
                    <svg class="w-32 h-32 float-icon" viewBox="0 0 24 24" fill="white" style="animation-delay: 1.5s;">
                        <path d="M21 16v-2l-8-5V3.5c0-.83-.67-1.5-1.5-1.5S10 2.67 10 3.5V9l-8 5v2l8-2.5V19l-2 1.5V22l3.5-1 3.5 1v-1.5L13 19v-5.5l8 2.5z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { usePage } from '@inertiajs/vue3';
import { onMounted, onUnmounted, ref } from 'vue';

const loading = ref(false);
const page = usePage();

const handleInertiaStart = (e) => {
    const visit = e?.detail?.visit;
    const headers = (visit && visit.headers) || {};
    if (headers['X-Background-Request'] === 'true') {
        return;
    }
    startLoading();
};

const startLoading = () => {
    loading.value = true;
};

const stopLoading = () => {
    setTimeout(() => {
        loading.value = false;
    }, 800); // Légèrement plus long pour apprécier l'animation
};

onMounted(() => {
    document.addEventListener('inertia:start', handleInertiaStart);
    document.addEventListener('inertia:finish', stopLoading);

    document.addEventListener('click', (e) => {
        if (e.target.closest('a[href]:not([download]):not([target="_blank"])') ||
            e.target.closest('button[type="submit"]')) {
            startLoading();
        }
    });
});

onUnmounted(() => {
    document.removeEventListener('inertia:start', handleInertiaStart);
    document.removeEventListener('inertia:finish', stopLoading);
});
</script>

<style scoped>
.logo-container {
    @apply relative;
    filter: drop-shadow(0 0 30px rgba(59, 130, 246, 0.2));
}

.logo-letter {
    @apply inline-block relative text-white font-black text-6xl tracking-tighter;
    text-shadow: 0 0 20px rgba(255, 255, 255, 0.1);
}

.truck-bounce {
    animation: truck-bounce-anim 2s ease-in-out infinite;
    animation-delay: var(--delay);
    transform-origin: center bottom;
}

@keyframes truck-bounce-anim {
    0%, 100% { transform: translateY(0) scale(1); }
    50% { transform: translateY(-10px) scale(1.02); }
}

.truck-move {
    animation: truck-shake 0.8s ease-in-out infinite;
}

@keyframes truck-shake {
    0%, 100% { transform: translateX(0) rotate(0deg); }
    25% { transform: translateX(1px) rotate(1deg); }
    75% { transform: translateX(-1px) rotate(-1deg); }
}

.shimmer {
    @apply absolute top-0 w-full h-full;
    left: -100%;
    background: linear-gradient(
        to right,
        rgba(255, 255, 255, 0) 0%,
        rgba(255, 255, 255, 0.4) 50%,
        rgba(255, 255, 255, 0) 100%
    );
    animation: shimmer-anim 2s infinite;
}

@keyframes shimmer-anim {
    0% { left: -100%; }
    100% { left: 100%; }
}

.road-line {
    @apply absolute top-0 w-full h-full;
    left: -100%;
    width: 200%;
    background: repeating-linear-gradient(
        to right,
        transparent 0px,
        transparent 10px,
        rgba(255, 255, 255, 0.4) 10px,
        rgba(255, 255, 255, 0.4) 20px
    );
    animation: road-move 0.6s linear infinite;
}

@keyframes road-move {
    0% { transform: translateX(0); }
    100% { transform: translateX(20px); }
}

.progress-bar {
    animation: progress-fill 2s ease-in-out infinite, gradient-move 3s linear infinite;
}

@keyframes progress-fill {
    0% { width: 0%; }
    50% { width: 80%; }
    100% { width: 100%; }
}

@keyframes gradient-move {
    0% { background-position: 0% 50%; }
    100% { background-position: 200% 50%; }
}

.ant-loading-dot {
    @apply w-3 h-3 rounded-full mx-1.5 inline-block;
    animation: ant-dot-bounce 1.4s infinite ease-in-out both;
}

.ant-loading-dot:nth-child(1) { animation-delay: -0.32s; }
.ant-loading-dot:nth-child(2) { animation-delay: -0.16s; }
.ant-loading-dot:nth-child(3) { animation-delay: 0s; }
.ant-loading-dot:nth-child(4) { animation-delay: 0.16s; }

@keyframes ant-dot-bounce {
    0%, 80%, 100% { transform: scale(0.6); opacity: 0.3; }
    40% { transform: scale(1.3); opacity: 1; }
}

.float-icon {
    animation: float-bg 8s ease-in-out infinite;
}

@keyframes float-bg {
    0%, 100% { transform: translateY(0px) rotate(0deg); }
    50% { transform: translateY(-20px) rotate(10deg); }
}

.animate-pulse-slow {
    animation: pulse 6s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

@keyframes pulse {
    0%, 100% { opacity: 0.1; transform: scale(1); }
    50% { opacity: 0.3; transform: scale(1.1); }
}

@media (max-width: 640px) {
    .logo-letter { @apply text-5xl; }
    .w-72 { @apply w-56; }
}
</style>
