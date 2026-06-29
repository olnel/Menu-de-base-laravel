<template>
    <Layout :title="title">

        <PortailHeaderBar v-if="!noHeader" :title="title" :subtitle="subtitle" />

        <a-layout id="content" :has-sider="false" :class="[noPadding ? '' : '!bg-gray-100 bg-fixed']">
            <a-layout-content class="flex flex-1" :class="[noPadding ? '' : '!bg-gray-100']">
                <div class="flex flex-col mx-auto w-full" :class="[noPadding ? '' : 'px-2 lg:px-6']">

                    <div :class="[noPadding ? '' : 'my-6', 'flex-1']">
                        <slot></slot>
                    </div>

                </div>
            </a-layout-content>
        </a-layout>

        <footer v-if="!noPadding" class="bg-white border-t border-gray-200 py-3">
            <div class="px-8 text-center text-xs text-gray-400">
                © {{ new Date().getFullYear() }} TransMada – Transport Management System
            </div>
        </footer>

    </Layout>
</template>

<script setup>
import Layout from "@/Layouts/Layout.vue";
import PortailHeaderBar from "@/Layouts/Partials/PortailHeaderBar.vue";
import {defineProps} from "vue";

defineProps({
    client:   Object,
    title:    String,
    subtitle: String,
    noPadding: {
        type: Boolean,
        default: false
    },
    noHeader: {
        type: Boolean,
        default: false
    }
});

</script>
