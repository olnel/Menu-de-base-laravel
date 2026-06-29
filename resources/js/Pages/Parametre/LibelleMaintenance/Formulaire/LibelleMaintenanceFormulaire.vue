<script setup>
import { ref } from "vue";
import {router, useForm} from "@inertiajs/vue3";
import FormItem from "@/Components/FormItem.vue";
import FormModal from "@/Components/FormModal.vue";
import {ColorPicker} from "vue3-colorpicker";
import "vue3-colorpicker/style.css";

const props = defineProps({
    flash: Object
});

const form = useForm({
    id: null,
    libelle: null,
    notification: null,
    background: null,
    text_color: null,
});

const open = ref(false);
const title = ref("");


const close = () => {
    open.value = false;
    form.reset();
    form.clearErrors();
};

const add = () => {
    title.value = "Nouveau Libellé";
    open.value = true;
};

const update = (rowData) => {

    router.visit(`${route('libelle_maintenance.show', {libelle_maintenance: rowData.id})}`, {
        preserveState: true,
        preserveScroll: true,
        only: ['flash'],
        onSuccess: (reponse) => {
            const response = reponse.props.flash.maintenance;
            Object.keys(response).forEach(key => {
                form[key] = response[key];
            });
            title.value = "Modifier";
            open.value = true;
        },
    });
};

const submit = () => {
    form.clearErrors();
    const method = form.id ? 'put' : 'post';
    const url = form.id ? route('libelle_maintenance.update', form.id) : route('libelle_maintenance.store');
    form.transform(data => ({
        ...data,
        _method: method.toUpperCase()
    })).post(url, {
        onSuccess: () => close(),
        forceFormData: true
    });
};

defineExpose({add, update, close});
</script>

<template>
    <FormModal
        v-model:open="open"
        :loading="form.processing"
        @close="close"
        @submit="submit"
        :titre="title"
        size="sm"
        :show_champ_obligatoir="false"
    >

        <div class="">
            <form-item required has-feedback label="Libellé" :help="form.errors.libelle">
                <a-input
                    v-model:value="form.libelle"
                    placeholder=""
                    size="large"
                />
            </form-item>

            <form-item
                required
                has-feedback
                label="Couleur du Background"
                :help="form.errors.background"
            >
                <a-input
                    v-model:value="form.background"
                    placeholder=""
                    readonly
                    size="large"
                    type="color"
                    class="rounded-r-none w-full bg-white"
                />
            </form-item>

            <form-item
                required
                has-feedback
                label="Couleur du Texte"
                :help="form.errors.text_color"
            >
                <a-input
                    v-model:value="form.text_color"
                    placeholder=""
                    readonly
                    size="large"
                    class="rounded-r-none w-full bg-white"
                    type="color"
                />
            </form-item>


            <form-item required has-feedback label="Notification" :help="form.errors.notification">
                <a-input-number
                    v-model:value="form.notification"
                    placeholder=""
                    class="w-full"
                    size="large"
                />
            </form-item>
        </div>
    </FormModal>
</template>

<style scoped>
:deep(.ant-upload) {
    width: 100%;
}

:deep(.ant-card) {
    border-radius: 8px;
    overflow: hidden;
    border: 1px solid #f0f0f0;
    transition: all 0.3s;
}

:deep(.ant-card:hover) {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

:deep(.ant-btn-circle) {
    width: 24px;
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.truncate {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.vc-color-wrap{
    height: 40px !important;
}
</style>
