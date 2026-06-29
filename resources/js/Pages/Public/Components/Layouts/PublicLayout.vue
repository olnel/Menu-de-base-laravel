<script setup>
import { defineProps } from 'vue'
import { Head, usePage } from '@inertiajs/vue3'
import HeaderPage from '@/Pages/Public/Components/Layouts/Partials/HeaderPage.vue'
import FooterPage from '@/Pages/Public/Components/Layouts/Partials/FooterPage.vue'
import Breadcrumb from "@/Pages/Public/Components/Breadcrumb.vue";

const props = defineProps({
    title: String,
    selectedMenu: {
        type: String,
        default: "Home",
    },
    openMenu: {
        type: String,
        default: "",
    },
    information: {
        type: Object,
        default: {}
    }
})
</script>

<template>
    <Head :title="title" />
    <div class="min-h-screen" v-bind="$attrs">
        <header-page  :selected-keys="[selectedMenu]" :open-keys="[openMenu]"/>

        <div>
            <slot />
        </div>
        <FooterPage :information="information" />
    </div>
</template>

<style scoped>
</style>
