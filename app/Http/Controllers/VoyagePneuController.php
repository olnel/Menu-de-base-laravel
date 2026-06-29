<?php

namespace App\Http\Controllers;

use App\Models\Voyage;
use App\Services\VoyagePneuService;
use Illuminate\Http\Request;

class VoyagePneuController extends Controller
{
    public function __construct(protected VoyagePneuService $voyagePneuService) {}

    public function sync(Request $request, Voyage $voyage)
    {
        $validated = $request->validate([
            'pneus'                  => 'nullable|array',
            'pneus.*.pneu_serie_id'  => 'nullable|exists:pneu_series,id',
            'pneus.*.numero_serie'   => 'nullable|string',
            'pneus.*.position'       => 'nullable|string|max:100',
            'pneus.*.designation'    => 'nullable|string|max:255',
            'pneus.*.etat'           => 'nullable|string|max:50',
            'pneus.*.is_secours'     => 'nullable|boolean',
        ]);

        $output = $this->voyagePneuService->syncPneus($voyage, $validated['pneus'] ?? []);

        return back()->with(
            $output['error'] ? 'message.error' : 'message.success',
            $output['message']
        );
    }
}
