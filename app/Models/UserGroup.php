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
            "children" => [],
        ],

        [
            "title" => "Gestion Personnels",
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
            "title" => "Paramètres",
            "key" => "parametre",
            "icon" => "fa-cog",
            "gradient" => ["#112e79", "#112e79"],
            "routes" => [],
            "children" => [
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



    ];
    public function users()
    {
        return $this->hasMany(User::class);
    }

}
