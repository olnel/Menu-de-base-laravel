<?php

namespace App\Services;

use App\Repositories\BackupRepository;
use App\Services\Base\BaseService;
use Exception;
use Illuminate\Support\Facades\Storage;

class BackupService extends BaseService
{
    protected array $relation = ['user'];
    protected array $scope = [
        'filter' => 'search',
        'filterdatestart' => 'start_date',
        'filterdateend' => 'end_date',
    ];
    public function __construct(BackupRepository $repository)
    {
        parent::__construct($repository);
    }

    protected function initializeFilters(): void
    {
        $this->setFilterLabel('filename')->setFilterValue('id');
    }

    /**
     * Lance une nouvelle sauvegarde du backend (base de données).
     */
    public function runBackup(): array
    {
        $filename = 'backup-' . now()->format('Y-m-d-H-i-s') . '.sql';
        $path = 'backups/' . $filename;

        try {
            // Création du dossier si inexistant
            if (!Storage::disk('local')->exists('backups')) {
                Storage::disk('local')->makeDirectory('backups');
            }

            $dbConfig = config('database.connections.mysql');
            $database = $dbConfig['database'];
            $username = $dbConfig['username'];
            $password = $dbConfig['password'];
            $host = $dbConfig['host'];

            $fullPath = storage_path('app/' . $path);

            // Commande mysqldump (assure que mysqldump est dans le PATH)
            // Sur Windows avec Laragon, c'est généralement disponible.
            $command = sprintf(
                'mysqldump --user=%s --password=%s --host=%s %s > %s',
                escapeshellarg($username),
                escapeshellarg($password),
                escapeshellarg($host),
                escapeshellarg($database),
                escapeshellarg($fullPath)
            );

            $output = [];
            $returnVar = 0;
            exec($command, $output, $returnVar);

            if ($returnVar !== 0) {
                throw new Exception("Erreur lors de l'exécution de mysqldump (code de retour: $returnVar). Vérifiez que l'outil est installé et accessible.");
            }

            $size = Storage::disk('local')->size($path);

            $backup = $this->repository->create([
                'filename' => $filename,
                'path'     => $path,
                'size'     => $size,
                'type'     => 'database',
                'status'   => 'success',
                'user_id'  => auth()->id(),
            ]);

            return $this->successResponse('Sauvegarde effectuée avec succès', $backup);

        } catch (Exception $e) {
            return $this->errorResponse('Échec de la sauvegarde', $e);
        }
    }

    /**
     * Télécharge un fichier de sauvegarde.
     */
    public function download($backup)
    {
        if (!Storage::disk('local')->exists($backup->path)) {
            throw new Exception("Le fichier de sauvegarde n'existe plus sur le disque.");
        }

        return Storage::disk('local')->download($backup->path, $backup->filename);
    }
}
