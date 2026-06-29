<script setup>
import FormItem from "@/Components/FormItem.vue";
import FormModal from "@/Components/FormModal.vue";

import BaseUploadImage from "@/Components/UploadImage.vue";
import { router, useForm } from "@inertiajs/vue3";
import { ref } from "vue";

//formulaire
const form = useForm({
    id: null,
    matricule: null,
    img: null,
    nom: null,
    prenom: null,
    telephone: null,
    adresse: null,
    date_naissance: null,
    CIN: null,
    vehicules: [],
});

//variable pour ouvrir le formulaire
const open = ref(false);
const title = ref("Ajouter un Chauffeur");
const imgPreview = ref(null);

const props = defineProps({
    vehicules: {
        type: Array,
        default: () => [],
    },
});

// pour la photo du chauffeur
const handlePhotoUpload = (file) => {
    form.img = file;
};

// Supprimer la photo
const removePhoto = () => {
    form.img = null;
    imgPreview.value = null;
};

//Fermeture du formulaire
const close = () => {
    open.value = false;
    imgPreview.value = null;
    form.reset();
    form.clearErrors();
};

//Ajout d'un chauffeur
const add = () => {
    title.value = "Ajouter un Chauffeur";
    form.reset();
    form.clearErrors();
    form.id = null;
    open.value = true;
    imgPreview.value = null;
};

//Soumission du Formulaire
const submit = () => {
    form.clearErrors();

    const formData = new FormData();
    console.log("Formulaire envoyé :", form.data());
    Object.entries(form.data()).forEach(([key, value]) => {
        if (
            key !== "id" &&
            key !== "img" &&
            value !== null &&
            value !== undefined
        ) {
            if (Array.isArray(value)) {
                value.forEach((item) => {
                    formData.append(`${key}[]`, item);
                });
            } else {
                formData.append(key, value);
            }
        }
    });

    if (form.img instanceof File) {
        formData.append("img", form.img);
    }

    router.post(route("chauffeur.store"), formData, {
        onSuccess: () => {
            close();
        },
        onError: (errors) => {
            console.error("Erreurs de validation:", errors);
            form.errors = errors;
        },
        forceFormData: true,
        headers: {
            "Content-Type": "multipart/form-data",
        },
    });
};

defineExpose({ add, close });
</script>

