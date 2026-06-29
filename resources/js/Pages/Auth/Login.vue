<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import FormItem from "@/Components/FormItem.vue";

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Log in" />

        <div v-if="status" class="mb-4 text-sm font-medium text-green-600">
            {{ status }}
        </div>

        <a-form @submit.prevent="submit" layout="vertical">

            <form-item label="Email" :help="form.errors.email" required>
                <a-input v-model:value="form.email" size="large"/>
            </form-item>

            <form-item label="Mot de passe" :help="form.errors.password" required>
                <a-input-password v-model:value="form.password" size="large"/>
            </form-item>

            <a-checkbox v-model:checked="form.remember">Se souvenir de moi</a-checkbox>

            <a-button type="primary" size="large" html-type="submit" class="mt-6 w-full" :loading="form.processing">
                Connexion
            </a-button>

            <div class="mt-5 flex justify-center">
                <Link v-if="canResetPassword" :href="route('password.request')" class="rounded-md text-sm text-gray-600 underline hover:text-gray-900">
                    Mot de passe oublier?
                </Link>
            </div>
        </a-form>
    </GuestLayout>
</template>
