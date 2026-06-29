<script setup>
import { ref } from "vue";
import {router, useForm} from "@inertiajs/vue3";
import { UploadOutlined, CloseOutlined, PlusOutlined, DeleteOutlined } from '@ant-design/icons-vue';
import FormItem from "@/Components/FormItem.vue";
import FormModal from "@/Components/FormModal.vue";
import {gotoSearch} from "../../../../Utils/FiltreUtils.js";
import ButtonIcon from "@/Components/ButtonIcon.vue";
import {message} from "ant-design-vue";
import dayjs from "dayjs";
import InputNumberWithSepartor from "@/Components/InputNumberWithSepartor.vue";

const props = defineProps({
    flash: Object,
    magasins: {
        type: Array,
        default: []
    }
});
const dateFormat = 'DD/MM/YYYY';

const form = useForm({
    id: null,
    date_inventaire: dayjs().format('YYYY-MM-DD'),
    magasin_id: null,
    details: []
});

const filtre = ref({search: null, magasin_id: ''});
const LIST_ARTICLE = ref([]);
const open = ref(false);
const title = ref("");

const close = () => {
    open.value = false;
    form.reset();
    form.clearErrors();
};

const add = () => {
    title.value = "Nouvel Élément";
    open.value = true;
    LIST_ARTICLE.value = [];
    form.magasin_id = props.magasins[0].value;
    fetchArticle();
};

const update = (rowData) => {

    router.visit(`${route('paramelement.show', {paramelement: rowData.id})}`, {
        preserveState: true,
        preserveScroll: true,
        only: ['flash'],
        onSuccess: () => {
            console.log(props.flash);
            Object.keys(props.flash.data).forEach(key => {
                console.log(key, props.flash.data[key]);
                form[key] = props.flash.data[key];
            });
            title.value = "Modifier";
            open.value = true;
        },
    });
};

const submit = () => {
    form.clearErrors();
    const method = form.id ? 'put' : 'post';
    const url = form.id ? route('article_inventaire.update', form.id) : route('article_inventaire.store');

    form.transform(data => ({
        ...data,
        _method: method.toUpperCase()
    })).post(url, {
        onSuccess: () => close(),
        forceFormData: true
    });
};

const pushDetail = (article) => {
    // Vérification plus concise de l'existence de l'article
    const articleExists = form.details.some(detail => detail.article_id === article.id);

    if (articleExists) {
        message.warning('Cet article est déjà enregistré');
        return;
    }

    // Ajout de l'article avec calcul immédiat de l'écart
    form.details.push({
        article_id: article.id,
        stock_reel: 0,
        stock_theorique: article.stock,
        ecart_stock: -article.stock,
        remarque: '',
        designation: article.designation,
        reference: article.reference
    });
}

const spliceDetal= (index) => {
    form.details.splice(index, 1);
}


const calculEcartStock = ()=> {
    form.details.forEach(value => {
        value.ecart_stock = parseFloat(value.stock_reel) - parseFloat(value.stock_theorique)
    })
}

const fetchArticle = () => {
    const search = "";
    const url = route('article.getarticle',{magasin_id: form.magasin_id, search: filtre.value.search});
    // const url = route('article.bymagasin_info', {magasin: form.magasin_id, search: filtre.value.search});
    router.visit(url, {
        preserveState: true,
        preserveScroll: true,
        only: ['flash'],
        onSuccess: (response) => {
            console.log(response.props.flash.data.data)
            LIST_ARTICLE.value = response.props.flash.data.data
        },
    });
}

const handleSearch = () => {
    fetchArticle();
}

