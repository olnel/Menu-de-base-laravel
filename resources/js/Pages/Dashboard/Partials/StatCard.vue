<script setup>

defineProps({
    title: String,
    value: [String, Number],
    icon: String,
    desc: String
});

</script>

<template>
    <a-card :bordered="false" class="bg-white main-rounded main-shadow">
        <div class="flex justify-between">
            <div>
                <p class="text-sm font-medium text-text-primary/75">{{ title }}</p>
                <p class="text-2xl font-semibold mt-2">{{ value }}</p>
            </div>
            <div class="h-12 w-12 rounded-full bg-primary/20 flex items-center justify-center">
                <font-awesome-icon :icon="icon" class="h-6 w-6 text-primary" />
            </div>
        </div>
        <div v-if="desc" class="mt-4 flex items-center">
            <span class="text-sm text-text-primary/70">{{ desc }}</span>
        </div>
    </a-card>
</template>
