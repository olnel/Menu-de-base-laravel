<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PortailEvaluationStoreRequest extends FormRequest
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
            'voyage_id'   => 'required|integer|exists:voyages,id',
            'note'        => 'required|integer|between:1,5',
            'commentaire' => 'nullable|string|max:1000',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'voyage_id.required' => 'Veuillez sélectionner un voyage.',
            'voyage_id.exists'   => 'Le voyage sélectionné est invalide.',
            'note.required'      => 'Veuillez attribuer une note.',
            'note.between'       => 'La note doit être comprise entre 1 et 5 étoiles.',
            'commentaire.max'    => 'Le commentaire ne peut pas dépasser 1000 caractères.',
        ];
    }
}
