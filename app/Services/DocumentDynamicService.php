<?php

namespace App\Services;

use App\Models\Document;
use App\Models\DocumentModel;
use App\Models\DocumentableType;
use App\Repositories\DocumentRepository;
use App\Services\Base\BaseService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Exception;

class DocumentDynamicService extends BaseService
{
    protected $repository;
    protected ImageService $imageService;

    public function __construct(DocumentRepository $documentRepository, ImageService $imageService)
    {
        $this->repository = $documentRepository;
        $this->imageService = $imageService;
        parent::__construct($documentRepository);
    }

    /**
     * Récupère les modèles de documents requis pour un type d'entité donné
     */
    public function getRequiredDocumentModels(string $modelClass)
    {
        return DocumentModel::whereHas('documentableType', function($query) use ($modelClass) {
            $query->where('model_class', $modelClass);
        })->with('documentType')->where('actif', true)->orderBy('ordre')->get();
    }

    /**
     * Upload et enregistre un document pour une entité
     */
    public function saveDocument($entity, int $documentTypeId, $file, $dateExpiration = null, $observation = null, $parentId = null, $type = 'fichier')
    {
        DB::beginTransaction();
        try {
            $path = 'documents/' . strtolower(class_basename($entity)) . '/' . $entity->id;
            
            $filePath = null;
            if ($file) {
                // Si c'est une image, on peut utiliser le traitement d'image, sinon upload simple
                if ($file instanceof \Illuminate\Http\UploadedFile && str_starts_with($file->getMimeType(), 'image/')) {
                    $processed = $this->imageService->processWithMemorySafety($file, $path, ['create_thumb' => true]);
                    $filePath = $processed['main']['path'];
                } else {
                    $filePath = $file->store($path, 'public');
                }
            }

            $document = $this->repository->create([
                'parent_id' => $parentId,
                'type' => $type,
                'documentable_type' => get_class($entity),
                'documentable_id' => $entity->id,
                'document_type_id' => $documentTypeId,
                'fichier' => $filePath,
                'date_expiration' => $dateExpiration,
                'observation' => $observation
            ]);

            DB::commit();
            return $this->successResponse('Document enregistré avec succès', $document);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Erreur save document: ' . $e->getMessage());
            return $this->errorResponse('Erreur lors de l\'enregistrement du document', $e);
        }
    }

    /**
     * Récupère les documents d'une entité regroupés par type de document
     */
    public function getEntityDocuments($modelClass, $modelId)
    {
        return Document::where('documentable_type', $modelClass)
            ->where('documentable_id', $modelId)
            ->whereNull('parent_id') // On prend les parents (fichiers seuls ou dossiers)
            ->with(['children.documentType', 'documentType'])
            ->get();
    }

    /**
     * Supprime un document et ses fichiers physiques
     */
    public function deleteDocument(Document $document)
    {
        try {
            // Supprimer récursivement si c'est un dossier
            if ($document->type === 'dossier') {
                foreach ($document->children as $child) {
                    $this->deleteDocument($child);
                }
            }

            // Supprimer le fichier physique si présent
            if ($document->fichier && \Illuminate\Support\Facades\Storage::disk('public')->exists($document->fichier)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($document->fichier);
                // Optionnel: supprimer le thumbnail
                $thumbPath = str_replace(basename($document->fichier), 'thumb_' . basename($document->fichier), $document->fichier);
                if (\Illuminate\Support\Facades\Storage::disk('public')->exists($thumbPath)) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($thumbPath);
                }
            }

            $document->delete();
            return ['error' => false, 'message' => 'Document supprimé avec succès'];
        } catch (Exception $e) {
            return ['error' => true, 'message' => 'Erreur lors de la suppression : ' . $e->getMessage()];
        }
    }

    /**
     * Récupère les documents qui vont bientôt expirer selon le paramétrage alert_days
     */
    public function getExpiringDocuments()
    {
        $today = Carbon::today();

        return Document::join('document_types', 'documents.document_type_id', '=', 'document_types.id')
            ->join('documentable_types', 'documents.documentable_type', '=', 'documentable_types.model_class')
            ->join('document_models', function($join) {
                $join->on('document_models.documentable_type_id', '=', 'documentable_types.id')
                     ->on('document_models.document_type_id', '=', 'documents.document_type_id');
            })
            ->whereNotNull('documents.date_expiration')
            ->whereRaw('DATEDIFF(documents.date_expiration, ?) <= document_models.alert_days', [$today])
            ->whereRaw('documents.date_expiration >= ?', [$today])
            ->select('documents.*', 'document_types.nom as type_nom', 'document_models.alert_days')
            ->get();
    }

    public function planningDocumentDynamicExpiration(array $filters = []): array
    {
        $today = Carbon::today();
        
        // On récupère uniquement les documents les plus récents par type et entité
        // et qui sont dans leur période d'alerte
        $results = Document::join('document_types', 'documents.document_type_id', '=', 'document_types.id')
            ->join('documentable_types', 'documents.documentable_type', '=', 'documentable_types.model_class')
            ->join('document_models', function($join) {
                $join->on('document_models.documentable_type_id', '=', 'documentable_types.id')
                     ->on('document_models.document_type_id', '=', 'documents.document_type_id');
            })
            ->whereNotNull('documents.date_expiration')
            ->whereRaw('DATEDIFF(documents.date_expiration, ?) <= document_models.alert_days', [$today])
            ->with(['documentable']) // Pour charger l'entité (Chauffeur, Vehicule, etc.)
            ->select('documents.*', 'document_types.nom as type_nom', 'documentable_types.nom as entity_type_name')
            ->get();

        return $results->map(function ($doc) use ($today) {
            $expirationDate = Carbon::parse($doc->date_expiration);
            $isExpired = $today->greaterThan($expirationDate);
            
            // On essaie de trouver un nom pour l'entité
            $entityName = '';
            if ($doc->documentable) {
                if (isset($doc->documentable->nom)) {
                    $entityName = $doc->documentable->nom . ' ' . ($doc->documentable->prenom ?? '');
                } elseif (isset($doc->documentable->immatriculation)) {
                    $entityName = $doc->documentable->immatriculation;
                } elseif (isset($doc->documentable->numero_remorque)) {
                    $entityName = $doc->documentable->numero_remorque;
                }
            }

            return [
                'id' => 'dyn_' . $doc->id,
                'title' => trim($entityName) . ' : ' . $doc->type_nom,
                'description' => $doc->observation,
                'date' => $expirationDate->format('d-m-Y'),
                'background' => $isExpired ? '#ff4d4f' : '#fadb14', // Red if expired, Yellow if warning
                'text_color' => $isExpired ? '#ffffff' : '#000000',
                'type' => 'document',
                'entity_type' => $doc->entity_type_name,
                'nom_document' => $doc->type_nom,
                'entity_name' => trim($entityName)
            ];
        })->all();
    }

    private function getBackgroundColorByEntity($entityType)
    {
        return match (strtolower($entityType)) {
            'personne', 'agent', 'chauffeur' => '#faad14', // Orange
            'tracteur', 'vehicule' => '#52c41a', // Green
            'remorque' => '#ff4d4f', // Red
            default => '#1890ff', // Blue
        };
    }
}
