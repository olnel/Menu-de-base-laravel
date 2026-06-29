<script setup lang="ts">
import {computed, ref} from 'vue';
import {CloseOutlined} from '@ant-design/icons-vue';
import ButtonAnimation from "@/Pages/Public/Components/Button/ButtonAnimation.vue";
import {Link} from "@inertiajs/vue3";

// Props
const props = defineProps({
    visible: {
        type: Boolean,
        required: true
    }
});

// Emits
const emit = defineEmits(['close']);

// Cart data
const cartItems = ref([
    {
        id: 1,
        name: 'Chaussure Rouge',
        brand: 'Loune',
        price: 12.00,
        quantity: 1,
        image: '/img/slider-1.jpg'
    },
    {
        id: 2,
        name: 'Chaussure Beige',
        brand: 'Loune',
        price: 12.00,
        quantity: 1,
        image: '/img/slider-1.jpg'
    }
]);

const cartTitle = computed(() => {
    const count = cartItems.value.length;
    const label = count > 1 ? 'Produits' : 'Produit';
    return `Carte d'achat (${count} ${label})`;
});

// Computed total
const cartTotal = ref(cartItems.value.reduce((sum, item) => sum + (item.price * item.quantity), 0));

// Methods
const onClose = () => {
    emit('close');
};

const removeItem = (itemId: number) => {
    cartItems.value = cartItems.value.filter(item => item.id !== itemId);
    cartTotal.value = cartItems.value.reduce((sum, item) => sum + (item.price * item.quantity), 0);
};
</script>
<template>
    <a-drawer
        :title="cartTitle"
        :visible="visible"
        :closable="true"
        @close="onClose"
        placement="right"
        :width="400"
    >
        <div class="flex flex-col h-full">
            <div class="flex-grow">
                <!-- Cart Items -->
                <div v-for="item in cartItems" :key="item.id" class="p-4 border-b">
                    <div class="flex items-center">
                        <div class="w-16 h-16 bg-gray-100 rounded overflow-hidden mr-4">
                            <img :src="item.image" :alt="item.name" class="object-cover w-full h-full"/>
                        </div>
                        <div class="flex-grow">
                            <h3 class="text-lg font-medium">{{ item.name }}</h3>
                            <p class="text-gray-600">
                                {{ item.quantity }} {{ item.quantity > 1 ? 'Chaussures' : 'Chaussure' }}
                                <span class="text-green-600">{{ item.brand }}</span> × {{ item.price.toFixed(2) }}
                            </p>
                        </div>
                        <a-button
                            shape="circle"
                            @click="removeItem(item.id)"
                            class="group flex items-center justify-center border !border-gray-300 transition-all duration-300 hover:!border-red-600 hover:shadow-md"
                        >
                            <template #icon>
                                <font-awesome-icon
                                    icon="xmark"
                                    class="text-gray-500 transition-colors duration-300 group-hover:text-red-500"
                                />
                            </template>
                        </a-button>
                    </div>
                </div>
            </div>

            <!-- Cart Footer -->
            <div class="mt-auto">
                <div class="p-4 border-t flex justify-between items-center">
                    <span class="text-lg font-medium">
                        {{ cartItems.length }} {{ cartItems.length > 1 ? 'Produits' : 'Produit' }}
                    </span>
                    <span class="text-lg font-medium">{{ cartTotal.toFixed(2) }} Ar</span>
                </div>
                <div class="p-4 flex flex-col gap-3">
                    <Link href="panierVerifier">
                        <ButtonAnimation
                            block
                            label="Vérifier"
                            icon="check"
                            backgroundColor="!bg-green-600"
                            hoverColor="hover:!bg-green-700"
                            borderColor="!border-green-600"
                            textColor="!text-white"
                            class="flex items-center justify-center font-medium"
                            @click=""
                        />
                    </Link>

                    <Link href="panierView">
                        <ButtonAnimation
                            block
                            label="Aller au panier"
                            icon="shopping-cart"
                            backgroundColor="!bg-yellow-50"
                            hoverColor="hover:!bg-yellow-100"
                            borderColor="!border-green-400"
                            textColor="!text-green-600"
                            class="flex items-center justify-center font-medium"
                            @click=""
                        />
                    </Link>
                </div>
            </div>
        </div>
    </a-drawer>
</template>

<style>
/* Additional custom styles */
.ant-drawer-body {
    padding: 0;
    display: flex;
    flex-direction: column;
    height: 100%;
}

.ant-drawer-header {
    border-bottom: 1px solid #f0f0f0;
}

/* Override Ant Design with some Tailwind customizations */
.ant-btn-primary {
    background-color: #047857;
    border-color: #047857;
}

.ant-btn-primary:hover {
    background-color: #065f46;
    border-color: #065f46;
}
</style>
