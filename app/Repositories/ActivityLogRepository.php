<?php

namespace App\Repositories;

use App\Models\ActivityLog;

class ActivityLogRepository extends BaseRepository
{
    public function __construct(ActivityLog $activityLog)
    {
        parent::__construct($activityLog);
        $this->setDefaultOrder('created_at', 'desc');
    }

    public function getDistinctModules(): array
    {
        return $this->model->newQuery()
            ->whereNotNull('module')
            ->distinct()
            ->pluck('module')
            ->filter()
            ->values()
            ->all();
    }

    public function getDistinctActions(): array
    {
        return $this->model->newQuery()
            ->whereNotNull('action')
            ->distinct()
            ->pluck('action')
            ->filter()
            ->values()
            ->all();
    }
}
