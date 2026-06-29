<script setup>
import {ref} from "vue";
import {router, useForm} from "@inertiajs/vue3";
import FormItem from "@/Components/FormItem.vue";
import FormModal from "@/Components/FormModal.vue";
import {message} from "ant-design-vue";

;
import InputNumberWithSepartor from "@/Components/InputNumberWithSepartor.vue";
import AutocompleteComponent from "@/Components/AutoCompleteComponent/AutocompleteComponent.vue";
import dayjs from "dayjs";
import {DeleteOutlined, PlusOutlined} from "@ant-design/icons-vue";

const props = defineProps({
    flash: Object,
    clients: {
        type: Array,
        default: [],
    }
});
const dateFormat = 'DD/MM/YYYY';

const form = useForm({
    id: null,
    nom_client: null,
    date_devis: dayjs().format('YYYY-MM-DD'),
    validite_devis: null,
    condition_delais: null,
    condition_paiement: null,
    montant_total: 0,
    numero_devis: null,
    count_devis: 0,
    details: [
        {
            quantite: 0,
            prix_unitaire: 0,
            montant: 0,
            libelle: null
        }
    ]
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
    // title.value = "Nouveau Dévis Client";
    // open.value = true;
    router.visit(`${route('devisclient.generatenumero')}`, {
        preserveState: true,
        preserveScroll: true,
        only: ['flash'],
        onSuccess: (reponse) => {
            const response = reponse.props.flash.data;
            form.numero_devis = response.numero_devis;
            title.value = "DEVIS N° : "+form.numero_devis;
            open.value = true;
        },
    });
};

const update = (rowData) => {

    router.visit(`${route('devisclient.show', {devisclient: rowData.id})}`, {
        preserveState: true,
        preserveScroll: true,
        only: ['flash'],
        onSuccess: (reponse) => {
            const response = reponse.props.flash.data;
            Object.keys(response).forEach(key => {
                form[key] = response[key];
            });
            fetchArticle();
            title.value = "Edition Devis" ;
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
    const method = form.id ? 'put' : 'post';
    const url = form.id ? route('devisclient.update', form.id) : route('devisclient.store');

    form.transform(data => ({
        ...data,
        _method: method.toUpperCase()
    }))[method](url, {
        onSuccess: () => {
            close();
        },
        onError: (errors) => {
            Object.values(errors).forEach(errMsg => {
                message.error(errMsg);
            });
        },
        forceFormData: true
    });
};

const pushDetail = () => {
    form.details.push({
        qte_commander: 0,
        quantite: 0,
        prix_unitaire: 0,
        montant: 0,
        libelle: null
    });
}

const spliceDetal = (index) => {
    form.details.splice(index, 1);
}

const fetchArticle = () => {
    const url = route('article.getarticle', filtre.value);

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

const calculMontant = () => {
    form.details.forEach(item => {
        item.montant = parseFloat( item.quantite) * parseFloat( item.prix_unitaire);
    });
    form.montant_total = form.details.reduce((acc, item) => acc + item.montant, 0);
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
        size="lg"
        :show_champ_obligatoir="false"
    >
        <a-form layout="vertical">
            <a-form layout="vertical">
                <div class="grid xl:grid-cols-2 lg:grid-cols-2 md:grid-cols-1 sm:grid-cols-1 grid-cols-1 gap-4">
                    <div>
                        <form-item required has-feedback label="Date" :help="form.errors.date_devis">
                            <a-date-picker
                                v-model:value="form.date_devis"
                                :format="dateFormat"
                                size="large"
                                class="w-full text-center"
                                :value-format="'YYYY-MM-DD'"
                            />
                        </form-item>

                        <form-item required has-feedback label="Validité du Devis" :help="form.errors.validite_devis">
                            <a-date-picker
                                v-model:value="form.validite_devis"
                                :format="dateFormat"
                                size="large"
                                class="w-full text-center"
                                :value-format="'YYYY-MM-DD'"
                            />
                        </form-item>
                    </div>
                    <div>
                        <form-item required has-feedback label="Client" :help="form.errors.nom_client">

                            <AutocompleteComponent
                                v-model="form.nom_client"
                                :options="clients"
                                placeholder="Rechercher ou créer un client"
                                :field-config="{
                                label: 'label',
                                value: 'label',
                                search: 'label',
                            }"
                            />
<!--                            <autocomplete-component :options="props.clients"
                                                    v-model="form.nom_client"
                                                    class=" !w-full"
                                                    size="large"
                                                    :field-config="{
                                                        label: 'label',
                                                        value: 'label',
                                                        search: 'label'
                                                    }"
                                                    placeholder=""
                            />-->
                        </form-item>
                        <form-item required has-feedback label="Conditions de Délais" :help="form.errors.condition_delais">
                            <a-input placeholder="Ex: 30 jours Ouvrés" v-model:value="form.condition_delais" size="large" class="w-full"/>
                        </form-item>
                    </div>
                </div>
                <form-item required has-feedback label="Conditions de Paiement" :help="form.errors.condition_paiement">
                    <a-textarea placeholder="Ex: 50% à la commande, 50% à la livraison"  v-model:value="form.condition_paiement" size="large" class="w-full"/>
                </form-item>

                <h2>Détails</h2>

                <table class="min-w-full ">
                    <thead class="bg-primary/80 sticky top-0 z-10 text-nowrap">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider" width="300px">Libellé</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-white uppercase tracking-wider" >Qte</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-white uppercase tracking-wider">P.U</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-white uppercase tracking-wider">Montant</th>
                        <th class="px-4 py-2 text-center text-xs font-semibold text-white uppercase tracking-wider" width="50px">
                            <a-button
                                type="primary"
                                size="middle"
                                @click="pushDetail"
                                class="bg-white text-green-600 border-white hover:bg-green-50 hover:border-green-100"
                            >
                                <font-awesome-icon icon="fa-solid fa-plus" class="!me-0"/>
                            </a-button>
                        </th>
                    </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-green-900 border ">
                    <tr v-for="(item, index) in form.details" :key="index">
                        <td class="border border-primary/25">
                            <a-input v-model:value="item.libelle" class="rounded-none border-0 focus:border-b-green-600" size="large"/>
                        </td>
                        <td class="border border-primary/25">
                            <InputNumberWithSepartor
                                v-model="item.quantite"
                                class="rounded-none border-0 focus:border-b-green-600"
                                size="large"
                                @input="calculMontant"
                                :min="0"
                            />
                        </td>
                        <td class="border border-primary/25">
                            <InputNumberWithSepartor
                                v-model="item.prix_unitaire"
                                class="rounded-none border-0 focus:border-b-green-600"
                                size="large"
                                @input="calculMontant"
                                :min="0"
                            />
                        </td>
                        <td class="border border-primary/25">
                            <InputNumberWithSepartor
                                v-model="item.montant"
                                class="rounded-none border-0 focus:border-b-green-600"
                                size="large"
                                :min="0"
                                readonly=""
                            />
                        </td>
                        <td class="border border-primary/25 !text-center">
                            <a-button
                                danger
                                type="text"
                                size="small"
                                @click="spliceDetal(index)"
                                v-if="form.details.length > 1"
                            >
                                <DeleteOutlined/>
                            </a-button>
                        </td>
                    </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td class="text-right py-1 !text-xl !pr-3" colspan="3">Total Montant</td>
                            <td>
                                <InputNumberWithSepartor
                                    v-model="form.montant_total"
                                    class="rounded-none border-0 focus:border-b-green-600"
                                    size="large"
                                    :min="0"
                                />
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </a-form>
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
