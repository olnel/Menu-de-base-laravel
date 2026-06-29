<script setup>

const props = defineProps({
    title: String,
})

</script>

<template>
    <div class="toolbar-container flex w-full bg-white main-shadow main-rounded">
        <a-space-compact class="left">
            <div v-if="title" class="flex items-center px-5">
                <h6>{{ title }}</h6>
            </div>

            <slot name="left"/>
        </a-space-compact>

        <a-space-compact class="right ml-auto">
            <slot />
        </a-space-compact>
    </div>
</template>

<style>

.toolbar-container > * > * {
    @apply !rounded-none border-x border-gray-200 h-full last:border-r-0 first:border-l-0;
}

.toolbar-container > * > .ant-btn {
    @apply !p-5 hover:border-gray-200 hover:bg-gray-100 hover:text-text-primary;
}

.toolbar-container > * > .ant-btn span {
    @apply hidden sm:block;
}

</style>
