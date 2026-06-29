<script setup>

import {onMounted, ref} from "vue";
const props = defineProps({
    data: {
        type: Object,
        default: () => {}
    }
})

const gradient = ref({
    x1: 0,
    y1: 0,
    x2: 1920,
    y2: 0
})

onMounted(() => {
    if (window.innerWidth <= 768) {
        // Rotation du gradient pour mobile (vertical par exemple)
        gradient.value = {
            x1: 0,
            y1: 0,
            x2: 0,
            y2: 279
        }
    }
})



// Color mapping for dynamic classes
const colorMappings = {
    green: {
        text: 'text-green-600',
        hover: 'group-hover:text-green-500',
        outline: 'outline-yellow-500',
        hoverOutline: 'group-hover:outline-yellow-500',
        gradient: 'from-yellow-500 to-green-600'
    },
    red: {
        text: 'text-red-600',
        hover: 'group-hover:text-red-600',
        outline: 'outline-red-600',
        hoverOutline: 'group-hover:outline-red-600',
        gradient: 'from-red-500 to-red-700'
    },
    orange: {
        text: 'text-orange-500',
        hover: 'group-hover:text-orange-600',
        outline: 'outline-orange-600',
        hoverOutline: 'group-hover:outline-orange-600',
        gradient: 'from-yellow-500 to-orange-600'
    }
};
</script>

<template>
    <!-- Features with improved responsive design -->
    <div class="relative py-8 md:py-12 lg:py-16 px-4 sm:px-6 lg:px-8 overflow-hidden">
        <!-- Enhanced container with better spacing -->
        <div class="container mx-auto relative z-10 mb-16">
            <!-- Responsive grid with better gap handling -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-8 lg:gap-10">
                <!-- Loop through features array to create cards -->
                <div v-for="(feature, index) in data" :key="index" class="relative group">
                    <!-- Separator for cards with responsive visibility -->
                    <div
                        v-if="index > 0"
                        class="hidden lg:block absolute -left-5 top-1/2 transform -translate-y-1/2 -translate-x-1/2 h-3/4 w-px bg-white/30"
                    ></div>

                    <!-- Floating icon with responsive sizing -->
                    <div class="absolute w-14 h-14 sm:w-16 sm:h-16 md:w-18 md:h-18 lg:w-20 lg:h-20
                              bg-white rounded-full shadow-lg -left-2 -top-3
                              flex items-center justify-center z-10
                              transition-transform duration-300 group-hover:scale-105 group-hover:shadow-xl">
                        <font-awesome-icon
                            :icon="feature.icon"
                            :class="`text-xl sm:text-2xl md:text-3xl lg:text-4xl
                                    ${colorMappings[feature.accentColor].text}
                                    leading-none transition-all duration-300
                                    ${colorMappings[feature.accentColor].hover}`"
                        />
                    </div>

                    <!-- Card with responsive padding and sizing -->
                    <a-card
                        :body-style="{ padding: '0' }"
                        :class="`relative pl-[5rem] sm:pl-16 md:pl-20 lg:pl-28
                                bg-white outline outline-1 outline-offset-[-1px]
                                ${colorMappings[feature.accentColor].outline}
                                flex items-center justify-center ml-auto gap-3 sm:gap-4
                                rounded-tr-[20px] sm:rounded-tr-[25px] md:rounded-tr-[30px]
                                rounded-bl-[20px] sm:rounded-bl-[25px] md:rounded-bl-[30px]
                                p-3 sm:p-4 md:p-5 lg:p-6
                                overflow-hidden transition-all duration-300
                                group-hover:shadow-lg group-hover:outline-2
                                ${colorMappings[feature.accentColor].hoverOutline}
                                h-full`"
                    >
                        <!-- Animated circle with responsive positioning -->
                        <div :class="`absolute w-20 h-20 sm:w-24 sm:h-24 md:w-28 md:h-28 lg:w-32 lg:h-32
                                    bg-gradient-to-b ${colorMappings[feature.accentColor].gradient}
                                    rounded-full -left-6 sm:-left-7 md:-left-8 lg:-left-9
                                    -top-6 sm:-top-7 md:-top-8 lg:-top-9
                                    z-0 transition-all duration-500
                                    group-hover:scale-125 group-hover:opacity-100 group-hover:rotate-45`">
                        </div>

                        <!-- Content with improved text scaling -->
                        <div class="relative z-10 flex flex-col w-full">
                            <h3 :class="`text-lg sm:text-xl md:text-2xl font-semibold
                                      mb-1 sm:mb-2 transition-colors duration-300
                                      ${colorMappings[feature.accentColor].hover}
                                      line-clamp-2`">
                                {{ feature.titre }}
                            </h3>
                            <p class="text-sm sm:text-md md:text-base text-gray-500
                                     transition-colors duration-300 group-hover:text-gray-700
                                     line-clamp-3 md:line-clamp-none">
                                {{ feature.description }}
                            </p>
                        </div>
                    </a-card>
                </div>
            </div>
        </div>
        <div class="absolute left-1/2 -ml-2 bottom-5 animate-bounce z-20">
            <font-awesome-icon
                icon="fa-angle-double-down"
                key="angle-double-down"
                class="text-2xl text-white"
            />
        </div>

        <!-- SVG Background with improved responsive positioning -->
        <div class="absolute inset-0 w-full h-full flex items-center justify-center pointer-events-none">
            <svg
                class="w-full h-full absolute bottom-0 left-0 right-0"
                preserveAspectRatio="xMidYMid slice"
                viewBox="0 0 1920 279"
                fill="none"
                xmlns="http://www.w3.org/2000/svg"
            >
                <defs>
                    <linearGradient
                        id="tailwindGradient"
                        :x1="gradient.x1"
                        :y1="gradient.y1"
                        :x2="gradient.x2"
                        :y2="gradient.y2"
                        gradientUnits="userSpaceOnUse"
                    >
                        <stop offset="10%" stop-color="#16a34a" />
                        <stop offset="100%" stop-color="#eab308" />
                    </linearGradient>
                </defs>

                <path
                    d="M1920 0V223H1227C1185 223 1083.2 230.1 1001.1 268C964.1 285.1 950 279.4 923.6 269.8C803.4 223 692.9 223 692.9 223H0V0H1920Z"
                    fill="url(#tailwindGradient)" />
            </svg>
        </div>
    </div>
</template>

<style scoped>
/* Add any additional styles for edge cases if needed */
@media (max-width: 370px) {
    :deep(.ant-card-body) {
        padding: 0.5rem !important;
    }
}
</style>
