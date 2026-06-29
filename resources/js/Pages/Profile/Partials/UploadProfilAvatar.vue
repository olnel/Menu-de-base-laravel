<template>
    <div class="flex flex-col items-center gap-4">
        <avatar
            :src="$page.props.auth.user.avatar"
            :size="100"
            icon="fa-circle-user"
        />

        <div class="flex items-center gap-2">
            <a-upload
                :multiple="false"
                accept="image/png, image/jpeg"
                list-type="picture"
                :maxCount="1"
                :show-upload-list="false"
                :before-upload="beforeUpload"
            >
                <a-button class="uppercase font-semibold">
                    Choisir une image
                </a-button>
            </a-upload>

            <a-button class="uppercase font-semibold" @click="deleteAvatar">
                Supprimer
            </a-button>
        </div>
    </div>
</template>

<script setup>
import Avatar from '@/Components/Avatar.vue';
import {router} from '@inertiajs/vue3';

const beforeUpload = file => {
    router.post(route('profile.avatar'), {avatar_file: file}, {
        onSuccess: (page) => {

        }
    })
    return false;
};

const deleteAvatar = () => {
    router.delete(route("profile.avatar.delete"));
}

</script>

<style>

</style>
