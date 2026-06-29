<?php

namespace App\Traits;

use App\Interfaces\ActivityLoggerInterface;

/**
 * Trait à apposer sur n'importe quel modèle Eloquent pour qu'il soit
 * automatiquement journalisé via les évènements created / updated / deleted.
 *
 * Open/Closed Principle : pour qu'un nouveau module bénéficie du log, il suffit
 * d'ajouter `use LogsActivity;` dans son modèle. Aucune ligne à toucher dans
 * ActivityLogService.
 *
 * Personnalisation possible :
 *  - public string $logModule = 'voyage';
 *  - public array $logExcept  = ['updated_at', 'remember_token'];
 *  - public bool  $logEnabled = true;
 */
trait LogsActivity
{
    public static function bootLogsActivity(): void
    {
        static::created(function ($model) {
            if (! self::activityLogEnabled($model)) {
                return;
            }
            app(ActivityLoggerInterface::class)->logCreated(
                $model,
                self::resolveLogModule($model)
            );
        });

        static::updated(function ($model) {
            if (! self::activityLogEnabled($model)) {
                return;
            }
            $original = $model->getOriginal();
            $changes  = $model->getChanges();

            $except = property_exists($model, 'logExcept') ? $model->logExcept : ['updated_at'];
            foreach ($except as $field) {
                unset($original[$field], $changes[$field]);
            }
            if (empty($changes)) {
                return;
            }

            app(ActivityLoggerInterface::class)->logUpdated(
                $model,
                array_intersect_key($original, $changes),
                $changes,
                self::resolveLogModule($model)
            );
        });

        static::deleted(function ($model) {
            if (! self::activityLogEnabled($model)) {
                return;
            }
            app(ActivityLoggerInterface::class)->logDeleted(
                $model,
                self::resolveLogModule($model)
            );
        });
    }

    protected static function activityLogEnabled($model): bool
    {
        if (property_exists($model, 'logEnabled')) {
            return (bool) $model->logEnabled;
        }
        return true;
    }

    protected static function resolveLogModule($model): string
    {
        if (property_exists($model, 'logModule') && !empty($model->logModule)) {
            return $model->logModule;
        }
        return strtolower(class_basename($model));
    }
}
