<?php

namespace App\Http\Controllers;

use App\Models\Voyage;
use App\Models\VoyageCharge;
use App\Services\VoyageChargeService;
use Illuminate\Http\Request;

class VoyageChargeController extends Controller
{
    protected VoyageChargeService $voyageChargeService;

    public function __construct(VoyageChargeService $voyageChargeService)
    {
        $this->voyageChargeService = $voyageChargeService;
    }

    public function sync(Request $request, Voyage $voyage)
    {
        $validatedData = $request->validate([
            'voyage_charges' => 'nullable|array',
            'voyage_charges.*.id' => 'nullable|exists:voyage_charges,id',
            'voyage_charges.*.libelle' => 'required|string|max:255',
            'voyage_charges.*.montant' => 'required|numeric',
            'tresorerie_id' => 'required|exists:tresoreries,id',
            'mode_paiement' => 'required|string',
        ]);
        $output = $this->voyageChargeService->syncVoyageCharges(
            $voyage,
            $validatedData['voyage_charges'] ?? [],
            $validatedData['tresorerie_id'],
            $validatedData['mode_paiement']
        );

        return back()->with(
            $output['error'] ? 'message.error' : 'message.success',
            $output['message']
        );
    }

    public function destroy(Voyage $voyage, VoyageCharge $voyage_charge)
    {
        $output = $this->voyageChargeService->deleteCharge($voyage_charge);
        return back()->with(
            $output['error'] ? 'message.error' : 'message.success',
            $output['message']
        );
    }
}
