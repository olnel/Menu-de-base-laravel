<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ImportRequest extends FormRequest
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
            'file' => 'required|file|mimes:xlsx,csv,xls',
            'columns' => 'required|array',
            'model' => 'required|string',
        ];
    }

    public function messages(): array
    {
        return [
            'file.required' => 'Le fichier est obligatoire.',
            'file.file' => 'Le fichier doit être un fichier valide.',
            'file.mimes' => 'Le fichier doit être au format xlsx, csv ou xls.',

            'columns.required' => 'Les colonnes sont obligatoires.',
            'columns.array' => 'Les colonnes doivent être un tableau.',

            'model.required' => 'Le modèle est obligatoire.',
            'model.string' => 'Le modèle doit être une chaîne de caractères.',
            'model.exists' => 'Le modèle spécifié n\'existe pas ou n\'est pas valide.',
        ];
    }
}
