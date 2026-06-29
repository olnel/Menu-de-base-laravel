<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import FormItem from "@/Components/FormItem.vue";
import PasswordRequirment from "@/Components/PasswordRequirment.vue";

const props = defineProps({
    email: {
        type: String,
        required: true,
    },
    token: {
        type: String,
        required: true,
    },
});

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('password.store'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Reset Password" />

        <a-form @submit.prevent="submit" layout="vertical">
            <form-item label="Email" :help="form.errors.email" required>
                <a-input v-model:value="form.email" size="large"/>
            </form-item>

            <form-item label="Mot de passe" :help="form.errors.password" required>
                <a-input v-model:value="form.password" size="large"/>
                <password-requirment />
            </form-item>

            <form-item label="Confirmation de mot de passe" :help="form.errors.password_confirmation" required>
                <a-input v-model:value="form.password_confirmation" size="large"/>
            </form-item>

            <a-button type="primary" html-type="submit" class="mt-3 w-full !py-3" :loading="form.processing">
                Reinitialiser le mot de passe
            </a-button>
        </a-form>
    </GuestLayout>
</template>
