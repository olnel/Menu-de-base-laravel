<script setup>
import { ref, watch } from "vue";
import { message } from 'ant-design-vue';

const props = defineProps({
    initialFiles: {
        type: Array,
        default: () => []
    },
    maxFiles: {
        type: Number,
        default: 10
    },
    accept: {
        type: String,
        default: "image/*"
    },
    reset: {
        type: Boolean,
        default: false
    }
});

const emit = defineEmits(['updateFiles']);

const previewVisible = ref(false);
const previewImage = ref("");
const fileList = ref([]);
const rawFiles = ref([]);

// Initialisation avec fichiers existants
if (props.initialFiles?.length > 0) {
    fileList.value = props.initialFiles.map((img, index) => ({
        uid: `existing-${index}`,
        name: img.name || `image-${index}.jpg`,
        status: 'done',
        url: img.url || '/storage/' + img.path,
        isExisting: true
    }));
}

async function getBase64(file) {
    return new Promise((resolve, reject) => {
        const reader = new FileReader();
        reader.readAsDataURL(file);
        reader.onload = () => resolve(reader.result);
        reader.onerror = error => reject(error);
    });
}

const handlePreview = async file => {
    if (!file.url && !file.preview) {
        file.preview = await getBase64(file.originFileObj);
    }
    previewImage.value = file.url || file.preview;
    previewVisible.value = true;
};

const handleChange = async ({ fileList: newFileList }) => {
    fileList.value = newFileList;

    // Extraire les nouveaux fichiers bruts
    rawFiles.value = await Promise.all(
        newFileList
            .filter(file => !file.isExisting)
            .map(async file => ({
                fileObj: file.originFileObj,
                preview: file.preview || await getBase64(file.originFileObj)
            }))
    );

    emit('updateFiles', {
        existing: fileList.value.filter(f => f.isExisting),
        newFiles: rawFiles.value.map(f => f.fileObj)
    });
};

const beforeUpload = () => {
    // Empêche l'upload automatique
    return false;
};

const fileValidator = file => {
    const isImage = file.type.startsWith('image/');
    const isLt5M = file.size / 1024 / 1024 < 5;

    if (!isImage) {
        message.error('Seules les images sont autorisées!');
        return false;
    }

    if (!isLt5M) {
        message.error('La taille maximale est de 5MB!');
        return false;
    }

    return true;
};

watch(() => props.reset, (newVal) => {
    if (newVal) {
        fileList.value = [];
        rawFiles.value = [];
        emit('updateFiles', { existing: [], newFiles: [] });
    }
});


</script>

<template>
    <div class="space-y-2">

        <a-upload
            v-model:file-list="fileList"
            list-type="picture-card"
            :multiple="true"
            :before-upload="beforeUpload"
            :accept="accept"
            @preview="handlePreview"
            @change="handleChange"
            :custom-request="({ file }) => fileValidator(file)"
        >
            <div v-if="fileList.length < maxFiles">
                <div class="ant-upload-text">Ajouter des images</div>
                <div class="ant-upload-hint">(Max {{ maxFiles }} fichiers)</div>
            </div>
        </a-upload>

        <div class="text-sm text-gray-500 mt-2">
            <slot name="tips">
                <p class="font-medium text-gray-600">Conseils :</p>
                <ul class="list-disc pl-5 space-y-1">
                    <li>Formats acceptés: JPG, PNG</li>
                    <li>Taille max: 5MB par image</li>
                    <li>Photos nettes et bien éclairées</li>
                </ul>
            </slot>
        </div>

        <a-modal :visible="previewVisible" :footer="null" @cancel="previewVisible = false">
            <img alt="Preview" style="width: 100%" :src="previewImage" />
        </a-modal>
    </div>
</template>

<style scoped>
:deep(.ant-upload-list-picture-card-container) {
    width: 100px;
    height: 100px;
}

:deep(.ant-upload-select) {
    width: 100px;
    height: 100px;
    border: 1px dashed #d9d9d9;
    border-radius: 8px;
}
</style>
