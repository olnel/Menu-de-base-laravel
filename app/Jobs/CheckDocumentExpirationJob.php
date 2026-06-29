<?php

namespace App\Jobs;

use App\Models\Notification;
use App\Models\Document;
use App\Services\DocumentDynamicService;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class CheckDocumentExpirationJob implements ShouldQueue
{
    use Queueable;

    public function handle(DocumentDynamicService $service): void
    {
        try {
            $expiringDocs = $service->getExpiringDocuments();
            
            foreach ($expiringDocs as $doc) {
                $this->processNotification($doc);
            }
        } catch (\Exception $e) {
            Log::error('Erreur CheckDocumentExpirationJob: ' . $e->getMessage());
        }
    }

    private function processNotification(Document $doc): void
    {
        $today = Carbon::today();
        $due = Carbon::parse($doc->date_expiration);
        $daysRemaining = (int) $today->diffInDays($due, false);

        $type = 'warning';
        if ($daysRemaining < 0) {
            $type = 'overdue';
        } elseif ($daysRemaining <= 3) {
            $type = 'urgent';
        }

        $entityName = $this->getEntityDisplayName($doc->documentable);

        Notification::updateOrCreate(
            [
                'notifiable_type' => Document::class,
                'notifiable_id'   => $doc->id,
            ],
            [
                'type'           => $type,
                'titre'          => "Expiration : " . $doc->type_nom,
                'message'        => "Le document {$doc->type_nom} pour {$entityName} expire dans {$daysRemaining} jours.",
                'jours_restants' => $daysRemaining,
                'data'           => [
                    'entity_type'     => class_basename($doc->documentable_type),
                    'entity_name'     => $entityName,
                    'document_type'   => $doc->type_nom,
                    'date_expiration' => $doc->date_expiration,
                ],
            ]
        );
    }

    private function getEntityDisplayName($entity): string
    {
        if (!$entity) return 'Inconnu';
        
        if (isset($entity->nom)) {
            return $entity->nom . (isset($entity->prenom) ? ' ' . $entity->prenom : '');
        }
        
        if (isset($entity->immatriculation)) {
            return $entity->immatriculation;
        }

        if (isset($entity->numero_remorque)) {
            return $entity->numero_remorque;
        }

        return 'ID: ' . $entity->id;
    }
}
