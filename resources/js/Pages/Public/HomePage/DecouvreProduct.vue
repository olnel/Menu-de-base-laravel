<script setup>
import { ref, computed } from 'vue';
import ProductCard from "@/Pages/Public/Components/Card/ProductCard.vue";
import ProductSectionTitle from "@/Pages/Public/Components/SectionTitle/ProductSectionTitle.vue";

// Données des produits
const products = ref([
    {
        id: 1,
        brand: 'RAFIA',
        name: 'All Natural Italian-Style Chicken Meatballs',
        price: '80 000 Ar',
        image: '/img/sacs.jpg',
        colors: ['red', 'green'],
        available: false
    },
    {
        id: 2,
        brand: 'RAFIA',
        name: 'All Natural Italian-Style Chicken Meatballs',
        price: '80 000 Ar',
        image: '/img/sacs.jpg',
        colors: ['red', 'green'],
        available: true
    },
    {
        id: 3,
        brand: 'RAFIA',
        name: 'All Natural Italian-Style Chicken Meatballs',
        price: '80 000 Ar',
        image: '/img/sacs.jpg',
        colors: ['red', 'green'],
        available: true
    },
    {
        id: 4,
        brand: 'RAFIA',
        name: 'All Natural Italian-Style Chicken Meatballs',
        price: '80 000 Ar',
        image: '/img/sacs.jpg',
        colors: ['red', 'green'],
        available: true
    },
    {
        id: 5,
        brand: 'RAFIA',
        name: 'All Natural Italian-Style Chicken Meatballs',
        price: '80 000 Ar',
        image: '/img/sacs.jpg',
        colors: ['red', 'green'],
        available: true
    },
    {
        id: 6,
        brand: 'RAFIA',
        name: 'All Natural Italian-Style Chicken Meatballs',
        price: '80 000 Ar',
        image: '/img/sacs.jpg',
        colors: ['red', 'green'],
        available: true
    },
    {
        id: 7,
        brand: 'RAFIA',
        name: 'All Natural Italian-Style Chicken Meatballs',
        price: '80 000 Ar',
        image: '/img/sacs.jpg',
        colors: ['red', 'green'],
        available: true
    },
    {
        id: 8,
        brand: 'RAFIA',
        name: 'All Natural Italian-Style Chicken Meatballs',
        price: '80 000 Ar',
        image: '/img/sacs.jpg',
        colors: ['red', 'green'],
        available: true
    }
]);

// Pagination
const currentPage = ref(1);
const pageSize = ref(5);

// Propriétés calculées
const displayedProducts = computed(() => {
    const start = (currentPage.value - 1) * pageSize.value;
    const end = start + pageSize.value;
    return products.value.slice(start, end);
});

const totalPages = computed(() => {
    return Math.ceil(products.value.length / pageSize.value);
});

const isLastPage = computed(() => {
    return currentPage.value >= totalPages.value;
});

// Méthodes
const handlePageChange = (page) => {
    currentPage.value = page;
};

const prevPage = () => {
    if (currentPage.value > 1) {
        currentPage.value--;
    }
};

const nextPage = () => {
    const maxPage = Math.ceil(products.value.length / pageSize.value);
    if (currentPage.value < maxPage) {
        currentPage.value++;
    }
};
</script>

<template>
    <div class="container mx-auto px-4 sm:px-6 lg:px-0">
        <!-- En-tête avec animation -->
        <div class="relative flex justify-between items-center py-4 border-b border-gray-200">
            <ProductSectionTitle title="Découvrez nos produits" />

            <div class="flex items-center space-x-2">
                <a-tooltip title="Précédent">
                    <a-button
                        type="default"
                        shape="circle"
                        size="middle"
                        :disabled="currentPage === 1"
                        :class="[
                                    'flex-shrink-0 flex items-center justify-center rounded-full bg-primary hover:!text-white text-white border-none',
                                    'transition-all duration-300 ease-in-out hover:shadow-lg hover:shadow-primary/50 hover:brightness-110',
                                    { 'opacity-50 cursor-not-allowed': currentPage === 1 }
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
                                    'flex-shrink-0 flex items-center justify-center rounded-full bg-primary hover:!text-white text-white border-none',
                                    'transition-all duration-300 ease-in-out hover:shadow-lg hover:shadow-primary/50 hover:brightness-110',
                                    { 'opacity-50 cursor-not-allowed': isLastPage }
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

        <!-- Grille de produits avec Transition -->
        <TransitionGroup
            name="product-list"
            tag="div"
            class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4 mt-8"
        >
            <ProductCard
                v-for="product in displayedProducts"
                :key="product.id"
                :product="product"
            />
        </TransitionGroup>

        <!-- Pagination -->
        <div class="mt-12 flex flex-col items-center gap-4">
            <div class="flex items-center justify-center gap-2">
                <a-button
                    size="middle"
                    @click="prevPage"
                    :disabled="currentPage === 1"
                    class="rounded-lg bg-white border border-gray-200 text-gray-700 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200"
                >
                    Précédent
                </a-button>

                <div class="flex items-center gap-2">
                    <template v-for="page in totalPages" :key="page">
                        <a-button
                            size="middle"
                            @click="handlePageChange(page)"
                            :class="[
                                    'rounded-lg transition-all duration-200 font-medium',
                                    currentPage === page
                                        ? 'bg-primary text-white shadow-md shadow-primary/30'
                                        : 'bg-white border border-gray-200 text-gray-700 hover:bg-gray-50'
                                ]"
                        >
                            {{ page }}
                        </a-button>
                    </template>
                </div>

                <a-button
                    size="middle"
                    @click="nextPage"
                    :disabled="isLastPage"
                    class=" rounded-lg bg-white border border-gray-200 text-gray-700 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200"
                >
                    Suivant
                </a-button>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* Animations de transition pour la liste de produits */
.product-list-enter-active,
.product-list-leave-active {
    transition: all 0.5s ease;
}

.product-list-enter-from {
    opacity: 0;
    transform: translateY(30px);
}

.product-list-leave-to {
    opacity: 0;
    transform: translateY(-30px);
}

/* Effet d'entrée échelonné */
.product-list-enter-active {
    transition-delay: calc(0.1s * v-bind('displayedProducts.length'));
}

/* Effet de survol du bouton */
.ant-btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}
</style>
