<?php

namespace App\Http\Controllers;

use App\Models\Voyage;
use App\Models\VoyageMarchandise;
use App\Services\VoyageMarchandiseService;
use Illuminate\Http\Request;

class VoyageMarchandiseController extends Controller
{
    protected VoyageMarchandiseService $voyageMarchandiseService;

    public function __construct(VoyageMarchandiseService $voyageMarchandiseService)
    {
        $this->voyageMarchandiseService = $voyageMarchandiseService;
    }

    public function sync(Request $request, Voyage $voyage)
    {
        $validatedData = $request->validate([
            'voyage_marchandises' => 'nullable|array',
            'voyage_marchandises.*.id' => 'nullable|exists:voyage_marchandises,id',
            'voyage_marchandises.*.designation' => 'required|string|max:255',
        ]);

        $output = $this->voyageMarchandiseService->syncVoyageMarchandises($voyage, $validatedData['voyage_marchandises'] ?? []);

        return back()->with(
            $output['error'] ? 'message.error' : 'message.success',
            $output['message']
        );
    }
}
