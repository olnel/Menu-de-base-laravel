<script setup>
import { ref, computed } from "vue";
import { useForm } from "@inertiajs/vue3";
import FormItem from "@/Components/FormItem.vue";
import FormModal from "@/Components/FormModal.vue";

const props = defineProps({

});

const form = useForm({
    id: null,
    nom_categorie: null,
    is_navigation: 0,
});


const isNavigationBool = computed({
    get: () => form.is_navigation === 1,
    set: (val) => form.is_navigation = val ? 1 : 0
});

const open = ref(false);
const title = ref("");

const close = () => {
    open.value = false;
    form.reset();
    form.clearErrors();
};

const add = () => {
    title.value = "Nouvelle Catégorie";
    open.value = true;
};

const update = (data) => {
    title.value = "Modification";
    open.value = true;

    form.id = data.id;
    form.nom_categorie = data.nom_categorie;
    form.is_navigation = data.is_navigation;
};

const submit = () => {
    form.clearErrors();

    if (form.id) {
        form.put(route('categorie.update', form.id), {
            onSuccess: () => close()
        });
    } else {
        form.post(route("categorie.store"), {
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
        <form-item required has-feedback label="Catégorie" :help="form.errors.nom_categorie">
            <div class="flex w-full">
                <a-input
                    class="flex-grow rounded-r-none"
                    v-model:value="form.nom_categorie"
                    size="large"
                />
                <span class="flex items-center bg-gray-100 border-l-0 h-[40px] px-2 border border-gray-300 rounded-r">
          <a-checkbox v-model:checked="isNavigationBool" class="!m-0"></a-checkbox>
        </span>
            </div>
        </form-item>
    </FormModal>
</template>

<style scoped>

</style>
