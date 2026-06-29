<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreArticleApproRequest extends FormRequest
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
            'date_appro' => 'required|date',
            'fournisseur_id' => 'nullable|numeric',
            'montant_ht_appro' => 'required|numeric',
            'montant_tva_appro' => 'required|numeric',
            'montant_ttc_appro' => 'required|numeric',
            'montant_payer_appro' => 'required|numeric',
            'remise_general_ariary' => 'required|numeric',
            'taux_tva' => 'required|numeric',
            'details' => 'required|array|min:1',
            'details.*.article_id' => 'required|numeric|exists:articles,id',
            'details.*.magasin_id' => 'required|numeric|exists:articles,id',
            'details.*.prix_unitaire' => 'required|numeric|min:0',
            'details.*.quantite' => 'required|numeric',
            'details.*.montant' => 'required|numeric|min:0',
            'details.*.remise' => 'required|numeric|min:0',
            'details.*.remise_ariary' => 'required|numeric|min:0',
            'details.*.tva_detail' => 'required|numeric|min:0',
            'details.*.montant_tva' => 'required|numeric|min:0'
        ];
    }
}
