<template>
    <div class="w-full my-16 bg-white">
        <div class="container mx-auto">
            <!-- Slider container -->
            <div class="w-full overflow-hidden relative py-8">
                <div
                    class="flex items-center gap-8 slider-animation"
                    :class="{ 'paused': isPaused }"
                    :style="{ animationDuration: `${animationDuration}s` }"
                    @mouseenter="isPaused = true"
                    @mouseleave="isPaused = false"
                >
                    <div
                        v-for="(partner, index) in displayedPartners"
                        :key="`logo-${index}`"
                        class="flex-shrink-0 transition-all duration-300 ease-in-out transform hover:scale-110"
                    >
                        <a-tooltip :title="partner.nom_partner">
                            <div
                                class="flex items-center justify-center overflow-hidden rounded-md bg-transparent hover:bg-gray-50 hover:shadow-md transition-all duration-300">
                                <img
                                    :src="partner.logo"
                                    :alt="partner.nom_partner"
                                    class="h-16 md:h-24 w-auto object-contain grayscale opacity-80 hover:grayscale-0 hover:opacity-100 transition-all duration-300"
                                />
                            </div>
                        </a-tooltip>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import {ref, computed} from 'vue';
// État pour la pause au survol
const isPaused = ref(false);

const props = defineProps({
    partners: {
        type: Object,
        default: () => []
    }
})

// Données des partenaires
const partners = ref(props.partners);

// Calculer la durée d'animation en fonction du nombre de partenaires
const animationDuration = computed(() => partners.value.length * 3);

// Dupliquer les partenaires pour créer un effet de défilement infini
const displayedPartners = computed(() => {
    // Répéter les partenaires suffisamment pour assurer un défilement fluide
    return [...partners.value, ...partners.value, ...partners.value];
});
</script>

<style scoped>
@keyframes scroll {
    0% {
        transform: translateX(0);
    }
    100% {
        transform: translateX(calc(-100% / 3));
    }
}

.slider-animation {
    animation: scroll 30s linear infinite;
    will-change: transform;
}

.paused {
    animation-play-state: paused;
}
</style>
