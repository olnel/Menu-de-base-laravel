<?php

namespace App\Console\Commands;

use App\Models\ReparationVehicule;
use App\Models\Ticket;
use App\Services\TicketService;
use Illuminate\Console\Command;

class BackfillReparationTickets extends Command
{
    protected $signature = 'app:backfill-reparation-tickets
        {--reparation-id= : Generate tickets for a specific reparation ID only}
        {--dry-run : Show what would be done without actually creating tickets}
        {--force : Regenerate tickets even if they already exist}';

    protected $description = 'Generate missing tickets for existing reparation vehicules that have technician labor data';

    public function handle(TicketService $ticketService): int
    {
        $query = ReparationVehicule::query()->with(['articles.details']);

        if ($reparationId = $this->option('reparation-id')) {
            $query->where('id', $reparationId);
        }

        if (!$this->option('force')) {
            // Only reparations WITHOUT any tickets
            $reparationIdsWithTickets = Ticket::whereNotNull('reparation_vehicule_id')
                ->distinct()
                ->pluck('reparation_vehicule_id')
                ->toArray();

            $query->whereNotIn('id', $reparationIdsWithTickets);
        }

        $reparations = $query->get();
        $count = $reparations->count();

        if ($count === 0) {
            $this->info('Aucune réparation à traiter.');
            return Command::SUCCESS;
        }

        $this->info("Traitement de {$count} réparation(s)...");
        $this->newLine();

        $generated = 0;
        $skipped = 0;

        $bar = $this->output->createProgressBar($count);
        $bar->start();

        foreach ($reparations as $reparation) {
            $hasTechnician = false;
            foreach ($reparation->articles as $article) {
                foreach ($article->details as $detail) {
                    if ($detail->technicien || $detail->technicien_changer) {
                        $hasTechnician = true;
                        break 2;
                    }
                }
            }

            if (!$hasTechnician) {
                $skipped++;
                $bar->advance();
                continue;
            }

            if ($this->option('dry-run')) {
                $this->line("  [DRY-RUN] Réparation #{$reparation->id} ({$reparation->immatriculation}) - tickets seraient générés");
                $bar->advance();
                continue;
            }

            $ticketService->generateForReparation($reparation);
            $generated++;
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        if ($this->option('dry-run')) {
            $this->info("Dry-run terminé : {$count} réparation(s) examinée(s), {$skipped} sans technicien.");
        } else {
            $this->info("Terminé ! {$generated} réparation(s) traité(s), {$skipped} sans technicien (ignorées).");
        }

        return Command::SUCCESS;
    }
}
