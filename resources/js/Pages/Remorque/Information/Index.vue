<script>
const TYPE = [
    {value: 'Photo origine', label: 'Photo origine'},
    {value: 'Constat', label: 'Constat'},
    {value: 'Controle Technique', label: 'Controle Technique'},
    {value: 'Entretien', label: 'Entretien'},
    {value: 'Sinistre', label: 'Sinistre'},
];
</script>

<script setup>
import SousMenuPrincipale from "@/Pages/Menu/SousMenuPrincipale.vue";

import {nextTick, ref, watch} from "vue";
import {gotoSearch} from "../../../../Utils/FiltreUtils.js";

import FilterBase from "@/Components/Filter/FilterBase.vue";
import ListePhotos from "@/Pages/Remorque/Information/ListePhotos.vue";
import {DownOutlined} from "@ant-design/icons-vue";
import {message, Space} from "ant-design-vue";
import ButtonIcon from "@/Components/ButtonIcon.vue";
import ListeDocuments from "@/Pages/Remorque/Information/ListeDocuments.vue";
import usePermissions from "@/UserPermissions/usePermissions.js";
import RemorqueInformation from "@/Pages/Remorque/Information/RemorqueInformation.vue";
import RemorqueElement from "@/Pages/Remorque/Information/RemorqueElement.vue";

const { can } = usePermissions();

const dateFormat = 'YYYY/MM/DD';


const props = defineProps({
    info: {
        type: Object,
        default: () => ({})
    },
    elements: {
        type: Object,
        default: []
    },
    LIST_ELEMENT_VEHICULE: {
        type: Array,
        default: []
    },
    liste_couleur: {
        type: Array,
        default: () => []
    },
    liste_modele: {
        type: Array,
        default: () => []
    },
    liste_marque: {
        type: Array,
        default: () => []
    },
    list_emplacements: {
        type: Array,
        default: () => []
    },

    liste_photo: {
        type: Array,
        default: [],
    },
    documents: {
        type: Array,
        default: [],
    },


    filters: {
        type: Object,
        default: () => ({})
    },
    flash: {
        type: Object,
        default: () => ({})
    },
    errors: Object
});
const activeKey = ref('1');
const title = ref('Informations sur le remorque : ');
const ref_photo = ref();
const ref_document = ref();

const errors = ref({...props.errors});

const filterPhoto = ref({
    search: '',
    search_photo: '',
    start_date: '',
    end_date: '',
    type_element: '',
});

const filterDocument = ref({
    search: '',
    search_document: '',
    start_date_document: '',
    end_date_document: '',
});

const dropdownVisible = ref(false);
const dropdownDocument = ref(false);

const localCouleurVehicule = ref([...props.liste_couleur]);
const localModeleVehicule = ref([...props.liste_modele]);
const localMarqueVehicule = ref([...props.liste_marque]);

watch(() => props.liste_couleur, (newVal) => {
    localCouleurVehicule.value = [...newVal];
}, {deep: true});

watch(() => props.liste_modele, (newVal) => {
    localModeleVehicule.value = [...newVal];
}, {deep: true});

watch(() => props.liste_marque, (newVal) => {
    localMarqueVehicule.value = [...newVal];
}, {deep: true});



const closeDropdown = () => {
    dropdownVisible.value = false;
    dropdownDocument.value = false;
}


const localElements = ref([...props.LIST_ELEMENT_VEHICULE]);
const form = ref({...props.info.data.vehicule})

const applyFilters = (data) => {
    filterPhoto.value.search_photo = data.search;
    const url = route('remorque.info', {remorque: props.info.data.vehicule.id});
    gotoSearch(filterPhoto.value, url, ['liste_photo'])
};

const applyFiltersDocument = (data) => {
    filterDocument.value.search_document = data.search;
    const url = route('remorque.info', {remorque: props.info.data.vehicule.id});
    gotoSearch(filterDocument.value, url, ['documents'])
};

const resetFilterPhoto = () => {
    filterPhoto.value.search = "";
    filterPhoto.value.search_photo = "";
    filterPhoto.value.start_date = "";
    filterPhoto.value.end_date = "";
    filterPhoto.value.type_element = "";
}

const resetFilterDocument = () => {
    filterDocument.value.search = "";
    filterDocument.value.search_document = "";
    filterDocument.value.start_date_document = "";
    filterDocument.value.end_date_document = "";

}

const resetFilters = () => {
    resetFilterPhoto();
    resetFilterDocument()
    const url = route('remorque.info', {remorque: props.info.data.vehicule.id});
    gotoSearch(filterPhoto.value, url, ['liste_photo'])
};

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

</script>

