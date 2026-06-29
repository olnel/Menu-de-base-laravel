<?php

namespace App\Services;

use App\Repositories\ArticleInventaireRepository;
use App\Services\Base\BaseService;
use App\Utils\ForamatDate;
use Illuminate\Support\Facades\DB;
use mysql_xdevapi\Exception;

class ArticleInventaireService extends BaseService
{
    protected $repository, $articleMouvemenet_service, $articleMagasin_service;
    protected array $scope =  ['filter' => 'search', 'magasin' => 'magasin_id', 'famillearticle' => 'article_famille_id', 'filterdatestart' => 'start_date', 'filterdateend' => 'end_date'];
    public function __construct(ArticleInventaireRepository $articleInventaireRepository, ArticleMouvementService $articleMouvementService,
                                ArticlemagasinService $articlemagasinService)
    {
        $this->repository = $articleInventaireRepository;
        $this->articleMouvemenet_service = $articleMouvementService;
        $this->articleMagasin_service = $articlemagasinService;

        parent::__construct($articleInventaireRepository);
    }

    // Your service methods go here
    protected function initializeFilters(): void
    {
        $this->setFilterValue('id')->setFilterLabel('id');
    }


    /**
     * Creates inventory records with related movements and stock updates
     */
    public function save(array $validated): array
    {
        $details = $validated['details'];
        $magasin_id = $validated['magasin_id'];
        $date_inventaire = $validated['date_inventaire'];
        $date_heure_enregistrement = date('Y-m-d H:i:s');

        try {
            foreach ($details as $detail) {
                $this->createInventoryRecord($magasin_id, $date_inventaire, $detail, $date_heure_enregistrement);
                $this->createMovementRecord($magasin_id, $date_inventaire, $date_heure_enregistrement, $detail);
                $this->updateStock($detail['article_id'], $magasin_id, $detail['stock_reel']);
            }
            return $this->successResponse('INSERTION TERMINER AVEC SUCCES');
        } catch (\Exception $e) {
            return $this->errorResponse('IL Y A ERREUR LORS DE L\'INSERTION', $e);
        }
    }


    private function createInventoryRecord(int $magasin_id, string $date_inventaire, array $detail, $date_heure_enregistrement): void
    {
        $data_detail = [
            'magasin_id' => $magasin_id,
            'date_inventaire' => $date_inventaire,
            'article_id' => $detail['article_id'],
            'user_id' => auth()->id(),
            'stock_theorique' => $detail['stock_theorique'],
            'stock_reel' => $detail['stock_reel'],
            'ecart_stock' => $detail['ecart_stock'],
            'remarque' => $detail['remarque'],
            'date_heure_enregistrment' => $date_heure_enregistrement
        ];

        parent::create($data_detail);
    }


    private function createMovementRecord(int $magasin_id, string $date_inventaire, string $date_heure_enregistrement, array $detail): void
    {
        $data_mouvement = [
            'article_id' => $detail['article_id'],
            'user_id' => auth()->id(),
            'magasin_id' => $magasin_id,
            'qte_initial' => $detail['stock_theorique'],
            'qte_mvt' => $detail['stock_reel'],
            'qte_final' => $detail['stock_reel'],
            'reference_mvt' => '',
            'date_mvt' => ForamatDate::normaliserDate($date_inventaire),
            'date_heure_enregistrement' => $date_heure_enregistrement,
            'commentaire_mvt' => $detail['remarque'],
            'operation_mvt' => 'INVENTAIRE',
        ];
        $this->articleMouvemenet_service->create($data_mouvement);
    }

    private function updateStock(mixed $article_id, mixed $magasin_id, mixed $stock_reel): void
    {
        $this->articleMagasin_service->updateOrCreateStock(
            $article_id,
            $magasin_id,
            $stock_reel
        );
    }
}
