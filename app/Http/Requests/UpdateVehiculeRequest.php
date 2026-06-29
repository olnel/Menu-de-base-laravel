<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateVehiculeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $vehicule = $this->route('vehicule');

        return [
            'immatriculation'                      => 'required|string|unique:vehicules,immatriculation,' . $vehicule->id,
            'marque'                               => 'nullable|string',
            'modele'                               => 'nullable|string',
            'remorque_id'                          => 'nullable|numeric',
            'num_chassis'                          => 'nullable|string|unique:vehicules,num_chassis,' . $vehicule->id,
            'num_carte_grise'                      => 'nullable|string|unique:vehicules,num_carte_grise,' . $vehicule->id,
            'couleur'                              => 'nullable|string',
            'nbre_porte'                           => 'nullable|integer',
            'valeur_initial'                       => 'nullable|numeric',
            'param_element_id'                     => 'nullable',
            'element_vehicules'                    => 'sometimes|nullable|array',
            'element_vehicules.*.emplacement'      => 'sometimes|nullable|string',
            'element_vehicules.*.libelle'          => 'sometimes|nullable|string',
            'element_vehicules.*.reference'        => 'sometimes|nullable|string',
            'element_vehicules.*.numero_serie'     => 'sometimes|nullable|string|distinct',
            'element_vehicules.*.etat_piece'       => 'sometimes|nullable|string',
            'element_vehicules.*.is_pneu'          => 'sometimes|boolean',
            'element_vehicules.*.is_first'         => 'sometimes|nullable|boolean',
            'element_vehicules.*.date_montage'     => 'sometimes|nullable|date',
            'documents'                            => 'nullable|array',
            'documents.*.document_type_id'         => 'required|exists:document_types,id',
            'documents.*.fichier'                  => 'nullable|file|max:10240',
            'documents.*.date_expiration'          => 'nullable|date',
            'documents.*.observation'              => 'nullable|string',
        ];
    }
}
