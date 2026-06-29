<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreArticle extends FormRequest
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
            'nom_famille_article' => 'required|string|max:255',
            'reference' => 'nullable|string|max:255',
            'designation' => 'nullable|string|max:255',
            'type_article' => 'nullable|string|max:255',
            'marque' => 'nullable|string|max:255',
            'valeur' => 'nullable|numeric',
            'seuil_stock' => 'nullable|integer|min:0',
        ];
    }
}
