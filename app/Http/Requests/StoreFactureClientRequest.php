<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFactureClientRequest extends FormRequest
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
            'date_facture' => 'required|date',
            'date_echeance' => 'nullable|date',
            'montant_ht' => 'nullable|numeric',
            'montant_tva' => 'nullable|numeric',
            'montant_ttc' => 'nullable|numeric',
            'montant_payer' => 'nullable|numeric',
            'taux_tva' => 'nullable|numeric',
            'montant_voyage' => 'nullable|numeric',
            'statut_facture' => 'required|string',
            'client_id' => 'required|numeric',
            'remise' => 'nullable|numeric',
            'montant_remise' => 'nullable|numeric',
            'montant_reste_a_payer' => 'nullable|numeric|default:0',
            'voyages' => 'nullable|array',
        ];
    }
}
