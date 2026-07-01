<?php

namespace App\Services;

use App\Models\Paie;
use App\Models\PaieElement;
use App\Models\Salarie;
use App\Models\PrimeConfig;
use App\Models\Voyage;
use App\Models\InfoSociete;
use App\Repositories\PaieRepository;
use App\Utils\NumberToWordsHelper;
use App\Services\Base\BaseService;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use App\Services\Document\DocumentConfig;
use App\Services\Document\DocumentService;

class PaieService extends BaseService
{
    protected $repository;
    protected DocumentService $documentService;
    protected array $relation = ['salarie', 'elements', 'salarie.typeSalarie'];
    protected array $scope = [
        'filter' => 'search',
        'mois' => 'mois',
        'annee' => 'annee',
        'statut' => 'statut'
    ];

    public function __construct(
        PaieRepository $paieRepository,
        DocumentService $documentService
    ) {
        $this->repository = $paieRepository;
        $this->documentService = $documentService;
        parent::__construct($paieRepository);
    }

    public function getAll(array $filters = [])
    {
        $perPage = $filters['per_page'] ?? config("app.pagination.per_page", 20);
        $page = $filters['current_page'] ?? 1;

        $query = Paie::with($this->relation);

        // Appliquer les scopes manuellement pour plus de sécurité
        if (!empty($filters['mois'])) $query->mois($filters['mois']);
        if (!empty($filters['annee'])) $query->annee($filters['annee']);
        if (!empty($filters['statut'])) $query->statut($filters['statut']);
        if (!empty($filters['search'])) $query->filter($filters['search']);

        return $query->orderBy('id', 'desc')->paginate($perPage, ['*'], 'page', $page);
    }

    public function generatePdf(Paie $paie)
    {
        $paie->load(['salarie', 'elements', 'salarie.typeSalarie']);
        $config = DocumentConfig::forPaie($paie);
        return $this->documentService->generateAndDownloadPdf($config['pdf']);
    }

    /**
     * Génère un PDF groupé pour toutes les fiches de paie filtrées
     */
    public function generateMassPdf(array $filters)
    {
        $paies = Paie::with(['salarie', 'elements', 'salarie.typeSalarie']);

        if (!empty($filters['mois'])) $paies->mois($filters['mois']);
        if (!empty($filters['annee'])) $paies->annee($filters['annee']);
        if (!empty($filters['statut'])) $paies->statut($filters['statut']);
        if (!empty($filters['search'])) $paies->filter($filters['search']);

        $data = $paies->orderBy('id', 'desc')->get();

        if ($data->isEmpty()) {
            throw new Exception("Aucune fiche de paie trouvée pour l'impression.");
        }

        $entreprise = InfoSociete::first();
        
        $config = [
            'template' => 'pdf.mass_bulletin_paie',
            'data' => [
                'paies' => $data,
                'entreprise' => $entreprise
            ],
            'folder' => 'paies',
            'filename' => 'Bulletins_Groupe_' . ($filters['mois'] ?? '') . '_' . ($filters['annee'] ?? ''),
            'paper_size' => 'A4',
            'orientation' => 'portrait',
            'force_regenerate' => true
        ];

        return $this->documentService->generateAndDownloadPdf($config);
    }

    /**
     * Génère un rapport PDF du tableau récapitulatif des paies
     */
    public function generateListReportPdf(array $filters)
    {
        $paies = Paie::with(['salarie', 'salarie.typeSalarie']);

        if (!empty($filters['mois'])) $paies->mois($filters['mois']);
        if (!empty($filters['annee'])) $paies->annee($filters['annee']);
        if (!empty($filters['statut'])) $paies->statut($filters['statut']);
        if (!empty($filters['search'])) $paies->filter($filters['search']);

        $data = $paies->orderBy('id', 'desc')->get();

        $totalNet = $data->sum('salaire_net');
        $total_lettre = ucfirst(NumberToWordsHelper::convert($totalNet));

        $moisNom = [
            1 => 'Janvier', 2 => 'Février', 3 => 'Mars', 4 => 'Avril', 5 => 'Mai', 6 => 'Juin',
            7 => 'Juillet', 8 => 'Août', 9 => 'Septembre', 10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre'
        ];
        $periode = ($moisNom[$filters['mois'] ?? 0] ?? '') . ' ' . ($filters['annee'] ?? '');

        $config = [
            'template' => 'pdf.paie_list_report',
            'data' => [
                'paies' => $data,
                'entreprise' => InfoSociete::first(),
                'periode' => $periode,
                'total_lettre' => $total_lettre
            ],
            'folder' => 'paies',
            'filename' => 'Rapport_Paies_' . ($filters['mois'] ?? '') . '_' . ($filters['annee'] ?? ''),
            'paper_size' => 'A4',
            'orientation' => 'landscape',
            'force_regenerate' => true
        ];

        return $this->documentService->generateAndDownloadPdf($config);
    }

