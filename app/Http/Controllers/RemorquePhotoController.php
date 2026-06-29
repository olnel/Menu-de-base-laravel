<?php

namespace App\Http\Controllers;

use App\Models\RemorquePhoto;
use App\Models\VehiculePhoto;
use App\Services\RemorquePhotoService;
use App\Services\VehiculePhotoService;
use App\Utils\ForamatDate;
use Carbon\Carbon;
use Illuminate\Http\Request;

class RemorquePhotoController extends Controller
{
    private $service;

    public function __construct(RemorquePhotoService $remorquePhotoService)
    {
        $this->service = $remorquePhotoService;
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
            'remorque_id' => 'required',
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
    public function show(RemorquePhoto $remorquephoto)
    {

        $remorquephoto->liste_image = json_decode($remorquephoto->liste_image, true);

        return back()->with([
            'flash' => [
                'data' => $remorquephoto->toArray()
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
    public function update(Request $request, RemorquePhoto $remorquephoto)
    {

        $validated = $request->validate([
            'remorque_id' => 'required',
            'date_prise_photo' => 'required|date',
            'type_element' => 'required|string',
            'etat_vehicule' => 'nullable|string',
            'liste_image' => 'nullable|array',
            'existing_images' => 'nullable',
        ]);

        $validated['date_prise_photo'] = ForamatDate::normaliserDate($validated['date_prise_photo']);

        $output= $this->service->update($remorquephoto,$validated);

        return back()->with(
            $output['error'] ? 'message.error' : 'message.success',
            $output['message']
        );
    }

    /**-
     * Remove the specified resource from storage.
     */
    public function destroy(RemorquePhoto $remorquephoto)
    {
        try {
            $output = $this->service->delete($remorquephoto);

            return back()->with(
                $output['error'] ? 'message.error' : 'message.success',
                $output['message']
            );

        } catch (\Exception $e) {
            return back()->with('message.error', 'Une erreur est survenue lors de la suppression');
        }
    }
}
