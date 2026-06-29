<template>
    <div
        class="relative w-full h-32 overflow-hidden inset-0 bg-cover bg-center brightness-110 bg-no-repeat"
        style="background-image: url('/img/becca-mchaffie.jpg');"
    >
        <!-- Overlay -->
        <div class="absolute inset-0 bg-gradient-to-r from-black/90 to-black/0"></div>

        <!-- Breadcrumb -->
        <div class="container mx-auto relative z-10 flex items-center h-full px-6">
            <a-breadcrumb :separator="customSeparator" class="!text-white">
                <!-- Icône dynamique (Home) -->
                <a-breadcrumb-item v-if="showHomeIcon">
                    <Link :href="homeRoute" class="!text-gray-400">
                        <font-awesome-icon :icon="icon" />
                    </Link>
                </a-breadcrumb-item>

                <!-- Autres items -->
                <a-breadcrumb-item v-for="(item, index) in items" :key="index">
                    <Link
                        :href="item.route"
                        class="cursor-pointer"
                        :class="[
                            index === items.length - 1
                                ? '!text-green-400 font-medium'
                                : '!text-gray-300 hover:!text-white',
                        ]"
                        preserve-scroll
                    >
                        {{ item.text }}
                    </Link>
                </a-breadcrumb-item>
            </a-breadcrumb>
        </div>
    </div>
</template>

<script setup>
import {h, resolveComponent} from "vue";
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    items: {
        type: Array,
        default: () => [
            { text: 'Category', route: '/category' },
            { text: 'Accessoires', route: '/category/accessoires' },
            { text: 'For Sale', route: '/category/accessoires/for-sale' },
        ],
    },
    icon: {
        type: [String, Array],
        default: () => ['fas', 'home'],
    },
    showHomeIcon: {
        type: Boolean,
        default: true,
    },
    homeRoute: {
        type: String,
        default: '/',
    }
});

const customSeparator = h('span', { class: 'mx-2 !text-gray-400' }, [
    h(resolveComponent('font-awesome-icon'), { icon: ['fas', 'angle-right'] })
]);
</script>
