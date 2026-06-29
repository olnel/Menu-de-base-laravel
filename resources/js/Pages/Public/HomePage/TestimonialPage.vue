<script setup>
import {ref, computed, onMounted, onUnmounted} from 'vue'
import {Swiper, SwiperSlide} from 'swiper/vue'
import {FreeMode, Navigation, Pagination, Autoplay} from 'swiper/modules';
// Swiper styles
import "swiper/css";
import "swiper/css/free-mode";
import "swiper/css/pagination";
import "swiper/css/navigation";
// Modules pour Swiper
const modules = ref([FreeMode, Navigation, Pagination, Autoplay]);

const swiperRef = ref(null);
const currentPage = ref(1);
const totalSlides = ref(0);
const slidesPerView = ref(3);

const breakpoints = {
    320: {slidesPerView: 1, spaceBetween: 10},
    768: {slidesPerView: 2, spaceBetween: 10},
    1024: {slidesPerView: 3, spaceBetween: 16},
    1200: {slidesPerView: 3, spaceBetween: 16},
};

const testimonials = ref([
    {
        content:
            "Pellentesque eu nibh eget mauris congue mattis mattis nec tellus. Phasellus imperdiet elit eu magna dictum, bibendum cursus velit sodales. Donec sed neque eget.",
        name: "Robert Fox",
        role: "Customer",
        avatar: "/api/placeholder/48/48",
        rating: 5
    },
    {
        content:
            "Pellentesque eu nibh eget mauris congue mattis mattis nec tellus. Phasellus imperdiet elit eu magna dictum, bibendum cursus velit sodales. Donec sed neque eget.",
        name: "Dianne Russell",
        role: "Customer",
        avatar: "/api/placeholder/48/48",
        rating: 5
    },
    {
        content:
            "Pellentesque eu nibh eget mauris congue mattis mattis nec tellus. Phasellus imperdiet elit eu magna dictum, bibendum cursus velit sodales. Donec sed neque eget.",
        name: "Robert Fox",
        role: "Customer",
        avatar: "/api/placeholder/48/48",
        rating: 5
    },
    {
        content:
            "Pellentesque eu nibh eget mauris congue mattis mattis nec tellus. Phasellus imperdiet elit eu magna dictum, bibendum cursus velit sodales. Donec sed neque eget.",
        name: "Jane Cooper",
        role: "Customer",
        avatar: "/api/placeholder/48/48",
        rating: 5
    },
    {
        content:
            "Pellentesque eu nibh eget mauris congue mattis mattis nec tellus. Phasellus imperdiet elit eu magna dictum, bibendum cursus velit sodales. Donec sed neque eget.",
        name: "Wade Warren",
        role: "Customer",
        avatar: "/api/placeholder/48/48",
        rating: 5
    },
    {
        content:
            "Pellentesque eu nibh eget mauris congue mattis mattis nec tellus. Phasellus imperdiet elit eu magna dictum, bibendum cursus velit sodales. Donec sed neque eget.",
        name: "Wade Warren",
        role: "Customer",
        avatar: "/api/placeholder/48/48",
        rating: 5
    }
]);

// Calculer le nombre de slides visibles en fonction de la largeur de l'écran
const updateSlidesPerView = () => {
    if (window.innerWidth >= 1024) {
        slidesPerView.value = 3;
    } else if (window.innerWidth >= 768) {
        slidesPerView.value = 2;
    } else {
        slidesPerView.value = 1;
    }
};

// Déterminer si nous sommes sur la dernière page
const isLastPage = computed(() => {
    if (!swiperRef.value || !totalSlides.value) return true;

    const totalGroups = Math.ceil(totalSlides.value / slidesPerView.value);
    const currentGroup = Math.floor(swiperRef.value.activeIndex / slidesPerView.value) + 1;

    return currentGroup >= totalGroups;
});

// Fonctions pour naviguer entre les pages
const nextPage = () => {
    if (!isLastPage.value && swiperRef.value) {
        swiperRef.value.slideNext();
    }
};

const prevPage = () => {
    if (currentPage.value > 1 && swiperRef.value) {
        swiperRef.value.slidePrev();
    }
};

// Callbacks Swiper
const onSwiper = (swiper) => {
    swiperRef.value = swiper;
    totalSlides.value = testimonials.value.length;
    updateSlidesPerView();
};

const onSlideChange = (swiper) => {
    currentPage.value = swiper.activeIndex + 1;
};

// Gestionnaire de redimensionnement pour la réactivité
onMounted(() => {
    window.addEventListener('resize', updateSlidesPerView);
});

