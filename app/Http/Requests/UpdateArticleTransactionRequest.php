<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateArticleTransactionRequest extends FormRequest
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
            'date_transaction' => 'required|date',
            'reference_mouvement' => 'nullable|string',
            'magasin_id' => 'required|string',
            'vehicule_id' => 'nullable',
            'type_mvt' => 'required|string',
            'motif' => 'nullable|string',
            'magasin_cible' => 'nullable',
            'details' => 'required|array|min:1',
            'details.*.article_id' => 'required|numeric|exists:articles,id',
            'details.*.qte_mouvement' => 'required|numeric',
        ];
    }
}
