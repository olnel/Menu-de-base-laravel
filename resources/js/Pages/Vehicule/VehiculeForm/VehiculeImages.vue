<script setup>
import {ref, watch} from "vue";
import ButtonIcon from "@/Components/ButtonIcon.vue";
import FormItem from "@/Components/FormItem.vue";
import { PictureOutlined, DeleteOutlined } from "@ant-design/icons-vue";
import UploadMultipleComponent from "@/Components/UploadFile/UploadMultipleComponent.vue";
import {message} from "ant-design-vue";
import {router, useForm} from "@inertiajs/vue3";
import {confirm_delete} from "../../../../Utils/confirmation_modal.js";

const TYPE = [
    {value: 'Photo origine', label: 'Photo origine'},
    {value: 'Constat', label: 'Constat'},
    {value: 'Controle Technique', label: 'Controle Technique'},
    {value: 'Entretien', label: 'Entretien'},
    {value: 'Sinistre', label: 'Sinistre'},
];

const props = defineProps({
    vehicule_id: {
        type: String,
        required: true,
    },
    vehiculePhotos: {
        type: Array,
        required: false,
        default: () => []
    },
    errors: Object
});

const localPhotoVehicule = ref([...props.vehiculePhotos]);

const dateFormat = 'YYYY/MM/DD';

const newPhoto = () => {
    localPhotoVehicule.value.push({
        date_prise_photo: null,
        type_element: null,
        etat_vehicule: null,
        images: []
    });
};




const mapExistingImages = (listeImage) => {

    return listeImage?.map(img => ({
        url: img.main,
        isExisting: true
    })) || [];
};

const savePhoto = (photo) => {
    photo.loading = true;
    // Créer l'objet FormData
    const formData = new FormData();
    formData.append('vehicule_id', props.vehicule_id);
    formData.append('date_prise_photo', photo.date_prise_photo || '');
    formData.append('type_element', photo.type_element || '');
    formData.append('etat_vehicule', photo.etat_vehicule || '');

    // Ajouter les images
    if (photo.liste_image && photo.liste_image.length > 0) {
        photo.liste_image.forEach((item, index) => {
            if (item instanceof File) {
                formData.append(`liste_image[${index}]`, item);
            } else if (item.main) {
                formData.append(`liste_image[]`, item.main);
            }
        });
    }

    const url = photo.id
        ? route('vehiculephoto.update', photo.id)
        : route('vehiculephoto.store');

    if (photo.id) {
        formData.append('_method', 'PUT');
    }


    router.post(url, formData, {
        onSuccess: () => close(),
        forceFormData: true
    });
};


const removePhoto = async (index) => {
    const photo = localPhotoVehicule.value[index];

    if (photo.id) {
        confirm_delete(() => {
            router.delete(route('vehiculephoto.destroy', photo.id), {
                preserveScroll: true,
                onSuccess: () => {
                    localPhotoVehicule.value.splice(index, 1);
                },
                onError: () => {
                }
            });
        });
    } else {
        localPhotoVehicule.value.splice(index, 1);
    }
};



const handleFilesUpdate = (photo, { existing = [], newFiles = [] } = {}) => {
    photo.liste_image = [
        ...existing.filter(img => img.isExisting).map(img => ({ src: img.url })),
        ...newFiles.map(file => file)
    ];
};


watch(() => props.vehiculePhotos, (newVal) => {
    localPhotoVehicule.value = [...newVal];
}, {deep: true});
</script>

<template>
    <div class="space-y-6">
        <ButtonIcon
            type="primary"
            text="Ajouter Nouveau Photos"
            icon="fa-camera"
            @click="newPhoto"
        />

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            <div v-for="(photo, index) in localPhotoVehicule" :key="index" class="border rounded-none border-primary/15 rounded-lg p-4 shadow-sm">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                    <FormItem required label="Date Prise Photo">

                        <a-date-picker
                            v-model:value="photo.date_prise_photo"
                            :format="dateFormat"
                            size="large"
                            class="w-full"
                            :value-format="'DD-MM-YYYY'"
                        />
                    </FormItem>

                    <FormItem required label="Type">
                        <a-select
                            v-model:value="photo.type_element"
                            placeholder="Type de photo"
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
                    </FormItem>
                </div>

                <FormItem required label="Etat Véhicule" class="mb-4">
                    <a-textarea
                        v-model:value="photo.etat_vehicule"
                        placeholder="Description de l'état"
                        :auto-size="{ minRows: 3, maxRows: 5 }"
                    />
                </FormItem>

                <div class="border-t pt-4">
                    <div class="flex items-center justify-between mb-2">
                        <h4 class="font-medium flex items-center">
                            <PictureOutlined class="mr-2"/>
                            <span class="text-red-500">* </span> Galerie d'Images
                        </h4>
                    </div>

                    <UploadMultipleComponent
                        :initial-files="mapExistingImages(photo.liste_image)"
                        @updateFiles="(files) => handleFilesUpdate(photo, files)"
                    >
                        <template #tips>
                            <p class="text-xs text-gray-500 mt-2">
                                Formats acceptés: JPG, PNG (max 5MB)
                            </p>
                        </template>
                    </UploadMultipleComponent>
                </div>

                <div class="flex justify-end gap-2 mt-4 pt-3 border-t">
                    <a-button

                        danger
                        class="w-full"
                        @click="removePhoto(index)"
                    >
                        <template #icon><DeleteOutlined /></template>
                        Supprimer
                    </a-button>

                    <a-button
                        type="primary"
                        class="w-full"
                        @click="savePhoto(photo)"

                    >
                        <template #icon><SaveOutlined /></template>
                        Enregistrer
                    </a-button>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.ant-upload-hint {
    font-size: 12px;
    color: #888;
}
</style>
