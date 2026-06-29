<script setup>
import FormItem from "@/Components/FormItem.vue";
import FormModal from "@/Components/FormModal.vue";
import UploadImage from "@/Components/UploadImage.vue";
import DynamicDocumentManager from "@/Components/DynamicDocumentManager.vue";
import { router, useForm } from "@inertiajs/vue3";
import { ref, computed, watch } from "vue";
import dayjs from "dayjs";

const props = defineProps({
    types_salarie: { type: Array, default: () => [] },
    required_documents: { type: Array, default: () => [] },
    vehicules: { type: Array, default: () => [] },
    chauffeurs_list: { type: Array, default: () => [] },
});

const form = useForm({
    id: null,
    matricule: null,
    nom: null,
    prenom: null,
    sexe: null,
    salaire: null,
    date_naissance: null,
    lieu_naissance: null,
    nationalite: null,
    cin: null,
    date_cin: null,
    lieu_cin: null,
    telephone: null,
    email: null,
    adresse: null,
    photo: null,
    date_embauche: dayjs().format("YYYY-MM-DD"), // Date du jour par défaut
    statut: "actif",
    observation: null,
    type_salarie_id: null,
    vehicule_id: null,
    parent_chauffeur_id: null,
    documents: [],
});

const open = ref(false);
const title = ref("");
const imageUrl = ref("");
const activeTab = ref("info");

// Auto-format fields
watch(
    () => form.nom,
    (val) => {
        if (val) form.nom = val.toUpperCase();
    },
);

const capitalizeFirstLetter = (val) => {
    if (!val) return val;
    return val.charAt(0).toUpperCase() + val.slice(1);
};

watch(
    () => form.prenom,
    (val) => {
        if (val) form.prenom = capitalizeFirstLetter(val);
    },
);

watch(
    () => form.lieu_naissance,
    (val) => {
        if (val) form.lieu_naissance = capitalizeFirstLetter(val);
    },
);

watch(
    () => form.adresse,
    (val) => {
        if (val) form.adresse = capitalizeFirstLetter(val);
    },
);

watch(
    () => form.nationalite,
    (val) => {
        if (val) form.nationalite = capitalizeFirstLetter(val);
    },
);

watch(
    () => form.lieu_cin,
    (val) => {
        if (val) form.lieu_cin = capitalizeFirstLetter(val);
    },
);

watch(
    () => form.cin,
    (val) => {
        if (val) {
            let digits = val.replace(/\D/g, "");
            digits = digits.substring(0, 12);
            form.cin = digits.replace(/(\d{3})(?=\d)/g, "$1 ");
        }
    },
);

// Logique pour déterminer si c'est un chauffeur ou aide-chauffeur
const selectedTypeLabel = computed(() => {
    const type = props.types_salarie.find(
        (t) => t.value === form.type_salarie_id,
    );
    return type ? type.label.toLowerCase() : "";
});

const isChauffeur = computed(() => {
    return (
        selectedTypeLabel.value.includes("chauffeur") &&
        !selectedTypeLabel.value.includes("aide")
    );
});

const isAideChauffeur = computed(() => {
    return (
        selectedTypeLabel.value.includes("aide") &&
        selectedTypeLabel.value.includes("chauffeur")
    );
});

const close = () => {
    open.value = false;
    form.reset();
    form.clearErrors();
    imageUrl.value = "";
    // Réinitialiser la date du jour après le reset
    form.date_embauche = dayjs().format("YYYY-MM-DD");
    activeTab.value = "info";
    form.documents = [];
};

const add = () => {
    title.value = "Nouveau Salarié";
    form.documents = props.required_documents.map((doc) => ({
        document_type_id: doc.document_type_id,
        document_type_name: doc.document_type.nom,
        expiration_required: !!doc.expiration_required,
        obligatoire: !!doc.obligatoire,
        fichier: null,
        date_expiration: null,
        observation: null,
    }));
    open.value = true;
};

