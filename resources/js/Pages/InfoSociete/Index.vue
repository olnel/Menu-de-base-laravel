<script setup xmlns:hover="http://www.w3.org/1999/xhtml">
import SousMenuPrincipale from "@/Pages/Menu/SousMenuPrincipale.vue";
import { ref, computed, onMounted } from "vue";
import { useForm, router } from "@inertiajs/vue3";
import usePermissions from "@/UserPermissions/usePermissions.js";
import FormItem from "@/Components/FormItem.vue";
import BaseUploadImage from "@/Components/UploadImage.vue";
import { message } from "ant-design-vue";

const { can } = usePermissions();

const props = defineProps({
    data: {
        type: Object,
        default: () => ({}),
    },
    flash: {
        type: Object,
        default: () => ({}),
    },
    currencies: {
        type: Object,
        default: () => ({}),
    },
});

const title = ref("Informations de la Société");
const imgPreview = ref(null);

const form = useForm({
    id: props.data.id || null,
    nom_societe: props.data.nom_societe || "",
    adresse_societe: props.data.adresse_societe || "",
    telephone_societe: props.data.telephone_societe || "",
    email_societe: props.data.email_societe || "",
    stat_societe: props.data.stat_societe || "",
    nif_societe: props.data.nif_societe || "",
    rcs_societe: props.data.rcs_societe || "",
    devise: props.data.devise || "MGA",
    mail_password: props.data.mail_password || "",
    logo_societe: null,
    current_logo_url: props.data.logo_societe || null,
});

onMounted(() => {
    if (form.current_logo_url) {
        imgPreview.value = form.current_logo_url;
    }

    if (props.flash.success) {
        message.success(props.flash.success);
    }
    if (props.flash.error) {
        message.error(props.flash.error);
    }
});

const handlePhotoUpload = (file) => {
    form.logo_societe = file;
    if (file) {
        imgPreview.value = URL.createObjectURL(file);
    } else {
        imgPreview.value = null;
    }
    form.clearErrors('logo_societe');
};

const removePhoto = () => {

    form.logo_societe = null;
    form.current_logo_url = null;
    imgPreview.value = null;

    form.transform((data) => ({ ...data, delete_logo: true }));
    message.info("L'image sera supprimée après l'enregistrement.");
};

const submitForm = () => {
    form.clearErrors();

    const method = form.id ? 'put' : 'post';
    const url = form.id ? route('infosociete.update', form.id) : route('infosociete.store');

    form.transform(data => ({
        ...data,
        _method: method.toUpperCase()
    })).post(url, {
        onSuccess: () => close(),
        forceFormData: true
    });
};
</script>

