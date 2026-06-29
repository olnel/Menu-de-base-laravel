<script>
    const TYPE = [  {value: 'banque', label: 'Banque'},
                    {value: 'caisse', label: 'Caisse'},
                    {value: 'Mobile Money', label: 'Mobile Money'},
                ];

</script>
<script setup>
import { ref } from "vue";
import {router, useForm} from "@inertiajs/vue3";
import FormItem from "@/Components/FormItem.vue";
import FormModal from "@/Components/FormModal.vue";
import "vue3-colorpicker/style.css";

const props = defineProps({
    flash: Object
});

const form = useForm({
    id: null,
    nom_tresorerie: null,
    commentaire: null,
    type_tresorerie: 'Caisse',
    numero_compte: null,
    numero_telephone: null,
    bic: null,
    iban: null,
    banque_correspondante: null,
    titulaire_compte: null,
    code_swift: null,
});

const open = ref(false);
const title = ref("");


const close = () => {
    open.value = false;
    form.reset();
    form.clearErrors();
};

const add = () => {
    title.value = "Nouvelle Trésorerie";
    open.value = true;
};

const update = (rowData) => {

    router.visit(`${route('tresorerie.show', {tresorerie: rowData.id})}`, {
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
    const url = form.id ? route('tresorerie.update', form.id) : route('tresorerie.store');
    form.transform(data => ({
            ...data,
            _method: method.toUpperCase()
        })).post(url, {
            forceFormData: true,
            onSuccess: (response) => {
                const error = response.props?.message?.error;
                if (!error){
                    close();
                }
            },

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
            <form-item required has-feedback label="Nom de la Trésorerie" :help="form.errors.nom_tresorerie">
                <a-input
                    v-model:value="form.nom_tresorerie"
                    placeholder=""
                    size="large"
                />
            </form-item>
            <form-item required has-feedback label="Type" :help="form.errors.type_tresorerie">
                <a-select
                    v-model:value="form.type_tresorerie"
                    placeholder="Type de photo"
                    class="w-full"
                    :options="TYPE"
                    size="large"
                >
                </a-select>
            </form-item>
            <form-item v-if="form.type_tresorerie === 'Mobile Money'"  has-feedback label="Numero de téléphone" :help="form.errors.numero_telephone">
                <a-input
                    v-model:value="form.numero_telephone"
                    placeholder="Numéro de téléphone"
                    size="large"
                />
            </form-item>
            <form-item v-if="form.type_tresorerie === 'Mobile Money'"  has-feedback label="Titulaire du compte" :help="form.errors.numero_telephone">
                <a-input
                    v-model:value="form.titulaire_compte"
                    placeholder="Titulaire du compte"
                    size="large"
                />
            </form-item>


            <div v-show="form.type_tresorerie === 'banque'" class="mb-4">

                <form-item  has-feedback label="Numero de Compte" :help="form.errors.numero_compte">
                    <a-input
                        v-model:value="form.numero_compte"
                        placeholder="Numéro de compte"
                        size="large"
                    />
                </form-item>
                <form-item has-feedback label="Banque Correspondante" :help="form.errors.banque_correspondante">
                    <a-input
                        v-model:value="form.banque_correspondante"
                        placeholder="Banque Correspondante"
                        size="large"
                    />
                </form-item>
                <form-item has-feedback label="BIC" :help="form.errors.bic">
                    <a-input
                        v-model:value="form.bic"
                        placeholder="BIC"
                        size="large"
                    />
                </form-item>
                <form-item has-feedback label="IBAN" :help="form.errors.iban">
                    <a-input
                        v-model:value="form.iban"
                        placeholder="IBAN"
                        size="large"
                    />
                </form-item>

                <form-item has-feedback label="Code SWIFT" :help="form.errors.code_swift">
                    <a-input
                        v-model:value="form.code_swift"
                        placeholder="Code SWIFT"
                        size="large"
                    />
                </form-item>
            </div>


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
