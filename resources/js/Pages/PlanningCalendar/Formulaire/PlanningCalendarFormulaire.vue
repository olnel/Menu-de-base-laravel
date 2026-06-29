<script setup>
import { ref } from "vue";
import {router, useForm} from "@inertiajs/vue3";
import FormItem from "@/Components/FormItem.vue";
import FormModal from "@/Components/FormModal.vue";


const props = defineProps({
    flash: Object,
    vehiculeListes: {
        type: Array,
        default: () => []
    },
    libelleMaintenance: {
        type: Array,
        default: () => []
    }
});

const localLibelleMaintenance = ref([...props.libelleMaintenance]);
const filteredOptions = ref([...props.libelleMaintenance]);

const form = useForm({
    id: null,
    date_maintenance: null,
    vehicule_id: null,
    libelle: null,
    notification: null,
    background: null,
    text_color: null,
});

const open = ref(false);
const title = ref("");
const dateFormat = 'DD/MM/YYYY';

const close = () => {
    open.value = false;
    form.reset();
    form.clearErrors();
};

const add = () => {
    title.value = "Nouveau Planning";
    open.value = true;
};

const update = (rowData) => {

    router.visit(`${route('planning_calendar.show', {planning_calendar: rowData.id})}`, {
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
    const url = form.id ? route('planning_calendar.update', form.id) : route('planning_calendar.store');

    form.transform(data => ({
        ...data,
        _method: method.toUpperCase()
    })).post(url, {
        onSuccess: () => close(),
        forceFormData: true
    });
};

const filtreLibelleMaintenance = (value) => {
    const results = localLibelleMaintenance.value
        .filter(item => item.libelle.toLowerCase().includes(value.toLowerCase()))
        .map(item => ({
            id: item.libelle,
            libelle: item.libelle
        }));

    filteredOptions.value = results;

    if (results.length === 0) {
        form.background = null;
        form.text_color = null;
        form.notification = null;
    }
};


const onSelectLibelle = (value) => {
    const selected = localLibelleMaintenance.value.find(item => item.libelle === value);
    if (selected) {
        form.background = selected.background || null;
        form.text_color = selected.text_color || null;
        form.notification = selected.notification || null;
    }
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
        size="sm"
        :show_champ_obligatoir="false"
    >

        <div class="">
            <form-item required has-feedback label="Date Maintenance" :help="form.errors.date_maintenance">
                <a-date-picker
                    v-model:value="form.date_maintenance"
                    :format="dateFormat"
                    size="large"
                    class="w-full"
                    :value-format="'YYYY-MM-DD'"
                />
            </form-item>

            <form-item required has-feedback label="N° Immatriculation" :help="form.errors.vehicule_id">
                <a-select
                    :options="vehiculeListes"
                    v-model:value="form.vehicule_id"
                    size="large"
                />
            </form-item>
            <form-item required has-feedback label="Libellé" :help="form.errors.libelle">

                <a-auto-complete
                    v-model:value="form.libelle"
                    :options="filteredOptions"
                    :filter-option="false"
                    :field-names="{label: 'libelle', value: 'libelle' }"
                    placeholder="Entrez un libellé"
                    @search="filtreLibelleMaintenance"
                    @select="onSelectLibelle"
                    size="large"
                />
            </form-item>

<!--            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">-->
<!--                &lt;!&ndash; Couleur de fond &ndash;&gt;-->
<!--                <div>-->
<!--                    <form-item-->
<!--                        required-->
<!--                        has-feedback-->
<!--                        label="Couleur de fond"-->
<!--                        :help="form.errors.background"-->
<!--                    >-->
<!--                        <a-input v-model:value="form.background" format="rgb" type="color"  />-->
<!--                    </form-item>-->
<!--                </div>-->

<!--                &lt;!&ndash; Couleur texte &ndash;&gt;-->
<!--                <div>-->
<!--                    <form-item-->
<!--                        required-->
<!--                        has-feedback-->
<!--                        label="Couleur du texte"-->
<!--                        :help="form.errors.text_color"-->
<!--                    >-->
<!--                        <a-input v-model:value="form.text_color" type="color"  />-->
<!--                    </form-item>-->
<!--                </div>-->
<!--            </div>-->

            <form-item
                has-feedback
                label="Jours de prévenance"
                help="Rempli automatiquement selon le libellé sélectionné"
            >
                <a-input-number
                    v-model:value="form.notification"
                    :min="0"
                    addon-after="jours"
                    class="w-full"
                    size="large"
                    :disabled="!form.libelle"
                />
            </form-item>
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
