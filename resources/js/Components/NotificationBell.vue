<script setup>
import { onMounted, onUnmounted, ref } from "vue";
import axios from "axios";

/**
 * Configuration visuelle par type d'alerte.
 * Extensible quand de nouveaux types (retard_voyage, echeance_facture...) seront ajoutés.
 */
const TYPE_CONFIG = {
    overdue: {
        dot:   "bg-red-400",
        badge: "bg-red-50 text-red-700 border-red-100",
        icon:  "fas fa-triangle-exclamation",
    },
    urgent: {
        dot:   "bg-orange-400",
        badge: "bg-orange-50 text-orange-700 border-orange-100",
        icon:  "fas fa-bell",
    },
    warning: {
        dot:   "bg-amber-400",
        badge: "bg-amber-50 text-amber-700 border-amber-100",
        icon:  "fas fa-clock",
    },
};

const MODULE_ICONS = {
    PlanningCalendar: "fas fa-wrench",
    Voyage:           "fas fa-truck",
    FactureClient:    "fas fa-file-invoice-dollar",
    SessionFormation: "fas fa-graduation-cap",
    Formation:        "fas fa-graduation-cap",
};

const POLL_MS = 5 * 60 * 1000;

const open          = ref(false);
const loading       = ref(false);
const notifications = ref([]);
const unreadCount   = ref(0);

const typeConfig  = (type)   => TYPE_CONFIG[type]   ?? TYPE_CONFIG.warning;
const moduleIcon  = (module) => MODULE_ICONS[module] ?? "fas fa-bell";

const countLabel = (n) =>
    n.jours_restants == null     ? ""
    : n.jours_restants < 0       ? `Dépassé de ${Math.abs(n.jours_restants)}j`
    : n.jours_restants === 0     ? "Aujourd'hui"
    :                              `Dans ${n.jours_restants}j`;

const fetchNotifications = async () => {
    loading.value = true;
    try {
        const { data } = await axios.get(route("notifications.index"));
        notifications.value = data.notifications;
        unreadCount.value   = data.unread_count;
    } catch {
        // Silencieux — pas de spam en cas de perte réseau
    } finally {
        loading.value = false;
    }
};

const markAsRead = async (notification) => {
    await axios.patch(route("notifications.read", notification.id));
    notifications.value = notifications.value.filter((n) => n.id !== notification.id);
    unreadCount.value   = Math.max(0, unreadCount.value - 1);
};

const markAllAsRead = async () => {
    await axios.patch(route("notifications.read_all"));
    notifications.value = [];
    unreadCount.value   = 0;
    open.value          = false;
};

let poller = null;
onMounted(() => {
    fetchNotifications();
    poller = setInterval(fetchNotifications, POLL_MS);
});
onUnmounted(() => clearInterval(poller));
</script>

<template>
    <a-popover
        v-model:open="open"
        placement="bottomRight"
        trigger="click"
        :arrow="false"
    >
        <!-- ── Déclencheur ─────────────────────────────────────────────── -->
        <a-tooltip title="Alertes">
            <a-badge :count="unreadCount" :overflow-count="9" color="orange">
                <a-button
                    type="text"
                    :class="[
                        'group flex items-center justify-center text-white',
                        'hover:!text-secondary hover:!bg-white/5 border-white/20 hover:border-white/50 rounded-md',
                        'transition-all duration-300 !p-3 !h-9 !w-9',
                        unreadCount > 0 ? 'animate-pulse' : '',
                    ]"
                >
                    <font-awesome-icon
                        icon="fas fa-bell"
                        class="text-lg group-hover:scale-110 transition-transform duration-300"
                    />
                </a-button>
            </a-badge>
        </a-tooltip>

        <!-- ── Panneau ─────────────────────────────────────────────────── -->
        <template #content>
            <div class="w-80">

                <!-- En-tête -->
                <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100">
                    <div class="flex items-center gap-2">
                        <font-awesome-icon icon="fas fa-bell" class="text-gray-500 text-sm" />
                        <span class="font-bold text-gray-800 text-sm">Alertes</span>
                        <span
                            v-if="unreadCount"
                            class="bg-orange-100 text-orange-700 text-xs font-bold rounded-full px-1.5 py-0.5"
                        >
                            {{ unreadCount }}
                        </span>
                    </div>
                    <button
                        v-if="unreadCount > 0"
                        class="text-xs text-blue-600 hover:text-blue-800 font-medium transition-colors"
                        @click="markAllAsRead"
                    >
                        Tout lire
                    </button>
                </div>

                <!-- Liste -->
                <div class="max-h-80 overflow-y-auto divide-y divide-gray-50">

                    <div v-if="loading && !notifications.length" class="py-8 flex justify-center">
                        <a-spin size="small" />
                    </div>

                    <div
                        v-else-if="!notifications.length"
                        class="py-8 flex flex-col items-center gap-2 text-gray-400"
                    >
                        <font-awesome-icon icon="fas fa-check-circle" class="text-2xl text-green-400" />
                        <span class="text-xs">Aucune alerte en cours</span>
                    </div>

                    <div
                        v-for="n in notifications"
                        :key="n.id"
                        class="flex items-start gap-3 px-4 py-3 hover:bg-gray-50 transition-colors cursor-pointer group"
                        @click="markAsRead(n)"
                    >
                        <!-- Icône module -->
                        <div class="flex-shrink-0 mt-0.5">
                            <div :class="['w-1.5 h-1.5 rounded-full mt-1.5', typeConfig(n.type).dot]"></div>
                        </div>

                        <!-- Corps -->
                        <div class="flex-1 min-w-0">
                            <!-- Ligne 1 : immat / titre + badge alerte -->
                            <div class="flex items-center gap-2 flex-wrap">
                                <font-awesome-icon
                                    :icon="moduleIcon(n.module)"
                                    class="text-gray-400 text-[10px]"
                                />
                                <span class="font-semibold text-gray-800 text-xs">
                                    {{ n.data?.immatriculation ?? n.titre }}
                                </span>
                                <span
                                    class="inline-flex items-center gap-1 rounded-full border px-2 py-0.5 text-[10px] font-bold"
                                    :class="typeConfig(n.type).badge"
                                >
                                    <font-awesome-icon :icon="typeConfig(n.type).icon" class="text-[9px]" />
                                    {{ countLabel(n) }}
                                </span>
                            </div>

                            <!-- Ligne 2 : libellé -->
                            <p class="text-xs text-gray-600 mt-0.5 truncate">
                                {{ n.data?.libelle ?? n.message }}
                            </p>

                            <!-- Ligne 3 : date échéance -->
                            <p v-if="n.data?.date_maintenance" class="text-[10px] text-gray-400 mt-0.5">
                                Échéance : {{ n.data.date_maintenance }}
                            </p>
                        </div>

                        <!-- Croix "marquer lu" -->
                        <font-awesome-icon
                            icon="fas fa-xmark"
                            class="text-gray-300 group-hover:text-gray-500 text-xs mt-1 flex-shrink-0 transition-colors"
                        />
                    </div>
                </div>

                <!-- Pied -->
                <div class="px-4 py-2.5 border-t border-gray-100 bg-gray-50/60">
                    <a
                        :href="route('planning_calendar.index')"
                        class="text-xs text-blue-600 hover:text-blue-800 font-medium transition-colors"
                    >
                        Voir le planning complet →
                    </a>
                </div>
            </div>
        </template>
    </a-popover>
</template>
