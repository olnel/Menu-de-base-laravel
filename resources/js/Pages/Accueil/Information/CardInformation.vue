<template>
    <div class="mx-auto p-4">
        <div class="bg-white rounded-xl shadow-md overflow-hidden transition-all duration-300 hover:shadow-lg">
            <div class="flex flex-col md:flex-row">
                <!-- Partie droite - Image/Logo -->
                <div class="md:w-1/3 p-6 flex items-center justify-center bg-gray-50 border-b md:border-b-0 md:border-r border-gray-200">
                    <div class="relative w-full aspect-square">
                        <img :src="previewImage || defaultImage"
                             alt="logo"
                             class="w-full h-full object-contain p-2 border border-gray-200 rounded-lg bg-white">
                        <input type="file"
                               @change="handleLogoUpload"
                               class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                               accept="image/*">
                        <div class="absolute bottom-2 right-2 bg-white/80 px-2 py-1 rounded text-xs text-gray-600">
                            Cliquer pour changer
                        </div>
                    </div>
                </div>

                <div class="md:w-2/3 p-6">
                    <h2 class="text-2xl font-bold text-gray-800 mb-4">Modifier les informations</h2>
                    <a-form layout="vertical">
                        <div class="space-y-4">
                            <form-item required has-feedback label="Contact" :help="element.errors.contact">
                                <a-input
                                    v-model:value="element.contact"
                                    placeholder=""
                                    size="large"
                                />
                            </form-item>
                            <form-item required has-feedback label="Email" :help="element.errors.email">
                                <a-input
                                    v-model:value="element.email"
                                    placeholder=""
                                    size="large"
                                />
                            </form-item>

                            <form-item required has-feedback label="Adresse" :help="element.errors.addresse">
                                <a-input
                                    v-model:value="element.addresse"
                                    placeholder=""
                                    size="large"
                                />
                            </form-item>

                            <form-item required has-feedback label="Heure d'ouverture" :help="element.errors.heure_ouverture">
                                <a-input
                                    v-model:value="element.heure_ouverture"
                                    placeholder=""
                                    size="large"
                                />
                            </form-item>

                            <form-item required has-feedback label="Visite" :help="element.errors.visite">
                                <a-input
                                    v-model:value="element.visite"
                                    placeholder=""
                                    size="large"
                                />
                            </form-item>
                        </div>
                    </a-form>

                    <!-- Bouton Enregistrer -->
                    <div class="mt-6 flex justify-end space-x-3">
                        <a-button type="primary"
                                  @click="saveChanges"
                                  class="css-dev-only-do-not-override-nxgiy5 ant-btn ant-btn-primary">
                            <template #icon>
                                <SaveOutlined />
                            </template>
                            Enregistrer
                        </a-button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { SaveOutlined } from '@ant-design/icons-vue';
import FormItem from "@/Components/FormItem.vue";
import {useForm} from "@inertiajs/vue3";
import {ref} from "vue";

const defaultImage = ref("/img/default/default_img.png");

const props = defineProps({
    initialData: {
        type: Object,
        required: true
    }
});

const element = useForm({
    ...props.initialData
});

const previewImage = ref(props.initialData.logo);

const saveChanges = () => {
    element.clearErrors();
    const method = element.id ? 'put' : 'post';
    const url = element.id ? route('information.update', element.id) : route('information.store');

    element.transform(data => ({
        ...data,
        _method: method === 'put' ? 'PUT' : 'POST'
    })).post(url, {
        onSuccess: () => close(),
        forceFormData: true
    });
}

const handleLogoUpload = (e) => {
    const file = e.target.files[0];
    if (file) {
        element.logo = file;
        previewImage.value = URL.createObjectURL(file);
        e.target.value = '';
    }
}
</script>

<style scoped>
.aspect-square {
    aspect-ratio: 1/1;
}
</style>
