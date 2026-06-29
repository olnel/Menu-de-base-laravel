<script setup>
import {ref} from "vue";
import {router, useForm} from "@inertiajs/vue3";
import FormItem from "@/Components/FormItem.vue";
import FormModal from "@/Components/FormModal.vue";
import {message} from "ant-design-vue";;
import InputNumberWithSepartor from "@/Components/InputNumberWithSepartor.vue";
import AutocompleteComponent from "@/Components/AutoCompleteComponent/AutocompleteComponent.vue";
import dayjs from "dayjs";

const props = defineProps({
    flash: Object,
    fournisseurs: {
        type: Array,
        default: [],
    }
});
const dateFormat = 'DD/MM/YYYY';

const form = useForm({
    id: null,
    date_boncommande: dayjs().format('YYYY-MM-DD'),
    numero_bon_commande: null,
    nom_fournisseur: null,
    show_prix_unitaire: 0,
    details: []
});


const filtre = ref({search: null});

const LIST_ARTICLE = ref([]);
const open = ref(false);
const title = ref("");

const close = () => {
    open.value = false;
    form.reset();
    form.clearErrors();
};

const add = () => {
    router.visit(`${route('article_boncommande.generatenumero')}`, {
        preserveState: true,
        preserveScroll: true,
        only: ['flash'],
        onSuccess: (reponse) => {
            const response = reponse.props.flash.data;
            // const article = reponse.props.flash.article;

            form.numero_bon_commande = response.numero_bon_commande;
            title.value = "Bon Commande N° : "+form.numero_bon_commande;
            open.value = true;
            fetchArticle();
            // LIST_ARTICLE.value = article.data;
        },
    });
};

const update = (rowData) => {

    router.visit(`${route('article_boncommande.show', {article_boncommande: rowData.id})}`, {
        preserveState: true,
        preserveScroll: true,
        only: ['flash'],
        onSuccess: (reponse) => {
            const response = reponse.props.flash.data;
            Object.keys(response).forEach(key => {
                form[key] = response[key];
            });
            fetchArticle();
            title.value = "Edit Bon Commande N° : "+form.numero_bon_commande;
            open.value = true;
        },
    });
};

/*const handlePrixUnitaire = () => {
    if (form.show_prix_unitaire === 0) {
        form.details = form.details.map(item => {
            return {...item, prix_unitaire: 0};
        });
    }

}*/

const submit = () => {
    form.clearErrors();
    // handlePrixUnitaire();
    const method = form.id ? 'put' : 'post';
    const url = form.id ? route('article_boncommande.update', form.id) : route('article_boncommande.store');

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
        qte_commander: 0,
        designation: article.designation,
        prix_unitaire: article.valeur,
        reference: article.reference
    });
}

const spliceDetal = (index) => {
    form.details.splice(index, 1);
}

const fetchArticle = () => {
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
    fetchArticle()
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

<!--        <div class="grid xl:grid-cols-12 md:grid-cols-12 grid-cols-12 gap-4">-->
        <div class="grid xl:grid-cols-12 md:grid-cols-12 grid-cols-12 gap-4">
            <div class="lg:col-span-4 col-span-12">
                <a-form layout="vertical">
                    <div class="grid xl:grid-cols-2 gap-0 xl:gap-2">
                        <div>
                            <form-item required has-feedback label="Date" :help="form.errors.date_boncommande">
                                <a-date-picker
                                    v-model:value="form.date_boncommande"
                                    :format="dateFormat"
                                    size="large"
                                    class="w-full text-center"
                                    :value-format="'YYYY-MM-DD'"
                                />
                            </form-item>
                        </div>

                        <div>
                            <form-item required has-feedback label="Fournisseur" :help="form.errors.nom_fournisseur">
                                <autocomplete-component :options="fournisseurs"
                                                        v-model="form.nom_fournisseur"
                                                        class=" !w-full"
                                                        size="large"
                                                        placeholder=""
                                />
                            </form-item>
                        </div>
                    </div>




<!--                    <form-item has-feedback label="N° Bon de Commande" :help="form.errors.numero_bon_commande">
                        <a-input v-model:value="form.numero_bon_commande" size="large"/>

                    </form-item>-->


                    <table class="min-w-full divide-y ">
                        <thead class="bg-primary text-white">
                        <tr>
                            <th colspan="3">
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
                            <!--                            <th width="80px">Stock</th>-->
                            <th width="40px">
                                -
                            </th>
                        </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-green-900 border ">
                        <tr v-for="(a, index_article) in LIST_ARTICLE" :key="index_article">

                            <td>
                                <a-input readonly v-model:value="a.designation"
                                         class="rounded-none border-0 focus:border-b-green-600" size="large"/>

                            </td>
                            <td>
                                <a-input readonly v-model:value="a.reference"
                                         class="rounded-none border-0 focus:border-b-green-600" size="large"/>
                            </td>
                            <td class="!text-center cursor-pointer hover:!bg-primary/5"   @click="pushDetail(a)">
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
                <div>
                    <a-checkbox v-model:checked="form.show_prix_unitaire" :true-value="1" :false-value="0">Afficher Prix Unitaire</a-checkbox>
                </div>
                <table class="min-w-full divide-y ">
                    <thead class="bg-primary text-white">
                    <tr>
                        <th class="py-1.5">Désignation</th>
                        <th>Référence</th>
                        <th width="150">Qte</th>
                        <th v-show="form.show_prix_unitaire">Prix Unitaire</th>
                        <th width="40px">
                            -
                        </th>
                    </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-green-900 border ">
                    <tr v-for="(a, index_article) in form.details" :key="index_article">
                        <td class="border border-primary/25">
                            <a-input readonly v-model:value="a.designation"
                                     class="rounded-none border-0 focus:border-b-green-600" size="large"/>
                        </td>
                        <td class="border border-primary/25">
                            <a-input readonly v-model:value="a.reference"
                                     class="rounded-none border-0 focus:border-b-green-600" size="large"/>
                        </td>
                        <td class="border border-primary/25">
                            <InputNumberWithSepartor
                                min="0"
                                v-model:value="a.qte_commander"
                                class="rounded-none w-full !text-center border-0 focus:border-b-green-600" size="large"
                            />
                        </td>
                        <td v-show="form.show_prix_unitaire" class="border border-primary/25">
                            <InputNumberWithSepartor
                                min="0"
                                v-model="a.prix_unitaire"
                                class="rounded-none w-full !text-right border-0 focus:border-b-green-600" size="large"
                            />
                        </td>

                        <td class="text-center cursor-pointer hover:!bg-red-100"  @click="spliceDetal(index_article)">
                            <font-awesome-icon
                                class=" !m-0 text-red-500 cursor-pointer"
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
</style>
