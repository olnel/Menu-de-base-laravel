<template>
    <a-form-item
        id="form-item"
        :help="help"
        :validate-status="help ? 'error' : ''"
    >
        <div class="mx-0">
            <slot></slot>
        </div>
    </a-form-item>
</template>

<script setup>
import { defineProps } from "vue";

const props = defineProps({
    help: String,
});
</script>

<style>
#form-item .ant-form-item-label {
    padding-bottom: 5px !important;
    @apply block font-medium;
}

#form-item .ant-input,
#form-item .ant-input-password {
    @apply bg-transparent;
}
</style>
