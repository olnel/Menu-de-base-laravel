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
    nom_fournisseur: null,
    adresse_fournisseur: null,
    mail_fournisseur: null,
    tel_fournisseur: null,
    nif_fournisseur: null,
    stat_fournisseur: null,
    rcs_fournisseur: null,
});

const open = ref(false);
const title = ref("");


const close = () => {
    open.value = false;
    form.reset();
    form.clearErrors();
};

const add = () => {
    title.value = "Nouveau Magasin";
    open.value = true;
};

const update = (rowData) => {

    router.visit(`${route('fournisseur.show', {fournisseur: rowData.id})}`, {
        preserveState: true,
        preserveScroll: true,
        only: ['flash'],
        onSuccess: (reponse) => {
            const response = reponse.props.flash.data;
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
    const url = form.id ? route('fournisseur.update', form.id) : route('fournisseur.store');
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
            <form-item required has-feedback label="Nom" :help="form.errors.nom_fournisseur">
                <a-input
                    v-model:value="form.nom_fournisseur"
                    placeholder=""
                    size="large"
                />
            </form-item>

            <form-item has-feedback label="Adresse" :help="form.errors.adresse_fournisseur">
                <a-input
                    v-model:value="form.adresse_fournisseur"
                    placeholder=""
                    size="large"
                />
            </form-item>
            <form-item has-feedback label="Contacte" :help="form.errors.tel_fournisseur">
                <a-input
                    v-model:value="form.tel_fournisseur"
                    placeholder=""
                    size="large"
                />
            </form-item>
            <form-item has-feedback label="E-Mail" :help="form.errors.mail_fournisseur">
                <a-input
                    v-model:value="form.mail_fournisseur"
                    placeholder=""
                    size="large"
                />
            </form-item>

            <form-item has-feedback label="NIF" :help="form.errors.nif_fournisseur">
                <a-input
                    v-model:value="form.nif_fournisseur"
                    placeholder=""
                    size="large"
                />
            </form-item>

            <form-item has-feedback label="STAT" :help="form.errors.stat_fournisseur">
                <a-input
                    v-model:value="form.stat_fournisseur"
                    placeholder=""
                    size="large"
                />
            </form-item>

            <form-item has-feedback label="STAT" :help="form.errors.rcs_fournisseur">
                <a-input
                    v-model:value="form.rcs_fournisseur"
                    placeholder=""
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
