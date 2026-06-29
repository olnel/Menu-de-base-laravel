<template>
    <FormModal
        v-model:open="isModalVisible"
        :titre="
            isEditMode
                ? 'Modifier Réservation'
                : 'Ajouter Réservation N° : ' + form.numero_reservation
        "
        :loading="form.processing"
        :show_champ_obligatoir="false"
        @close="close"
        @submit="submit"
        size="lg"
    >
        <div
            class="rounded-md p-4 lg:p-6 border border-gray-100 shadow-sm transition-all duration-300 bg-white"
        >
            <a-row :gutter="[16, 24]">
                <a-col :xs="24" :lg="12">
                    <FormItem
                        label="Date Reservation"
                        required
                        :help="form.errors.date_reservation"
                    >
                        <a-date-picker
                            v-model:value="form.date_reservation"
                            size="large"
                            class="w-full"
                            format="DD/MM/YYYY"
                        />
                    </FormItem>
                    <FormItem
                        label="Lieu de chargement"
                        required
                        :help="form.errors.lieu_chargement"
                    >
                        <AutocompleteComponent
                            v-model="form.lieu_chargement"
                            :options="lieuxOptions"
                            placeholder="Sélectionner ou saisir un lieu"
                            :allow-add="true"
                            :field-config="{
                                label: 'label',
                                value: 'value',
                                search: 'label',
                            }"
                            @search="handleLieuChargementSearch"
                        />
                    </FormItem>
                    <FormItem
                        label="Lieu de Livraison"
                        required
                        :help="form.errors.lieu_livraison"
                    >
                        <AutocompleteComponent
                            v-model="form.lieu_livraison"
                            :options="lieuxOptions"
                            placeholder="Sélectionner ou saisir un lieu"
                            :allow-add="true"
                            :field-config="{
                                label: 'label',
                                value: 'value',
                                search: 'label',
                            }"
                            @search="handleLieuLivraisonSearch"
                        />
                    </FormItem>
                </a-col>
                <a-col :xs="24" :lg="12">
                    <FormItem
                        v-if="!portail"
                        label="Client"
                        required
                        :help="form.errors.nom_client"
                    >
                        <AutocompleteComponent
                            v-model="form.nom_client"
                            :options="clients"
                            placeholder="Rechercher ou créer un client"
                            :field-config="{
                                label: 'label',
                                value: 'label',
                                search: 'label',
                            }"
                        />
                    </FormItem>
                    <FormItem
                        label="Nombre de véhicules"
                        required
                        :help="form.errors.nbr_vehicule"
                    >
                        <a-input-number
                            v-model:value="form.nbr_vehicule"
                            :min="1"
                            size="large"
                            class="w-full"
                            placeholder="Nombre de véhicules"
                        />
                    </FormItem>
                    <FormItem
                        v-if="!portail"
                        label="Etat"
                        required
                        :help="form.errors.etat_facture"
                    >
                        <a-select
                            v-model:value="form.etat_facture"
                            size="large"
                            class="w-full"
                            placeholder="Sélectionner l'état"
                        >
                            <a-select-option value="valide">Validé</a-select-option>
                            <a-select-option value="non_valide">Non Validé</a-select-option>
                        </a-select>
                    </FormItem>

                    <!-- Champs portail uniquement -->
                    <template v-if="portail">
                        <FormItem label="Poids (kg)" :help="form.errors.poids">
                            <a-input
                                v-model:value="form.poids"
                                size="large"
                                class="w-full"
                                placeholder="Ex : 500"
                            />
                        </FormItem>
                        <FormItem label="Volume (m³)" :help="form.errors.volume">
                            <a-input
                                v-model:value="form.volume"
                                size="large"
                                class="w-full"
                                placeholder="Ex : 10"
                            />
                        </FormItem>
                    </template>
                </a-col>
                <a-col :xs="24" :lg="24">
                    <FormItem
                        v-if="portail"
                        label="Type de marchandise"
                        :help="form.errors.type_marchandise"
                    >
                        <a-input
                            v-model:value="form.type_marchandise"
                            size="large"
                            class="w-full"
                            placeholder="Ex : Électroménager, Alimentaire..."
                        />
                    </FormItem>
                    <FormItem
                        label="Commentaire"
                        :help="form.errors.commentaire"
                    >
                        <a-textarea
                            v-model:value="form.commentaire"
                            :rows="4"
                        />
                    </FormItem>
                </a-col>
            </a-row>
        </div>
    </FormModal>
</template>

<script setup>
import AutocompleteComponent from "@/Components/AutoCompleteComponent/AutocompleteComponent.vue";
import FormItem from "@/Components/FormItem.vue";
import FormModal from "@/Components/FormModal.vue";
import { router, useForm } from "@inertiajs/vue3";
import dayjs from "dayjs";
import { computed, ref } from "vue";

const isModalVisible = ref(false);
const isEditMode = ref(false);

