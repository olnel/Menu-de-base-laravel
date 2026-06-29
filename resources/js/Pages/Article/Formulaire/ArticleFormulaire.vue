<script>
    const TYPE_ARTICLE = [
        {value: 'CONSOMMMABLE', label: 'Consommmable'},
        {value: 'PIÈCE MAJEURE', label: 'Pièce majeure'},
        {value: 'PNEU', label: 'Pneu'},
    ]
</script>
<script setup>
import { ref } from "vue";
import {router, useForm} from "@inertiajs/vue3";
import FormItem from "@/Components/FormItem.vue";
import FormModal from "@/Components/FormModal.vue";
import {ColorPicker} from "vue3-colorpicker";
import "vue3-colorpicker/style.css";
import AutocompleteComponent from "@/Components/AutoCompleteComponent/AutocompleteComponent.vue";
import InputNumberWithSepartor from "@/Components/InputNumberWithSepartor.vue";

const props = defineProps({
    flash: Object,
    LIST_FAMILLE_ARTICLE: {
        type: Array,
        default:() => []
    }
});

const localFamilleArticle= ref([...props.LIST_FAMILLE_ARTICLE]);

const form = useForm({
    id: null,
    article_famille_id: null,
    nom_famille_article: null,
    reference: null,
    designation: null,
    type_article: null,
    marque: null,
    valeur: null,
    seuil_stock: null,
});

const open = ref(false);
const title = ref("");


const close = () => {
    open.value = false;
    form.reset();
    form.clearErrors();
};

const add = () => {
    title.value = "Nouveau Article";
    open.value = true;
};

const update = (rowData) => {

    router.visit(`${route('article.show', {article: rowData.id})}`, {
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
    const url = form.id ? route('article.update', form.id) : route('article.store');
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
            <form-item required has-feedback label="Famille Article" :help="form.errors.nom_famille_article">
                <AutocompleteComponent :options="localFamilleArticle"
                                       size="large"
                                       placeholder=""
                                        v-model:value="form.nom_famille_article"
                                       >
                    <template #option="{ option }">
                        <div class="truncate">
                            {{ option.label }}
                        </div>
                    </template>
                </AutocompleteComponent>
            </form-item>
            <form-item  has-feedback label="Marque" :help="form.errors.marque">
                <a-input
                    v-model:value="form.marque"
                    placeholder=""
                    size="large"
                />
            </form-item>
            <form-item required has-feedback label="Référence" :help="form.errors.reference">
                <a-input
                    v-model:value="form.reference"
                    placeholder=""
                    size="large"
                />
            </form-item>
            <form-item required has-feedback label="Désignation" :help="form.errors.designation">
                <a-input
                    v-model:value="form.designation"
                    placeholder=""
                    size="large"
                />
            </form-item>
            <form-item required has-feedback label="Type d'article" :help="form.errors.type_article">
                <a-select
                    ref="select"
                    :options="TYPE_ARTICLE"
                    v-model:value="form.type_article"
                    :allowClear="true"
                    size="large"
                >

                </a-select>
            </form-item>

            <form-item has-feedback label="Valeur" :help="form.errors.valeur">
                <InputNumberWithSepartor
                    v-model="form.valeur"
                    size="large"
                    class="w-full"
                />
            </form-item>

            <form-item has-feedback label="Seuil de stock" :help="form.errors.seuil_stock">
                <InputNumberWithSepartor
                    v-model="form.seuil_stock"
                    size="large"
                    class="w-full"
                    :min="0"
                    placeholder="Quantité minimale avant alerte"
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
