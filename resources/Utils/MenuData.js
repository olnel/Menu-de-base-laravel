export function getMenuData() {
    return [
        getItem(
            "Tableau de Bord",
            "dashboard",
            "fa-chart-bar",
            null,
            null,
            [],
            ["#eab308", "#ca8a04"]
        ),

        getItem(
            "Facturation et Comptabilité",
            "accueil",
            "fa-file-invoice-dollar",
            null,
            null,
            [
                getItem(
                    "Trésoreries",
                    "tresorerie",
                    "fa-solid fa-car",
                    "tresorerie.index"
                ),
                getItem(
                    "Mouvements de Trésorerie",
                    "tresorerie_mouvement",
                    "fa-solid fa-car",
                    "tresorerie_mouvement.index"
                ),
                getItem(
                    "Flux de Trésorerie ",
                    "tresorerieflux",
                    "fa-solid fa-car",
                    "tresorerieflux.index"
                ),
                getItem(
                    "Factures Fournisseurs",
                    "article_approvisionnement",
                    "fa-solid fa-calendar-check",
                    "article_approvisionnement.index",
                    "stock"
                ),
                getItem(
                    "Factures Clients",
                    "factureclient",
                    "fa-solid fa-calendar-check",
                    "factureclient.index",
                    "client"
                ),
            ],
            ["#6b7280", "#4b5563"]
        ),

        getItem(
            "Gestion des Clients",
            "gestion_financiere",
            "fa-users",
            null,
            "client",
            [
                getItem("Client", "client", "fa-solid fa-user", "client.index", "client"),
                getItem(
                    "Dévis Client",
                    "devisclient",
                    "fa-solid fa-car",
                    "devisclient.index",
                    "client"
                ),
                getItem(
                    "Fournisseur",
                    "fournisseur",
                    "fa-solid fa-calendar-check",
                    "fournisseur.index"
                ),
                getItem(
                    "Réclamations",
                    "reclamation",
                    "fa-solid fa-triangle-exclamation",
                    "reclamation.index",
                    "reclamations"
                ),
            ],
            ["#a855f7", "#7e22ce"]
        ),

        getItem(
            "MAINTENANCE ET ATELIER",
            "article_famille",
            "fa-tools",
            null,
            "stock",
            [
                getItem(
                    "Famille d'article",
                    "article_famille",
                    "fa-solid fa-layer-group",
                    "article_famille.index",
                    "stock"
                ),
                getItem(
                    "Article",
                    "article",
                    "fa-solid fa-calendar-check",
                    "article.index",
                    "stock"
                ),
                getItem(
                    "Bon de Commande",
                    "article_boncommande",
                    "fa-solid fa-calendar-check",
                    "article_boncommande.index",
                    "stock"
                ),
                getItem(
                    "Inventaire",
                    "article_inventaire",
                    "fa-solid fa-calendar-check",
                    "article_inventaire.index",
                    "stock"
                ),
                getItem(
                    "Mouvement",
                    "article_mouvement",
                    "fa-solid fa-calendar-check",
                    "article_mouvement.index",
                    "stock"
                ),
                getItem(
                    "Flux",
                    "article_flux",
                    "fa-solid fa-calendar-check",
                    "article_flux.index",
                    "stock"
                ),
                getItem(
                    "Inventaire Pneu",
                    "pneu_inventaire",
                    "fa-solid fa-calendar-check",
                    "pneu_inventaire.index",
                    "maintenance"
                ),
                getItem(
                    "Pneu Remplacement",
                    "pneu_remplacement",
                    "fa-solid fa-calendar-check",
                    "pneu_remplacement.index",
                    "maintenance"
                ),
            ],
            ["#f97316", "#ea580c"]
        ),

        getItem(
            "JOURNAL DES ÉVÉNEMENTS",
            "tiers",
            "fa-solid fa-file-lines",
            null,
            null,
            [
                getItem(
                    "Logs",
                    "activity_log",
                    "fa-solid fa-screwdriver-wrench",
                    "activity_log.index"
                ),
            ],
            ["#3b82f6", "#2563eb"]
        ),

        getItem(
            "GESTION DU PERSONNEL",
            "salarie_module",
            "fa-user-tie",
            null,
            "chauffeur",
            [
                getItem(
                    "Salarié",
                    "salarie",
                    "fa-solid fa-users",
                    "salarie.index",
                    "chauffeur"
                ),
                getItem(
                    "Paie",
                    "paie",
                    "fa-solid fa-file-invoice-dollar",
                    "paie.index"
                ),
                getItem(
                    "Type",
                    "type_salarie",
                    "fa-solid fa-tags",
                    "type_salarie.index"
                ),
                getItem(
                    "Primes",
                    "prime_config",
                    "fa-solid fa-hand-holding-dollar",
                    "prime_config.index"
                ),
                getItem(
                    "Historique",
                    "salarie_history",
                    "fa-solid fa-clock-rotate-left",
                    "salarie.history_global"
                ),
                getItem(
                    "Formation",
                    "formation",
                    "fa-solid fa-graduation-cap",
                    "formations.index"
                ),
            ],
            ["#f43f5e", "#e11d48"]
        ),

        getItem(
            "GESTIOIN DES VOYAGES & MISSIONS",
            "voyages",
            "fa-car",
            null,
            "voyage",
            [
                getItem(
                    "Liste des Réservations",
                    "reservation",
                    "fa-solid fa-calendar-check",
                    "reservation.index",
                    "voyage"
                ),
                getItem(
                    "Liste des voyages",
                    "voyage_list",
                    "fa-solid fa-calendar-check",
                    "voyages.index",
                    "voyage"
                ),
            ],
            ["#3b82f6", "#2563eb"]
        ),

        getItem(
            "Gestion du Carburant",
            "magasin",
            "fa-gas-pump",
            null,
            "carburant",
            [
                getItem(
                    "Carte Carburant",
                    "carburant-cards",
                    "fa-solid fa-credit-card",
                    "carburant_cards.index",
                    "carburant"
                ),
                getItem(
                    "Transactions",
                    "carburant-transactions",
                    "fa-solid fa-credit-card",
                    "carburant_transactions.index",
                    "carburant"
                ),
                getItem(
                    "Mouvement Carburant",
                    "carburant-mouvements",
                    "fa-solid fa-check-square",
                    "carburant_mouvements.index",
                    "carburant"
                ),
            ],
            ["#ec4899", "#db2777"]
        ),

        getItem(
            "Gestion de la Flotte",
            "gestion_flotte",
            "fa-car-side",
            null,
            "flotte",
            [
                getItem(
                    "Véhicule",
                    "vehicule",
                    "fa-solid fa-car",
                    "vehicule.index",
                    "flotte"
                ),
                getItem(
                    "Rémorque",
                    "remorque",
                    "fa-solid fa-car",
                    "remorque.index",
                    "flotte"
                ),
                getItem(
                    "Maintenance Curative",
                    "reparation_vehicule",
                    "fa-solid fa-screwdriver-wrench",
                    "reparation_vehicule.index",
                    "maintenance"
                ),
                getItem(
                    "Documents Véhicule",
                    "vehiculedocument",
                    "fa-solid fa-car",
                    "vehiculedocument.index",
                    "documents"
                ),
                getItem(
                    "Maintenance Préventif",
                    "calendar-planning",
                    "fa-solid fa-calendar-check",
                    "planning_calendar.index",
                    "maintenance"
                ),
            ],
            ["#22c55e", "#16a34a"]
        ),

        getItem(
            "Carte GPS",
            "gps_map",
            "fa-solid fa-map-location-dot",
            "gps.map.index",
            "gps",
            null,
            ["#0ea5e9", "#0284c7"]
        ),

        /*getItem(
            "Administration",
            "administration_parent",
            "fa-shield-halved",
            null,
            null,
            [
                getItem(
                    "Gestion des Tenants",
                    "administration",
                    "fa-solid fa-users-gear",
                    "tenant.index",
                    null, null, null, true
                ),
                getItem(
                    "Suivi des abonnements",
                    "admin_abonnements",
                    "fa-solid fa-calendar-check",
                    "admin.abonnements.calendrier",
                    null, null, null, true
                ),
                getItem(
                    "Notifications",
                    "notification_settings",
                    "fa-solid fa-bell",
                    "notification_settings.index",
                    null, null, null, true
                ),
            ],
            ["#172A6C", "#1e3a8a"],
            true
        ),*/

        getItem(
            "Mon Abonnement",
            "abonnement_mon",
            "fa-credit-card",
            "abonnement.mon",
            null,
            null,
            ["#059669", "#047857"]
        ),

        getItem(
            "Paramètres",
            "parametre",
            "fa-cog",
            null,
            null,
            [
                getItem(
                    "Groupes Utilisateur",
                    "user-groups",
                    "fa-solid fa-users-gear",
                    "group_user.index"
                ),
                getItem(
                    "Utilisateurs",
                    "user",
                    "fa-solid fa-user-group",
                    "user.index"
                ),
                getItem(
                    "Magasin",
                    "magasin",
                    "fa-solid fa-calendar-check",
                    "magasin.index"
                ),
                getItem(
                    "Info Société",
                    "infosociete",
                    "fa-solid fa-calendar-check",
                    "infosociete.index"
                ),

                getItem(
                    "Éléments Véhicule",
                    "vehicle-elements",
                    "fa-solid fa-car-side",
                    "paramelement.index"
                ),
                getItem(
                    "Maintenances",
                    "maintenances",
                    "fa-solid fa-screwdriver-wrench",
                    "libelle_maintenance.index"
                ),
                getItem(
                    "Tarifs",
                    "tarif",
                    "fa-solid fa-screwdriver-wrench",
                    "tarif.index"
                ),
                getItem(
                    "Backup",
                    "backup",
                    "fa-solid fa-screwdriver-wrench",
                    "backups.index"
                ),
                getItem(
                    "Immobilisation",
                    "immobilisation",
                    "fa-solid fa-screwdriver-wrench",
                    "immobilisation.index"
                ),
                getItem(
                    "Modèles de Documents",
                    "document_config",
                    "fa-solid fa-file-signature",
                    "dynamic.documents.config"
                ),
            ],
            ["#112e79", "#112e79"]
        ),
    ];
}

function getItem(label, key, icon, routeName, module = null, children = null, gradient = null, dnaOnly = false) {
    return {
        key,
        label,
        icon,
        routeName,
        module,
        gradient,
        dnaOnly,
        children: children
            ? children.map((child) => ({
                  ...child,
                  class: "menu-item-child",
              }))
            : null,
    };
}

export function findParentByChildKey(childKey) {
    const menu = getMenuData();

    for (const parent of menu) {
        if (parent.children) {
            const foundChild = parent.children.find(
                (child) => child.key === childKey
            );
            if (foundChild) {
                return parent;
            }
        }
    }

    return null; // si non trouvé
}
