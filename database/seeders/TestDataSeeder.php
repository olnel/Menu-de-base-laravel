<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\ArticleFamille;
use App\Models\Magasin;
use App\Models\PneuInventaire;
use App\Models\PneuMouvement;
use App\Models\PneuSerie;
use App\Models\Remorque;
use App\Models\User;
use App\Models\Vehicule;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class TestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // TRUNCATE vide complètement les tables (ignore soft deletes + remet auto-increment à 0)
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        DB::statement('TRUNCATE TABLE pneu_mouvements');
        DB::statement('TRUNCATE TABLE pneu_inventaires');
        DB::statement('TRUNCATE TABLE pneu_series');
        DB::statement('TRUNCATE TABLE article_magasin');
        DB::statement('TRUNCATE TABLE articles');
        DB::statement('TRUNCATE TABLE article_familles');
        DB::statement('TRUNCATE TABLE magasins');
        DB::statement('TRUNCATE TABLE chauffeur_vehicule');
        DB::statement('TRUNCATE TABLE vehicule_elements');
        DB::statement('TRUNCATE TABLE vehicule_photos');
        DB::statement('TRUNCATE TABLE vehicule_documents');
        DB::statement('TRUNCATE TABLE vehicules');
        DB::statement('TRUNCATE TABLE remorque_elements');
        DB::statement('TRUNCATE TABLE remorque_photos');
        DB::statement('TRUNCATE TABLE remorque_documents');
        DB::statement('TRUNCATE TABLE remorques');

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 0. Utilisateur par défaut
        $user = User::firstOrCreate(
            ['email' => 'admin@transmada.mg'],
            [
                'name' => 'Admin TransMada',
                'password' => Hash::make('password'),
            ]
        );

        // 1. Magasin (Warehouse)
        $magasin = Magasin::firstOrCreate(
            ['nom_magasin' => 'Magasin Central']
        );

        // 2. Familles d'articles
        $famillePneu = ArticleFamille::firstOrCreate(['nom_famille_article' => 'Pneumatiques']);
        $famillePiece = ArticleFamille::firstOrCreate(['nom_famille_article' => 'Pièces Détachées']);

        // 3. Articles (10 articles)
        $articlesPneu = [];
        for ($i = 1; $i <= 5; $i++) {
            $art = Article::create([
                'article_famille_id' => $famillePneu->id,
                'reference' => "PNEU-REF-00$i",
                'designation' => "Modèle Pneu Type $i",
                'type_article' => 'Pneu',
                'marque' => ($i % 2 == 0) ? 'Michelin' : 'Bridgestone',
                'valeur' => 400000 + ($i * 10000),
            ]);
            
            // article_magasin (Stock)
            $art->magasins()->attach($magasin->id, ['stock' => 100]);
            $articlesPneu[] = $art;
        }

        for ($i = 6; $i <= 10; $i++) {
            $art = Article::create([
                'article_famille_id' => $famillePiece->id,
                'reference' => "PIECE-REF-00$i",
                'designation' => "Pièce Détachée $i",
                'type_article' => 'CONSOMMABLE',
                'marque' => 'Bosch',
                'valeur' => 50000 + ($i * 5000),
            ]);
            
            // article_magasin (Stock)
            $art->magasins()->attach($magasin->id, ['stock' => 50]);
        }

        // 4. Véhicules (10 véhicules)
        $vehicules = [];
        for ($i = 1; $i <= 10; $i++) {
            $v = Vehicule::create([
                'immatriculation' => "TRUCK-$i-MAD",
                'marque' => ($i % 2 == 0) ? 'Renault' : 'Mercedes',
                'modele' => ($i % 2 == 0) ? 'Premium' : 'Actros',
                'num_chassis' => "VIN-VEH-000000000$i",
                'couleur' => ($i % 3 == 0) ? 'Bleu' : 'Blanc',
                'num_carte_grise' => "CG-VEH-00$i",
                'description' => "Véhicule de test numéro $i",
            ]);
            $vehicules[] = $v;
        }

        // 5. Remorques (10 remorques)
        $remorques = [];
        for ($i = 1; $i <= 10; $i++) {
            $r = Remorque::create([
                'numero_remorque' => "REM-00$i-MAD",
                'modele_remorque' => ($i % 2 == 0) ? 'Plateau' : 'Citerne',
                'marque_remorque' => ($i % 2 == 0) ? 'Lamberet' : 'Magyar',
            ]);
            $remorques[] = $r;
        }

        // 6. Pneu Series et Inventaires
        $dateInventaire = now()->format('Y-m-d');
        
        // Création de pneus "libres" (anciennement prévus pour véhicules)
        for ($i = 0; $i < 30; $i++) {
            $numeroSerie = "SN-FREE-V-" . ($i+1) . "-" . uniqid();
            $pneu = PneuSerie::create([
                'article_id' => $articlesPneu[0]->id,
                'numero_serie' => $numeroSerie,
                'etat' => 'neuf',
                'is_existe' => true,
                'occupe' => false,
                'type' => null,
                'vehicule_id' => null,
                'remorque_id' => null,
            ]);

            // Création de l'inventaire pour ce pneu
            $inventaire = PneuInventaire::create([
                'magasin_id'      => $magasin->id,
                'user_id'         => $user->id,
                'remarque'        => 'Inventaire initial - Pneu libre (Type V)',
                'date_inventaire' => $dateInventaire,
                'article_id'      => $pneu->article_id,
                'numero_serie'    => $pneu->numero_serie,
                'is_existe'       => true,
                'occupe'          => false,
                'etat'            => 'neuf',
                'type'            => null,
                'vehicule_id'     => null,
                'remorque_id'     => null,
            ]);

            // Mise à jour du pneu avec l'ID de l'inventaire
            $pneu->update(['pneu_inventaire_id' => $inventaire->id]);

            // Création du mouvement
            PneuMouvement::create([
                'pneu_serie_id'      => $pneu->id,
                'pneu_inventaire_id' => $inventaire->id,
                'article_id'         => $pneu->article_id,
                'magasin_id'         => $magasin->id,
                'user_id'            => $user->id,
                'numero_serie'       => $pneu->numero_serie,
                'is_existe'          => true,
                'etat'               => 'neuf',
                'type_mvt'           => 'INVENTAIRE',
                'reference_mvt'      => 'INV-PNEU-' . $inventaire->id . '-' . $pneu->id,
                'date_mvt'           => $dateInventaire,
                'date_heure_enregistrement' => now(),
            ]);
        }

        // Création de pneus "libres" (anciennement prévus pour remorques)
        for ($i = 0; $i < 30; $i++) {
            $numeroSerie = "SN-FREE-R-" . ($i+1) . "-" . uniqid();
            $pneu = PneuSerie::create([
                'article_id' => $articlesPneu[1]->id,
                'numero_serie' => $numeroSerie,
                'etat' => 'bon',
                'is_existe' => true,
                'occupe' => false,
                'type' => null,
                'vehicule_id' => null,
                'remorque_id' => null,
            ]);

            // Inventaire
            $inventaire = PneuInventaire::create([
                'magasin_id'      => $magasin->id,
                'user_id'         => $user->id,
                'remarque'        => 'Inventaire initial - Pneu libre (Type R)',
                'date_inventaire' => $dateInventaire,
                'article_id'      => $pneu->article_id,
                'numero_serie'    => $pneu->numero_serie,
                'is_existe'       => true,
                'occupe'          => false,
                'etat'            => 'bon',
                'type'            => null,
                'vehicule_id'     => null,
                'remorque_id'     => null,
            ]);

            $pneu->update(['pneu_inventaire_id' => $inventaire->id]);

            PneuMouvement::create([
                'pneu_serie_id'      => $pneu->id,
                'pneu_inventaire_id' => $inventaire->id,
                'article_id'         => $pneu->article_id,
                'magasin_id'         => $magasin->id,
                'user_id'            => $user->id,
                'numero_serie'       => $pneu->numero_serie,
                'is_existe'          => true,
                'etat'               => 'bon',
                'type_mvt'           => 'INVENTAIRE',
                'reference_mvt'      => 'INV-PNEU-' . $inventaire->id . '-' . $pneu->id,
                'date_mvt'           => $dateInventaire,
                'date_heure_enregistrement' => now(),
            ]);
        }

        // Pneus en stock additionnels (20 pneus)
        for ($i = 1; $i <= 20; $i++) {
            $numeroSerie = "SN-STK-$i-" . uniqid();
            $pneu = PneuSerie::create([
                'article_id' => $articlesPneu[2]->id,
                'numero_serie' => $numeroSerie,
                'etat' => 'neuf',
                'is_existe' => true,
                'occupe' => false,
                'type' => null,
                'vehicule_id' => null,
                'remorque_id' => null,
            ]);

            // Inventaire pour pneu en stock
            $inventaire = PneuInventaire::create([
                'magasin_id'      => $magasin->id,
                'user_id'         => $user->id,
                'remarque'        => 'Inventaire initial - Stock',
                'date_inventaire' => $dateInventaire,
                'article_id'      => $pneu->article_id,
                'numero_serie'    => $pneu->numero_serie,
                'is_existe'       => true,
                'occupe'          => false,
                'etat'            => 'neuf',
                'type'            => null,
                'vehicule_id'     => null,
                'remorque_id'     => null,
            ]);

            $pneu->update(['pneu_inventaire_id' => $inventaire->id]);

            PneuMouvement::create([
                'pneu_serie_id'      => $pneu->id,
                'pneu_inventaire_id' => $inventaire->id,
                'article_id'         => $pneu->article_id,
                'magasin_id'         => $magasin->id,
                'user_id'            => $user->id,
                'numero_serie'       => $pneu->numero_serie,
                'is_existe'          => true,
                'etat'               => 'neuf',
                'type_mvt'           => 'INVENTAIRE',
                'reference_mvt'      => 'INV-PNEU-' . $inventaire->id . '-' . $pneu->id,
                'date_mvt'           => $dateInventaire,
                'date_heure_enregistrement' => now(),
            ]);
        }
    }
}
