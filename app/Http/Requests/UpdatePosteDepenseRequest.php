<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePosteDepenseRequest extends FormRequest
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
        $postedepense = $this->route('postedepense');
        return [
            'libelle' => [
                'required',
                'string',
                Rule::unique('poste_depenses')
                    ->ignore($postedepense->id)
                    ->whereNull('deleted_at'),
            ],
        ];
    }
}
