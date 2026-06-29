<script setup>
defineProps({
    title: {
        type: String,
        required: true
    },
    color: {
        type: String,
        default: 'bg-primary'
    },
    textColor: {
        type: String,
        default: 'text-black' // tu peux mettre 'text-primary' ou autre
    }
})
</script>

<template>
    <div class="flex items-center">
        <div :class="['w-3 h-8 rounded-sm mr-2', color]"></div>
        <h1
            :class="[
        'relative inline-block text-3xl font-serif',
        textColor,
        'after:content-[\'\'] after:absolute after:-bottom-5 after:rounded-l-full after:-left-5 after:h-1 after:z-0',
        `after:${color}`
      ]"
        >
            {{ title }}
        </h1>
    </div>
</template>
