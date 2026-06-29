<script setup>
import { ref } from "vue";
import { useForm } from "@inertiajs/vue3";
import { UploadOutlined, CloseOutlined } from '@ant-design/icons-vue';
import FormItem from "@/Components/FormItem.vue";
import FormModal from "@/Components/FormModal.vue";

const form = useForm({
    id: null,
    nom_partner: null,
    logo: null,
});

const open = ref(false);
const title = ref("");
const previewImage = ref(null);
const defaultImage = ref("/img/default/default_img.png");

const close = () => {
    open.value = false;
    form.reset();
    form.clearErrors();
    previewImage.value = null;
};

const add = () => {
    title.value = "Nouveau Partenaire";
    open.value = true;
};

const update = (data) => {
    title.value = "Modifier Partenaire";
    open.value = true;
    form.id = data.id;
    form.nom_partner = data.nom_partner;
    previewImage.value = data.logo || null;
};

const handleImageUpload = (e) => {
    const file = e.file;
    if (file) {
        form.logo = file;
        previewImage.value = URL.createObjectURL(file);
    }
};

const removeImage = () => {
    form.logo = null;
    previewImage.value = null;
    if (form.id) {
        form.logo = 'delete'; // Pour indiquer la suppression
    }
};

const submit = () => {
    form.clearErrors();
    const url = form.id
        ? route('partener.update', form.id)
        : route('partener.store');

    form.transform(data => ({
        ...data,
        _method: form.id ? 'PUT' : 'POST'
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
        <div class="grid grid-cols-1 gap-6">
            <div class="space-y-4">
                <FormItem required has-feedback label="Nom du Partenaire" :help="form.errors.nom_partner">
                    <a-input
                        v-model:value="form.nom_partner"
                        placeholder="Nom du partenaire"
                        size="large"
                    />
                </FormItem>

                <FormItem required label="Logo" :help="form.errors.logo">
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
                            Téléverser un logo
                        </a-button>
                    </a-upload>

                    <a-card hoverable class="mt-4 relative" :body-style="{ padding: 0 }">
                        <img
                            :src="previewImage || defaultImage"
                            alt="Logo du partenaire"
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
                                {{ form.logo?.name || (previewImage ? 'Logo sélectionné' : 'Logo par défaut') }}
                            </div>
                        </div>
                    </a-card>
                </FormItem>
            </div>
        </div>
    </FormModal>
</template>

<style scoped>

</style>
