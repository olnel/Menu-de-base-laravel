<?php

namespace App\Services;

use App\Models\Tarif;
use App\Repositories\TarifDetailsRepository;
use App\Repositories\TarifRepository;
use App\Services\Base\BaseService;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TarifService extends BaseService
{
    const MSG_INSERT_SUCCESS = 'INSERTION TERMINÉE AVEC SUCCÈS';
    const MSG_UPDATE_SUCCESS = 'MODIFICATION TERMINÉE AVEC SUCCÈS';
    const MSG_DELETE_SUCCESS = 'SUPPRESSION TERMINÉE AVEC SUCCÈS';
    const MSG_ERROR_INSERT = 'ERREUR LORS DE L\'INSERTION';
    const MSG_ERROR_UPDATE = 'ERREUR LORS DE LA MODIFICATION';
    const MSG_ERROR_DELETE = 'ERREUR LORS DE LA SUPPRESSION';
    protected TarifRepository $tarifRepository;
    protected TarifDetailsRepository $tarifDetailsRepository;

    public function __construct(TarifRepository $tarifRepository, TarifDetailsRepository $tarifDetailsRepository)
    {
        $this->tarifRepository = $tarifRepository;
        $this->tarifDetailsRepository = $tarifDetailsRepository;
        parent::__construct($tarifRepository);
    }

    public function saveWithDetais(mixed $validated): array
    {
        DB::beginTransaction();

        try {

            $details = $validated['details'];

            // Étape 1 : Insertion du Tarif
            $tarif = $this->saveTarif($validated);

            // Étape 2 : Enregistrement des détails
            $this->saveDetails($tarif['element']->id, $details);

            DB::commit();
            return $this->successResponse(self::MSG_INSERT_SUCCESS);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Erreur saveDevisWithDetails', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return $this->errorResponse(self::MSG_ERROR_INSERT, $e);
        }
    }

    public function updateWithDetails(Tarif $tarif, mixed $validated): array
    {
        DB::beginTransaction();

        try {
            $details = $validated['details'];
            // Étape 1 : suppression du tarif détails
            $this->tarifDetailsRepository->deleteByTarifId($tarif->id);
            // Étape 2 : Mise à jour du Tarif
            $this->tarifRepository->update($tarif, $validated);
            //Étape 3 : Enregistrement des nouveaux détails
            $this->saveDetails($tarif->id, $details);

            DB::commit();
            return $this->successResponse(self::MSG_UPDATE_SUCCESS);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Erreur updateDevisWithDetails', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return $this->errorResponse(self::MSG_ERROR_UPDATE, $e);
        }
    }

    public function deleteWithDetails(Tarif $tarif)
    {
        DB::beginTransaction();

        try {
            // Étape 1 : suppression des détails du tarif
            $this->tarifDetailsRepository->deleteByTarifId($tarif->id);
            // Étape 2 : suppression du tarif
            $this->tarifRepository->delete($tarif);

            DB::commit();
            return $this->successResponse(self::MSG_DELETE_SUCCESS);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Erreur deleteDevisWithDetails', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return $this->errorResponse(self::MSG_ERROR_DELETE, $e);
        }
    }


    private function saveTarif(mixed $data): array
    {
        return parent::create($data);
    }

    private function saveDetails($id, mixed $details)
    {

        foreach ($details as $detail) {
            $detail['tarif_id'] = $id;
            $this->tarifDetailsRepository->create($detail);
        }

    }
    public function getTarifsWithDetailsMapped(): array
    {
        return $this->tarifRepository->fetchData(['details'])
        ->map(function ($tarif) {
            return [
                'id' => $tarif->id,
                'nom_tarif' => $tarif->nom_tarif,
                'details' => $tarif->details->map(function ($detail) {
                    return [
                        'value' => $detail->prix_ht,
                        'label' => $detail->libelle . '   ' . number_format($detail->prix_ht, 0, ',', ' ') . ' Ar',
                        'id' => $detail->id,
                    ];
                })->toArray(),
            ];
        })->toArray();
    }



}
