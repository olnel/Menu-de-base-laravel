<?php

namespace Database\Seeders;

use App\Models\DocumentableType;
use App\Models\DocumentType;
use App\Models\DocumentModel;
use Illuminate\Database\Seeder;

class DocumentSystemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Types d'entités
        $personne = DocumentableType::updateOrCreate(['nom' => 'Personne'], ['model_class' => 'App\Models\Salarie']);
        $tracteur = DocumentableType::updateOrCreate(['nom' => 'Tracteur'], ['model_class' => 'App\Models\Vehicule']);
        $remorque = DocumentableType::updateOrCreate(['nom' => 'Remorque'], ['model_class' => 'App\Models\Remorque']);

        // 2. Types de documents
        $types = [
            'CIN', 'Permis', 'Certificat résidence', 'Photo', 
            'Carte grise', 'Assurance', 'Visite technique', 'Carte remorque'
        ];

        $docTypes = [];
        foreach ($types as $type) {
            $docTypes[$type] = DocumentType::updateOrCreate(['nom' => $type])->id;
        }

        // 3. Modèles documentaires (Lien entre entité et documents requis)
        
        // Personne
        $personneDocs = ['CIN', 'Permis', 'Certificat résidence', 'Photo'];
        foreach ($personneDocs as $index => $typeName) {
            DocumentModel::updateOrCreate([
                'documentable_type_id' => $personne->id,
                'document_type_id' => $docTypes[$typeName],
            ], [
                'obligatoire' => true,
                'expiration_required' => in_array($typeName, ['CIN', 'Permis']),
                'alert_days' => 30, // Par défaut 30 jours
                'ordre' => $index,
            ]);
        }

        // Tracteur
        $tracteurDocs = ['Carte grise', 'Assurance', 'Visite technique'];
        foreach ($tracteurDocs as $index => $typeName) {
            DocumentModel::updateOrCreate([
                'documentable_type_id' => $tracteur->id,
                'document_type_id' => $docTypes[$typeName],
            ], [
                'obligatoire' => true,
                'expiration_required' => true,
                'alert_days' => 15, // Exemple demandé
                'ordre' => $index,
            ]);
        }

        // Remorque
        $remorqueDocs = ['Carte remorque', 'Assurance', 'Visite technique'];
        foreach ($remorqueDocs as $index => $typeName) {
            DocumentModel::updateOrCreate([
                'documentable_type_id' => $remorque->id,
                'document_type_id' => $docTypes[$typeName],
            ], [
                'obligatoire' => true,
                'expiration_required' => true,
                'alert_days' => 15,
                'ordre' => $index,
            ]);
        }
    }
}