const update = (rowData) => {
    router.visit(`${route("salarie.show", { salarie: rowData.id })}`, {
        preserveState: true,
        preserveScroll: true,
        only: ["flash"],
        onSuccess: (reponse) => {
            const response = reponse.props.flash.data;
            Object.keys(response).forEach((key) => {
                if (key in form) {
                    form[key] = response[key];
                }
            });

            // Charger les infos chauffeur spécifiques
            if (response.chauffeur) {
                form.vehicule_id = response.chauffeur.vehicule_id;
                form.parent_chauffeur_id =
                    response.chauffeur.parent_chauffeur_id;
            }

            if (response.photo) {
                imageUrl.value = `/${response.photo}`;
            }

            // Initialiser les documents pour l'update
            form.documents = props.required_documents.map((doc) => ({
                document_type_id: doc.document_type_id,
                document_type_name: doc.document_type.nom,
                expiration_required: !!doc.expiration_required,
                obligatoire: !!doc.obligatoire,
                fichier: null,
                date_expiration: null,
                observation: null,
            }));

            title.value = "Modifier le Salarié";
            open.value = true;
        },
    });
};

const submit = () => {
    form.clearErrors();
    const method = form.id ? "put" : "post";
    const url = form.id
        ? route("salarie.update", form.id)
        : route("salarie.store");

    // Pour les formulaires avec fichiers, Inertia recommande d'utiliser POST et d'ajouter _method pour les PUT
    form.transform((data) => ({
        ...data,
        _method: method.toUpperCase(),
    })).post(url, {
        onSuccess: () => close(),
        forceFormData: true,
    });
};

const onPhotoChange = (file) => {
    form.photo = file;
};

const sexeOptions = [
    { label: "Masculin", value: "M" },
    { label: "Féminin", value: "F" },
];

const statutOptions = [
    { label: "Actif", value: "actif" },
    { label: "Inactif", value: "inactif" },
    { label: "Suspendu", value: "suspendu" },
];

defineExpose({ add, update, close });
</script>

