<?php

namespace App\Services;

use App\Models\Voyage;
use App\Repositories\VoyageMarchandiseRepository;
use App\Services\Base\BaseService;

class VoyageMarchandiseService extends BaseService
{
    // Your service methods go here
    protected $repository;
    public function __construct(VoyageMarchandiseRepository $repository)
    {
        $this->repository = $repository;
        parent::__construct($repository);
    }

    // public function createVoyageMarchandise($voyage, array $marchandiseData): array
    // {
    //     $voyage->voyageMarchandises()->create($marchandiseData);
    //     return ['error' => false, 'message' => 'Marchandise ajoutée avec succès.'];
    // }

    // public function updateVoyageMarchandise($marchandise, array $validated): array
    // {
    //     return parent::update($marchandise, $validated);
    // }

    // public function deleteVoyageMarchandise($marchandise): array
    // {
    //     return parent::delete($marchandise);
    // }

    public function syncVoyageMarchandises(Voyage $voyage, array $newItems): array
    {
        $existingItemIds = $voyage->voyageMarchandises()->pluck('id')->toArray();
        $updatedIds = [];
        foreach ($newItems as $itemData) {
            if (isset($itemData['id'])) {
                //mise à jour
                $marchandise = $voyage->voyageMarchandises()->find($itemData['id']);
                if ($marchandise) {
                    $marchandise->update($itemData);
                    $updatedIds[] = $marchandise->id;
                }
            } else {
                //création
                $marchandise = $voyage->voyageMarchandises()->create($itemData);
                $updatedIds[] = $marchandise->id;
            }
        }
        
        $itemsToDelete = array_diff($existingItemIds, $updatedIds);
        if (!empty($itemsToDelete)) {
            $voyage->voyageMarchandises()->whereIn('id', $itemsToDelete)->delete();
        }
        return ['error' => false, 'message' => 'Marchandises mis à jour avec succès.'];
    }
}
