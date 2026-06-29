<script setup>
import { defineProps } from 'vue';
import antdvTheme from "../../Theme/antdv-theme.js";

const props = defineProps({
        src: {
            type: String,
            default: ''
        },
        color: {
            type: String,
            default: "#444"
        },
        size: {
            type: Number,
            default: 50
        },
        icon: {
            logo: String,
            default: 'fa-circle-user'
        },
    })
</script>

<template>
    <a-avatar
        :src="src"
        :size="size"
        class="border-0 bg-white"
    >
        <template #icon>
            <font-awesome-icon
                :style="{'color': color}"
                class="w-full h-full"
                :icon="`fa-solid ${icon}`" />
        </template>
    </a-avatar>
</template>

<style scoped>

</style>
