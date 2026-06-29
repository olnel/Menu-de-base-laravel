<?php

$numberToWords = new \NumberToWords\NumberToWords();
$numberTransformer = $numberToWords->getNumberTransformer('fr');
$total_ht = 0;
$total_tva = 0;
$total_ttc = 0;
$logoBase64 = '';

if (!empty($entreprise->logo_societe)) {
    $logoPath = public_path($entreprise->logo_societe);
    if (file_exists($logoPath) && is_readable($logoPath) && !is_dir($logoPath)) {
        $type = pathinfo($logoPath, PATHINFO_EXTENSION);
        $data = file_get_contents($logoPath);
        $logoBase64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
    }
}

?>
    <!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Facture Client N° {{$factureclient->numero_facture}}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: sans-serif;
            color: #333;
            background: #fff;
            padding: 30px;
            font-size: 18px;
        }
        .invoice-container {
            width: 100%;
            min-height: 1100px;
        }
        .clearfix::after {
            content: "";
            display: table;
            clear: both;
        }
        .invoice-header {
            margin-bottom: 40px;
            border-bottom: 1px solid #eee;
            padding-bottom: 20px;
        }
        .invoice-header .left, .invoice-header .right {
            display: inline-block;
            vertical-align: top;
        }
        .invoice-header .left {
            width: 50%;
        }
        .invoice-header .right {
            width: 49%;
            text-align: right;
        }
        .company-logo {
            width: 250px;
        }
        .company-info, .client-info {
            margin-top: 15px;
        }
        .company-info p, .client-info p {
            margin-bottom: 5px;
        }
        .invoice-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
            border: 1px solid #e0e0e0;
        }
        .invoice-table th, .invoice-table td {
            padding: 10px 12px;
            border-bottom: 1px solid #eee;
            text-align: left;
        }

        .invoice-table td {
            border: none !important;
        }
        .invoice-table th {
            background-color: #0056b3;
            color: #fff;
            text-transform: uppercase;
        }
        .invoice-table td.text-right {
            text-align: right;
        }
        .totals-table {
            width: 350px;
            border-collapse: collapse;
            float: right;
            margin-top: 20px;
            background: #fcfcfc;
            border: 1px solid #e0e0e0;
        }
        .totals-table td {
            padding: 8px 15px;
            border-bottom: 1px dashed #ddd;
        }
        .totals-table tr.grand-total td {
            border-top: 2px solid #0056b3;
            font-weight: bold;
            color: #0056b3;
            font-size: 18px;
        }
        .payment-info-table {
            width: 100%;
            margin-top: 150px;
            /*border-top: 1px solid #eee;*/
            /*padding-top: 20px;*/
        }
        .payment-info-table h3 {
            font-size: 1.2em;
            color: #0056b3;
            margin-bottom: 10px;
        }
        .payment-info-table p {
            margin-bottom: 8px;
        }
        .text-bold {
            font-weight: bold;
        }
        .text-left{
            text-align: left !important;
        }
        .text-right{
            text-align: right !important;
        }
        .text-center {
            text-align: center !important;
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

    </style>
</head>
<body>
<div class="invoice-container">
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
            <div class="document-title">Facture</div>
            <div class="document-info">
                <div><strong>N°:</strong> {{ $factureclient->numero_facture }}</div>
                <p>Date : {{ $factureclient->date_facture }}</p>

                <div class="client-info">
                    <strong>{{ $factureclient->nom_client }}</strong>
                    @if(!empty($factureclient->adresse_client))<p>Adresse : {{ $factureclient->adresse_client }}</p>@endif
                    @if(!empty($factureclient->nif_client))<p>NIF : {{ $factureclient->nif_client }}</p>@endif
                    @if(!empty($factureclient->stat_client))<p>STAT : {{ $factureclient->stat_client }}</p>@endif
                    @if(!empty($factureclient->contact_client))<p>Tél : {{ $factureclient->contact_client }}</p>@endif
                    @if(!empty($factureclient->mail_client))<p>Email : {{ $factureclient->mail_client }}</p>@endif
                </div>

{{--                <div><strong>Date:</strong> {{ \Carbon\Carbon::parse($devis->date_devis)->format('d/m/Y') }}</div>--}}
{{--                <div><strong>Validité:</strong> {{ \Carbon\Carbon::parse($devis->validite_devis)->format('d/m/Y') }}</div>--}}
            </div>
        </div>
    </div>

    <table class="invoice-table">
        <thead>
        <tr>
            <th>Référence</th>
            <th>Description</th>
            <th class="text-center" style="text-align: center !important;">Qte</th>
            <th class="text-right" style="text-align: right !important;">PU HT</th>
            <th class="text-right" style="text-align: right !important;">Montant HT</th>
        </tr>
        </thead>
        <tbody>
        @foreach($factureclient->voyages ?? [] as $voyage)
            @php
                $qte_voyage = $voyage->kilometrage > 0 ? $voyage->kilometrage : 1;
                $montant_ht_voyage = $voyage->tarif_ht * $qte_voyage;
                $total_ht += $montant_ht_voyage;
                $has_mobilisation = $voyage->nbr_jour > 0 ;

            @endphp
            <tr>
                <td rowspan="{{ $has_mobilisation ? 2 : 1 }}">{{ $voyage->numero_voyage }}</td>
                <td>Transport {{ $voyage->destination }}</td>
                <td class="text-center">{{ $qte_voyage }}</td>
                <td class="text-right">{{ number_format($voyage->tarif_ht, 0, ',', ' ') }}</td>
                <td class="text-right">{{ number_format($montant_ht_voyage, 0, ',', ' ') }}</td>
            </tr>
            @if($has_mobilisation)
                @php
                    $nbre_jour = $voyage->nbr_jour > 0 ? $voyage->nbr_jour : 0;
                    $montant_mobilisation = $nbre_jour * $voyage->mobilisation;
                    $total_ht += $montant_mobilisation;
                @endphp
                <tr>
                    <td class="">Immobilisation</td>
                    <td class="text-center ">{{ $nbre_jour }}</td>
                    <td class="text-right ">{{ number_format($voyage->mobilisation, 0, ',', ' ') }}</td>
                    <td class="text-right ">{{ number_format($montant_mobilisation, 0, ',', ' ') }}</td>
                </tr>
            @endif
        @endforeach
        </tbody>
    </table>



    <table class="totals-table">
        <tr>
            <td>Total HT</td>
            <td style="text-align:right">{{ number_format($factureclient->montant_ht, 0, ',', ' ') }}</td>
        </tr>
        @if($factureclient->montant_remise)
            <tr>
                <td>Remise</td>
                <td style="text-align:right">{{ number_format($factureclient->montant_remise, 0, ',', ' ') }}</td>
            </tr>
        @endif
        @if($factureclient->taux_tva)
            <tr>
                <td>TVA </td>
                <td style="text-align:right">{{ number_format($factureclient->montant_tva, 0, ',', ' ') }}</td>
            </tr>
        @endif

        <tr class="grand-total">
            <td>Total TTC</td>
            <td style="text-align:right">{{ number_format($factureclient->montant_ttc, 0, ',', ' ') }}</td>
        </tr>
    </table>

    <table class="payment-info-table">
        <tr>
            <td>
                <p class="note">Arrêtée la présente facture à la somme de : <span style="text-transform: capitalize !important;">{{ $numberTransformer->toWords($factureclient->montant_ttc) }} Ariary</span> </p>
            </td>
        </tr>
    </table>
</div>
</body>
</html>
