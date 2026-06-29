<script setup>
import { ref, computed } from "vue";
import { useForm } from "@inertiajs/vue3";
import FormItem from "@/Components/FormItem.vue";
import FormModal from "@/Components/FormModal.vue";

const props = defineProps({

});

const form = useForm({
    id: null,
    question: null,
    reponse: null,
});


const open = ref(false);
const title = ref("");

const close = () => {
    open.value = false;
    form.reset();
    form.clearErrors();
};

const add = () => {
    title.value = "Nouveau FAQ";
    open.value = true;
};

const update = (data) => {
    title.value = "Modification";
    open.value = true;

    form.id = data.id;
    form.question = data.question;
    form.reponse = data.reponse;
};

const submit = () => {
    form.clearErrors();

    if (form.id) {
        form.put(route('faq.update', form.id), {
            onSuccess: () => close()
        });
    } else {
        form.post(route("faq.store"), {
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
        <form-item required has-feedback label="Question" :help="form.errors.question">
            <a-input
                class="w-full"
                v-model:value="form.question"
                size="large"
            />

        </form-item>

        <form-item has-feedback label="Réponse" :help="form.errors.reponse">
            <a-textarea
                class="w-full"
                v-model:value="form.reponse"
                size="large"
                :auto-size="{ minRows: 5, maxRows: 5 }"
            />
        </form-item>
    </FormModal>
</template>

<style scoped>

</style>
