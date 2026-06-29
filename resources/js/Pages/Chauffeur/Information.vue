<template>
    <AuthenticatedLayout :title="pageTitle" selected-menu="chauffeur">
        <a-card class="main-shadow rounded-lg overflow-hidden">
            <div class="space-y-8">
                <a-tabs v-model:activeKey="activeTab">
                    <a-tab-pane key="personal" tab="Informations Personnelles">
                        <div class="p-4">
                            <InfoPerso
                                ref="chauffeurFormRef"
                                :vehicules="vehicules"
                                :initial-chauffeur="chauffeur"
                            />

                            <div class="flex justify-end mt-6" v-if="can('chauffeur.update')">
                                <a-button
                                    type="primary"
                                    size="large"
                                    :loading="
                                        chauffeurFormRef?.form?.processing
                                    "
                                    @click="submitPersonalInformation"
                                >
                                    <template #icon>
                                        <font-awesome-icon
                                            icon="fa-solid fa-save"
                                            class="mr-2"
                                        />
                                    </template>
                                    Enregistrer les modifications
                                </a-button>
                            </div>
                        </div>
                    </a-tab-pane>

                    <a-tab-pane key="documents" tab="Documents du Chauffeur" v-if="can('chauffeurdocument.index')">
                        <InfoDocuments :chauffeur="chauffeur" />
                    </a-tab-pane>
                </a-tabs>
            </div>
        </a-card>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { computed, ref } from "vue";
import InfoPerso from "@/Pages/Chauffeur/Informations/InfoPerso.vue";

import InfoDocuments from "@/Pages/Chauffeur/Informations/InfoDoc.vue";
import usePermissions from "@/UserPermissions/usePermissions.js";
const { can } = usePermissions()

const props = defineProps({
    chauffeur: {
        type: Object,
        required: true,
    },
    vehicules: {
        type: Array,
        default: () => [],
    },
});

const activeTab = ref("personal");
const chauffeurFormRef = ref(null);

const pageTitle = computed(() => {
    return props.chauffeur
        ? `Informations de ${props.chauffeur.nom.toUpperCase()} ${
              props.chauffeur.prenom
          }`
        : "Informations du Chauffeur";
});


const submitPersonalInformation = () => {
    if (chauffeurFormRef.value) {
        chauffeurFormRef.value.submit();
    }
};
</script>

<style scoped></style>
