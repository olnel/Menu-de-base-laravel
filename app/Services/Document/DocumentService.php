<?php

namespace App\Services\Document;

use App\Services\PDFService\PDFService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
class DocumentService
{
    private PDFService $PDFService;
    public function __construct(PDFService $PDFService)
    {
        $this->PDFService = $PDFService;
    }

    /**
     * Génère un PDF de manière dynamique
     */
    public function generatePdf(array $config): string
    {
        try {
            $filename = $this->generateFilename($config);
            $storagePath = "{$config['folder']}/{$filename}";
            $fullPath = public_path($storagePath);
            $folderPath = public_path("{$config['folder']}");
            $this->deleteOldDocuments( $folderPath, $this->generateFilesNames($config));

            // Vérifier si le PDF existe déjà
            if (!file_exists($fullPath) || ($config['force_regenerate'] ?? false)) {
                $this->createDirectoryIfNotExists($fullPath);
                $this->generateAndSavePdf($config, $filename, $fullPath);
            }

            return $fullPath;
        } catch (\Exception $e) {
            Log::error('Erreur génération PDF: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'config' => $config
            ]);
            throw $e;
        }
    }

    /**
     * Génère un PDF et le retourne pour téléchargement/visualisation
     * @throws \Exception
     */
    public function generateAndDownloadPdf(array $config)
    {
        $fullPath = $this->generatePdf($config);
        return response()->file($fullPath);
    }

    /**
     * Envoie un email avec un PDF en pièce jointe
     */
    public function sendEmailWithPdf(array $config): array
    {
        try {
            // 1. Générer le PDF s'il n'existe pas
            $pdfPath = $this->generatePdf($config['pdf']);
            // 2. Envoyer l'email
            $this->sendEmail($config['email'], $pdfPath);
            return [
                'error' => false,
                'message' => 'Email envoyé avec succès',
            ];
        } catch (\Exception $e) {
            Log::error('Erreur envoi email avec PDF: ' . $e->getMessage(), [
                'config' => $config
            ]);
//            throw new HttpException(500, "Échec de l'envoi de l'email : " . $e->getMessage());
            return [
                'error' => true,
                'message' => 'Échec de l\'envoi : ' . $e->getMessage()
            ];
        }
    }

    /**
     * Génère le nom de fichier pour le PDF
     * @param array $config Configuration du document
     * @return string Nom de fichier généré
     */
    private function generateFilename(array $config): string
    {

        $baseFilename = $config['filename_from_field'] ?? 'document';
        if (isset($config['filename_from_field']) && isset($config['data'][$config['filename_from_field']])) {
            $baseFilename = Str::slug($config['data'][$config['filename_from_field']], '_');
        }
        // Remplacer les caractères non valides pour les noms de fichiers
        $baseFilename = str_replace('/', '_', $baseFilename);

        return "{$baseFilename}.pdf";
    }

    private function generateFilesNames(array $config): string
    {

        $baseFilename = $config['filename'] ?? 'document';
        if (isset($config['filename']) && isset($config['data'][$config['filename']])) {
            $baseFilename = Str::slug($config['data'][$config['filename']], '_');
        }
        // Remplacer les caractères non valides pour les noms de fichiers
        $baseFilename = str_replace('/', '_', $baseFilename);

        return "{$baseFilename}";
    }

    /**
     * Supprime les anciens documents PDF dans le dossier spécifié
     * @param string $folderPath Chemin du dossier où se trouvent les PDF
     * @param string $baseFilename Nom de base du fichier (sans extension)
     * @return void
     */
    private function deleteOldDocuments(string $folderPath, string $baseFilename): void
    {
        if (!is_dir($folderPath)) {
            return;
        }

        $files = scandir($folderPath);
        foreach ($files as $file) {
            if (stripos($file, $baseFilename . '_') === 0 && substr($file, -4) === '.pdf') {
                @unlink($folderPath . DIRECTORY_SEPARATOR . $file);
            }
        }
    }

    /**
     * Crée le répertoire si nécessaire
     */
    private function createDirectoryIfNotExists(string $fullPath): void
    {
        if (!file_exists(dirname($fullPath))) {
            mkdir(dirname($fullPath), 0755, true);
        }
    }

    /**
     * Génère et sauvegarde le PDF
     */
    private function generateAndSavePdf(array $config, string $filename, string $fullPath): void
    {
        $pdfOptions = array_merge([
            'margin-top' => '50',
            'margin-right' => '50',
            'margin-bottom' => '15',
            'margin-left' => '15',
            'padding-top' => '150',
            'defaultFont' => 'DejaVu Sans',
            'isRemoteEnabled' => true,
        ], $config['pdf_options'] ?? []);

        Log::info('config data', [
                'config' => $config['data']
        ]);
        $this->PDFService
            ->setFilename($filename)
            ->setPaperSize($config['paper_size'] ?? 'A4')
            ->setOrientation($config['orientation'] ?? 'portrait')
            ->setOptions($pdfOptions)
            ->save($config['template'], $config['data'], $fullPath);
    }

    /**
     * Envoie l'email avec la classe mail appropriée
     */
    private function sendEmail(array $emailConfig, string $pdfPath): void
    {
        $mailClass = $emailConfig['mail_class'];
        $recipients = $emailConfig['recipients'];
        $additionalData = $emailConfig['additional_data'] ?? [];

        Log::info('Envoi email', [
            'additional_data' => $additionalData,

        ]);
        // Créer une instance de la classe mail avec les données nécessaires
        $mailInstance = new $mailClass($additionalData, $pdfPath);

        // Envoyer à un ou plusieurs destinataires
        if (is_array($recipients)) {
            Mail::to($recipients)->send($mailInstance);
        } else {
            Mail::to($recipients)->send($mailInstance);
        }
    }

    public function generateHtml(array $config)
    {
        // Récupère le nom de la vue et les données à passer à cette vue
        $view = $config['template'];
        $data = $config['data'];

        // Retourne la vue rendue en tant que chaîne de caractères HTML
        return view($view, $data)->render();
    }
}
