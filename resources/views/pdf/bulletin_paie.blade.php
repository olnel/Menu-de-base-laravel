<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Bulletin de Paie - {{ $paie->salarie->nom }} {{ $paie->salarie->prenom }}</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 12px; color: #333; }
        .header { margin-bottom: 20px; border-bottom: 2px solid #112e79; padding-bottom: 10px; }
        .company-info { float: left; width: 50%; }
        .employee-info { float: right; width: 40%; border: 1px solid #ddd; padding: 10px; background: #f9f9f9; }
        .clear { clear: both; }
        .title { text-align: center; font-size: 18px; font-weight: bold; margin: 20px 0; text-transform: uppercase; color: #112e79; }
        .period { text-align: center; margin-bottom: 20px; font-style: italic; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th { background-color: #f2f2f2; border: 1px solid #ddd; padding: 8px; text-align: left; }
        td { border: 1px solid #ddd; padding: 8px; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .footer { margin-top: 50px; }
        .signature-box { float: left; width: 45%; height: 100px; border: 1px solid #ddd; padding: 10px; text-align: center; }
        .total-row { background-color: #eee; font-weight: bold; }
        .net-a-payer { font-size: 16px; background-color: #112e79; color: white; padding: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <div class="company-info">
            <h2 style="margin: 0; color: #112e79;">{{ $entreprise->nom_societe ?? 'TRANSMADA' }}</h2>
            <p style="margin: 5px 0;">
                {{ $entreprise->adresse_societe ?? '' }}<br>
                Tél : {{ $entreprise->telephone_societe ?? '' }}<br>
                Email : {{ $entreprise->email_societe ?? '' }}<br>
                NIF/STAT : {{ $entreprise->nif_societe ?? '' }} / {{ $entreprise->stat_societe ?? '' }}
            </p>
        </div>
        <div class="employee-info">
            <p style="margin: 0;"><strong>MATRICULE :</strong> {{ $paie->salarie->matricule ?? 'N/A' }}</p>
            <p style="margin: 5px 0;"><strong>NOM :</strong> {{ $paie->salarie->nom ?? '' }} {{ $paie->salarie->prenom ?? '' }}</p>
            <p style="margin: 5px 0;"><strong>POSTE :</strong> {{ $paie->salarie->typeSalarie->libelle ?? 'Employé' }}</p>
            <p style="margin: 5px 0;"><strong>CIN :</strong> {{ $paie->salarie->cin ?? 'N/A' }}</p>
        </div>
        <div class="clear"></div>
    </div>

    <div class="title">Bulletin de Paie</div>
    <div class="period">Période : {{ str_pad($paie->mois, 2, '0', STR_PAD_LEFT) }} / {{ $paie->annee }}</div>

    <table>
        <thead>
            <tr>
                <th>Désignation</th>
                <th class="text-right">Montant (Ar)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Salaire de base</td>
                <td class="text-right">{{ number_format($paie->salaire_base, 2, ',', ' ') }}</td>
            </tr>
            @foreach($paie->elements as $element)
            <tr>
                <td>{{ $element->libelle }}</td>
                <td class="text-right">
                    @if($element->type === 'prime')
                        + {{ number_format($element->montant, 2, ',', ' ') }}
                    @else
                        - {{ number_format($element->montant, 2, ',', ' ') }}
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td class="text-right font-bold">TOTAL DES PRIMES</td>
                <td class="text-right text-green-600">+ {{ number_format($paie->montant_primes, 2, ',', ' ') }}</td>
            </tr>
            <tr class="total-row">
                <td class="text-right font-bold">TOTAL DES RETENUES</td>
                <td class="text-right text-red-600">- {{ number_format($paie->montant_retenues, 2, ',', ' ') }}</td>
            </tr>
            <tr>
                <td class="text-right font-bold net-a-payer">NET À PAYER (Ar)</td>
                <td class="text-right font-bold net-a-payer">{{ number_format($paie->salaire_net, 2, ',', ' ') }}</td>
            </tr>
        </tfoot>
    </table>

    <p>Arrêté le présent bulletin à la somme de : <strong>{{ $somme_lettre ?? '' }} Ariary</strong></p>

    <div class="footer" style="background-color: #f5f5f5; padding: 15px; border-radius: 5px;">
        <p style="margin: 0; font-size: 14px;"><strong>Détails du règlement :</strong></p>
        <table style="width: 100%; border: none; margin-top: 10px; margin-bottom: 0;">
            <tr>
                <td style="border: none; padding: 2px;">Mode de paiement : <strong>{{ $paie->mode_paiement ?? 'Non défini' }}</strong></td>
                <td style="border: none; padding: 2px; text-align: right;">Date : <strong>{{ $paie->date_paiement ? date('d/m/Y', strtotime($paie->date_paiement)) : '-' }}</strong></td>
            </tr>
            @if($paie->telephone_paiement)
            <tr>
                <td style="border: none; padding: 2px;" colspan="2">N° Téléphone : <strong>{{ $paie->telephone_paiement }}</strong></td>
            </tr>
            @endif
            @if($paie->reference_paiement)
            <tr>
                <td style="border: none; padding: 2px;" colspan="2">Référence : <strong>{{ $paie->reference_paiement }}</strong></td>
            </tr>
            @endif
        </table>
    </div>

    <div style="margin-top: 30px;">
        <div class="signature-box">
            Signature de l'employeur
        </div>
        <div class="signature-box" style="float: right;">
            Signature du salarié
        </div>
        <div class="clear"></div>
    </div>

    <div style="margin-top: 30px; font-size: 10px; color: #777; text-align: center; border-top: 1px solid #eee; padding-top: 10px;">
        Généré le {{ date('d/m/Y H:i') }} - Logiciel de Gestion TransMada
    </div>
</body>
</html>
