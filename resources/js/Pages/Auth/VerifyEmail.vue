<script setup>
import { computed } from 'vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    status: {
        type: String,
    },
});

const form = useForm({});

const submit = () => {
    form.post(route().has('admin.verification.send') ? route('admin.verification.send') : route('verification.send'));
};

const verificationLinkSent = computed(() => props.status === 'verification-link-sent',);
</script>

<template>
    <GuestLayout>
        <Head title="Email Verification" />

        <div class="mb-6 text-sm text-gray-600">
            Merci de vous être inscrit ! Avant de commencer, pourriez-vous vérifier votre adresse e-mail en cliquant sur le lien que nous venons de vous envoyer par e-mail ?
            Si vous n'avez pas reçu l'e-mail, nous serons ravis de vous en envoyer un autre.
        </div>

        <div class="mb-6 text-sm font-medium text-green-600" v-if="verificationLinkSent">
            Un nouveau lien de vérification a été envoyé à l'adresse e-mail que vous avez fournie lors de l'inscription.
        </div>

        <a-form @submit.prevent="submit" layout="vertical">
            <a-button type="primary" html-type="submit" class="mt-3 w-full !py-3" :loading="form.processing">
                Renvoyer l'e-mail de vérification
            </a-button>

            <div class="mt-5 flex justify-center">
                <Link :href="route().has('admin.logout') ? route('admin.logout') : route('logout')" class="rounded-md text-sm text-gray-600 underline hover:text-gray-900">
                    Déconnexion
                </Link>
            </div>
        </a-form>
    </GuestLayout>
</template>
