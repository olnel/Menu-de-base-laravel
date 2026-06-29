<template>
    <div class="flex flex-wrap gap-6 p-4">
        <!-- Card Component -->
        <div
            v-for="(partner, index) in partners"
            :key="index"
            class="card-container group relative flex flex-col rounded-lg overflow-hidden shadow-lg hover:shadow-xl transition-all duration-300"
        >
            <!-- Image Header -->
            <div class="image-container h-48 bg-gray-100 overflow-hidden">
                <img
                    :src="partner.logo || '/images/default-partner-logo.png'"
                    :alt="partner.nom_partner"
                    class="w-full h-full object-contain bg-white p-2"
                />
            </div>

            <!-- Partner Name -->
            <div class="p-4 bg-white flex-grow flex items-center">
                <h3 class="text-lg font-semibold text-gray-800 text-center w-full truncate">
                    {{ partner.nom_partner }}
                </h3>
            </div>

            <!-- Action Buttons -->
            <div class="button-group absolute top-2 right-2 flex space-x-1 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                <template v-for="(action, idx) in processedActions" :key="idx">
                    <a-button
                        v-if="!action.disabled || !action.disabled(partner)"
                        :type="action.danger ? 'primary' : 'default'"
                        :danger="action.danger"
                        shape="circle"
                        @click="handleAction(action, partner)"
                        class="!flex items-center justify-center !w-8 !h-8"
                        :disabled="action.disabled && action.disabled(partner)"
                    >
                        <template #icon>
                            <font-awesome-icon :icon="action.icon" class="text-sm" />
                        </template>
                    </a-button>
                </template>
            </div>
        </div>
    </div>

    <!-- Pagination -->
    <div class="px-4 pb-4 flex justify-center">
        <a-pagination
            v-model:current="currentPage"
            :total="partners.total"
            :pageSize="partners.per_page"
            :showSizeChanger="false"
            @change="handlePageChange"
            class="pagination-custom"
        />
    </div>
</template>

<script setup>
import {computed, ref, watch} from 'vue';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';



const props = defineProps({
    partners: {
        type: Array,
        default: () => []
    },
    actions: {
        type: Array,
        default: () => []
    }
});


const emit = defineEmits(['edit', 'delete', 'page-change']);

const currentPage = ref(props.partners.current_page);

// Watch for external page changes
watch(() => props.partners.current_page, (newVal) => {
    currentPage.value = newVal;
});

// Actions par défaut si aucune n'est fournie
const processedActions = computed(() => {
    return props.actions.length > 0 ? props.actions : [
        {
            text: "Modifier",
            action: (record) => emit('edit', record),
            icon: 'fa-pen-to-square',
            danger: false
        },
        {
            text: "Supprimer",
            action: (record) => emit('delete', record),
            icon: 'fa-trash',
            danger: true,
            disabled: (record) => record.is_you
        }
    ];
});

const handleAction = (action, partner) => {
    if (typeof action.action === 'function') {
        action.action(partner);
    }
};


const handlePageChange = (page) => {
    emit('page-change', page);
};
</script>

<style scoped>
.card-container {
    width: 250px;
    height: 350px;
    transition: transform 0.2s;
}

.card-container:hover {
    transform: translateY(-5px);
}

.button-group {
    transition: opacity 0.2s;
}

.card-container:hover .button-group {
    opacity: 1;
}

.object-contain {
    object-fit: fill;
}

/* Style personnalisé pour la pagination */
/* Style amélioré pour la pagination */
:deep(.pagination-custom .ant-pagination-item),
:deep(.pagination-custom .ant-pagination-prev),
:deep(.pagination-custom .ant-pagination-next),
:deep(.pagination-custom .ant-pagination-jump-prev),
:deep(.pagination-custom .ant-pagination-jump-next) {
    min-width: 36px;
    height: 36px;
    line-height: 36px;
    border-radius: 6px;
    margin: 0 4px;
    border: 1px solid #e0e0e0;
    transition: all 0.3s ease;
}

:deep(.pagination-custom .ant-pagination-item a),
:deep(.pagination-custom .ant-pagination-prev a),
:deep(.pagination-custom .ant-pagination-next a) {
    color: #555;
    font-weight: 500;
}

:deep(.pagination-custom .ant-pagination-item:hover),
:deep(.pagination-custom .ant-pagination-prev:hover),
:deep(.pagination-custom .ant-pagination-next:hover) {
    border-color: #1890ff;
    background-color: #f0f7ff;
}

:deep(.pagination-custom .ant-pagination-item-active) {
    border-color: #1890ff;
    background-color: #1890ff;
    box-shadow: 0 2px 6px rgba(24, 144, 255, 0.3);
}

:deep(.pagination-custom .ant-pagination-item-active a) {
    color: white;
    font-weight: 600;
}

:deep(.pagination-custom .ant-pagination-item-active:hover) {
    background-color: #40a9ff;
    border-color: #40a9ff;
}

:deep(.pagination-custom .ant-pagination-disabled) {
    opacity: 0.6;
    cursor: not-allowed;
}

:deep(.pagination-custom .ant-pagination-disabled:hover) {
    background-color: transparent;
    border-color: #e0e0e0;
}
</style>
