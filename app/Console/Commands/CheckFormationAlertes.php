<?php

namespace App\Console\Commands;

use App\Jobs\CheckFormationAlertesJob;
use Illuminate\Console\Command;

class CheckFormationAlertes extends Command
{
    protected $signature   = 'formation:check-alerts';
    protected $description = 'Génère les alertes de renouvellement pour les formations';

    public function handle(): void
    {
        CheckFormationAlertesJob::dispatch();
        $this->info('Job d\'alerte formation dispatché');
    }
}
