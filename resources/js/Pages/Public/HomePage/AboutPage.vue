<template>
    <div class="relative overflow-hidden bg-white" id="a-propos">
        <!-- Loader -->
        <div v-if="!imagesLoaded" class="min-h-screen flex items-center justify-center bg-white">
            <span class="text-gray-500 text-lg animate-pulse">Chargement...</span>
        </div>

        <!-- Contenu principal affiché une fois les images chargées -->
        <div
            v-show="imagesLoaded"
            class="relative overflow-hidden bg-white transition-opacity duration-700 opacity-0"
            :class="{ 'opacity-100': imagesLoaded }"
        >
            <!-- Decorative leaves on right side -->
            <div class="absolute -right-14 md:-right-40 top-0 bottom-0 z-10">

                <img
                    src="/img/banbou.png"
                    alt="Décoration feuilles"
                    class="h-40 w-auto sm:h-60 md:h-full object-cover"
                    loading="eager"
                    decoding="async"
                />
            </div>

            <div class="container mx-auto px-4 py-8 md:py-16 relative">
                <div class="absolute left-0 top-0 bg-cover bg-center bg-no-repeat opacity-5 z-0 rounded-lg">
                    <img
                        src="/img/maki.png"
                        alt="Décoration feuilles"
                        class="h-full w-full object-cover"
                        loading="eager"
                        decoding="async"
                    />
                </div>

                <a-row :gutter="[24, 32]" align="middle">
                    <!-- Left column with text -->
                    <a-col :xs="24" :md="24" :lg="12" class="relative">
                        <div class="relative z-10">
                            <section-title title="À propos" variant="small" />
                            <h1 class="text-3xl md:text-6xl font-bold text-gray-800 my-4 md:mb-6">
                               {{data.length > 0 ? data[0].titre_principal : ''}}
                            </h1>
                            <p class="text-base text-gray-600 mb-6 max-w-screen-sm">
                                {{data.length > 0 ? data[0].description : ''}}
                            </p>
<!--                            <ButtonAnimation
                                :label="data.length > 0 ? data[0].bouton_label : ''"
                                icon="fa-solid fa-arrow-right"
                                backgroundColor="!bg-primary"
                                hoverColor="hover:!bg-primary"
                                borderColor="!border-primary"
                                textColor="!text-white"
                                class="mt-4 flex items-center justify-center font-medium"
                            />-->
                        </div>
                    </a-col>

                    <!-- Right column with images -->
                    <a-col :xs="24" :md="24" :lg="12">
                        <div class="relative h-[300px] md:h-[400px] lg:h-[544px] w-full">
                            <!-- Green accent rectangles -->
                            <div
                                class="absolute w-2/3 h-64 lg:w-96 lg:h-96 -right-2 md:-right-8 top-12 md:top-0 bg-primary rounded-xl"
                            ></div>
                            <div
                                class="hidden md:block absolute w-36 h-40 lg:w-52 lg:h-60 -left-2 bottom-0 bg-primary rounded-xl"
                            ></div>

                            <!-- Images with responsive positioning -->
                            <div
                                class="absolute right-0 top-16 w-full md:w-2/3 h-64 md:h-[340px] lg:h-[473px] rounded-2xl overflow-hidden shadow-lg"
                            >
                                <img
                                    class="w-full h-full object-cover rounded-2xl"
                                    :src="data.length > 0 ? data[0].img_2 : defaultImage"
                                    alt="Boutique vue extérieure"
                                    loading="eager"
                                    decoding="async"
                                />
                            </div>

                            <div
                                class="hidden md:block absolute -left-2 top-8 w-36 h-44 lg:w-52 lg:h-60 overflow-hidden rounded-xl shadow-lg"
                            >
                                <img
                                    class="w-full h-full object-cover rounded-xl"
                                    :src="data.length > 0 ? data[0].img : defaultImage"
                                    alt="Intérieur de la boutique"
                                    loading="eager"
                                    decoding="async"
                                />
                            </div>
                        </div>
                    </a-col>
                </a-row>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import ButtonAnimation from "@/Pages/Public/Components/Button/ButtonAnimation.vue";
import SectionTitle from "@/Pages/Public/Components/SectionTitle/SectionTitle.vue";

const props = defineProps({
    data: {
        type: Array,
        default: () => [
            {titre_principal: null, description: null, img: null, img_2: null, bouton_label: null, }
        ]
    }
})

// Gestion du chargement d'images
const imagesLoaded = ref(false);
const defaultImage = ref("/img/default/default_img.png");

const preloadImages = async (sources) => {
    await Promise.all(
        sources.map(
            (src) =>
                new Promise((resolve) => {
                    const img = new Image();
                    img.src = src;
                    img.onload = resolve;
                })
        )
    );
};

onMounted(async () => {
    await preloadImages([
        "/img/banbou.png",
        "/img/maki.png",
        "/img/store1.jpg",
        "/img/store2.jpg",
    ]);
    imagesLoaded.value = true;
});


</script>

<style scoped></style>