<template>
    <FormModal
        v-model:open="open"
        :loading="form.processing"
        @close="close"
        @submit="submit"
        :titre="title"
        size="full_screen"
        :show_champ_obligatoir="false"
    >
        <a-tabs v-model:activeKey="activeTab" class="w-full custom-tabs">
            <a-tab-pane
                key="info"
                tab="Informations Personnelles"
                class="bg-gray-100 p-4 rounded-md"
            >
                <div class="flex flex-col gap-3 mb-3">
                    <!-- Top Section: Photo and Identity -->
                    <div
                        class="grid grid-cols-1 lg:grid-cols-12 gap-3 items-stretch"
                    >
                        <!-- Section Photo -->
                        <div class="lg:col-span-3 flex">
                            <div
                                class="flex flex-col items-center justify-start p-8 bg-white rounded-lg border border-dashed border-gray-200 hover:border-primary/40 transition-all duration-500 hover:shadow-md h-full w-full"
                            >
                                <div class="w-full text-center mb-4">
                                    <h3
                                        class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-0.5"
                                    >
                                        Photo de Profil
                                    </h3>
                                    <p
                                        class="text-xs text-gray-400 font-medium"
                                    >
                                        Portrait ou photo d'identité
                                    </p>
                                </div>

                                <div
                                    class="relative group rounded-lg overflow-hidden shadow-inner"
                                >
                                    <UploadImage
                                        :url="imageUrl"
                                        @onChange="onPhotoChange"
                                        class="!min-w-[240px]"
                                    />
                                </div>

                                <!-- Affichage du Matricule en mode modification -->
                                <div
                                    v-if="form.id"
                                    class="mt-2 w-full text-center p-3 bg-primary/5 rounded-lg border border-primary/10"
                                >
                                    <span
                                        class="text-xs font-bold text-primary/60 uppercase tracking-widest block mb-1"
                                        >Matricule actuel</span
                                    >
                                    <span
                                        class="text-xl font-black text-primary tracking-tighter"
                                        >{{ form.matricule }}</span
                                    >
                                </div>

                                <div
                                    class="mt-4 w-full p-4 bg-gray-50 rounded-lg border border-gray-200"
                                >
                                    <h4
                                        class="text-xs font-bold text-gray-500 uppercase mb-3 tracking-wider flex items-center"
                                    >
                                        <font-awesome-icon
                                            icon="fa-solid fa-shield-halved"
                                            class="mr-2 text-primary/60"
                                        />
                                        Spécifications
                                    </h4>
                                    <ul class="space-y-2">
                                        <li
                                            class="flex items-center text-[11px] text-gray-500 font-medium"
                                        >
                                            <div
                                                class="w-1.5 h-1.5 rounded-full bg-green-400 mr-2"
                                            ></div>
                                            Format : WebP, JPG ou PNG
                                        </li>
                                        <li
                                            class="flex items-center text-[11px] text-gray-500 font-medium"
                                        >
                                            <div
                                                class="w-1.5 h-1.5 rounded-full bg-green-400 mr-2"
                                            ></div>
                                            Poids max : 2.0 Mo
                                        </li>
                                        <li
                                            class="flex items-center text-[11px] text-gray-500 font-medium"
                                        >
                                            <div
                                                class="w-1.5 h-1.5 rounded-full bg-green-400 mr-2"
                                            ></div>
                                            Recommandé : Carré (1:1)
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Section Informations Personnelles -->
                        <div class="lg:col-span-9 flex">
                            <div
                                class="bg-white p-6 rounded-lg border border-gray-100 flex flex-col h-full w-full"
                            >
                                <div
                                    class="flex items-center mb-4 pb-1 border-b border-gray-100"
                                >
                                    <font-awesome-icon
                                        icon="fa-solid fa-id-card"
                                        class="text-primary text-xl mr-3"
                                    />
                                    <h3 class="text-lg font-bold text-gray-800">
                                        Informations d'Identification
                                    </h3>
                                </div>
                                <div
                                    class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-1"
                                >
                                    <form-item
                                        required
                                        has-feedback
                                        label="Type de Salarié"
                                        class="md:col-span-2"
                                        :help="form.errors.type_salarie_id"
                                    >
                                        <a-select
                                            v-model:value="form.type_salarie_id"
                                            placeholder="Sélectionner un type de poste"
                                            size="large"
                                            :options="types_salarie"
                                            :field-names="{
                                                label: 'label',
                                                value: 'value',
                                            }"
                                            class="w-full"
                                        />
                                    </form-item>

                                    <!-- Champs conditionnels pour Chauffeur -->
                                    <form-item
                                        v-if="isChauffeur"
                                        has-feedback
                                        label="Véhicule Assigné"
                                        class="md:col-span-2"
                                        :help="form.errors.vehicule_id"
                                    >
                                        <a-select
                                            v-model:value="form.vehicule_id"
                                            placeholder="Sélectionner un véhicule"
                                            size="large"
                                            show-search
                                            :filter-option="
                                                (input, option) =>
                                                    option.label
                                                        .toLowerCase()
                                                        .indexOf(
                                                            input.toLowerCase(),
                                                        ) >= 0
                                            "
                                            class="w-full"
                                        >
                                            <a-select-option
                                                v-for="v in vehicules"
                                                :key="v.id"
                                                :value="v.id"
                                                :label="`${v.immatriculation} - ${v.marque} ${v.modele}`"
                                            >
                                                {{ v.immatriculation }} -
                                                {{ v.marque }} {{ v.modele }}
                                            </a-select-option>
                                        </a-select>
                                    </form-item>

                                    <!-- Champs conditionnels pour Aide Chauffeur -->
                                    <form-item
                                        v-if="isAideChauffeur"
                                        has-feedback
                                        label="Chauffeur Référent"
                                        class="md:col-span-2"
                                        :help="form.errors.parent_chauffeur_id"
                                    >
                                        <a-select
                                            v-model:value="
                                                form.parent_chauffeur_id
                                            "
                                            placeholder="Assigner à un chauffeur"
                                            size="large"
                                            show-search
                                            :filter-option="
                                                (input, option) =>
                                                    option.label
                                                        .toLowerCase()
                                                        .indexOf(
                                                            input.toLowerCase(),
                                                        ) >= 0
                                            "
                                            class="w-full"
                                        >
                                            <a-select-option
                                                v-for="c in chauffeurs_list"
                                                :key="c.id"
                                                :value="c.id"
                                                :label="`${c.nom} ${c.prenom} (${c.matricule})`"
                                            >
                                                {{ c.nom }} {{ c.prenom }} ({{
                                                    c.matricule
                                                }})
                                            </a-select-option>
                                        </a-select>
                                    </form-item>

                                    <form-item
                                        required
                                        has-feedback
                                        label="Nom"
                                        :help="form.errors.nom"
                                    >
                                        <a-input
                                            v-model:value="form.nom"
                                            placeholder="Nom"
                                            size="large"
                                        />
                                    </form-item>

                                    <form-item
                                        has-feedback
                                        label="Prénom"
                                        :help="form.errors.prenom"
                                    >
                                        <a-input
                                            v-model:value="form.prenom"
                                            placeholder="Prénom"
                                            size="large"
                                        />
                                    </form-item>

                                    <form-item
                                        has-feedback
                                        label="Sexe"
                                        :help="form.errors.sexe"
                                    >
                                        <a-select
                                            v-model:value="form.sexe"
                                            placeholder="Sexe"
                                            size="large"
                                            :options="sexeOptions"
                                            class="w-full"
                                        />
                                    </form-item>

                                    <form-item
                                        has-feedback
                                        label="Date de naissance"
                                        :help="form.errors.date_naissance"
                                    >
                                        <a-date-picker
                                            v-model:value="form.date_naissance"
                                            class="w-full"
                                            size="large"
                                            value-format="YYYY-MM-DD"
                                        />
                                    </form-item>

                                    <form-item
                                        has-feedback
                                        label="Lieu de naissance"
                                        :help="form.errors.lieu_naissance"
                                    >
                                        <a-input
                                            v-model:value="form.lieu_naissance"
                                            placeholder="Lieu de naissance"
                                            size="large"
                                        />
                                    </form-item>

                                    <form-item
                                        has-feedback
                                        label="Nationalité"
                                        :help="form.errors.nationalite"
                                    >
                                        <a-input
                                            v-model:value="form.nationalite"
                                            placeholder="Nationalité"
                                            size="large"
                                        />
                                    </form-item>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                        <div class="md:col-span-2">
                            <!-- Section Coordonnées -->
                            <div
                                class="bg-white p-6 rounded-lg border border-gray-100 h-full"
                            >
                                <div
                                    class="flex items-center mb-4 pb-1 border-b border-gray-100"
                                >
                                    <font-awesome-icon
                                        icon="fa-solid fa-address-book"
                                        class="text-primary text-xl mr-3"
                                    />
                                    <h3 class="text-lg font-bold text-gray-800">
                                        Coordonnées & Contact
                                    </h3>
                                </div>
                                <div
                                    class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-1"
                                >
                                    <form-item
                                        has-feedback
                                        label="Email"
                                        :help="form.errors.email"
                                    >
                                        <a-input
                                            v-model:value="form.email"
                                            placeholder="email@exemple.com"
                                            size="large"
                                        />
                                    </form-item>

                                    <form-item
                                        has-feedback
                                        label="Téléphone"
                                        :help="form.errors.telephone"
                                    >
                                        <a-input
                                            v-model:value="form.telephone"
                                            placeholder="Téléphone"
                                            size="large"
                                        />
                                    </form-item>

                                    <form-item
                                        has-feedback
                                        label="Adresse"
                                        class="md:col-span-2"
                                        :help="form.errors.adresse"
                                    >
                                        <a-textarea
                                            v-model:value="form.adresse"
                                            placeholder="Adresse complète"
                                            :rows="2"
                                        />
                                    </form-item>
                                </div>
                            </div>
                        </div>

                        <div class="md:col-span-2">
                            <!-- Section Administrative -->
                            <div
                                class="bg-white p-6 rounded-lg border border-gray-100 h-full"
                            >
                                <div
                                    class="flex items-center mb-4 pb-1 border-b border-gray-100"
                                >
                                    <font-awesome-icon
                                        icon="fa-solid fa-briefcase"
                                        class="text-primary text-xl mr-3"
                                    />
                                    <h3 class="text-lg font-bold text-gray-800">
                                        Détails Administratifs
                                    </h3>
                                </div>
                                <div
                                    class="grid grid-cols-1 md:grid-cols-4 gap-x-6 gap-y-1"
                                >
                                    <form-item
                                        has-feedback
                                        label="Numéro CIN"
                                        :help="form.errors.cin"
                                    >
                                        <a-input
                                            v-model:value="form.cin"
                                            placeholder="Numéro CIN"
                                            size="large"
                                        />
                                    </form-item>

                                    <form-item
                                        has-feedback
                                        label="Date CIN"
                                        :help="form.errors.date_cin"
                                    >
                                        <a-date-picker
                                            v-model:value="form.date_cin"
                                            class="w-full"
                                            size="large"
                                            value-format="YYYY-MM-DD"
                                        />
                                    </form-item>

                                    <form-item
                                        has-feedback
                                        label="Lieu CIN"
                                        :help="form.errors.lieu_cin"
                                    >
                                        <a-input
                                            v-model:value="form.lieu_cin"
                                            placeholder="Lieu délivrance"
                                            size="large"
                                        />
                                    </form-item>

                                    <form-item
                                        has-feedback
                                        label="Date d'embauche"
                                        :help="form.errors.date_embauche"
                                    >
                                        <a-date-picker
                                            v-model:value="form.date_embauche"
                                            class="w-full"
                                            size="large"
                                            value-format="YYYY-MM-DD"
                                        />
                                    </form-item>

                                    <form-item
                                        has-feedback
                                        label="Salaire"
                                        :help="form.errors.salaire"
                                    >
                                        <a-input-number
                                            v-model:value="form.salaire"
                                            placeholder="Montant"
                                            size="large"
                                            class="w-full"
                                            prefix="Ar"
                                            :min="0"
                                            :precision="2"
                                            :formatter="
                                                (value) =>
                                                    !value
                                                        ? ''
                                                        : `${value}`.replace(
                                                              /\B(?=(\d{3})+(?!\d))/g,
                                                              ' ',
                                                          )
                                            "
                                            :parser="
                                                (value) =>
                                                    value.replace(
                                                        /\s?|(\s*)/g,
                                                        '',
                                                    )
                                            "
                                        />
                                    </form-item>

                                    <form-item
                                        has-feedback
                                        label="Statut"
                                        :help="form.errors.statut"
                                    >
                                        <a-select
                                            v-model:value="form.statut"
                                            placeholder="Statut"
                                            size="large"
                                            :options="statutOptions"
                                            class="w-full"
                                        />
                                    </form-item>

                                    <form-item
                                        has-feedback
                                        label="Observation"
                                        class="md:col-span-2"
                                        :help="form.errors.observation"
                                    >
                                        <a-textarea
                                            v-model:value="form.observation"
                                            placeholder="Observations"
                                            :rows="2"
                                        />
                                    </form-item>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </a-tab-pane>

            <a-tab-pane
                key="docs"
                tab="Documents Administratifs"
                class="bg-gray-100 p-4 rounded-md"
            >
                <div
                    class="p-6 bg-white rounded-lg border border-gray-100 min-h-[400px]"
                >
                    <div
                        class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8 pb-4 border-b border-gray-100"
                    >
                        <div class="flex items-center gap-3">
                            <div
                                class="w-12 h-12 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-500 text-xl"
                            >
                                <font-awesome-icon
                                    icon="fa-solid fa-file-shield"
                                />
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-gray-800">
                                    Dossier Numérique
                                </h3>
                                <p class="text-sm text-gray-500">
                                    Gérez les documents obligatoires et
                                    contractuels du salarié
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-4">
                        <div
                            v-for="(doc, index) in form.documents"
                            :key="doc.document_type_id"
                            class="p-4 bg-gray-50 rounded-lg border border-gray-100 hover:border-primary/30 transition-colors"
                        >
                            <div
                                class="flex flex-col md:flex-row md:items-center justify-between gap-4"
                            >
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="font-bold text-gray-700">{{
                                            doc.document_type_name
                                        }}</span>
                                        <a-tag
                                            v-if="doc.obligatoire"
                                            color="error"
                                            class="text-[10px] uppercase"
                                        >
                                            Obligatoire
                                        </a-tag>
                                    </div>
                                    <p class="text-xs text-gray-400">
                                        Uploader le document numérisé
                                    </p>
                                </div>

                                <div
                                    class="flex flex-col md:flex-row gap-4 flex-1 items-end md:items-center"
                                >
                                    <div class="w-full md:w-auto flex-1">
                                        <a-input
                                            type="file"
                                            @change="
                                                (e) =>
                                                    (form.documents[
                                                        index
                                                    ].fichier =
                                                        e.target.files[0])
                                            "
                                            class="!rounded-lg"
                                        />
                                    </div>
                                    <div
                                        v-if="doc.expiration_required"
                                        class="w-full md:w-48"
                                    >
                                        <a-date-picker
                                            v-model:value="
                                                form.documents[index]
                                                    .date_expiration
                                            "
                                            placeholder="Date d'expiration"
                                            class="w-full"
                                            value-format="YYYY-MM-DD"
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div
                            v-if="form.documents.length === 0"
                            class="md:col-span-2 py-20 flex flex-col items-center justify-center bg-gray-50 rounded-lg border border-dashed border-gray-200"
                        >
                            <font-awesome-icon
                                icon="fa-solid fa-folder-open"
                                class="text-5xl text-gray-200 mb-4"
                            />
                            <p class="text-gray-400 font-medium italic">
                                Aucun document requis configuré pour ce type de
                                salarié.
                            </p>
                        </div>
                    </div>

                    <div
                        v-if="form.id"
                        class="mt-10 pt-10 border-t border-gray-100"
                    >
                        <div class="flex items-center mb-6">
                            <font-awesome-icon
                                icon="fa-solid fa-folder-open"
                                class="text-primary/40 text-lg mr-3"
                            />
                            <h3 class="text-lg font-bold text-gray-800">
                                Explorateur de Documents
                            </h3>
                        </div>
                        <DynamicDocumentManager
                            modelClass="App\Models\Salarie"
                            :modelId="form.id"
                        />
                    </div>
                </div>
            </a-tab-pane>
        </a-tabs>
    </FormModal>
