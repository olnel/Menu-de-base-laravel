<script setup>

import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import DataTable from "@/Components/DataTable.vue";
import {computed, ref} from "vue";

import {confirm_delete} from "../../../../Utils/confirmation_modal.js";
import {router} from "@inertiajs/vue3";
import ButtonIcon from "@/Components/ButtonIcon.vue";
import EvenementForm from "@/Pages/Accueil/Evenement/EvenementForm.vue";
import AproposForm from "@/Pages/Accueil/Apropos/AproposForm.vue";
import FaqForm from "@/Pages/Accueil/Faq/FaqForm.vue";


const props = defineProps({
    data: {
        type: Object,
        default: () => {
        }
    },
    filters: {
        type: Object,
        default: () => {
        }
    },

})

const formModal = ref()
const title = computed(() => `Messages (${props.data?.total ?? 0})`)

const columns = [
    {title: "Nom", dataIndex: "nom", key: "nom", },
    {title: "Email", dataIndex: "email", key: "email", },
    {title: "Téléphone", dataIndex: "telephone", key: "telephone", },
    {title: "Pays", dataIndex: "pays", key: "pays", },
    {title: "Objets", dataIndex: "objet", key: "objet", },
    {title: "", dataIndex: "message", key: "message", },
]

const handleDelete = (record) => {
    confirm_delete(() => {
        router.delete(route('message.destroy', record.id), {
            preserveScroll: true,
        })
    });
}

const actions = [
    {text: "Modifier", action: (record) => formModal.value.update(record), icon: 'fa-pen-to-square'},
    {
        text: "Supprimer",
        action: handleDelete,
        icon: 'fa-trash',
        disabled: (record) => record.is_you || props.data.total < 1
    },
]

</script>

<template>
    <authenticated-layout :title="title" selected-menu="message">
<!--        v-if="data.data.length < 0"-->
<!--        <template #actions >-->
<!--            <ButtonIcon @click="() => formModal.add()" type="primary" text="Nouveau" icon="fa-plus"/>-->
<!--        </template>-->

        <data-table
            :columns="columns"
            :data="data"
            class="main-shadow mt-5"
            :show-index="true">

            <template #default="{ column, record, text }">
                <template v-if="column.key === 'message'">
                    <a-dropdown placement="bottomRight" :trigger="['hover', 'click']">
                        <div class="flex items-center max-w-[200px]">
                            <font-awesome-icon
                                icon="fa-solid fa-comment"
                                class="me-2 text-lg text-gray-500 cursor-pointer"
                            />
                        </div>

                        <template #overlay>
                            <a-menu class="max-w-[400px] max-h-[300px] overflow-auto">
                                <a-menu-item class="p-2 whitespace-normal">
                                    <span class="whitespace-pre-wrap break-words m-0 font-sans">{{ text }}</span>
                                </a-menu-item>
                            </a-menu>
                        </template>
                    </a-dropdown>
                </template>
            </template>

        </data-table>

    </authenticated-layout>

</template>

<style scoped>

</style>
