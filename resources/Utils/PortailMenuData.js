export function getPortailMenuData() {
    return [
        getItem(
            "Tableau de Bord",
            "portail_dashboard",
            "fa-house",
            "portail.dashboard",
            ["#001c47", "#003080"],
            true
        ),
        getItem(
            "Réservation & Devis",
            "portail_reservation",
            "fa-calendar-plus",
            "portail.reservation",
            ["#4f46e5", "#3730a3"],
            true
        ),
        getItem(
            "Suivi des Expéditions",
            "portail_tracking",
            "fa-location-dot",
            "portail.tracking",
            ["#0891b2", "#0e7490"],
            true
        ),
        getItem(
            "Historique & Documents",
            "portail_historique",
            "fa-clock-rotate-left",
            "portail.historique",
            ["#7c3aed", "#6d28d9"],
            true
        ),
        getItem(
            "Évaluation",
            "portail_evaluation",
            "fa-star",
            "portail.evaluation",
            ["#d97706", "#b45309"],
            true
        ),
        getItem(
            "Réclamations",
            "portail_reclamations",
            "fa-triangle-exclamation",
            "portail.reclamations",
            ["#e11d48", "#be123c"],
            true
        ),
        /*getItem(
            "Mon Compte",
            "portail_compte",
            "fa-user-gear",
            null,
            ["#0f766e", "#0d9488"],
            true,
            "mon_compte"
        ),*/
    ];
}

function getItem(label, key, icon, routeName, gradient, available = true, action = null) {
    return { key, label, icon, routeName, gradient, available, action };
}