</template>

<style scoped>
.custom-tabs :deep(.ant-tabs-nav) {
    @apply mb-0 px-4 bg-white border-b border-gray-200;
}

.custom-tabs :deep(.ant-tabs-tab) {
    @apply py-4 px-2 font-semibold text-gray-500 transition-all hover:text-primary;
}

.custom-tabs :deep(.ant-tabs-tab-active) {
    @apply text-primary;
}

:deep(.ant-form-item) {
    @apply mb-3;
}

:deep(.ant-form-item-label > label) {
    @apply text-xs font-bold text-gray-500 uppercase tracking-tight;
}

:deep(.ant-input),
:deep(.ant-select-selector),
:deep(.ant-picker),
:deep(.ant-input-number) {
    @apply rounded-xl border-gray-200 !shadow-none hover:border-primary focus:border-primary transition-all !duration-300;
}

:deep(.ant-input-number-input) {
    @apply font-bold text-primary;
}

.custom-file-input {
    @apply !p-1 !h-auto text-xs text-gray-500 bg-white border border-gray-200 rounded-lg hover:border-primary transition-colors;
}

.custom-file-input::file-selector-button {
    @apply mr-3 py-1.5 px-3 !rounded-md border-0 text-xs font-bold bg-primary/10 text-primary hover:bg-primary/20 cursor-pointer transition-colors;
}
</style>
