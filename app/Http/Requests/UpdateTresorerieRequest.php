<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTresorerieRequest extends FormRequest
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
        $tresorerie = $this->route('tresorerie');
        return [
            'nom_tresorerie' => [
                'required',
                'string',
                Rule::unique('tresoreries')
                    ->ignore($tresorerie->id)
                    ->whereNull('deleted_at'),
            ],
            'type_tresorerie' => 'required',
            'numero_compte' => 'nullable|string|max:255',
            'bic' => 'nullable|string|max:255',
            'iban' => 'nullable|string|max:255',
            'banque_correspondante' => 'nullable|string|max:255',
            'code_swift' => 'nullable|string|max:255',
            'numero_telephone' => 'nullable|string',
            'titulaire_compte' => 'nullable|string',
        ];
    }
}
