<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Rapport de Rentabilité des Actifs</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 10px; color: #333; margin: 0; padding: 20px; }
        .header { margin-bottom: 20px; border-bottom: 2px solid #112e79; padding-bottom: 10px; }
        .title { text-align: center; font-size: 16px; font-weight: bold; margin-bottom: 5px; color: #112e79; text-transform: uppercase; }
        .subtitle { text-align: center; font-size: 12px; margin-bottom: 20px; font-style: italic; }
        .kpis { width: 100%; margin-bottom: 25px; }
        .kpi-card { width: 22%; float: left; border: 1px solid #ddd; padding: 10px; border-radius: 5px; background-color: #fafafa; text-align: center; margin-right: 2%; }
        .kpi-card:last-child { margin-right: 0; }
        .kpi-title { font-size: 8px; font-weight: bold; color: #888; text-transform: uppercase; }
        .kpi-value { font-size: 14px; font-weight: bold; margin-top: 5px; color: #112e79; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th { background-color: #112e79; color: #ffffff; border: 1px solid #ddd; padding: 8px; text-align: left; font-weight: bold; text-transform: uppercase; font-size: 8px; }
        td { border: 1px solid #ddd; padding: 8px; vertical-align: middle; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        .total-row { background-color: #eee; font-weight: bold; }
        .marge-positive { color: green; font-weight: bold; }
        .marge-negative { color: red; font-weight: bold; }
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

    <div class="title">Rapport de Rentabilité des Actifs</div>
    <div class="subtitle">Période : {{ $periode }}</div>

    <div class="kpis">
        <div class="kpi-card">
            <div class="kpi-title">Chiffre d'Affaires</div>
            <div class="kpi-value">{{ number_format($summary['total_revenus'], 0, '.', ' ') }} Ar</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-title">Marge Nette</div>
            <div class="kpi-value" style="color: {{ $summary['total_marge'] >= 0 ? 'green' : 'red' }}">{{ number_format($summary['total_marge'], 0, '.', ' ') }} Ar</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-title">Charges Totales</div>
            <div class="kpi-value">{{ number_format($summary['total_charges'], 0, '.', ' ') }} Ar</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-title">ROI Moyen</div>
            <div class="kpi-value">{{ number_format($summary['average_roi'], 2, '.', ' ') }} %</div>
        </div>
        <div style="clear: both;"></div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Actif</th>
                <th>Type</th>
                <th class="text-right">Investissement</th>
                <th class="text-right">Revenus</th>
                <th class="text-right">Maintenance</th>
                <th class="text-right">Charges Route</th>
                <th class="text-right">Coût Social</th>
                <th class="text-right">Marge Nette</th>
                <th class="text-center">ROI</th>
            </tr>
        </thead>
        <tbody>
            @foreach($assets as $item)
            <tr>
                <td class="font-bold">{{ $item['label'] }}</td>
                <td class="text-center">{{ ucfirst($item['type']) }}</td>
                <td class="text-right">{{ number_format($item['valeur_initial'], 0, '.', ' ') }} Ar</td>
                <td class="text-right">
                    {{ number_format($item['revenus'], 0, '.', ' ') }} Ar
                    <div style="font-size: 8px; color: #666;">({{ $item['nb_voyages'] }} voy.)</div>
                </td>
                <td class="text-right">{{ number_format($item['depense_maintenance'], 0, '.', ' ') }} Ar</td>
                <td class="text-right">{{ number_format($item['charges_route'], 0, '.', ' ') }} Ar</td>
                <td class="text-right">{{ number_format($item['cout_social'], 0, '.', ' ') }} Ar</td>
                <td class="text-right {{ $item['marge_nette'] >= 0 ? 'marge-positive' : 'marge-negative' }}">
                    {{ number_format($item['marge_nette'], 0, '.', ' ') }} Ar
                </td>
                <td class="text-center font-bold">{{ number_format($item['roi'], 2, '.', ' ') }}%</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="2" class="text-right">TOTAL GÉNÉRAL</td>
                <td class="text-right">{{ number_format($summary['total_investissement'], 0, '.', ' ') }} Ar</td>
                <td class="text-right">{{ number_format($summary['total_revenus'], 0, '.', ' ') }} Ar</td>
                <td class="text-right">{{ number_format($assets->sum('depense_maintenance'), 0, '.', ' ') }} Ar</td>
                <td class="text-right">{{ number_format($assets->sum('charges_route'), 0, '.', ' ') }} Ar</td>
                <td class="text-right">{{ number_format($assets->sum('cout_social'), 0, '.', ' ') }} Ar</td>
                <td class="text-right {{ $summary['total_marge'] >= 0 ? 'marge-positive' : 'marge-negative' }}">
                    {{ number_format($summary['total_marge'], 0, '.', ' ') }} Ar
                </td>
                <td class="text-center" style="color: #112e79;">{{ number_format($summary['average_roi'], 2, '.', ' ') }}%</td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
