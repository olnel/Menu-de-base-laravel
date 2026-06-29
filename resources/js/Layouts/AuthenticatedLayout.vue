<script setup>
import {defineProps, ref} from 'vue';
import {router, usePage} from '@inertiajs/vue3';
import Layout from "@/Layouts/Layout.vue";
import SideBar from "@/Layouts/Partials/SideBar.vue";
import HeaderBar from "@/Layouts/Partials/HeaderBar.vue";

const siderCollapsed = ref(false)
const props = defineProps({
    title: String,
    backTo: {
        type: String,
        default: null
    },
    selectedMenu: {
        type: String,
        default: ''
    },
    noPadding: {
        type: Boolean,
        default: false
    }
})
const page = usePage();

const toggleSidebar = () => {
    // siderCollapsed.value = !siderCollapsed.value
    if (page.url === '/'){
        return
    }
    router.get('/');
}

</script>

<template>
    <Layout :title="title">

        <HeaderBar
            :title="title"
            :selected-menu="selectedMenu"
            :toggleSidebar="toggleSidebar"
            :backTo="backTo"
        />

        <a-layout id="content" :has-sider="false" class="!bg-gray-100 bg-fixed">

<!--            <side-bar v-model:collapsed="siderCollapsed" :selected-menu="selectedMenu"/>-->

            <a-layout-content class="flex !bg-gray-100 flex-1">
                <div class="flex flex-col mx-auto w-full" :class="noPadding ? '' : 'px-2 lg:px-6'">

                    <div v-if="!noPadding" class="flex gap-3 items-center flex-col sm:flex-row sm:justify-between">
                        <div class="action-container flex gap-3 flex-col sm:flex-row w-full sm:w-auto">
                            <slot name="export"></slot>
                            <slot name="actions"></slot>
                        </div>
                    </div>

                    <div class="flex-1" :class="noPadding ? '' : 'my-6'">
                        <slot></slot>
                    </div>
                </div>
            </a-layout-content>
        </a-layout>
    </Layout>

</template>

<style>
.action-container > * {
    @apply w-full sm:w-auto;
}

</style>
