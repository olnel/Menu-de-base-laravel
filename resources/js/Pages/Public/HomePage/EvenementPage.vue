<script setup>
import { ref, computed, onMounted } from 'vue';
import { Swiper, SwiperSlide } from 'swiper/vue';
import { Autoplay as SwiperAutoplay, Pagination as SwiperPagination } from 'swiper/modules';
import 'swiper/css';
import 'swiper/css/pagination';
import 'swiper/css/autoplay';
import ButtonAnimation from '@/Pages/Public/Components/Button/ButtonAnimation.vue';

const props = defineProps({
    data: {
        type: Object,
        default: () => {}
    }
})


const textFromDatabase = "Événement Shopping Spécial";

// Mise en forme dynamique du titre
const formattedText = computed(() => {
    const words = textFromDatabase.split(' ');
    let formatted = '';
    words.forEach((word, index) => {
        formatted += word + ' ';
        if ((index + 1) % 2 === 0) {
            formatted += '<br>';
        }
    });
    return formatted.trim();
});

// Préchargement des images
const imagesToPreload = [
    '/img/event-bg.jpg',
    '/img/scolbag.png',
    '/img/tady.png',
];
const imagesLoaded = ref(0);

const preloadImages = () => {
    imagesToPreload.forEach((src) => {
        const img = new Image();
        img.onload = () => {
            imagesLoaded.value++;
        };
        img.src = src;
    });
};

onMounted(() => {
    preloadImages();
});
</script>
<template>
    <div class="w-full relative  pt-24 overflow-hidden">
        <!-- Swiper visible seulement après chargement complet des images -->
        <div
            class="relative w-full h-full md:h-[500px] flex items-center bg-cover bg-no-repeat py-4"
            style="background-image: url('/img/event-bg.jpg'); background-position: 50% 5%;"
        >
            <div class="absolute inset-0 bg-gradient-to-r from-[#1B8058] to-[#FFCD00] opacity-[96%] z-0"></div>
            <swiper
                :modules="[SwiperAutoplay, SwiperPagination]"
                :autoplay="{ delay: 5000, disableOnInteraction: false }"
                :pagination="{ clickable: true }"
                :loop="true"
                class="w-full"
                id="event_swiper"

            >
                <swiper-slide v-for="(value, key) in data" :key="key">
                    <div class="container mx-auto px-4 sm:px-6 lg:px-0 flex flex-col md:flex-row items-center justify-between">
                        <!-- Text zone -->
                        <div class="text-white md:w-1/2 z-10">
                            <h1 v-html="value.title" class="text-5xl md:text-6xl mb-2 font-medium text-white font-['Prata'] capitalize"></h1>

                            <p class="mb-6 text-base md:text-lg pt-2">
                                {{value.description}}
                            </p>
                            <ButtonAnimation
                                :label="value.button_label"
                                icon="fa-solid fa-arrow-right"
                                backgroundColor="!bg-yellow-500"
                                hoverColor="hover:!bg-yellow-400"
                                borderColor="!border-yellow-500"
                                textColor="!text-black"
                                class="mt-4 flex items-center justify-center font-medium"
                            />
                        </div>

                        <!-- Image section -->
                        <div class="relative md:w-1/2 flex justify-center mt-6 md:mt-0">
                            <div class="relative">
                                <!-- Main image -->
                                <div>
                                    <img
                                        :src="value.img"
                                        alt="Sac à dos bleu"
                                        class="h-96 object-contain relative z-20 transform md:-translate-y-12 sm:translate-y-0"
                                    />
                                </div>

                                <!-- Decorative image -->
                                <div class="absolute -right-0 top-1/3 sm:top-1/2 transform md:-translate-y-1/2 sm:translate-y-0 translate-x-6 sm:translate-x-24 z-10">
                                    <div
                                        class="bg-cover bg-center bg-no-repeat w-40 h-80"
                                        style="background-image: url('/img/tady.png'); background-size: 130px;"
                                    ></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </swiper-slide>


            </swiper>
        </div>
    </div>
</template>
<style>
#event_swiper {
    @apply w-full h-full overflow-visible;
}

#event_swiper .swiper-slide {
    @apply overflow-visible flex items-center justify-center;
}

 #event_swiper .swiper-slide img {
    @apply object-cover w-[80%] h-auto;
}

 #event_swiper .swiper-pagination {
    @apply absolute bottom-0 sm:bottom-8  left-44 transform -translate-x-1/2 translate-y-1/2 z-10;
}

 #event_swiper .swiper-pagination-bullet {
    @apply bg-white/70 w-3 h-3 mx-1;
}

 #event_swiper .swiper-pagination-bullet-active {
    @apply bg-white;
}
</style>
