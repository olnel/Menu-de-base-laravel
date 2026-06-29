<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FactureclientRequestStore extends FormRequest
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
            'date_reglement' => 'required|date',
            'facture_client_id' => 'required|string',
            'tresorerie_id' => 'required|string',
            'montant_reglement' => 'required|numeric',
            'mode_reglement' => 'required|string',
            'commentaire' => 'required|string',
        ];
    }
}