const props = defineProps({
    clients: {
        type: Array,
        default: () => [],
    },
    vehicules: {
        type: Array,
        default: () => [],
    },
    flash: {
        type: Object,
        default: () => ({}),
    },
    portail: {
        type: Boolean,
        default: false,
    },
    portailClientName: {
        type: String,
        default: null,
    },
});

const form = useForm({
    id: null,
    numero_reservation: null,
    date_reservation: null,
    lieu_chargement: null,
    lieu_livraison: null,
    nom_client: null,
    nbr_vehicule: null,
    etat_facture: "valide",
    commentaire: null,
    by_client: false,
    poids: null,
    volume: null,
    type_marchandise: null,
});

const searchLieuChargementValue = ref("");
const searchLieuLivraisonValue = ref("");
const lieuxOptions = ref([]);
const filteredLieuxChargementOptions = computed(() => {
    const options = lieuxOptions.value || [];
    const searchValue = searchLieuChargementValue.value;
    const newOptions = [...options];
    if (
        searchValue &&
        !options.some(
            (opt) => opt.label === searchValue || opt.value === searchValue
        )
    ) {
        newOptions.push({ value: searchValue, label: searchValue });
    }
    return newOptions;
});

const filteredLieuxLivraisonOptions = computed(() => {
    const options = lieuxOptions.value || [];
    const searchValue = searchLieuLivraisonValue.value;
    const newOptions = [...options];
    if (
        searchValue &&
        !options.some(
            (opt) => opt.label === searchValue || opt.value === searchValue
        )
    ) {
        newOptions.push({ value: searchValue, label: searchValue });
    }
    return newOptions;
});

const handleLieuChargementSearch = (value) => {
    searchLieuChargementValue.value = value;
    form.lieu_chargement = value;
};

const handleLieuLivraisonSearch = (value) => {
    searchLieuLivraisonValue.value = value;
    form.lieu_livraison = value;
};

const add = (initialData = {}) => {
    isEditMode.value = false;
    form.reset();
    form.clearErrors();

    if (initialData.date_reservation) {
        form.date_reservation = dayjs(initialData.date_reservation);
    } else {
        form.date_reservation = dayjs();
    }

    if (props.portail) {
        form.nom_client   = props.portailClientName;
        form.etat_facture = 'non_valide';
        form.by_client    = true;
    }

    const genRoute = props.portail
        ? 'portail.reservation.generatenumero'
        : 'reservation.generatenumero';

    router.visit(route(genRoute), {
        preserveState: true,
        preserveScroll: true,
        only: ["flash"],
        onSuccess: (page) => {
            const response = page.props.flash.data;
            form.numero_reservation = response.numero_reservation;
            lieuxOptions.value = page.props.flash.lieu_options || [];
            isModalVisible.value = true;
        },
    });
};

const update = (record) => {
    const showRoute = props.portail
        ? 'portail.reservation.show'
        : 'reservation.show';

    router.visit(route(showRoute, record.id), {
        preserveState: true,
        preserveScroll: true,
        only: ["flash"],
        onSuccess: (page) => {
            const reservation = page.props.flash.data;
            form.reset();
            form.clearErrors();
            form.id = reservation.id;
            form.numero_reservation = reservation.numero_reservation;
            form.date_reservation = dayjs(reservation.date_reservation);
            form.lieu_chargement = reservation.lieu_chargement;
            form.lieu_livraison = reservation.lieu_livraison;
            form.nom_client = reservation.nom_client;
            form.commentaire = reservation.commentaire;
            form.etat_facture = reservation.etat_facture;
            form.nbr_vehicule = reservation.nbr_vehicule;
            if (props.portail) {
                form.by_client        = true;
                form.poids            = reservation.poids;
                form.volume           = reservation.volume;
                form.type_marchandise = reservation.type_marchandise;
            }
            if (page.props.flash.lieu_options) {
                lieuxOptions.value = page.props.flash.lieu_options;
            }
            isEditMode.value = true;
            isModalVisible.value = true;
        },
    });
};

const close = () => {
    isModalVisible.value = false;
    form.reset();
};

const submit = () => {
    const dataToSend = { ...form.data() };
    if (dataToSend.date_reservation) {
        dataToSend.date_reservation =
            dataToSend.date_reservation.format("YYYY-MM-DD");
    }

    const onError = (errors) => {
        form.errors = errors;
    };

    const storeRoute  = props.portail ? 'portail.reservation.store'         : 'reservation.store';
    const updateRoute = props.portail ? 'portail.reservation.update'        : 'reservation.update';

    if (isEditMode.value) {
        router.put(route(updateRoute, form.id), dataToSend, {
            onSuccess: () => close(),
            onError,
            preserveState: true,
            preserveScroll: true,
        });
    } else {
        router.post(route(storeRoute), dataToSend, {
            onSuccess: () => close(),
            onError,
            preserveState: true,
            preserveScroll: true,
        });
    }
};

defineExpose({ add, update });
</script>

<style></style>
