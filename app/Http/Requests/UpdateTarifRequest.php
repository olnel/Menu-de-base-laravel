<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTarifRequest extends FormRequest
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
        $tarif = $this->route('tarif');
        return [
            'nom_tarif' => [
                'required',
                'string',
                Rule::unique('tarifs') ->ignore($tarif->id)->whereNull('deleted_at'),
            ],
            'details' => 'required|array|min:1',
            'details.*.libelle' => 'required|string|max:255',
            'details.*.prix_ht' => 'required|numeric',
            'details.*.tva' => 'nullable|numeric',
            'details.*.prix_ttc' => 'nullable|numeric'
        ];
    }
}
