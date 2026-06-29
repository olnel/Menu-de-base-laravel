<script setup>
import { ref, computed } from "vue";
import { useForm } from "@inertiajs/vue3";
import FormItem from "@/Components/FormItem.vue";
import FormModal from "@/Components/FormModal.vue";

const props = defineProps({

});

const form = useForm({
    id: null,
    titre: null,
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
    form.titre = data.titre;
    form.description = data.description;
};

const submit = () => {
    form.clearErrors();

    if (form.id) {
        form.put(route('feature.update', form.id), {
            onSuccess: () => close()
        });
    } else {
        form.post(route("feature.store"), {
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
        <form-item required has-feedback label="Titre" :help="form.errors.titre">

                <a-input
                    class="w-full"
                    v-model:value="form.titre"
                    size="large"
                />

        </form-item>

        <form-item has-feedback label="Description" :help="form.errors.description">
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
