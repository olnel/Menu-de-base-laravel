<script>
    const STATUT_COMMANDE = [
        {label: 'LIVRER' , value: 1},
        {label: 'NON LIVRER' , value: 0},
    ];
</script>
<script setup>
import SousMenuPrincipale from "@/Pages/Menu/SousMenuPrincipale.vue";
import DataTable from "@/Components/DataTable.vue";
import ButtonIcon from "@/Components/ButtonIcon.vue";
import {computed, reactive, ref} from "vue";
import { confirm_delete } from "../../../Utils/confirmation_modal.js";
import { router } from "@inertiajs/vue3";
import FilterBase from "@/Components/Filter/FilterBase.vue";
import {createSearchFilter, gotoSearch} from "../../../Utils/FiltreUtils.js";
import usePermissions from "@/UserPermissions/usePermissions.js";
import {DownOutlined} from "@ant-design/icons-vue";
import {Space} from "ant-design-vue";
import ArticleApprovisionnemntFormulaire
    from "@/Pages/ArticleApprovisionnement/Formulaire/ArticleApprovisionnemntFormulaire.vue";
import ArticleBonCommandeFormulaire from "@/Pages/ArticleBonCommande/Formulaire/ArticleBonCommandeFormulaire.vue";
import dayjs from "dayjs";
import ApprovisionnementFormulaire from "@/Components/ApprovisionnementFormulaire/ApprovisionnementFormulaire.vue";
const { can } = usePermissions()
const dropdownVisible = ref(false);
const dateFormat = 'YYYY/MM/DD';

const props = defineProps({
    data: {
        type: Object,
        default: () => ({})
    },
    magasins: {
        type: Array,
        default: []
    },
    filters: {
        type: Object,
        default: () => ({})
    },
    flash: {
        type: Object,
        default: () => ({})
    },

    fournisseurs: {
        type: Array,
        default: () => ({})
    },
    errors: Object
});

const refFormModal = ref();
const refApproModal = ref();
const formPhotoModal = ref();

const filter = ref({...createSearchFilter(), start_date: null, end_date: null, magasin_id: null, nom_fournisseur: null, statut: null})

const title = computed(() => `Liste Bon Commande (${props.data?.total ?? 0})`);

const columns = [
    { key: "date_boncommande", title: "date", dataIndex: "date_boncommande", width: 20, customCell: () => ({ class: 'text-center' }),customHeaderCell:()=>({class:'!text-center'})  },
    { key: "numero_bon_commande", title: "N° BonCommande", dataIndex: "numero_bon_commande", width: 120,customCell:()=>({class:'!text-center'}),customHeaderCell:()=>({class:'!text-center'}) },
    { key: "nom_fournisseur", title: "Fournisseur", dataIndex: "nom_fournisseur", width: 120 },
    { key: "nom_user", title: "Utilisateur", dataIndex: "nom_user", width: 100, customCell: () => ({ class: 'text-left' }) },
    { key: "numero_bon_livraison", title: "N° Bon Livraison", dataIndex: "numero_bon_livraison", width: 100, customCell: () => ({ class: 'text-center' }),customHeaderCell:()=>({class:'!text-center'}) },
    { key: "date_heure_enregistrement", title: "Date.Enr", dataIndex: "date_heure_enregistrement", width: 100, customCell: () => ({ class: 'text-center' }),customHeaderCell:()=>({class:'!text-center'}) },
];

const handleDelete = (record)=> {
    confirm_delete(() => {
        router.delete(route('article_boncommande.destroy', record.id), {
            preserveScroll: true,
        });
    });
}

const printBonCommande = (record) => {
    // Créer un lien temporaire et déclencher le téléchargement
    const link = document.createElement('a');
    link.href = route('article_boncommande.print', { article_boncommande: record.id });
    link.target = '_blank';
    link.download = `bon-commande-${record.numero_bon_commande}.pdf`;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
};


const sendMailToFournisseur = (record) => {
    const url = route('article_boncommande.sendMail', { article_boncommande: record.id });

    router.visit(url, {
        preserveState: true,
        preserveScroll: true,
        only: ['flash'],
        onSuccess: (response) => {
            console.log(response)
        },
    });
}

const actions = [
    {
        text: "Modifier",
        action: (record) => refFormModal.value.update(record),
        icon: 'fa-pen-to-square',
        privilege: 'article_boncommande.update',
        disabled: (record) => record.is_genere_appro
    },
    {
        text: "Impression BonCommande",
        action: (record) =>printBonCommande(record),
        icon: 'fa-print',
        privilege: 'article_boncommande.impression'
    },
    {
        text: "Générer Approvisionnement",
        action: (record) => refApproModal.value.newappro(record),
        icon: 'fa-sync-alt',
        privilege: 'article_boncommande.appro',
        disabled: (record) => record.is_genere_appro
    },
    {
        text: "Envoi mail Fournisseur",
        action: (record) =>sendMailToFournisseur(record),
        icon: 'fa-paper-plane',
        privilege: 'article_boncommande.envoi_email'
    },
    {
        text: "Supprimer",
        action: handleDelete,
        icon: 'fa-trash',
        class:"!text-red-600",
        disabled: (record) => (record.is_you || props.data.total < 1 || record.is_genere_appro),
        privilege: 'article_boncommande.destroy'
    },
];


