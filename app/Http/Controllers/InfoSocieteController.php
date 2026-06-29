<?php

namespace App\Http\Controllers;

use App\Models\InfoSociete;
use App\Services\InfosocieteService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class InfoSocieteController extends Controller
{
    public function __construct(private readonly InfosocieteService $service)
    {

    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $output = InfoSociete::findOrFail(1);

        $currencies = json_decode(file_get_contents(public_path('currency.json')), true);

        return Inertia::render("InfoSociete/Index", [
            "data" => $output,
            "currencies" => $currencies,
            "flash" => session('flash', [])
        ]);
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
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
    public function update(Request $request, InfoSociete $infosociete)
    {
        $validated = $request->validate([
            'nom_societe' => 'required|string',
            'adresse_societe'=> 'nullable|string',
            'telephone_societe'=> 'nullable|string',
            'email_societe'=> 'nullable|string',
            'nif_societe'=> 'nullable|string',
            'logo_societe'=> 'nullable|file|mimes:jpeg,png,jpg',
            'stat_societe'=> 'nullable|string',
            'rcs_societe'=> 'nullable|string',
            'mail_password'=> 'nullable|string',
            'devise'=> 'required|string|max:10',
        ]);

        $output = $this->service->update($infosociete, $validated);
        return back()->with(
            $output['error'] ? 'message.error' : 'message.success',
            $output['message']
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
