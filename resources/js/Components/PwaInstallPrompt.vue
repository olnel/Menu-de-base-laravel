<template>
    <Transition name="slide-up">
        <div
            v-if="showPrompt"
            class="fixed bottom-4 left-4 right-4 z-50 flex items-center gap-3 bg-white border border-gray-200 rounded-2xl shadow-xl px-4 py-3 md:left-auto md:right-6 md:w-80"
        >
            <img src="/icons/icon-72x72.png" alt="TransMada" class="w-10 h-10 rounded-xl shrink-0" />
            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-gray-800 leading-tight">Installer TransMada</p>
                <p class="text-xs text-gray-500 mt-0.5">Accès rapide depuis votre écran d'accueil</p>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <button
                    @click="dismiss"
                    class="text-gray-400 hover:text-gray-600 p-1"
                    aria-label="Fermer"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
                <button
                    @click="install"
                    class="bg-blue-900 hover:bg-blue-800 text-white text-xs font-medium px-3 py-1.5 rounded-lg transition-colors"
                >
                    Installer
                </button>
            </div>
        </div>
    </Transition>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue';

const showPrompt = ref(false);
let deferredPrompt = null;

const STORAGE_KEY = 'pwa_install_dismissed';

function onBeforeInstallPrompt(e) {
    e.preventDefault();
    deferredPrompt = e;

    const dismissed = localStorage.getItem(STORAGE_KEY);
    if (!dismissed) {
        setTimeout(() => { showPrompt.value = true; }, 3000);
    }
}

async function install() {
    if (!deferredPrompt) return;
    showPrompt.value = false;
    deferredPrompt.prompt();
    const { outcome } = await deferredPrompt.userChoice;
    if (outcome === 'accepted') {
        localStorage.setItem(STORAGE_KEY, '1');
    }
    deferredPrompt = null;
}

function dismiss() {
    showPrompt.value = false;
    localStorage.setItem(STORAGE_KEY, '1');
}

onMounted(() => {
    window.addEventListener('beforeinstallprompt', onBeforeInstallPrompt);
});

onBeforeUnmount(() => {
    window.removeEventListener('beforeinstallprompt', onBeforeInstallPrompt);
});
</script>

<style scoped>
.slide-up-enter-active,
.slide-up-leave-active {
    transition: all 0.3s ease;
}
.slide-up-enter-from,
.slide-up-leave-to {
    opacity: 0;
    transform: translateY(20px);
}
</style>
