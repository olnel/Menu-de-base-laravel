<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class UserGroup extends Basemodel
{
    use HasFactory;


    protected $casts = [
        'privileges' => 'array',
        'routes' => 'array',
    ];
    // public function scopeFilter($query)
    // {
    //     $request = app(Request::class);


    /**
     * Scope pour la recherche
     */
    public function scopeFilter($query)
    {
        $request = resolve(Request::class);
        if (isset($request->search) && !empty($request->search)) {
            $search = $request->search;
            $query->where('name', 'LIKE', "%$search%");
        }
        return $query;
    }

    public static $privileges = [

        [
            "title" => "Tableau de Bord",
            "key" => "dashboards",
            "icon" => "fa-chart-bar",
            "gradient" => ["#eab308", "#ca8a04"],
            "routes" => [],
            "children" => [
                [
                    "title" => "Accéder au Tableau de Bord",
                    "key" => "dashboard",
                    "routes" => [],
                    "children" => [
                        [
                            "title" => "Consulter le Planning général",
                            "key" => "calendrier.index",
                            "routes" => ["calendrier.index"],
                        ],
                        [
                            "title" => "Tableau de Bord Voyage",
                            "key" => "dashboard.voyage",
                            "routes" => ["dashboard.voyage"],
                        ],
                        [
                            "title" => "Tableau de Bord Véhicule",
                            "key" => "dashboard.vehicule",
                            "routes" => ["dashboard.vehicule"],
                        ],
                        [
                            "title" => "Tableau de Bord Comptabilité",
                            "key" => "dashboard.comptablite",
                            "routes" => ["dashboard.comptablite"],
                        ],
                        [
                            "title" => "Tableau de Bord Carburant",
                            "key" => "dashboard.carburant",
                            "routes" => ["dashboard.carburant"],
                        ],
                        [
                            "title" => "Tableau de Bord Pneu",
                            "key" => "dashboard.pneu",
                            "routes" => ["dashboard.pneu"],
                        ],
                        [
                            "title" => "Tableau de Bord Chauffeur",
                            "key" => "dashboard.chauffeur",
                            "routes" => ["dashboard.chauffeur"],
                        ],
                        [
                            "title" => "Tableau de Bord Rentabilité",
                            "key" => "dashboard.rentabilite_module",
                            "routes" => [],
                            "children" => [
                                [
                                    "title" => "Consulter la Rentabilité",
                                    "key" => "dashboard.rentabilite",
                                    "routes" => ["dashboard.rentabilite"],
                                ],
                                [
                                    "title" => "Exporter la Rentabilité",
                                    "key" => "dashboard.rentabilite.export",
                                    "routes" => ["dashboard.rentabilite.export"],
                                ],
                            ],
                        ],
                    ],
                    "with" => ["calendrier"],
                ],
            ],
        ],

        [
            "title" => "Facturation et Comptabilité",
            "key" => "accueil",
            "icon" => "fa-file-invoice-dollar",
            "gradient" => ["#6b7280", "#4b5563"],
            "routes" => [],
            "children" => [
                [
                    "title" => "Trésorerie",
                    "key" => "tresorerie",
                    "routes" => [],
                    "children" => [
                        [
                            "title" => "Voir la liste des trésoreries",
                            "key" => "tresorerie.index",
                            "routes" => ["tresorerie.index"],
                        ],
                        [
                            "title" => "Ajouter une nouvelle trésorerie",
                            "key" => "tresorerie.store",
                            "routes" => ["tresorerie.store"],
                        ],
                        [
                            "title" => "Modifier une trésorerie",
                            "key" => "tresorerie.update",
                            "routes" => ["tresorerie.update"],
                        ],
                        [
                            "title" => "Afficher les mouvements d'une trésorerie",
                            "key" => "tresorerie.mouvement",
                            "routes" => ["tresorerie.mouvement"],
                        ],
                        [
                            "title" => "Voir l'historique d'une trésorerie",
                            "key" => "tresorerie.historique",
                            "routes" => ["tresorerie.historique"],
                        ],
                        [
                            "title" => "Supprimer une trésorerie",
                            "key" => "tresorerie.destroy",
                            "routes" => ["tresorerie.destroy"],
                        ],
                    ],
                    "with" => ["tresorerie"],
                ],
                [
                    "title" => "Mouvement du Trésorerie",
                    "key" => "tresorerie_mouvement",
                    "routes" => [],
                    "children" => [
                        [
                            "title" => "Voir la liste des mouvement de tresorerie",
                            "key" => "tresorerie_mouvement.index",
                            "routes" => ["tresorerie_mouvement.index"]
                        ],
                        [
                            "title" => "Créer un mouvement de tresorerie",
                            "key" => "tresorerie_mouvement.store",
                            "routes" => ["tresorerie_mouvement.store"]
                        ],
                        [
                            "title" => "modifier un mouvement d'une tresorerie",
                            "key" => "tresorerie_mouvement.update",
                            "routes" => ["tresorerie_mouvement.update"]
                        ],
                        [
                            "title" => "Annuler un mouvement",
                            "key" => "tresorerie_mouvement.destroy",
                            "routes" => ["tresorerie_mouvement.destroy"]
                        ]
                    ]
                ],
                [
                    "title" => "Flux des Trésoreries",
                    "key" => "tresorerie_flux",
                    "routes" => [],
                    "children" => [
                        [
                            "title" => "Voir tout les flux de Trésorerie ",
                            "key" => "tresorerieflux.index",
                            "routes" => ["tresorerieflux.index"]
                        ]
                    ]
                ],

                [
                    "title" => "Factures Fournisseurs",
                    "key" => "article_approvisionnement",
                    "routes" => [],
                    "children" => [
                        [
                            "title" => "Voir les factures fournisseurs",
                            "key" => "article_approvisionnement.index",
                            "routes" => ["article_approvisionnement.index"],
                        ],
                        [
                            "title" => "Creer un approvisionnement",
                            "key" => "article_approvisionnement.store",
                            "routes" => ["article_approvisionnement.store"],
                        ],
                        [
                            "title" => "Modifier un approvisionnement",
                            "key" => "article_approvisionnement.update",
                            "routes" => ["article_approvisionnement.update"],
                        ],
                        [
                            "title" => "Annuler un approvisionnement",
                            "key" => "article_approvisionnement.destroy",
                            "routes" => ["article_approvisionnement.destroy"],
                        ]
                    ],
                    "with" => ["article_approvisionnement"],
                ],
                [
                    "title" => "Factures Clients",
                    "key" => "factureclient",
                    "routes" => [
                        "factureclient.index",
                        "factureclient.update",
                        "factureclient.generer_facture",
                        "factureclient.envoi_email",
                        "factureclient.regler_facture",
                        "factureclient.historique_reglement",
                        "factureclient.destroy",
                    ],
                    "children" => [
                        [
                            "title" => "Voir la liste des factures clients",
                            "key" => "factureclient.index",
                            "routes" => ["factureclient.index"],
                        ],
                        [
                            "title" => "Modifier une facture",
                            "key" => "factureclient.update",
                            "routes" => ["factureclient.update"],
                        ],
                        [
                            "title" => "Generer une facture",
                            "key" => "factureclient.generer_facture",
                            "routes" => ["factureclient.generer_facture"],
                        ],
                        [
                            "title" => "Envoyer Email au client",
                            "key" => "factureclient.envoi_email",
                            "routes" => ["factureclient.envoi_email"],
                        ],
                        [
                            "title" => "Regler une facture",
                            "key" => "factureclient.regler_facture",
                            "routes" => ["factureclient.regler_facture"]
                        ],
                        [
                            "title" => "Voir l'historique des reglements",
                            "key" => "factureclient.historique_reglement",
                            "routes" => ["factureclient.historique_reglement"]
                        ],
                        [
                            "title" => "Supprimer une facture",
                            "key" => "factureclient.destroy",
                            "routes" => ["factureclient.destroy"]
                        ],
                    ],
                    "with" => ["factureclient"],
                ],
                [
                    "title" => "Postes de Dépense",
                    "key" => "postedepense",
                    "routes" => [],
                    "children" => [
                        [
                            "title" => "Voir les postes de dépense",
                            "key" => "postedepense.index",
                            "routes" => ["postedepense.index"],
                        ],
                        [
                            "title" => "Ajouter un poste de dépense",
                            "key" => "postedepense.store",
                            "routes" => ["postedepense.store"],
                        ],
                        [
                            "title" => "Modifier un poste de dépense",
                            "key" => "postedepense.update",
                            "routes" => ["postedepense.update"],
                        ],
                        [
                            "title" => "Supprimer un poste de dépense",
                            "key" => "postedepense.destroy",
                            "routes" => ["postedepense.destroy"],
                        ],
                    ],
                    "with" => ["postedepense"],
                ],
            ],
        ],

        [
            "title" => "Gestion des Clients",
            "key" => "gestion_financiere",
            "icon" => "fa-users",
            "gradient" => ["#a855f7", "#7e22ce"],
            "routes" => [],
            "children" => [
                [
                    "title" => "Clients",
                    "key" => "client",
                    "routes" => [],
                    "children" => [
                        [
                            "title" => "Voir tout les clients",
                            "key" => "client.index",
                            "routes" => ["client.index"],
                        ],
                        [
                            "title" => "Ajouter un nouveau client",
                            "key" => "client.store",
                            "routes" => ["client.store"],
                        ],
                        [
                            "title" => "Modifier un client",
                            "key" => "client.update",
                            "routes" => ["client.update"],
                        ],
                        [
                            "title" => "Supprimer un client",
                            "key" => "client.destroy",
                            "routes" => ["client.destroy"],
                        ],
                        [
                            "title" => "Importer des clients",
                            "key" => "client.import",
                            "routes" => ["client.import"],
                        ]
                    ],
                    "with" => ["client"],
                ],
                [
                    "title" => "Dévis Client",
                    "key" => "devisclient",
                    "routes" => [],
                    "children" => [
                        [
                            "title" => "Voir les devis clients",
                            "key" => "devisclient.index",
                            "routes" => ["devisclient.index"],
                        ],
                        [
                            "title" => "Créer un devis client",
                            "key" => "devisclient.store",
                            "routes" => ["devisclient.store"],
                        ],
                        [
                            "title" => "Modifier un devis client",
                            "key" => "devisclient.update",
                            "routes" => ["devisclient.update"],
                        ],
                        [
                            "title" => "Imprimer un devis ",
                            "key" => "devisclient.impression",
                            "routes" => ["devisclient.impression"],
                        ],
                        [
                            "title" => "Envoyer un devis par email ",
                            "key" => "devisclient.envoi_email",
                            "routes" => ["devisclient.envoi_email"],
                        ],
                        [
                            "title" => "Supprimer un devis client",
                            "key" => "devisclient.destroy",
                            "routes" => ["devisclient.destroy"],
                        ],
                    ],
                    "with" => ["devisclient"],
                ],
                [
                    "title" => "Fournisseurs",
                    "key" => "fournisseur",
                    "routes" => [],
                    "children" => [
                        [
                            "title" => "Voir les fournisseurs",
                            "key" => "fournisseur.index",
                            "routes" => ["fournisseur.index"],
                        ],
                        [
                            "title" => "Créer un fournisseur",
                            "key" => "fournisseur.store",
                            "routes" => ["fournisseur.store"],
                        ],

                        [
                            "title" => "Modifier un fournisseur",
                            "key" => "fournisseur.update",
                            "routes" => ["fournisseur.update"],
                        ],
                        [
                            "title" => "Supprimer un fournisseur",
                            "key" => "fournisseur.destroy",
                            "routes" => ["fournisseur.destroy"],
                        ],
                    ],
                    "with" => ["fournisseur"],
                ],
                [
                    "title" => "Réclamations",
                    "key" => "reclamation",
                    "routes" => [],
                    "children" => [
                        [
                            "title" => "Voir les réclamations",
                            "key" => "reclamation.index",
                            "routes" => ["reclamation.index"],
                        ],
                        [
                            "title" => "Mettre à jour une réclamation",
                            "key" => "reclamation.update",
                            "routes" => ["reclamation.update"],
                        ],
                        [
                            "title" => "Supprimer une réclamation",
                            "key" => "reclamation.destroy",
                            "routes" => ["reclamation.destroy"],
                        ],
                    ],
                    "with" => ["reclamation"],
                ],
            ],
        ],




        [
            "title" => "Gestion de Garage",
            "key" => "articles",
            "icon" => "fa-tools",
            "gradient" => ["#f97316", "#ea580c"],
            "routes" => [],
            "children" => [
                [
                    "title" => "Articles",
                    "key" => "article",
                    "routes" => [],
                    "children" => [
                        [
                            "title" => "Voir les listes des articles",
                            "key" => "article.index",
                            "routes" => ["article.index"],
                        ],
                        [
                            "title" => "Enregistré Nouveau articles",
                            "key" => "article.store",
                            "routes" => ["article.store"],
                        ],
                        [
                            "title" => "Modifier un article",
                            "key" => "article.update",
                            "routes" => ["article.update"],
                        ],
                        [
                            "title" => "Supprimer un article",
                            "key" => "article.destroy",
                            "routes" => ["article.destroy"],
                        ],
                    ],
                    "with" => ["article"],
                ],

                [
                    "title" => "Bon de Commande",
                    "key" => "article_boncommande",
                    "routes" => [],
                    "children" => [
                        [
                            "title" => "Voir les bons de commande",
                            "key" => "article_boncommande.index",
                            "routes" => ["article_boncommande.index"],
                        ],
                        [
                            "title" => "Créer un nouveau bon de commande",
                            "key" => "article_boncommande.store",
                            "routes" => ["article_boncommande.store"],
                        ],
                        [
                            "title" => "Modifier un bon de commande",
                            "key" => "article_boncommande.update",
                            "routes" => ["article_boncommande.update"],
                        ],
                        [
                            "title" => "Imprimer un bon de commande",
                            "key" => "article_boncommande.impression",
                            "routes" => ["article_boncommande.impression"],
                        ],
                        [
                            "title" => "Générer un approvisionnement",
                            "key" => "article_boncommande.appro",
                            "routes" => ["article_boncommande.appro"],
                        ],
                        [
                            "title" => "Envoyer email au fournisseur",
                            "key" => "article_boncommande.envoi_email",
                            "routes" => ["article_boncommande.envoi_email"],
                        ],
                        [
                            "title" => "Supprimer un bon de commande",
                            "key" => "article_boncommande.destroy",
                            "routes" => ["article_boncommande.destroy"],
                        ],
                    ],
                    "with" => ["article_boncommande"],
                ],

                [
                    "title" => "Inventaire",
                    "key" => "article_inventaire",
                    "routes" => [],
                    "children" => [
                        [
                            "title" => "Voir les inventaires",
                            "key" => "article_inventaire.index",
                            "routes" => ["article_inventaire.index"],
                        ],
                        [
                            "title" => "Créer un nouvel inventaire",
                            "key" => "article_inventaire.store",
                            "routes" => ["article_inventaire.store"],
                        ],
                    ],
                    "with" => ["article_inventaire"],
                ],

                [
                    "title" => "Mouvement",
                    "key" => "article_mouvement",
                    "routes" => [],
                    "children" => [
                        [
                            "title" => "Voir les mouvements",
                            "key" => "article_mouvement.index",
                            "routes" => ["article_mouvement.index"],
                        ],
                        [
                            "title" => "Créer un nouveau mouvement",
                            "key" => "article_mouvement.store",
                            "routes" => ["article_mouvement.store"],
                        ],
                        [
                            "title" => "Modifier un mouvement",
                            "key" => "article_mouvement.update",
                            "routes" => ["article_mouvement.update"],
                        ],
                        [
                            "title" => "Supprimer un mouvement",
                            "key" => "article_mouvement.destroy",
                            "routes" => ["article_mouvement.destroy"],
                        ],
                    ],
                    "with" => ["article_mouvement"],
                ],

                [
                    "title" => "Flux",
                    "key" => "article_flux",
                    "routes" => [],
                    "children" => [
                        [
                            "title" => "Voir les flux",
                            "key" => "article_flux.index",
                            "routes" => ["article_flux.index"],
                        ],
                    ],
                    "with" => ["article_flux"],
                ],
            ],
        ],





        [
            "title" => "Gestion des Chauffeurs",
            "key" => "chauffeurs",
            "icon" => "fa-user-tie",
            "gradient" => ["#3b82f6", "#2563eb"],
            "routes" => [],
            "children" => [
                [
                    "title" => "Gérer les Chauffeurs",
                    "key" => "chauffeur",
                    "routes" => [],
                    "children" => [
                        [
                            "title" => "Voir la liste complète des chauffeurs",
                            "key" => "chauffeur.index",
                            "routes" => ["chauffeur.index"],
                        ],
                        [
                            "title" => "Ajouter un nouveau chauffeur",
                            "key" => "chauffeur.store",
                            "routes" => ["chauffeur.store"],
                        ],
                        [
                            "title" => "Modifier les informations d'un chauffeur",
                            "key" => "chauffeur.update",
                            "routes" => ["chauffeur.update"],
                        ],
                        [
                            "title" => "Supprimer un chauffeur",
                            "key" => "chauffeur.destroy",
                            "routes" => ["chauffeur.destroy"],
                        ],
                        [
                            "title" => "Consulter le profil complet d'un chauffeur",
                            "key" => "chauffeur.informations",
                            "routes" => ["chauffeur.informations"],
                        ],
                        [
                            "title" => "Voir les documents d'un chauffeur",
                            "key" => "chauffeurdocument.index",
                            "routes" => ["chauffeurdocument.index"],
                        ],
                        [
                            "title" => "Téléverser un nouveau document",
                            "key" => "chauffeurdocument.store",
                            "routes" => ["chauffeurdocument.store"],
                        ],
                        [
                            "title" => "Mettre à jour un document existant",
                            "key" => "chauffeurdocument.update",
                            "routes" => ["chauffeurdocument.update"],
                        ],
                        [
                            "title" => "Supprimer un document",
                            "key" => "chauffeurdocument.destroy",
                            "routes" => ["chauffeurdocument.destroy"],
                        ],
                        [
                            "title" => "Importer des chauffeurs",
                            "key" => "chauffeur.import",
                            "routes" => ["chauffeur.import"],
                        ],
                    ],
                    "with" => ["chauffeur"],
                ],
            ],
        ],

        [
            "title" => "Gestion des Salariés",
            "key" => "salarie_module",
            "icon" => "fa-user-tie",
            "gradient" => ["#f43f5e", "#e11d48"],
            "routes" => [],
            "children" => [
                [
                    "title" => "Salariés",
                    "key" => "salarie",
                    "routes" => [],
                    "children" => [
                        [
                            "title" => "Voir la liste des salariés",
                            "key" => "salarie.index",
                            "routes" => ["salarie.index"],
                        ],
                        [
                            "title" => "Ajouter un salarié",
                            "key" => "salarie.store",
                            "routes" => ["salarie.store"],
                        ],
                        [
                            "title" => "Modifier un salarié",
                            "key" => "salarie.update",
                            "routes" => ["salarie.update"],
                        ],
                        [
                            "title" => "Supprimer un salarié",
                            "key" => "salarie.destroy",
                            "routes" => ["salarie.destroy"],
                        ],
                    ],
                    "with" => ["salarie"],
                ],
                [
                    "title" => "Type de Salariés",
                    "key" => "type_salarie",
                    "routes" => [],
                    "children" => [
                        [
                            "title" => "Voir les types de salariés",
                            "key" => "type_salarie.index",
                            "routes" => ["type_salarie.index"],
                        ],
                        [
                            "title" => "Ajouter un type",
                            "key" => "type_salarie.store",
                            "routes" => ["type_salarie.store"],
                        ],
                        [
                            "title" => "Modifier un type",
                            "key" => "type_salarie.update",
                            "routes" => ["type_salarie.update"],
                        ],
                        [
                            "title" => "Supprimer un type",
                            "key" => "type_salarie.destroy",
                            "routes" => ["type_salarie.destroy"],
                        ],
                    ],
                    "with" => ["type_salarie"],
                ],
                [
                    "title" => "Historique des Actions",
                    "key" => "salarie.history_global",
                    "routes" => ["salarie.history_global"],
                ],
                [
                    "title" => "Modèles de Documents",
                    "key" => "document_config",
                    "routes" => [],
                    "children" => [
                        [
                            "title" => "Configurer les modèles",
                            "key" => "dynamic.documents.config",
                            "routes" => ["dynamic.documents.config"],
                        ],
                        [
                            "title" => "Enregistrer un modèle",
                            "key" => "dynamic.documents.config.store",
                            "routes" => ["dynamic.documents.config.store"],
                        ],
                    ],
                ],
                [
                    "title" => "Gestion de la Paie",
                    "key" => "paie",
                    "routes" => [],
                    "children" => [
                        [
                            "title" => "Voir la liste des fiches de paie",
                            "key" => "paie.index",
                            "routes" => ["paie.index"],
                        ],
                        [
                            "title" => "Générer les fiches de paie",
                            "key" => "paie.store",
                            "routes" => ["paie.store"],
                        ],
                        [
                            "title" => "Modifier une fiche de paie",
                            "key" => "paie.update",
                            "routes" => ["paie.update"],
                        ],
                        [
                            "title" => "Payer une fiche de paie",
                            "key" => "paie.pay",
                            "routes" => ["paie.pay"],
                        ],
                        [
                            "title" => "Imprimer une fiche de paie",
                            "key" => "paie.print",
                            "routes" => ["paie.print"],
                        ],
                        [
                            "title" => "Exporter les fiches de paie",
                            "key" => "paie.export",
                            "routes" => ["paie.export"],
                        ],
                        [
                            "title" => "Supprimer une fiche de paie",
                            "key" => "paie.destroy",
                            "routes" => ["paie.destroy"],
                        ],
                    ],
                    "with" => ["paie"],
                ],
                [
                    "title" => "Configuration des Primes",
                    "key" => "prime_config",
                    "routes" => [],
                    "children" => [
                        [
                            "title" => "Voir les configurations de primes",
                            "key" => "prime_config.index",
                            "routes" => ["prime_config.index"],
                        ],
                        [
                            "title" => "Ajouter une configuration de prime",
                            "key" => "prime_config.store",
                            "routes" => ["prime_config.store"],
                        ],
                        [
                            "title" => "Modifier une configuration de prime",
                            "key" => "prime_config.update",
                            "routes" => ["prime_config.update"],
                        ],
                        [
                            "title" => "Supprimer une configuration de prime",
                            "key" => "prime_config.destroy",
                            "routes" => ["prime_config.destroy"],
                        ],
                    ],
                    "with" => ["prime_config"],
                ],
                [
                    "title" => "Formations",
                    "key" => "formations",
                    "routes" => [],
                    "children" => [
                        [
                            "title" => "Voir la liste des formations",
                            "key" => "formations.index",
                            "routes" => ["formations.index"],
                        ],
                        [
                            "title" => "Ajouter une formation",
                            "key" => "formations.store",
                            "routes" => ["formations.store"],
                        ],
                        [
                            "title" => "Modifier une formation",
                            "key" => "formations.update",
                            "routes" => ["formations.update"],
                        ],
                        [
                            "title" => "Supprimer une formation",
                            "key" => "formations.destroy",
                            "routes" => ["formations.destroy"],
                        ],
                        [
                            "title" => "Créer une session de formation",
                            "key" => "formations.sessions.store",
                            "routes" => ["formations.sessions.store"],
                        ],
                        [
                            "title" => "Voir les participants d'une session",
                            "key" => "formations.derniere_session.participants",
                            "routes" => ["formations.derniere_session.participants"],
                        ],
                        [
                            "title" => "Voir les participants d'une formation",
                            "key" => "formations.sessions.participants",
                            "routes" => ["formations.sessions.participants"],
                        ],
                    ],
                    "with" => ["formations"],
                ],
            ],
        ],

        [
            "title" => "Gestion des Voyages",
            "key" => "voyages",
            "gradient" => ["#8f8f", "#8f8d"],
            "routes" => [],
            "children" => [
                [
                    "title" => "Gestion des Réservations",
                    "key" => "reservation",
                    "routes" => [],
                    "children" => [
                        [
                            "title" => "Voir la liste des réservations",
                            "key" => "reservation.index",
                            "routes" => ["reservation.index"],
                        ],
                        [
                            "title" => "Créer une nouvelle réservation",
                            "key" => "reservation.store",
                            "routes" => ["reservation.store"],
                        ],
                        [
                            "title" => "Modifier une réservation",
                            "key" => "reservation.update",
                            "routes" => ["reservation.update"],
                        ],
                        [
                            "title" => "Supprimer une réservation",
                            "key" => "reservation.destroy",
                            "routes" => ["reservation.destroy"],
                        ],
                        [
                            "title" => "Voir les détails",
                            "key" => "reservation.details",
                            "routes" => ["reservation.details"],
                        ],
                        [
                            "title" => "Plannifier les voyages",
                            "key" => "voyages.sync",
                            "routes" => ["voyages.sync"],
                        ]
                    ],
                    "with" => ["reservation"],
                ],

                [
                    "title" => "Gestion des Voyages",
                    "key" => "voyages",
                    "routes" => [],
                    "children" => [
                        [
                            "title" => "Voir la liste des voyages",
                            "key" => "voyages.index",
                            "routes" => ["voyages.index"],
                        ],
                        [
                            "title" => "Modifier un voyage",
                            "key" => "voyages.update",
                            "routes" => ["voyages.update"],
                        ],
                        [
                            "title" => "Facturer un voyage",
                            "key" => "voyages.facture",
                            "routes" => ["voyages.facture"],
                        ],
                        [
                            "title" => "Supprimer un voyage",
                            "key" => "voyages.destroy",
                            "routes" => ["voyages.destroy"],
                        ],
                    ],
                    "with" => ["voyages"],
                ],
            ],
        ],

        [
            "title" => "Gestion du Carburant",
            "key" => "carburant",
            "icon" => "fa-gas-pump",
            "gradient" => ["#ec4899", "#db2777"],
            "routes" => [],
            "children" => [
                [
                    "title" => "Carte carburant",
                    "key" => "carburant_cards",
                    "routes" => [],
                    "children" => [
                        [
                            "title" => "Voir la liste des cartes carburant",
                            "key" => "carburant_cards.index",
                            "routes" => ["carburant_cards.index"],
                        ],
                        [
                            "title" => "Ajouter une nouvelle carte carburant",
                            "key" => "carburant_cards.store",
                            "routes" => ["carburant_cards.store"],
                        ],
                        [
                            "title" => "Modifier une carte carburant",
                            "key" => "carburant_cards.update",
                            "routes" => ["carburant_cards.update"],
                        ],
                        [
                            "title" => "Désactiver/Activer une carte carburant",
                            "key" => "updateCardStatus",
                            "routes" => ["updateCardStatus"],
                        ],
                        [
                            "title" => "Supprimer une carte carburant",
                            "key" => "carburant_cards.destroy",
                            "routes" => ["carburant_cards.destroy"],
                        ],
                        [
                            "title" => "Effectuer un ajustement de solde",
                            "key" => "carburant_cards.ajustement",
                            "routes" => ["carburant_cards.ajustement"],
                        ],
                    ],
                    "with" => ["carburant_cards"],
                ],
                [
                    "title" => "Transactions carburant",
                    "key" => "carburant_transactions",
                    "routes" => [],
                    "children" => [
                        [
                            "title" => "Voir la liste des transactions",
                            "key" => "carburant_transactions.index",
                            "routes" => ["carburant_transactions.index"],
                        ],
                        [
                            "title" => "Créer une nouvelle transaction",
                            "key" => "carburant_transactions.store",
                            "routes" => ["carburant_transactions.store"],
                        ],
                        [
                            "title" => "Modifier une transaction",
                            "key" => "carburant_transactions.update",
                            "routes" => ["carburant_transactions.update"],
                        ],
                        [
                            "title" => "Supprimer une transaction",
                            "key" => "carburant_transactions.destroy",
                            "routes" => ["carburant_transactions.destroy"],
                        ],
                    ],
                    "with" => ["carburant_transactions"],
                ],
                [
                    "title" => "Mouvements carburant",
                    "key" => "carburant_mouvements",
                    "routes" => [],
                    "children" => [
                        [
                            "title" => "Voir la liste des mouvements",
                            "key" => "carburant_mouvements.index",
                            "routes" => ["carburant_mouvements.index"],
                        ],
                    ],
                ]
            ],
        ],

        [
            "title" => "Gestion de la Flotte",
            "key" => "gestion_flotte",
            "icon" => "fa-car-side",
            "gradient" => ["#22c55e", "#16a34a"],
            "routes" => [],
            "children" => [
                [
                    "title" => "Administrer le parc véhicules",
                    "key" => "vehicule",
                    "routes" => [],
                    "children" => [
                        [
                            "title" => "Voir les listes du véhicule",
                            "key" => "vehicule.index",
                            "routes" => ["vehicule.index"],
                        ],
                        [
                            "title" => "Enregistrer un nouveau véhicule",
                            "key" => "vehicule.store",
                            "routes" => ["vehicule.store"],
                        ],
                        [
                            "title" => "Modifier les caractéristiques d'un véhicule",
                            "key" => "vehicule.update",
                            "routes" => ["vehicule.update"],
                        ],
                        [
                            "title" => "Retirer un véhicule du parc",
                            "key" => "vehicule.destroy",
                            "routes" => ["vehicule.destroy"],
                        ],
                        [
                            "title" => "Consulter la fiche technique complète",
                            "key" => "vehicule.show",
                            "routes" => ["vehicule.show"],
                        ],
                        [
                            "title" => "Importer des vehicules",
                            "key" => "import.vehicule.standard",
                            "routes" => ["import.vehicule.standard"]
                        ],



                        [
                            "title" => "Voir les équipements du véhicule",
                            "key" => "vehicule_element.index",
                            "routes" => ["vehicule_element.index"],
                        ],
                        [
                            "title" => "Ajouter un nouvel équipement",
                            "key" => "vehicule_element.store",
                            "routes" => ["vehicule_element.store"],
                        ],



                        [
                            "title" => "Parcourir les photos du véhicule",
                            "key" => "vehicule_photo.index",
                            "routes" => ["vehicule_photo.index"],
                        ],
                        [
                            "title" => "Ajouter une nouvelle photo",
                            "key" => "vehicule_photo.store",
                            "routes" => ["vehicule_photo.store"],
                        ],
                        [
                            "title" => "Modifier les métadonnées d'une photo",
                            "key" => "vehicule_photo.update",
                            "routes" => ["vehicule_photo.update"],
                        ],
                        [
                            "title" => "Supprimer une photo",
                            "key" => "vehicule_photo.destroy",
                            "routes" => ["vehicule_photo.destroy"],
                        ],
                    ],
                    "with" => ["vehicule"],
                ],
                [
                    "title" => "Remorques",
                    "key" => "remorque",
                    "routes" => [],
                    "children" => [
                        [
                            "title" => "Voir les remorques",
                            "key" => "remorque.index",
                            "routes" => ["remorque.index"],
                        ],
                        [
                            "title" => "Créer une remorque",
                            "key" => "remorque.store",
                            "routes" => ["remorque.store"],
                        ],
                        [
                            "title" => "Afficher les details d'une remorque",
                            "key" => "remorque.show",
                            "routes" => ["remorque.show"],
                        ],
                        [
                            "title" => "Modifier une remorque",
                            "key" => "remorque.update",
                            "routes" => ["remorque.update"],
                        ],
                        [
                            "title" => "Importer des remorques",
                            "key" => "remorque.import",
                            "routes" => ["remorque.import"],
                        ],
                        [
                            "title" => "Supprimer une remorque",
                            "key" => "remorque.destroy",
                            "routes" => ["remorque.destroy"],
                        ],
                        [
                            "title" => "Voir les équipements de la remorque",
                            "key" => "remorque_element.index",
                            "routes" => ["remorque_element.index"],
                        ],
                        [
                            "title" => "Ajouter un nouvel équipement",
                            "key" => "remorque_element.store",
                            "routes" => ["remorque_element.store"],
                        ],

                        [
                            "title" => "Parcourir les photos de la remorque",
                            "key" => "remorque_photo.index",
                            "routes" => ["remorque_photo.index"],
                        ],
                        [
                            "title" => "Ajouter une nouvelle photo",
                            "key" => "remorque_photo.store",
                            "routes" => ["remorque_photo.store"],
                        ],
                        [
                            "title" => "Modifier les métadonnées d'une photo",
                            "key" => "remorque_photo.update",
                            "routes" => ["remorque_photo.update"],
                        ],
                        [
                            "title" => "Supprimer une photo",
                            "key" => "remorque_photo.destroy",
                            "routes" => ["remorque_photo.destroy"],
                        ],




                        [
                            "title" => "Voir les documents de la remorque",
                            "key" => "remorque.index_remorque_document",
                            "routes" => ["remorque.index_remorque_document"],
                        ],
                        [
                            "title" => "Ajouter un nouveau document",
                            "key" => "remorque.store_remorque_document",
                            "routes" => ["remorque.store_remorque_document"],
                        ],
                        [
                            "title" => "Modifier un document",
                            "key" => "remorque.update_remorque_document",
                            "routes" => ["remorque.update_remorque_document"],
                        ],
                        [
                            "title" => "Supprimer un document",
                            "key" => "remorque.destroy_remorque_document",
                            "routes" => ["remorque.destroy_remorque_document"],
                        ],

                    ],
                    "with" => ["remorque"],
                ],
                [
                    "title" => "Documents Véhicule",
                    "key" => "vehiculedocument",
                    "routes" => [],
                    "children" => [
                        [
                            "title" => "Voir les documents véhicule",
                            "key" => "vehiculedocument.index",
                            "routes" => ["vehiculedocument.index"],
                        ],
                        [
                            "title" => "Ajouter un nouveau document véhicule",
                            "key" => "vehiculedocument.store",
                            "routes" => ["vehiculedocument.store"],
                        ],
                        [
                            "title" => "Modifier un document véhicule",
                            "key" => "vehiculedocument.update",
                            "routes" => ["vehiculedocument.update"],
                        ],
                        [
                            "title" => "Supprimer un document véhicule",
                            "key" => "vehiculedocument.destroy",
                            "routes" => ["vehiculedocument.destroy"],
                        ],
                    ],
                    "with" => ["vehiculedocument"],
                ],
                [
                    "title" => "Maintenance Préventif",
                    "key" => "planning_calendar",

                    "routes" => [],
                    "children" => [
                        [
                            "title" => "Voir le planning",
                            "key" => "planning_calendar.index",
                            "routes" => ["planning_calendar.index"],
                        ],
                        [
                            "title" => "Ajouter un événement",
                            "key" => "planning_calendar.store",
                            "routes" => ["planning_calendar.store"],
                        ],
                        [
                            "title" => "Modifier un événement",
                            "key" => "planning_calendar.update",
                            "routes" => ["planning_calendar.update"],
                        ],
                        [
                            "title" => "Supprimer un événement",
                            "key" => "planning_calendar.destroy",
                            "routes" => ["planning_calendar.destroy"],
                        ],
                    ],
                    "with" => ["planning_calendar"],
                ],
                [
                    "title" => "Maintenance Curative (Réparations)",
                    "key" => "reparation_vehicule",
                    "routes" => [],
                    "children" => [
                        [
                            "title" => "Voir la liste des réparations",
                            "key" => "reparation_vehicule.index",
                            "routes" => ["reparation_vehicule.index"],
                        ],
                        [
                            "title" => "Créer une fiche de réparation",
                            "key" => "reparation_vehicule.store",
                            "routes" => ["reparation_vehicule.store"],
                        ],
                        [
                            "title" => "Modifier une fiche de réparation",
                            "key" => "reparation_vehicule.update",
                            "routes" => ["reparation_vehicule.update"],
                        ],
                        [
                            "title" => "Supprimer une fiche de réparation",
                            "key" => "reparation_vehicule.destroy",
                            "routes" => ["reparation_vehicule.destroy"],
                        ],
                    ],
                    "with" => ["reparation_vehicule"],
                ],
                [
                    "title" => "Inventaire Pneus",
                    "key" => "pneu_inventaire",
                    "routes" => [],
                    "children" => [
                        [
                            "title" => "Voir l'inventaire des pneus",
                            "key" => "pneu_inventaire.index",
                            "routes" => ["pneu_inventaire.index"],
                        ],
                        [
                            "title" => "Ajouter des pneus à l'inventaire",
                            "key" => "pneu_inventaire.store",
                            "routes" => ["pneu_inventaire.store"],
                        ],
                        [
                            "title" => "Supprimer un enregistrement de pneu",
                            "key" => "pneu_inventaire.destroy",
                            "routes" => ["pneu_inventaire.destroy"],
                        ],
                        [
                            "title" => "Exporter l'inventaire pneus",
                            "key" => "pneu_inventaire.export",
                            "routes" => ["pneu_inventaire.export"],
                        ],
                        [
                            "title" => "Exporter l'inventaire pneus (export_pneu_inventaire)",
                            "key" => "export_pneu_inventaire.export",
                            "routes" => ["export_pneu_inventaire.export"],
                        ],
                    ],
                    "with" => ["pneu_inventaire"],
                ],
                [
                    "title" => "Remplacement Pneus",
                    "key" => "pneu_remplacement",
                    "routes" => [],
                    "children" => [
                        [
                            "title" => "Voir la liste des remplacements de pneus",
                            "key" => "pneu_remplacement.index",
                            "routes" => ["pneu_remplacement.index"],
                        ],
                        [
                            "title" => "Enregistrer un remplacement de pneu",
                            "key" => "pneu_remplacement.store",
                            "routes" => ["pneu_remplacement.store"],
                        ],
                        [
                            "title" => "Modifier un remplacement de pneu",
                            "key" => "pneu_remplacement.update",
                            "routes" => ["pneu_remplacement.update"],
                        ],
                        [
                            "title" => "Supprimer un remplacement de pneu",
                            "key" => "pneu_remplacement.destroy",
                            "routes" => ["pneu_remplacement.destroy"],
                        ],
                    ],
                    "with" => ["pneu_remplacement"],
                ],
            ],
        ],

        [
            "title" => "Paramètres",
            "key" => "parametre",
            "icon" => "fa-cog",
            "gradient" => ["#112e79", "#112e79"],
            "routes" => [],
            "children" => [
                [
                    "title" => "Magasin",
                    "key" => "magasin",
                    "routes" => [],
                    "children" => [
                        [
                            "title" => "Voir les listes des magasins",
                            "key" => "magasin.index",
                            "routes" => ["magasin.index"],
                        ],
                        [
                            "title" => "Ajouter des Magasins",
                            "key" => "magasin.store",
                            "routes" => ["magasin.store"],
                        ],
                        [
                            "title" => "Modifier un élément",
                            "key" => "magasin.update",
                            "routes" => ["magasin.update"],
                        ],
                        [
                            "title" => "Supprimer un magasin",
                            "key" => "magasin.destroy",
                            "routes" => ["magasin.destroy"],
                        ],
                    ],
                    "with" => ["magasin"],
                ],
                [
                    "title" => "Info Société",
                    "key" => "infosociete",

                    "routes" => [],
                    "children" => [
                        [
                            "title" => "Voir les informations société",
                            "key" => "infosociete.index",
                            "routes" => ["infosociete.index"],
                        ],
                    ],
                    "with" => ["infosociete"],
                ],
                [
                    "title" => "Groupes d'utilisateurs",
                    "key" => "group_user",

                    "routes" => [],
                    "children" => [
                        [
                            "title" => "Voir les liste groupe d'utilisateur",
                            "key" => "group_user.index",
                            "routes" => ["group_user.index"],
                        ],
                        [
                            "title" => "Crée nouveau groupe",
                            "key" => "group_user.store",
                            "routes" => ["group_user.store"],
                        ],
                        [
                            "title" => "Modifier un groupe",
                            "key" => "group_user.update",
                            "routes" => ["group_user.update"],
                        ],
                        [
                            "title" => "Supprimer un groupe",
                            "key" => "group_user.destroy",
                            "routes" => ["group_user.destroy"],
                        ],
                    ],
                    "with" => ["group_user"],
                ],
                [
                    "title" => "Utilisateurs",
                    "key" => "user",

                    "routes" => [],
                    "children" => [
                        [
                            "title" => "Lister les utilisateurs",
                            "key" => "user.index",
                            "routes" => ["user.index"],
                        ],
                        [
                            "title" => "Ajouter un utilisateur",
                            "key" => "user.store",
                            "routes" => ["user.store"],
                        ],
                        [
                            "title" => "Modifier un utilisateur",
                            "key" => "user.update",
                            "routes" => ["user.update"],
                        ],
                        [
                            "title" => "Supprimer un utilisateur",
                            "key" => "user.destroy",
                            "routes" => ["user.destroy"],
                        ],
                    ],
                    "with" => ["user"],
                ],
                [
                    "title" => "Elément du véhicule",
                    "key" => "paramelement",

                    "routes" => [],
                    "children" => [
                        [
                            "title" => "Voir les éléments du véhicule",
                            "key" => "paramelement.index",
                            "routes" => ["paramelement.index"],
                        ],
                        [
                            "title" => "Ajouter des nouveux éléments du véhicule",
                            "key" => "paramelement.store",
                            "routes" => ["paramelement.store"],
                        ],
                        [
                            "title" => "Modifier un éléments du véhicule",
                            "key" => "paramelement.update",
                            "routes" => ["paramelement.update"],
                        ],
                        [
                            "title" => "Supprimer les données",
                            "key" => "paramelement.destroy",
                            "routes" => ["paramelement.destroy"],
                        ],
                    ],
                    "with" => ["paramelement"],
                ],
                [
                    "title" => "Libéllé maintenance du véhicule",
                    "key" => "libelle_maintenance",

                    "routes" => [],
                    "children" => [
                        [
                            "title" => "Voir les listes des libéllés de maintenance",
                            "key" => "libelle_maintenance.index",
                            "routes" => ["libelle_maintenance.index"],
                        ],
                        [
                            "title" => "Crée un nouveau libéllé de maintenance",
                            "key" => "libelle_maintenance.store",
                            "routes" => ["libelle_maintenance.store"],
                        ],
                        [
                            "title" => "Modifier un libéllé de maintenance",
                            "key" => "libelle_maintenance.update",
                            "routes" => ["libelle_maintenance.update"],
                        ],
                        [
                            "title" => "Supprimer les données",
                            "key" => "libelle_maintenance.destroy",
                            "routes" => ["libelle_maintenance.destroy"],
                        ],
                    ],
                    "with" => ["libelle_maintenance"],
                ],
                [
                    "title" => "Tarifs",
                    "key" => "tarif",

                    "routes" => [],
                    "children" => [
                        [
                            "title" => "Voir les tarifs",
                            "key" => "tarif.index",
                            "routes" => ["tarif.index"],
                        ],
                        [
                            "title" => "Ajouter un nouveau tarif",
                            "key" => "tarif.store",
                            "routes" => ["tarif.store"],
                        ],
                        [
                            "title" => "Met à jour les details d'un tarif",
                            "key" => "tarif.update",
                            "routes" => ["tarif.update"],
                        ],
                        [
                            "title" => "Supprimer un tarif",
                            "key" => "tarif.destroy",
                            "routes" => ["tarif.destroy"],
                        ],
                    ],
                    "with" => ["tarif"],
                ],
                [
                    "title" => "Immobilisation",
                    "key" => "immobilisation",

                    "routes" => [],
                    "children" => [
                        [
                            "title" => "Voir les immobilisations",
                            "key" => "immobilisation.index",
                            "routes" => ["immobilisation.index"],
                        ],
                        [
                            "title" => "Modifier l'immobilisation",
                            "key" => "immobilisation.update",
                            "routes" => ["immobilisation.update"],
                        ],
                    ],
                    "with" => ["immobilisation"],
                ],
                [
                    "title" => "Modele de document",
                    "key" => "modeledocument",

                    "routes" => [],
                    "children" => [
                        [
                            "title" => "Voir les modèles de document",
                            "key" => "modele.document.import",
                            "routes" => ["modele.document.import"],
                        ],
                    ],
                    "with" => ["modeledocument"],
                ],
                [
                    "title" => "Sauvegardes",
                    "key" => "backups",
                    "routes" => [],
                    "children" => [
                        [
                            "title" => "Voir les sauvegardes",
                            "key" => "backups.index",
                            "routes" => ["backups.index"],
                        ],
                        [
                            "title" => "Créer une sauvegarde",
                            "key" => "backups.store",
                            "routes" => ["backups.store"],
                        ],
                        [
                            "title" => "Télécharger une sauvegarde",
                            "key" => "backups.download",
                            "routes" => ["backups.download"],
                        ],
                        [
                            "title" => "Supprimer une sauvegarde",
                            "key" => "backups.destroy",
                            "routes" => ["backups.destroy"],
                        ],
                    ],
                    "with" => ["backups"],
                ],
                [
                    "title" => "Journal d'Activité",
                    "key" => "activity_log",
                    "routes" => [],
                    "children" => [
                        [
                            "title" => "Voir le journal d'activité",
                            "key" => "activity_log.index",
                            "routes" => ["activity_log.index"],
                        ],
                    ],
                    "with" => ["activity_log"],
                ],
            ],
        ],

        [
            "title" => "Carte GPS",
            "key" => "gps_map",
            "icon" => "fa-map-location-dot",
            "gradient" => ["#0ea5e9", "#0284c7"],
            "routes" => [],
            "children" => [
                [
                    "title" => "Accès à la Carte GPS",
                    "key" => "gps.map.index",
                    "routes" => ["gps.map.index"],
                ],
                [
                    "title" => "Suivi en temps réel",
                    "key" => "gps.map.live",
                    "routes" => ["gps.map.live"],
                ],
                [
                    "title" => "Historique des positions",
                    "key" => "gps.map.history",
                    "routes" => ["gps.map.history"],
                ],
            ],
        ],

        [
            "title" => "Modules Export ",
            "key" => "modules_export",
            "icon" => "fa-file-export",
            "gradient" => ["#f97316", "#ea580c"],
            "routes" => [],
            "children" => [
                [
                    "title" => "Exporter les articles",
                    "key" => "export_article.export",
                    "routes" => ["export_article.export"],
                ],
                [
                    "title" => "Exporter les clients",
                    "key" => "export_client.export",
                    "routes" => ["export_client.export"],
                ],
                [
                    "title" => "Exporter les fournisseurs",
                    "key" => "export_fournisseur.export",
                    "routes" => ["export_fournisseur.export"],
                ],
                [
                    "title" => "Exporter les factures clients",
                    "key" => "export_facture_client.export",
                    "routes" => ["export_facture_client.export"],
                    "with" => ["client"],
                ],
                [
                    "title" => "Exporter la trésorerie",
                    "key" => "export_tresorerie.export",
                    "routes" => ["export_tresorerie.export"],
                ],
                [
                    "title" => "Exporter les mouvements de trésorerie",
                    "key" => "export_tresorerie_mouvement.export",
                    "routes" => ["export_tresorerie_mouvement.export"],
                ],
                [
                    "title" => "Exporter les flux de trésorerie",
                    "key" => "export_tresorerie_flux.export",
                    "routes" => ["export_tresorerie_flux.export"],
                ],
                [
                    "title" => "Exporter les approvisionnements",
                    "key" => "export_article_approvisionnement.export",
                    "routes" => ["export_article_approvisionnement.export"],
                ],
                [
                    "title" => "Exporter les réservations",
                    "key" => "export_reservation.export",
                    "routes" => ["export_reservation.export"],
                ],
                [
                    "title" => "Exporter les voyages",
                    "key" => "export_voyages.export",
                    "routes" => ["export_voyages.export"],
                ],
                [
                    "title" => "Exporter les chauffeurs",
                    "key" => "export_chauffeur.export",
                    "routes" => ["export_chauffeur.export"],
                ],
                [
                    "title" => "Exporter les véhicules",
                    "key" => "export_vehicule.export",
                    "routes" => ["export_vehicule.export"],
                ],
                [
                    "title" => "Exporter les documents de véhicule",
                    "key" => "export_vehiculedocument.export",
                    "routes" => ["export_vehiculedocument.export"],
                ],
                [
                    "title" => "Exporter les plannings de maintenance",
                    "key" => "export_planning_calendar.export",
                    "routes" => ["export_planning_calendar.export"],
                ],
                [
                    "title" => "Exporter les remorques",
                    "key" => "export_remorque.export",
                    "routes" => ["export_remorque.export"],
                ],
                // [
                //     "title" => "Exporter les carburants",
                //     "key" => "export_carburant.export",
                //     "routes" => ["export_carburant.export"],
                // ],
                [
                    "title" => "Exporter les mouvements de carburant",
                    "key" => "export_carburant_mouvement.export",
                    "routes" => ["export_carburant_mouvement.export"],
                ],
                [
                    "title" => "Exporter les cartes carburant",
                    "key" => "export_carburant_card.export",
                    "routes" => ["export_carburant_card.export"],
                ],
                [
                    "title" => "Exporter les utilisateurs",
                    "key" => "export_user.export",
                    "routes" => ["export_user.export"],
                ],
                // [
                //     "title" => "Exporter les groupes d'utilisateurs",
                //     "key" => "export_group_user.export",
                //     "routes" => ["export_group_user.export"],
                // ],
                [
                    "title" => "Exporter les magasins",
                    "key" => "export_magasin.export",
                    "routes" => ["export_magasin.export"],
                ],
                [
                    "title" => "Exporter les libellés de maintenance",
                    "key" => "export_libelle_maintenance.export",
                    "routes" => ["export_libelle_maintenance.export"],
                ],
                [
                    "title" => "Exporter les éléments du véhicule",
                    "key" => "export_paramelement.export",
                    "routes" => ["export_paramelement.export"],
                ],
            ],
        ],


    ];
    public function users()
    {
        return $this->hasMany(User::class);
    }

}
