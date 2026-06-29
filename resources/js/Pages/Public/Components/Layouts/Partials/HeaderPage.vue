<script setup>
import {Link} from '@inertiajs/vue3';
import {ref, onMounted, onUnmounted} from "vue";
import CartDrawer from "@/Pages/Public/Components/Drawer/CartDrawer.vue";
import {LOUINE_MENU} from "@/Pages/Public/Components/constants/menu-data.js";

const searchQuery = ref('');
const cartItemCount = ref(1);
const cartTotal = ref('00,00');
const isTopBarVisible = ref(true);
const isMobileMenuOpen = ref(false);
const isScrollingDown = ref(false);
const isSearchVisible = ref(false);
const showScrollTop = ref(false);
const lastScrollY = ref(0);
const isCartDrawerVisible = ref(false);

// Variables pour le menu
// const selectedKeys = ref(['home']);
const localOpenKeys = ref([]);

const props = defineProps({
    selectedKeys: {
        type: Array,
        default: () => [],
    },
    openKeys: {
        type: Array,
        default: () => [],
    },
});

const handleOpenChange = (keys) => {
    localOpenKeys.value = keys;
};

const toggleMobileMenu = () => {
    isMobileMenuOpen.value = !isMobileMenuOpen.value;
    if (isMobileMenuOpen.value) {
        document.body.classList.add('overflow-hidden');
    } else {
        document.body.classList.remove('overflow-hidden');
    }
};

const toggleSearch = () => {
    isSearchVisible.value = !isSearchVisible.value;
    if (isSearchVisible.value) {
        setTimeout(() => {
            document.querySelector('.search-input input').focus();
        }, 300);
    }
};

const toggleCartDrawer = () => {
    isCartDrawerVisible.value = !isCartDrawerVisible.value;
};

const handleScroll = () => {
    const currentScrollY = window.scrollY;
    isScrollingDown.value = currentScrollY > lastScrollY.value && currentScrollY > 50;
    showScrollTop.value = currentScrollY > 300;
    isTopBarVisible.value = !isScrollingDown.value || currentScrollY < 50;
    lastScrollY.value = currentScrollY;
};

const scrollToTop = () => {
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
};

const scrollToAnchor = (id) => {
    const el = document.getElementById(id);
    if (el) {
        el.scrollIntoView({ behavior: 'smooth' });
    }
};


onMounted(() => {
    window.addEventListener('scroll', handleScroll);
});

onUnmounted(() => {
    window.removeEventListener('scroll', handleScroll);
});
</script>

