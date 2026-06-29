<script>
const TYPE= [
    {value: 'Photo origine', label: 'Photo origine'},
    {value: 'Constat', label: 'Constat'},
    {value: 'Controle Technique', label: 'Controle Technique'},
    {value: 'Entretien', label: 'Entretien'},
    {value: 'Sinistre', label: 'Sinistre'},
]
</script>

<script setup>
import {ref, watch} from "vue";
import {router, useForm} from "@inertiajs/vue3";
import FormItem from "@/Components/FormItem.vue";
import FormModal from "@/Components/FormModal.vue";
import {DeleteOutlined, PictureOutlined, PlusOutlined} from "@ant-design/icons-vue";
import UploadMultipleComponent from "@/Components/UploadFile/UploadMultipleComponent.vue";
const activeKey = ref('1');
const dateFormat = 'YYYY/MM/DD';

const props = defineProps({
    // flash: {
    //     type: Object,
    //     default: () => ({})
    // },
    information: {
        required: true,
        type: Object,
    },
    /*element: {
        type: Array,
        default: () => []
    },*/
    vehiculeImage: {
        type: Array,
        default: () => []
    },
    LIST_ELEMENT_VEHICULE: {
        type: Array,
        default: () => []
    }
})

const form = useForm(props.information);

const open = ref(false);
const title = ref("");
const value1 = ref("");
// Ajoutez une référence réactive pour les images
const localVehiculeImages = ref([...props.vehiculeImage]);

/*const filePreviews = ref([]);
//Nouvelle variable pour stocker les nouveaux fichiers
const newImages = ref([]);*/

const close = () => {
    open.value = false;
    form.reset();
    form.clearErrors();
    // filePreviews.value = [];
    // newImages.value = []; // Reset des nouvelles images
};

const add = () => {
    title.value = "Ajouter un Véhicule";
    open.value = true;
};

const update = (rowData) => {

    // title.value = rowData.immatriculation;
    // open.value = true;
    router.visit(`${route('vehicule.show', {vehicule: rowData.id})}`, {
        preserveState: true,
        preserveScroll: true,
        only: ['flash'],
        onSuccess: () => {
           /* if (props.flash.data) {
                Object.assign(form, props.flash.data.vehicule || {});
            }*/
            // title.value = "Modifier";
            // open.value = true;
        },
    });
};

/*const handleFilesUpdate = ({ existing, newFiles }) => {
    console.log('Existing images:', existing);
    console.log('New files:', newFiles);

    // Gestion correcte des images existantes
    form.existing_images = existing.map(img => img.id || img.url);

    // Stocker les nouveaux fichiers séparément
    newImages.value = newFiles;

    // Mettre à jour les prévisualisations
    filePreviews.value = [
        ...existing,
        ...newFiles.map(file => ({
            preview: URL.createObjectURL(file),
            isExisting: false
        }))
    ];
};*/

const handleElementVehicule = ()=> {
    /*const selectedElement = props.flash.element_vehicule.find(
        element => element.id === form.param_element_id
    );
    form.element_vehicules = selectedElement.details.map(detail => ({
        ...detail,
        numero_serie: '',
        etat_piece: ''
    }));*/
}

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
    form.element_vehicules.splice(index , 1)
}

/*const submit = () => {
    const formData = new FormData();

    // Ajouter les champs standards
    Object.entries(form.data()).forEach(([key, value]) => {
        if (key !== 'existing_images' && value !== null && value !== undefined) {
            formData.append(key, value);
        }
    });

    // Ajouter les images existantes
    if (form.existing_images.length > 0) {
        form.existing_images.forEach((imageId, index) => {
            formData.append(`existing_images[${index}]`, imageId);
        });
    }

    // Ajouter les nouvelles images
    newImages.value.forEach((file, index) => {
        formData.append(`images[${index}]`, file);
    });

    const options = {
        onSuccess: () => close(),
        onError: (errors) => {
            console.error('Erreurs de validation:', errors);
        }
    };

    if (form.id) {
        formData.append('_method', 'PUT');
        router.post(route('vehicule.update', form.id), formData, options);
    } else {
        router.post(route("vehicule.store"), formData, options);
    }
};*/

const submit = () => {
    form.clearErrors();
    const method = form.id ? 'put' : 'post';
    const url = form.id ? route('vehicule.update', form.id) : route('vehicule.store');

    form.transform(data => ({
        ...data,
        _method: method.toUpperCase()
    })).post(url, {
        onSuccess: () => close(),
        forceFormData: true
    });
};

watch(() => props.information, (newVal) => {
    if (newVal && Object.keys(newVal).length > 0) {
        form.reset();
        Object.assign(form, newVal);
        title.value = newVal.immatriculation || "Détails Véhicule";
        open.value = true;
    }
}, { deep: true });



// Watcher pour vehiculeImage
watch(() => props.vehiculeImage, (newImages) => {
    localVehiculeImages.value = [...newImages];
}, { deep: true });

