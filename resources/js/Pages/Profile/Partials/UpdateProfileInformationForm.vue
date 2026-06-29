<script setup>
import { useForm, usePage } from '@inertiajs/vue3';
import FormItem from "@/Components/FormItem.vue";
import AppSection from "@/Components/AppSection.vue";

const user = usePage().props.auth.user;

const form = useForm({
    name: user.name,
    email: user.email,
    tel: user.tel,
});
</script>

<template>

    <AppSection
        title="Modifier votre profile."
        description="Mettez à jour les informations de profil de votre compte ainsi que votre adresse e-mail."
    >

        <a-form @submit.prevent="form.patch(route('profile.update'))" class="mt-6" layout="vertical">

            <form-item label="Nom" :help="form.errors.name" required>
                <a-input v-model:value="form.name" size="large"/>
            </form-item>

            <div class="grid grid-cols-1 md:grid-cols-2 md:gap-4">
                <form-item label="Email" :help="form.errors.email" required>
                    <a-input v-model:value="form.email" size="large"/>
                </form-item>

                <form-item label="Téléphone" :help="form.errors.tel">
                    <a-input v-model:value="form.tel" size="large"/>
                </form-item>
            </div>

            <a-button type="primary" html-type="submit" :loading="form.processing" class="uppercase mt-2">
                Enrégistrer les modifications
            </a-button>
        </a-form>

    </AppSection>
</template>
