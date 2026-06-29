<template>
    <div>
        <a-input
            placeholder="Rechercher un nom, prenom, ..."
            :value="modelValue"
            @input="$emit('update:modelValue', $event.target.value)"
            allow-clear
            class="w-full"
        >
        </a-input>
    </div>
</template>

<script setup>
import { library } from "@fortawesome/fontawesome-svg-core";
import { faSearch } from "@fortawesome/free-solid-svg-icons";
library.add(faSearch);

defineProps({
    modelValue: {
        type: String,
        default: "",
    },
});

defineEmits(["update:modelValue"]);
</script>

<style scoped></style>
