<?php

$numberToWords = new \NumberToWords\NumberToWords();
$numberTransformer = $numberToWords->getNumberTransformer('fr');
$logoPath = public_path($entreprise->logo_societe ?? null);
$logoBase64 = '';

if (file_exists($logoPath) && is_readable($logoPath)) {
    $type = pathinfo($logoPath, PATHINFO_EXTENSION);
    $data = file_get_contents($logoPath);
    $logoBase64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Devis N°{{ $devis->numero_devis }}</title>
    <style>
        /* General Styles */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #333;
            background: white;
            line-height: 1.6;
            padding: 25px;
        }

        .container {
            width: 100%;
            margin: 0 auto;
            background: white !important;
        }

        /* Header */
        .header {
            width: 100%;
            margin-bottom: 40px;
            border-bottom: 2px solid #172a6c;
            padding-bottom: 20px;
            display: table;
            table-layout: fixed;
        }

        .header-left, .header-right {
            display: table-cell;
            vertical-align: top;
            width: 50%;
        }

        .header-left {
            text-align: left;
        }

        .header-right {
            text-align: right;
        }

        .company-logo img {
            max-width: 250px;
            height: auto;
            margin-bottom: 10px;
        }

        .company-name {
            font-size: 28px;
            font-weight: bold;
            color: #172a6c;
        }

        .company-details div, .company-details p {
            font-size: 14px;
            color: #666;
            padding: 0!important;
            margin: 0!important;
        }

        .document-title {
            font-size: 32px;
            font-weight: bold;
            color: #172a6c;
            margin-bottom: 5px;
            text-transform: uppercase;
        }

        .document-info {
            font-size: 16px;
            color: #555;
            line-height: 1.5;
        }

        .document-info strong {
            color: #333;
        }

        /* Client Section */
        .client-section {
            margin-bottom: 15px;
        }

        .client-info-box {
            background: #f0f8ff;
            padding: 25px;
            border-left: 5px solid #172a6c;
            border-radius: 4px;
        }

        .client-info-box h3 {
            font-size: 20px;
            font-weight: bold;
            color: #1a202c;
            margin-bottom: 10px;
        }

        .client-info-box p {
            font-size: 14px;
            color: #555;
            margin: 0;
            line-height: 1.5;
        }

        /* Items Table */
        .section-title {
            font-size: 22px;
            font-weight: 700;
            color: #1a202c;
            margin-bottom: 15px;
            border-bottom: 2px solid #e0e0e0;
            padding-bottom: 8px;
            text-transform: uppercase;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
            background: #ffffff;
            border: 1px solid #e0e0e0;
        }

        .items-table thead th {
            background: #9b9999;
            color: white;
            padding: 15px;
            text-align: left;
            font-size: 15px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .items-table tbody tr {
            border-bottom: 1px solid #e0e0e0;
        }

        .items-table tbody tr:nth-child(even) {
            background: #f9f9f9;
        }

        .items-table td {
            padding: 5px;
            font-size: 15px;
            vertical-align: top;
            font-family: 'Arial', sans-serif !important;
        }
        .items-table tfoot tr th  {
            padding: 5px;
            font-size: 15px;
            font-family: 'Arial', sans-serif !important;
        }

        .item-description {
            font-weight: 500;
            color: #333;
        }

        .text-right {
            text-align: right !important;
        }

        .text-center {
            text-align: center !important;
        }

        /* Totals */
        .totals-section {
            width: 100%;
            display: table;
            table-layout: fixed;
        }

        .totals-spacer {
            display: table-cell;
            width: 60%;
        }

        .totals-box-cell {
            display: table-cell;
            width: 40%;
            text-align: right;
        }

        .totals-box {
            width: 100%;
            border: 2px solid #2563eb;
            background: #f0f8ff;
            padding: 20px;
            border-radius: 4px;
        }

        .total-row {
            display: table;
            width: 100%;
            margin-bottom: 10px;
        }

        .total-label, .total-value {
            display: table-cell;
            width: 50%;
            font-size: 16px;
            font-weight: 600;
            color: #555;
            vertical-align: middle;
        }

        .total-value {
            text-align: right;
            color: #172a6c;
        }

        .total-row.final {
            border-top: 2px solid #2563eb;
            padding-top: 15px;
            margin-top: 15px;
        }

        .total-row.final .total-label {
            font-size: 20px;
            font-weight: bold;
            color: #1a202c;
        }

        .total-row.final .total-value {
            font-size: 20px;
            font-weight: bold;
            color: #172a6c;
        }

        /* Conditions */
        .conditions-section {
            margin-top: 40px;
        }

        .conditions-container {
            width: 100%;
            display: table;
            table-layout: fixed;
            border-spacing: 20px 0;
        }

        .condition-box {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            background: #f0fdf4;
            padding: 25px;
            border-left: 5px solid #172a6c;
            border-radius: 4px;
        }

        .condition-title {
            font-size: 16px;
            font-weight: bold;
            color: #172a6c;
            margin-bottom: 10px;
        }

        .condition-content {
            font-size: 14px;
            color: #555;
            line-height: 1.6;
        }

        /* Footer */
        .footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 1px solid #e0e0e0;
            text-align: center;
        }

        .footer-note {
            font-size: 12px;
            color: #888;
            margin-top: 10px;
        }
    </style>