<template>
    <FormModal
        v-model:open="open"
        :titre="title"
        :loading="form.processing"
        @close="close"
        @submit="submit"
        size="lg"
        :show_champ_obligatoir="false"
    >
        <div class="p-0 lg:p-4">
            <a-row :gutter="[8, 24]">
                <a-col :xs="24" :lg="8" class="flex">
                    <div
                        class="bg-primary/[0.01] rounded-md p-2 lg:p-6 w-full border border-gray-100 transition-all duration-300"
                    >
                        <div class="text-center">
                            <!-- Titre de section avec icône -->
                            <div
                                class="flex items-center justify-center gap-2 mb-6"
                            >
                                <div
                                    class="bg-gradient-to-r from-sky-500 to-primary p-2 flex items-center justify-center w-7 h-7 rounded-md"
                                >
                                    <font-awesome-icon
                                        icon="fa-solid fa-camera"
                                        class="text-white text-sm"
                                    />
                                </div>

                                <h3 class="text-lg font-semibold text-gray-800">
                                    Photo du Chauffeur
                                </h3>
                            </div>
                        </div>

                        <!-- Upload image moderne -->
                        <div class="mb-4">
                            <BaseUploadImage
                                :url="imgPreview"
                                @onChange="handlePhotoUpload"
                                accept="image"
                                class="mx-auto"
                            />
                        </div>

                        <!-- Bouton supprimer avec style moderne -->
                        <a-button
                            v-if="form.id && (form.img || imgPreview)"
                            danger
                            type="text"
                            size="small"
                            class="mt-3 hover:bg-red-50 rounded-lg px-4 py-2 font-medium"
                            @click="removePhoto"
                        >
                            <font-awesome-icon
                                icon="fa-solid fa-trash"
                                class="mr-2"
                            />
                            Supprimer l'image
                        </a-button>

                        <!-- Info formats -->
                        <div
                            class="mt-4 p-3 bg-primary/5 rounded-lg border border-blue-200"
                        >
                            <p class="text-xs text-primary font-medium">
                                <font-awesome-icon
                                    icon="fa-solid fa-info-circle"
                                    class="mr-2"
                                />
                                Formats acceptés : JPG, PNG
                            </p>
                        </div>
                    </div>
                </a-col>

                <a-col :xs="24" :lg="16">
                    <div
                        class="bg-primary/[0.01] rounded-md p-2 lg:p-6 border border-gray-100 transition-all duration-300"
                    >
                        <!-- Titre de section -->
                        <div
                            class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-100"
                        >
                            <div
                                class="bg-gradient-to-r from-primary to-sky-400 p-2 rounded-lg"
                            >
                                <font-awesome-icon
                                    icon="fa-solid fa-id-card"
                                    class="text-white text-sm"
                                />
                            </div>
                            <h3 class="text-lg font-semibold text-gray-800">
                                Informations Personnelles
                            </h3>
                        </div>

                        <a-row :gutter="[16, 2]">
                            <a-col :xs="24" :lg="12">
                                <form-item
                                    label="N° Matricule"
                                    required
                                    :help="form.errors.matricule"
                                >
                                    <a-input
                                        v-model:value="form.matricule"
                                        size="large"
                                        class="modern-input"
                                        placeholder=""
                                    >
                                        <template #prefix>
                                            <font-awesome-icon
                                                icon="fa-solid fa-hashtag"
                                                class="text-gray-400"
                                            />
                                        </template>
                                    </a-input>
                                </form-item>
                            </a-col>

                            <!-- Nom -->
                            <a-col :xs="24" :lg="12">
                                <form-item
                                    label="Nom"
                                    required
                                    :help="form.errors.nom"
                                >
                                    <a-input
                                        v-model:value="form.nom"
                                        size="large"
                                        class="modern-input uppercase"
                                        placeholder=""
                                    >
                                        <template #prefix>
                                            <font-awesome-icon
                                                icon="fa-solid fa-user"
                                                class="text-gray-400"
                                            />
                                        </template>
                                    </a-input>
                                </form-item>
                            </a-col>

                            <!-- Prénom -->
                            <a-col :xs="24" :lg="12">
                                <form-item
                                    label="Prénom"
                                    required
                                    :help="form.errors.prenom"
                                >
                                    <a-input
                                        v-model:value="form.prenom"
                                        size="large"
                                        class="modern-input capitalize"
                                        placeholder=""
                                    >
                                        <template #prefix>
                                            <font-awesome-icon
                                                icon="fa-solid fa-user-circle"
                                                class="text-gray-400"
                                            />
                                        </template>
                                    </a-input>
                                </form-item>
                            </a-col>

                            <!-- Date de naissance -->
                            <a-col :xs="24" :lg="12">
                                <form-item
                                    label="Date de naissance"

                                    :help="form.errors.date_naissance"
                                >
                                    <a-date-picker
                                        v-model:value="form.date_naissance"
                                        size="large"
                                        class="w-full modern-input"
                                        format="DD/MM/YYYY"
                                        :value-format="'YYYY-MM-DD'"
                                        placeholder=""
                                    />
                                </form-item>
                            </a-col>

                            <!-- CIN -->
                            <a-col :xs="24" :lg="12">
                                <form-item
                                    label="N° CIN"

                                    :help="form.errors.CIN"
                                >
                                    <a-input
                                        v-model:value="form.CIN"
                                        size="large"
                                        class="modern-input"
                                        placeholder=""
                                    >
                                        <template #prefix>
                                            <font-awesome-icon
                                                icon="fa-solid fa-id-badge"
                                                class="text-gray-400"
                                            />
                                        </template>
                                    </a-input>
                                </form-item>
                            </a-col>

                            <!-- Téléphone -->
                            <a-col :xs="24" :lg="12">
                                <form-item
                                    label="Téléphone"
                                    :help="form.errors.telephone"
                                >
                                    <a-input
                                        v-model:value="form.telephone"
                                        size="large"
                                        class="modern-input"
                                        placeholder=""
                                    >
                                        <template #prefix>
                                            <font-awesome-icon
                                                icon="fa-solid fa-phone"
                                                class="text-gray-400"
                                            />
                                        </template>
                                    </a-input>
                                </form-item>
                            </a-col>

                            <!-- Section Adresse (pleine largeur) -->
                            <a-col :span="24">
                                <form-item
                                    label="Adresse"
                                   
                                    :help="form.errors.adresse"
                                >
                                    <a-input
                                        v-model:value="form.adresse"
                                        size="large"
                                    >
                                        <template #prefix>
                                            <font-awesome-icon
                                                icon="fa-solid fa-map-marker-alt"
                                                class="text-gray-400"
                                            />
                                        </template>
                                    </a-input>
                                </form-item>
                            </a-col>

                            <!--                            <a-col :span="24">-->
                            <!--                                <div class="flex items-center gap-3 mb-4">-->
                            <!--                                    <div class="bg-gradient-to-r from-purple-500 to-pink-600 p-1 flex items-center justify-center w-7 h-7 rounded-md">-->
                            <!--                                        <font-awesome-icon icon="fa-solid fa-map-marker-alt" class="text-white text-sm" />-->
                            <!--                                    </div>-->
                            <!--                                    <h3 class="text-lg font-semibold text-gray-800">Adresse <span class="text-red-400">*</span></h3>-->
                            <!--                                </div>-->

                            <!--                                <form-item label="Adresse" required :help="form.errors.adresse">-->
                            <!--                                    <a-input v-model:value="form.adresse" size="large"/>-->

                            <!--                                </form-item>-->
                            <!--                            </a-col>-->
                        </a-row>
                    </div>
                </a-col>

                <a-col :span="24">
                    <div class="flex items-center gap-3 mb-4">
                        <div
                            class="bg-gradient-to-r from-orange-500 to-red-600 p-1 flex items-center justify-center w-7 h-7 rounded-md"
                        >
                            <font-awesome-icon
                                icon="fa-solid fa-car"
                                class="text-white text-sm"
                            />
                        </div>
                        <h3 class="text-lg font-semibold text-gray-800">
                            Véhicules Associés
                        </h3>
                    </div>

                    <form-item :help="form.errors.vehicules">
                        <a-select
                            v-model:value="form.vehicules"
                            mode="multiple"
                            size="large"
                            placeholder="Sélectionner les véhicules"
                            class="w-full"
                            show-search
                            :filter-option="
                                (input, option) =>
                                    option.label
                                        .toLowerCase()
                                        .includes(input.toLowerCase())
                            "
                            option-filter-prop="label"
                        >
                            <a-select-option
                                v-for="v in props.vehicules"
                                :key="v.id"
                                :value="v.id"
                                :label="`${v.immatriculation} ${v.marque} ${v.modele}`"
                            >
                                <div class="flex flex-col">
                                    <span class="font-semibold">{{
                                        v.immatriculation
                                    }}</span>
                                    <span class="text-xs text-gray-500"
                                        >{{ v.marque }} - {{ v.modele }}</span
                                    >
                                </div>
                            </a-select-option>
                        </a-select>
                    </form-item>
                </a-col>
            </a-row>
        </div>
    </FormModal>
</template>

<style>
.ant-upload-hint {
    font-size: 12px;
    color: #888;
}

.ant-picker-dropdown .ant-picker-today-btn {
    @apply !text-primary;
}
</style>
