<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UdateDevisclient extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'date_devis' => 'required|date',
            'validite_devis' => 'required|date',
            'condition_delais' => 'required|string|max:255',
            'condition_paiement' => 'required|string|max:255',
            'nom_client' => 'required|string|max:255',
            'montant_total' => 'required|numeric',
            'details' => 'required|array|min:1',
            'details.*.libelle' => 'required|string|max:255',
            'details.*.quantite' => 'required|numeric',
            'details.*.prix_unitaire' => 'nullable|numeric',
            'details.*.montant' => 'nullable|numeric'
        ];
    }
}
