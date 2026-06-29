<?php

namespace App\Http\Controllers;

use App\Models\VehiculePhoto;
use App\Services\VehiculePhotoService;
use App\Utils\ForamatDate;
use Carbon\Carbon;
use Illuminate\Http\Request;

class VehiculePhotoController extends Controller
{
    private $service;

    public function __construct(VehiculePhotoService $vehiculePhotoService)
    {
        $this->service = $vehiculePhotoService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {


        $validated = $request->validate([
            'vehicule_id' => 'required',
            'type_element' => 'required|string',
            'date_prise_photo' => 'required|date',
            'etat_vehicule' => 'nullable|string',
            'liste_image' => 'required|array|min:1',
        ]);



        $validated['date_prise_photo'] =  ForamatDate::normaliserDate($validated['date_prise_photo']);
        $output= $this->service->create($validated);

        return back()->with(
            $output['error'] ? 'message.error' : 'message.success',
            $output['message']
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(VehiculePhoto $vehiculephoto)
    {

        $vehiculephoto->liste_image = json_decode($vehiculephoto->liste_image, true);

        return back()->with([
            'flash' => [
                'data' => $vehiculephoto->toArray()
            ]
        ]);

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, VehiculePhoto $vehiculephoto)
    {

        $validated = $request->validate([
            'vehicule_id' => 'required',
            'date_prise_photo' => 'required|date',
            'type_element' => 'required|string',
            'etat_vehicule' => 'nullable|string',
            'liste_image' => 'nullable|array',
            'existing_images' => 'nullable',
        ]);
        $validated['date_prise_photo'] = ForamatDate::normaliserDate($validated['date_prise_photo']);

        $output= $this->service->update($vehiculephoto,$validated);

        return back()->with(
            $output['error'] ? 'message.error' : 'message.success',
            $output['message']
        );
    }

    /**-
     * Remove the specified resource from storage.
     */
    public function destroy(VehiculePhoto $vehiculephoto)
    {
        try {
            $output = $this->service->delete($vehiculephoto);

            return back()->with(
                $output['error'] ? 'message.error' : 'message.success',
                $output['message']
            );

        } catch (\Exception $e) {
            return back()->with('message.error', 'Une erreur est survenue lors de la suppression');
        }
    }
}
