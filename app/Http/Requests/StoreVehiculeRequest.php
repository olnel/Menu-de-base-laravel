<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreVehiculeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'immatriculation'                      => 'required|string|unique:vehicules,immatriculation',
            'marque'                               => 'nullable|string',
            'modele'                               => 'nullable|string',
            'remorque_id'                          => 'nullable|numeric',
            'num_chassis'                          => 'nullable|string|unique:vehicules,num_chassis',
            'num_carte_grise'                      => 'nullable|string|unique:vehicules,num_carte_grise',
            'couleur'                              => 'nullable|string',
            'nbre_porte'                           => 'nullable|integer',
            'valeur_initial'                       => 'nullable|numeric',
            'param_element_id'                     => 'nullable',
            'element_vehicules'                    => 'nullable|array',
            'element_vehicules.*.emplacement'      => 'nullable|string',
            'element_vehicules.*.libelle'          => 'nullable|string',
            'element_vehicules.*.reference'        => 'nullable|string',
            'element_vehicules.*.numero_serie'     => 'nullable|string|distinct',
            'element_vehicules.*.etat_piece'       => 'nullable|string',
            'element_vehicules.*.is_pneu'          => 'nullable|boolean',
            'element_vehicules.*.is_first'         => 'nullable|boolean',
            'element_vehicules.*.date_montage'     => 'nullable|date',
            'documents'                            => 'nullable|array',
            'documents.*.document_type_id'         => 'required|exists:document_types,id',
            'documents.*.fichier'                  => 'nullable|file|max:10240',
            'documents.*.date_expiration'          => 'nullable|date',
            'documents.*.observation'              => 'nullable|string',
        ];
    }
}
