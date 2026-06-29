<?php

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\ArticleApprovisionnementController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\ArticleFamilleController;
use App\Http\Controllers\ArticleInventaireController;
use App\Http\Controllers\ArticleMouvementController;
use App\Http\Controllers\ArticleTransactionController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\BoncommandeController;
use App\Http\Controllers\CalendrierController;
use App\Http\Controllers\CarburantCardController;
use App\Http\Controllers\CarburantMvmtController;
use App\Http\Controllers\CarburantTransactionController;
use App\Http\Controllers\ChauffeurController;
use App\Http\Controllers\ChauffeurDocumentController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DevisClientController;
use App\Http\Controllers\DocumentDynamicController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\FactureClientController;
use App\Http\Controllers\FactureClientReglementController;
use App\Http\Controllers\FormationController;
use App\Http\Controllers\FournisseurController;
use App\Http\Controllers\ImmobilisationController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\InfoSocieteController;
use App\Http\Controllers\LibelleMaintenanceController;
use App\Http\Controllers\MagasinController;
use App\Http\Controllers\MainController;
use App\Http\Controllers\NumeroSerieController;
use App\Http\Controllers\PaieController;
use App\Http\Controllers\ParamElementController;
use App\Http\Controllers\PlanningCalendarController;
use App\Http\Controllers\PneuInventaireController;
use App\Http\Controllers\PneuRemplacementController;
use App\Http\Controllers\PosteDepenseController;
use App\Http\Controllers\PrimeConfigController;
use App\Http\Controllers\ReclamationController;
use App\Http\Controllers\RemorqueController;
use App\Http\Controllers\RemorqueDocumentController;
use App\Http\Controllers\RemorquePhotoController;
use App\Http\Controllers\RentabiliteController;
use App\Http\Controllers\ReparationVehiculeController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\SalarieController;
use App\Http\Controllers\SalarieHistoryController;
use App\Http\Controllers\SmsController;
use App\Http\Controllers\TarifController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\TresorerieController;
use App\Http\Controllers\TresorerieFluxController;
use App\Http\Controllers\TresorerieMouvementController;
use App\Http\Controllers\TypeSalarieController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserGroupController;
use App\Http\Controllers\VehiculeController;
use App\Http\Controllers\VehiculeDocumentController;
use App\Http\Controllers\VehiculePhotoController;
use App\Http\Controllers\VoyageChargeController;
use App\Http\Controllers\VoyageController;
use App\Http\Controllers\VoyageMarchandiseController;
use App\Http\Controllers\VoyagePneuController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

require __DIR__ . '/auth.php';

