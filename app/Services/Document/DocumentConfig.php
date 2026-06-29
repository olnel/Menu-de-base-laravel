<?php

namespace App\Services\Document;

use App\Mail\BonCommandeFournisseurMail;
use App\Mail\DevisMail;
use App\Mail\FactureclientMail;
use App\Mail\InvoiceReminderMail;
use App\Models\FactureClient;
use App\Models\InfoSociete;

use App\Models\Paie;
use App\Utils\NumberToWordsHelper;

class DocumentConfig
{
    public static function forPaie(Paie $paie): array
    {
        $somme_lettre = NumberToWordsHelper::convert($paie->salaire_net);

        return [
            'pdf' => [
                'template' => 'pdf.bulletin_paie',
                'data' => [
                    'paie' => $paie,
                    'entreprise' => InfoSociete::first(),
                    'somme_lettre' => ucfirst($somme_lettre)
                ],
                'folder' => 'paies',
                'filename_from_field' => 'Bulletin_' . $paie->salarie->nom . '_' . $paie->mois . '_' . $paie->annee,
                'filename' => 'Bulletin_' . $paie->id,
                'paper_size' => 'A4',
                'orientation' => 'portrait',
                'force_regenerate' => true
            ]
        ];
    }

    public static function forInvoiceReminder(FactureClient $factureclient): array
    {
        return [
            'pdf' => [
                'template' => 'pdf.factureclient',
                'data' => [
                    'factureclient' => $factureclient,
                    'date' => now()->format('d/m/Y'),
                    'entreprise' =>  InfoSociete::first()
                ],
                'folder' => 'factureclient',
                'filename_from_field' => $factureclient->numero_facture. '_' . now()->format('YmdHis'),
                'filename' => $factureclient->numero_facture,
                'paper_size' => 'A4',
                'orientation' => 'portrait',
                'force_regenerate' => true // Toujours régénérer pour avoir les infos à jour
            ],
            'email' => [
                'mail_class' => InvoiceReminderMail::class,
                'recipients' => $factureclient->client->mail_client,
                'additional_data' => $factureclient
            ]
        ];
    }

    public static function forBonCommande($bonCommande): array
    {
        return [
            'pdf' => [
                'template' => 'pdf.boncommande',
                'data' => [
                    'bonCommande' => $bonCommande,
                    'date' => now()->format('d/m/Y'),
                    'entreprise' => InfoSociete::first()
                ],
                'folder' => 'boncommandes',
                'filename_from_field' => $bonCommande->numero_bon_commande. '_' . now()->format('YmdHis'),
                'filename' => $bonCommande->numero_bon_commande,
                'paper_size' => 'A4',
                'orientation' => 'portrait',
                'force_regenerate' => false
            ],
            'email' => [
                'mail_class' => BonCommandeFournisseurMail::class,
                'recipients' => $bonCommande->fournisseur->mail_fournisseur,
                'additional_data' => $bonCommande
            ]
        ];
    }

    public static function forDevis($devis): array
    {
        return [
            'pdf' => [
                'template' => 'pdf.devis',
                'data' => [
                    'devis' => $devis,
                    'date' => now()->format('d/m/Y'),
                    'entreprise' =>  InfoSociete::first()
                ],
                'folder' => 'devis',
                'filename_from_field' => $devis->numero_devis. '_' . now()->format('YmdHis'),
                'filename' => $devis->numero_devis,
                'paper_size' => 'A4',
                'orientation' => 'portrait',
                'force_regenerate' => false
            ],
            'email' => [
                'mail_class' => DevisMail::class,
                'recipients' => $devis->client->mail_client,
                'additional_data' => $devis
            ]
        ];
    }



    public static function forFactureClient(FactureClient $factureclient): array
    {
        return [
            'pdf' => [
                'template' => 'pdf.factureclient',
                'data' => [
                    'factureclient' => $factureclient,
                    'date' => now()->format('d/m/Y'),
                    'entreprise' =>  InfoSociete::first()
                ],
                'folder' => 'factureclient',
                'filename_from_field' => $factureclient->numero_facture. '_' . now()->format('YmdHis'),
                'filename' => $factureclient->numero_facture,
                'paper_size' => 'A4',
                'orientation' => 'portrait',
                'force_regenerate' => false
            ],
            'email' => [
                'mail_class' => FactureclientMail::class,
                'recipients' => $factureclient->client->mail_client,
                'additional_data' => $factureclient
            ]
        ];
    }
}
