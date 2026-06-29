<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PortailReclamationIndexRequest extends FormRequest
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
            'search'     => 'nullable|string|max:255',
            'statut'     => 'nullable|string|in:retard,casse,perte,mauvaise_manipulation,autre',
            'start_date' => 'nullable|date',
            'end_date'   => 'nullable|date|after_or_equal:start_date',
            'page'       => 'nullable|integer|min:1',
        ];
    }
}
