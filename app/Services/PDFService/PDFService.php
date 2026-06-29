<?php

namespace App\Services\PDFService;

use Barryvdh\DomPDF\Facade\Pdf;

class PDFService
{
    private $orientation = 'portrait';
    private $paperSize = 'A4';
    private $filename = 'document.pdf';
    private $options = [];

    public function __construct()
    {
        $this->options = [
            'isHtml5ParserEnabled' => true,
            'isPhpEnabled' => true,
            'defaultFont' => 'Arial',
            'dpi' => 150,
            'defaultPaperSize' => 'A4',
            'isRemoteEnabled' => true
        ];
    }

    /**
     * Définir la taille du papier
     * Accepte une chaîne (ex: 'A4') ou un array (ex: [0, 0, 226, 842] pour 80x297mm en points)
     */
    public function setPaperSize(string|array $size): self
    {
        $this->paperSize = $size;
        return $this;
    }

    /**
     * Définir l'orientation
     */
    public function setOrientation(string $orientation): self
    {
        $this->orientation = $orientation;
        return $this;
    }

    private function sanitizeFilename(string $filename): string
    {
        // Remplacer les / et \ par des caractères similaires autorisés
        return str_replace(['/', '\\'], ['-', '-'], $filename);
    }

    /**
     * Définir le nom du fichier
     */
    public function setFilename(string $filename): self
    {
        $this->filename = $this->sanitizeFilename($filename);
        return $this;
    }

    /**
     * Définir des options supplémentaires
     */
    public function setOptions(array $options): self
    {
        $this->options = array_merge($this->options, $options);
        return $this;
    }

    /**
     * Générer le PDF à partir d'une vue
     */
    public function generateFromView(string $view, array $data = []): \Barryvdh\DomPDF\PDF
    {
        $pdf = Pdf::loadView($view, $data);

        $pdf->setPaper($this->paperSize, $this->orientation);

        foreach ($this->options as $key => $value) {
            $pdf->setOption($key, $value);
        }

        return $pdf;
    }

    /**
     * Générer le PDF à partir de HTML
     */
    public function generateFromHtml(string $html): \Barryvdh\DomPDF\PDF
    {
        $pdf = Pdf::loadHTML($html);

        $pdf->setPaper($this->paperSize, $this->orientation);

        foreach ($this->options as $key => $value) {
            $pdf->setOption($key, $value);
        }

        return $pdf;
    }

    /**
     * Télécharger le PDF
     */
    public function download(string $view, array $data = [])
    {
        $pdf = $this->generateFromView($view, $data);
        return $pdf->download($this->filename);
    }

    /**
     * Afficher le PDF dans le navigateur
     */
    public function stream(string $view, array $data = [])
    {
        $pdf = $this->generateFromView($view, $data);
        return $pdf->stream($this->filename);
    }

    /**
     * Sauvegarder le PDF sur le serveur
     */
    public function save(string $view, array $data = [], string $path = null)
    {
        $pdf = $this->generateFromView($view, $data);
        $savePath = $path ?? storage_path('app/pdfs/' . $this->filename);

        // Créer le dossier si il n'existe pas
        $directory = dirname($savePath);
        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }

        return $pdf->save($savePath);
    }

    /**
     * Obtenir le contenu du PDF en base64
     */
    public function getBase64(string $view, array $data = []): string
    {
        $pdf = $this->generateFromView($view, $data);
        return base64_encode($pdf->output());
    }
}
