<template>
    <a-input
        v-model:value="displayValue"
        @focus="onFocus"
        @blur="onBlur"
        @keypress="onKeyPress"
        class="w-full text-right [&>input]:text-right"
    />
</template>

<script setup>
import {ref, watch, computed} from 'vue';

const props = defineProps({
    modelValue: Number
});

const emit = defineEmits(['update:modelValue']);

const isFocused = ref(false);

const displayValue = computed({
    get() {
        if (isFocused.value) {
            return props.modelValue?.toString()?.replace(/\s/g, '') || '';
        }
        return props.modelValue ? formatValue(props.modelValue) : '';
    },
    set(value) {
        if (isFocused.value) {
            // Supprime tout ce qui n'est pas chiffre, signe négatif ou point décimal
            const cleanedValue = value.replace(/[^\d.-]/g, '');
            const num = Number(cleanedValue);
            emit('update:modelValue', isNaN(num) ? null : num);
        }
    }
});

const formatValue = (value) => {
    return value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' ');
};

const onFocus = () => {
    isFocused.value = true;
};

const onBlur = () => {
    isFocused.value = false;
};

// Empêche la saisie de caractères non-numériques
const onKeyPress = (event) => {
    const charCode = event.which ? event.which : event.keyCode;
    // Autorise: chiffres 0-9, point (.), touche retour, suppression, tabulation
    if (charCode !== 46 && charCode > 31 && (charCode < 48 || charCode > 57)) {
        event.preventDefault();
    }
};
</script>
