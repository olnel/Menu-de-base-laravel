<script setup>
import {ref, watch, nextTick} from "vue";
import VehiculeInformation from "@/Pages/Vehicule/VehiculeForm/VehiculeInformation.vue";
import FormModal from "@/Components/FormModal.vue";
import {router} from "@inertiajs/vue3";
import VehiculeImages from "@/Pages/Vehicule/VehiculeForm/VehiculeImages.vue";
import DynamicDocumentManager from "@/Components/DynamicDocumentManager.vue";
import { message } from 'ant-design-vue'; // ← Assure-toi que c'est bien importé

const props = defineProps({
    LIST_ELEMENT_VEHICULE: {
        type: Array,
        default: () => []
    },
    errors: {
        type: Object,
        default: () => ({})
    }
});

const activeKey = ref('1');
const open = ref(false);
const title = ref("");

const errors = ref({...props.errors});

// Réactifs locaux pour les données
const localInformation = ref(null);
const localElements = ref([...props.LIST_ELEMENT_VEHICULE]);
const localPhotos = ref(null);
const documents = ref(null);

// Synchroniser les données si props changent
watch(() => props.LIST_ELEMENT_VEHICULE, (newVal) => {
    localElements.value = [...newVal];
}, {deep: true});

watch(() => props.errors, (newVal) => {
    errors.value = {...newVal};

    // Affichage des messages d'erreur
    if (Object.keys(newVal).length > 0) {
        nextTick(() => {
            for (const [champ, msg] of Object.entries(newVal)) {
                // Si l'erreur est une chaîne, on l'affiche directement
                if (typeof msg === 'string') {
                    message.error(msg);
                }
                // Sinon, on suppose que c’est un tableau (ex: Laravel)
                else if (Array.isArray(msg)) {
                    msg.forEach(err => message.error(err));
                }
            }
            errors.value = {};
        });
    }
}, {immediate: true, deep: true});

defineExpose({add, update, close});

function add() {
    title.value = "Ajouter un Véhicule";
    open.value = true;
}

function update(rowData) {
    router.visit(route('vehicule.info', {vehicule: rowData.id}));
}

/*function update(rowData) {
    router.visit(route('vehicule.show', {vehicule: rowData.id}), {
        preserveState: true,
        preserveScroll: true,
        onSuccess: (reponse) => {
            localInformation.value = reponse.props.flash.data.vehicule;
            localPhotos.value = reponse.props.flash.data.photos;
            documents.value = reponse.props.flash.data.documents;
            title.value = rowData.immatriculation;
            open.value = true;
        }
    });
}*/

function close() {
    open.value = false;
}
</script>


<template>
    <FormModal
        v-model:open="open"
        :titre="title"
        size="full_screen"
        :show_champ_obligatoir="false"
        :show-footer="false"
    >

        <a-tabs v-model:activeKey="activeKey">
            <a-tab-pane key="1" tab="Informations du vehicule">
                <pre v-if="Object.keys(localInformation).length === 0">Aucune information disponible</pre>
                <VehiculeInformation
                    v-else
                    :information="localInformation"
                    :elementVehicules="localElements"
                />
            </a-tab-pane>

            <a-tab-pane key="2" tab="Liste Images">

                <VehiculeImages
                    :vehicule_id="localInformation.id ?? 0"
                    :vehiculePhotos="localPhotos ?? []"
                />
            </a-tab-pane>
            <a-tab-pane key="3" tab="Documents">
                <DynamicDocumentManager 
                    v-if="localInformation?.id" 
                    modelClass="App\Models\Vehicule" 
                    :modelId="localInformation.id" 
                />
            </a-tab-pane>
        </a-tabs>
    </FormModal>
</template>
