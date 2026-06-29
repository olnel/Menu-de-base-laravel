
<script setup>
import {ref} from 'vue';
import PublicLayout from "@/Pages/Public/Components/Layouts/PublicLayout.vue";
import Breadcrumb from "@/Pages/Public/Components/Breadcrumb.vue";

const breadcrumbs = [
    { text: 'FAQ', route: '/faq' }
];

const activeKeys = ref([]);

const props = defineProps({
    faq: {
        type: Object,
        default: () => []
    },
    information: {
        type: Object,
        default: () => {}
    }
})


</script>

<template>

    <PublicLayout title="FAQ" :information="information" selected-menu="faq" class="relative">
        <breadcrumb :items="breadcrumbs" />
        <div class="absolute inset-0 bg-cover bg-center bg-no-repeat bg-fixed -z-10"
             style="background-image: url('/img/bg_faq.jpg')"></div>
        <div class="absolute inset-0 bg-black/5 -z-10"></div>
        <div class="py-16">
            <div class="container mx-auto px-4 sm:px-6">
                <div class="max-w-2xl ml-auto bg-white/40 backdrop-blur-3xl p-4 rounded-md">
                    <div class="text-left mb-8">
                        <div class="inline-block mb-2">
                            <a-typography-title :level="2" class="!text-primary !m-0">FAQ</a-typography-title>
                        </div>
                        <h2 class="text-3xl md:text-4xl text-neutral-700 mb-4">
                            <span class="font-extrabold uppercase tracking-wide">VOS QUESTIONS, </span>NOS RÉPONSES !
                        </h2>

                        <p class="text-gray-600 text-lg">
                            Vous avez une question ? Retrouvez ici les réponses aux interrogations les plus fréquentes
                            concernant nos
                            produits, nos services et le fonctionnement de notre site.
                        </p>
                    </div>
                    <!-- Enhanced FAQ accordion with visual elements -->
                    <div class="space-y-4">
                        <a-collapse
                            v-model:activeKey="activeKeys"
                            :bordered="false"
                            accordion
                            class="bg-transparent shadow-none"
                        >
                            <a-collapse-panel
                                v-for="(item, index) in faq"
                                :key="index"
                                :showArrow="false"
                                class="faq-panel rounded-sm overflow-hidden shadow-sm border border-gray-100 mb-4 bg-white"
                            >
                                <template #header>
                                    <div :class="['flex items-center px-4 py-3 transition-colors duration-200',
                                  activeKeys && activeKeys.includes(index.toString()) ? 'bg-primary text-white' : 'bg-white']"
                                    >
                                        <div
                                            :class="[
                                        'w-8 h-8 rounded-full flex items-center justify-center border-2 mr-4 flex-shrink-0',
                                        activeKeys && activeKeys.includes(index.toString())
                                          ? 'border-white'
                                          : 'border-primary'
                                      ]"
                                        >
                                            <font-awesome-icon
                                                icon="question"
                                                :class="[
                                          'text-base',
                                          activeKeys && activeKeys.includes(index.toString()) ? 'text-white' : 'text-primary'
                                        ]"
                                            />
                                        </div>

                                        <span
                                            :class="['font-medium text-lg transition-all duration-300',activeKeys && activeKeys.includes(index.toString()) ? 'text-white' : 'text-gray-800']"
                                        >
                                      {{ item.question }}
                                </span>
                                        <div class="ml-auto">
                                            <font-awesome-icon
                                                :icon="activeKeys && activeKeys.includes(index.toString()) ? 'chevron-up' : 'chevron-down'"
                                                :class="[
                                          'transition-transform duration-200',
                                          activeKeys && activeKeys.includes(index.toString()) ? 'text-white' : 'text-primary'
                                        ]"
                                            />
                                        </div>
                                    </div>
                                </template>

                                <div class="py-4 px-1 text-gray-700 text-base">
                                    {{ item.reponse }}
                                </div>
                            </a-collapse-panel>
                        </a-collapse>
                    </div>
                </div>


            </div>
        </div>
    </PublicLayout>
</template>

<style scoped>
/* Custom styles to enhance the visual design */
:deep(.ant-collapse) {
    padding: 0 !important;
    background-color: transparent;
}

:deep(.ant-collapse-header) {
    padding: 0 !important;
    align-items: center !important;
}

:deep(.ant-collapse-content) {
    border-top: none !important;
}

:deep(.ant-btn-primary) {
    background-color: #4CAF50;
    border-color: #4CAF50;
}

:deep(.ant-btn-primary:hover) {
    background-color: #3d8b40;
    border-color: #3d8b40;
}

:deep(.faq-panel) {
    transition: all 0.3s ease;
}

:deep(.faq-panel:hover) {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
}
</style>
