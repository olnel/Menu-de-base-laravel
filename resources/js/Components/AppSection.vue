<script setup>

const props = defineProps({
    title: String,
    description: String,
})

</script>

<template>
    <section>
        <header>
            <h2 class="text-lg font-medium text-gray-900">
                {{ title }}
            </h2>

            <div class="mt-1 text-sm text-gray-600">
                <slot name="description" v-if="$slots.description" />
                <p v-else>{{ description }}</p>
            </div>
        </header>

        <div class="mt-5">
            <slot />
        </div>
    </section>
</template>
