<?php

namespace App\Services;

use App\Interfaces\ActivityLoggerInterface;
use App\Repositories\ActivityLogRepository;
use App\Services\Base\BaseService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Service de journalisation des actions utilisateurs.
 *
 * SOLID
 *  - SRP : un seul rôle, persister une trace d'action.
 *  - OCP : pour ajouter un nouveau type d'action il suffit d'appeler log()
 *          avec un nouveau verbe, sans modifier le service.
 *  - LSP : peut remplacer toute autre implémentation d'ActivityLoggerInterface.
 *  - DIP : les appelants typent ActivityLoggerInterface et non la classe concrète.
 */
class ActivityLogService extends BaseService implements ActivityLoggerInterface
{
    protected array $scope = [
        'filter' => 'search',
        'filterAction' => 'action',
        'filterModule' => 'module',
        'filterUser' => 'user_id',
        'filterdatestart' => 'start_date',
        'filterdateend' => 'end_date',
    ];

    protected array $relation = ['user'];

    public function __construct(ActivityLogRepository $repository)
    {
        parent::__construct($repository);
    }

    protected function initializeFilters(): void
    {
        $this->setFilterValue('id')->setFilterLabel('action');
    }

    /**
     * Méthode principale. Ne lève jamais d'exception : un échec
     * de log ne doit pas casser le métier.
     */
    public function log(
        string  $action,
        ?string $module = null,
        ?Model  $subject = null,
        ?string $description = null,
        array   $context = []
    ): void
    {
        try {
            $user = auth()->user();

            if ($user?->is_dna) {
                return;
            }

            $request = request();

            $this->repository->create([
                'user_id' => $user?->id,
                'user_name' => $user?->name,
                'user_email' => $user?->email,
                'action' => $action,
                'module' => $module,
                'subject_type' => $subject ? get_class($subject) : null,
                'subject_id' => $subject?->getKey(),
                'description' => $description,
                'old_values' => $context['old_values'] ?? null,
                'new_values' => $context['new_values'] ?? null,
                'ip_address' => $request?->ip(),
                'user_agent' => substr((string)$request?->userAgent(), 0, 500),
                'url' => substr((string)$request?->fullUrl(), 0, 500),
                'method' => $request?->method(),
                'route_name' => optional($request?->route())->getName(),
            ]);
        } catch (Throwable $e) {
            Log::error('[ActivityLog] échec d\'enregistrement: ' . $e->getMessage(), [
                'action' => $action,
                'module' => $module,
                'subject' => $subject ? get_class($subject) . '#' . $subject->getKey() : null,
            ]);
        }
    }

    public function logCreated(Model $subject, ?string $module = null, ?string $description = null): void
    {
        $this->log(
            action: 'created',
            module: $module ?? $this->guessModule($subject),
            subject: $subject,
            description: $description ?? "Création {$this->shortName($subject)} #{$subject->getKey()}",
            context: ['new_values' => $subject->getAttributes()]
        );
    }

    public function logUpdated(Model $subject, array $oldValues, array $newValues, ?string $module = null, ?string $description = null): void
    {
        $diffOld = [];
        $diffNew = [];
        foreach ($newValues as $key => $value) {
            $previous = $oldValues[$key] ?? null;
            if ($previous != $value) {
                $diffOld[$key] = $previous;
                $diffNew[$key] = $value;
            }
        }
        if (empty($diffNew)) {
            return;
        }
        $this->log(
            action: 'updated',
            module: $module ?? $this->guessModule($subject),
            subject: $subject,
            description: $description ?? "Modification {$this->shortName($subject)} #{$subject->getKey()}",
            context: ['old_values' => $diffOld, 'new_values' => $diffNew]
        );
    }

    public function logDeleted(Model $subject, ?string $module = null, ?string $description = null): void
    {
        $this->log(
            action: 'deleted',
            module: $module ?? $this->guessModule($subject),
            subject: $subject,
            description: $description ?? "Suppression {$this->shortName($subject)} #{$subject->getKey()}",
            context: ['old_values' => $subject->getAttributes()]
        );
    }

    private function guessModule(Model $subject): string
    {
        return strtolower(class_basename($subject));
    }

    private function shortName(Model $subject): string
    {
        return class_basename($subject);
    }
}
