<?php

namespace App\Console\Commands;

use App\Models\CarburantCard;
// Nous n'injectons plus CarburantMouvementService directement pour la recharge
// use App\Services\CarburantMouvementService;
use App\Services\CarburantCardService; // Nous utiliserons ce service à la place
use Illuminate\Console\Command;
use Carbon\Carbon;

class CreateRechargeMensuelCard extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cards:create-monthly-recharges';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mouvement de recharge automatique pour chaque carte carburant active au début du mois.';
    protected $carburantCardService;
    public function __construct(CarburantCardService $carburantCardService)
    {
        parent::__construct();
        $this->carburantCardService = $carburantCardService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Début des recharges mensuelles automatiques pour les cartes carburant...');
        // Récupérer uniquement les cartes actives
        $activeCards = CarburantCard::where('active', true)->get();
        if ($activeCards->isEmpty()) {
            $this->info('Aucune carte active trouvée pour la recharge mensuelle.');
            return;
        }
        foreach ($activeCards as $card) {
            $result = $this->carburantCardService->processRechargeOrAdjustment($card,null,"recharge mensuel tout les debut du mois",null);
            if (!empty($result['error'])) {
                $this->error("Échec de la recharge automatique pour la carte {$card->numero_carte}: " . $result['message']);
            } else {
                $this->info("Recharge automatique de {$card->plafond_mensuel} Ar effectuée pour la carte {$card->numero_carte}.");
            }
        }

        $this->info('Recharges mensuelles automatiques terminées.');
    }
}
