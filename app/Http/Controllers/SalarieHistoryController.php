<?php

namespace App\Http\Controllers;

use App\Models\SalarieHistory;
use App\Models\Salarie;
use App\Models\User;
use App\Utils\ExtractFiltre;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SalarieHistoryController extends Controller
{
    public function index(Request $request)
    {
        $query = SalarieHistory::with(['user:id,name', 'salarie' => function($q) {
            $q->withTrashed();
        }]);

        // Filtrage par recherche (nom salarié ou nom utilisateur)
        if ($request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('salarie', function($sq) use ($search) {
                    $sq->withTrashed()->where('nom', 'LIKE', "%$search%")
                       ->orWhere('prenom', 'LIKE', "%$search%")
                       ->orWhere('matricule', 'LIKE', "%$search%");
                })->orWhereHas('user', function($uq) use ($search) {
                    $uq->where('name', 'LIKE', "%$search%");
                })->orWhere('action', 'LIKE', "%$search%");
            });
        }

        // Filtrage par action
        if ($request->action) {
            $query->where('action', $request->action);
        }

        // Filtrage par date
        if ($request->date_start) {
            $query->whereDate('created_at', '>=', $request->date_start);
        }
        if ($request->date_end) {
            $query->whereDate('created_at', '<=', $request->date_end);
        }

        $data = $query->orderBy('created_at', 'desc')->paginate(20);

        return Inertia::render('Salarie/HistoryGlobal', [
            'data' => $data,
            'filters' => [
                'search' => $request->search ?? '',
                'action' => $request->action ?? null,
                'date_start' => $request->date_start ?? null,
                'date_end' => $request->date_end ?? null,
            ],
            'actions_list' => SalarieHistory::distinct()->pluck('action'),
        ]);
    }
}
