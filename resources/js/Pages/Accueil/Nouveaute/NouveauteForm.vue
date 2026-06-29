<script>
const LIST_STRETCH = [
    {value: '', label: 'Par défaut'},
    {value: 'big', label: 'Grand'},
    {value: 'h', label: 'Hauteur'},
    {value: 'v', label: 'Vertical'},
]

</script>

<script setup>
import { ref } from "vue";
import { useForm } from "@inertiajs/vue3";
import { UploadOutlined, CloseOutlined } from '@ant-design/icons-vue';
import FormItem from "@/Components/FormItem.vue";
import FormModal from "@/Components/FormModal.vue";

const form = useForm({
    id: null,
    title: null,
    type: null,
    img: null,
    count: null,
    stretch: null,
    button_label: "Acheter",
    button_link: null,
    badge_date_jour: null,
    badge_date_annee: null,


});

const open = ref(false);
const title = ref("");
const previewImage = ref(null);
// Image par défaut
const defaultImage = ref("/img/default/default_img.png");

const close = () => {
    open.value = false;
    form.reset();
    form.clearErrors();
    previewImage.value = null;
};

const add = () => {
    title.value = "Nouveauté";
    open.value = true;
};

const update = (data) => {
    title.value = "Modification";
    open.value = true;
    Object.assign(form, data);
    previewImage.value = data.img || null;
};

const handleImageUpload = (e) => {
    const file = e.file;
    if (file) {
        form.img = file;
        previewImage.value = URL.createObjectURL(file);
    }
};

const removeImage = () => {
    form.img = null;
    previewImage.value = null;
    if (form.id && form.original_img) {
        form.img = 'delete';
    }
};

const submit = () => {
    form.clearErrors();
    const method = form.id ? 'put' : 'post';
    const url = form.id ? route('nouveaute.update', form.id) : route('nouveaute.store');

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
                <form-item required has-feedback label="Titre principal" :help="form.errors.title">
                    <a-input
                        v-model:value="form.title"
                        placeholder=""
                        size="large"
                    />
                </form-item>

                <form-item required has-feedback label="Type" :help="form.errors.type">
                    <a-input
                        v-model:value="form.type"
                        placeholder="Accessoir"
                        size="large"
                    />
                </form-item>
                <form-item has-feedback label="Total" :help="form.errors.count">
                    <a-input
                        v-model:value="form.count"
                        placeholder="8K"
                        size="large"
                    />
                </form-item>

                <form-item has-feedback label="Stretch" :help="form.errors.stretch">
                    <a-select class="w-full" v-model:value="form.stretch" block :options="LIST_STRETCH" size="large" />
                </form-item>

                <div class="grid grid-cols-2 gap-4">
                    <form-item class="m-0" has-feedback label="Jour du badge" :help="form.errors.badge_date_jour">
                        <a-input
                            v-model:value="form.badge_date_jour"
                            placeholder="24"
                            size="large"
                        />
                    </form-item>

                    <form-item class="m-0" has-feedback label="Mois/Année du badge" :help="form.errors.badge_date_annee">
                        <a-input
                            v-model:value="form.badge_date_annee"
                            placeholder="DEC 2023"
                            size="large"
                        />
                    </form-item>
                </div>

                <form-item has-feedback label="Texte du bouton" :help="form.errors.button_label">
                    <a-input
                        v-model:value="form.button_label"
                        placeholder="Acheter"
                        size="large"
                    />
                </form-item>

                <form-item  has-feedback label="Lien du bouton" :help="form.errors.button_link">
                    <a-input
                        v-model:value="form.button_link"
                        placeholder="/collections/maillots"
                        size="large"
                    />
                </form-item>
            </div>

            <!-- Colonne droite - Image -->
            <div class="space-y-4">
                <form-item required label="Photo" :help="form.errors.img">
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

                    <!-- Carte d'aperçu avec bouton de suppression -->
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
                            @click="removeImage"
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
