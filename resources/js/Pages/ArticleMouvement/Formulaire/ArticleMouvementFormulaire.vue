<script>
const TYPE_MVT = [
    {label: 'Entrée', value: 'Entrée'},
    {label: 'Sortie', value: 'Sortie'},
    {label: 'Transfert', value: 'Transfert'},
];
</script>

<script setup>
import { computed, ref, watch, toRefs } from "vue";
import { router, useForm } from "@inertiajs/vue3";
import FormItem from "@/Components/FormItem.vue";
import FormModal from "@/Components/FormModal.vue";
import { message } from "ant-design-vue";
import InputNumberWithSepartor from "@/Components/InputNumberWithSepartor.vue";
import AutocompleteComponent from "@/Components/AutoCompleteComponent/AutocompleteComponent.vue";
import dayjs from "dayjs";

const props = defineProps({
    flash: Object,
    magasins: {
        type: Array,
        default: () => [],
    },
    vehicules: {
        type: Array,
        default: () => [],
    },
});

const { magasins, vehicules } = toRefs(props);
const dateFormat = 'DD/MM/YYYY';

const form = useForm({
    id: null,
    date_transaction: dayjs().format('YYYY-MM-DD'),
    reference_mouvement: null,
    magasin_id: null,
    vehicule_id: null,
    type_mvt: null,
    motif: null,
    magasin_cible: null,
    details: []
});

const filtre = ref({ search: null, magasin_id: null, exclude_type_article: 'PNEU' });
const LIST_ARTICLE = ref([]);
const open = ref(false);
const title = ref("");


const filteredMagasinCibleOptions = computed(() => {
    if (!form.magasin_id) return magasins.value;
    return magasins.value.filter(magasin => magasin.value !== form.magasin_id);
});

watch(() => [form.type_mvt, form.magasin_id], () => {
    if (form.type_mvt !== 'Transfert' || !form.magasin_id) {
        form.magasin_cible = null;
    }
});

const close = () => {
    open.value = false;
    form.reset();
    form.clearErrors();
};

const add = () => {
    router.visit(`${route('article_mouvement.getreference')}`, {
        preserveState: true,
        preserveScroll: true,
        only: ['flash'],
        onSuccess: (reponse) => {
            const response = reponse.props.flash.data;
            form.reference_mouvement = response.reference_mouvement;
            title.value = "Nouvau Mouvement N° : "+form.reference_mouvement;
            open.value = true;

            if (props.magasins.length > 0) {

                form.magasin_id = props.magasins[0].value;
                filtre.value.magasin_id = props.magasins[0].value;
                fetchArticle();
            }
        },
    });
};

const update = (rowData) => {

    router.visit(`${route('article_mouvement.show', {article_mouvement: rowData.id})}`, {
        preserveState: true,
        preserveScroll: true,
        only: ['flash'],
        onSuccess: (reponse) => {
            const response = reponse.props.flash.data;
            Object.keys(response).forEach(key => {
                form[key] = response[key];
            });
            fetchArticle();
            title.value = "Edit Bon Commande N° : "+form.reference_mouvement;
            open.value = true;
        },
    });
};

const submit = () => {
    form.clearErrors();
    // handlePrixUnitaire();
    const method = form.id ? 'put' : 'post';
    const url = form.id ? route('article_mouvement.update', form.id) : route('article_mouvement.store');

    form.transform(data => ({
        ...data,
        _method: method.toUpperCase()
    })).post(url, {
        onSuccess: () => close(),
        forceFormData: true
    });
};

const pushDetail = (article) => {
    if (form.details.some(detail => detail.article_id === article.id)) {
        message.warning('Cet article est déjà enregistré');
        return;
    }

    form.details.push({
        article_id: article.id,
        qte_mouvement: 0,
        designation: article.designation,
        reference: article.reference,
        stock: article.stock // Ajout du stock pour référence
    });
};

const spliceDetail = (index) => {
    form.details.splice(index, 1);
};

const fetchArticle = () => {
    filtre.value.magasin_id = form.magasin_id;
    const url = route('article.getarticle',filtre.value);

    router.visit(url, {
        preserveState: true,
        preserveScroll: true,
        only: ['flash'],
        onSuccess: (response) => {
            LIST_ARTICLE.value = response.props.flash.data.data;
        },
    });
}

const handleSearch = () => {
    fetchArticle();
};

defineExpose({ add, update, close });
</script>

