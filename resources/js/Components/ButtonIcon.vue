<script setup>

const props = defineProps({
    text: String,
    icon: String,
    color: String,
    loading: {
        type: Boolean,
        default: () => false,
    }
})

</script>

<template>
    <a-button size="large" ghost :loading="loading" :title="text" class=" !py-3 flex gap-2 items-center justify-center !border !border-primary transition-all duration-300" :style="color ? {backgroundColor: color} : {}">
        <template #icon>
            <font-awesome-icon class="text-[15px]" v-if="icon && !loading" :icon="icon"/>
        </template>
        <span v-if="text" class="capitalize line-clamp-1" style="line-height: normal;">{{text}}</span>
    </a-button>
</template>
