<?php

namespace App\Mail;

use App\Models\BoncommandeFournisseur;
use App\Models\factureClient;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
class FactureclientMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public FactureClient $factureClient,
        public string $pdfPath
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Facture client N°' . $this->factureClient->numero_facture,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.factureClient',
            with: [
                'factureclient' => $this->factureClient,
            ],
        );
    }

    public function attachments(): array
    {
        return [
            Attachment::fromPath($this->pdfPath)
                ->as('factureclient' . $this->factureClient->numero_facture . '.pdf')
                ->withMime('application/pdf'),
        ];
    }
}
