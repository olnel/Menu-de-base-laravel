<script setup>
import { ref, onMounted } from "vue";
import ButtonAnimation from "@/Pages/Public/Components/Button/ButtonAnimation.vue";
import SectionTitle from "@/Pages/Public/Components/SectionTitle/SectionTitle.vue";

const props = defineProps({
    data: {
        type: Object,
        default: () => {}
    }
})

// Données des produits
const productItems =  ref(props.data);

// Préchargement
const imagesLoaded = ref(false);

const preloadImages = async () => {
    const sources = productItems.value.map(item => item.image);
    await Promise.all(
        sources.map(
            src =>
                new Promise(resolve => {
                    const img = new Image();
                    img.src = src;
                    img.onload = resolve;
                    img.onerror = resolve;
                })
        )
    );
    imagesLoaded.value = true;
};

onMounted(preloadImages);
</script>

<template>
    <div class="container mx-auto px-4 sm:px-6 lg:px-0 py-16">
        <SectionTitle title="Nouveautés" variant="large"/>

        <!-- Loader -->
        <div v-if="!imagesLoaded" class="flex items-center justify-center py-24 text-gray-400">
            Chargement des produits...
        </div>

        <!-- Grille produits -->
        <div
            v-show="imagesLoaded"
            class="product-grid opacity-0 transition-opacity duration-700"
            :class="{ 'opacity-100': imagesLoaded }"
        >
            <div
                v-for="(item, index) in productItems"
                :key="index"
                :class="[
          'product-card',
          item.stretch === 'h' ? 'h-stretch' : '',
          item.stretch === 'v' ? 'v-stretch' : '',
          item.stretch === 'big' ? 'big-stretch' : '',
          `fade-in-up-${index % 3}`
        ]"
            >
                <div
                    class="relative z-0 overflow-hidden rounded-md h-full group cursor-pointer transform transition-all duration-500">
                    <img
                        :src="item.img"
                        :alt="item.title"
                        class="w-full h-full object-cover transition-all duration-700 ease-out group-hover:scale-110 group-hover:filter group-hover:brightness-110"
                    />
                    <div
                        class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-transparent opacity-80 group-hover:from-green-600 group-hover:opacity-100 transition-all duration-300"></div>

                    <div
                        class="absolute bottom-0 left-0 p-6 w-full transform transition-all duration-500 ease-out group-hover:translate-y-0"
                        :class="{ 'translate-y-16': !item.visible }"
                    >
                        <!-- Badge Marque -->
                        <div
                            class="block mb-3 transform transition-all duration-500 delay-100 opacity-100 group-hover:opacity-100 translate-y-4 group-hover:translate-y-0">
                            <div
                                class="bg-white flex justify-center w-16 rounded-sm rounded-bl-[100px] rounded-tr-[100px] shadow-md overflow-hidden mb-2">
                                <img src="/img/logo_loune.jpg" alt="Brand Logo" class="h-full w-full object-cover"/>
                            </div>
                            <span class="text-base text-white">{{ item.title }}</span>
                        </div>

                        <!-- Titre -->
                        <h3 class="text-white text-2xl font-['Prata'] uppercase font-normal mb-2 transition-all duration-300 group-hover:text-white">
                            {{ item.type }}
                        </h3>

                        <!-- Count -->
                        <div class="flex items-center justify-between mb-4">
                            <p class="text-gray-200 text-sm opacity-0 group-hover:opacity-100 transition-all duration-500 delay-150">
                            <span class="inline-block mr-1">
                              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline" fill="none"
                                   viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                              </svg>
                            </span>
                                {{ item.count }}
                            </p>
                        </div>

                        <!-- Bouton -->
                        <ButtonAnimation
                            :label="item.button_label"
                            icon="fa-solid fa-arrow-right"
                            backgroundColor="!bg-yellow-500"
                            hoverColor="hover:!bg-yellow-400"
                            borderColor="!border-yellow-500"
                            textColor="!text-black"
                            class="transition-all duration-300 transform opacity-0 translate-y-4 group-hover:opacity-100 group-hover:translate-y-0"
                        />

                        <!-- Points décoratifs -->
                        <div class="absolute top-0 group-hover:-top-4 left-6">
                            <div class="grid grid-cols-6 gap-1">
                                <div v-for="dot in 18" :key="dot"
                                     class="w-1 h-1 bg-white rounded-full opacity-80"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Badge Date -->
                    <div
                        class="absolute top-4 right-4 bg-white/80 group-hover:bg-white rounded-md w-12 h-14 text-center flex flex-col items-center justify-center transition-all duration-500 transform scale-75 group-hover:scale-110 shadow-lg">
                        <span
                            class="text-xl font-bold text-green-600 group-hover:text-green-600 transition-colors duration-300">{{ item.badge_date_jour }}</span>
                        <span
                            class="text-xs uppercase text-zinc-500 group-hover:text-black font-bold tracking-tight transition-colors duration-300">{{ item.badge_date_annee }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.product-grid {
    @apply relative grid gap-2 md:gap-4;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    grid-auto-rows: 300px;
    grid-auto-flow: dense;
    perspective: 1000px;
}

.product-card {
    @apply transition-all duration-300 ease-in-out;
}

.h-stretch {
    @apply col-span-2;
}

.v-stretch {
    @apply row-span-2;
}

.big-stretch {
    @apply col-span-2 row-span-2;
}

.fade-in-up-0 {
    animation: fadeInUp 0.6s ease-out;
}

.fade-in-up-1 {
    animation: fadeInUp 0.6s ease-out 0.2s both;
}

.fade-in-up-2 {
    animation: fadeInUp 0.6s ease-out 0.4s both;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@media (max-width: 768px) {
    .gallery-grid {
        grid-auto-rows: 220px;
    }

    .h-stretch,
    .v-stretch,
    .big-stretch {
        @apply col-span-1 row-span-1;
    }
}

@media (min-width: 1600px) {
    .gallery-grid {
        grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
        grid-auto-rows: 300px;
    }
}
</style>
