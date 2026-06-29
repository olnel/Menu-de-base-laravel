<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateReclamationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'statut'  => 'required|string|in:en_attente,en_cours,resolue,rejetee',
            'reponse' => 'nullable|string|max:2000',
        ];
    }

    public function messages(): array
    {
        return [
            'statut.required' => 'Le statut est obligatoire.',
            'statut.in'       => 'Statut invalide.',
            'reponse.max'     => 'La réponse ne doit pas dépasser 2000 caractères.',
        ];
    }
}
