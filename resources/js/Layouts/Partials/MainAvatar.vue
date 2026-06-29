
<script setup>
import {Link, usePage} from '@inertiajs/vue3';
import { computed } from 'vue';
import Avatar from '@/Components/Avatar.vue';

const page = usePage();
const user = page.props.auth.user;
const isAdmin = computed(() => page.url.startsWith('/admin'));

</script>

<template>
    <a-dropdown :trigger="['click']">

        <avatar
            :src="user.avatar"
            :size="23"
            icon="fa-circle-user"
            class="cursor-pointer hover:ring-4 ring-gray-200"
        />

        <template #overlay>
            <a-menu class="w-[260px] !text-gray-800 overflow-hidden">
                <div class="avatar-head">
                    <avatar
                        :src="user.avatar"
                        :size="70"
                        icon="fa-circle-user"
                        class="mx-auto mt-12 ring-white ring-2"
                    />
                    <div class="font-medium text-center text-lg mt-2">{{ user.name }}</div>
                    <div class="truncate text-center">{{ user.email }}</div>
                </div>
                <a-menu-divider/>
                <a-menu-item key="0" class="text-sm !text-gray-800">
                    <Link :href="route('profile.edit')">
                        <font-awesome-icon class="me-1" icon="fa-solid fa-circle-user"/>
                        Profile
                    </Link>
                </a-menu-item>
                <a-menu-item key="1" class="text-sm !text-gray-800">
                    <Link :href="isAdmin ? route('admin.logout') : route('logout')" method="post" as="button">
                        <font-awesome-icon class="me-1" icon="fa-solid fa-right-from-bracket"/>
                        Déconnexion
                    </Link>
                </a-menu-item>
            </a-menu>
        </template>
    </a-dropdown>
</template>

<style scoped>

.avatar-head{
    @apply flex flex-col p-2 text-sm justify-center;
}

.avatar-head::before{
    content: '';
    /*background-image: url("/img/bg-tin.jpg");*/
    /*background-position: center;*/
    @apply absolute w-full h-24 bg-primary/50 top-0 left-0;
}

</style>
