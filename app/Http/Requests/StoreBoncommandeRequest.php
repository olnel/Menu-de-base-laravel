<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBoncommandeRequest extends FormRequest
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
            'date_boncommande' => 'required|date',
            'nom_fournisseur' => 'nullable|string',
            'show_prix_unitaire' => 'required',
            'numero_bon_commande' => [
                'required',
                'string',
                Rule::unique('boncommande_fournisseurs')->whereNull('deleted_at')
            ],
            'details' => 'required|array|min:1',
            'details.*.article_id' => 'required|numeric|exists:articles,id',
            'details.*.qte_commander' => 'required|numeric',
            'details.*.prix_unitaire' => 'nullable|numeric'
        ];
    }
}
