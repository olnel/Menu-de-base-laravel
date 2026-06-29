<script setup>
import { DownloadOutlined, EyeOutlined } from "@ant-design/icons-vue";
import { message } from "ant-design-vue";
import { ref, watch } from "vue";

const props = defineProps({
    initialFiles: {
        type: Array,
        default: () => [],
    },
    maxFiles: {
        type: Number,
        default: 10,
    },
    accept: {
        type: String,
        default: "image/*,.pdf,.doc,.docx,.xls,.xlsx,.txt",
    },
    reset: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(["updateFiles"]);

const previewVisible = ref(false);
const previewImage = ref("");
const fileList = ref([]);
const rawFiles = ref([]);

if (props.initialFiles?.length > 0) {
    fileList.value = props.initialFiles.map((f, index) => ({
        uid: `existing-${index}`,
        name: f.name || `fichier-${index}`,
        status: "done",
        url: f.url || f.path,
        type: f.type || "image/jpeg",
        isExisting: true,
    }));
}

async function getBase64(file) {
    return new Promise((resolve, reject) => {
        const reader = new FileReader();
        reader.readAsDataURL(file);
        reader.onload = () => resolve(reader.result);
        reader.onerror = (error) => reject(error);
    });
}

const handlePreview = async (file) => {
    if (!file.url && !file.preview) {
        file.preview = await getBase64(file.originFileObj);
    }
    previewImage.value = `../${file.url}` || file.preview;
    previewVisible.value = true;
};

const handleChange = async ({ fileList: newFileList }) => {
    fileList.value = newFileList;

    rawFiles.value = await Promise.all(
        newFileList
            .filter((file) => !file.isExisting)
            .map(async (file) => ({
                fileObj: file.originFileObj,
                preview: file.preview || (await getBase64(file.originFileObj)),
            }))
    );

    emit("updateFiles", {
        existing: fileList.value.filter((f) => f.isExisting),
        newFiles: rawFiles.value.map((f) => f.fileObj),
    });
};

const beforeUpload = () => false;

const fileValidator = (file) => {
    const allowedTypes = [
        "image/jpeg",
        "image/png",
        "image/gif",
        "application/pdf",
        "application/msword",
        "application/vnd.openxmlformats-officedocument.wordprocessingml.document",
        "application/vnd.ms-excel",
        "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
        "text/plain",
    ];

    const isAllowed =
        allowedTypes.includes(file.type) || file.type.startsWith("image/");
    const isLt10M = file.size / 1024 / 1024 < 10;

    if (!isAllowed) {
        message.error("Fichier non autorisé !");
        return false;
    }
    if (!isLt10M) {
        message.error("Taille max 10MB !");
        message.error("Taille max 10MB !");
        return false;
    }

    return true;
};

watch(
    () => props.reset,
    () => {
        fileList.value = [];
        rawFiles.value = [];
    }
);
</script>

<template>
    <div class="space-y-2">
        <a-upload
            v-model:file-list="fileList"
            list-type="picture-card"
            :multiple="true"
            :before-upload="beforeUpload"
            :accept="accept"
            @change="handleChange"
            :custom-request="({ file }) => fileValidator(file)"
        >
            <!-- Slot icônes d'action -->
            <template #itemRender="{ file }">
                <!--{{file.url}}-->
                <div class="relative group flex gap-4">
                    <div
                        v-if="file.type && file.type.startsWith('image/')"
                        class="h-20 w-full overflow-hidden shadow rounded"
                    >
                        <img
                            :src="`../${file.url || file.thumbUrl}`"
                            alt="image"
                            class="object-cover w-full h-full "
                        />
                    </div>
                    <div
                        v-else
                        class="w-full h-20 bg-gray-200 flex items-center justify-center rounded text-xs p-1"
                    >
                        {{ file.name }}
                    </div>
                    <div
                        class="absolute bottom-1 right-1 flex space-x-1 opacity-0 group-hover:opacity-100 transition-all"
                    >
                        <button
                            v-if="file.type && file.type.startsWith('image/')"
                            @click.prevent="handlePreview(file)"
                            class="bg-white p-1 border rounded shadow-sm hover:bg-gray-100"
                            title="Voir"
                        >
                            <EyeOutlined />
                        </button>
                        <a
                            :href="`../${file.url || file.thumbUrl}`"
                            download
                            class="bg-white p-1 border rounded shadow-sm hover:bg-gray-100"
                            title="Télécharger"
                        >
                            <DownloadOutlined />
                        </a>
                    </div>
                </div>
            </template>
        </a-upload>

        <!-- Aperçu -->
        <a-modal
            :visible="previewVisible"
            :footer="null"
            @cancel="previewVisible = false"
        >
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
:deep(:where(.css-dev-only-do-not-override-1qemppx).ant-upload-wrapper.ant-upload-picture-card-wrapper .ant-upload-list.ant-upload-list-picture-card .ant-upload-list-item-container){
    @apply  !h-auto !space-x-4 ;
}

</style>
