<?php

namespace App\Console\Commands;

use App\Models\Document;
use App\Models\DocumentType;
use App\Models\DocumentableType;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MigrateOldDocuments extends Command
{
    protected $signature = 'documents:migrate-old-data';
    protected $description = 'Migre les documents des anciennes tables (vehicule_documents, remorque_documents, etc.) vers le nouveau système';

    public function handle()
    {
        $this->info('Début de la migration des anciens documents...');

        // 1. Migration Véhicules
        if (Schema::hasTable('vehicule_documents')) {
            $oldVehiculeDocs = DB::table('vehicule_documents')->whereNull('deleted_at')->get();
            foreach ($oldVehiculeDocs as $oldDoc) {
                $typeId = $this->getOrCreateTypeId($oldDoc->nom_document);
                $this->createDocument('App\Models\Vehicule', $oldDoc->vehicule_id, $typeId, $oldDoc->fichier_jointe, $oldDoc->date_expiration, $oldDoc->description);
            }
            $this->info(count($oldVehiculeDocs) . ' documents de véhicules migrés.');
        }

        // 2. Migration Remorques
        if (Schema::hasTable('remorque_documents')) {
            $oldRemorqueDocs = DB::table('remorque_documents')->whereNull('deleted_at')->get();
            foreach ($oldRemorqueDocs as $oldDoc) {
                $typeId = $this->getOrCreateTypeId($oldDoc->nom_document);
                $this->createDocument('App\Models\Remorque', $oldDoc->remorque_id, $typeId, $oldDoc->fichier_jointe, $oldDoc->date_expiration, $oldDoc->description);
            }
            $this->info(count($oldRemorqueDocs) . ' documents de remorques migrés.');
        }

        // 3. Migration Chauffeurs
        if (Schema::hasTable('documents_chauffeurs')) {
            $oldChauffeurDocs = DB::table('documents_chauffeurs')->whereNull('deleted_at')->get();
            foreach ($oldChauffeurDocs as $oldDoc) {
                $typeId = $this->getOrCreateTypeId($oldDoc->type);
                $this->createDocument('App\Models\Chauffeur', $oldDoc->chauffeur_id, $typeId, $oldDoc->fichier, $oldDoc->date_expiration, $oldDoc->description);
            }
            $this->info(count($oldChauffeurDocs) . ' documents de chauffeurs migrés.');
        }

        $this->info('Migration terminée !');
        return Command::SUCCESS;
    }

    private function getOrCreateTypeId($name)
    {
        if (empty($name)) $name = 'Inconnu';
        return DocumentType::firstOrCreate(['nom' => $name])->id;
    }

    private function createDocument($modelClass, $modelId, $typeId, $filesJson, $expiration, $obs)
    {
        if (empty($modelId)) return;

        $files = json_decode($filesJson, true);
        if (is_array($files)) {
            foreach ($files as $file) {
                Document::create([
                    'documentable_type' => $modelClass,
                    'documentable_id' => $modelId,
                    'document_type_id' => $typeId,
                    'fichier' => is_array($file) ? ($file['path'] ?? '') : $file,
                    'date_expiration' => $expiration,
                    'observation' => $obs
                ]);
            }
        } elseif (!empty($filesJson)) {
            Document::create([
                'documentable_type' => $modelClass,
                'documentable_id' => $modelId,
                'document_type_id' => $typeId,
                'fichier' => $filesJson,
                'date_expiration' => $expiration,
                'observation' => $obs
            ]);
        }
    }
}
