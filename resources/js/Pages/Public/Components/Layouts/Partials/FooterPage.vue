<script setup>
import {Link, useForm} from "@inertiajs/vue3";
import {ref, onMounted, onUnmounted, computed, defineProps} from 'vue';
import {info} from "autoprefixer";
// Reactive state
// const email = ref('');
const currentYear = computed(() => new Date().getFullYear());
const form = useForm({
    id: '',
    email: '',
});
const props = defineProps({
    information: {
        type: Object,
        default: {}
    }
})

// Social media icons
const socialMedia = [
    {name: 'Facebook', icon: ['fab', 'facebook-f'], url: '#'},
    {name: 'Whatsapp', icon: ['fab', 'whatsapp'], url: '#'},
    {name: 'Twitter', icon: ['fab', 'twitter'], url: '#'},
];

// Payment methods
const paymentMethods = [
    {name: 'Visa', image: '/img/visa.png'},
    {name: 'MVola', image: '/img/telma.png'},
    {name: 'Orange Money', image: '/img/orange.png'},
    {name: 'Airtel Money', image: '/img/airtel.png'}
];

// Footer links
const footerLinks = [
    {
        title: 'Mon compte',
        links: [
            {name: 'Se connetcer', url: '#'},
            {name: 'S’inscrire', url: '#'},
            {name: 'Order History', url: '#'},
            {name: 'Panier', url: '#'}
        ],
    },
    {
        title: 'Information',
        links: [
            {name: 'Paiement', url: '#'},
            {name: 'Promotions', url: '#'},
            {name: 'Nous contacter', url: '#'},
            {name: 'Événements', url: '#'}
        ],
    },
    {
        title: 'A propos',
        links: [
            {name: 'Entreprise', url: '#'},
            {name: 'Contactez-nous', url: '#'},
            {name: 'Nous contacter', url: '#'},
            {name: 'Point de vente', url: '#'},
            {name: 'FAQ', url: '#'}
        ],
    },
    {
        title: 'Liens rapides',
        links: [
            {name: 'Accueil', url: '#'},
            {name: 'Prét à porter', url: '#'},
            {name: 'chaussures', url: '#'},
            {name: "Sacs", url: '#'},
            {name: 'Accessoires', url: '#'},
            {name: 'À propos de nous', url: '#'},
        ],
    },
    {
        title: 'Categories',
        links: [
            {name: 'T-Shirt', url: '#'},
            {name: 'Prêt a Porter', url: '#'},
            {name: 'Accessoires', url: '#'},
            {name: "Décoration", url: '#'},
            {name: 'Homme', url: '#'},
            {name: 'Femme', url: '#'},
        ],
    },
];

// Methods
const submitEmail = () => {
    form.clearErrors();
    const method = 'post';
    const url = route('newsletter.subscribe');

    form.transform(data => ({
        ...data,
        _method: method === 'put' ? 'PUT' : 'POST'
    })).post(url, {
        onSuccess: () => close(),
        forceFormData: true
    });
};
</script>