onUnmounted(() => {
    window.removeEventListener('resize', updateSlidesPerView);
});
</script>
<template>
    <div class="bg-gray-100 relative">
        <div class="container mx-auto px-4 sm:px-6 lg:px-0 py-8 sm:py-12 relative">
            <!-- Testimonial Header -->
            <div class="relative flex flex-col md:flex-row justify-between items-start md:items-center py-4 gap-4">
                <div class="mb-8">
                    <p class="text-primary font-medium font-['Segoe_Script'] uppercase tracking-wide">CLIENT
                        TESTIMONIAL</p>
                    <h2 class="text-4xl leading-none font-bold text-gray-800">Ce que dit notre client</h2>
                </div>

                <div class="flex items-center space-x-2">
                    <a-tooltip title="Précédent">
                        <a-button
                            type="default"
                            shape="circle"
                            size="middle"
                            :disabled="currentPage === 1"
                            :class="[
                                'flex-shrink-0 flex items-center justify-center rounded-full bg-white  hover:!text-white text-black border-none transition-all duration-300 ease-in-out hover:shadow-lg hover:bg-primary hover:shadow-primary/50 hover:brightness-110',
                                { 'cursor-not-allowed': currentPage === 1 }
                            ]"
                            @click="prevPage"
                        >
                            <template #icon>
                                <font-awesome-icon icon="arrow-left"/>
                            </template>
                        </a-button>
                    </a-tooltip>

                    <a-tooltip title="Suivant">
                        <a-button
                            size="middle"
                            type="default"
                            shape="circle"
                            :disabled="isLastPage"
                            :class="[
                                'flex-shrink-0 flex items-center justify-center rounded-full bg-white  hover:!text-white text-black border-none transition-all duration-300 ease-in-out hover:shadow-lg hover:bg-primary hover:shadow-primary/50 hover:brightness-110',
                                { ' cursor-not-allowed ': isLastPage }
                            ]"
                            @click="nextPage"
                        >
                            <template #icon>
                                <font-awesome-icon icon="arrow-right"/>
                            </template>
                        </a-button>
                    </a-tooltip>
                </div>
            </div>

            <!-- Swiper Component -->
            <div class="testimonial-swiper-container w-full">
                <swiper
                    ref="swiperRef"
                    :modules="modules"
                    :breakpoints="breakpoints"
                    :slidesPerView="3"
                    :space-between="16"
                    :free-mode="false"
                    :autoplay="{ delay: 3000, disableOnInteraction: false }"
                    :pagination="{
                        clickable: true,

                    }"
                    @swiper="onSwiper"
                    @slideChange="onSlideChange"
                    id="testimonial_swiper"
                >
                    <swiper-slide v-for="(testimonial, index) in testimonials" :key="index">
                        <div class="bg-white p-4 sm:p-6 rounded-lg shadow-sm">
                            <font-awesome-icon icon="fa-solid fa-quote-left"
                                               class="!me-2 !size-6 sm:!size-7 text-primary"/>

                            <p class="text-gray-700 text-sm sm:text-base mb-4 sm:mb-6">
                                {{ testimonial.content }}
                            </p>

                            <div class="flex flex-col sm:flex-row sm:items-center gap-4 sm:gap-0">
                                <!-- Avatar et nom -->
                                <div class="flex items-center">
                                    <a-avatar :src="testimonial.avatar" :size="48"/>
                                    <div class="ml-3">
                                        <h4 class="font-medium text-gray-900 text-sm sm:text-base">{{
                                                testimonial.name
                                            }}</h4>
                                        <p class="text-gray-500 text-xs sm:text-sm">{{ testimonial.role }}</p>
                                    </div>
                                </div>

                                <!-- Note -->
                                <div class="sm:ml-auto">
                                    <div class="flex">
                                        <a-rate :value="2" disabled :count="5"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </swiper-slide>
                </swiper>
            </div>
        </div>
        <!-- Decorative Elements -->
        <div class="absolute right-0 top-1/2 transform -translate-y-1/2 hidden sm:block">
            <img src="/img/art.png" alt="Decorative element" class="w-auto h-auto object-cover"/>
        </div>
        <div class="absolute bottom-8 left-12 hidden sm:block">
            <img src="/img/raphia.png" alt="Decorative element" class="w-auto h-auto origin-top-left object-cover"/>
        </div>
    </div>
</template>

<style>
#testimonial_swiper {
    @apply  w-full h-full overflow-hidden relative pb-16
}

#testimonial_swiper.swiper-pagination {
    @apply absolute left-0 right-0 !bottom-0 gap-1 z-10 w-full cursor-pointer text-center flex items-center justify-center;
    transition: 300ms opacity;
    transform: translate3d(0, 0, 0);
}

#testimonial_swiper .swiper-pagination-bullet {
    @apply w-3 h-3 opacity-100 rounded-full cursor-pointer;
    background: theme('colors.gray.300');
    transition: all 0.3s ease;
}


#testimonial_swiper .swiper-pagination-bullet-active {
    @apply !w-10 h-3 opacity-100 rounded-full !bg-gradient-to-t !from-green-600 !to-primary;
}

@media (max-width: 640px) {
    #testimonial_swiper .swiper-pagination-bullet-active {
        width: 30px !important;
        height: 10px;
    }

    #testimonial_swiper .swiper-pagination-bullet {
        width: 10px;
        height: 10px;
    }
}

.swiper-slide {
    height: auto;
}
</style>