Route::middleware(['auth', 'log_activity'])->group(function () {
    Route::get("/", [MainController::class, 'index'])->name("dashboard");

    // Journal des actions utilisateurs
    Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity_log.index');

    // Dashboards
    Route::get("/dashboard/voyage", [DashboardController::class, 'voyage'])->name("dashboard.voyage");
    Route::get("/dashboard/vehicule", [DashboardController::class, 'vehicule'])->name("dashboard.vehicule");
    Route::get("/dashboard/comptablite", [DashboardController::class, 'comptablite'])->name("dashboard.comptablite");
    Route::get("/dashboard/carburant", [DashboardController::class, 'carburant'])->name("dashboard.carburant");
    Route::get("/dashboard/pneu", [DashboardController::class, 'pneu'])->name("dashboard.pneu");
    Route::get("/dashboard/chauffeur", [DashboardController::class, 'chauffeur'])->name("dashboard.chauffeur");
    Route::get("/dashboard/rentabilite", [RentabiliteController::class, 'index'])->name("dashboard.rentabilite");
    Route::get("/dashboard/rentabilite/export", [RentabiliteController::class, 'export'])->name("dashboard.rentabilite.export");

    // Flotte
    Route::resource("/vehicule", VehiculeController::class);
    Route::resource("/remorque", RemorqueController::class);
    Route::get('/vehicule/informations/{vehicule}', [VehiculeController::class, 'details'])->name('vehicule.info');
    Route::get('/remorque/informations/{remorque}', [RemorqueController::class, 'details'])->name('remorque.info');

    // Maintenance
    Route::resource("/reparation_vehicule", ReparationVehiculeController::class);
    Route::get("/reparation_vehicules/get-articles-by-magasin", [ReparationVehiculeController::class, 'getArticlesByMagasin'])->name('reparation_vehicule.get_articles_by_magasin');
    Route::resource("/pneu_inventaire", PneuInventaireController::class);
    Route::get("/pneu_inventaire/get-sup-data-by-magasin", [PneuInventaireController::class, 'getSupDataByMagasin'])->name('pneu_inventaire.get_sup_data_by_magasin');
    Route::resource("/pneu_remplacement", PneuRemplacementController::class);
    Route::get("/pneu_remplacement/search-pneus", [PneuRemplacementController::class, 'searchPneus'])->name('pneu_remplacement.search_pneus');

    // Paramètres
    Route::resource("/paramelement", ParamElementController::class);
    Route::resource("/libelle_maintenance", LibelleMaintenanceController::class);

    // Info société
    Route::get("/infosociete", [InfoSocieteController::class, 'index'])->name('infosociete.index');
    Route::put("/infosociete/{infosociete}", [InfoSocieteController::class, 'update'])->name('infosociete.update');

    // Tarifs
    Route::resource("/tarif", TarifController::class)->only(['index', 'store', 'show', 'update', 'destroy']);

    // Photos & Documents
    Route::resource("/vehiculephoto", VehiculePhotoController::class);
    Route::resource("/remorquephoto", RemorquePhotoController::class);
    Route::resource("/vehiculedocument", VehiculeDocumentController::class);
    Route::resource("/remorquedocument", RemorqueDocumentController::class);

    // Tickets pour les réparations
    Route::prefix('reparation_vehicule/{reparation_vehicule}/tickets')->name('reparation_vehicule.tickets.')->group(function () {
        Route::post('/generate', [TicketController::class, 'generate'])->name('generate');
        Route::get('/download-all', [TicketController::class, 'downloadAllPdf'])->name('download_all');
    });
    Route::get('/tickets/{ticket}/download', [TicketController::class, 'downloadPdf'])->name('tickets.download');

    // Planning
    Route::resource("/planning_calendar", PlanningCalendarController::class);

    // Stock / Magasin
    Route::resource("/magasin", MagasinController::class);
    Route::resource("/article", ArticleController::class);
    Route::resource("/article_famille", ArticleFamilleController::class);
    Route::get("/articles/getarticle", [ArticleController::class, 'getArticle'])->name('article.getarticle');
    Route::get("/articles/{article}/pneu-series", [ArticleController::class, 'getPneuSeries'])->name('article.pneu_series');
    Route::resource("/article_inventaire", ArticleInventaireController::class);
    Route::resource("/article_flux", ArticleMouvementController::class);
    Route::resource("/article_mouvement", ArticleTransactionController::class);
    Route::get("/article_mouvements/getreference", [ArticleTransactionController::class, 'generatereference'])->name('article_mouvement.getreference');
    Route::resource("/article_approvisionnement", ArticleApprovisionnementController::class);
    Route::get("/article/fetch/{magasin}", [ArticleController::class, 'fetchByMagasin'])->name('article.bymagasin_info');
    Route::resource("/article_boncommande", BoncommandeController::class);
    Route::get("/articles_boncommandes/getnumero", [BoncommandeController::class, 'generatenumero'])->name('article_boncommande.generatenumero');
    Route::get("/articles_boncommandes/{article_boncommande}/print", [BoncommandeController::class, 'print'])->name('article_boncommande.print');
    Route::get("/article_boncommandes/{article_boncommande}/sendMail", [BoncommandeController::class, 'sendMail'])->name('article_boncommande.sendMail');

    // Numéro de série
    Route::post('/numero-serie/fetch', [NumeroSerieController::class, 'fetch'])->name('numero-serie.fetch');
    Route::get('/numero-serie/search', [NumeroSerieController::class, 'searchJson'])->name('numero-serie.search');

    // Calendrier
    Route::get("/calendrier", [CalendrierController::class, 'index'])->name('calendrier.index');

    // Trésorerie
    Route::resource("/tresorerie", TresorerieController::class);
    Route::resource("/tresorerie_mouvement", TresorerieMouvementController::class);
    Route::get("/tresorerieflux", [TresorerieFluxController::class, 'index'])->name("tresorerieflux.index");

    // Sauvegardes
    Route::prefix('backups')->name('backups.')->group(function () {
        Route::get('/',                    [BackupController::class, 'index'])->name('index');
        Route::post('/',                   [BackupController::class, 'store'])->name('store');
        Route::get('/{backup}/download',   [BackupController::class, 'download'])->name('download');
        Route::delete('/{backup}',         [BackupController::class, 'destroy'])->name('destroy');
    });

    // Utilisateurs & Groupes
    Route::resource("user", UserController::class);
    Route::resource("/group_user", UserGroupController::class);

    // Salariés / RH
    Route::resource("/type_salarie", TypeSalarieController::class);
    Route::get("/salarie/history-global", [SalarieHistoryController::class, 'index'])->name('salarie.history_global');
    Route::get("/salarie/{salarie}/history", [SalarieController::class, 'history'])->name('salarie.history');
    Route::resource("/salarie", SalarieController::class);
    Route::resource("/prime_config", PrimeConfigController::class);

    // Formations
    Route::prefix('formations')->name('formations.')->group(function () {
        Route::get('/',                              [FormationController::class, 'index'])->name('index');
        Route::get('/liste',                         [FormationController::class, 'liste'])->name('liste');
        Route::post('/',                             [FormationController::class, 'store'])->name('store');
        Route::get('/{formation}',                   [FormationController::class, 'show'])->name('show');
        Route::put('/{formation}',                   [FormationController::class, 'update'])->name('update');
        Route::delete('/{formation}',                [FormationController::class, 'destroy'])->name('destroy');
        Route::get('/{formation}/derniere-participants', [FormationController::class, 'derniereSessionParticipants'])->name('derniere_session.participants');
        Route::get('/{formation}/participants-precedente', [FormationController::class, 'participantsPrecedente'])->name('participants.precedente');
        Route::post('/sessions',                     [FormationController::class, 'storeSession'])->name('sessions.store');
        Route::get('/sessions/{session}/participants', [FormationController::class, 'sessionParticipants'])->name('sessions.participants');
        Route::get('/sessions/{session}/print', [FormationController::class, 'printSession'])->name('sessions.print');
        Route::get('/{formation}/sessions', [FormationController::class, 'getSessions'])->name('sessions');
    });

    // Postes de Dépense
    Route::resource("/postedepense", PosteDepenseController::class);

    // Paie
    Route::prefix('paie')->name('paie.')->group(function () {
        Route::get('/', [PaieController::class, 'index'])->name('index');
        Route::get('/preview', [PaieController::class, 'preview'])->name('preview');
        Route::post('/generate', [PaieController::class, 'generate'])->name('generate');
        Route::put('/{paie}', [PaieController::class, 'update'])->name('update');
        Route::post('/{paie}/element', [PaieController::class, 'storeElement'])->name('element.store');
        Route::delete('/element/{element}', [PaieController::class, 'destroyElement'])->name('element.destroy');
        Route::post('/{paie}/pay', [PaieController::class, 'pay'])->name('pay');
        Route::get('/mass-print', [PaieController::class, 'massPrint'])->name('mass-print');
        Route::get('/print-list', [PaieController::class, 'printList'])->name('print-list');
        Route::get('/{paie}/print', [PaieController::class, 'print'])->name('print');
        Route::delete('/{paie}', [PaieController::class, 'destroy'])->name('destroy');
    });

    // Gestion Documentaire Dynamique
    Route::prefix('documents-administratifs')->name('dynamic.documents.')->group(function () {
        Route::get('/', [DocumentDynamicController::class, 'index'])->name('index');
        Route::get('/config', [DocumentDynamicController::class, 'configIndex'])->name('config');
        Route::resource('/config-models', DocumentDynamicController::class)->names([
            'index' => 'config-models.index',
            'store' => 'config.store',
            'show' => 'config.show',
            'update' => 'config.update',
            'destroy' => 'config.destroy',
        ])->except(['edit', 'create']);
        Route::get('/required', [DocumentDynamicController::class, 'getRequiredDocuments'])->name('required');
        Route::get('/entity-documents', [DocumentDynamicController::class, 'getEntityDocuments'])->name('entity.documents');
        Route::post('/upload', [DocumentDynamicController::class, 'upload'])->name('upload');
        Route::delete('/delete/{document}', [DocumentDynamicController::class, 'deleteDocument'])->name('delete');
    });

    // Chauffeurs
    Route::resource("/chauffeur", ChauffeurController::class);
    Route::resource('chauffeur_documents', ChauffeurDocumentController::class);
    Route::get('/chauffeurs/{chauffeur}/informations', [ChauffeurController::class, 'showInformations'])->name('chauffeur.informations');

    // Carburant
    Route::resource("/carburant_cards", CarburantCardController::class);
    Route::get('/updateCardStatus/{carburantcard}', [CarburantCardController::class, 'updateCardStatus'])->name('updateCardStatus');
    Route::resource("/carburant_mouvements", CarburantMvmtController::class);
    Route::resource("/carburant_transactions", CarburantTransactionController::class);
    Route::get('/carburant_cards/{carburantcard}/history', [CarburantMvmtController::class, 'index'])->name('carburant_cards.history');
    Route::post('/carburant_cards/recharge', [CarburantCardController::class, 'HandleRechargeCard'])->name('carburant_cards.RechargeCard');

    // Fournisseurs
    Route::resource("/fournisseur", FournisseurController::class);

    // Clients, Factures, Devis
    Route::resource("/client", ClientController::class);
    Route::post('/client/{client}/reset-password', [ClientController::class, 'resetPassword'])->name('client.reset-password');
    Route::resource("/factureclient", FactureClientController::class);
    Route::get("/factureclients/getnumero", [FactureClientController::class, 'generateInvoiceNumber'])->name('factureclient.generatenumero');
    Route::get("/factureclients/reglement/{factureclient}", [FactureClientController::class, 'fetchReglement'])->name('factureclient.showreglement');
    Route::prefix('factureclients/{factureclient}')->group(function () {
        Route::get('print', [FactureClientController::class, 'print'])->name('factureclient.print');
        Route::get('send-mail', [FactureClientController::class, 'sendMail'])->name('factureclient.sendMail');
    });
    Route::resource("/devisclient", DevisClientController::class);
    Route::resource("/factureclientreglement", FactureClientReglementController::class);
    Route::get("/factureclientreglements/{factureclient}", [FactureClientReglementController::class, 'historiqueReglement'])->name('factureclientreglement.historique');
    Route::get("/devisclients/getnumero", [DevisClientController::class, 'generatenumero'])->name('devisclient.generatenumero');
    Route::prefix('devisclients/{devisclient}')->group(function () {
        Route::get('print', [DevisClientController::class, 'print'])->name('devisclient.print');
        Route::get('send-mail', [DevisClientController::class, 'sendMail'])->name('devisclients.sendMail');
    });

    // Réclamations
    Route::resource("/reclamation", ReclamationController::class)->only(['index', 'update', 'destroy']);

    // Réservations & Voyages
    Route::get('/reservation/generatenumero', [ReservationController::class, 'generatenumero'])->name('reservation.generatenumero');
    Route::resource("/reservation", ReservationController::class);
    Route::resource('/voyages', VoyageController::class);
    Route::get('/voyages/{voyage}/details', [VoyageController::class, 'details'])->name('voyages.details');
    Route::patch('/voyages/{voyage}/suivi', [VoyageController::class, 'updateSuivi'])->name('voyages.suivi');
    Route::prefix('voyages/{voyage}')->group(function () {
        Route::put('marchandises/sync', [VoyageMarchandiseController::class, 'sync'])->name('voyages.marchandises.sync');
        Route::put('charges/sync', [VoyageChargeController::class, 'sync'])->name('voyages.charges.sync');
        Route::delete('charges/{voyage_charge}', [VoyageChargeController::class, 'destroy'])->name('voyages.charges.destroy');
        Route::put('pneus/sync', [VoyagePneuController::class, 'sync'])->name('voyages.pneus.sync');
    });

    // SMS
    Route::get('/sms', [SmsController::class, 'create'])->name('sms.create');
    Route::post('/sms/send', [SmsController::class, 'send'])->name('sms.send');
    Route::post('/sms/send-bulk', [SmsController::class, 'sendBulk'])->name('sms.send-bulk');
    Route::get('/sms/balance', [SmsController::class, 'getBalance'])->name('sms.balance');

    // Import / Export
    Route::post('import-excel', [ImportController::class, 'import'])->name('import.excel');
    Route::post('import-excel-chauffeur-all', [ImportController::class, 'import_chauffeur_all'])->name('import.chauffeur');
    Route::post('import-excel-vehicule', [ImportController::class, 'import_vehicule'])->name('import.vehicule.standard');
    Route::post('import-excel-chauffeur', [ImportController::class, 'import_chauffeur'])->name('import.chauffeur.standard');
    Route::get('modele/document', [ImportController::class, 'index'])->name('modele.document.import');

    // Export data
    Route::get("export/tresoreries", [ExportController::class, 'export'])->name('tresorerie.export');
    Route::get("export/tresorerie_mouvements", [ExportController::class, 'export'])->name('tresorerie_mouvement.export');
    Route::get("export/tresoreries_fluxs", [ExportController::class, 'export'])->name('tresorerie_flux.export');
    Route::get("export/article_approvisionnements", [ExportController::class, 'export'])->name('article_approvisionnement.export');
    Route::get("export/paie", [ExportController::class, 'export'])->name('paie.export');
    Route::get("export/facture_clients", [ExportController::class, 'export'])->name('facture_client.export');
    Route::get("export/devis_clients", [ExportController::class, 'export'])->name('devis_clients.export');
    Route::get("export/clients", [ExportController::class, 'export'])->name('client.export');
    Route::get("export/fournisseurs", [ExportController::class, 'export'])->name('fournisseur.export');
    Route::get("export/articles", [ExportController::class, 'export'])->name('article.export');
    Route::get("export/article_inventaires", [ExportController::class, 'export'])->name('article_inventaire.export');
    Route::get("export/article_mouvements", [ExportController::class, 'export'])->name('article_mouvement.export');
    Route::get("export/article_flux", [ExportController::class, 'export'])->name('article_flux.export');
    Route::get("export/pneu_inventaires", [ExportController::class, 'export'])->name('pneu_inventaire.export');
    Route::get("export/chauffeurs", [ExportController::class, 'export'])->name('chauffeur.export');
    Route::get("export/reservations", [ExportController::class, 'export'])->name('reservation.export');
    Route::get("export/voyages", [ExportController::class, 'export'])->name('voyage.export');
    Route::get("export/carburant_card", [ExportController::class, 'export'])->name('carburant_card.export');
    Route::get("export/carburant_card_mouvement", [ExportController::class, 'export'])->name('carburant_card_mouvement.export');
    Route::get("export/vehicule", [ExportController::class, 'export'])->name('vehicule.export');
    Route::get("export/remorque", [ExportController::class, 'export'])->name('remorque.export');
    Route::get("export/vehiculedocument", [ExportController::class, 'export'])->name('vehiculedocument.export');
    Route::get("export/planning_calendar", [ExportController::class, 'export'])->name('planning_calendar.export');
    Route::get("export/magasin", [ExportController::class, 'export'])->name('magasin.export');
    Route::get("export/group_user", [ExportController::class, 'export'])->name('group_user.export');
    Route::get("export/user", [ExportController::class, 'export'])->name('user.export');
    Route::get("export/paramelement", [ExportController::class, 'export'])->name('paramelement.export');
    Route::get("export/libelle_maintenance", [ExportController::class, 'export'])->name('libelle_maintenance.export');
});
