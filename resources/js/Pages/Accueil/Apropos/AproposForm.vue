<script setup>
import { ref } from "vue";
import { useForm } from "@inertiajs/vue3";
import { UploadOutlined, CloseOutlined } from '@ant-design/icons-vue';
import FormItem from "@/Components/FormItem.vue";
import FormModal from "@/Components/FormModal.vue";

const form = useForm({
    id: null,
    titre_section: null,
    titre_principal: null,
    description: null,
    bouton_label: "Découvrir",
    bouton_link: null,
    img: null,
    img_2: null,
});

const open = ref(false);
const title = ref("");
const previewImage = ref(null);
const previewImage2 = ref(null);
// Image par défaut
const defaultImage = ref("/img/default/default_img.png");

const close = () => {
    open.value = false;
    form.reset();
    form.clearErrors();
    previewImage.value = null;
    previewImage2.value = null;
};

const add = () => {
    title.value = "Nouveau Enregistrement";
    open.value = true;
};

const update = (data) => {
    title.value = "Modifier";
    open.value = true;
    Object.assign(form, data);
    previewImage.value = data.img || null;
    previewImage2.value = data.img_2 || null;
};

const handleImageUpload = (e) => {
    const file = e.file;
    if (file) {
        form.img = file;
        previewImage.value = URL.createObjectURL(file);
    }
};

const handleImageUploadOtherImg = (e) => {
    const file = e.file;
    if (file) {
        form.img_2 = file;
        previewImage2.value = URL.createObjectURL(file);
    }
};

const removeImage = (type) => {
    if (type == 'img') {
        form.img = null;
        previewImage.value = null;
        if (form.id && form.original_img) {
            form.img = 'delete';
        }
    }else{
        form.img_2 = null;
        previewImage2.value = null;
        if (form.id && form.original_img2) {
            form.img_2 = 'delete';
        }
    }

};

const submit = () => {
    form.clearErrors();
    const method = form.id ? 'put' : 'post';
    const url = form.id ? route('apropos.update', form.id) : route('apropos.store');

    form.transform(data => ({
        ...data,
        _method: method === 'put' ? 'PUT' : 'POST'
    })).post(url, {
        onSuccess: () => close(),
        forceFormData: true
    });
};

defineExpose({ add, update, close });
</script>

<template>
    <FormModal
        v-model:open="open"
        :title="title"
        :loading="form.processing"
        @close="close"
        @submit="submit"
        size="large"
        :show_champ_obligatoir="false"
    >
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Colonne gauche - Formulaire -->
            <div class="space-y-4">
                <!--                <form-item required has-feedback label="Titre Section" :help="form.errors.titre_section">
                                    <a-input
                                        v-model:value="form.titre_section"
                                        placeholder=""
                                        size="large"
                                    />
                                </form-item>-->

                           <form-item required has-feedback label="Titre" :help="form.errors.titre_principal">
                                    <a-input
                                        v-model:value="form.titre_principal"
                                        placeholder=""
                                        size="large"
                                    />
                                </form-item>

                <form-item has-feedback label="Description" :help="form.errors.description">
                    <a-textarea
                        v-model:value="form.description"
                        placeholder=""
                        :auto-size="{ minRows: 4, maxRows: 6 }"
                    />
                </form-item>

                <form-item has-feedback label="Texte du bouton" :help="form.errors.bouton_label">
                    <a-input
                        v-model:value="form.bouton_label"
                        placeholder=""
                        size="large"
                    />
                </form-item>

                <form-item required has-feedback label="Lien du bouton" :help="form.errors.bouton_link">
                    <a-input
                        v-model:value="form.bouton_link"
                        placeholder=""
                        size="large"
                    />
                </form-item>
            </div>

            <!-- Colonne droite - Image -->
            <div class="space-y-4">
                <form-item required label="1er Image" :help="form.errors.img">
                    <a-upload
                        accept="image/*"
                        :before-upload="() => false"
                        @change="handleImageUpload"
                        :show-upload-list="false"
                        class="w-full"
                    >
                        <a-button type="dashed" size="large" block>
                            <template #icon>
                                <UploadOutlined />
                            </template>
                            Téléverser une image
                        </a-button>
                    </a-upload>
                    <a-card
                        hoverable
                        class="mt-4 relative"
                        :body-style="{ padding: 0 }"
                    >
                        <img
                            :src="previewImage || defaultImage"
                            alt="Image d'arrière-plan"
                            class="w-full h-64 object-contain bg-gray-100"
                        />
                        <a-button
                            v-if="previewImage"
                            type="text"
                            danger
                            shape="circle"
                            size="small"
                            class="absolute top-2 right-2 bg-white/80 hover:bg-white"
                            @click="removeImage('img')"
                        >
                            <template #icon>
                                <CloseOutlined />
                            </template>
                        </a-button>
                        <div class="p-3 text-sm text-gray-500">
                            <div class="truncate">
                                {{ form.img?.name || (previewImage ? 'Image sélectionnée' : 'Image par défaut') }}
                            </div>
                            <div class="text-xs text-gray-400 mt-1">
                                Taille recommandée : 1920×1080px
                            </div>
                        </div>
                    </a-card>
                </form-item>


                <form-item required label="2nd Image" :help="form.errors.img_2">
                    <a-upload
                        accept="image/*"
                        :before-upload="() => false"
                        @change="handleImageUploadOtherImg"
                        :show-upload-list="false"
                        class="w-full"
                    >
                        <a-button type="dashed" size="large" block>
                            <template #icon>
                                <UploadOutlined />
                            </template>
                            Téléverser une image
                        </a-button>
                    </a-upload>
                    <a-card
                        hoverable
                        class="mt-4 relative"
                        :body-style="{ padding: 0 }"
                    >
                        <img
                            :src="previewImage2 || defaultImage"
                            alt="Image d'arrière-plan"
                            class="w-full h-64 object-contain bg-gray-100"
                        />
                        <a-button
                            v-if="previewImage2"
                            type="text"
                            danger
                            shape="circle"
                            size="small"
                            class="absolute top-2 right-2 bg-white/80 hover:bg-white"
                            @click="removeImage('img2')"
                        >
                            <template #icon>
                                <CloseOutlined />
                            </template>
                        </a-button>
                        <div class="p-3 text-sm text-gray-500">
                            <div class="truncate">
                                {{ form.img_2?.name || (previewImage ? 'Image sélectionnée' : 'Image par défaut') }}
                            </div>
                            <div class="text-xs text-gray-400 mt-1">
                                Taille recommandée : 1920×1080px
                            </div>
                        </div>
                    </a-card>
                </form-item>
            </div>


        </div>
    </FormModal>
</template>

<style scoped>
/* Styles personnalisés */
:deep(.ant-upload) {
    width: 100%;
}

:deep(.ant-card) {
    border-radius: 8px;
    overflow: hidden;
    border: 1px solid #f0f0f0;
    transition: all 0.3s;
}

:deep(.ant-card:hover) {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

:deep(.ant-btn-circle) {
    width: 24px;
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.truncate {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
</style>
