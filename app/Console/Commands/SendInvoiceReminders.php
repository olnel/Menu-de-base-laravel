<?php

namespace App\Console\Commands;

use App\Models\FactureClient;
use App\Services\Document\DocumentConfig;
use App\Services\Document\DocumentService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendInvoiceReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoices:send-reminders {--force : Envoyer même si un rappel a été envoyé récemment}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envoie des emails de relance pour les factures impayées dont la date d\'échéance est dépassée.';

    /**
     * Create a new command instance.
     */
    public function __construct(protected DocumentService $documentService)
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Début de l\'envoi des relances de factures...');

        // Récupérer les factures impayées dont l'échéance est dépassée
        $overdueInvoices = FactureClient::where('statut_facture', '!=', 'payée')
            ->where('date_echeance', '<', Carbon::today())
            ->where('montant_reste_a_payer', '>', 0)
            ->with('client')
            ->get();

        if ($overdueInvoices->isEmpty()) {
            $this->info('Aucune facture impayée en retard trouvée.');
            return;
        }

        $count = 0;
        foreach ($overdueInvoices as $invoice) {
            // Vérifier si un rappel a été envoyé récemment (ex: au cours des 7 derniers jours)
            if (!$this->option('force') && $invoice->last_reminder_sent_at) {
                $lastSent = Carbon::parse($invoice->last_reminder_sent_at);
                if ($lastSent->diffInDays(Carbon::now()) < 7) {
                    $this->line("Rappel déjà envoyé récemment pour la facture {$invoice->numero_facture} (le {$lastSent->format('d/m/Y')}). Passage...");
                    continue;
                }
            }

            if (!$invoice->client || !$invoice->client->mail_client) {
                $this->error("La facture {$invoice->numero_facture} n'a pas de client ou d'email client valide. Passage...");
                continue;
            }

            $this->info("Envoi de la relance pour la facture {$invoice->numero_facture} à {$invoice->client->mail_client}...");

            try {
                $config = DocumentConfig::forInvoiceReminder($invoice);
                $result = $this->documentService->sendEmailWithPdf($config);

                if (!$result['error']) {
                    $invoice->last_reminder_sent_at = Carbon::now();
                    // Mettre à jour le statut si nécessaire
                    if ($invoice->statut_facture !== 'En retard') {
                        $invoice->statut_facture = 'En retard';
                    }
                    $invoice->save();
                    $count++;
                    $this->info("Relance envoyée avec succès pour {$invoice->numero_facture}.");
                } else {
                    $this->error("Erreur lors de l'envoi de la relance pour {$invoice->numero_facture} : " . $result['message']);
                }
            } catch (\Exception $e) {
                $this->error("Exception lors de l'envoi de la relance pour {$invoice->numero_facture} : " . $e->getMessage());
                Log::error("Erreur relance facture {$invoice->numero_facture}: " . $e->getMessage());
            }
        }

        $this->info("Traitement terminé. {$count} relance(s) envoyée(s).");
    }
}
