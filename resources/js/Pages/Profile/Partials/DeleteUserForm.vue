<script setup>
import { useForm } from '@inertiajs/vue3';
import { nextTick, ref } from 'vue';
import FormItem from "@/Components/FormItem.vue";

const confirmingUserDeletion = ref(false);
const passwordInput = ref(null);

const form = useForm({
    password: '',
});

const confirmUserDeletion = () => {
    confirmingUserDeletion.value = true;

    nextTick(() => passwordInput.value.focus());
};

const deleteUser = () => {
    form.delete(route('profile.destroy'), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onError: () => passwordInput.value.focus(),
        onFinish: () => form.reset(),
    });
};

const closeModal = () => {
    confirmingUserDeletion.value = false;

    form.clearErrors();
    form.reset();
};
</script>

<template>
    <section class="space-y-6">
        <header>
            <h2 class="text-lg font-medium text-gray-900">
                Delete Account
            </h2>

            <p class="mt-1 text-sm text-gray-600">
                Once your account is deleted, all of its resources and data will
                be permanently deleted. Before deleting your account, please
                download any data or information that you wish to retain.
            </p>
        </header>

        <a-button type="primary" class="bg-red-500 hover:!bg-red-700" @click="confirmUserDeletion">
            Delete Account
        </a-button>

        <a-modal
            v-model:open="confirmingUserDeletion"
            title="Are you sure you want to delete your account?"
            @close="closeModal"
            ok-text="Delete Account"
            @ok="deleteUser"
            :ok-button-props="{class: 'bg-red-500 hover:!bg-red-700'}"
            :confirm-loading="form.processing"
            @cancel="closeModal"
        >
                <p class="mt-1 text-sm text-gray-600">
                    Once your account is deleted, all of its resources and data
                    will be permanently deleted. Please enter your password to
                    confirm you would like to permanently delete your account.
                </p>

                <form-item class="mt-6" :help="form.errors.password" required>
                    <a-input-password ref="passwordInput" v-model:value="form.password" size="large"/>
                </form-item>
        </a-modal>
    </section>
</template>
