<template>
    <div class="relative group">
        <div class="w-[40px] h-[40px] rounded-md overflow-hidden">
            <img
                :src="src"
                :alt="alt"
                class="w-full h-full object-cover cursor-pointer"
                @click="handlePreview"
            />
        </div>
        <div
            class="absolute inset-0 bg-black/25 opacity-0 group-hover:opacity-100 transition-opacity duration-200 flex items-center justify-center cursor-pointer"
            @click="handlePreview"
        >
            <font-awesome-icon
                icon="fa-solid fa-eye"
                class="text-white text-sm"
            />
        </div>
    </div>

    <a-modal
        v-model:visible="previewVisible"
        :footer="null"
        :closable="true"
        :maskClosable="true"
        class="image-preview-modal"
    >
        <div class="flex justify-center">
            <img :src="img" :alt="alt" class="w-full h-full object-contain" />
        </div>
    </a-modal>
</template>

<script setup>
import { ref } from "vue";

const props = defineProps({
    src: {
        type: String,
        required: true,
    },
    img: {
        type: String,
        required: true,
    },
    alt: {
        type: String,
        default: "Image preview",
    },
});

const previewVisible = ref(false);

const handlePreview = () => {
    previewVisible.value = true;
};
</script>

<style scoped>
.image-preview-modal :deep(.ant-modal-content) {
    @apply bg-transparent shadow-none;
}

.image-preview-modal :deep(.ant-modal-body) {
    @apply p-0;
}

.image-preview-modal :deep(.ant-modal-close) {
    @apply text-white;
}
</style>
