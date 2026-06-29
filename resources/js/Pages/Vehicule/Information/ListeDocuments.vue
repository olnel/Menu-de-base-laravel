<script setup>
import DataTable from "@/Components/DataTable.vue";
import {confirm_delete} from "../../../../Utils/confirmation_modal.js";
import {router} from "@inertiajs/vue3";
import {ref} from "vue";
import ModalPhotoVehicule from "@/Pages/Vehicule/Information/ModalPhotoVehicule.vue";
import ModalDocument from "@/Pages/Vehicule/Information/ModalDocument.vue";
import {mapExistingImages} from "../../../../Utils/fileUtil.js";
import FilePreviewList from "@/Components/FilePreviewList.vue";

const vehicule_id = ref(null);
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
        router.delete(route('vehiculedocument.destroy', record.id), {
            preserveScroll: true,
        });
    });
};


const add = (id) => {
    ref_photovehicule.value.openModal(id);
    vehicule_id.value = id;
}

const actions = [
    { text: "Modification", action: (record) => ref_photovehicule.value.update(record), icon: 'fa-eye', privilege: 'vehicule.update_vehicule_document' },
    { text: "Supprimer", action: handleDelete, icon: 'fa-trash', privilege: 'vehicule.destroy_vehicule_document'},
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
        :vehicule_id="vehicule_id"
        :vehiculeDocuments="{}"
    />
</template>

<style scoped>

</style>