<template>
    <!-- Top Bar avec animation de hauteur en fonction du scroll -->
    <div class="relative z-50 bg-white border-b py-3 px-6 flex justify-end text-sm transition-all duration-300"
         :class="{'max-h-0 opacity-0 overflow-hidden': !isTopBarVisible && isScrollingDown, 'max-h-12 opacity-100': isTopBarVisible || !isScrollingDown}">
        <div class="container mx-auto flex md:justify-end items-center gap-2 text-sm justify-center">
            <Link :href="route('login')" class="text-gray-600 hover:text-primary transition-colors duration-300">
                Se connecter
            </Link>

            <div class="h-4 border-l border-gray-300 mx-1"></div>

            <Link :href="route('register')" class="text-gray-600 hover:text-primary transition-colors duration-300">
                S'inscrire
            </Link>
        </div>
    </div>

    <div
        class="sticky top-0 z-50 transition-transform duration-500 ease-in-out"
        :class="{'shadow-md': isScrollingDown && !isMobileMenuOpen,'shadow-none sm:shadow-none border-b border-gray-50 sm:border-none': !isScrollingDown || isMobileMenuOpen}"
    >
        <!-- Header -->
        <header class="bg-white py-4 px-4">
            <div class="container mx-auto flex items-center justify-between">
                <!-- Bouton menu mobile avec animation de rotation -->
                <a-button
                    size="large"
                    type="text"
                    @click="toggleMobileMenu"
                    class="md:hidden text-gray-700 group rounded-md !bg-transparent border-0 cursor-pointer transition-all duration-300"
                    aria-label="Toggle Sidebar"
                >
                    <font-awesome-icon
                        :icon="!isMobileMenuOpen ? 'bars' : 'times'"
                        class="!me-0 text-xl transition-transform duration-300 group-hover:!text-primary"
                        :class="{'rotate-90': isMobileMenuOpen}"
                    />
                </a-button>

                <!-- Logo avec animation au survol -->
                <Link href="/" class="flex-shrink-0 hidden md:block transform hover:scale-105 transition-transform duration-300">
                    <img src="/img/logo_loune.jpg" alt="Louine" class="h-12"/>
                </Link>

                <!-- Search -->
                <div class="hidden md:block flex-grow max-w-lg mx-6">
                    <a-input-search
                        placeholder="Recherche..."
                        size="large"
                        :style="{ width: '100%' }"
                        v-model:value="searchQuery"
                        class="search-input-desktop"
                    >
                        <template #enterButton>
                            <a-button type="text" class="bg-primary hover:bg-green-700 !text-white transition-colors duration-300">
                                <span class="flex items-center">
                                    <span>Rechercher</span>
                                </span>
                            </a-button>
                        </template>
                    </a-input-search>
                </div>

                <!-- User Actions -->
                <div class="flex items-center gap-4">
                    <!-- Bouton recherche mobile avec animation -->
                    <a-button
                        size="large"
                        type="text"
                        @click="toggleSearch"
                        class="md:hidden text-gray-600 rounded-md !bg-transparent border-0 cursor-pointer group transition-all duration-300"
                        aria-label="Toggle Search"
                    >
                        <font-awesome-icon
                            :icon="isSearchVisible ? 'times' : 'search'"
                            class="!me-0 text-xl group-hover:text-primary transition-all duration-300"
                            :class="{'rotate-90': isSearchVisible}"
                        />
                    </a-button>

                    <!-- Séparateur vertical pour mobile -->
                    <div class="md:hidden h-6 border-l border-gray-300"></div>

                    <!-- Icône utilisateur avec animation -->
                    <Link href="/" class="text-gray-600 hover:text-primary transition-colors duration-300 relative">
                        <div class="relative p-1 rounded-full hover:bg-gray-100 transition-all duration-300">
                            <font-awesome-icon
                                icon="user"
                                class="text-xl transform hover:scale-110 transition-transform duration-300"
                            />
                        </div>
                    </Link>

                    <!-- Séparateur vertical pour mobile -->
                    <div class="md:hidden h-6 border-l border-gray-300"></div>

                    <!-- Panier avec animation -->
                    <div class="flex items-center gap-4">
                        <a @click="toggleCartDrawer" class="hover:text-primary transition-colors duration-300 relative inline-block group cursor-pointer">
                            <div class="relative w-fit p-1 rounded-full hover:bg-gray-100 transition-all duration-300">
                                <a-badge
                                    :count="cartItemCount"
                                    class="transform transition-transform duration-300"
                                >
                                    <font-awesome-icon
                                        icon="shopping-cart"
                                        class="text-gray-600 text-xl transform group-hover:text-primary group-hover:scale-110 group-hover:rotate-6 transition-all duration-300"
                                    />
                                </a-badge>
                            </div>
                        </a>

                        <div class="hidden md:flex flex-col text-xs">
                            <span class="text-gray-600 text-base">Panier:</span>
                            <span class="text-base font-bold transition-colors duration-300 text-primary">{{ cartTotal }}€</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mobile Search (expandable) avec animation améliorée -->
            <div class="container w-full mx-auto overflow-hidden transition-all duration-300 md:hidden"
                 :class="{ 'max-h-12 opacity-100 mt-3 mb-3': isSearchVisible, 'max-h-0 opacity-0 mt-0': !isSearchVisible }"
            >
                <a-input-search
                    size="large"
                    placeholder="Recherche..."
                    :style="{ width: '100%' }"
                    v-model:value="searchQuery"
                    class="search-input"
                >
                    <template #enterButton>
                        <a-button class="!bg-primary hover:!bg-green-700 !border-primary transition-all duration-300">
                            <font-awesome-icon icon="search" class="!me-0 text-xl text-white transition-transform duration-300"/>
                        </a-button>
                    </template>
                </a-input-search>
            </div>
        </header>

        <!-- Main navigation -->
        <nav class="hidden md:block bg-[#333333] py-2">
            <div class="container mx-auto flex items-center">
                <div class="flex-grow">
                    <a-menu
                        mode="horizontal"
                        theme="dark"
                        :selectedKeys="selectedKeys"
                        :openKeys="localOpenKeys"
                        @openChange="handleOpenChange"
                        class="!border-0 bg-transparent"
                    >
                        <template v-for="(menu, index) in LOUINE_MENU">
                            <a-sub-menu
                                v-if="menu.sous_menu && menu.sous_menu.length > 0"
                                :key="menu.key ? menu.key : 'E-' + index"
                            >
                                <template #title>
                                    <font-awesome-icon :icon="menu.icon" class="!text-xs !me-2"/>
                                    <span class="">{{ menu.title }}</span>
                                </template>

                                <a-menu-item
                                    v-for="sous in menu.sous_menu"
                                    :key="sous.key"
                                >
                                    <Link :href="sous.route">
                                        <font-awesome-icon :icon="sous.icon" class="!text-xs !me-2"/>
                                        <span class="">{{ sous.title }}</span>
                                    </Link>
                                </a-menu-item>
                            </a-sub-menu>

                            <a-menu-item
                                v-else
                                :key="menu.key"
                                class="menu-item"
                            >
                                <a
                                    v-if="menu.key === 'a-propos'"

                                    @click.prevent="scrollToAnchor('a-propos')"
                                >
                                    <font-awesome-icon :icon="menu.icon" class="!text-xs !me-2" />
                                    <span>{{ menu.title }}</span>
                                </a>

                                <Link
                                    v-else
                                    :href="menu.route"
                                >
                                    <font-awesome-icon :icon="menu.icon" class="!text-xs !me-2" />
                                    <span>{{ menu.title }}</span>
                                </Link>
                            </a-menu-item>
                        </template>
                    </a-menu>
                </div>

                <div class="ml-0">
                    <a-button type="text"
                              class="group !p-0 text-white hover:!text-green-500 flex items-center gap-2 ml-auto transition duration-300">
                        <font-awesome-icon icon="fa-solid fa-phone-volume"
                                           class="text-lg transform group-hover:rotate-12 group-hover:scale-110 transition-transform duration-300"/>
                        <span class="font-medium text-base">(+261) 34 00 000 01</span>
                    </a-button>
                </div>
            </div>
        </nav>
    </div>

    <!-- Menu mobile slide-in -->
    <div class="md:hidden fixed inset-0 bg-black bg-opacity-50 z-50 transition-opacity duration-300"
         :class="{'opacity-100 pointer-events-auto': isMobileMenuOpen, 'opacity-0 pointer-events-none': !isMobileMenuOpen}"
         @click="toggleMobileMenu">
    </div>

    <div class="md:hidden fixed top-0 left-0 h-full w-3/4 max-w-xs bg-[#333333] z-50 shadow-xl transform transition-transform duration-300 ease-in-out"
         :class="{'translate-x-0': isMobileMenuOpen, '-translate-x-full': !isMobileMenuOpen}">
        <div class="p-4 border-b flex justify-between items-center">
            <img src="/img/logo_loune.jpg" alt="Louine" class="h-10"/>
            <a-button
                type="text"
                @click="toggleMobileMenu"
                class="text-white !bg-transparent border-0 group transition-all duration-300"
            >
                <font-awesome-icon icon="times" class="text-2xl group-hover:text-primary" />
            </a-button>
        </div>

        <div class="overflow-y-auto h-full pb-20">
            <a-menu
                mode="inline"
                theme="dark"
                :selectedKeys="selectedKeys"
                :openKeys="localOpenKeys"
                @openChange="handleOpenChange"
                class="!border-0 bg-transparent"
            >
                <template v-for="(menu, index) in LOUINE_MENU">
                    <a-sub-menu
                        v-if="menu.sous_menu && menu.sous_menu.length > 0"
                        :key="menu.key ? menu.key : 'E-' + index"
                    >
                        <template #title>
                            <font-awesome-icon :icon="menu.icon" class="!text-xs !me-2"/>
                            <span class="">{{ menu.title }}</span>
                        </template>

                        <a-menu-item
                            v-for="sous in menu.sous_menu"
                            :key="sous.key"
                        >
                            <Link :href="sous.route">
                                <font-awesome-icon :icon="sous.icon" class="!text-xs !me-2"/>
                                <span class="">{{ sous.title }}</span>
                            </Link>
                        </a-menu-item>
                    </a-sub-menu>

                    <a-menu-item
                        v-else
                        :key="menu.key"
                        class="menu-item"
                    >
                        <Link :href="menu.route">
                            <font-awesome-icon :icon="menu.icon" class="!text-xs !me-2"/>
                            <span class="">{{ menu.title }}</span>
                        </Link>
                    </a-menu-item>
                </template>
            </a-menu>

            <div class="p-4 border-t mt-4">
                <a-button size="large" type="primary" block class="mb-3 bg-primary hover:bg-green-700 border-primary flex items-center gap-2 justify-center">
                    <template #icon><font-awesome-icon icon="user" /></template>
                    <span>Se connecter</span>
                </a-button>

                <a-button size="large" block class="!border-primary text-primary flex items-center gap-2 justify-center">
                    <template #icon><font-awesome-icon icon="user-plus" /></template>
                    <span>S'inscrire</span>
                </a-button>
            </div>

            <div class="p-4 border-t group text-white hover:!text-green-500 flex items-center justify-center gap-2 ml-auto transition duration-300">
                <font-awesome-icon icon="fa-solid fa-phone-volume"
                                   class="text-lg transform group-hover:rotate-12 group-hover:scale-110 transition-transform duration-300"/>
                <span class="font-medium text-base">(+261) 34 00 000 01</span>
            </div>
        </div>
    </div>

    <CartDrawer :visible="isCartDrawerVisible" @close="toggleCartDrawer" />

    <!-- Scroll to top button -->
    <a-button
        v-show="showScrollTop"
        @click="scrollToTop"

        type="text"
        size="large"
        shape="circle"
        class="fixed bottom-6 right-6 z-[9999] rounded-full flex items-center justify-center bg-gradient-to-r from-[#00B207] to-primary !text-white shadow-lg transform transition-all duration-300 hover:scale-110 active:scale-95"
    >
        <font-awesome-icon icon="arrow-up" class="text-xl" />
    </a-button>
