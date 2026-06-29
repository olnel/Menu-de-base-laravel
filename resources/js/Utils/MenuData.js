export function getMenuData() {
    return [
        {
            key: "dashboard",
            label: "Tableau de bord",
            routeName: "dashboard",
            icon: "fa-solid fa-chart-pie",
        },
        {
            key: "flotte",
            label: "Flotte",
            icon: "fa-solid fa-truck",
            children: [
                { key: "vehicule", label: "Véhicules", routeName: "vehicule.index", module: "flotte" },
                { key: "remorque", label: "Remorques", routeName: "remorque.index", module: "flotte" },
            ],
        },
        {
            key: "chauffeur",
            label: "Chauffeurs",
            routeName: "chauffeur.index",
            icon: "fa-solid fa-user",
            module: "chauffeur",
        },
        {
            key: "voyages",
            label: "Voyages",
            icon: "fa-solid fa-route",
            module: "voyage",
            children: [
                { key: "reservation", label: "Réservations", routeName: "reservation.index", module: "voyage" },
                { key: "voyage_list", label: "Voyages", routeName: "voyages.index", module: "voyage" },
            ],
        },
        {
            key: "carburant",
            label: "Carburant",
            icon: "fa-solid fa-gas-pump",
            module: "carburant",
            children: [
                { key: "carburant_cards", label: "Cartes", routeName: "carburant_cards.index", module: "carburant" },
                { key: "carburant_mouvements", label: "Mouvements", routeName: "carburant_mouvements.index", module: "carburant" },
                { key: "carburant_transactions", label: "Transactions", routeName: "carburant_transactions.index", module: "carburant" },
            ],
        },
        {
            key: "magasin",
            label: "Stock",
            icon: "fa-solid fa-warehouse",
            module: "stock",
            children: [
                { key: "magasin_list", label: "Magasins", routeName: "magasin.index", module: "stock" },
                { key: "article_famille", label: "Familles articles", routeName: "article_famille.index", module: "stock" },
                { key: "article", label: "Articles", routeName: "article.index", module: "stock" },
                { key: "article_inventaire", label: "Inventaires", routeName: "article_inventaire.index", module: "stock" },
                { key: "article_flux", label: "Flux", routeName: "article_flux.index", module: "stock" },
                { key: "article_approvisionnement", label: "Approvisionnements", routeName: "article_approvisionnement.index", module: "stock" },
                { key: "article_boncommande", label: "Bons de commande", routeName: "article_boncommande.index", module: "stock" },
            ],
        },
        {
            key: "maintenance",
            label: "Maintenance",
            icon: "fa-solid fa-wrench",
            module: "maintenance",
            children: [
                { key: "reparation_vehicule", label: "Réparations", routeName: "reparation_vehicule.index", module: "maintenance" },
                { key: "pneu_inventaire", label: "Inventaire pneus", routeName: "pneu_inventaire.index", module: "maintenance" },
                { key: "pneu_remplacement", label: "Remplacement pneus", routeName: "pneu_remplacement.index", module: "maintenance" },
                { key: "planning_calendar", label: "Planning", routeName: "planning_calendar.index", module: "maintenance" },
            ],
        },
        {
            key: "tiers",
            label: "Tiers",
            icon: "fa-solid fa-handshake",
            children: [
                { key: "client", label: "Clients", routeName: "client.index", module: "client" },
                { key: "fournisseur", label: "Fournisseurs", routeName: "fournisseur.index", module: "client" },
                { key: "factureclient", label: "Factures clients", routeName: "factureclient.index", module: "client" },
                { key: "devisclient", label: "Devis clients", routeName: "devisclient.index", module: "client" },
                { key: "factureclientreglement", label: "Règlements", routeName: "factureclientreglement.index", module: "client" },
            ],
        },
        {
            key: "tresorerie",
            label: "Trésorerie",
            icon: "fa-solid fa-coins",
            children: [
                { key: "tresorerie", label: "Comptes", routeName: "tresorerie.index" },
                { key: "tresorerie_mouvement", label: "Mouvements", routeName: "tresorerie_mouvement.index" },
                { key: "tresorerieflux", label: "Flux", routeName: "tresorerieflux.index" },
                { key: "postedepense", label: "Postes de dépense", routeName: "postedepense.index" },
            ],
        },
        {
            key: "rh",
            label: "Ressources Humaines",
            icon: "fa-solid fa-users",
            children: [
                { key: "salarie", label: "Salariés", routeName: "salarie.index" },
                { key: "type_salarie", label: "Types", routeName: "type_salarie.index" },
                { key: "prime_config", label: "Configuration primes", routeName: "prime_config.index" },
                { key: "paie", label: "Paie", routeName: "paie.index" },
                { key: "formations", label: "Formations", routeName: "formations.index" },
            ],
        },
        {
            key: "settings",
            label: "Paramètres",
            icon: "fa-solid fa-gear",
            children: [
                { key: "user", label: "Utilisateurs", routeName: "user.index" },
                { key: "group_user", label: "Groupes", routeName: "group_user.index" },
                { key: "paramelement", label: "Éléments véhicule", routeName: "paramelement.index" },
                { key: "libelle_maintenance", label: "Libellés maintenance", routeName: "libelle_maintenance.index" },
                { key: "tarif", label: "Tarifs", routeName: "tarif.index" },
                { key: "infosociete", label: "Info société", routeName: "infosociete.index" },
                { key: "backups", label: "Sauvegardes", routeName: "backups.index" },
            ],
        },
    ];
}

export function findParentByChildKey(childKey) {
    const data = getMenuData();
    for (const parent of data) {
        if (parent.key === childKey) return parent;
        if (parent.children) {
            for (const child of parent.children) {
                if (child.key === childKey) return { ...parent, children: [child] };
            }
        }
    }
    return null;
}
