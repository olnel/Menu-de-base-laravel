<?php

namespace App\Services;

use App\Models\PneuSerie;
use App\Repositories\PneuRemplacementRepository;
use App\Services\Base\BaseService;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PneuRemplacementService extends BaseService
{
    protected $repository;

    protected array $relation = [
        'vehicule',
        'remorque',
        'pneuSerieRetire.vehicule',
        'pneuSerieRetire.remorque',
        'pneuSerieMonte.vehicule',
        'pneuSerieMonte.remorque',
        'user',
    ];

    protected array $scope = [
        'filter'          => 'search',
        'filterdatestart' => 'start_date',
        'filterdateend'   => 'end_date',
        'vehicule'        => 'vehicule_id',
        'typeoperation'   => 'type_operation',
    ];

    public function __construct(PneuRemplacementRepository $repository)
    {
        $this->repository = $repository;
        parent::__construct($repository);
    }

    protected function initializeFilters(): void
    {
        $this->setFilterValue('id')->setFilterLabel('type_operation');
    }

    public function save(array $validated): array
    {
        DB::beginTransaction();

        try {
            $userId    = Auth::id();
            $operation = null;

            if ($validated['type_operation'] === 'remplacement') {
                $shared = [
                    'type_operation' => $validated['type_operation'],
                    'date_operation' => $validated['date_operation'],
                    'vehicule_id'    => $validated['vehicule_id']  ?? null,
                    'remorque_id'    => $validated['remorque_id']  ?? null,
                    'technicien'     => $validated['technicien']   ?? null,
                    'observations'   => $validated['observations'] ?? null,
                    'user_id'        => $userId,
                ];

                foreach ($validated['lignes'] as $ligne) {
                    $record = array_merge($shared, [
                        'pneu_serie_retire_id' => $ligne['pneu_serie_retire_id'],
                        'pneu_serie_monte_id'  => $ligne['pneu_serie_monte_id']  ?? null,
                        'position_retire'      => $ligne['position']             ?? null,
                        'position_monte'       => $ligne['position']             ?? null,
                        'motif'                => $ligne['motif']                ?? null,
                        'date_hors_service'    => $ligne['date_hors_service']    ?? null,
                    ]);
                    $operation = $this->repository->create($record);
                    $this->updatePneuSeriesAfterOperation($record);
                }
            } else {
                $shared = [
                    'type_operation' => $validated['type_operation'],
                    'date_operation' => $validated['date_operation'],
                    'vehicule_id'    => $validated['vehicule_id']  ?? null,
                    'remorque_id'    => $validated['remorque_id']  ?? null,
                    'technicien'     => $validated['technicien']   ?? null,
                    'observations'   => $validated['observations'] ?? null,
                    'user_id'        => $userId,
                ];

                foreach ($validated['lignes'] as $ligne) {
                    $record = array_merge($shared, [
                        'pneu_serie_retire_id' => $ligne['pneu_serie_retire_id'],
                        'pneu_serie_monte_id'  => $ligne['pneu_serie_monte_id']  ?? null,
                        'position_retire'      => $ligne['position_retire']      ?? null,
                        'position_monte'       => $ligne['position_monte']       ?? null,
                        'motif'                => $ligne['motif']                ?? null,
                        'date_hors_service'    => $ligne['date_hors_service']    ?? null,
                    ]);
                    $operation = $this->repository->create($record);
                    $this->updatePneuSeriesAfterOperation($record);
                }
            }

            DB::commit();
            Log::info('PneuRemplacement enregistré', ['id' => $operation?->id]);
            return $this->successResponse('Opération(s) enregistrée(s) avec succès', $operation);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('PneuRemplacement erreur: ' . $e->getMessage(), ['exception' => $e]);
            return $this->errorResponse('Erreur lors de l\'enregistrement', $e);
        }
    }

    public function modifier(PneuSerie $model, array $validated): array
    {
        DB::beginTransaction();

        try {
            $this->repository->update($model, $validated);
            DB::commit();
            return $this->successResponse('Mise à jour effectuée avec succès', $model);
        } catch (Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Erreur lors de la mise à jour', $e);
        }
    }

    public function supprimer($model): array
    {
        return $this->delete($model);
    }

    public function getByVehicule(int $vehiculeId)
    {
        return $this->repository->getByVehicule($vehiculeId);
    }

    public function getByPneuSerie(int $pneuSerieId)
    {
        return $this->repository->getByPneuSerie($pneuSerieId);
    }

    private function updatePneuSeriesAfterOperation(array $validated): void
    {
        $typeOperation  = $validated['type_operation'];
        $vehiculeId     = $validated['vehicule_id']          ?? null;
        $remorqueId     = $validated['remorque_id']          ?? null;
        $serieRetireId  = $validated['pneu_serie_retire_id'];
        $serieMonteId   = $validated['pneu_serie_monte_id']  ?? null;
        $positionRetire = $validated['position_retire']      ?? null;
        $positionMonte  = $validated['position_monte']       ?? null;
        $motif          = $validated['motif']                ?? null;
        $dateHorsService= $validated['date_hors_service']    ?? null;

        $serieRetire = PneuSerie::find($serieRetireId);

        if ($typeOperation === 'remplacement') {
            if ($serieRetire) {
                $serieRetire->vehicule_id       = null;
                $serieRetire->remorque_id       = null;
                $serieRetire->etat              = 'a_remplacer';
                $serieRetire->occupe            = false;
                $serieRetire->position_actuel   = null;
                $serieRetire->is_first          = $serieRetire->is_first == 1 ? 2 : $serieRetire->is_first; // Si c'était le premier montage, on le marque comme ancien premier
                if ($motif === 'fin_vie') {
                    $serieRetire->date_hors_service = $dateHorsService;
                }
                $serieRetire->save();
            }

            if ($serieMonteId) {
                $serieMonte = PneuSerie::find($serieMonteId);
                if ($serieMonte) {
                    $serieMonte->vehicule_id     = $vehiculeId;
                    $serieMonte->remorque_id     = $remorqueId;
                    $serieMonte->occupe          = true;
                    $serieMonte->type            = $positionMonte;
                    $serieMonte->position_actuel = $positionMonte;
                    $serieMonte->is_first = $serieMonte->is_first == 0 ? 1 : 2; // Si c'est la première fois qu'on monte ce pneu, on le marque comme premier montage
                    $serieMonte->save();
                }
            }
        }

        if ($typeOperation === 'permutation' && $serieMonteId) {
            $serieMonte = PneuSerie::find($serieMonteId);

            if ($serieRetire && $serieMonte) {
                $serieRetire->type           = $positionMonte;
                $serieRetire->position_actuel= $positionMonte;
                $serieRetire->save();

                $serieMonte->type            = $positionRetire;
                $serieMonte->position_actuel = $positionRetire;
                $serieMonte->save();
            }
        }
    }
}
