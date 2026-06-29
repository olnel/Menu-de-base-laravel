<script setup>

</script>

<template>
    <a-card class="main-card w-full">
        <slot/>
    </a-card>
</template>

<style>

.main-card{
    @apply bg-white shadow border-0 p-4 sm:p-6;
}

.main-card .ant-card-head{
    @apply  border-0 p-0 min-h-fit mb-5;
}

.main-card .ant-card-head-title{
    @apply font-medium uppercase;
}

.main-card .ant-card-meta-description{
    @apply !text-gray-600;
}

.main-card .ant-card-body{
    @apply  p-0;
}

.main-card .card-description{
    @apply text-gray-600;
}

</style>