const applyFilters = (data) => {
    filter.value = data;
    const url = route('article_boncommande.index');
    gotoSearch(filter.value, url)
};

const closeDropdown = ()=> {
    dropdownVisible.value = false;
}

const resetFilters = () => {
    filter.value.search = "";
    filter.value.start_date = null;
    filter.value.end_date = null;
    filter.value.magasin_id = null;
    filter.value.nom_fournisseur = null;
    filter.value.statut = null;
    const url = route('article_boncommande.index');
    gotoSearch(filter.value, url)
};

const state = reactive({
    selectedRowKeys: [],
    loading: false,
});

const hasSelected = computed(() => state.selectedRowKeys.length > 0);
const onSelectChange = selectedRowKeys => {
    console.log('selectedRowKeys changed: ', selectedRowKeys);
    state.selectedRowKeys = selectedRowKeys;
};

const generateAppro = () => {

}

</script>

<template>
    <SousMenuPrincipale :title="title" selectedMenu="article_boncommande">
        <template #top>
            <FilterBase v-model="filter"
                        @search="applyFilters"
                        @reset="resetFilters"
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
                                v-model:value="filter.start_date"
                                :format="dateFormat"
                                size="large"
                                class="w-full text-center"
                                placeholder="Du"
                                :value-format="'DD-MM-YYYY'"
                            />
                            <a-date-picker
                                v-model:value="filter.end_date"
                                :format="dateFormat"
                                size="large"
                                class="w-full text-center"
                                placeholder="Au"
                                :value-format="'DD-MM-YYYY'"
                            />

                            <a-select
                                v-model:value="filter.nom_fournisseur"
                                placeholder="Fournisseur"
                                :options="props.fournisseurs"
                                class="w-full"
                                size="large"
                                allow-clear
                            >

                            </a-select>

                            <a-select
                                v-model:value="filter.statut"
                                placeholder="Etat"
                                :options="STATUT_COMMANDE"
                                class="w-full"
                                size="large"
                                allow-clear
                            >

                            </a-select>

                            <div class="flex space-x-2 !mt-6">
                                <a-button block type="primary" size="middle" @click="applyFilters(filter)">Appliquer</a-button>
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
                <div class="flex items-center gap-2">
                    <ButtonIcon
                        v-if="can('article_boncommande.store')"
                        @click="() => refFormModal.add()"
                        type="primary"
                        text="Nouveau Bon Commande"
                        icon="fa-plus"
                        class="!rounded-md hover:!text-white hover:!bg-sky-800 transition-all duration-300"
                    />
                </div>
            </template>
        </FilterBase>
        </template>

        <DataTable
            :columns="columns"
            :data="data"
            :actions="actions"
            class="main-shadow"
            :show-index="true"
            :btn_action="true"
        >
            <template #bodyCell="{ column, record}">
                <template v-if="column.key === 'remise_general_ariary'">
                    {{ new Intl.NumberFormat().format(record.remise_general_ariary) }}
                </template>

                <template v-if="column.key === 'montant_ht_appro'">
                    {{ new Intl.NumberFormat().format(record.montant_ht_appro) }}
                </template>
                <template v-if="column.key === 'montant_tva_appro'">
                    {{ new Intl.NumberFormat().format(record.montant_tva_appro) }}
                </template>
                <template v-if="column.key === 'montant_ttc_appro'">
                    {{ new Intl.NumberFormat().format(record.montant_ttc_appro) }}
                </template>
                <template v-if="column.key === 'date_boncommande'">
                    {{ record.date_boncommande ? dayjs(record.date_boncommande).format('DD/MM/YYYY') : ''}}
                </template>
                <template v-if="column.key === 'date_heure_enregistrement'">
                    {{ record.date_heure_enregistrement ? dayjs(record.date_heure_enregistrement).format('DD/MM/YYYY HH:mm:ss') : ''}}
                </template>

            </template>


        </DataTable>

        <ArticleBonCommandeFormulaire
            :magasins="magasins"
            :fournisseurs = "fournisseurs"
            ref="refFormModal"
        />

        <ApprovisionnementFormulaire ref="refApproModal"
                                     :magasins="magasins"
        />

    </SousMenuPrincipale>
</template>

<style scoped>

</style>
