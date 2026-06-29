<template>
    <!-- Composant sans rendu DOM -->
</template>

<script setup>
import { notification } from 'ant-design-vue';
import { onMounted, onBeforeUnmount } from 'vue';
import { usePage } from '@inertiajs/vue3';

// Configuration
const NOTIFICATION_INTERVAL = 2 * 60 * 60 * 1000; // 2 heures
const STORAGE_KEY = 'mnt_notif_last_display';
let timeoutRef = null;

// Méthodes
const getLastDisplayTime = () => {
    try {
        const value = localStorage.getItem(STORAGE_KEY);
        return value ? parseInt(value) : 0;
    } catch {
        return 0;
    }
};

const showNotifications = () => {
    const notifications = usePage().props.maintenanceNotifications;
    if (!notifications?.length) return;

    // Debug: Vérifier le contenu
    console.log('Notifications à afficher:', notifications);

    // Détruire les notifications précédentes
    notification.destroy();

    // Attendre un peu avant d'afficher les nouvelles notifications
    setTimeout(() => {
        notifications.forEach((notif, index) => {
            // Délai échelonné pour éviter les conflits
            setTimeout(() => {
                notification.open({
                    key: `mnt-${notif.id}-${Date.now()}-${index}`, // Clé unique avec index
                    message: `${notif.libelle} - ${notif.vehicule}`,
                    description: `Échéance: ${formatDate(notif.date_maintenance)} (J-${notif.days_remaining})`,
                    duration: 8,
                    style: {
                        backgroundColor: notif.background || '#f6ffed',
                        color: notif.text_color || '#52c41a',
                        borderLeft: `4px solid ${notif.text_color || '#52c41a'}`
                    },
                    placement: 'topRight' // Assurer un placement cohérent
                });
            }, index * 100); // Délai de 100ms entre chaque notification
        });
    }, 100);

    // Enregistrer l'heure d'affichage
    try {
        localStorage.setItem(STORAGE_KEY, Date.now().toString());
    } catch (e) {
        console.warn("Erreur localStorage", e);
    }
};

const shouldDisplay = () => {
    return Date.now() - getLastDisplayTime() >= NOTIFICATION_INTERVAL;
};

const scheduleNextCheck = () => {
    const now = Date.now();
    const nextCheck = getLastDisplayTime() + NOTIFICATION_INTERVAL;
    const delay = Math.max(nextCheck - now, 1000); // Minimum 1s

    timeoutRef = setTimeout(() => {
        if (shouldDisplay()) {
            showNotifications();
        }
        scheduleNextCheck(); // Continuer à vérifier
    }, delay);
};

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('fr-FR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    });
};

// Lifecycle
onMounted(() => {
    // Debug: Vérifier les données au montage
    const notifications = usePage().props.maintenanceNotifications;
    console.log('Données notifications au montage:', notifications);

    // Affichage immédiat au premier chargement si nécessaire
    if (shouldDisplay()) {
        showNotifications();
    }

    // Démarrer le timer pour les vérifications ultérieures
    scheduleNextCheck();
});

onBeforeUnmount(() => {
    clearTimeout(timeoutRef);
    // Nettoyer les notifications restantes
    notification.destroy();
});
</script>
