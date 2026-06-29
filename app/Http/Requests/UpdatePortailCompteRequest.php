<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePortailCompteRequest extends FormRequest
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
        $clientId = session('portail_client_id_' . tenant('id'));

        return [
            'login'                  => 'required|string|max:255|unique:clients,login,' . $clientId,
            'nouveau_mot_de_passe'   => 'nullable|string|min:6|confirmed',
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
            'login.unique'                       => 'Ce login est déjà utilisé.',
            'nouveau_mot_de_passe.min'           => 'Le mot de passe doit contenir au moins 6 caractères.',
            'nouveau_mot_de_passe.confirmed'     => 'La confirmation du mot de passe ne correspond pas.',
        ];
    }
}
