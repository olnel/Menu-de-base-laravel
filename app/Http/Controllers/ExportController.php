<?php

namespace App\Http\Controllers;

use App\Exports\ExportDataexcel;
use App\Utils\ExtractFiltre;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ExportController extends Controller
{

    public function export(Request $request)
    {
        $type = basename($request->path());

        $type = explode('.', $request->route()->getName())[0];

        $request->validate([
            'exportable' => 'required',
        ]);

        $exportable = json_decode($request->exportable); 
        $models = [
            'tresorerie' => \App\Models\Tresorerie::class,
            'tresorerie_mouvement' => \App\Models\TresorerieMouvement::class,
            'tresorerie_flux' => \App\Models\TresorerieFlux::class,
            'article_approvisionnement' => \App\Models\ArticleApprovisionnement::class,
            'facture_client' => \App\Models\FactureClient::class,
            'client' => \App\Models\Client::class,
            'fournisseur' => \App\Models\Fournisseur::class,
            'article' => \App\Models\Article::class,
            'article_inventaire' => \App\Models\ArticleInventaire::class,
            'article_mouvement' => \App\Models\ArticleTransaction::class,
            'article_flux' => \App\Models\ArticleMouvement::class,
            'pneu_inventaire' => \App\Models\PneuInventaire::class,
            'chauffeur' => \App\Models\Chauffeur::class,
            'reservation' => \App\Models\Reservation::class,
            'voyage' => \App\Models\Voyage::class,
            'carburant_card' => \App\Models\CarburantCard::class,
            'carburant_card_mouvement' => \App\Models\CarburantMouvement::class,
            'vehicule' => \App\Models\Vehicule::class,
            'remorque' => \App\Models\Remorque::class,
            'vehiculedocument' => \App\Models\VehiculeDocument::class,
            'planning_calendar' => \App\Models\PlanningCalendar::class,
            'magasin' => \App\Models\Magasin::class,
            'group_user' => \App\Models\UserGroup::class,
            'user' => \App\Models\User::class,
            'paramelement' => \App\Models\ParamElement::class,
            'libelle_maintenance' => \App\Models\LibelleMaintenance::class,
            'paie' => \App\Models\Paie::class,
        ];

        if (!array_key_exists($type, $models)) {
            abort(404, "Type d'export inconnu : {$type}");
        }

        $modelClass = $models[$type];

        $query = $modelClass::query();

        if ($type === 'user') {
            $query->with('group')->isdna();
        }

        if ($type === 'article_inventaire') {
            $query->with(['article.famille', 'magasin', 'user']);
        }

        if ($type === 'article_mouvement') {
            $query->with(['user', 'magasin', 'magasincible', 'vehicule']);
        }

        if ($type === 'article_flux') {
            $query->with(['article.famille', 'magasin', 'user']);
        }

        if ($type === 'pneu_inventaire') {
            $query->with(['article', 'magasin', 'user', 'vehicule', 'remorque']);
        }

        if ($type === 'paie') {
            $query->with(['salarie', 'salarie.typeSalarie']);
            if ($request->has('mois')) $query->where('mois', $request->mois);
            if ($request->has('annee')) $query->where('annee', $request->annee);
            if ($request->has('statut')) $query->where('statut', $request->statut);
        }

        if (method_exists($modelClass, 'filter')) {
            $query = $modelClass::filter($request->search);
        }

        $data = $query->get();
        $title = $type;
        $exportType = $request->input('type_export', 'pdf');
        
        return (new ExportDataexcel($exportable, $data, $title, $exportType))->download();
    }


}