</template>

<style>
.ant-badge-count {
    @apply !bg-primary !transition-transform !duration-300;
}

.ant-badge:hover .ant-badge-count {
    @apply !scale-110 !bg-primary;
}

.ant-menu {
    @apply !bg-transparent;
}



.ant-menu-submenu-selected > .ant-menu-submenu-title {
    @apply !text-green-500;
}

.ant-menu-horizontal >.ant-menu-submenu-selected {
    @apply !text-green-500 !bg-transparent;
}

.ant-menu-dark.ant-menu-horizontal > .ant-menu-item:hover,
.ant-menu-dark.ant-menu-horizontal > .ant-menu-submenu:hover {
    @apply !bg-transparent !text-green-500;
}

.ant-menu-dark.ant-menu-submenu > .ant-menu {
    @apply !bg-[#333333] !rounded-sm;
}

.ant-menu-dark .ant-menu-item-selected {
    @apply !text-green-500 !bg-transparent;
}
.ant-btn {
    @apply !transition-all !duration-300 !ease-in-out;
}

.ant-btn:active{
    @apply !scale-95;
}

.ant-menu-inline .ant-menu-item:hover,
.ant-menu-inline .ant-menu-submenu-title:hover {
    @apply !text-primary !bg-emerald-100/20;
}

.search-input .ant-input,
.search-input-desktop .ant-input {
    @apply !transition-all !duration-300 !border-gray-300;
}

.search-input .ant-input:focus,
.search-input-desktop .ant-input:focus {
    @apply !border-primary !ring-1 !ring-primary/50;
}

.ant-input-group-addon {
    @apply !bg-primary !border-primary !transition-all !duration-300;
}

.ant-input-search-button:hover {
    @apply !bg-primary !border-primary;
}
</style>
