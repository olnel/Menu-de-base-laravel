<?php

namespace App\Console\Commands;

use App\Models\CarburantCard;
use App\Models\CarburantMouvement;
use Illuminate\Console\Command;
use Carbon\Carbon;

class CreateRechargeMensuelCard extends Command
{
    protected $signature = 'cards:create-monthly-recharges';

    protected $description = 'Mouvement de recharge automatique pour chaque carte carburant active au début du mois.';

    public function handle()
    {
        $this->info('Début des recharges mensuelles automatiques pour les cartes carburant...');
        $activeCards = CarburantCard::where('active', true)->get();
        if ($activeCards->isEmpty()) {
            $this->info('Aucune carte active trouvée pour la recharge mensuelle.');
            return;
        }
        foreach ($activeCards as $card) {
            CarburantMouvement::create([
                'carburant_card_id' => $card->id,
                'type' => 'recharge',
                'montant' => $card->plafond_mensuel,
                'solde_avant' => $card->solde,
                'solde_apres' => $card->solde + $card->plafond_mensuel,
                'description' => 'Recharge mensuelle automatique',
                'date_mouvement' => now(),
            ]);
            $card->increment('solde', $card->plafond_mensuel);
            $this->info("Recharge automatique de {$card->plafond_mensuel} Ar effectuée pour la carte {$card->numero_carte}.");
        }

        $this->info('Recharges mensuelles automatiques terminées.');
    }
}
