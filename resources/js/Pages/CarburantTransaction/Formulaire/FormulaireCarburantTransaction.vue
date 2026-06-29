<template>
    <FormModal
        v-model:open="isModalVisible"
        :titre="
            isEditMode
                ? 'Modifier Transaction Carburant'
                : 'Ajouter Transaction Carburant'
        "
        :loading="form.processing"
        @close="handleCancel"
        @submit="handleSubmit"
        size="lg"
        :show_champ_obligatoir="false"
    >
        <div class="p-4">
            <a-row :gutter="[16, 24]">
                <a-col :xs="24" :lg="24">
                    <div
                        class="rounded-md p-4 lg:p-6 border border-gray-100 shadow-sm transition-all duration-300 bg-white"
                    >
                        <a-row :gutter="[24, 16]">
                            <!-- Colonne gauche -->
                            <a-col :xs="24" :lg="12">
                                <form-item
                                    label="Date de Transaction"
                                    required
                                    :help="form.errors.date_transaction"
                                >
                                    <a-date-picker
                                        v-model:value="form.date_transaction"
                                        size="large"
                                        class="w-full"
                                        format="YYYY-MM-DD"
                                    />
                                </form-item>

                                <form-item
                                    label="Mode de Paiement"
                                    required
                                    :help="form.errors.type"
                                >
                                    <a-segmented
                                        v-model:value="form.type"
                                        :options="[
                                            {
                                                label: 'Carte',
                                                value: 'achat_carte',
                                            },
                                            {
                                                label: 'Espèce',
                                                value: 'achat_espece',
                                            },
                                        ]"
                                        block
                                        size="large"
                                        class="!w-full type-segmented"
                                    />
                                </form-item>
                                <form-item
                                    label="Carburant (Litre)"
                                    :help="form.errors.carburant_litre"
                                    required
                                >
                                    <InputNumberWithSepartor
                                        v-model:modelValue="
                                            form.carburant_litre
                                        "
                                        size="large"
                                        placeholder="0"
                                        :min="0"
                                        class="modern-input w-full"
                                    />
                                </form-item>

                                <form-item
                                    label="Prix Unitaire (Ariary)"
                                    :help="form.errors.prix_unitaire"
                                    required
                                >
                                    <InputNumberWithSepartor
                                        v-model:modelValue="form.prix_unitaire"
                                        size="large"
                                        placeholder="0"
                                        :min="0"
                                        class="modern-input w-full"
                                    />
                                </form-item>
                                <form-item
                                    label="Montant (Ariary)"
                                    required
                                    :help="form.errors.montant"
                                >
                                    <InputNumberWithSepartor
                                        v-model:modelValue="form.montant"
                                        size="large"
                                        placeholder="0"
                                        :min="0"
                                        class="modern-input w-full"
                                        :disabled="true"
                                    />
                                </form-item>

                                <form-item
                                    label="Commentaire"
                                    :help="form.errors.commentaire"
                                >
                                    <a-textarea
                                        v-model:value="form.commentaire"
                                        :auto-size="{ minRows: 6, maxRows: 6 }"
                                    >
                                    </a-textarea>
                                </form-item>
                            </a-col>

                            <!-- Colonne droite -->
                            <a-col :xs="24" :lg="12">
                                <form-item
                                    label="Reference"
                                    required
                                    :help="form.errors.reference"
                                >
                                    <a-input
                                        v-model:value="form.reference"
                                        size="large"
                                        class="w-full"
                                    />
                                </form-item>
                                <form-item
                                    label="Carte Carburant Associée"
                                    :help="form.errors.carburant_card_id"
                                    v-if="form.type === 'achat_carte'"
                                >
                                    <a-select
                                        v-model:value="form.carburant_card_id"
                                        size="large"
                                        placeholder="Sélectionner une carte"
                                        class="w-full"
                                        allow-clear
                                        show-search
                                        :filter-option="
                                            (input, option) =>
                                                option.label
                                                    .toLowerCase()
                                                    .includes(
                                                        input.toLowerCase()
                                                    )
                                        "
                                        option-filter-prop="label"
                                    >
                                        <a-select-option
                                            v-for="card in carburantCards"
                                            :key="card.value"
                                            :value="card.value"
                                            :label="card.label"
                                        >
                                            <div
                                                class="flex items-center justify-between"
                                            >
                                                <span class="font-medium">{{
                                                    card.label
                                                }}</span>
                                                <span>{{ card.solde }}</span>
                                            </div>
                                        </a-select-option>
                                    </a-select>
                                </form-item>

                                <form-item
                                    label="Véhicule Associé"
                                    :help="form.errors.vehicule_id"
                                >
                                    <a-select
                                        v-model:value="form.vehicule_id"
                                        size="large"
                                        placeholder="Sélectionner un véhicule"
                                        class="w-full"
                                        allow-clear
                                        show-search
                                        :filter-option="
                                            (input, option) =>
                                                option.label
                                                    .toLowerCase()
                                                    .includes(
                                                        input.toLowerCase()
                                                    )
                                        "
                                        option-filter-prop="label"
                                    >
                                        <a-select-option
                                            v-for="vehicule in vehicules"
                                            :key="vehicule.value"
                                            :value="vehicule.value"
                                            :label="vehicule.label"
                                        >
                                            {{ vehicule.label }}
                                        </a-select-option>
                                    </a-select>
                                </form-item>

                                <form-item
                                    label="Chauffeur Associé"
                                    :help="form.errors.chauffeur_id"
                                >
                                    <a-select
                                        v-model:value="form.chauffeur_id"
                                        size="large"
                                        placeholder="Sélectionner un chauffeur"
                                        class="w-full"
                                        allow-clear
                                        show-search
                                        :filter-option="
                                            (input, option) =>
                                                option.label
                                                    .toLowerCase()
                                                    .includes(
                                                        input.toLowerCase()
                                                    )
                                        "
                                        option-filter-prop="label"
                                    >
                                        <a-select-option
                                            v-for="chauffeur in chauffeurs"
                                            :key="chauffeur.value"
                                            :value="chauffeur.value"
                                            :label="chauffeur.label"
                                        >
                                            {{ chauffeur.label }}
                                        </a-select-option>
                                    </a-select>
                                </form-item>
                                <form-item
                                    label="Fichiers joints"
                                    required
                                    class="mt-6"
                                >
                                    <div
                                        class="bg-gray-50 border border-dashed border-gray-300 rounded-md p-4 hover:border-sky-500 hover:bg-sky-50 transition-all duration-300 max-h-60 overflow-y-auto"
                                    >
                                        <UploadMultipleFileAndImage
                                            :initial-files="
                                                mapExistingImages(
                                                    form.piece_jointe
                                                )
                                            "
                                            @updateFiles="
                                                (files) =>
                                                    handleFilesUpdate(
                                                        form,
                                                        files
                                                    )
                                            "
                                            :key="uploadComponentKey"
                                            accept="image/*,.pdf,.doc,.docx"
                                        >
                                            <template #tips>
                                                <div
                                                    class="mt-3 p-3 bg-primary/5 rounded border border-blue-200"
                                                >
                                                    <p
                                                        class="text-xs text-primary font-medium"
                                                    >
                                                        <font-awesome-icon
                                                            icon="fa-solid fa-info-circle"
                                                            class="mr-2"
                                                        />
                                                        Formats acceptés :
                                                        Images, PDF, DOC
                                                        <span
                                                            class="ml-1 font-semibold"
                                                            >(max 5MB)</span
                                                        >
                                                    </p>
                                                </div>
                                            </template>
                                        </UploadMultipleFileAndImage>
                                    </div>
                                </form-item>
                            </a-col>
                        </a-row>
                    </div>
                </a-col>
            </a-row>
        </div>
    </FormModal>
