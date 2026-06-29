<?php

namespace App\Console\Commands;

use App\Jobs\CheckMaintenanceAlertesJob;
use Illuminate\Console\Command;

class CheckMaintenanceAlertes extends Command
{
    protected $signature   = 'maintenance:check-alerts';
    protected $description = 'Génère les alertes de prévenance pour les maintenances planifiées';

    public function handle(): int
    {
        CheckMaintenanceAlertesJob::dispatch();

        $this->info('Job d\'alerte de maintenance dispatché');

        return Command::SUCCESS;
    }
}