    /**
     * Récupère les données pour la prévisualisation de la génération
     */
    public function getGenerationPreview(int $mois, int $annee): array
    {
        $salaries = Salarie::with(['typeSalarie', 'chauffeur'])->where('statut', 'actif')->get();
        $primeConfigs = PrimeConfig::where('is_actif', true)->get();
        
        $previewData = [];
        foreach ($salaries as $salarie) {
            // Vérifier si la paie existe déjà
            $exists = Paie::where('salarie_id', $salarie->id)
                ->where('mois', $mois)
                ->where('annee', $annee)
                ->exists();

            if ($exists) continue;

            $nbVoyages = 0;
            if ($salarie->chauffeur) {
                $chauffeurId = $salarie->chauffeur->id;
                $nbVoyages = Voyage::where(function($q) use ($chauffeurId) {
                        $q->where('chauffeur_id', $chauffeurId)
                          ->orWhere('aide_chauffeur_id', $chauffeurId);
                    })
                    ->whereMonth('date_voyage', $mois)
                    ->whereYear('date_voyage', $annee)
                    ->count();
            }

            // Identifier les primes automatiques (liées au type ou au voyage)
            $automaticPrimes = [];
            foreach ($primeConfigs as $config) {
                if ($config->type_salarie_id == $salarie->type_salarie_id || ($config->is_per_voyage && $salarie->chauffeur)) {
                    $montant = $config->montant;
                    $libelle = $config->libelle;
                    
                    if ($config->is_per_voyage && $nbVoyages > 0) {
                        $montant = $config->montant * $nbVoyages;
                        $libelle .= " ($nbVoyages voyages)";
                        $automaticPrimes[] = [
                            'config_id' => $config->id,
                            'libelle' => $libelle,
                            'montant' => $montant
                        ];
                    } elseif (!$config->is_per_voyage && $config->type_salarie_id == $salarie->type_salarie_id) {
                        $automaticPrimes[] = [
                            'config_id' => $config->id,
                            'libelle' => $libelle,
                            'montant' => $montant
                        ];
                    }
                }
            }

            $previewData[] = [
                'salarie_id' => $salarie->id,
                'matricule' => $salarie->matricule,
                'nom_complet' => $salarie->nom . ' ' . $salarie->prenom,
                'salaire_base' => $salarie->salaire ?? 0,
                'nb_voyages' => $nbVoyages,
                'automatic_primes' => $automaticPrimes,
            ];
        }

        return [
            'salaries' => $previewData,
            'prime_configs' => $primeConfigs->where('is_global', true)->values(),
        ];
    }

    /**
     * Génération en masse à partir des données de prévisualisation
     */
    public function bulkGenerate(int $mois, int $annee, array $data): array
    {
        DB::beginTransaction();
        try {
            $count = 0;
            foreach ($data as $item) {
                $paie = Paie::create([
                    'salarie_id' => $item['salarie_id'],
                    'mois' => $mois,
                    'annee' => $annee,
                    'salaire_base' => $item['salaire_base'],
                    'statut' => 'paye',
                    'date_paiement' => now(),
                    'mode_paiement' => 'Espèce',
                    'user_id' => auth()->id()
                ]);

                // 1. Ajouter les primes automatiques calculées en preview
                foreach ($item['automatic_primes'] ?? [] as $ap) {
                    $paie->elements()->create([
                        'type' => 'prime',
                        'libelle' => $ap['libelle'],
                        'montant' => $ap['montant']
                    ]);
                }

                // 2. Ajouter les primes globales cochées
                foreach ($item['selected_global_primes'] ?? [] as $gp) {
                    $paie->elements()->create([
                        'type' => 'prime',
                        'libelle' => $gp['libelle'],
                        'montant' => $gp['montant']
                    ]);
                }

                $paie->recalculate();
                $count++;
            }

            DB::commit();
            return $this->successResponse("$count fiches de paie générées avec succès.");
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Erreur bulk generate: " . $e->getMessage());
            return $this->errorResponse("Erreur lors de la génération", $e);
        }
    }

