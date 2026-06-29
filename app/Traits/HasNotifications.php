<?php

namespace App\Traits;

use App\Models\Notification;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasNotifications
{
    public function notifications(): MorphMany
    {
        return $this->morphMany(Notification::class, 'notifiable');
    }

    public static function bootHasNotifications(): void
    {
        // Nettoyage automatique des alertes quand la source est supprimée
        static::deleting(function ($model) {
            $model->notifications()->delete();
        });
    }
}