// Méthode pour mettre à jour une date
const updateImageDate = (index, date) => {
    localVehiculeImages.value[index].date_prise_photo = date;
    // Si vous devez sauvegarder cette modification, ajoutez ici la logique API
};

defineExpose({ add, update, close });
</script>

<template>

    <FormModal
        v-model:open="open"
        :titre="title"
        :loading="form.processing"
        @close="close"
        @submit="submit"
        size="full_screen"
        :show_champ_obligatoir="false"
    >

        <a-tabs v-model:activeKey="activeKey">

            <a-tab-pane key="1" tab="Informations sur le véhicule">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 p-4">
                    <!-- Section Informations -->
                    <div class="space-y-6">
                        <div class="space-y-4">
                            <form-item label="Immatriculation" required :help="form.errors.immatriculation">
                                <a-input v-model:value="form.immatriculation" size="large" />
                            </form-item>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <form-item label="Marque" required :help="form.errors.marque">
                                    <a-input v-model:value="form.marque" size="large" />
                                </form-item>
                                <form-item label="Modèle" :help="form.errors.modele">
                                    <a-input v-model:value="form.modele" size="large" />
                                </form-item>
                            </div>

                            <form-item label="N° Châssis" required :help="form.errors.num_chassis">
                                <a-input v-model:value="form.num_chassis" size="large" />
                            </form-item>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <form-item label="Couleur" :help="form.errors.couleur">
                                    <a-input v-model:value="form.couleur" size="large" />
                                </form-item>
                                <form-item label="N° Carte Grise" :help="form.errors.num_carte_grise">
                                    <a-input v-model:value="form.num_carte_grise" size="large" />
                                </form-item>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <form-item label="Nombre de portes" :help="form.errors.nbre_porte">
                                    <a-input-number v-model:value="form.nbre_porte" :min="2" :max="5" size="large" class="w-full" />
                                </form-item>
                                <form-item label="Valeur Initiale" :help="form.errors.valeur_initial">
                                    <a-input-number v-model:value="form.valeur_initial" :min="0" size="large" class="w-full" />
                                </form-item>
                            </div>
                        </div>
                    </div>

                    <!-- Section Images -->
                    <div class="space-y-6">

                        <form-item label="Elément Véhicule" :help="form.errors.param_element_id">
                            <a-select
                                ref="select"
                                v-model:value="form.param_element_id"
                                @change="handleElementVehicule"
                                :allowClear="true"
                                size="large"
                            >
                                <a-select-option v-for="(e, index) in props.LIST_ELEMENT_VEHICULE" :key="index" :value="e.id">
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
                </div>
            </a-tab-pane>
            <a-tab-pane key="2" tab="Liste Images" force-render>

                <div v-for="(image, index) in localVehiculeImages" :key="index">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 p-2">
                        <div class="space-y-4">
                            <form-item label="Date Prise Photo" required >
                                <a-date-picker
                                    v-model:value="image.date_prise_photo"
                                    :format="dateFormat"
                                    size="large"
                                    class="w-full"
                                    :value-format="'DD-MM-YYYY'"
                                />
                            </form-item>
                        </div>
                        <div class="space-y-6">
                            <form-item label="Type" >
                                <a-select
                                    v-model:value="image.type_element"
                                    placeholder="Sélectionnez le type de photo"
                                    class="w-full"
                                    size="large"
                                >
                                    <a-select-option
                                        v-for="type in TYPE"
                                        :key="type.value"
                                        :value="type.value"
                                    >
                                        {{ type.label }}
                                    </a-select-option>
                                </a-select>
                            </form-item>
                        </div>
                    </div>
                    <form-item label="Etat Véhicule" >
                        <a-textarea
                            v-model:value="image.etat_vehicule"
                            placeholder=""
                            :auto-size="{ minRows: 4, maxRows: 6 }"
                        />
                    </form-item>
                    <div class="space-y-6">
                        <div class="border-b border-gray-200 pb-4">
                            <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                                <PictureOutlined class="mr-2"/>
                                Galerie d'Images
                            </h3>
                        </div>

<!--                        <UploadMultipleComponent
                            :initial-files="filePreviews"
                            :reset="!open"
                            @updateFiles="handleFilesUpdate"
                        >
                            <template #tips>
                                <p class="font-medium text-gray-600">Conseils photos :</p>
                                <ul class="list-disc pl-5 space-y-1">
                                    <li>Montrez tous les angles du véhicule</li>
                                    <li>Évitez les reflets et ombres gênantes</li>
                                    <li>Prenez des photos en haute résolution</li>
                                </ul>
                            </template>
                        </UploadMultipleComponent>-->
                    </div>

                </div>


            </a-tab-pane>
            <a-tab-pane key="3" tab="Tab 3">

            </a-tab-pane>
        </a-tabs>


    </FormModal>
</template>

<style scoped>
.ant-upload-hint {
    font-size: 12px;
    color: #888;
}
</style>
