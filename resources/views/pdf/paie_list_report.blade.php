<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Rapport des Paies - {{ $periode }}</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 10px; color: #333; margin: 0; padding: 20px; }
        .header { margin-bottom: 20px; border-bottom: 2px solid #112e79; padding-bottom: 10px; }
        .title { text-align: center; font-size: 16px; font-weight: bold; margin-bottom: 5px; color: #112e79; text-transform: uppercase; }
        .subtitle { text-align: center; font-size: 12px; margin-bottom: 20px; font-style: italic; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th { background-color: #f2f2f2; border: 1px solid #ddd; padding: 8px; text-align: left; font-weight: bold; text-transform: uppercase; font-size: 9px; }
        td { border: 1px solid #ddd; padding: 8px; vertical-align: middle; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        .footer { margin-top: 30px; text-align: right; font-size: 12px; }
        .total-row { background-color: #eee; font-weight: bold; }
        .status-badge { padding: 3px 6px; border-radius: 3px; font-size: 8px; font-weight: bold; text-transform: uppercase; }
    </style>
</head>
<body>
    <div class="header">
        <div style="float: left; width: 60%;">
            <h2 style="margin: 0; color: #112e79;">{{ $entreprise->nom_societe ?? 'TRANSMADA' }}</h2>
            <p style="margin: 5px 0; font-size: 9px;">
                {{ $entreprise->adresse_societe ?? '' }}<br>
                Tél : {{ $entreprise->telephone_societe ?? '' }}
            </p>
        </div>
        <div style="float: right; width: 35%; text-align: right;">
            <p style="margin: 0;">Date : {{ date('d/m/Y') }}</p>
            <p style="margin: 5px 0;">Heure : {{ date('H:i') }}</p>
        </div>
        <div style="clear: both;"></div>
    </div>

    <div class="title">Récapitulatif des Paies</div>
    <div class="subtitle">Période : {{ $periode }}</div>

    <table>
        <thead>
            <tr>
                <th width="80">Matricule</th>
                <th>Salarié</th>
                <th class="text-right">Salaire Base</th>
                <th class="text-right">Primes</th>
                <th class="text-right">Retenues</th>
                <th class="text-right">Net à Payer</th>
                <th class="text-center" width="70">Statut</th>
            </tr>
        </thead>
        <tbody>
            @php 
                $totalBase = 0; 
                $totalPrimes = 0; 
                $totalRetenues = 0; 
                $totalNet = 0; 
            @endphp
            @foreach($paies as $paie)
            @php 
                $totalBase += $paie->salaire_base; 
                $totalPrimes += $paie->montant_primes; 
                $totalRetenues += $paie->montant_retenues; 
                $totalNet += $paie->salaire_net; 
            @endphp
            <tr>
                <td class="font-bold">{{ $paie->salarie->matricule }}</td>
                <td>{{ $paie->salarie->nom }} {{ $paie->salarie->prenom }}</td>
                <td class="text-right">{{ number_format($paie->salaire_base, 0, '.', ' ') }} Ar</td>
                <td class="text-right text-green-600">+ {{ number_format($paie->montant_primes, 0, '.', ' ') }}</td>
                <td class="text-right text-red-600">- {{ number_format($paie->montant_retenues, 0, '.', ' ') }}</td>
                <td class="text-right font-bold">{{ number_format($paie->salaire_net, 0, '.', ' ') }} Ar</td>
                <td class="text-center">
                    {{ strtoupper($paie->statut) }}
                </td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="2" class="text-right">TOTAL GÉNÉRAL</td>
                <td class="text-right">{{ number_format($totalBase, 0, '.', ' ') }} Ar</td>
                <td class="text-right text-green-600">+ {{ number_format($totalPrimes, 0, '.', ' ') }}</td>
                <td class="text-right text-red-600">- {{ number_format($totalRetenues, 0, '.', ' ') }}</td>
                <td class="text-right" style="color: #112e79; font-size: 11px;">{{ number_format($totalNet, 0, '.', ' ') }} Ar</td>
                <td></td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        Arrêté le présent rapport à la somme de : <strong>{{ $total_lettre }} Ariary</strong>
    </div>

    <div style="margin-top: 50px;">
        <div style="float: left; width: 40%; text-align: center; border-top: 1px solid #333; padding-top: 10px;">
            Signature Direction
        </div>
        <div style="float: right; width: 40%; text-align: center; border-top: 1px solid #333; padding-top: 10px;">
            Le Comptable
        </div>
        <div style="clear: both;"></div>
    </div>
</body>
</html>
