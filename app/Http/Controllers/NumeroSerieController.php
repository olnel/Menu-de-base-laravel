<?php

namespace App\Http\Controllers;

use App\Services\PneuSerieService;
use Illuminate\Http\Request;

class NumeroSerieController extends Controller
{
    public function __construct(private readonly PneuSerieService $pneuSerieService){}


    public function fetch(Request $request)
    {
        $search = $request->input('search');
        $data = $this->pneuSerieService->getPneusNonAssignes($search);
        return back()->with([
            'flash' => [
                'data' => $data->toArray()
            ]
        ]);
    }

    public function searchJson(Request $request)
    {
        $validated = $request->validate([
            'search'    => 'nullable|string|max:100',
            'exclude'   => 'nullable|array',
            'exclude.*' => 'string|max:255',
        ]);

        $pneus = $this->pneuSerieService->getPneusDisponibles(
            $validated['search']  ?? null,
            $validated['exclude'] ?? [],
        );

        return response()->json(['pneus' => $pneus]);
    }
}
