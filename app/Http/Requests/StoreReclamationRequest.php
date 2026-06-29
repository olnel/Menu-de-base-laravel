<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReclamationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'objet'      => 'required|string|max:255',
            'categorie'  => 'required|string|in:retard,casse,perte,mauvaise_manipulation,autre',
            'description'=> 'required|string|min:10',
            'voyage_id'  => 'nullable|integer|exists:voyages,id',
            'images'     => 'nullable|array|max:10',
            'images.*'   => 'image|mimes:jpeg,jpg,png,webp|max:5120',
        ];
    }

    public function messages(): array
    {
        return [
            'objet.required'       => 'L\'objet de la réclamation est obligatoire.',
            'categorie.required'   => 'Veuillez sélectionner une catégorie.',
            'categorie.in'         => 'La catégorie sélectionnée est invalide.',
            'description.required' => 'La description est obligatoire.',
            'description.min'      => 'La description doit comporter au moins 10 caractères.',
            'voyage_id.exists'     => 'Le voyage sélectionné est invalide.',
            'images.max'           => 'Vous ne pouvez pas joindre plus de 10 images.',
            'images.*.image'       => 'Chaque fichier joint doit être une image.',
            'images.*.mimes'       => 'Les formats acceptés sont : JPEG, JPG, PNG, WEBP.',
            'images.*.max'         => 'Chaque image ne doit pas dépasser 5 Mo.',
        ];
    }
}
