<template>
    <a-row :gutter="[8, 8]" class="items-center">
        <a-col :span="14">
            <a-input
                v-model:value="item.libelle"
                placeholder="Libellé"
                size="large"
                :status="status.libelle ? 'error' : ''"
            />
        </a-col>
        <a-col :span="8">
            <InputNumberWithSepartor
                v-model:modelValue="item.montant"
                :min="0"
                size="large"
                placeholder="Montant"
                class="w-full"
                :status="status.montant ? 'error' : ''"
            />
        </a-col>
        <a-col :span="2">
            <a-button
                type="text"
                size="large"
                danger
                @click="emit('remove')"
                class="w-full flex items-center justify-center cursor-pointer"
            >
                <DeleteOutlined class="text-red-500 text-lg font-sv" />
            </a-button>
        </a-col>
    </a-row>
</template>

<script setup>
import InputNumberWithSepartor from "@/Components/InputNumberWithSepartor.vue";
import { DeleteOutlined } from "@ant-design/icons-vue";

const props = defineProps({
    item: {
        type: Object,
        required: true,
    },
    status: {
        type: Object,
        default: () => ({}),
    },
});

const emit = defineEmits(["remove"]);
</script>
