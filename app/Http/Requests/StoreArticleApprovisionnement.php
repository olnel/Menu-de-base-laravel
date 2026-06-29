<?php

namespace App\Http\Requests;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Http\FormRequest;

class StoreArticleApprovisionnement extends FormRequest
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
            'numero_bon_livraison' => 'required|string',
            'boncommande_fournisseur_id' => 'nullable|string',
            'nom_fournisseur' => 'required|string',
            'maj_prix_article' => 'nullable|boolean',
            'montant_ht_appro' => 'required|numeric',
            'montant_tva_appro' => 'required|numeric',
            'montant_ttc_appro' => 'required|numeric',
            'montant_a_payer_appro' => 'required|numeric',
            'montant_payer_appro' => 'required|numeric',
            'montant_reste_a_payer_appro' => 'required|numeric',
            'remise_general' => 'required|numeric',
            'magasin_id' => 'required|numeric',
            'remise_general_ariary' => 'required|numeric',
            'numero_bon_commande' => 'nullable|string',
            'taux_tva' => 'required|numeric',
            'type_approvisionnement' => 'nullable|string',
            'details' => 'required|array|min:1',
            'details.*.article_id' => 'required|numeric|exists:articles,id',
            'details.*.magasin_id' => 'required|numeric|exists:articles,id',
            'details.*.prix_unitaire' => 'required|numeric|min:0',
            'details.*.quantite' => 'required|numeric',
            'details.*.montant' => 'required|numeric|min:0',
            'details.*.remise' => 'required|numeric|min:0',
            'details.*.remise_ariary' => 'required|numeric|min:0',
            'details.*.tva_detail' => 'required|numeric|min:0',
            'details.*.montant_tva' => 'required|numeric|min:0',
            'details.*.valeur_remise' => 'required|numeric|min:0',
            'details.*.valeur_ht' => 'required|numeric|min:0',
            'details.*.type_article' => 'nullable|string',
            'details.*.numeros_serie' => 'required_if:details.*.type_article,pneu|array|min:1',
            'details.*.numeros_serie.*.numero_serie' => [
                'required_if:details.*.type_article,pneu',
                'string',
                'min:1',
                'distinct',
                // Vérifier si le numéro de série existe déjà
                function ($attribute, $value, $fail) {
                    $exists = DB::table('pneu_series')->where('numero_serie', $value)->exists();
                    if ($exists) { $fail("Le numéro de série '$value' existe déjà.");}
                },
            ],
            'details.*.numeros_serie.*.etat' => 'nullable|string|min:1',
        ];
    }
}