<template>
    <!-- Footer -->
    <a-layout-footer class="relative w-full !bg-transparent text-white font-sans z-0">
        <div class="relative pt-24">

            <div class="absolute inset-0 w-full h-full flex items-center justify-center pointer-events-none">
                <svg
                    class="w-full h-full absolute top-0 left-0 right-0 transform scale-y-[-1]"
                    preserveAspectRatio="xMidYMid slice"
                    viewBox="0 0 1920 279"
                    fill="none"
                    xmlns="http://www.w3.org/2000/svg"
                >
                    <path
                        d="M1920 0V223H1227C1185 223 1083.2 230.1 1001.1 268C964.1 285.1 950 279.4 923.6 269.8C803.4 223 692.9 223 692.9 223H0V0H1920Z"
                        fill="#f3f4f6"
                    />
                </svg>
            </div>

            <div class="container relative mx-auto px-4 py-8 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- Contact Rapide -->
                <div>
                    <h3 class="text-lg font-bold text-black mb-4">Contact Rapide</h3>
                    <div class="flex items-center space-x-3 group mb-3">
                        <font-awesome-icon :icon="['fas', 'envelope']"
                                           class="text-primary w-4 h-4 group-hover:text-[#46a426]"/>
                        <span class="text-gray-600 group-hover:text-black transition-colors duration-300">{{information.email}}</span>
                    </div>
                    <div class="flex items-center space-x-3 group mb-3">
                        <font-awesome-icon :icon="['fas', 'phone']"
                                           class="text-primary w-4 h-4 group-hover:text-[#46a426]"/>
                        <span
                            class="text-gray-600 group-hover:text-black transition-colors duration-300">{{information.contact}}</span>
                    </div>
                </div>

                <!-- Nos adresse -->
                <div>
                    <h3 class="text-lg font-bold text-black mb-4">Nos adresse</h3>
                    <div class="flex items-center space-x-3 group max-w-sm">
                        <font-awesome-icon :icon="['fas', 'fa-location-dot']"
                                           class="text-primary text-xl group-hover:text-[#46a426]"/>
                        <span class="text-gray-600 group-hover:text-black transition-colors duration-300">{{information.addresse}}</span>
                    </div>
                </div>

                <!-- Horaires d'ouvertures -->
                <div class="flex flex-col gap-3">
                    <h3 class="text-lg font-bold text-black">Horaires d'ouvertures</h3>
                    <div class="flex items-center space-x-3 group">
                        <font-awesome-icon :icon="['fas', 'cloud-sun']"
                                           class="text-primary w-4 h-4 group-hover:text-[#46a426]"/>
                        <span class="text-gray-600 group-hover:text-black transition-colors duration-300">{{information.visite}}</span>
                    </div>
                    <div class="flex items-center space-x-3 group">
                        <font-awesome-icon :icon="['fas', 'clock']"
                                           class="text-primary w-4 h-4 group-hover:text-[#46a426]"/>
                        <span class="text-gray-600 group-hover:text-black transition-colors duration-300">{{information.heure_ouverture}}</span>
                    </div>
                </div>

                <!-- Newsletter -->
                <div class="relative z-10">
                    <h3 class="text-xl font-semibold mb-2 text-black">Abonnez-vous à notre newsletter</h3>

                    <div class="flex items-center gap-3">
                        <div class="relative w-full">
                            <a-input
                                v-model:value="form.email"
                                type="email"
                                placeholder="Your email address"
                                class="w-full bg-white border border-gray-200 rounded-full py-3 px-4 text-black placeholder-gray-300 focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary transition-all group"
                            />
                            <a-button
                                size="large"
                                type="text"
                                @click="submitEmail"
                                class="flex items-center justify-center absolute -right-4 top-1/2 -translate-y-1/2 w-auto !h-full !border-none bg-primary !text-white hover:!bg-[#46a426] font-medium !rounded-full  transition-all duration-300 overflow-hidden group"
                            >
                                <span>S'abonner</span>
                                <font-awesome-icon :icon="['fas', 'paper-plane']"
                                                   class="ml-2 transform group-hover:translate-x-1 transition-transform duration-300"/>
                            </a-button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="!bg-[#151511]">
            <div class="container mx-auto py-8">
                <!-- Footer Links (Collapse for Mobile) -->
                <div class="md:hidden">
                    <a-collapse accordion :bordered="false" class="mb-8">
                        <a-collapse-panel
                            v-for="(category, idx) in footerLinks" :key="idx"
                            :header="category.title"
                            class="custom-collapse-panel mb-2 rounded-none border-none"
                        >
                            <ul class="list-none px-10 space-y-3  mb-3">
                                <li v-for="link in category.links" :key="link.name" class="group">
                                    <a :href="link.url"
                                       class="inline-flex items-center text-white/50 group-hover:text-white transition-all duration-300">
                                    <span
                                        class="w-0 h-px bg-primary mr-0 group-hover:w-3 group-hover:mr-2 transition-all duration-300"></span>
                                        {{ link.name }}
                                    </a>
                                </li>
                            </ul>
                        </a-collapse-panel>
                    </a-collapse>
                </div>

                <!-- Affichage des liens en version desktop -->
                <div class="hidden md:grid grid-cols-2 lg:grid-cols-6 mb-8">
                    <!-- Logo Section -->
                    <div class="text-center md:text-left space-y-4">
                        <!-- Logo avec animation -->
                        <Link href="/" class="inline-block group">
                            <img
                                :src="information.logo"
                                alt="Artipik Logo"
                                class="h-12 w-auto transition-transform duration-500 group-hover:scale-110"
                            />
                        </Link>

                        <!-- Description -->
                        <p class="text-white/50 leading-relaxed text-sm">
                            Morbi cursus porttitor enim lobortis molestie. Duis gravida turpis dui, eget bibendum magna
                            congue nec.
                        </p>

                        <!-- Contact -->
                        <div class="flex flex-wrap items-center gap-4 mt-4">
                            <div
                                class="py-1.5 px-3 bg-zinc-900 shadow-[0px_1.5px_0px_0px_rgba(32,181,38,1.00)] text-white text-sm font-medium">
                                {{information.contact}}
                            </div>
                        </div>
                    </div>

                    <div v-for="(category, idx) in footerLinks" :key="idx" class="space-y-2">
                        <h3 class="text-lg font-medium relative inline-block">
                            <span class="text-white">{{ category.title }}</span>
                        </h3>
                        <ul class="space-y-3">
                            <li v-for="link in category.links" :key="link.name" class="group">
                                <a href="#"
                                   class="inline-flex items-center text-white/50 group-hover:text-white transition-all duration-300">
                                <span
                                    class="w-0 h-px bg-[#00B207] mr-0 group-hover:w-3 group-hover:mr-2 transition-all duration-300"></span>
                                    {{ link.name }}
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Divider -->
                <div class="h-px bg-gradient-to-r from-transparent via-white/20 to-transparent my-8"></div>

                <!-- Copyright (Centered on Mobile) -->
                <div class="flex flex-col sm:flex-row justify-between items-center gap-3">
                    <div class="space-y-4">
                        <div class="flex flex-wrap gap-3">
                            <a-button
                                type="text"
                                shape="circle"
                                size="large"
                                v-for="(social, index) in socialMedia"
                                :key="index"
                                :href="social.url"
                                :aria-label="social.name"
                                class="group relative bg-transparent hover:!bg-primary hover:border-green-500 text-white flex items-center justify-center overflow-hidden transition-all duration-300"
                            >
                                <font-awesome-icon :icon="social.icon"
                                                   class="text-lg text-white/70 group-hover:text-white relative z-10 group-hover:scale-125 transition-all duration-300"/>
                            </a-button>
                        </div>
                    </div>

                    <p class="text-white/50 text-sm mb-4 sm:mb-0">
                        Loune eCommerce © {{ currentYear }}. Tous droits réservés
                    </p>

                    <!-- Payment Methods (Centered on Mobile) -->
                    <div class="flex items-center justify-center md:justify-end gap-4">
                        <div class="flex flex-wrap items-center gap-2">
                            <a-tooltip v-for="payment in paymentMethods" :key="payment.name" :title="payment.name">
                                <div
                                    class="w-12 h-9 overflow-hidden rounded-md bg-white/5 border border-white/70 flex items-center justify-center hover:bg-white/10 transition-all duration-300">
                                    <img class="w-full h-full object-cover" :src="payment.image" alt=""/>
                                </div>
                            </a-tooltip>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </a-layout-footer>
</template>

<style scoped>
.custom-collapse-panel >>> .ant-collapse-header {
    @apply text-white;
}

.custom-collapse-panel >>> .ant-collapse-header .ant-collapse-header-text {
    @apply font-semibold;
}

.custom-collapse-panel >>> .ant-collapse-content {
    @apply bg-transparent;
}

.custom-collapse-panel >>> .ant-collapse-content-box {
    @apply p-0;
}

.ant-collapse-borderless > .ant-collapse-item {
    @apply border-b !border-b-primary/20;
}
</style>
