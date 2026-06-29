<script setup>
import BaseUploadImage from "@/Components/BaseUploadImage.vue";
import FormItem from "@/Components/FormItem.vue";
import FormModal from "@/Components/FormModal.vue";
import { router, useForm } from "@inertiajs/vue3";
import { ref } from "vue";

const props = defineProps({
    roles: {
        user_groupes: Array,
        default: () => [],
    },
});

const form = useForm({
    id: null,
    name: null,
    email: null,
    password: null,
    password_confirmation: null,
    user_group_id: null,
    img: null,
});

const open = ref(false);
const title = ref("");
const imgPreview = ref(null);

const close = () => {
    open.value = false;
    form.reset();
    form.clearErrors();
    imgPreview.value = null;
};

const add = () => {
    title.value = "Nouvel utilisateur";
    form.reset();
    form.clearErrors();
    open.value = true;
    imgPreview.value = null;
};

const update = (data) => {
    title.value = "Modification utilisateur";
    open.value = true;

    Object.keys(form.data()).forEach((key) => {
        if (key !== "img" && data.hasOwnProperty(key)) {
            form[key] = data[key];
        }
    });
    imgPreview.value = data.img || null;
    form.img = null;
    form.user_group_id = data.user_group_id?.value ?? data.user_group_id;
    form.id = data.id;
};

const handlePhotoUpload = (file) => {
    form.img = file;
};

const submit = () => {
    form.clearErrors();

    const formData = new FormData();

    Object.keys(form.data()).forEach((key) => {
        if (key !== "img" && form[key] !== null && form[key] !== undefined) {
            if (key === "role") {
                formData.append(key, form[key]);
            } else {
                formData.append(key, form[key]);
            }
        }
    });

    if (form.img instanceof File) {
        formData.append("img", form.img);
    }

    const options = {
        onSuccess: () => {
            close();
        },
        onError: (errors) => {
            console.error("Validation errors:", errors);
            form.errors = errors;
        },
        method: "post",
    };

    if (form.id) {
        formData.append("_method", "PUT");
        router.post(route("user.update", form.id), formData, options);
    } else {
        router.post(route("user.store"), formData, options);
    }
};

defineExpose({
    add,
    update,
    close,
});
</script>

<template>
    <FormModal
        v-model:open="open"
        :titre="title"
        :loading="form.processing"
        @close="close"
        @submit="submit"
        :size="'lg'"
        :show_champ_obligatoir="false"
    >
        <div class="p-0 lg:p-4">
            <a-row :gutter="[8, 24]">
                <a-col :xs="24" :lg="8">
                    <div
                        class="bg-primary/[0.01] rounded-md p-2 lg:p-6 border border-gray-100 transition-all duration-300"
                    >
                        <div class="text-center">
                            <!-- Titre de section avec icône -->
                            <div
                                class="flex items-center justify-center gap-2 mb-6"
                            >
                                <div
                                    class="bg-gradient-to-r from-sky-500 to-primary p-2 flex items-center justify-center w-7 h-7 rounded-md"
                                >
                                    <font-awesome-icon
                                        icon="fa-solid fa-camera"
                                        class="text-white text-sm"
                                    />
                                </div>
                                <h3 class="text-lg font-semibold text-gray-800">
                                    Photo de l'utilisateur
                                </h3>
                            </div>
                        </div>

                        <div class="mb-4">
                            <BaseUploadImage
                                :url="imgPreview"
                                @onChange="handlePhotoUpload"
                                accept="image"
                                class="mx-auto"
                            />
                        </div>

                        <div
                            class="mt-4 p-3 bg-primary/5 rounded-lg border border-blue-200"
                        >
                            <p class="text-xs text-primary font-medium">
                                <font-awesome-icon
                                    icon="fa-solid fa-info-circle"
                                    class="mr-2"
                                />
                                Formats acceptés : JPG, PNG
                            </p>
                        </div>
                    </div>
                </a-col>

                <a-col :xs="24" :lg="16">
                    <div
                        class="bg-primary/[0.01] rounded-md p-2 lg:p-6 border border-gray-100 transition-all duration-300"
                    >
                        <!-- Titre de section -->
                        <div
                            class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-100"
                        >
                            <div
                                class="bg-gradient-to-r from-primary to-sky-400 p-2 rounded-lg"
                            >
                                <font-awesome-icon
                                    icon="fa-solid fa-id-card"
                                    class="text-white text-sm"
                                />
                            </div>
                            <h3 class="text-lg font-semibold text-gray-800">
                                Informations Personnelles
                            </h3>
                        </div>

                        <a-row :gutter="[16, 2]">
                            <a-col :xs="24">
                                <FormItem
                                    required
                                    has-feedback
                                    label="Nom"
                                    :help="form.errors.name"
                                >
                                    <a-input
                                        class="w-full"
                                        v-model:value="form.name"
                                        size="large"
                                    />
                                </FormItem>
                            </a-col>

                            <a-col :xs="24" :lg="12">
                                <FormItem
                                    required
                                    has-feedback
                                    label="Email"
                                    :help="form.errors.email"
                                >
                                    <a-input
                                        class="w-full"
                                        v-model:value="form.email"
                                        size="large"
                                    />
                                </FormItem>
                            </a-col>

                            <a-col :xs="24" :lg="12">
                                <FormItem
                                    required
                                    has-feedback
                                    label="Groupe Utilisateurs"
                                    :help="form.errors.user_group_id"
                                >
                                    <a-select
                                        class="w-full"
                                        v-model:value="form.user_group_id"
                                        block
                                        :options="roles"
                                        size="large"
                                    />
                                </FormItem>
                            </a-col>

                            <a-col :xs="24" :lg="12">
                                <FormItem
                                    :required="form.id == null"
                                    has-feedback
                                    label="Mot de passe"
                                    :help="form.errors.password"
                                >
                                    <a-input-password
                                        class="w-full"
                                        v-model:value="form.password"
                                        size="large"
                                    />
                                </FormItem>
                            </a-col>

                            <a-col :xs="24" :lg="12">
                                <FormItem
                                    :required="form.id == null"
                                    has-feedback
                                    label="Confirmation de mot de passe"
                                    :help="form.errors.password_confirmation"
                                >
                                    <a-input-password
                                        class="w-full"
                                        v-model:value="
                                            form.password_confirmation
                                        "
                                        size="large"
                                    />
                                </FormItem>
                            </a-col>
                        </a-row>
                    </div>
                </a-col>
            </a-row>
        </div>
    </FormModal>
</template>

<style scoped></style>
