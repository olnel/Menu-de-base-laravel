<script setup>
import DataTable from "@/Components/DataTable.vue";
import {confirm_delete} from "../../../../Utils/confirmation_modal.js";
import {router} from "@inertiajs/vue3";
import {ref} from "vue";
import ModalPhotoVehicule from "@/Pages/Vehicule/Information/ModalPhotoVehicule.vue";
import FilePreviewList from "@/Components/FilePreviewList.vue";
import {mapExistingImages} from "../../../../Utils/fileUtil.js";
import ModalPhotoRemorque from "@/Pages/Remorque/Information/ModalPhotoRemorque.vue";

const remorque_id = ref(null);
const ref_photovehicule = ref();
const props = defineProps({
    tablePhoto: {
        type: Array,
        default: [],
    }
});


const columns = [
    { key: "date_prise_photo", title: "Date", dataIndex: "date_prise_photo" },
    { key: "type_element", title: "Type", dataIndex: "type_element" },
    { key: "etat_vehicule", title: "Etat", dataIndex: "etat_vehicule" },

];

const handleDelete = (record) => {
    confirm_delete(() => {
        router.delete(route('remorquephoto.destroy', record.id), {
            preserveScroll: true,
        });
    });
};


const add = (id) => {
    ref_photovehicule.value.openModal(id);
    remorque_id.value = id;
}

const actions = [
    { text: "Modification", action: (record) => ref_photovehicule.value.update(record), icon: 'fa-eye', privilege: 'remorque_photo.update' },
    { text: "Supprimer", action: handleDelete, icon: 'fa-trash',  privilege: 'remorque_photo.destroy'},
];

defineExpose({ add });
</script>

<template>
    <DataTable
        :columns="columns"
        :data="tablePhoto"
        :actions="actions"
        class="main-shadow"
        :show-index="true"
        :btn_action="false"
    >
        <template #expandedRowRender="{ record }">

            <div class="p-4">
                <FilePreviewList
                    :initial-files="
                            mapExistingImages(record.liste_image)
                        "
                    :disabled="true"
                    :preview-only="true"
                />
            </div>
        </template>

    </DataTable>

    <ModalPhotoRemorque ref="ref_photovehicule"
                        :remorque_id="remorque_id"
                        :vehiculePhotos="{}"
    />

</template>

<style scoped>

</style>
