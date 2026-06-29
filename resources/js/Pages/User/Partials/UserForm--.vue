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
                formData.append(key, form[key]?.value ?? form[key]);
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
        <div class="grid grid-cols-2 gap-4">
            <div
                class="flex flex-col items-center justify-center space-y-3 border border-gray-200 rounded-lg p-4 bg-white shadow-sm mb-4"
            >
                <h3 class="text-base font-semibold text-gray-700">
                    Photo de l'utilisateur
                </h3>
                <BaseUploadImage
                    :url="imgPreview"
                    @onChange="handlePhotoUpload"
                    accept="image"
                />
                <p class="text-xs text-gray-500 text-center">
                    Formats acceptés : JPG, PNG
                </p>
            </div>
            <div>
                <form-item
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
                </form-item>

                <form-item
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
                </form-item>

                <form-item
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
                </form-item>
                <form-item
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
                </form-item>

                <form-item
                    :required="form.id == null"
                    has-feedback
                    label="Confirmation de mot de passe"
                    :help="form.errors.password_confirmation"
                >
                    <a-input-password
                        class="w-full"
                        v-model:value="form.password_confirmation"
                        size="large"
                    />
                </form-item>
            </div>
        </div>
    </FormModal>
</template>

<style scoped></style>
