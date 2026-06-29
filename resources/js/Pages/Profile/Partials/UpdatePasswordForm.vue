<script setup>
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import FormItem from "@/Components/FormItem.vue";
import AppSection from "@/Components/AppSection.vue";

const passwordInput = ref(null);
const currentPasswordInput = ref(null);

const form = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const updatePassword = () => {
    form.put(route('password.update'), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
        onError: () => {
            if (form.errors.password) {
                form.reset('password', 'password_confirmation');
                passwordInput.value.focus();
            }
            if (form.errors.current_password) {
                form.reset('current_password');
                currentPasswordInput.value.focus();
            }
        },
    });
};
</script>

<template>

    <AppSection
        title="Changez votre mot de passe."
        description="Assurez-vous que votre compte utilise un mot de passe long et aléatoire pour rester sécurisé."
    >
        <a-form @submit.prevent="updatePassword" class="mt-6" layout="vertical">

            <form-item label="Mot de passe actuel" :help="form.errors.current_password" required>
                <a-input-password ref="currentPasswordInput" v-model:value="form.current_password" size="large"/>
            </form-item>

            <div class="grid grid-cols-1 md:grid-cols-2 md:gap-4">
                <form-item label="Nouveau mot de passe" :help="form.errors.password" required>
                    <a-input-password ref="passwordInput" v-model:value="form.password" size="large"/>
                </form-item>

                <form-item label="Confirmer le mot de passe" :help="form.errors.password_confirmation" required>
                    <a-input-password v-model:value="form.password_confirmation" size="large"/>
                </form-item>
            </div>

            <div class="flex items-center gap-4">
                <a-button type="primary" html-type="submit" :loading="form.processing" class="uppercase">
                    Modifier le mot de passe
                </a-button>
            </div>

        </a-form>
    </AppSection>
</template>
