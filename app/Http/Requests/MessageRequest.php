<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MessageRequest extends FormRequest
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
            'nom' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'pays' => 'required|string|max:255',
            'telephone' => 'required|string|max:20',
            'objet' => 'nullable|string|max:255',
            'message' => 'required|string',
        ];
    }

    public function messages(): array
    {
        return [
            'nom.required' => 'Le nom est obligatoire.',
            'nom.string' => 'Le nom doit être une chaîne de caractères.',
            'nom.max' => 'Le nom ne doit pas dépasser 255 caractères.',

            'email.required' => 'L\'adresse e-mail est obligatoire.',
            'email.email' => 'L\'adresse e-mail doit être valide.',
            'email.max' => 'L\'adresse e-mail ne doit pas dépasser 255 caractères.',

            'pays.required' => 'Le pays est obligatoire.',
            'pays.string' => 'Le pays doit être une chaîne de caractères.',
            'pays.max' => 'Le pays ne doit pas dépasser 255 caractères.',

            'telephone.required' => 'Le numéro de téléphone est obligatoire.',
            'telephone.string' => 'Le numéro de téléphone doit être une chaîne de caractères.',
            'telephone.max' => 'Le numéro de téléphone ne doit pas dépasser 20 caractères.',

            'objet.string' => 'L\'objet doit être une chaîne de caractères.',
            'objet.max' => 'L\'objet ne doit pas dépasser 255 caractères.',

            'message.required' => 'Le message est obligatoire.',
            'message.string' => 'Le message doit être une chaîne de caractères.',
        ];
    }
}
