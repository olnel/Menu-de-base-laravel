<script setup>
import DataTable from "@/Components/DataTable.vue";
import {confirm_delete} from "../../../../Utils/confirmation_modal.js";
import {router} from "@inertiajs/vue3";
import {ref} from "vue";
import ModalPhotoVehicule from "@/Pages/Vehicule/Information/ModalPhotoVehicule.vue";
import ModalDocument from "@/Pages/Remorque/Information/ModalDocument.vue";
import {mapExistingImages} from "../../../../Utils/fileUtil.js";
import FilePreviewList from "@/Components/FilePreviewList.vue";

const remorque_id = ref(null);
const ref_photovehicule = ref();
const props = defineProps({
    tableDocument: {
        type: Array,
        default: [],
    }
});


const columns = [
    { key: "date_expiration", title: "Date", dataIndex: "date_expiration" },
    { key: "nom_document", title: "Nom du Document", dataIndex: "nom_document" },
    { key: "description", title: "Description", dataIndex: "description" },

];

const handleDelete = (record) => {
    confirm_delete(() => {
        router.delete(route('remorquedocument.destroy', record.id), {
            preserveScroll: true,
        });
    });
};


const add = (id) => {
    ref_photovehicule.value.openModal(id);
    remorque_id.value = id;
}

const actions = [
    { text: "Modification", action: (record) => ref_photovehicule.value.update(record), icon: 'fa-eye', privilege: 'remorque_document.update' },
    { text: "Supprimer", action: handleDelete, icon: 'fa-trash', privilege: 'remorque_document.destroy'},
];

defineExpose({ add });
</script>

<template>
    <DataTable
        :columns="columns"
        :data="tableDocument"
        :actions="actions"
        class="main-shadow"
        :show-index="true"
        :btn_action="false"
    >
        <template #expandedRowRender="{ record }">

            <div class="p-4">
                <FilePreviewList
                    :initial-files="
                            mapExistingImages(record.fichier_jointe)
                        "
                    :disabled="true"
                    :preview-only="true"
                />
            </div>
        </template>

        <template #emptyText>
            <a-empty description="Aucun document associé" size="small" />
        </template>

    </DataTable>

    <!--    <FormulairePhotoVehicule ref="ref_photovehicule" />-->

    <ModalDocument
        ref="ref_photovehicule"
        :remorque_id="remorque_id"
        :vehiculeDocuments="{}"
    />
</template>

<style scoped>

</style>
