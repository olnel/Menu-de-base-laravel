<template>
    <a-upload-dragger
        :multiple="false"
        :maxCount="1"
        accept="video/*"
        :show-upload-list="false"
        :before-upload="beforeUpload"
        class="uploader w-full"
    >
        <img v-if="image" :src="image" alt="Image" class="h-full w-full object-cover rounded-lg" />
        <div class="text-text-primary/50 m-10" v-else>
            <svg
                class="mx-auto h-12 w-12 text-gray-400"
                stroke="currentColor"
                fill="none"
                viewBox="0 0 48 48"
                xmlns="http://www.w3.org/2000/svg"
            >
                <!-- Contour de l'icône vidéo -->
                <path
                    d="M12 4H36a4 4 0 014 4v24a4 4 0 01-4 4H12a4 4 0 01-4-4V8a4 4 0 014-4z"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                />
                <!-- Triangle représentant le bouton play -->
                <path
                    d="M20 12l12 8-12 8V12z"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                >
                    <!-- Animation du triangle (jeu du bouton play) -->
                    <animate
                        attributeName="stroke-dasharray"
                        from="0,40"
                        to="40,0"
                        dur="1s"
                        repeatCount="indefinite"
                    />
                </path>
            </svg>
            <div>Video</div>
        </div>
    </a-upload-dragger>
</template>

<script setup>

import {ref, watch} from "vue";
import {base64ToBlob} from "../../Utils/functions.js";

const emit = defineEmits(['onChange'])
const props = defineProps({
    url: {
        type: String,
        default: ''
    }
})

const image = ref(props.url ?? '')

watch(props, (newValue) => {
    image.value = newValue.url ?? ''
})

function generateThumbnail(file, onSuccess) {
    const video = document.createElement("video");
    const canvas = document.createElement("canvas");
    const ctx = canvas.getContext("2d");

    // Charger la vidéo dans le lecteur vidéo
    video.src = URL.createObjectURL(file);

    video.addEventListener("loadedmetadata", () => {
        video.currentTime = video.duration / 3; // Définir le temps de capture
    });

    let thumbnail = null;
    video.addEventListener("seeked", () => {
        // Dimensionner le canvas aux dimensions de la vidéo
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;

        // Dessiner l'image de la vidéo sur le canvas
        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

        // Convertir le canvas en une URL de données
        onSuccess(canvas.toDataURL("image/png"));

        // Libérer l'objet URL de la vidéo pour économiser de la mémoire
        URL.revokeObjectURL(video.src);
    });
}

const beforeUpload = file => {
    generateThumbnail(file, (thumb) => {
        image.value = thumb
        emit('onChange', {
            file: file,
            thumb: base64ToBlob(thumb)
        })
    })
    return false;
};

</script>

<style>

.uploader .ant-upload{
    @apply !p-0.5 overflow-hidden;
}

</style>
