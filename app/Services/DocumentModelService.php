<?php

namespace App\Services;

use App\Models\DocumentModel;
use App\Models\DocumentType;
use App\Repositories\DocumentModelRepository;
use App\Services\Base\BaseService;
use Illuminate\Support\Facades\DB;

class DocumentModelService extends BaseService
{
    protected $repository;
    protected array $relation = ['documentableType', 'documentType'];

    public function __construct(DocumentModelRepository $repository)
    {
        $this->repository = $repository;
        parent::__construct($repository);
    }

    protected function initializeFilters(): void
    {
        $this->setFilterLabel('id')->setFilterValue('id');
    }

    public function syncModels($documentableTypeId, array $documents)
    {
        DB::beginTransaction();
        try {
            $newIds = [];

            foreach ($documents as $docData) {
                // Resolve DocumentType by name
                $docType = DocumentType::firstOrCreate(['nom' => $docData['document_type_name']]);
                
                $modelData = [
                    'documentable_type_id' => $documentableTypeId,
                    'document_type_id' => $docType->id,
                    'obligatoire' => $docData['obligatoire'] ?? false,
                    'expiration_required' => $docData['expiration_required'] ?? false,
                    'multiple_allowed' => $docData['multiple_allowed'] ?? false,
                    'alert_days' => $docData['alert_days'] ?? 0,
                    'ordre' => $docData['ordre'] ?? 0,
                    'actif' => $docData['actif'] ?? true,
                ];

                if (isset($docData['id']) && $docData['id']) {
                    $model = DocumentModel::find($docData['id']);
                    if ($model) {
                        $model->update($modelData);
                        $newIds[] = $model->id;
                    } else {
                        $model = DocumentModel::create($modelData);
                        $newIds[] = $model->id;
                    }
                } else {
                    $model = DocumentModel::create($modelData);
                    $newIds[] = $model->id;
                }
            }

            // Remove ones not in new list for this documentable_type
            DocumentModel::where('documentable_type_id', $documentableTypeId)
                ->whereNotIn('id', $newIds)
                ->delete();

            DB::commit();
            return ['error' => false, 'message' => 'Modèles de documents mis à jour avec succès'];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['error' => true, 'message' => 'Erreur lors de la synchronisation : ' . $e->getMessage()];
        }
    }
}
