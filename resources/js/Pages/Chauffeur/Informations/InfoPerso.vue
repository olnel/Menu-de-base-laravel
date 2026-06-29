<script setup>
import FormItem from "@/Components/FormItem.vue";
import BaseUploadImage from "@/Components/UploadImage.vue";
import { router, useForm } from "@inertiajs/vue3";
import { ref, watch } from "vue"; // Import 'watch'

// formulaire
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

const imgPreview = ref(null);

const props = defineProps({
    vehicules: {
        type: Array,
        default: () => [],
    },
    // NEW: Add a prop to receive the chauffeur object directly
    initialChauffeur: {
        type: Object,
        default: null,
    },
});

// Watch for changes in initialChauffeur prop and update the form
watch(
    () => props.initialChauffeur,
    (newChauffeur) => {
        if (newChauffeur) {
            console.log(
                "InfoPerso: Initializing form with newChauffeur:",
                newChauffeur
            );
            Object.keys(form.data()).forEach((key) => {
                if (
                    key === "vehicules" &&
                    Array.isArray(newChauffeur.vehicules)
                ) {
                    form.vehicules = newChauffeur.vehicules.map((v) =>
                        typeof v === "object" && v !== null ? v.id : v
                    );
                } else {
                    form[key] = newChauffeur[key] ?? null;
                }
            });
            form.id = newChauffeur.id; // Ensure ID is set
            imgPreview.value = newChauffeur.img || null;
        } else {
            console.log("InfoPerso: initialChauffeur is null, resetting form.");
            form.reset(); // Reset form if chauffeur is null
            imgPreview.value = null;
        }
    },
    { immediate: true, deep: true }
);

const handlePhotoUpload = (file) => {
    form.img = file;
};
const submit = () => {
    if (!form.id) {
        console.error("Cannot submit update: Chauffeur ID is missing.");
        return;
    }

    const formData = new FormData();
    console.log("Formulaire InfoPerso envoyé :", form.data());
    Object.entries(form.data()).forEach(([key, value]) => {
        if (
            key !== "existing_images" &&
            value !== null &&
            value !== undefined &&
            key !== "id"
        ) {
            if (Array.isArray(value)) {
                value.forEach((item) => {
                    formData.append(`${key}[]`, item);
                });
            } else if (key === "img") {
                if (value instanceof File) {
                    formData.append(key, value);
                }
            } else {
                formData.append(key, value);
            }
        }
    });

    formData.append("_method", "PUT");

    router.post(route("chauffeur.update", form.id), formData, {
        onSuccess: () => {
            form.clearErrors();
        },
        onError: (errors) => {
            console.error("Validation errors on update:", errors);
            form.errors = errors;
        },
        preserveScroll: true,
        preserveState: true,
        forceFormData: true,
        headers: {
            "Content-Type": "multipart/form-data",
        },
    });
};

defineExpose({ submit, form });
</script>

<template>
    <a-form layout="vertical">
        <div class="p-0 lg:p-4">
            <a-row :gutter="[8, 24]">
                <a-col :xs="24" :lg="8">
                    <div
                        class="bg-primary/[0.01] rounded-md p-2 lg:p-6 border border-gray-100 transition-all duration-300"
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
                                :url="`../../${imgPreview}`"
                                @onChange="handlePhotoUpload"
                                accept="image"
                                class="mx-auto"
                            />
                        </div>

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
                                        placeholder="NOM DE FAMILLE"
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
    </a-form>
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