<template>

    <SousMenuPrincipale :title="title + info.data.vehicule.numero_remorque" selectedMenu="remorque" :backTo="route('remorque.index')">
        <a-card size="middle" class="h-full">

            <a-tabs v-model:activeKey="activeKey" animated class="overflow-hidden">
                <a-tab-pane key="1" tab="Informations du remorque" v-if="can('vehicule.update')">
                    <RemorqueInformation :information="info.data.vehicule"
                                         :LIST_COULEUR_VEHICULE="localCouleurVehicule"
                                         :LIST_MODELE_VEHICULE="localModeleVehicule"
                                         :LIST_MARQUE_VEHICULE="localMarqueVehicule"
                                         :show-element-vehicule="false" />

                </a-tab-pane>

                <a-tab-pane key="2" tab="Eléments Véhicule" v-if="can('vehicule.index_element_vehicule')">
                    <RemorqueElement :tab_element_vehicule="localElements"
                                     :LIST_ELEMENT="list_emplacements"
                                     :formulaire = "form"
                    />


                </a-tab-pane>

                <a-tab-pane key="3" tab="Liste Photos" v-if="can('vehicule.index_vehicule_photo')">

                    <FilterBase v-model="filterPhoto"
                                @search="applyFilters"
                                @reset="resetFilters"
                                :show_boxShasow="false"
                                class="!px-0"
                    >
                        <template #otherFilter>
                            <a-popover
                                placement="bottomRight"
                                trigger="click"
                                :visible="dropdownVisible"
                                @visibleChange="val => dropdownVisible = val"
                            >
                                <template #content>
                                    <div class="bg-white p-4 w-96 space-y-2 rounded-md">
                                        <a-date-picker
                                            v-model:value="filterPhoto.start_date"
                                            :format="dateFormat"
                                            size="large"
                                            class="w-full text-center"
                                            placeholder="Du"
                                            :value-format="'DD-MM-YYYY'"
                                        />
                                        <a-date-picker
                                            v-model:value="filterPhoto.end_date"
                                            :format="dateFormat"
                                            size="large"
                                            class="w-full text-center"
                                            placeholder="Au"
                                            :value-format="'DD-MM-YYYY'"
                                        />
                                        <a-select
                                            v-model:value="filterPhoto.type_element"
                                            placeholder="Type de photo"
                                            class="w-full"
                                            size="large"
                                            allow-clear
                                        >
                                            <a-select-option
                                                v-for="type in TYPE"
                                                :key="type.value"
                                                :value="type.value"
                                            >
                                                {{ type.label }}
                                            </a-select-option>
                                        </a-select>
                                        <div class="flex space-x-2 !mt-6">
                                            <a-button block type="primary" size="middle" @click="applyFilters(filterPhoto)">Appliquer</a-button>
                                            <a-button block type="default" size="middle" @click="closeDropdown">Fermer</a-button>
                                        </div>
                                    </div>
                                </template>

                                <a-button size="large" type="default" class="!rounded-none border-r-0 focus:z-10">
                                    <Space>
                                        <font-awesome-icon class="text-[15px]" icon="fa-filter" />
                                        Filtres
                                        <DownOutlined />
                                    </Space>
                                </a-button>
                            </a-popover>

                        </template>

                        <template #add>
                            <ButtonIcon v-if="can('vehicule.store_vehicule_photo')" @click="() => ref_photo.add(info.data.vehicule.id)" type="primary" text="Nouveau" icon="fa-plus" class="!rounded-md hover:!text-white hover:!bg-sky-800 transition-all duration-300"/>
                        </template>
                    </FilterBase>

                    <ListePhotos :tablePhoto="liste_photo"
                                 ref="ref_photo"

                    />


                </a-tab-pane>
                <a-tab-pane key="4" tab="Documents" v-if="can('vehicule.index_vehicule_document')" >

                    <FilterBase v-model="filterDocument"
                                @search="applyFiltersDocument"
                                @reset="resetFilters"
                                :show_boxShasow="false"
                                class="!px-0"
                    >
                        <template #otherFilter>
                            <a-popover
                                placement="bottomRight"
                                trigger="click"
                                :visible="dropdownDocument"
                                @visibleChange="val => dropdownDocument = val"
                            >
                                <template #content>
                                    <div class="bg-white p-2 w-96 space-y-2 rounded-md ">
                                        <a-date-picker
                                            v-model:value="filterDocument.start_date_document"
                                            :format="dateFormat"
                                            size="large"
                                            class="w-full text-center"
                                            placeholder="Du"
                                            :value-format="'DD-MM-YYYY'"
                                        />
                                        <a-date-picker
                                            v-model:value="filterDocument.end_date_document"
                                            :format="dateFormat"
                                            size="large"
                                            class="w-full text-center"
                                            placeholder="Au"
                                            :value-format="'DD-MM-YYYY'"
                                        />
                                        <div class="flex space-x-2 !mt-6">
                                            <a-button block type="primary" size="middle" @click="applyFiltersDocument(filterDocument)">Appliquer</a-button>
                                            <a-button block type="default" size="middle" @click="closeDropdown">Fermer</a-button>
                                        </div>
                                    </div>
                                </template>

                                <a-button size="large" type="default" class="!rounded-none border-r-0 focus:z-10">
                                    <Space>
                                        <font-awesome-icon class="text-[15px]" icon="fa-filter" />
                                        Filtres
                                        <DownOutlined />
                                    </Space>
                                </a-button>
                            </a-popover>

                        </template>

                        <template #add>
                            <ButtonIcon v-if="can('vehicule.store_vehicule_document')" @click="() => ref_document.add(info.data.vehicule.id)" type="primary" text="Nouveau" icon="fa-plus" class="!rounded-md hover:!text-white hover:!bg-sky-800 transition-all duration-300"/>
                        </template>
                    </FilterBase>

                    <ListeDocuments :tableDocument="documents"
                                    ref="ref_document"

                    />
                </a-tab-pane>
            </a-tabs>
        </a-card>
    </SousMenuPrincipale>
</template>

<style scoped>
/* Styles spécifiques si nécessaires */
</style>
