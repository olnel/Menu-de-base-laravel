<?php

namespace App\Repositories;

use App\Models\Backup;

class BackupRepository extends BaseRepository
{
    public function __construct(Backup $model)
    {
        parent::__construct($model);
    }
}
