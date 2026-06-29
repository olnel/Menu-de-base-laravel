<?php

namespace App\Mail;

use App\Models\BoncommandeFournisseur;
use App\Models\DevisClient;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
class DevisMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public DevisClient $devisClient,
        public string $pdfPath
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Devis N°' . $this->devisClient->numero_devis,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.devisClient',
            with: [
                'devisClient' => $this->devisClient,
            ],
        );
    }

    public function attachments(): array
    {
        return [
            Attachment::fromPath($this->pdfPath)
                ->as('devis_client' . $this->devisClient->numero_devis . '.pdf')
                ->withMime('application/pdf'),
        ];
    }
}
