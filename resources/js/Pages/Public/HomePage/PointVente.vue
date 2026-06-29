<template>
    <div class="relative w-full h-full bg-cover bg-center bg-no-repeat py-12" style="background-image: url('/img/store1.jpg')">

        <div class="absolute inset-0 h-2/3 bg-gradient-to-r from-primary to-green-700"></div>
        <!-- Section header with green background -->
        <div class="container mx-auto">
            <div class="relative px-4 text-center">
                <h2 class="text-white text-3xl md:text-4xl font-bold mb-6">
                    {{point_de_vente[0].title}}
<!--                    NOS <span class="font-normal">POINTS DE VENTE</span>-->
                </h2>

                <div class="max-w-4xl mx-auto">
                    <p class="text-white text-base md:text-lg mb-8">
                        {{point_de_vente[0].description}}
                    </p>

                    <!-- Divider with logo -->
                    <div class="flex items-center justify-center">
                        <div class="h-1 bg-white flex-grow max-w-16"></div>
                        <a-button size="large"  shape="circle" class="bg-white">
                            <font-awesome-icon icon="fa-solid fa-headset" class="text-primary text-xl" />
                        </a-button>
                        <div class="h-1 bg-white flex-grow max-w-16"></div>
                    </div>
                </div>
            </div>

            <!-- Store locations grid -->
            <div class="py-12 px-4">
                <div class="flex flex-wrap justify-center gap-6">
                    <!-- Store location cards -->
                    <a-card
                        :body-style="{ padding: '4px' }"
                        v-for="(store, index) in boutique"
                        :key="index"
                        class="bg-white rounded-lg shadow-md overflow-hidden flex flex-col w-64"
                    >
                        <div class="h-64 relative">
                            <img :src="store.img" :alt="store.nom_boutique" class="w-full h-full object-cover" />
                            <div class="absolute -bottom-3 left-1/2 transform -translate-x-1/2">
                                <a-button
                                    type="text"
                                    class="bg-primary border-none border-primary hover:!bg-green-700 !text-white !rounded-full"
                                >
                                    {{ store.nom_boutique }}
                                </a-button>
                            </div>
                        </div>
                        <div class="px-4 py-6 mt-auto">
                            <div class="flex items-center mb-3">
                                <font-awesome-icon icon="fa-solid fa-phone" class="text-primary text-base mr-2" />
                                <span>{{ store.telephone }}</span>
                            </div>
                            <div class="flex items-center">
                                <font-awesome-icon icon="fa-solid fa-location-dot" class="text-primary text-base mr-2" />
                                <span>{{ store.addresse }}</span>
                            </div>
                        </div>
                    </a-card>
                </div>
            </div>

        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
// Store data

const props = defineProps({
    point_de_vente: {
        type: Object,
        default: () => {}
    },
    boutique: {
        type: Object,
        default: () => {}
    }
})

const stores = ref([
    {
        image: '/img/store2.jpg',
        alt: 'Boutique moderne',
        buttonText: 'Bazary be',
        phone: '+261 34 00 000 00',
        location: 'Store Location: Lincoln- 344, Illinois, Chicago, USA'
    },
    {
        image: '/img/store2.jpg',
        alt: 'Salon de coiffure',
        buttonText: 'Infos Point de vente',
        phone: '+261 34 00 000 00',
        location: 'Store Location: Lincoln- 344, Illinois, Chicago, USA'
    },
    {
        image: '/img/store2.jpg',
        alt: 'Boutique de décoration',
        buttonText: 'Infos Point de vente',
        phone: '+261 34 00 000 00',
        location: 'Store Location: Lincoln- 344, Illinois, Chicago, USA'
    },
    {
        image: '/img/store2.jpg',
        alt: 'Restaurant traditionnel',
        buttonText: 'Infos Point de vente',
        phone: '+261 34 00 000 00',
        location: 'Store Location: Lincoln- 344, Illinois, Chicago, USA'
    },
    {
        image: '/img/store2.jpg',
        alt: 'Restaurant traditionnel',
        buttonText: 'Infos Point de vente',
        phone: '+261 34 00 000 00',
        location: 'Store Location: Lincoln- 344, Illinois, Chicago, USA'
    }
]);
</script>

<style scoped>
/* Additional custom styles can go here */
:deep(.ant-btn-primary) {
    @apply text-white font-medium;
}
</style>
