<template>
    <Head :title="title"/>

    <a-config-provider :theme="AntdvTheme">
        <div class="flex flex-col text-text-primary min-h-screen" >
            <slot></slot>
        </div>
    </a-config-provider>
    <Notification />
    <Loader/>
    <PwaInstallPrompt />
</template>

<script setup>
import AntdvTheme from "../../Theme/antdv-theme.js"
import {Head, usePage} from "@inertiajs/vue3";
import {defineProps, onMounted, watch} from "vue";
import {message} from "ant-design-vue";
import Loader from "@/Components/Loader.vue";
import Notification from "@/Components/Notification.vue";
import PwaInstallPrompt from "@/Components/PwaInstallPrompt.vue";

const props = defineProps({
    title: String
})

const page = usePage();

watch(() => page.props.message, (data) => {

    if (data && (data.success || data.error)) {
        showMessage(data);
    }
});

const showMessage = (data) => {
    if (data.success)
        message.success(data.success);

    if (data.error)
        message.error(data.error);
}
/*
onMounted(() => {
    setTimeout(() => showMessage(page.props.message), 1000);
})*/

</script>
