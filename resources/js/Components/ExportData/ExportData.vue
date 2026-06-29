<script setup lang="js">
import { defineProps, computed, ref, reactive, watch, nextTick } from 'vue';
import { DownloadOutlined, FilePdfOutlined, FileExcelOutlined } from '@ant-design/icons-vue';

const props = defineProps({
    columns: {
        default: () => []
    },
    title: {
        default: ''
    },
    url: {
        default: '',
    },
    filter: {
        default: () => ({})
    },
    show_import: {
        type: Boolean,
        default: false
    }
});

const dropdownVisible = ref(false);
const selectAll = ref(true);

// Créer un état réactif local pour les colonnes exportables
const exportableColumns = reactive({});

const initColumnsExportable = () => {
    props.columns.forEach(column => {
        if (column.dataIndex != 'actions' && column.key != 'index' && column.key != 'action' && column.key != 'photo') {
            exportableColumns[column.dataIndex] = column.exportable !== undefined ? column.exportable : true;
        }
    });
    updateSelectAllState();
};

const getExportableColumns = () => {
    return props.columns.filter(c => (c.dataIndex != 'actions' && c.key != 'index' && c.key != 'action' &&  c.key != 'photo' ));
};

const get_url_params = () => {
    const params = new URLSearchParams();
    let exportable = {};

    getExportableColumns().forEach(column => {
        if (exportableColumns[column.dataIndex]) {
            exportable[column.dataIndex] = column.title;
        }
    });

    params.append('exportable', JSON.stringify(exportable));
    params.append('filter', JSON.stringify(props.filter));
    params.append('search', props.filter.search);

    return params;
};

const url_export_pdf = computed(() => {
    const params = get_url_params();
    params.append('type_export', 'pdf');
    return `${props.url}?${params.toString()}`;
});

const url_export_excel = computed(() => {
    const params = get_url_params();
    params.append('type_export', 'xlsx');
    return `${props.url}?${params.toString()}`;
});

const hasExportableColumns = computed(() => {
    return Object.values(exportableColumns).some(value => value === true);
});

const toggleSelectAll = async (e) => {
    const newValue = e.target.checked;
    selectAll.value = newValue;

    getExportableColumns().forEach(column => {
        exportableColumns[column.dataIndex] = newValue;
    });

    // Forcer la mise à jour de la vue
    await nextTick();
};

const updateSelectAllState = () => {
    const columns = getExportableColumns();
    if (columns.length === 0) {
        selectAll.value = false;
        return;
    }

    const allSelected = columns.every(col => exportableColumns[col.dataIndex] === true);
    const noneSelected = columns.every(col => exportableColumns[col.dataIndex] === false);

    if (allSelected) {
        selectAll.value = true;
    } else if (noneSelected) {
        selectAll.value = false;
    } else {
        // État indéterminé - on peut choisir de mettre false ou gérer un état mixte
        selectAll.value = false;
    }
};

const handleColumnToggle = async (columnDataIndex) => {
    exportableColumns[columnDataIndex] = !exportableColumns[columnDataIndex];
    updateSelectAllState();
    await nextTick();
};

// Fermer le dropdown uniquement pour les clics sur les liens d'exportation
const handleExportClick = () => {
    dropdownVisible.value = false;
};

// Watcher pour réinitialiser quand les colonnes changent
watch(() => props.columns, () => {
    initColumnsExportable();
}, { deep: true, immediate: true });

// Initialisation
initColumnsExportable();
</script>

<template>
    <a-dropdown v-model:open="dropdownVisible" placement="bottomLeft" :trigger="['click']">
        <a-button class="!rounded-l-none" size="large" :title="props.title">
            <DownloadOutlined />
        </a-button>
        <template #overlay>
            <a-menu>
                <a-menu-item :disabled="!hasExportableColumns" @click="handleExportClick">
                    <a :href="url_export_excel" target="_blank">
                        <FileExcelOutlined />
                        Exporter en Excel
                    </a>
                </a-menu-item>
                <!-- <a-menu-item :disabled="!hasExportableColumns" @click="handleExportClick">
                    <a :href="url_export_pdf" target="_blank">
                        <FilePdfOutlined />
                        Exporter en PDF
                    </a>
                </a-menu-item> -->
                <a-menu-item v-if="show_import" :disabled="!hasExportableColumns" @click="handleExportClick">
                    <slot name="import">
                    </slot>
                </a-menu-item>
                <a-menu-divider />
                <a-menu-item disabled>Paramètres d'export</a-menu-item>
                <a-menu-divider />

                <a-menu-item @click.stop>
                    <a-checkbox
                        :checked="selectAll"
                        @change="toggleSelectAll"
                    >
                        Sélectionner tout
                    </a-checkbox>
                </a-menu-item>
                <a-menu-divider />

                <a-menu-item
                    v-for="col in getExportableColumns()"
                    :key="col.dataIndex"
                    @click.stop
                >
                    <a-checkbox
                        :checked="exportableColumns[col.dataIndex]"
                        @change="() => handleColumnToggle(col.dataIndex)"
                    >
                        {{ col.title }}
                    </a-checkbox>
                </a-menu-item>
            </a-menu>
        </template>
    </a-dropdown>
</template>
