<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import FormItem from "@/Components/FormItem.vue";
import PasswordRequirment from "@/Components/PasswordRequirment.vue";

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Register" />

        <a-form @submit.prevent="submit" layout="vertical">

            <form-item label="Nom" :help="form.errors.name" required>
                <a-input v-model:value="form.name" size="large"/>
            </form-item>

            <form-item label="Email" :help="form.errors.email" required>
                <a-input v-model:value="form.email" size="large"/>
            </form-item>

            <form-item label="Mot de passe" :help="form.errors.password" required>
                <a-input-password v-model:value="form.password" size="large"/>
                <password-requirment />
            </form-item>

            <form-item label="Confirmation de mot de passe" :help="form.errors.password_confirmation" required>
                <a-input-password v-model:value="form.password_confirmation" size="large"/>
            </form-item>

            <a-button type="primary" html-type="submit" class="mt-3 w-full !py-3" :loading="form.processing">
                S'inscrire
            </a-button>

            <div class="mt-5 flex justify-center">
                <Link :href="route('login')" class="rounded-md text-sm text-gray-600 underline hover:text-gray-900">
                    Already registered?
                </Link>
            </div>
        </a-form>
    </GuestLayout>
</template>
