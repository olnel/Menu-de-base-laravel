<script>
import modePaiement from "../../../../Utils/modePaiement.js";
const MODE_PAIEMENET = modePaiement;
</script>

<script setup>
import {ref} from "vue";
import {router, useForm} from "@inertiajs/vue3";
import FormItem from "@/Components/FormItem.vue";
import FormModal from "@/Components/FormModal.vue";


import InputNumberWithSepartor from "@/Components/InputNumberWithSepartor.vue";
import AutocompleteComponent from "@/Components/AutoCompleteComponent/AutocompleteComponent.vue";
import dayjs from "dayjs";

const props = defineProps({
    flash: Object,
    tresoreries: {
        type: Array,
        default: [],
    }
});
const dateFormat = 'DD/MM/YYYY';

const form = useForm({
    id: null,
    tresorerie_id: null,
    facture_client_id: null,
    date_reglement: dayjs().format('YYYY-MM-DD'),
    commentaire: null,
    mode_reglement: null,
    montant_reglement: 0,
    reste_a_payer: 0,
    montant_payer: 0,
});

const historiqueReglement = ref([]);


const filtre = ref({search: null});

const open = ref(false);
const title = ref("");

const close = () => {
    open.value = false;
    form.reset();
    form.clearErrors();
};

const add = () => {
    title.value = "Nouveau Réglement";
    open.value = true;

};

const reglement = (rowData) => {

    router.visit(`${route('factureclient.showreglement', {factureclient: rowData.id})}`, {
        preserveState: true,
        preserveScroll: true,
        only: ['flash'],
        onSuccess: (reponse) => {
            const response = reponse.props.flash.data;
            form.facture_client_id = response.id;
            form.reste_a_payer = 0;
            form.montant_reglement = response.montant_reste_a_payer;
            historiqueReglement.value = response.reglements;
            title.value = "Reglement de la facture " + rowData.numero_facture;
            open.value = true;
        },
    });
};


const submit = () => {
    form.clearErrors();

    const method = form.id ? 'put' : 'post';
    const url = form.id ? route('factureclientreglement.update', form.id) : route('factureclientreglement.store');

    form.transform(data => ({
        ...data,
        _method: method.toUpperCase()
    })).post(url, {
        onSuccess: () => close(),
        forceFormData: true
    });
};

defineExpose({add, reglement, close});
</script>

<template>
    <FormModal
        v-model:open="open"
        :loading="form.processing"
        @close="close"
        @submit="submit"
        :titre="title"
        size="lg"
        :show_champ_obligatoir="false"
    >

            <a-form layout="vertical">
                <div class="grid xl:grid-cols-2 lg:grid-cols-2 md:grid-cols-1 sm:grid-cols-1 grid-cols-1 gap-4">
                    <div>
                        <form-item required has-feedback label="Date" :help="form.errors.date_reglement">
                            <a-date-picker
                                v-model:value="form.date_reglement"
                                :format="dateFormat"
                                size="large"
                                class="w-full text-center"
                                :value-format="'YYYY-MM-DD'"
                            />
                        </form-item>

                        <form-item required has-feedback label="Trésorerie" :help="form.errors.tresorerie_id">
                            <a-select class="w-full" v-model:value="form.tresorerie_id" block :options="props.tresoreries" size="large" />
                        </form-item>
                    </div>
                    <div>
                        <form-item required has-feedback label="Mode de paiement" :help="form.errors.mode_reglement">
                            <autocomplete-component :options="MODE_PAIEMENET"
                                                    v-model="form.mode_reglement"
                                                    class=" !w-full"
                                                    :field-config="{
                                                        label: 'label',
                                                        value: 'label',
                                                        search: 'label'
                                                    }"
                                                    size="large"
                                                    placeholder=""
                            />
                        </form-item>
                        <form-item required has-feedback label="Montant" :help="form.errors.montant_reglement">
                            <InputNumberWithSepartor
                                v-model="form.montant_reglement"
                                size="large"
                                class="w-full text-right"
                            />
                        </form-item>
                    </div>
                </div>

                <form-item  has-feedback label="Commentaire" :help="form.errors.commentaire">
                    <a-textarea
                        v-model:value="form.commentaire"
                        size="large"
                        class="w-full p-0"
                        rows="4"
                    />
                </form-item>
            </a-form>

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

tfoot tr td {
    border: 1px solid #c9c9c9c9;
}
</style>
