<template>
    <FormModal
        v-model:open="isModalVisible"
        titre="Ajustement de Solde des Cartes Carburant"
        :loading="form.processing"
        @close="handleCancel"
        @submit="handleSubmit"
        size="sm"
        :show_champ_obligatoir="false"
    >
        <div class="p-4">
            <form-item
                label="Montant de l'ajustement (Ariary)"
                :help="form.errors.montant"
                required
            >
                <InputNumberWithSepartor
                    v-model="form.montant"
                    size="large"
                    placeholder="Saisir le montant"
                    :min="1"
                    class="modern-input w-full"
                />
            </form-item>
            <form-item label="Motif" :help="form.errors.motif">
                <a-textarea
                    v-model:value="form.motif"
                    :auto-size="{ minRows: 4, maxRows: 4 }"
                >
                </a-textarea>
            </form-item>

            <p class="text-sm text-gray-500 mt-2">
                {{ cardsToAdjustCount }} carte(s) sélectionnée(s).
            </p>
        </div>
    </FormModal>
</template>

<script setup>
import FormItem from "@/Components/FormItem.vue";
import FormModal from "@/Components/FormModal.vue";
import InputNumberWithSepartor from "@/Components/InputNumberWithSepartor.vue";
import { useForm } from "@inertiajs/vue3";
import { ref } from "vue";
const props = defineProps({
    carburantCardIds: {
        type: Array,
        default: () => [],
    },
});
const isModalVisible = ref(false);
const cardsToAdjustCount = ref(0);
const form = useForm({
    card_ids: [],
    montant: null,
    motif: "",
});
const add = (cardIds) => {
    form.card_ids = cardIds;
    cardsToAdjustCount.value = cardIds.length;
    isModalVisible.value = true;
};
const handleCancel = () => {
    isModalVisible.value = false;
    form.reset();
    form.clearErrors();
};

const emit = defineEmits(["adjustmentSuccess"]);
const handleSubmit = () => {
    form.post(route("carburant_cards.RechargeCard"), {
        onSuccess: () => {
            handleCancel();
            emit("adjustmentSuccess");
        },
    });
};

defineExpose({
    add,
});
</script>

<style scoped>
/* Ajoutez des styles spécifiques si nécessaire */
</style>
