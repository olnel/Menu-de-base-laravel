<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreFournisseurRequest extends FormRequest
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
            'nom_fournisseur' => [
                'required',
                'string',
                Rule::unique('fournisseurs')->whereNull('deleted_at'),
            ],
            'adresse_fournisseur' => 'nullable|string',
            'mail_fournisseur' => [
                'nullable',
                'string',
                Rule::unique('fournisseurs')->whereNull('deleted_at'),
            ],
            'tel_fournisseur' => [
                'nullable',
                'string',
                Rule::unique('fournisseurs')->whereNull('deleted_at'),
            ],
            'nif_fournisseur' => [
                'nullable',
                'string',
                Rule::unique('fournisseurs')->whereNull('deleted_at'),
            ],
            'stat_fournisseur' => [
                'nullable',
                'string',
                Rule::unique('fournisseurs')->whereNull('deleted_at'),
            ],
            'rcs_fournisseur' => [
                'nullable',
                'string',
                Rule::unique('fournisseurs')->whereNull('deleted_at'),
            ],
        ];
    }
}
