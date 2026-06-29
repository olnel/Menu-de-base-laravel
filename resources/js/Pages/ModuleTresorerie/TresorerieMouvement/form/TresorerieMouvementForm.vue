<script>
    import modePaiement from "../../../../../Utils/modePaiement.js";

    const TYPE_MVT = [
        {value: 'ENTREE', label: 'Encaissement'},
        {value: 'SORTIE', label: 'Décaissement'},
        {value: 'TRANSFERT', label: 'Transfert'},
        {value: 'AJUSTEMENT', label: 'Ajustement'},
    ];
    const MODE_PAIEMENET = modePaiement;
</script>
<script setup>
import {ref, watch, computed} from "vue";
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
    tresoreries: {
        type: Array,
        default: [],
    },
    posteDepenses: {
        type: Array,
        default: () => [],
    },
});
const dateFormat = 'DD/MM/YYYY';
const localPosteDepense = ref([...props.posteDepenses]);

watch(() => props.posteDepenses, (newVal) => {
    localPosteDepense.value = [...newVal];
}, {deep: true});

const form = useForm({
    id: null,
    tresorerie_id: null,
    tresorerie_id_cible: null,
    date_mvt: dayjs().format('YYYY-MM-DD'),
    libelle_mvt: null,
    reference_mvt: null,
    mode_paiement: null,
    commentaire: null,
    type_mvt: null,
    poste_depense: null,
    montant: 0,
});


const tresoreriesCibleOptions = computed(() =>
    props.tresoreries.filter(t => t.value !== form.tresorerie_id)
);

watch(() => form.tresorerie_id, (newVal) => {
    if (form.tresorerie_id_cible === newVal) {
        form.tresorerie_id_cible = null;
    }
});

const filtre = ref({search: null});

const open = ref(false);
const title = ref("");

const close = () => {
    open.value = false;
    form.reset();
    form.clearErrors();
};

const add = () => {
    form.clearErrors();
    title.value = "Nouveau Mouvement";
    open.value = true;

};

const update = (rowData) => {

    router.visit(`${route('tresorerie_mouvement.show', {tresorerie_mouvement: rowData.id})}`, {
        preserveState: true,
        preserveScroll: true,
        only: ['flash'],
        onSuccess: (reponse) => {
            const response = reponse.props.flash.data;
            Object.keys(response).forEach(key => {
                form[key] = response[key];
            });
            title.value = "Modification" ;
            open.value = true;
        },
    });
};


const submit = () => {
    form.clearErrors();

    const method = form.id ? 'put' : 'post';
    const url = form.id ? route('tresorerie_mouvement.update', form.id) : route('tresorerie_mouvement.store');

    form.transform(data => ({
        ...data,
        _method: method.toUpperCase()
    })).post(url, {
        onSuccess: (reponse) => {
            console.log(reponse)
            const success = reponse.props.message.success;
            console.log(success);
            if (success) {
                close()
            }
        },
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
        size="lg"
        :show_champ_obligatoir="false"
    >
        <a-form layout="vertical">
            <a-form layout="vertical">
                <div class="grid xl:grid-cols-2 lg:grid-cols-2 md:grid-cols-1 sm:grid-cols-1 grid-cols-1 gap-4">
                    <div>
                        <form-item required has-feedback label="Date" :help="form.errors.date_mvt">
                            <a-date-picker
                                v-model:value="form.date_mvt"
                                :format="dateFormat"
                                size="large"
                                class="w-full text-center"
                                :value-format="'YYYY-MM-DD'"
                            />
                        </form-item>

                        <form-item required has-feedback label="Motif" :help="form.errors.libelle_mvt">
                            <a-input
                                placeholder=""
                                v-model:value="form.libelle_mvt"
                                size="large"
                                class="w-full"
                            />
                        </form-item>

                        <form-item required has-feedback :label="'Trésorerie ' + (form.type_mvt === 'TRANSFERT' ? 'cible' : '')" :help="form.errors.tresorerie_id">
                            <autocomplete-component
                                :options="props.tresoreries"
                                v-model="form.tresorerie_id"
                                class="!w-full"
                                size="large"
                                option-label-prop="label"
                                placeholder="choisissez une trésorerie"
                            />
                        </form-item>

                        <form-item v-if="form.type_mvt ==='TRANSFERT'" required has-feedback label="Trésorerie qui encaisse" :help="form.errors.tresorerie_id">
                            <autocomplete-component :options="tresoreriesCibleOptions"
                                                    v-model="form.tresorerie_id_cible"
                                                    class=" !w-full"
                                                    size="large"
                                                    :field-config="{
                                                        label: 'label',
                                                        value: 'value',
                                                        search: 'label'
                                                    }"
                                                    placeholder="choisissez une trésorerie"
                            />
                        </form-item>
                    </div>
                    <div>
                        <form-item required has-feedback label="Type de Mouvement" :help="form.errors.type_mvt">

                            <autocomplete-component :options="TYPE_MVT"
                                                    v-model="form.type_mvt"
                                                    class=" !w-full"
                                                    size="large"
                                                    placeholder="Sélectionnez le type de mouvement"
                            />
                        </form-item>
                        <form-item required has-feedback label="Mode de paiement" :help="form.errors.mode_paiement">
                            <autocomplete-component :options="MODE_PAIEMENET"
                                                    v-model="form.mode_paiement"
                                                    class=" !w-full"
                                                    :field-config="{
                                                        label: 'label',
                                                        value: 'label',
                                                        search: 'label'
                                                    }"
                                                    size="large"
                                                    placeholder="Sélectionnez le mode de paiement"
                            />
                        </form-item>
                        <form-item required has-feedback label="Montant" :help="form.errors.montant">
                            <InputNumberWithSepartor
                                v-model="form.montant"
                                size="large"
                                class="w-full text-right"
                                />
                        </form-item>
                    </div>
                </div>

                <form-item v-if="form.type_mvt ==='SORTIE'" has-feedback label="Poste de dépense" :help="form.errors.poste_depense">

                    <autocomplete-component :options="localPosteDepense"
                                            v-model="form.poste_depense"
                                            class=" !w-full"
                                            :field-config="{
                                                        label: 'libelle',
                                                        value: 'libelle',
                                                        search: 'libelle'
                                                    }"
                                            size="large"
                                            placeholder=""
                    />

                </form-item>

                <form-item  has-feedback label="Commentaire" :help="form.errors.commentaire">
                    <a-textarea
                        v-model:value="form.commentaire"
                        size="large"
                        class="w-full"
                        rows="4"
                        />
                </form-item>

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
