<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTresorerieMouvementRequest extends FormRequest
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
            'tresorerie_id' => 'required|exists:tresoreries,id',
            'tresorerie_id_cible' => 'nullable|exists:tresoreries,id',
            'libelle_mvt' => 'required|string|max:255',
            'mode_paiement' => 'required|string|max:255',
            'type_mvt' => 'required|string|max:255',
            'montant' => 'required|numeric',
            'date_mvt' => 'required|date',
            'commentaire' => 'nullable|string',
            'poste_depense' => 'nullable|string',
        ];
    }
}
