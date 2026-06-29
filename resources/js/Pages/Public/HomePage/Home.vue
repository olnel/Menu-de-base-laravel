<script setup>


import HeroPage from "@/Pages/Public/HomePage/HeroPage.vue";
import PublicLayout from "@/Pages/Public/Components/Layouts/PublicLayout.vue";
import FeaturePage from "@/Pages/Public/HomePage/FeaturePage.vue";
import Nouveaute from "@/Pages/Public/HomePage/Nouveaute.vue";
import DecouvreProduct from "@/Pages/Public/HomePage/DecouvreProduct.vue";
import EvenementPage from "@/Pages/Public/HomePage/EvenementPage.vue";
import AboutPage from "@/Pages/Public/HomePage/AboutPage.vue";
import PointVente from "@/Pages/Public/HomePage/PointVente.vue";
import TestimonialPage from "@/Pages/Public/HomePage/TestimonialPage.vue";
import PartnerPage from "@/Pages/Public/HomePage/PartnerPage.vue";


const props = defineProps({
    feature: {
        type: Object,
        default: () => {}
    },
    heropage: {
        type: Object,
        default: () => {}
    },
    nouveaute: {
        type: Object,
        default: () => {}
    },
    event: {
        type: Object,
        default: () => {}
    },
    about: {
        type: Array,
        default: () => [
            {titre_principal: null, description: null, img: null, img_2: null, bouton_label: null, }
        ]
    },
    point_de_vente: {
        type: Object,
        default: () => {}
    },
    boutique: {
        type: Object,
        default: () => {}
    },
    partner: {
        type: Object,
        default: () => []
    },
    information: {
        type: Object,
        default: () => []
    },
})

console.log("DATA FAQ",props.faq);
</script>

<template>
    <PublicLayout title="Accueil" selected-menu="home"
                  class="bg-white"
                :information="information"
    >

        <hero-page :data="heropage"/>
        <feature-page :data="feature"/>
        <Nouveaute :data="nouveaute"/>
        <decouvre-product/>
        <evenement-page :data="event"/>
        <AboutPage :data="about"/>
        <point-vente :point_de_vente="point_de_vente" :boutique="boutique"/>
        <testimonial-page/>
        <partner-page :partners="partner"/>
    </PublicLayout>
</template>

<style scoped>

</style>
