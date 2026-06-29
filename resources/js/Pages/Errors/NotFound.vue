<template>
    <div class="flex flex-col items-center justify-center min-h-screen bg-white dark:bg-gray-900 text-center px-4">
        <!-- Image 404 -->
        <img src="/img/404.svg" alt="404 Not Found" class="w-full max-w-md md:max-w-lg lg:max-w-xl mx-auto"/>

        <!-- Message d'erreur -->
        <h1 class="text-2xl md:text-3xl font-semibold text-gray-800 dark:text-white mt-4">
            Oups ! Page non trouvée
        </h1>
        <p class="text-gray-600 dark:text-gray-400 mt-2">
            Il semble que vous soyez hors ligne. Vérifiez votre connexion internet et réessayez.
        </p>

        <!-- Bouton Retour à l'accueil -->
        <ButtonAnimation
            label="Retour à l'accueil"
            icon=""
            backgroundColor="!bg-green-600"
            hoverColor="hover:!bg-green-700"
            borderColor="!border-green-600"
            textColor="!text-white"
            class="flex items-center justify-center font-medium mt-6"
            @click="goHome"
        />
    </div>
</template>

<script setup>
import { router } from '@inertiajs/vue3'
import ButtonAnimation from "@/Pages/Public/Components/Button/ButtonAnimation.vue";

const goHome = () => {
    router.visit('/')
}
</script>

<style scoped>
</style>


