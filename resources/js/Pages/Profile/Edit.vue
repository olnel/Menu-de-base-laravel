<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import UploadProfilAvatar from "@/Pages/Profile/Partials/UploadProfilAvatar.vue";
import UpdatePasswordForm from "@/Pages/Profile/Partials/UpdatePasswordForm.vue";
import EmailVerification from "@/Pages/Profile/Partials/EmailVerification.vue";

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

</script>

<template>
    <AuthenticatedLayout title="Profile" selected-menu="profile">
        <a-card class="main-card">
            <div class="max-w-3xl">
                <UploadProfilAvatar />

                <hr class="my-10">

                <UpdateProfileInformationForm />

                <hr class="my-10">

                <EmailVerification v-if="mustVerifyEmail && $page.props.auth.user.email_verified_at === null" />

                <hr class="my-10" v-if="mustVerifyEmail && $page.props.auth.user.email_verified_at === null">

                <UpdatePasswordForm />
            </div>
        </a-card>
    </AuthenticatedLayout>
</template>
