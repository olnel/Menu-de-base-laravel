<script setup>
import { ref } from "vue";
import { router, useForm } from "@inertiajs/vue3";
import FormItem from "@/Components/FormItem.vue";
import FormModal from "@/Components/FormModal.vue";
import ButtonIcon from "@/Components/ButtonIcon.vue";

const props = defineProps({
    documentableTypes: Array,
    documentTypes: Array,
});

const form = useForm({
    id: null,
    documentable_type_id: null,
    documents: [],
});

const open = ref(false);
const title = ref("");

const close = () => {
    open.value = false;
    form.reset();
    form.clearErrors();
};

const addRow = () => {
    form.documents.push({
        id: null,
        document_type_name: '',
        obligatoire: true,
        expiration_required: false,
        alert_days: 15,
        ordre: form.documents.length,
        actif: true,
    });
};

const removeRow = (index) => {
    form.documents.splice(index, 1);
    updateOrders();
};

const updateOrders = () => {
    form.documents.forEach((doc, idx) => {
        doc.ordre = idx;
    });
};

const onDragStart = (event, index) => {
    event.dataTransfer.setData('index', index);
    event.dataTransfer.effectAllowed = 'move';
};

const onDrop = (event, index) => {
    const fromIndex = parseInt(event.dataTransfer.getData('index'));
    if (fromIndex === index) return;
    
    const movedItem = form.documents.splice(fromIndex, 1)[0];
    form.documents.splice(index, 0, movedItem);
    updateOrders();
};

const add = (typeId = null) => {
    form.reset();
    form.documents = [];
    addRow();
    if (typeId) form.documentable_type_id = typeId;
    title.value = "Nouveau Modèle de Document";
    open.value = true;
};

const update = (rowData) => {
    router.visit(`${route('dynamic.documents.config.show', {config_model: rowData.id})}`, {
        preserveState: true,
        preserveScroll: true,
        only: ['flash'],
        onSuccess: (reponse) => {
            const response = reponse.props.flash?.data;
            
            if (!response) return;

            form.documentable_type_id = response.documentable_type_id;
            form.id = response.documentable_type_id; // Using type_id as reference for sync
            form.documents = response.documents.map(doc => ({
                ...doc,
                obligatoire: !!doc.obligatoire,
                expiration_required: !!doc.expiration_required,
                actif: !!doc.actif,
            }));
            
            title.value = "Modifier les Modèles";
            open.value = true;
        },
    });
};

const submit = () => {
    form.clearErrors();
    // We'll use POST for both cases (sync approach) or PUT if we have a type_id
    const url = route('dynamic.documents.config.store');
    
    form.post(url, {
        onSuccess: () => close(),
    });
};

defineExpose({ add, update, close });
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
        <div class="space-y-6">
            <form-item label="Type d'entité" required :help="form.errors.documentable_type_id">
                <a-select 
                    v-model:value="form.documentable_type_id" 
                    placeholder="Sélectionner une entité" 
                    size="large" 
                    class="w-full"
                    :disabled="form.documents.some(d => d.id)"
                >
                    <a-select-option v-for="type in documentableTypes" :key="type.id" :value="type.id">
                        {{ type.nom }}
                    </a-select-option>
                </a-select>
            </form-item>

            <div class="border rounded-lg overflow-hidden">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="px-4 py-2 w-10"></th>
                            <th class="px-4 py-2">Type de Document</th>
                            <th class="px-4 py-2 text-center">Obligatoire</th>
                            <th class="px-4 py-2 text-center">Exp. Requise</th>
                            <th class="px-4 py-2 text-center">Multiple</th>
                            <th class="px-4 py-2 text-center">Jours d'alerte</th>
                            <th class="px-4 py-2 text-center">Statut</th>
                            <th class="px-4 py-2 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        <tr v-for="(doc, index) in form.documents" 
                            :key="index"
                            draggable="true"
                            @dragstart="onDragStart($event, index)"
                            @dragover.prevent
                            @drop="onDrop($event, index)"
                            class="hover:bg-blue-50/50 transition-colors"
                        >
                            <td class="px-4 py-2 text-gray-400 cursor-move">
                                <font-awesome-icon icon="fa-solid fa-grip-vertical" />
                            </td>
                            <td class="px-4 py-2">
                                <a-input v-model:value="doc.document_type_name" placeholder="Ex: Carte Grise" size="large" />
                            </td>
                            <td class="px-4 py-2 text-center">
                                <a-switch v-model:checked="doc.obligatoire" />
                            </td>
                            <td class="px-4 py-2 text-center">
                                <a-switch v-model:checked="doc.expiration_required" />
                            </td>
                            <td class="px-4 py-2 text-center">
                                <a-switch v-model:checked="doc.multiple_allowed" />
                            </td>
                            <td class="px-4 py-2 text-center">
                                <a-input-number v-model:value="doc.alert_days" :min="0" class="w-24" size="large" />
                            </td>
                            <td class="px-4 py-2 text-center">
                                <a-switch v-model:checked="doc.actif" />
                            </td>
                            <td class="px-4 py-2 text-right">
                                <a-button type="link" danger @click="removeRow(index)" :disabled="form.documents.length === 1">
                                    <font-awesome-icon icon="fa-solid fa-xmark" />
                                </a-button>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div class="p-4 bg-gray-50 border-t">
                    <a-button type="dashed" block size="large" @click="addRow">
                        <font-awesome-icon icon="fa-solid fa-plus" class="mr-2" /> Ajouter un document
                    </a-button>
                </div>
            </div>
        </div>
    </FormModal>
</template>

<style scoped>
.cursor-move {
    cursor: move;
}
</style>
