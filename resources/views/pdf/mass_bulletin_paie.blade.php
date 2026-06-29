<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Bulletins de Paie Groupés</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 11px; color: #333; margin: 0; padding: 0; }
        .page-break { page-break-after: always; }
        .bulletin-container { padding: 10px; }
        .header { margin-bottom: 15px; border-bottom: 2px solid #112e79; padding-bottom: 8px; }
        .company-info { float: left; width: 50%; }
        .employee-info { float: right; width: 45%; border: 1px solid #ddd; padding: 8px; background: #f9f9f9; }
        .clear { clear: both; }
        .title { text-align: center; font-size: 16px; font-weight: bold; margin: 15px 0; text-transform: uppercase; color: #112e79; }
        .period { text-align: center; margin-bottom: 15px; font-style: italic; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        th { background-color: #f2f2f2; border: 1px solid #ddd; padding: 6px; text-align: left; }
        td { border: 1px solid #ddd; padding: 6px; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .footer-reglement { background-color: #f5f5f5; padding: 10px; border-radius: 5px; margin-top: 20px; }
        .signature-section { margin-top: 20px; }
        .signature-box { float: left; width: 45%; height: 80px; border: 1px solid #ddd; padding: 8px; text-align: center; }
        .total-row { background-color: #eee; font-weight: bold; }
        .net-a-payer { font-size: 14px; background-color: #112e79; color: white; padding: 8px; }
        .text-green-600 { color: #16a34a; }
        .text-red-600 { color: #dc2626; }
    </style>
</head>
<body>
    @foreach($paies as $index => $paie)
    <div class="bulletin-container {{ $index < count($paies) - 1 ? 'page-break' : '' }}">
        <div class="header">
            <div class="company-info">
                <h2 style="margin: 0; color: #112e79; font-size: 16px;">{{ $entreprise->nom_societe ?? 'TRANSMADA' }}</h2>
                <p style="margin: 3px 0; font-size: 10px;">
                    {{ $entreprise->adresse_societe ?? '' }}<br>
                    Tél : {{ $entreprise->telephone_societe ?? '' }}<br>
                    NIF/STAT : {{ $entreprise->nif_societe ?? '' }} / {{ $entreprise->stat_societe ?? '' }}
                </p>
            </div>
            <div class="employee-info">
                <p style="margin: 0;"><strong>MATRICULE :</strong> {{ $paie->salarie->matricule ?? 'N/A' }}</p>
                <p style="margin: 2px 0;"><strong>NOM :</strong> {{ $paie->salarie->nom ?? '' }} {{ $paie->salarie->prenom ?? '' }}</p>
                <p style="margin: 2px 0;"><strong>POSTE :</strong> {{ $paie->salarie->typeSalarie->libelle ?? 'Employé' }}</p>
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
                            <span class="text-green-600">+ {{ number_format($element->montant, 2, ',', ' ') }}</span>
                        @else
                            <span class="text-red-600">- {{ number_format($element->montant, 2, ',', ' ') }}</span>
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

        <div class="footer-reglement">
            <p style="margin: 0; font-size: 11px;"><strong>Détails du règlement :</strong></p>
            <table style="width: 100%; border: none; margin-top: 5px; margin-bottom: 0;">
                <tr>
                    <td style="border: none; padding: 2px;">Mode : <strong>{{ $paie->mode_paiement ?? 'Espèce' }}</strong></td>
                    <td style="border: none; padding: 2px; text-align: right;">Date : <strong>{{ $paie->date_paiement ? date('d/m/Y', strtotime($paie->date_paiement)) : date('d/m/Y') }}</strong></td>
                </tr>
            </table>
        </div>

        <div class="signature-section">
            <div class="signature-box">
                Employeur
            </div>
            <div class="signature-box" style="float: right;">
                Salarié
            </div>
            <div class="clear"></div>
        </div>
    </div>
    @endforeach
</body>
</html>
