<template>
    <a-row :gutter="[8, 8]" class="items-center">
        <a-col :span="22">
            <a-input
                v-model:value="item.designation"
                placeholder="Désignation"
                size="large"
                :status="status.designation ? 'error' : ''"
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
