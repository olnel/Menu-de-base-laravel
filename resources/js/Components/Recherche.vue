<script setup>
import ButtonIcon from "@/Components/ButtonIcon.vue";
import {ref} from "vue";

const value = defineModel("value")
const emit = defineEmits(["onSearch"])

const show = ref(false)
const handleShow = () => {
    show.value = true
}

const handleHide = () => {
    show.value = false
    value.value = "";
    emit("onSearch")
}

</script>

<template>
    <a-input
        @pressEnter="emit('onSearch')"
        v-model:value="value"
        placeholder="Rechercher"
        size="large"
        :bordered="false"
        class="h-full w-full"
        v-if="show"
    >
        <template #prefix>
            <font-awesome-icon class="mx-3 border-0" icon="fa-solid fa-search"/>
        </template>
        <template #suffix>
            <font-awesome-icon @click="handleHide" class="mx-1.5 border-0 cursor-pointer main-hover rounded p-1" icon="fa-solid fa-xmark"/>
        </template>
    </a-input>

    <ButtonIcon title="Recherche" class="!ring-0" icon="fa-search" v-else @click="handleShow"/>
</template>
