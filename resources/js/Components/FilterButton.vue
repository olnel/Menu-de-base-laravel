<script setup>

import ButtonIcon from "@/Components/ButtonIcon.vue";
import {ref} from "vue";

const open = ref(false);
const emit = defineEmits(["onOk", "onCancel"])

const handleOk = () => {
    open.value = false;
    emit('onOk');
}

const handleCancel = () => {
    open.value = false;
    emit('onCancel');
}

</script>

<template>
    <a-dropdown v-model:open="open" placement="bottomRight" :trigger="['click']">
        <ButtonIcon text="Filtre" icon="fa-filter"/>

        <template #overlay>
            <a-menu class="min-w-72 !px-0">
                <perfect-scrollbar class="p-4 max-h-96 w-full" :options="{suppressScrollX: true}">
                    <slot />
                </perfect-scrollbar>

                <hr class="mb-3">
                <div class="flex justify-between px-4 pt-0 pb-3">
                    <a-button
                        type="text"
                        class="!py-1 !px-2 uppercase !rounded"
                        @click="handleCancel"
                    >
                        Annuler
                    </a-button>

                    <a-button
                        type="primary"
                        class="!py-1 !px-2 uppercase !rounded"
                        @click="handleOk"
                    >
                        Appliquer
                    </a-button>
                </div>
            </a-menu>
        </template>
    </a-dropdown>
</template>
