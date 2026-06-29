<script setup>
import AutocompleteComponent from "@/Components/AutoCompleteComponent/AutocompleteComponent.vue";
import FormItem from "@/Components/FormItem.vue";
import InputNumberWithSepartor from "@/Components/InputNumberWithSepartor.vue";
import { DeleteOutlined, PlusOutlined } from "@ant-design/icons-vue";
import { useForm } from "@inertiajs/vue3";
import { ref, watch } from "vue";

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
    LIST_REMORQUE: {
        type: Array,
        default: () => []
    },
});


// Réactifs locaux pour les données
const form = useForm(props.information);
const localElements = ref([...props.elementVehicules]);
const localCouleurVehicule = ref([...props.LIST_COULEUR_VEHICULE]);
const localModeleVehicule = ref([...props.LIST_MODELE_VEHICULE]);
const localMarqueVehicule = ref([...props.LIST_MARQUE_VEHICULE]);
const localRemorque = ref([...props.LIST_REMORQUE]);

const submit = () => {
    form.clearErrors();
    const method = form.id ? 'put' : 'post';
    const url = form.id ? route('vehicule.update', form.id) : route('vehicule.store');

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
    localModeleVehicule.value = [...newVal];
}, {deep: true});

watch(() => props.LIST_MARQUE_VEHICULE, (newVal) => {
    localMarqueVehicule.value = [...newVal];
}, {deep: true});
watch(() => props.LIST_REMORQUE, (newVal) => {
    localRemorque.value = [...newVal];
}, {deep: true});


</script>

<template>
    <div :class="showElementVehicule ? 'lg:grid-cols-2' : '' " class="grid  grid-cols-1  gap-8 p-4">
        <!-- Section Informations -->
        <a-form layout="vertical" >
            <div class=" space-y-6">

                <form-item label="Immatriculation" required :help="form.errors.immatriculation">
                    <a-input v-model:value="form.immatriculation" size="large" />
                </form-item>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <form-item label="Marque" required :help="form.errors.marque">

                        <AutocompleteComponent  v-model="form.marque"
                                                :options="localMarqueVehicule"
                                                placeholder="Choisissez une marque"
                        />
                    </form-item>
                    <form-item label="Modèle" :help="form.errors.modele">

                        <AutocompleteComponent  v-model="form.modele"
                                                :options="localModeleVehicule"
                                                placeholder="Choisissez un modèle"
                        />
                    </form-item>

                    <form-item label="N° Châssis" required :help="form.errors.num_chassis">
                        <a-input v-model:value="form.num_chassis" size="large" />
                    </form-item>

                    <form-item label="Couleur" :help="form.errors.couleur">

                        <AutocompleteComponent  v-model="form.couleur"
                                                :options="localCouleurVehicule"
                                                placeholder="Choisissez une couleur"
                        />
                    </form-item>
                    <form-item label="N° Carte Grise" :help="form.errors.num_carte_grise">
                        <a-input v-model:value="form.num_carte_grise" size="large" />
                    </form-item>


                    <form-item label="Nombre de portes" :help="form.errors.nbre_porte">
                        <a-input-number v-model:value="form.nbre_porte" :min="2" :max="5" size="large" class="w-full" />
                    </form-item>
                    <form-item label="Valeur Initiale" :help="form.errors.valeur_initial">

                        <InputNumberWithSepartor
                            v-model="form.valeur_initial"
                            size="large"
                            class="w-full text-right"
                        />
                    </form-item>


                    <form-item label="Remorque" :help="form.errors.remorque_id">
                        <a-select
                            ref="select"
                            v-model:value="form.remorque_id"
                            :options="localRemorque"
                            :allowClear="true"
                            size="large"
                            placeholder="Choisissez un remorque"
                        />
                    </form-item>
                </div>


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
