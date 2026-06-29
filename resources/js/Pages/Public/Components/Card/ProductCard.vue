<script setup>
import { defineProps } from 'vue';
import { Link } from '@inertiajs/vue3';
import { message } from 'ant-design-vue';
import ButtonAnimation from "@/Pages/Public/Components/Button/ButtonAnimation.vue";

const props = defineProps({
    product: {
        type: Object,
        required: true
    }
});

const getColorClass = (color) => {
    if (color === 'red') return 'border-red-500';
    if (color === 'green') return 'border-green-500';
    if (color === 'blue') return 'border-blue-500';
    return 'bg-white';
};

const selectColor = (color) => {
    message.success(`Couleur ${color} sélectionnée`);
};
</script>

<template>
    <Link :href="route('detailProduct', product.id)" class="block">
        <a-card
            :body-style="{ padding: '0' }"
            @click="goToProductDetail"
            class="border border-gray-200 rounded-lg group hover:border-primary overflow-hidden bg-white relative transform transition-all duration-300 hover:shadow-md hover:shadow-green-600 hover:-translate-y-1"
        >
            <!-- Badge de disponibilité -->
            <div
                :class="[
        'absolute top-0 left-0 rounded-br-[34px] inline-flex justify-start items-center gap-2.5 text-white px-4 h-8 py-1 text-xs uppercase',
        product.available ? 'bg-primary' : 'bg-red-500'
      ]"
            >
                {{ product.available ? 'Disponible' : 'Indisponible' }}
            </div>

            <!-- Image du produit avec zoom au survol -->
            <template #cover>
                <div class="h-48 flex items-center justify-center overflow-hidden bg-gray-50">
                    <img
                        :src="product.image"
                        :alt="product.name"
                        class="max-h-full max-w-full object-contain transition-transform duration-500 transform hover:scale-110"
                    />
                </div>
            </template>

            <!-- Informations du produit -->
            <div class="text-left mt-8 p-4">
                <h3 class="text-green-600 font-semibold uppercase">{{ product.brand }}</h3>
                <p class="text-base mb-2">{{ product.name }}</p>

                <!-- Prix avec animation -->
                <div class="flex justify-between items-center mt-4">
                    <p class="text-xl font-semibold font-['Dosis'] text-red-500 transition-all duration-300 hover:text-red-600 hover:font-bold">
                        {{ product.price }}
                    </p>

                    <!-- Options de couleur -->
                    <div class="flex items-center">
                        <span class="text-base mr-1">Couleurs:</span>
                        <div
                            v-for="color in product.colors"
                            :key="color"
                            class="w-4 h-4 rounded-full border-2 ml-1 cursor-pointer transition-transform duration-300 hover:scale-125"
                            :class="getColorClass(color)"
                            @click="selectColor(color)"
                        >
                        </div>
                    </div>
                </div>

                <!-- Bouton Ajouter au panier avec animation -->
                <ButtonAnimation
                    label="Ajouter au panier"
                    icon="fa-solid fa-shopping-cart"
                    backgroundColor="!bg-yellow-500"
                    hoverColor="hover:!bg-yellow-400"
                    borderColor="!border-yellow-500"
                    textColor="!text-black"
                    class="w-full mt-4 flex items-center justify-center"
                />
            </div>
        </a-card>
    </Link>
</template>
