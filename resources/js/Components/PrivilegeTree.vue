<template>
    <div class="space-y-4">
        <div
            v-for="module in privilege"
            :key="module.key"
            class="rounded-xl overflow-hidden border transition-all duration-300"
            :class="getModuleBorderClass(module.key)"
        >
            <!-- En-tête du module -->
            <div
                class="flex items-center justify-between px-5 py-4"
                :class="getModuleHeaderBg(module.key)"
            >
                <div class="flex items-center gap-3 min-w-0">
                    <!-- Icône avec dégradé -->
                    <div
                        class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 shadow-sm"
                        :style="getModuleIconStyle(module)"
                    >
                        <font-awesome-icon
                            :icon="module.icon || 'fa-solid fa-cog'"
                            class="text-white text-base"
                        />
                    </div>

                    <div class="min-w-0">
                        <h4 class="font-semibold text-sm text-gray-800 leading-snug truncate">
                            {{ module.title }}
                        </h4>
                        <div class="flex items-center gap-2 mt-0.5">
                            <!-- Barre de progression -->
                            <div class="w-20 h-1.5 bg-gray-200 rounded-full overflow-hidden">
                                <div
                                    class="h-full rounded-full transition-all duration-500"
                                    :class="getProgressBarColor(module.key)"
                                    :style="{ width: getModuleProgress(module.key) + '%' }"
                                ></div>
                            </div>
                            <span class="text-xs text-gray-500 whitespace-nowrap">
                                {{ countSelectedInModule(module.key) }}/{{ countTotalInModule(module.key) }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Bouton toggle module entier -->
                <div class="flex items-center gap-3 shrink-0">
                    <span class="text-xs font-medium hidden sm:block" :class="getModuleStatusTextClass(module.key)">
                        {{ getModuleStatusText(module.key) }}
                    </span>
                    <div
                        class="w-9 h-9 rounded-xl flex items-center justify-center cursor-pointer transition-all duration-200 hover:scale-105 active:scale-95 shadow-sm"
                        :class="getModuleToggleBg(module.key)"
                        @click="toggleModule(module.key, !isModuleFullyChecked(module.key))"
                    >
                        <font-awesome-icon
                            :icon="isModuleFullyChecked(module.key) ? 'fa-lock-open' : 'fa-lock'"
                            class="text-white text-sm"
                        />
                    </div>
                </div>
            </div>

            <!-- Grille des sous-modules -->
            <div
                v-if="module.children && module.children.length > 0"
                class="grid xl:grid-cols-3 lg:grid-cols-2 grid-cols-1 gap-3 p-4 bg-gray-50"
            >
                <div
                    v-for="child in module.children"
                    :key="child.key"
                    class="bg-white rounded-xl border transition-all duration-200 hover:shadow-md overflow-hidden"
                    :class="getChildBorderClass(child.key)"
                >
                    <!-- En-tête enfant -->
                    <div class="flex items-start justify-between px-4 py-3 border-b border-gray-100">
                        <div class="min-w-0 flex-1">
                            <span class="text-sm font-semibold text-gray-700 block leading-tight">
                                {{ child.title }}
                            </span>
                            <div class="flex items-center gap-1.5 mt-1">
                                <div
                                    class="w-1.5 h-1.5 rounded-full shrink-0"
                                    :class="getChildDotColor(child.key)"
                                ></div>
                                <span class="text-xs text-gray-400">
                                    {{ getChildStatusText(child.key) }}
                                </span>
                            </div>
                        </div>

                        <!-- Badge compteur + checkbox -->
                        <div class="flex items-center gap-2 ml-2 shrink-0">
                            <span
                                v-if="child.children && child.children.length > 0"
                                class="text-xs font-medium px-2 py-0.5 rounded-full"
                                :class="getChildBadgeClass(child.key)"
                            >
                                {{ countSelectedInChild(child.key) }}/{{ countTotalInChild(child.key) }}
                            </span>
                            <a-checkbox
                                :checked="isChildFullyChecked(child.key)"
                                :indeterminate="isChildPartiallyChecked(child.key)"
                                @change="(e) => toggleChild(child.key, e.target.checked)"
                            />
                        </div>
                    </div>

                    <!-- Arbre des permissions détaillées -->
                    <div
                        v-if="child.children && child.children.length > 0"
                        class="px-3 py-2"
                    >
                        <a-tree
                            :checkedKeys="getChildCheckedKeys(child.key)"
                            checkable
                            default-expand-all
                            auto-expand-parent
                            :tree-data="[child]"
                            :selectable="false"
                            :show-icon="false"
                            :block-node="true"
                            @check="(allKeys) => onCheckChild(child.key, allKeys)"
                            class="custom-tree"
                        />
                    </div>

                    <!-- Cas sans sous-enfants : juste une ligne de statut -->
                    <div v-else class="px-4 py-2">
                        <span class="text-xs text-gray-400 italic">Permission unique</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- État vide -->
        <div v-if="privilege.length === 0" class="text-center py-12 text-gray-400">
            <font-awesome-icon icon="fa-circle-exclamation" class="text-3xl mb-3" />
            <p class="text-sm">Aucun module à afficher</p>
        </div>
    </div>
</template>

<script setup>
const props = defineProps({
    privilege: { type: Array, required: true },
    selectedPrivileges: { type: Array, required: true },
});

const emit = defineEmits(["update:selectedPrivileges"]);

// ── Utilitaires routes ──────────────────────────────────────────────────────

const getAllModuleRoutes = (module) => {
    let routes = [];
    if (module.routes) routes.push(...module.routes);
    if (module.children) {
        module.children.forEach((child) => {
            if (child.routes) routes.push(...child.routes);
            if (child.children) {
                child.children.forEach((gc) => {
                    if (gc.routes) routes.push(...gc.routes);
                });
            }
        });
    }
    return routes;
};

const getAllChildRoutes = (child) => {
    let routes = [];
    if (child.routes) routes.push(...child.routes);
    if (child.children) {
        child.children.forEach((gc) => {
            if (gc.routes) routes.push(...gc.routes);
        });
    }
    return routes;
};

const findModule = (key) => props.privilege.find((m) => m.key === key);
const findChild = (key) =>
    props.privilege.flatMap((m) => m.children || []).find((c) => c.key === key);

// ── Statut (coché / partiel) ────────────────────────────────────────────────

const isModuleFullyChecked = (key) => {
    const m = findModule(key);
    if (!m) return false;
    const routes = getAllModuleRoutes(m);
    return routes.length > 0 && routes.every((r) => props.selectedPrivileges.includes(r));
};

const isModulePartiallyChecked = (key) => {
    const m = findModule(key);
    if (!m) return false;
    const routes = getAllModuleRoutes(m);
    const has = routes.some((r) => props.selectedPrivileges.includes(r));
    return has && !isModuleFullyChecked(key);
};

const isChildFullyChecked = (key) => {
    const c = findChild(key);
    if (!c) return false;
    const routes = getAllChildRoutes(c);
    return routes.length > 0 && routes.every((r) => props.selectedPrivileges.includes(r));
};

const isChildPartiallyChecked = (key) => {
    const c = findChild(key);
    if (!c) return false;
    const routes = getAllChildRoutes(c);
    const has = routes.some((r) => props.selectedPrivileges.includes(r));
    return has && !isChildFullyChecked(key);
};

const getChildCheckedKeys = (key) => {
    const c = findChild(key);
    if (!c) return [];
    return getAllChildRoutes(c).filter((r) => props.selectedPrivileges.includes(r));
};

// ── Compteurs ───────────────────────────────────────────────────────────────

const countTotalInModule = (key) => {
    const m = findModule(key);
    return m ? new Set(getAllModuleRoutes(m)).size : 0;
};

const countSelectedInModule = (key) => {
    const m = findModule(key);
    if (!m) return 0;
    return getAllModuleRoutes(m).filter((r) => props.selectedPrivileges.includes(r)).length;
};

const countTotalInChild = (key) => {
    const c = findChild(key);
    return c ? new Set(getAllChildRoutes(c)).size : 0;
};

const countSelectedInChild = (key) => {
    const c = findChild(key);
    if (!c) return 0;
    return getAllChildRoutes(c).filter((r) => props.selectedPrivileges.includes(r)).length;
};

const getModuleProgress = (key) => {
    const total = countTotalInModule(key);
    if (!total) return 0;
    return Math.round((countSelectedInModule(key) / total) * 100);
};

// ── Classes de style dynamiques ─────────────────────────────────────────────

const getModuleIconStyle = (module) => {
    if (module.gradient && module.gradient.length === 2) {
        return { background: `linear-gradient(135deg, ${module.gradient[0]}, ${module.gradient[1]})` };
    }
    return { background: 'linear-gradient(135deg, #6b7280, #4b5563)' };
};

const getModuleBorderClass = (key) => {
    if (isModuleFullyChecked(key)) return 'border-green-300';
    if (isModulePartiallyChecked(key)) return 'border-amber-300';
    return 'border-gray-200';
};

const getModuleHeaderBg = (key) => {
    if (isModuleFullyChecked(key)) return 'bg-green-50';
    if (isModulePartiallyChecked(key)) return 'bg-amber-50';
    return 'bg-white';
};

const getModuleToggleBg = (key) => {
    if (isModuleFullyChecked(key)) return 'bg-green-500 hover:bg-green-600';
    if (isModulePartiallyChecked(key)) return 'bg-amber-400 hover:bg-amber-500';
    return 'bg-gray-300 hover:bg-gray-400';
};

const getProgressBarColor = (key) => {
    if (isModuleFullyChecked(key)) return 'bg-green-500';
    if (isModulePartiallyChecked(key)) return 'bg-amber-400';
    return 'bg-gray-300';
};

const getModuleStatusText = (key) => {
    if (isModuleFullyChecked(key)) return 'Complet';
    if (isModulePartiallyChecked(key)) return 'Partiel';
    return 'Aucun accès';
};

const getModuleStatusTextClass = (key) => {
    if (isModuleFullyChecked(key)) return 'text-green-600';
    if (isModulePartiallyChecked(key)) return 'text-amber-500';
    return 'text-gray-400';
};

const getChildBorderClass = (key) => {
    if (isChildFullyChecked(key)) return 'border-green-200';
    if (isChildPartiallyChecked(key)) return 'border-amber-200';
    return 'border-gray-200';
};

const getChildDotColor = (key) => {
    if (isChildFullyChecked(key)) return 'bg-green-500';
    if (isChildPartiallyChecked(key)) return 'bg-amber-400';
    return 'bg-gray-300';
};

const getChildBadgeClass = (key) => {
    if (isChildFullyChecked(key)) return 'bg-green-100 text-green-700';
    if (isChildPartiallyChecked(key)) return 'bg-amber-100 text-amber-700';
    return 'bg-gray-100 text-gray-500';
};

const getChildStatusText = (key) => {
    if (isChildFullyChecked(key)) return 'Tous les accès activés';
    if (isChildPartiallyChecked(key)) return 'Accès partiels';
    return 'Aucun accès';
};

// ── Actions ─────────────────────────────────────────────────────────────────

const toggleModule = (key, checked) => {
    const m = findModule(key);
    if (!m) return;
    const routes = getAllModuleRoutes(m);
    const rest = props.selectedPrivileges.filter((r) => !routes.includes(r));
    emit("update:selectedPrivileges", checked ? [...new Set([...rest, ...routes])] : rest);
};

const toggleChild = (key, checked) => {
    const c = findChild(key);
    if (!c) return;
    const routes = getAllChildRoutes(c);
    const rest = props.selectedPrivileges.filter((r) => !routes.includes(r));
    emit("update:selectedPrivileges", checked ? [...new Set([...rest, ...routes])] : rest);
};

const onCheckChild = (key, newKeys) => {
    const c = findChild(key);
    if (!c) return;
    const allRoutes = getAllChildRoutes(c);
    const rest = props.selectedPrivileges.filter((r) => !allRoutes.includes(r));
    // a-tree @check returns { checked, halfChecked } when checkStrictly is false
    const checkedKeys = Array.isArray(newKeys) ? newKeys : (newKeys?.checked || []);
    const newRoutes = checkedKeys.filter((k) => allRoutes.includes(k));
    emit("update:selectedPrivileges", [...new Set([...rest, ...newRoutes])]);
};
</script>

<style scoped>
.custom-tree {
    font-size: 0.8125rem;
}

.custom-tree :deep(.ant-tree-node-content-wrapper) {
    padding: 3px 6px;
    border-radius: 5px;
    transition: background 0.15s ease;
}

.custom-tree :deep(.ant-tree-node-content-wrapper:hover) {
    background-color: rgba(0, 0, 0, 0.04);
}

.custom-tree :deep(.ant-tree-checkbox-checked .ant-tree-checkbox-inner) {
    background-color: #6366f1;
    border-color: #6366f1;
}

.custom-tree :deep(.ant-tree-checkbox-indeterminate .ant-tree-checkbox-inner::after) {
    background-color: #a5b4fc;
}

.custom-tree :deep(.ant-tree-title) {
    color: #374151;
    font-size: 0.8rem;
}

:deep(.ant-checkbox-checked .ant-checkbox-inner) {
    background-color: #6366f1;
    border-color: #6366f1;
}

:deep(.ant-checkbox-indeterminate .ant-checkbox-inner::after) {
    background-color: #a5b4fc;
}
</style>
