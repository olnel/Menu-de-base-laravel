<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateClientRequest extends FormRequest
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
        $client = $this->route('client');
        return [
            'nom_client' => [
                'required',
                'string',
                Rule::unique('clients')
                    ->ignore($client->id)
                    ->whereNull('deleted_at'),
            ],
            'adresse_client' => 'nullable|string',
            'mail_client' => [
                'nullable',
                'string',
                Rule::unique('clients')->ignore($client->id)->whereNull('deleted_at'),
            ],
            'tel_client' => [
                'nullable',
                'string',
                Rule::unique('clients')->ignore($client->id)->whereNull('deleted_at'),
            ],
            'nif_client' => [
                'nullable',
                'string',
                Rule::unique('clients')->ignore($client->id)->whereNull('deleted_at'),
            ],
            'stat_client' => [
                'nullable',
                'string',
                Rule::unique('clients')->ignore($client->id)->whereNull('deleted_at'),
            ],
            'rcs_client' => [
                'nullable',
                'string',
                Rule::unique('clients')->ignore($client->id)->whereNull('deleted_at'),
            ],
            'login' => [
                'nullable',
                'string',
                Rule::unique('clients')->ignore($client->id)->whereNull('deleted_at'),
            ],
        ];
    }
}
