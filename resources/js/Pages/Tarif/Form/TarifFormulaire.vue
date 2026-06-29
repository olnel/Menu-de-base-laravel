<script setup>
import { ref } from "vue";
import {router, useForm} from "@inertiajs/vue3";
import FormItem from "@/Components/FormItem.vue";
import FormModal from "@/Components/FormModal.vue";
import {ColorPicker} from "vue3-colorpicker";
import "vue3-colorpicker/style.css";
import {DeleteOutlined} from "@ant-design/icons-vue";
import InputNumberWithSepartor from "@/Components/InputNumberWithSepartor.vue";

const props = defineProps({
    flash: Object
});

const form = useForm({
    id: null,
    nom_tarif: null,
    details: [{
        id: null,
        libelle: null,
        prix_ht: null,
        prix_ttc: null,
        tva: 20,
    }],
});

const open = ref(false);
const title = ref("");


const close = () => {
    open.value = false;
    form.reset();
    form.clearErrors();
};

const add = () => {
    title.value = "Nouveau Tarif";
    open.value = true;
};

const update = (rowData) => {

    router.visit(`${route('tarif.show', {tarif: rowData.id})}`, {
        preserveState: true,
        preserveScroll: true,
        only: ['flash'],
        onSuccess: (reponse) => {
            const response = reponse.props.flash.data;
            Object.keys(response).forEach(key => {
                form[key] = response[key];
            });
            title.value = "Modifier";
            open.value = true;
        },
    });
};

const submit = () => {
    form.clearErrors();
    const method = form.id ? 'put' : 'post';
    const url = form.id ? route('tarif.update', form.id) : route('tarif.store');
    form.transform(data => ({
        ...data,
        _method: method.toUpperCase()
    })).post(url, {
        onSuccess: () => close(),
        forceFormData: true
    });
};

const pushDetail = () => {
    form.details.push({
        id: null,
        libelle: null,
        prix_ht: null,
        prix_ttc: null,
        tva: 20,
    });
};

const spliceDetal = (index)=> {
    if (form.details.length > 1) {
        form.details.splice(index, 1);
    }
};

const calculMontant = ()=> {
    form.details.forEach(item => {
        if (item.prix_ht && item.tva) {
            item.prix_ttc = parseFloat(item.prix_ht) + (parseFloat(item.prix_ht) * parseFloat(item.tva) / 100);
        } else {
            item.prix_ttc = null;
        }
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
        size="md"
        :show_champ_obligatoir="false"
    >

        <div class="">
            <form-item required has-feedback label="Nom Du Tarif" :help="form.errors.nom_tarif">
                <a-input
                    v-model:value="form.nom_tarif"
                    placeholder=""
                    size="large"
                />
            </form-item>
            <table class="min-w-full ">
                <thead class="bg-primary/80 sticky top-0 z-10 text-nowrap">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider" width="300px">Libellé</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-white uppercase tracking-wider" >Prix HT</th>
<!--                    <th class="px-4 py-3 text-center text-xs font-semibold text-white uppercase tracking-wider">TVA</th>-->
<!--                    <th class="px-4 py-3 text-center text-xs font-semibold text-white uppercase tracking-wider">Prix TTC</th>-->
                    <th class="px-4 py-2 text-center text-xs font-semibold text-white uppercase tracking-wider" width="50px">
                        <a-button
                            type="primary"
                            size="middle"
                            @click="pushDetail"
                            class="bg-white text-green-600 border-white hover:bg-green-50 hover:border-green-100"
                        >
                            <font-awesome-icon icon="fa-solid fa-plus" class="!me-0"/>
                        </a-button>
                    </th>
                </tr>
                </thead>
                <tbody class="bg-white divide-y divide-green-900 border ">
                <tr v-for="(item, index) in form.details" :key="index">
                    <td class="border border-primary/25">
                        <a-input v-model:value="item.libelle" class="rounded-none border-0 focus:border-b-green-600" size="large"/>
                    </td>
                    <td class="border border-primary/25">
                        <InputNumberWithSepartor
                            v-model="item.prix_ht"
                            class="rounded-none border-0 focus:border-b-green-600"
                            size="large"
                            @input="calculMontant"
                            :min="0"
                        />
                    </td>
<!--                    <td class="border border-primary/25">
                        <InputNumberWithSepartor
                            v-model="item.tva"
                            class="rounded-none border-0 focus:border-b-green-600"
                            size="large"
                            @input="calculMontant"
                            :min="0"
                        />
                    </td>
                    <td class="border border-primary/25">
                        <InputNumberWithSepartor
                            v-model="item.prix_ttc"
                            class="rounded-none border-0 focus:border-b-green-600"
                            size="large"
                            :min="0"
                            @input="calculMontant"
                        />
                    </td>-->
                    <td class="border border-primary/25 !text-center">
                        <a-button
                            danger
                            type="text"
                            size="small"
                            @click="spliceDetal(index)"
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
.vc-color-wrap{
    height: 40px !important;
}
</style>
