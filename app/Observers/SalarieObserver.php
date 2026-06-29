<?php

namespace App\Observers;

use App\Models\Salarie;
use App\Models\SalarieHistory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class SalarieObserver
{
    /**
     * Handle the Salarie "created" event.
     */
    public function created(Salarie $salarie): void
    {
        $this->logAction($salarie, 'Enregistrement', null, $salarie->getAttributes());
    }

    /**
     * Handle the Salarie "updated" event.
     */
    public function updated(Salarie $salarie): void
    {
        $oldValues = array_intersect_key($salarie->getOriginal(), $salarie->getChanges());
        $newValues = $salarie->getChanges();

        // On ignore updated_at
        unset($oldValues['updated_at'], $newValues['updated_at']);

        if (empty($newValues)) {
            return;
        }

        $this->logAction($salarie, 'Modification', $oldValues, $newValues);
    }

    /**
     * Handle the Salarie "deleting" event.
     */
    public function deleting(Salarie $salarie): void
    {
        // On utilise "deleting" au lieu de "deleted" pour pouvoir logguer avant la suppression physique (forceDelete)
        $action = $salarie->isForceDeleting() ? 'Suppression Définitive' : 'Suppression';
        $this->logAction($salarie, $action, $salarie->getAttributes(), null);
    }

    /**
     * Handle the Salarie "restored" event.
     */
    public function restored(Salarie $salarie): void
    {
        $this->logAction($salarie, 'Restauration', null, $salarie->getAttributes());
    }

    /**
     * Log action to salarie_histories table.
     */
    protected function logAction(Salarie $salarie, string $action, ?array $oldValues, ?array $newValues): void
    {
        SalarieHistory::create([
            'salarie_id' => $salarie->id,
            'user_id' => Auth::id(),
            'action' => $action,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => Request::ip(),
            'user_agent' => substr((string) Request::userAgent(), 0, 500),
        ]);
    }
}