</template>

<script setup>
import FormItem from "@/Components/FormItem.vue";
import FormModal from "@/Components/FormModal.vue";
import InputNumberWithSepartor from "@/Components/InputNumberWithSepartor.vue";
import UploadMultipleFileAndImage from "@/Components/UploadFile/UploadMultipleFileAndImage.vue";
import { router, useForm } from "@inertiajs/vue3";
import dayjs from "dayjs";
import { ref, watch } from "vue";

const isModalVisible = ref(false);
const isEditMode = ref(false);
const uploadComponentKey = ref(0);

const props = defineProps({
    carburantCards: {
        type: Array,
        default: () => [],
    },
    chauffeurs: {
        type: Array,
        default: () => [],
    },
    vehicules: {
        type: Array,
        default: () => [],
    },
});

const form = useForm({
    id: null,
    montant: null,
    date_transaction: dayjs(),
    carburant_card_id: null,
    description: null,
    type: "achat_carte",
    chauffeur_id: null,
    carburant_litre: null,
    prix_unitaire: null,
    vehicule_id: null,
    piece_jointe: [],
    commentaire: null,
    reference: null,
});

const add = () => {
    isEditMode.value = false;
    form.reset();
    form.clearErrors();
    isModalVisible.value = true;
    uploadComponentKey.value++;
};
const update = (record) => {
    isEditMode.value = true;
    form.clearErrors();
    form.id = record.id;
    form.carburant_litre = record.carburant_litre;
    form.prix_unitaire = record.prix_unitaire;
    form.montant = record.montant;
    form.date_transaction = dayjs(record.date_transaction);
    form.type = record.raw_type;
    form.carburant_card_id =
        record.raw_type === "achat_carte" ? record.carburant_card_id : null;
    form.chauffeur_id = record.chauffeur_id;
    form.vehicule_id = record.vehicule_id;
    form.commentaire = record.commentaire;
    form.reference = record.reference;
    form.piece_jointe = mapExistingImages(record.piece_jointe || []);
    isModalVisible.value = true;
    uploadComponentKey.value++;
};
const handleCancel = () => {
    isModalVisible.value = false;
    form.reset();
    form.clearErrors();
};

