<script setup>
import {ref} from "vue";
import {router} from "@inertiajs/vue3";
import FormModal from "@/Components/FormModal.vue";
import {message, Popconfirm} from 'ant-design-vue'; // Import Popconfirm and message for confirmation
import {DeleteOutlined} from '@ant-design/icons-vue';
import dayjs from "dayjs";

const props = defineProps({
    flash: Object,
    tresoreries: {
        type: Array,
        default: [],
    }
});
const dateFormat = 'DD/MM/YYYY';

const historiqueReglement = ref([]);
const open = ref(false);
const title = ref("");
const currentFacture = ref(null);

const close = () => {
    open.value = false;
};

const voirReglement = (rowData) => {
    currentFacture.value = rowData;
    router.visit(`${route('factureclientreglement.historique', {factureclient: rowData.id})}`, {
        preserveState: true,
        preserveScroll: true,
        only: ['flash'],
        onSuccess: (reponse) => {
            historiqueReglement.value = reponse.props.flash.data;
            title.value = "Historique règlement facture: " + rowData.numero_facture;
            open.value = true;
        },
        onError: (errors) => {
            message.error("Erreur lors du chargement de l'historique des règlements.");
            console.error(errors);
        }
    });
};

const deleteReglement = (reglement, index) => {

    router.delete(route('factureclientreglement.destroy', reglement.id), {
        preserveScroll: true,
        onSuccess: () => {
            historiqueReglement.value.splice(index, 1)
        },
    });
};

defineExpose({ voirReglement, close});
</script>

<template>
    <FormModal
        v-model:open="open"
        @close="close"
        :titre="title"
        size="lg"
        :show_champ_obligatoir="false"
        :show-footer="false"
    >
        <div v-if="historiqueReglement.length > 0">
            <table class="w-full border-collapse">
                <thead>
                <tr>
                    <th class="border p-2 text-left">Date Règlement</th>
                    <th class="border p-2 text-left">Montant</th>
                    <th class="border p-2 text-left">Mode Paiement</th>
                    <th class="border p-2 text-left">Commentaire</th>
                    <th class="border p-2 text-left" width="40px"> -</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="(reglement, key) in historiqueReglement" :key="reglement.id">
                    <td class="border p-2">{{ dayjs(reglement.date_reglement).format(dateFormat) }}</td>
                    <td class="border p-2">{{ reglement.montant_reglement }}</td>
                    <td class="border p-2">{{ reglement.mode_reglement }}</td>
                    <td class="border p-2">{{ reglement.commentaire }}</td>
                    <td class="border p-2">
                        <Popconfirm
                            title="Êtes-vous sûr de vouloir supprimer ce règlement ?"
                            ok-text="Oui"
                            cancel-text="Non"
                            @confirm="deleteReglement(reglement, key)"
                        >
                            <a-button danger type="text" size="small">
                                <template #icon>
                                    <DeleteOutlined />
                                </template>
                            </a-button>
                        </Popconfirm>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
        <div v-else class="text-center p-4 text-gray-500">
            Aucun historique de règlement pour cette facture.
        </div>
    </FormModal>
</template>

<style scoped>
/* Vos styles existants */
:deep(.ant-upload) {
    width: 100%;
}

:deep(.ant-card) {
    border-radius: 8px;
    overflow: hidden;
    border: 1px solid #f0f0f0;
    transition: all 0.3s;
}

:deep(.ant-card:hover) {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

:deep(.ant-btn-circle) {
    width: 24px;
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.truncate {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

tfoot tr td {
    border: 1px solid #c9c9c9c9;
}

/* Styles spécifiques au tableau */
table {
    width: 100%;
    border-collapse: collapse;
}

th, td {
    border: 1px solid #e8e8e8;
    padding: 8px;
    text-align: left;
}

th {
    background-color: #f5f5f5;
    font-weight: bold;
}
</style>
