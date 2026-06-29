<script setup>
import { ref, computed } from "vue";
import { useForm } from "@inertiajs/vue3";
import FormItem from "@/Components/FormItem.vue";
import FormModal from "@/Components/FormModal.vue";

const props = defineProps({
    data: {
        type: Object,
        default: () => {}
    }
})

const form = useForm({
    id: null,
    title: null,
    description: null,
});


const open = ref(false);
const title = ref("");

const close = () => {
    open.value = false;
    form.reset();
    form.clearErrors();
};

const add = () => {
    title.value = "Nouveau Feature";
    open.value = true;
};

const update = (data) => {
    title.value = "Modification";
    open.value = true;

    form.id = data.id;
    form.title = data.title;
    form.description = data.description;
};

const submit = () => {
    form.clearErrors();

    if (form.id) {
        form.put(route('pointdevente.update', form.id), {
            onSuccess: () => close()
        });
    } else {
        form.post(route("pointdevente.store"), {
            onSuccess: () => close()
        });
    }
};

defineExpose({
    add,
    update,
    close
});
</script>

<template>
    <FormModal
        v-model:open="open"
        :title="title"
        :loading="form.processing"
        @close="close"
        @submit="submit"
        :show_champ_obligatoir="false"
    >
        <form-item required has-feedback label="Titre" :help="form.errors.title">

            <a-input
                class="w-full"
                v-model:value="form.title"
                size="large"
            />

        </form-item>

        <form-item required has-feedback label="Description" :help="form.errors.description">
            <a-textarea
                class="w-full"
                v-model:value="form.description"
                size="large"
                :auto-size="{ minRows: 5, maxRows: 5 }"
            />

        </form-item>
    </FormModal>
</template>

<style scoped>

</style>
