<script setup>

import AppSection from "@/Components/AppSection.vue";
import {Link, useForm} from "@inertiajs/vue3";

const form = useForm({})

const submit = () => {
    form.post(route('verification.send'))
}

</script>

<template>
    <AppSection title="Verifier votre email">
        <template #description>
            <p>Votre adresse e-mail n'est pas vérifiée. Cliquez sur <strong>Vérifier</strong> pour renvoyer l'e-mail de vérification.</p>
        </template>

        <a-button type="primary" class="uppercase" :loading="form.processing" @click="submit">
            Vérifier
        </a-button>
    </AppSection>
</template>
