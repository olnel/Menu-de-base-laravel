<script setup>
import { ref } from "vue";
import {router, useForm} from "@inertiajs/vue3";
import { UploadOutlined, CloseOutlined, PlusOutlined, DeleteOutlined } from '@ant-design/icons-vue';
import FormItem from "@/Components/FormItem.vue";
import FormModal from "@/Components/FormModal.vue";

const props = defineProps({
    flash: Object
});

const form = useForm({
    id: null,
    type_model: null,
    details: [
        { id: '', param_element_id: '', emplacement: '', libelle: '', reference: '', is_pneu: false }
    ]
});



const open = ref(false);
const title = ref("");

const close = () => {
    open.value = false;
    form.reset();
    form.clearErrors();
};

const pushElement = () => {
    form.details.push({
        id: '', param_element_id: '', emplacement: '', libelle: '', reference: '', is_pneu: false
    });
};

const spliceElement = (index) => {
    form.details.splice(index, 1);
};

const add = () => {
    title.value = "Nouvel Élément";
    open.value = true;
};

const update = (rowData) => {

    router.visit(`${route('paramelement.show', {paramelement: rowData.id})}`, {
        preserveState: true,
        preserveScroll: true,
        only: ['flash'],
        onSuccess: () => {
            console.log(props.flash);
            Object.keys(props.flash.data).forEach(key => {
                console.log(key, props.flash.data[key]);
                form[key] = props.flash.data[key];
            });
            title.value = "Modifier";
            open.value = true;
        },
    });
};

const submit = () => {
    form.clearErrors();
    const method = form.id ? 'put' : 'post';
    const url = form.id ? route('paramelement.update', form.id) : route('paramelement.store');

    form.transform(data => ({
        ...data,
        _method: method.toUpperCase()
    })).post(url, {
        onSuccess: () => close(),
        forceFormData: true
    });
};

defineExpose({add, update, close});
</script>

<template>
    <FormModal
        v-model:open="open"
        :loading="form.processing"
        @close="close"
        @submit="submit"
        :titre="title"
        size="lg"
        :show_champ_obligatoir="false"
    >

        <div class="space-y-4">
            <form-item required has-feedback label="Nom du Modèle" :help="form.errors.type_model">
                <a-input
                    v-model:value="form.type_model"
                    placeholder=""
                    size="large"
                />
            </form-item>

            <table class="min-w-full  ">
                <thead class="bg-primary text-white">
                <tr>
                    <th class="">Emplacement</th>
                    <th>Libelldé</th>
                    <th>Référence</th>
                    <th>Pneu</th>
                    <th class="!bg-transparent">
                        <a-button type="default" class="!rounded-none" size="small" @click="pushElement" icon>
                            <PlusOutlined/>
                        </a-button>
                    </th>
                </tr>
                </thead>
                <tbody class="bg-white divide-y divide-green-900 border ">
                <tr v-for="(item, index) in form.details" :key="index">
                    <td class="border border-primary/25">
                        <a-input v-model:value="item.emplacement" class="rounded-none border-0 focus:border-b-green-600" size="large"/>
                    </td>
                    <td class="border border-primary/25">
                        <a-input v-model:value="item.libelle" size="large" class="rounded-none border-0"/>
                    </td>
                    <td class="border border-primary/25">
                        <a-input v-model:value="item.reference" size="large" class="rounded-none border-0"/>
                    </td>
                    <td class="text-center">
                        <a-checkbox v-model:checked="item.is_pneu"></a-checkbox>

                    </td>
                    <td class="border border-primary/25 text-center">
                        <a-button
                            danger
                            type="text"
                            size="small"
                            @click="spliceElement(index)"
                            v-if="form.details.length > 1"
                        >
                            <DeleteOutlined/>
                        </a-button>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </FormModal>
</template>

<style scoped>
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
</style>
