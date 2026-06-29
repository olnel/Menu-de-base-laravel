<script setup>
import { useForm } from "@inertiajs/vue3";
import {ref, watch} from "vue";
import {DeleteOutlined, PlusOutlined} from "@ant-design/icons-vue";
import FormItem from "@/Components/FormItem.vue";
import UserForm from "@/Pages/User/Partials/UserForm.vue";
import InputNumberWithSepartor from "@/Components/InputNumberWithSepartor.vue";
import AutocompleteComponent from "@/Components/AutoCompleteComponent/AutocompleteComponent.vue";

const props = defineProps({
    information: {
        type: Object,
        required: true,
    },
    elementVehicules: {
        type: Array,
        requried: false,
        default: []
    },
    showElementVehicule: {
        required: false,
        default: true,
        type: Boolean
    },
    LIST_COULEUR_VEHICULE: {
        type: Array,
        default: () => []
    },
    LIST_MODELE_VEHICULE: {
        type: Array,
        default: () => []
    },
    LIST_MARQUE_VEHICULE: {
        type: Array,
        default: () => []
    },
});


// Réactifs locaux pour les données
const form = useForm(props.information);
const localElements = ref([...props.elementVehicules]);
const localCouleurVehicule = ref([...props.LIST_COULEUR_VEHICULE]);
const localmodele_remorqueVehicule = ref([...props.LIST_MODELE_VEHICULE]);
const localmarque_remorqueVehicule = ref([...props.LIST_MARQUE_VEHICULE]);

/*const form = useForm({
    ...props.information,
    element_vehicules: props.information.element_vehicules || []
});*/


const submit = () => {
    form.clearErrors();
    const method = form.id ? 'put' : 'post';
    const url = form.id ? route('remorque.update', form.id) : route('remorque.store');

    form.transform(data => ({
        ...data,
        _method: method.toUpperCase()
    })).post(url, {
        onSuccess: () =>{ close()},
        forceFormData: true
    });
};

const pushElement = ()=> {
    form.element_vehicules.push({
        id: '',
        emplacement: '',
        libelle: '',
        reference: '',
        numero_serie: '',
        etat_piece: '',
    })
}

const spliceElement = (index)=> {
    form.element_vehicules.splice(index, 1)
}

watch(() => props.information, (newVal) => {
    Object.assign(form, newVal || {});
}, {deep: true});

watch(() => props.LIST_ELEMENT_VEHICULE, (newVal) => {
    localElements.value = [...newVal];
}, {deep: true});

watch(() => props.LIST_COULEUR_VEHICULE, (newVal) => {
    localCouleurVehicule.value = [...newVal];
}, {deep: true});

watch(() => props.LIST_MODELE_VEHICULE, (newVal) => {
    localmodele_remorqueVehicule.value = [...newVal];
}, {deep: true});

watch(() => props.LIST_MARQUE_VEHICULE, (newVal) => {
    localmarque_remorqueVehicule.value = [...newVal];
}, {deep: true});


</script>

<template>
    <div :class="showElementVehicule ? 'lg:grid-cols-2' : '' " class="grid  grid-cols-1  gap-8 p-4">
        <!-- Section Informations -->
        <a-form layout="vertical" >
            <div class=" space-y-6">

                <form-item label="Numéro remorque" required :help="form.errors.numero_remorque">
                    <a-input v-model:value="form.numero_remorque" size="large" />
                </form-item>


                    <form-item label="Marque" required :help="form.errors.marque_remorque">

                        <AutocompleteComponent  v-model="form.marque_remorque"
                                                :options="localmarque_remorqueVehicule"
                                                placeholder="Choisissez une marque"
                        />
                    </form-item>
                    <form-item label="Modèle" :help="form.errors.modele_remorque">

                        <AutocompleteComponent  v-model="form.modele_remorque"
                                                :options="localmodele_remorqueVehicule"
                                                placeholder="Choisissez un modèle"
                        />
                    </form-item>



            </div>

            <!-- Section Images -->
            <div class="space-y-6" v-show="showElementVehicule">
                <form-item label="Elément Véhicule" :help="form.errors.param_element_id">
                    <a-select
                        ref="select"
                        v-model:value="form.param_element_id"
                        @change="handleElementVehicule"
                        :allowClear="true"
                        size="large"
                    >
                        <a-select-option v-for="(e, index) in props.elementVehicules" :key="index" :value="e.id">
                            {{e.type_model}}
                        </a-select-option>

                    </a-select>
                </form-item>

                <table class="min-w-full divide-y  divide-green-200">
                    <thead class="bg-primary/50">
                    <tr>
                        <th class="py-1.5">Emplacement</th>
                        <th>Libelldé</th>
                        <th>Référence</th>
                        <th>N° Séries</th>
                        <th>Etat</th>
                        <th>
                            <a-button type="primary" size="small" @click="pushElement" icon>
                                <PlusOutlined/>
                            </a-button>
                        </th>
                    </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-green-900 border ">
                    <tr v-for="(item, index) in form.element_vehicules" :key="index">
                        <td class="border border-primary/25">
                            <a-input v-model:value="item.emplacement" class="rounded-none border-0 focus:border-b-green-600" size="large"/>
                        </td>
                        <td class="border border-primary/25">
                            <a-input v-model:value="item.libelle" size="large" class="rounded-none border-0"/>
                        </td>
                        <td class="border border-primary/25">
                            <a-input v-model:value="item.reference" size="large" class="rounded-none border-0"/>
                        </td>
                        <td class="border border-primary/25">
                            <a-input v-model:value="item.numero_serie" size="large" class="rounded-none border-0"/>
                        </td>
                        <td class="border border-primary/25">
                            <a-input v-model:value="item.etat_piece" size="large" class="rounded-none border-0"/>
                        </td>
                        <td class="border border-primary/25">
                            <a-button
                                danger
                                type="text"
                                size="small"
                                @click="spliceElement(index)"
                                v-if="form.element_vehicules.length > 1"
                            >
                                <DeleteOutlined/>
                            </a-button>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </a-form>


        <div class="col-span-2 flex justify-end">
            <a-button type="primary" @click="submit">
                Enregistrer les informations
            </a-button>
        </div>
    </div>
</template>
