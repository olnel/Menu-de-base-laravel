<template>
    <FormModal
        v-model:open="isModalVisible"
        :titre="
            isEditMode ? 'Modifier Carte Carburant' : 'Ajouter Carte Carburant'
        "
        :loading="form.processing"
        @close="handleCancel"
        @submit="handleSubmit"
        size="sm"
        :show_champ_obligatoir="false"
    >
        <div class="p-4 space-y-6">
            <div
                class="rounded-xl border border-gray-200 bg-white p-4 lg:p-6 shadow-sm"
            >
                <a-row :gutter="[24, 24]">
                    <!-- Numéro de Carte -->
                    <a-col :xs="24">
                        <form-item
                            label="Numéro de Carte"
                            required
                            :help="form.errors.numero_carte"
                        >
                            <a-input
                                v-model:value="form.numero_carte"
                                size="large"
                                class="modern-input"
                            />
                        </form-item>
                    </a-col>

                    <!-- Plafond Mensuel -->
                    <a-col :xs="24">
                        <form-item
                            label="Plafond Mensuel"
                            required
                            :help="form.errors.plafond_mensuel"
                        >
                            <InputNumberWithSepartor
                                v-model="form.plafond_mensuel"
                                size="large"
                                :min="0"
                                class="modern-input w-full"
                            />
                        </form-item>
                    </a-col>

                    <!-- Statut -->
                    <a-col  class="flex items-center">
                        <form-item
                            label="Statut"
                            required
                            :help="form.errors.active"
                            class="w-full"
                        >
                            <a-switch
                                v-model:checked="form.active"
                                checked-children="Activé"
                                un-checked-children="Désactivé"
                                size="large"
                                class="modern-switch"
                            />
                        </form-item>
                    </a-col>
                </a-row>
            </div>
        </div>
    </FormModal>
</template>

<script setup>
import FormItem from "@/Components/FormItem.vue";
import FormModal from "@/Components/FormModal.vue";
import InputNumberWithSepartor from "@/Components/InputNumberWithSepartor.vue";
import { router, useForm } from "@inertiajs/vue3";
import { ref } from "vue";
const form = useForm({
    id: null,
    numero_carte: "",
    plafond_mensuel: null,
    active: true,
});

const isModalVisible = ref(false);
const isEditMode = ref(false);

const add = () => {
    isEditMode.value = false;
    form.reset();
    form.clearErrors();
    isModalVisible.value = true;
};

const update = (record) => {
    isEditMode.value = true;
    form.clearErrors();
    form.id = record.id;
    form.numero_carte = record.numero_carte;
    form.plafond_mensuel = record.plafond_mensuel;
    form.active = !!record.active;
    isModalVisible.value = true;
};

const handleSubmit = () => {
    if (isEditMode.value) {
        console.log("ID envoyé pour update :", form.id);
        router.put(route("carburant_cards.update", form.id), form.data(), {
            onSuccess: () => {
                isModalVisible.value = false;
            },
            onError: (errors) => {
                form.errors = errors;
            },
        });
    } else {
        router.post(route("carburant_cards.store"), form.data(), {
            onSuccess: () => {
                isModalVisible.value = false;
            },
            onError: (errors) => {
                form.errors = errors;
            },
        });
    }
};

const handleCancel = () => {
    isModalVisible.value = false;
    form.reset();
    form.clearErrors();
};
defineExpose({ add, update });
</script>

<style scoped>
.modern-switch.ant-switch-checked {
    background-color: #52c41a; /* Ant Design success green */
}
.modern-switch.ant-switch:not(.ant-switch-checked) {
    background-color: #bfbfbf; /* A light grey for unchecked state */
}
</style>
