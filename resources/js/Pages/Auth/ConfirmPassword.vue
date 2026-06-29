<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import FormItem from "@/Components/FormItem.vue";

const form = useForm({
    password: '',
});

const submit = () => {
    form.post(route('password.confirm'), {
        onFinish: () => form.reset(),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Confirm Password" />

        <div class="mb-6 text-sm text-gray-600">
            Ceci est une zone sécurisée de l'application. Veuillez confirmer votre mot de passe avant de continuer.
        </div>

        <a-form @submit.prevent="submit" layout="vertical">

            <form-item label="Mot de passe" :help="form.errors.password" required>
                <a-input v-model:value="form.password" size="large"/>
            </form-item>

            <a-button type="primary" html-type="submit" class="mt-3 w-full !py-3" :loading="form.processing">
                Confirmé
            </a-button>
        </a-form>
    </GuestLayout>
</template>
