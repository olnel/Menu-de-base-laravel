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
import {ref} from "vue";
import {router, useForm} from "@inertiajs/vue3";
import FormItem from "@/Components/FormItem.vue";
import FormModal from "@/Components/FormModal.vue";
import dayjs from 'dayjs';
import {DeleteOutlined, PictureOutlined, PlusOutlined} from "@ant-design/icons-vue";
import UploadMultipleComponent from "@/Components/UploadFile/UploadMultipleComponent.vue";

const dateFormat = 'YYYY/MM/DD';
const props = defineProps({
    flash: {
        type: Object,
        default: () => ({})
    },
})

const form = useForm({
    id: null,
    type_element: null,
    vehicule_id: null,
    date_prise_photo: null,
    liste_image: [],
    etat_vehicule: null,
});

const open = ref(false);
const title = ref("");

const filePreviews = ref([]);
const newImages = ref([]);

const close = () => {
    open.value = false;
    form.reset();
    form.clearErrors();
    filePreviews.value = [];
    newImages.value = [];
};

const add = (vehicule_id) => {
    title.value = "Image Véhicule";
    open.value = true;
    form.vehicule_id = vehicule_id;
};

const update = (rowData) => {
    router.visit(route('vehiculephoto.show', {vehiculephoto: rowData.id}), {
        preserveState: true,
        preserveScroll: true,
        only: ['flash'],
        onSuccess: (response) => {
            if (response.props.flash.data) {
                const data = response.props.flash.data;

                // Convertir les images existantes en format compatible avec UploadMultipleComponent
                const existingImages = Array.isArray(data.liste_image)
                    ? data.liste_image.map(img => ({
                        uid: `existing-${Math.random().toString(36).substr(2, 9)}`,
                        name: img.main.split('/').pop(),
                        status: 'done',
                        url: img.main,
                        isExisting: true
                    }))
                    : [];

                Object.assign(form, data);
                filePreviews.value = existingImages;
                newImages.value = [];

                title.value = "Modifier";
                open.value = true;
            }
        },
    });
}


const openModal = (data) => {
    title.value = "Modifier le Véhicule";
    open.value = true;

    form.type_element = null;
    form.liste_image = [];
    form.date_prise_photo = dayjs().format('DD-MM-YYYY');
    form.etat_vehicule = null;
    form.vehicule_id = data.id;
};

const handleFilesUpdate = ({existing, newFiles}) => {
    // Garder trace des images existantes
    const keptExisting = filePreviews.value
        .filter(file => file.isExisting)
        .filter(file => existing.some(e => e.url === file.url));

    // Préparer les nouveaux fichiers pour prévisualisation
    const newPreviews = newFiles.map(file => ({
        uid: file.uid,
        name: file.name,
        status: 'uploading',
        originFileObj: file,
        preview: URL.createObjectURL(file),
        isExisting: false
    }));

    // Mettre à jour les prévisualisations
    filePreviews.value = [...keptExisting, ...newPreviews];

    // Mettre à jour la liste des images dans le form
    form.liste_image = [
        ...keptExisting.map(file => ({main: file.url})),
        ...newFiles
    ];
};
const submit = () => {
    form.clearErrors();

    // Créer un FormData pour gérer correctement les fichiers
    const formData = new FormData();

    // Ajouter les champs standards
    Object.entries(form.data()).forEach(([key, value]) => {
        if (key !== 'liste_image' && value !== null && value !== undefined) {
            formData.append(key, value);
        }
    });

    // Ajouter les images
    form.liste_image.forEach((item, index) => {
        if (item instanceof File) {
            // Si c'est un nouveau fichier
            formData.append(`liste_image[${index}]`, item);
        } else {
            // Si c'est une image existante (ID ou URL)
            formData.append(`liste_image[${index}]`, item);
        }
    });

    const method = form.id ? 'put' : 'post';
    const url = form.id ? route('vehiculephoto.update', form.id) : route('vehiculephoto.store');

    if (form.id) {
        formData.append('_method', 'PUT');
    }

    router.post(url, formData, {
        onSuccess: () => close(),
        forceFormData: true
    });
};

defineExpose({add, openModal, close, update});
</script>

<template>
    <FormModal
        v-model:open="open"
        :titre="title"
        :loading="form.processing"
        @close="close"
        @submit="submit"
        size="md"
        :show_champ_obligatoir="false"
    >

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 p-2">
            <!-- Section Informations -->
            <div class="space-y-4">
                <form-item label="Date Prise Photo" required :help="form.errors.date_prise_photo">
                    <a-date-picker v-model:value="form.date_prise_photo"
                                   :format="dateFormat"
                                   size="large"
                                   class="w-full"
                                   :value-format="'DD-MM-YYYY'"
                    />
                </form-item>
            </div>
            <div class="space-y-6">
                <form-item label="Type" required :help="form.errors.type_element">
                    <a-select
                        v-model:value="form.type_element"
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
        <form-item label="Etat Véhicule" required :help="form.errors.etat_vehicule">
            <a-textarea
                v-model:value="form.etat_vehicule"
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

            <UploadMultipleComponent
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
            </UploadMultipleComponent>
        </div>
    </FormModal>
</template>

<style scoped>
.ant-upload-hint {
    font-size: 12px;
    color: #888;
}
</style>
