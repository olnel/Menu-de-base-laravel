<template>
    <a-card
        id="image-upload"
        class="!rounded-md overflow-hidden border border-dashed border-gray-300 hover:border-primary transition-all duration-300 ease-in-out transform hover:scale-[1.02] hover:shadow-md bg-gradient-to-br from-gray-50 to-white"
        :class="{
        'border-primary shadow-md': image,
        'hover:bg-gradient-to-br hover:from-blue-50 hover:to-gray-50': !image
      }"
        style="min-width: 200px;"
    >
        <template #cover>
            <div class="relative p-2">
                <!-- Image avec overlay moderne -->
                <div class="relative flex justify-center h-44">
                    <a-avatar
                        :size="224"
                        :src="image || '/img/placeholder_img.png'"
                        shape="square"
                    />
                </div>
            </div>
        </template>

        <template #actions>
            <div class="px-3 py-3 relative">
                <a-upload
                    :multiple="false"
                    accept="image/png, image/jpeg, image/svg+xml"
                    list-type="picture"
                    :maxCount="1"
                    :show-upload-list="false"
                    :before-upload="beforeUpload"
                    class="w-full"
                >
                    <a-tooltip placement="top" :title="image ? 'modifier la photo' : 'choisir un photo'" class="absolute -top-2 right-6">
                        <a-button
                            type="primary"
                            size="middle"
                            shape="default"
                            class="p-2 flex items-center justify-center w-9 h-9 border border-gray-300 rounded-md bg-gradient-to-r from-sky-500 to-primary hover:from-primary hover:to-sky-500 font-semibold shadow-sm hover:shadow-md transition-all duration-300"
                        >
                            <div class="flex items-center justify-center gap-3">
                                <font-awesome-icon
                                    :icon="image ? 'fa-pen-to-square' : 'fa-camera'"
                                    class="text-lg"
                                />
                            </div>
                        </a-button>
                    </a-tooltip>
                </a-upload>
            </div>
        </template>
    </a-card>
</template>

<script setup>
import fallbackImageUrl from "@/../../public/img/default/default_img.png";
import { defineEmits, ref, watch } from "vue";

const emit = defineEmits("onChange");
const props = defineProps({
    url: {
        type: String,
        default: "",
    },
    size: {
        type: Number,
        default: 100,
    },
    color: {
        type: String,
        default: "gray",
    },
    icon: {
        logo: String,
        default: "fa-circle-dot",
    },
});
const image = ref(props.url ?? "");

watch(props, (newValue) => {
    image.value = newValue.url ?? "";
});

function getBase64(img, callback) {
    const reader = new FileReader();
    reader.addEventListener("load", () => callback(reader.result));
    reader.readAsDataURL(img);
}

const beforeUpload = (file) => {
    getBase64(file, (base64) => {
        image.value = base64;
        emit("onChange", file);
    });
    return false;
};

const onRemove = () => {
    image.value = props.url ?? "";
    emit("onChange", null);
};
</script>

<style>

#image-upload {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

#image-upload .ant-card-body {
    @apply p-0;
}

#image-upload .ant-card-actions {
    @apply border-t-0 bg-transparent;
}

#image-upload .ant-card-actions li {
    @apply m-0;
}

#image-upload .ant-card-actions li > span {
    @apply cursor-default w-full;
}

/* Animation pour l'icône upload */
@keyframes bounce-gentle {
    0%, 100% {
        transform: translateY(0);
    }
    50% {
        transform: translateY(-4px);
    }
}

.animate-bounce {
    animation: bounce-gentle 2s infinite;
}

/* Effet glassmorphism */
.backdrop-blur-sm {
    backdrop-filter: blur(4px);
}

/* Amélioration des transitions */
.transition-all {
    transition-property: all;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
}
</style>
