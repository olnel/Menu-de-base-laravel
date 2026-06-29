<script setup>
import { computed, ref, watch } from "vue";

const props = defineProps({
    options: {
        type: Array,
        required: true,
        default: () => [],
    },
    placeholder: {
        type: String,
        default: "Sélectionnez une option",
    },
    modelValue: {
        type: [String, Number, Object],
        default: null,
    },
    fieldConfig: {
        type: Object,
        default: () => ({
            label: "label",
            value: "value",
            search: "label",
        }),
    },
    allowClear: {
        type: Boolean,
        default: true,
    },
    disabled: {
        type: Boolean,
        default: false,
    },
    size: {
        type: String,
        default: "large",
        validator: (value) => ["small", "middle", "large"].includes(value),
    },
    filterOption: {
        type: Boolean,
        default: true,
    },
    allowAdd: {
        type: Boolean,
        default: true,
    },
    /** ✅ Nouvelle prop : contrôle l’affichage de la bordure */
    border: {
        type: Boolean,
        default: true,
    },
});

const emit = defineEmits([
    "update:modelValue",
    "search",
    "select",
    "change",
    "add-new-option",
]);

const displayValue = ref("");
const currentSearchText = ref("");

const normalizedOptions = computed(() =>
    props.options.map((option) => {
        if (typeof option === "object" && option !== null) {
            return {
                label: option[props.fieldConfig.label] || String(option),
                value: option[props.fieldConfig.label] || String(option),
                originalValue: option[props.fieldConfig.value] || String(option),
            };
        }
        return { label: String(option), value: String(option), originalValue: option };
    })
);

const filteredOptions = ref([...normalizedOptions.value]);

const optionExists = (searchText) =>
    normalizedOptions.value.some(
        (opt) => opt.label.toLowerCase() === searchText.toLowerCase()
    );

const computedOptions = computed(() => {
    let options = [...filteredOptions.value];
    if (
        props.allowAdd &&
        currentSearchText.value.trim() &&
        !optionExists(currentSearchText.value.trim())
    ) {
        options.unshift({
            label: currentSearchText.value.trim(),
            value: currentSearchText.value.trim(),
            originalValue: currentSearchText.value.trim(),
            isNewOption: true,
        });
    }
    return options;
});

watch(
    () => props.modelValue,
    (newValue) => {
        if (newValue) {
            const selected = normalizedOptions.value.find(
                (opt) => opt.originalValue === newValue
            );
            displayValue.value = selected ? selected.label : String(newValue);
        } else {
            displayValue.value = "";
        }
    },
    { immediate: true }
);

const handleSearch = (value) => {
    displayValue.value = value;
    currentSearchText.value = value;
    if (!props.filterOption) {
        emit("search", value);
        return;
    }
    filteredOptions.value =
        value.trim() === ""
            ? [...normalizedOptions.value]
            : normalizedOptions.value.filter((opt) =>
                String(opt.label).toLowerCase().includes(value.toLowerCase())
            );
    emit("search", value);
};

const handleSelect = (selectedValue, option) => {
    if (option.isNewOption) {
        const newOptionValue = currentSearchText.value.trim();
        const newOption = {
            [props.fieldConfig.label]: newOptionValue,
            [props.fieldConfig.value]: newOptionValue,
        };
        emit("add-new-option", newOption);
        emit("update:modelValue", newOptionValue);
        displayValue.value = newOptionValue;
        emit("select", { value: newOptionValue, option: newOption, isNew: true });
        emit("change", newOptionValue);
    } else {
        const selectedOption = normalizedOptions.value.find(
            (opt) => opt.value === selectedValue
        );
        if (selectedOption) {
            emit("update:modelValue", selectedOption.originalValue);
            displayValue.value = selectedOption.label;
            emit("select", {
                value: selectedOption.originalValue,
                option: selectedOption,
                isNew: false,
            });
            emit("change", selectedOption.originalValue);
        }
    }
    currentSearchText.value = "";
};

const handleClear = () => {
    displayValue.value = "";
    currentSearchText.value = "";
    emit("update:modelValue", null);
    emit("change", null);
};

watch(
    () => props.options,
    () => {
        filteredOptions.value = [...normalizedOptions.value];
    },
    { deep: true }
);

const handleKeyDown = (event) => {
    if (
        event.key === "Enter" &&
        props.allowAdd &&
        currentSearchText.value.trim() &&
        !optionExists(currentSearchText.value.trim())
    ) {
        handleSelect(currentSearchText.value.trim(), { isNewOption: true });
    }
};
</script>

<template>
    <a-auto-complete
        v-model:value="displayValue"
        :options="computedOptions"
        :placeholder="placeholder"
        :allow-clear="allowClear"
        :disabled="disabled"
        :size="size"
        :filter-option="false"
        @search="handleSearch"
        @select="handleSelect"
        @clear="handleClear"
        @keydown="handleKeyDown"
        :class="border ? 'mmmmmmmmm' : 'select-autocomplete xxxxxxx'"
    >
        <template #option="{ label }">
            <div>{{ label }}</div>
        </template>
    </a-auto-complete>
</template>

<style scoped>
/* Supprime la bordure interne quand border = false */
.ant-select-selector {
    border: none !important;
}

</style>
