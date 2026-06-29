<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import FormItem from "@/Components/FormItem.vue";

defineProps({
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
    <GuestLayout>
        <Head title="Forgot Password" />

        <div class="mb-6 text-sm text-gray-600">
            Mot de passe oublié ? Aucun problème. Il vous suffit de nous communiquer votre
            adresse e-mail et nous vous enverrons un lien de réinitialisation de mot de passe
            par e-mail, qui vous permettra d'en choisir un nouveau.
        </div>

        <div v-if="status" class="mb-6 text-sm font-medium text-green-600">
            {{ status }}
        </div>

        <a-form @submit.prevent="submit" layout="vertical">

            <form-item label="Email" :help="form.errors.email" required>
                <a-input v-model:value="form.email" size="large"/>
            </form-item>

            <a-button type="primary" html-type="submit" class="mt-3 w-full !py-3" :loading="form.processing">
                Envoyer le lien de réinitialisation
            </a-button>
        </a-form>
    </GuestLayout>
</template>
