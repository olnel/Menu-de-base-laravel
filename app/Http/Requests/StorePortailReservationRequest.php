<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePortailReservationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'date_reservation' => 'required|date',
            'lieu_chargement'  => 'required|string|max:255',
            'lieu_livraison'   => 'required|string|max:255',
            'nbr_vehicule'     => 'required|integer|min:1',
            'commentaire'      => 'nullable|string',
            'poids'            => 'nullable|string|max:100',
            'volume'           => 'nullable|string|max:100',
            'type_marchandise' => 'nullable|string|max:255',
        ];
    }
}