</head>
<body>

<div class="container">

    <div class="header">
        <div class="header-left">
            <div class="company-logo">
                @if(!empty($logoBase64))
                    <img src="{{ $logoBase64 }}" class="company-logo" alt="Logo">
                @endif
            </div>
            <div class="company-name">{{ $entreprise->nom_societe ?? 'socieete xxx' }}</div>
            <div class="company-details">
                @if(!empty($entreprise->adresse_societe))<div>{{ $entreprise->adresse_societe }}</div>@endif
                @if(!empty($entreprise->nif_societe))<p>NIF : {{ $entreprise->nif_societe }}</p>@endif
                @if(!empty($entreprise->stat_societe))<p>STAT : {{ $entreprise->stat_societe }}</p>@endif
                @if(!empty($entreprise->telephone_societe))<p>Tél : {{ $entreprise->telephone_societe }}</p>@endif
                @if(!empty($entreprise->email_societe))<p>{{ $entreprise->email_societe }}</p>@endif
                @if(!empty($entreprise->site_web))<p>{{ $entreprise->site_web }}</p>@endif
            </div>
        </div>
        <div class="header-right">
            <div class="document-title">DEVIS</div>
            <div class="document-info">
                <div><strong>N°:</strong> {{ $devis->numero_devis }}</div>
                <div><strong>Date:</strong> {{ \Carbon\Carbon::parse($devis->date_devis)->format('d/m/Y') }}</div>
                <div><strong>Validité:</strong> {{ \Carbon\Carbon::parse($devis->validite_devis)->format('d/m/Y') }}</div>
            </div>
        </div>
    </div>

    <div class="client-section">
        <div class="client-info-box">
            <h3>Informations Client</h3>
            <p><strong>Nom:</strong> {{ $devis->client->nom_client }}</p>
            @if(isset($devis->client->adresse_client))<p><strong>Adresse:</strong> {{ $devis->client->adresse_client }}</p>@endif
            @if(isset($devis->client->tel_client))<p><strong>Tél:</strong> {{ $devis->client->tel_client }}</p>@endif
            @if(isset($devis->client->mail_client))<p><strong>Email:</strong> {{ $devis->client->mail_client }}</p>@endif
            @if(isset($devis->client->nif_client))<p><strong>NIF:</strong> {{ $devis->client->nif_client }}</p>@endif
        </div>
    </div>

    <div class="items-section">
        <div class="section-title">Détails</div>
        <table class="items-table">
            <thead>
            <tr>
                <th style="width:50%;">Libellé</th>
                <th style="width:15%;" class="text-center">Qté</th>
                <th style="width:15%;" class="text-right">Prix Unitaire</th>
                <th style="width:20%;" class="text-right">Total</th>
            </tr>
            </thead>
            <tbody>
            @php
                $grandTotal = 0;
            @endphp
            @foreach($devis->details ?? [] as $detail)
                <tr>
                    <td class="item-description">{{ $detail->libelle }}</td>
                    <td class="text-center">{{ $detail->quantite }}</td>
                    <td class="text-right">{{ number_format($detail->prix_unitaire, 2, ',', ' ') }} </td>
                    <td class="text-right">{{ number_format($detail->quantite * $detail->prix_unitaire, 2, ',', ' ') }} </td>
                </tr>
                @php
                    $grandTotal += ($detail->quantite * $detail->prix_unitaire);
                @endphp
            @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="3" class="text-right">Total Ht</th>
                    <th colspan="3" class="text-right">{{ number_format($grandTotal, 2, ',', ' ') }}</th>
                </tr>
                <tr>
                    <th colspan="3" class="text-right"> TVA (20%)</th>
                    <th class="text-right">{{ number_format(($grandTotal * 0.2), 2, ',', ' ') }}</th>
                </tr>
                <tr>
                    <th colspan="3" class="text-right">Total TTC</th>
                    <th class="text-right">{{ number_format(($grandTotal+ ($grandTotal * 0.2)), 2, ',', ' ') }}</th>
                </tr>
            </tfoot>
        </table>
    </div>

<!--    <div class="totals-section">
        <div class="totals-spacer"></div>
        <div class="totals-box-cell">
            <div class="totals-box">
                <div class="total-row final">
                    <span class="total-label">TOTAL TTC :</span>
                    <span class="total-value">{{ number_format($grandTotal, 2, ',', ' ') }} </span>
                </div>
            </div>
        </div>
    </div>-->

    <div class="conditions-section">

        <div class="conditions-container">
            <div class="condition-box">
                <div class="condition-title">Délais de livraison</div>
                <div class="condition-content">
                    {{$devis->condition_delais}}
                </div>
            </div>
            <div class="condition-box">
                <div class="condition-title">Conditions de paiement</div>
                <div class="condition-content">
                    {{$devis->condition_paiement}}
                </div>
            </div>
        </div>
    </div>

</div>

</body>
</html>
