<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateNotificationSettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'days_before_expiry' => ['required', 'integer', 'min:1', 'max:15'],
            'enabled'            => ['required', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'days_before_expiry.required' => 'Le délai avant échéance est obligatoire.',
            'days_before_expiry.integer'  => 'Le délai doit être un nombre entier.',
            'days_before_expiry.min'      => 'Le délai doit être d\'au moins 1 jour.',
            'days_before_expiry.max'      => 'Le délai ne peut pas dépasser 15 jours.',
            'enabled.required'            => 'Le statut d\'activation est obligatoire.',
            'enabled.boolean'             => 'Le statut d\'activation doit être vrai ou faux.',
        ];
    }
}
