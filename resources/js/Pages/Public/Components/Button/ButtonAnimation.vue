<!-- ButtonComponent.vue -->
<script setup>
import {defineProps} from 'vue';

const props = defineProps({
    label: {
        type: String,
        default: 'Acheter'
    },
    icon: {
        type: String,
        default: 'fa-solid fa-arrow-right'
    },
    backgroundColor: {
        type: String,
        default: 'bg-yellow-500'
    },
    hoverColor: {
        type: String,
        default: 'hover:bg-yellow-400'
    },
    borderColor: {
        type: String,
        default: 'border-yellow-500'
    },
    textColor: {
        type: String,
        default: 'text-black'
    }
});
</script>

<template>
    <a-button
        size="large"
        type="primary"
        :class="[
      backgroundColor,
      hoverColor,
      borderColor,
      textColor,
      '!rounded-full flex items-center gap-2 h-auto anima-button'
    ]"
    >
        <span class="text-base">{{ label }}</span>
        <font-awesome-icon :icon="icon"/>
    </a-button>
</template>

<style scoped>
/* Professional hover animation for buttons */
.anima-button {
    transition: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
    position: relative;
    overflow: hidden;
}

.anima-button::after {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
    transition: left 0.7s;
}

.anima-button:hover::after {
    left: 100%;
}
</style>