<template>
    <SousMenuPrincipale :title="title" selectedMenu="infosociete">
        <div class="py-6 px-4 sm:px-6 lg:px-8">
            <a-form layout="vertical" @submit.prevent="submitForm">
                <div class="bg-white rounded-xl shadow-lg p-8 border border-gray-100 transition-all duration-300">
                    <a-row :gutter="[32, 32]" align="top">
                        <!-- Détails société -->
                        <a-col :xs="24" :lg="16">
                            <a-row :gutter="[16, 24]">
                                <a-col :xs="24" :md="12">
                                    <form-item label="Nom de la Société" :required="true" :help="form.errors.nom_societe">
                                        <a-input
                                            v-model:value="form.nom_societe"
                                            size="large"
                                            placeholder="Ex: Ma Super Société S.A."
                                            :class="{ 'border-red-500': form.errors.nom_societe }"
                                        >
                                            <template #prefix>
                                                <font-awesome-icon icon="fa-solid fa-building" class="text-gray-400" />
                                            </template>
                                        </a-input>
                                    </form-item>

                                    <form-item label="STAT" :help="form.errors.stat_societe">
                                        <a-input
                                            v-model:value="form.stat_societe"
                                            size="large"
                                            placeholder="Numéro STAT"
                                            :class="{ 'border-red-500': form.errors.stat_societe }"
                                        >
                                            <template #prefix>
                                                <font-awesome-icon icon="fa-solid fa-file-invoice" class="text-gray-400" />
                                            </template>
                                        </a-input>
                                    </form-item>

                                    <form-item label="NIF" :help="form.errors.nif_societe">
                                        <a-input
                                            v-model:value="form.nif_societe"
                                            size="large"
                                            placeholder="Numéro NIF"
                                            :class="{ 'border-red-500': form.errors.nif_societe }"
                                        >
                                            <template #prefix>
                                                <font-awesome-icon icon="fa-solid fa-fingerprint" class="text-gray-400" />
                                            </template>
                                        </a-input>
                                    </form-item>

                                    <form-item label="RCS" :help="form.errors.rcs_societe">
                                        <a-input
                                            v-model:value="form.rcs_societe"
                                            size="large"
                                            placeholder="Numéro RCS"
                                            :class="{ 'border-red-500': form.errors.rcs_societe }"
                                        >
                                            <template #prefix>
                                                <font-awesome-icon icon="fa-solid fa-registered" class="text-gray-400" />
                                            </template>
                                        </a-input>
                                    </form-item>
                                </a-col>

                                <a-col :xs="24" :md="12">
                                    <form-item label="Adresse" :required="true" :help="form.errors.adresse_societe">
                                        <a-input
                                            v-model:value="form.adresse_societe"
                                            size="large"
                                            placeholder="Ex: 123 Rue de l'Innovation, Ville"
                                            :class="{ 'border-red-500': form.errors.adresse_societe }"
                                        >
                                            <template #prefix>
                                                <font-awesome-icon icon="fa-solid fa-map-location-dot" class="text-gray-400" />
                                            </template>
                                        </a-input>
                                    </form-item>

                                    <form-item label="Téléphone" :required="true" :help="form.errors.telephone_societe">
                                        <a-input
                                            v-model:value="form.telephone_societe"
                                            size="large"
                                            placeholder="Ex: +261 32 123 45 67"
                                            :class="{ 'border-red-500': form.errors.telephone_societe }"
                                        >
                                            <template #prefix>
                                                <font-awesome-icon icon="fa-solid fa-phone-volume" class="text-gray-400" />
                                            </template>
                                        </a-input>
                                    </form-item>

                                    <form-item label="Email" :help="form.errors.email_societe">
                                        <a-input
                                            v-model:value="form.email_societe"
                                            size="large"
                                            type="email"
                                            placeholder="Ex: contact@masociete.com"
                                            :class="{ 'border-red-500': form.errors.email_societe }"
                                        >
                                            <template #prefix>
                                                <font-awesome-icon icon="fa-solid fa-at" class="text-gray-400" />
                                            </template>
                                        </a-input>
                                    </form-item>

                                    <form-item label="Mot de passe d'application" :help="form.errors.mail_password">
                                        <a-input
                                            v-model:value="form.mail_password"
                                            size="large"
                                            placeholder=""
                                            :class="{ 'border-red-500': form.errors.mail_password }"
                                        >
                                            <template #prefix>
                                                <font-awesome-icon icon="fa-solid fa-at" class="text-gray-400" />
                                            </template>
                                        </a-input>
                                    </form-item>

                                    
                                </a-col>
                                
                            </a-row>
                            
                            <a-row>
                               <a-col :span="24">
                                   <form-item label="Devise" :help="form.errors.devise" required>
                                       <a-select
                                           v-model:value="form.devise"
                                           size="large"
                                           placeholder="Sélectionner la devise"
                                           show-search
                                       >
                                           <a-select-option
                                               v-for="(currency, code) in currencies"
                                               :key="code"
                                               :value="code"
                                           >
                                               {{ code }} ({{ currency.symbol }}) - {{currency.name}}
                                           </a-select-option>
                                       </a-select>
                                   </form-item>
                               </a-col>
                             </a-row>
                           
                        </a-col>

                        <!-- Logo -->
                        <a-col :xs="24" :lg="8">
                            <div class="flex flex-col items-center">
                                <div
                                    class="w-72 h-72 sm:w-80 sm:h-80 flex items-center justify-center transition-all duration-300
                                            cursor-pointer !rounded-none overflow-hidden relative group bg-gray-50 shadow-inner"
                                >
                                    <BaseUploadImage
                                        :url="imgPreview"
                                        @onChange="handlePhotoUpload"
                                        accept="image/*"
                                        class="absolute inset-0 w-full h-full !object-contain"
                                        :class="{ 'border-red-500': form.errors.logo_societe }"
                                    >
                                        <template #default="{ hasImage }">
                                            <div v-if="!hasImage" class="flex flex-col items-center justify-center h-full text-gray-500">
                                                <font-awesome-icon icon="fa-solid fa-cloud-arrow-up" class="text-4xl mb-2 text-indigo-400" />
                                                <p class="text-sm font-medium">Uploader un logo</p>
                                                <p class="text-xs">JPG, PNG, GIF</p>
                                            </div>
                                            <div v-else class="absolute inset-0 flex items-center justify-center bg-black bg-opacity-40 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                                <span class="text-white text-sm font-semibold">Changer le logo</span>
                                            </div>
                                        </template>
                                    </BaseUploadImage>
                                </div>

                                <a-button
                                    v-if="form.current_logo_url || imgPreview"
                                    @click="removePhoto"
                                    danger
                                    type="text"
                                    size="middle"
                                    class="mt-4 text-red-600 hover:bg-red-50 rounded-lg px-4 py-2 font-medium transition-all duration-200"
                                >
                                    <font-awesome-icon icon="fa-solid fa-trash-can" class="mr-2" />
                                    Supprimer
                                </a-button>

                                <div class="mt-4 text-xs text-gray-500">
                                    Taille recommandée : 500x500px - Max 2MB
                                </div>
                            </div>
                        </a-col>

                    </a-row>

                    <!-- Bouton en bas -->
                    <div class="mt-10 flex justify-end">
                        <a-button
                            size="large"
                            html-type="submit"
                            :loading="form.processing"
                            class="px-10 py-3 rounded-lg !bg-primary shadow-lg hover:shadow-xl !text-white transition-all duration-300 font-semibold text-lg"
                        >
                            <font-awesome-icon icon="fa-solid fa-floppy-disk" class="mr-2" />
                            Enregistrer
                        </a-button>
                    </div>
                </div>
            </a-form>
        </div>
    </SousMenuPrincipale>