<template>
    <FormModal
        v-model:open="open"
        :loading="form.processing"
        @close="close"
        @submit="submit"
        :titre="title"
        size="full_screen"
        :show_champ_obligatoir="false"
    >
        <div class="grid xl:grid-cols-12 md:grid-cols-12 grid-cols-12 gap-4">
            <div class="lg:col-span-4 col-span-12">
                <a-form layout="vertical">
                    <div class="grid xl:grid-cols-2 gap-0 xl:gap-2">
                        <div>
                            <form-item required has-feedback label="Date" :help="form.errors.date_transaction">
                                <a-date-picker
                                    v-model:value="form.date_transaction"
                                    :format="dateFormat"
                                    size="large"
                                    class="w-full text-center"
                                    :value-format="'YYYY-MM-DD'"
                                />
                            </form-item>

                            <form-item required has-feedback label="Type Mouvement" :help="form.errors.type_mvt">
                                <autocomplete-component
                                    :options="TYPE_MVT"
                                    v-model="form.type_mvt"
                                    class="!w-full"
                                    size="large"
                                    placeholder=""
                                />
                            </form-item>
                        </div>

                        <div>
                            <form-item required has-feedback label="Magasin" :help="form.errors.magasin_id">
                                <a-select
                                    v-model:value="form.magasin_id"
                                    placeholder=""
                                    class="w-full"
                                    size="large"
                                    allow-clear
                                    :options="magasins"
                                    @change="fetchArticle"
                                />
                            </form-item>

                            <form-item required has-feedback label="Magasin Cible" :help="form.errors.magasin_cible">
                                <a-select
                                    v-model:value="form.magasin_cible"
                                    placeholder=""
                                    class="w-full"
                                    size="large"
                                    allow-clear
                                    :options="filteredMagasinCibleOptions"
                                    :disabled="form.type_mvt !== 'Transfert'"
                                />
                            </form-item>
                        </div>
                    </div>

                    <form-item has-feedback label="Véhicule" :help="form.errors.vehicule_id">
                        <autocomplete-component
                            :options="vehicules"
                            v-model="form.vehicule_id"
                            class="!w-full"
                            :disabled="form.type_mvt === 'Transfert'"
                            size="large"
                            :fieldConfig="{label: 'label', value: 'label'}"
                            placeholder=""
                        />
                    </form-item>

                    <form-item has-feedback label="Commentaire" :help="form.errors.motif">
                        <a-textarea
                            v-model:value="form.motif"
                            placeholder=""
                            :auto-size="{ minRows: 4, maxRows: 6 }"
                        />
                    </form-item>

                    <table class="min-w-full divide-y">
                        <thead class="bg-primary text-white">
                        <tr>
                            <th colspan="4">
                                <a-input-search
                                    v-model:value="filtre.search"
                                    @search="handleSearch"
                                    class="border-primary !rounded-l-none !border !border-r-0"
                                />
                            </th>
                        </tr>
                        <tr>
                            <th class="py-1.5">Désignation</th>
                            <th>Référence</th>
                            <th width="80px">Stock</th>
                            <th width="40px">-</th>
                        </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-green-900 border">
                        <tr v-for="(article, index) in LIST_ARTICLE" :key="index">
                            <td class="px-2">{{ article.designation }}</td>
                            <td class="px-2">{{ article.reference }}</td>
                            <td>
                                <a-input
                                    readonly
                                    v-model:value="article.stock"
                                    class="rounded-none border-0 focus:border-b-green-600"
                                    size="large"
                                />
                            </td>
                            <td
                                class="!text-center cursor-pointer hover:!bg-primary/5"
                                @click="pushDetail(article)"
                            >
                                <font-awesome-icon
                                    class="text-primary cursor-pointer"
                                    icon="fa-plus"
                                />
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </a-form>
            </div>

            <div class="col-span-8">
                <table class="min-w-full divide-y">
                    <thead class="bg-primary text-white">
                    <tr>
                        <th class="py-1.5">Désignation</th>
                        <th>Référence</th>
                        <th width="150">Quantité</th>
                        <th width="40px">-</th>
                    </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-green-900 border">
                    <tr v-for="(detail, index) in form.details" :key="index">
                        <td class="border border-primary/25">
                            <a-input
                                readonly
                                v-model:value="detail.designation"
                                class="rounded-none border-0 focus:border-b-green-600"
                                size="large"
                            />
                        </td>
                        <td class="border border-primary/25">
                            <a-input
                                readonly
                                v-model:value="detail.reference"
                                class="rounded-none border-0 focus:border-b-green-600"
                                size="large"
                            />
                        </td>
                        <td class="border border-primary/25">
                            <InputNumberWithSepartor
                                min="0"
                                :max="detail.stock || undefined"
                                v-model:value="detail.qte_mouvement"
                                class="rounded-none w-full !text-center border-0 focus:border-b-green-600"
                                size="large"
                            />
                        </td>
                        <td
                            class="text-center cursor-pointer hover:!bg-red-100"
                            @click="spliceDetail(index)"
                        >
                            <font-awesome-icon
                                class="!m-0 text-red-500 cursor-pointer"
                                icon="fa-trash"
                            />
                        </td>
                    </tr>
                    </tbody>
                </table>
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

tfoot tr td {
    border: 1px solid #c9c9c9c9;
}

:deep(.ant-input-group-wrapper > .ant-input-wrapper > .ant-input),
:deep(.ant-input-group-wrapper > .ant-input-wrapper > .ant-input-group-addon > .ant-btn) {
    @apply !rounded-none
}
</style>