    /**
     * Génère les brouillons de paie pour un mois donné (Ancienne version, conservée par sécurité)
     */
    public function generate(int $mois, int $annee): array
    {
        DB::beginTransaction();
        try {
            $salaries = Salarie::with(['typeSalarie', 'chauffeur'])->where('statut', 'actif')->get();
            $totalActive = $salaries->count();
            $count = 0;

            if ($totalActive === 0) {
                return $this->errorResponse("Aucun salarié actif trouvé pour générer la paie.", new Exception("No active employees"));
            }

            // Récupérer toutes les configurations de primes
            $primeConfigs = PrimeConfig::all();

            foreach ($salaries as $salarie) {
                // Vérifier si la paie existe déjà
                $exists = Paie::where('salarie_id', $salarie->id)
                    ->where('mois', $mois)
                    ->where('annee', $annee)
                    ->exists();

                if (!$exists) {
                    $paie = Paie::create([
                        'salarie_id' => $salarie->id,
                        'mois' => $mois,
                        'annee' => $annee,
                        'salaire_base' => $salarie->salaire ?? 0,
                        'salaire_net' => $salarie->salaire ?? 0,
                        'statut' => 'brouillon',
                        'user_id' => auth()->id()
                    ]);

                    // Ajouter les primes automatiques
                    $this->applyAutomaticPrimes($paie, $salarie, $primeConfigs);

                    $count++;
                }
            }

            DB::commit();

            if ($count === 0) {
                return $this->successResponse("Toutes les fiches de paie pour ce mois ont déjà été générées.");
            }

            $message = $count === 1 
                ? "1 nouvelle fiche de paie a été générée avec succès." 
                : "$count nouvelles fiches de paie ont été générées avec succès.";

            return $this->successResponse($message);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Erreur génération paie: " . $e->getMessage());
            return $this->errorResponse("Erreur lors de la génération", $e);
        }
    }

    /**
     * Applique les primes automatiques à une fiche de paie
     */
    private function applyAutomaticPrimes(Paie $paie, Salarie $salarie, $primeConfigs): void
    {
        foreach ($primeConfigs as $config) {
            $applicable = false;

            // Vérifier si la prime est globale
            if ($config->is_global) {
                $applicable = true;
            } 
            // Vérifier si la prime est spécifique au type de salarié
            elseif ($config->type_salarie_id == $salarie->type_salarie_id) {
                $applicable = true;
            }

            if ($applicable) {
                $montant = $config->montant;
                $libelle = $config->libelle;

                // Si la prime est par voyage, on calcule le nombre de voyages
                if ($config->is_per_voyage && $salarie->chauffeur) {
                    $chauffeurId = $salarie->chauffeur->id;
                    $nbVoyages = Voyage::where(function($q) use ($chauffeurId) {
                            $q->where('chauffeur_id', $chauffeurId)
                              ->orWhere('aide_chauffeur_id', $chauffeurId);
                        })
                        ->whereMonth('date_voyage', $paie->mois)
                        ->whereYear('date_voyage', $paie->annee)
                        ->count();

                    if ($nbVoyages > 0) {
                        $montant = $config->montant * $nbVoyages;
                        $libelle .= " ($nbVoyages voyages)";
                    } else {
                        $applicable = false; // Ne pas ajouter si 0 voyage
                    }
                }

                if ($applicable) {
                    $paie->elements()->create([
                        'type' => 'prime',
                        'libelle' => $libelle,
                        'montant' => $montant
                    ]);
                }
            }
        }
        
        $paie->recalculate();
    }

    /**
     * Ajoute un élément variable (prime/retenue)
     */
    public function addElement(Paie $paie, array $data): array
    {
        try {
            $element = $paie->elements()->create($data);
            return $this->successResponse("Élément ajouté avec succès", $element);
        } catch (Exception $e) {
            return $this->errorResponse("Erreur lors de l'ajout", $e);
        }
    }

    /**
     * Valide et enregistre le paiement
     */
    public function processPayment(Paie $paie, array $data): array
    {
        if ($paie->statut === 'paye') {
            return $this->errorResponse("Cette paie est déjà réglée", new Exception("Status Error"));
        }

        try {
            // Mettre à jour la paie sans créer de mouvement de trésorerie
            $paie->update([
                'statut' => 'paye',
                'date_paiement' => $data['date_paiement'] ?? now(),
                'mode_paiement' => $data['mode_paiement'],
                'reference_paiement' => $data['reference_paiement'] ?? null,
                'telephone_paiement' => $data['telephone_paiement'] ?? null,
            ]);

            return $this->successResponse("Paiement enregistré avec succès");
        } catch (Exception $e) {
            return $this->errorResponse("Erreur lors du paiement", $e);
        }
    }
}
