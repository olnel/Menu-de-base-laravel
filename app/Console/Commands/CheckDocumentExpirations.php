<?php

namespace App\Console\Commands;

use App\Jobs\CheckDocumentExpirationJob;
use Illuminate\Console\Command;

class CheckDocumentExpirations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'documents:check-expirations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Vérifie les expirations de documents et génère des notifications';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        CheckDocumentExpirationJob::dispatch();

        $this->info('Job d\'expiration de documents dispatché');

        return Command::SUCCESS;
    }
}
