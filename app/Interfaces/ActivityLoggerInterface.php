<?php

namespace App\Interfaces;

use Illuminate\Database\Eloquent\Model;

/**
 * Contrat applicatif pour la journalisation des actions utilisateurs.
 *
 * Toute classe qui veut produire un log doit dépendre de cette interface
 * (Dependency Inversion Principle), pas de l'implémentation concrète.
 */
interface ActivityLoggerInterface
{
    /**
     * Enregistre une action utilisateur.
     *
     * @param string      $action      Verbe d'action: created, updated, deleted, viewed, login, logout, exported, ...
     * @param string|null $module      Nom du module métier: vehicule, voyage, facture_client, ...
     * @param Model|null  $subject     Entité concernée (optionnel)
     * @param string|null $description Description courte lisible
     * @param array       $context     ['old_values' => [...], 'new_values' => [...]]
     */
    public function log(
        string $action,
        ?string $module = null,
        ?Model $subject = null,
        ?string $description = null,
        array $context = []
    ): void;

    public function logCreated(Model $subject, ?string $module = null, ?string $description = null): void;

    public function logUpdated(Model $subject, array $oldValues, array $newValues, ?string $module = null, ?string $description = null): void;

    public function logDeleted(Model $subject, ?string $module = null, ?string $description = null): void;
}