</template>


<style scoped>
/* Base styling for inputs - can be moved to a global CSS or utility file if preferred */
.ant-input-affix-wrapper,
.ant-input {
    border-radius: 10px !important; /* Slightly more rounded */
    padding: 12px 14px !important; /* Increased padding */
    font-size: 1rem !important;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05); /* Subtle shadow */
}

.ant-input-affix-wrapper-focused,
.ant-input-focused,
.ant-input:focus,
.ant-input:hover {
    border-color: #6366f1 !important; /* Tailwind indigo-500 */
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2) !important; /* Indigo shadow */
}

/* Specific style for inputs with errors */
.ant-input-affix-wrapper.border-red-500,
.ant-input.border-red-500 {
    border-color: #ef4444 !important; /* Tailwind red-500 */
}

.ant-form-item-has-error .ant-form-item-explain {
    color: #ef4444 !important;
    font-size: 0.85rem; /* Slightly larger error text */
}

/* Custom button styles for a more modern look */
.ant-btn-text.danger {
    color: #ef4444; /* Tailwind red-500 */
}

.ant-btn-text.danger:hover {
    background-color: #fef2f2; /* Tailwind red-50 */
    color: #dc2626; /* Tailwind red-600 */
}

.ant-btn-primary {
    background-color: #6366f1 !important; /* Tailwind indigo-500 */
    border-color: #6366f1 !important;
    transition: all 0.3s ease;
}

.ant-btn-primary:hover,
.ant-btn-primary:focus {
    background-color: #4f46e5 !important; /* Tailwind indigo-600 */
    border-color: #4f46e5 !important;
    transform: translateY(-1px); /* Slight lift effect */
}
</style>