const mapExistingImages = (listePj) => {
    const imageExtensions = ["jpg", "jpeg", "png", "gif", "bmp", "webp"];
    let processedListePj = listePj;
    if (typeof listePj === "string") {
        try {
            processedListePj = JSON.parse(listePj);
        } catch (e) {
            console.error("Failed to parse piece_jointe JSON string:", e);
            processedListePj = [];
        }
    }
    if (!processedListePj || !Array.isArray(processedListePj)) {
        return [];
    }
    return processedListePj
        .map((item) => {
            const originalPath =
                typeof item === "string"
                    ? item
                    : item.src || item.url || item.path;
            if (!originalPath) return null;
            const extension =
                originalPath.split(".").pop()?.toLowerCase() || "";
            const isImage = imageExtensions.includes(extension);
            return {
                url: `../../${originalPath}`,
                originalPath: originalPath,
                isExisting: true,
                type: isImage ? "image/jpeg" : extension,
                name: item.name || originalPath.split("/").pop(),
                size: item.size || 0,
                ...(typeof item === "object" ? item : {}),
            };
        })
        .filter(Boolean);
};
const handleFilesUpdate = (
    documentInstance,
    { existing = [], newFiles = [] } = {}
) => {
    console.log("Existing files:", existing);
    console.log("New files:", newFiles);
    const existingFiles = existing
        .filter((file) => file.isExisting)
        .map((file) => ({
            src: file.url.replace("../../", ""),
            isExisting: true,
        }));
    documentInstance.piece_jointe = [...existingFiles, ...newFiles];
};

//Lorsqu'on switch vers type achat_espece ,le carburant_card_id null
watch(
    () => form.type,
    (newValue) => {
        if (newValue === "achat_espece") {
            form.carburant_card_id = null;
        }
    }
);

watch(
    [() => form.carburant_litre, () => form.prix_unitaire],
    ([newLitre, newPrix]) => {
        const litre = parseFloat(newLitre);
        const prix = parseFloat(newPrix);
        if (!isNaN(litre) && !isNaN(prix) && litre >= 0 && prix >= 0) {
            form.montant = litre * prix;
        } else {
            form.montant = null;
        }
    }
);

const handleSubmit = () => {
    const formData = new FormData();
    if (isEditMode.value) {
        formData.append("_method", "PUT");
    }
    for (const key in form.data()) {
        if (key === "piece_jointe") continue;
        const value = form.data()[key];
        if (value !== null && value !== undefined) {
            if (dayjs.isDayjs(value)) {
                formData.append(key, value.format("YYYY-MM-DD"));
            } else {
                formData.append(key, value);
            }
        }
    }
    const existingFiles = [];
    const newFiles = [];
    if (form.piece_jointe?.length) {
        form.piece_jointe.forEach((item) => {
            if (item instanceof File) {
                newFiles.push(item);
            } else if (item.src) {
                existingFiles.push(item.src);
            }
        });
    }
    newFiles.forEach((file, index) => {
        formData.append(`piece_jointe[${index}]`, file);
    });
    formData.append("existing_files", JSON.stringify(existingFiles));
    const url = isEditMode.value
        ? route("carburant_transactions.update", form.id)
        : route("carburant_transactions.store");
    router.post(url, formData, {
        onSuccess: () => {
            isModalVisible.value = false;
        },
        onError: (errors) => {
            form.errors = errors;
        },
        forceFormData: true,
        headers: {
            "Content-Type": "multipart/form-data",
        },
    });
};
defineExpose({ add, update });
</script>

<style>
.type-segmented .ant-segmented-item-label {
    @apply py-2 px-6 text-base font-medium;
}

.type-segmented .ant-segmented-item-selected {
    @apply bg-primary text-white shadow-md !important;
}
</style>
