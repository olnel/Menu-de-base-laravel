<template>
    <div class="flex items-center" :class="{ 'mb-16': variant === 'large' }">
        <div
            :class="[
            'bg-primary flex-shrink-0',
            variant === 'large' ? 'h-6 w-px sm:h-px sm:w-6 flex-grow sm:flex-grow-0' : 'h-1 w-4 sm:h-px sm:w-6'
          ]"
        ></div>
        <h1
            :class="[
            'font-serif relative',
            variant === 'large' ? 'text-5xl mx-6' : 'text-3xl mx-4'
          ]"
        >
          <span class="bg-gradient-to-r from-primary to-green-500 bg-clip-text text-transparent">
            {{ title }}
          </span>
            <span
                :class="[
                  'absolute left-0 bg-primary',
                  variant === 'large' ? '-bottom-2 w-16 h-1' : '-bottom-2 w-5 h-1'
                ]"
            ></span>
        </h1>
    </div>
</template>

<script setup>
defineProps({
    title: {
        type: String,
        required: true,
    },
    variant: {
        type: String,
        default: 'large',
    },
})
</script>
