<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBoncommandeRequest extends FormRequest
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
            'id' => 'required|numeric|exists:boncommande_fournisseurs,id',
            'date_boncommande' => 'required|date',
            'nom_fournisseur' => 'nullable|string',
            Rule::unique('boncommande_fournisseurs')
                ->whereNull('deleted_at')
                ->ignore($this->boncommande?->id),
            'details' => 'required|array|min:1',
            'details.*.article_id' => 'required|numeric|exists:articles,id',
            'details.*.qte_commander' => 'required|numeric',
        ];
    }
}
