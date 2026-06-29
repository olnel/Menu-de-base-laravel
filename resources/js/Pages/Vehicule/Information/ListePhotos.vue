<script setup>
    import DataTable from "@/Components/DataTable.vue";
    import {confirm_delete} from "../../../../Utils/confirmation_modal.js";
    import {router} from "@inertiajs/vue3";
    import {ref} from "vue";
    import ModalPhotoVehicule from "@/Pages/Vehicule/Information/ModalPhotoVehicule.vue";
    import FilePreviewList from "@/Components/FilePreviewList.vue";
    import {mapExistingImages} from "../../../../Utils/fileUtil.js";

    const vehicule_id = ref(null);
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
        { key: "etat_vehicule", title: "Etat Véhicule", dataIndex: "etat_vehicule" },

    ];

    const handleDelete = (record) => {
        confirm_delete(() => {
            router.delete(route('vehiculephoto.destroy', record.id), {
                preserveScroll: true,
            });
        });
    };


    const add = (id) => {
        ref_photovehicule.value.openModal(id);
        vehicule_id.value = id;
    }

    const actions = [
        { text: "Modification", action: (record) => ref_photovehicule.value.update(record), icon: 'fa-eye', privilege: 'vehicule.update_vehicule_photo' },
        { text: "Supprimer", action: handleDelete, icon: 'fa-trash',  privilege: 'vehicule.destroy_vehicule_photo'},
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

<!--    <FormulairePhotoVehicule ref="ref_photovehicule" />-->

    <ModalPhotoVehicule
        ref="ref_photovehicule"
        :vehicule_id="vehicule_id"
        :vehiculePhotos="{}"
    />
</template>

<style scoped>

</style>
