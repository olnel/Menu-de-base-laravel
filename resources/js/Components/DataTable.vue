<script setup>
import usePermissions from "@/UserPermissions/usePermissions.js";
import { router, usePage } from "@inertiajs/vue3";
import { computed, onMounted } from "vue";

const { can } = usePermissions();

const props = defineProps({
    data: { type: Object, required: true },
    columns: { type: Array, required: true },
    actions: Array,
    filters: { type: Object, default: () => ({}) },
    rowKey: { type: String, default: "id" },
    reload_route: String,
    showIndex: { type: Boolean, default: false },
    btn_action: { type: Boolean, default: true },
    expandable: { type: Object, default: undefined },
    rowClassName: { type: Function, default: null },
});

const page = usePage();

const internalColumns = computed(() => {
    let cols = [...props.columns];

    if (props.showIndex && !cols.find((c) => c.key === "index")) {
        cols.unshift({
            title: "#",
            key: "index",
            width: 50,
            customCell: () => ({
                class: "!text-center",
            }),
        });
    }

    if (props.actions && !cols.find((c) => c.key === "action")) {
        cols.push({
            title: "",
            key: "action",
            width: 60,
            customCell: () => ({
                class: "fixed-column !text-center",
            }),
            fixed: "right",
        });
    }

    return cols;
});

const pagination = computed(() => ({
    total: props.data.total,
    current: props.data.current_page,
    pageSize: props.data.per_page,
    showSizeChanger: false,
    showTotal: (total, range) => `${range[0]} - ${range[1]} sur ${total}`,
}));

function handleTableChange(pag, filters, sorter) {
    const baseUrl = page.url.split("?")[0];
    router.get(
        baseUrl,
        { page: pag.current, ...props.filters },
        { preserveState: true, preserveScroll: true }
    );
}

function handleActionVisible(action, record) {
    if (action.visible == null) return true;
    return typeof action.visible === "function"
        ? action.visible(record)
        : !!action.visible;
}

function handleActionDisabled(action, record) {
    if (action.disabled == null) return false;
    return typeof action.disabled === "function"
        ? action.disabled(record)
        : !!action.disabled;
}

function getActionClass(action, record) {
    if (!action.class) return "";
    return typeof action.class === "function"
        ? action.class(record)
        : action.class;
}
</script>

<template>
    <a-table
        size="small"
        class="main-table !whitespace-nowrap main-rounded overflow-hidden min-w-full h-full"
        style="height: calc(100% - 64px)"
        :data-source="data.data"
        :pagination="pagination"
        @change="handleTableChange"
        :columns="internalColumns"
        :row-key="rowKey"
        :scroll="{ x: 'max-content', y: 'calc(100vh - 320px)' }"
        :expandable="props.expandable"
        :row-class-name="
            (record, index) => {
                const extra = props.rowClassName ? props.rowClassName(record) : '';
                const striped = index % 2 === 1 ? 'table-striped' : '';
                return [striped, extra].filter(Boolean).join(' ');
            }
        "
    >
        <template #headerCell="{ column }">
            <slot name="headerCell" :column="column">
                <span class="uppercase !text-nowrap w-full !text-left">
                    {{ column.title }}
                </span>
            </slot>
        </template>

        <template #emptyText>
            <a-empty description="Aucun résultat" class="my-10" />
        </template>

        <template #bodyCell="{ column, record, text, index }">
            <slot
                name="bodyCell"
                :column="column"
                :record="record"
                :text="text"
                :index="index"
            />

            <!-- Index colonne -->
            <template v-if="column.key === 'index'">
                {{ (data.current_page - 1) * data.per_page + index + 1 }}
            </template>

            <!-- Colonne image (exemples) -->
            <template v-else-if="column.key === 'image'">
                <a-image
                    width="60"
                    :src="record.thumb_image || record.thumb_img"
                    :preview="{ src: record.background_image || record.img }"
                    fallback=""
                    class="rounded object-cover h-[40px]"
                />
            </template>
            <template v-else-if="column.key === 'img_2'">
                <a-image
                    width="60"
                    :src="record.thumb_img2"
                    :preview="{ src: record.img_2 }"
                    fallback=""
                    class="rounded object-cover h-[40px]"
                />
            </template>

            <!-- Action buttons -->
            <template v-else-if="column.key === 'action'">
                <a-dropdown
                    v-if="btn_action"
                    placement="bottomRight"
                    :trigger="['click']"
                >
                    <a-button
                        type="text"
                        class="!p-0 !h-0 hover:!bg-transparent hover:text-text-primary"
                    >
                        <font-awesome-icon icon="fa-solid fa-ellipsis" />
                    </a-button>
                    <template #overlay>
                        <a-menu>
                            <template
                                v-for="action in actions"
                                :key="action.text"
                            >
                                <hr v-if="action.text === '-'" />
                                <a-menu-item
                                    v-else-if="
                                        handleActionVisible(action, record) &&
                                        can(action.privilege)
                                    "
                                    :class="
                                        typeof action.class === 'function'
                                            ? action.class(record)
                                            : action.class
                                    "
                                    :disabled="
                                        typeof action.disabled === 'function'
                                            ? action.disabled(record)
                                            : action.disabled
                                    "
                                    @click="action.action(record)"
                                >
                                    <font-awesome-icon
                                        v-if="action.icon"
                                        class="me-2"
                                        :icon="
                                            typeof action.icon === 'function'
                                                ? action.icon(record)
                                                : action.icon
                                        "
                                    />
                                    <span>{{
                                        typeof action.text === "function"
                                            ? action.text(record)
                                            : action.text
                                    }}</span>
                                </a-menu-item>
                            </template>
                        </a-menu>
                    </template>
                </a-dropdown>
                <template v-else>
                    <a-button
                        v-for="action in actions"
                        :key="action.text"
                        v-show="
                            handleActionVisible(action, record) &&
                            can(action.privilege)
                        "
                        :title="
                            typeof action.text === 'function'
                                ? action.text(record)
                                : action.text
                        "
                        :class="getActionClass(action, record)"
                        class="!p-1"
                        :disabled="handleActionDisabled(action, record)"
                        type="text"
                        @click="action.action(record)"
                    >
                        <font-awesome-icon :icon="action.icon" />
                    </a-button>
                </template>
            </template>
        </template>

        <template #summary>
            <slot name="summary" />
        </template>

        <template
            v-if="$slots.expandedRowRender"
            #expandedRowRender="{ record }"
        >
            <slot name="expandedRowRender" :record="record" />
        </template>
    </a-table>
</template>

<style scoped>
.main-table {
    background: white;
}

.main-table .ant-table-cell {
    padding: 8px;
}

.table-striped td {
    background-color: #fafafa;
}

.row-danger td {
    background-color: #fff2f0 !important;
}

.row-danger:hover td {
    background-color: #ffe0de !important;
}

.fixed-column {
    position: sticky !important;
    right: 0;
    background: white;
}

.ant-table-header {
    border-radius: 0 !important;
}

/* Correction pour les lignes sélectionnées */
:deep(.ant-table-wrapper .ant-table-tbody > tr.ant-table-row-selected > td) {
    background: white !important;
}

:deep(
        .ant-table-wrapper
            .ant-table-tbody
            > tr.ant-table-row-selected:hover
            > td
    ) {
    background: #fafafa !important;
}

/* Pour la case à cocher sélectionnée */
:deep(.ant-checkbox-checked .ant-checkbox-inner) {
    background-color: #1890ff;
    border-color: #1890ff;
}

/* Styles pour les actions personnalisées */
.ajustement-style {
    opacity: 0.7;
    font-style: italic;
}
</style>
