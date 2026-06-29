<?php

namespace App\Services;

use App\Repositories\PlanningCalendarRepository;
use App\Services\Base\BaseService;
use App\Utils\ForamatDate;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class PlanningCalendarService extends BaseService
{

    protected $repository;
    private $libelleMaintenance;

    protected array $scope = [
        'fetchdate' => 'date_filtre'
    ];

    public function __construct(PlanningCalendarRepository $planningCalendarRepository,
        LibelleMaintenanceService $libelleMaintenanceService)
    {
        $this->repository = $planningCalendarRepository;
        $this->libelleMaintenance = $libelleMaintenanceService;
        parent::__construct($this->repository);
    }

    // Your service methods go here
    protected function initializeFilters(): void
    {
        $this->setFilterValue('id')->setFilterLabel('date_maintenance');
    }

    public function create(array $validated):array
    {
        DB::beginTransaction();
        try {
            // 1- Insertion dans la table libellé maintenance
            $data_element = $this->extracteLibelleVehicule($validated);
            $libelle_maintenance_id = $this->libelleMaintenance->isExiste($data_element);

            //2- inertion dans la table planningCalendar
            $validated['libelle_maintenance_id'] = $libelle_maintenance_id;
            $this->repository->create($validated);

            DB::commit();

            return $this->successResponse('Insertion terminée avec succès');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Erreur lors de l\'enregistrement', $e);
        }

    }

    public function update($model, array $validated):array
    {
        DB::beginTransaction();
        try {
            // 1- Insertion dans la table libellé maintenance
            $data_element = $this->extracteLibelleVehicule($validated);
            $libelle_maintenance_id = $this->libelleMaintenance->isExiste($data_element);

            //2- inertion dans la table planningCalendar
            $validated['libelle_maintenance_id'] = $libelle_maintenance_id;
            $this->repository->update($model,$validated);

            DB::commit();

            return $this->successResponse('Insertion terminée avec succès');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Erreur lors de l\'enregistrement', $e);
        }

    }

    private function extracteLibelleVehicule(array $validated)
    {
        $element = [
            'libelle' => $validated['libelle'],
            'notification' => $validated['notification'],
            'background' => $validated['background'],
            'text_color' => $validated['text_color'],
        ];
        return $element;
    }

    /**
     * Récupère les notifications formatées pour l'affichage
     */
    public function getFormattedNotifications()
    {
        return $this->repository->getNotificationsToDisplay()
            ->map(function($maintenance) {
                return [
                    'id' => $maintenance->id,
                    'vehicule' => $maintenance->vehicule?->immatriculation,
                    'libelle' => $maintenance->libelleMaintenance?->libelle,
                    'date_maintenance' => $maintenance->date_maintenance,
                    'days_remaining' => $maintenance->getDaysRemaining(),
                    'background' => $maintenance->libelleMaintenance->background,
                    'text_color' => $maintenance->libelleMaintenance->text_color,
                    'notification_days' => $maintenance->libelleMaintenance->notification
                ];
            });
    }

    /**
     * Vérifie s'il y a des notifications à afficher
     */
    public function hasNotifications(): bool
    {
        return $this->repository->getNotificationsToDisplay()->isNotEmpty();
    }
}
