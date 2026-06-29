<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMagasnRequest extends FormRequest
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
        $magasin = $this->route('magasin');
        return [
            'nom_magasin' => [
                'required',
                'string',
                Rule::unique('magasins')
                    ->ignore($magasin->id)
                    ->whereNull('deleted_at'),
            ],
        ];
    }
}
