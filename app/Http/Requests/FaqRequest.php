<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FaqRequest extends FormRequest
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
            'question' => 'required|string|max:255',
            'reponse' => 'required|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'question.required' => 'La question est obligatoire.',
            'question.string' => 'La question doit être une chaîne de caractères.',
            'question.max' => 'La question ne doit pas dépasser 255 caractères.',

            'reponse.required' => 'La réponse est obligatoire.',
            'reponse.string' => 'La réponse doit être une chaîne de caractères.',
            'reponse.max' => 'La réponse ne doit pas dépasser 255 caractères.',
        ];
    }
}