defineExpose({add, update, close});
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
                <a-form layout="vertical" >
                    <div class="grid xl:grid-cols-2 gap-0 xl:gap-2">
                        <div>
                            <form-item required has-feedback label="Date Inventaire" :help="form.errors.date_inventaire">
                                <a-date-picker
                                    v-model:value="form.date_inventaire"
                                    :format="dateFormat"
                                    size="large"
                                    class="w-full text-center"
                                    :value-format="'YYYY-MM-DD'"
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
                                >
                                </a-select>
                            </form-item>
                        </div>
                    </div>





                    <table class="min-w-full divide-y ">
                        <thead class="bg-primary text-white">
                        <tr>
                            <th colspan="4">
                                <a-input-search
                                    class="border-primary !rounded-l-none !border !border-r-0"
                                    v-model:value="filtre.search"
                                    @search="handleSearch"
                                />
                            </th>
                        </tr>
                        <tr>
                            <th class="py-1.5">Désignation</th>
                            <th>Référence</th>
                            <th width="80px">Stock</th>
                            <th width="40px">
                                -
                            </th>
                        </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-green-900 border ">
                            <tr v-for="(a, index_article) in LIST_ARTICLE" :key="index_article">
                                <td>
                                    <a-input readonly v-model:value="a.designation" class="rounded-none border-0 focus:border-b-green-600" size="large"/>

                                </td>
                                <td>
                                    <a-input readonly v-model:value="a.reference" class="rounded-none border-0 focus:border-b-green-600" size="large"/>
                                </td>
                                <td class="text-right">
                                    <InputNumberWithSepartor
                                        min="0"
                                        v-model="a.stock"
                                        readonly
                                        class="rounded-none w-full !text-right border-0 focus:border-b-green-600" size="large"
                                    />
                                </td>
                                <td class="!text-center cursor-pointer hover:!bg-primary/5"  @click="pushDetail(a)">
                                    <font-awesome-icon
                                        class=" text-primary cursor-pointer"
                                        icon="fa-plus"
                                    />
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </a-form>

            </div>
            <div class="col-span-8">
                <table class="min-w-full divide-y ">
                    <thead class="bg-primary text-white">
                    <tr>
                        <th class="py-1.5">Désignation</th>
                        <th>Référence</th>
                        <th width="100px">Stock Théorique</th>
                        <th width="100px">Stock Réel</th>
                        <th width="100px">Ecar Stock</th>
                        <th>Remarque</th>
                        <th width="40px">
                            -
                        </th>
                    </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-green-900 border ">
                    <tr v-for="(a, index_article) in form.details" :key="index_article">
                        <td class="border border-primary/25 px-2">
                            {{a.designation}}
<!--                            <a-input readonly v-model:value="a.designation" class="rounded-none border-0 focus:border-b-green-600" size="large"/>-->
                        </td>
                        <td class="border border-primary/25 px-2">
                            {{a.reference}}
<!--                            <a-input readonly v-model:value="a.reference" class="rounded-none border-0 focus:border-b-green-600" size="large"/>-->
                        </td>
                        <td class="border border-primary/25 !text-center">
                            <InputNumberWithSepartor
                                v-model="a.stock_theorique"
                                readonly
                                class="rounded-none w-full !text-right border-0 focus:border-b-green-600" size="large"
                            />

                        </td>
                        <td class="border border-primary/25">

                            <InputNumberWithSepartor
                                v-model="a.stock_reel"
                                @input="calculEcartStock"
                                class="rounded-none w-full !text-right border-0 focus:border-b-green-600" size="large"
                            />
<!--                            <a-input-number
                                min="0"
                                @input="calculEcartStock"
                                v-model:value="a.stock_reel" class="rounded-none w-full !text-center border-0 focus:border-b-green-600" size="large"
                            />-->
                        </td>
                        <td class="border border-primary/25">
                            <InputNumberWithSepartor
                                v-model="a.ecart_stock"
                                readonly
                                class="rounded-none w-full !text-right border-0 focus:border-b-green-600" size="large"
                            />

<!--                            <a-input-number
                                min="0"
                                readonly
                                :input-style="{ textAlign: 'center' }"
                                v-model:value="a.ecart_stock" class="rounded-none w-full !text-center border-0 focus:border-b-green-600" size="large"
                            />-->
                        </td>
                        <td class="border border-primary/25">
                            <a-input placeholder="Votre remarque " v-model:value="a.remarque" class="rounded-none border-0 focus:border-b-green-600" size="large"/>
                        </td>

                        <td class="text-center cursor-pointer hover:!bg-red-100" @click="spliceDetal(index_article)">
                            <font-awesome-icon

                                class=" text-red-500 cursor-pointer"
                                icon="fa-trash"
                            />
<!--                            <ButtonIcon size="small" @click="pushDetail(a)" icon="fa-trash" type="primary" />-->
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
</style>
