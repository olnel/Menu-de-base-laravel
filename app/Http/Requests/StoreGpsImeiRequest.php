<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validation pour l'enregistrement de plusieurs IMEIs GPS en une seule soumission.
 */
class StoreGpsImeiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'imeis'               => ['required', 'array', 'min:1'],
            'imeis.*.imei'        => [
                'required',
                'digits:15',
                'distinct',
                Rule::unique('gps_imei_registry', 'imei')->whereNull('deleted_at'),
            ],
            'imeis.*.description' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'imeis.required'        => 'La liste des IMEIs est obligatoire.',
            'imeis.min'             => 'Veuillez saisir au moins un IMEI.',
            'imeis.*.imei.required' => 'L\'IMEI est obligatoire.',
            'imeis.*.imei.digits'   => 'L\'IMEI doit contenir exactement 15 chiffres.',
            'imeis.*.imei.distinct' => 'Cet IMEI apparaît en doublon dans la liste.',
            'imeis.*.imei.unique'   => 'Cet IMEI est déjà enregistré.',
        ];
    }
}
