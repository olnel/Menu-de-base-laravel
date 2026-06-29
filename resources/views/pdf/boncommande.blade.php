<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Boncommande N°{{ $bonCommande->numero_bon_commande ?? 'BC-001' }}</title>
    <style>


        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }

        .container {
            width: 100%;
            margin: 0 auto;
        }

        /* Header Layout */
        .header {
            display: table;
            width: 100%;
            margin-bottom: 7px;
            border-bottom: 2px solid #2c5aa0;
            padding-bottom: 20px;
        }

        .header-left {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            padding-right: 20px;
        }

        .header-right {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }

        /* Logo et informations société (gauche) */
        .company-section {
            text-align: left;
        }

        .company-logo {
            margin-bottom: 15px;
        }

        .company-logo img {
            max-width: 120px;
            max-height: 80px;
        }

        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #2c5aa0;
            margin-bottom: 8px;
        }

        .company-details {
            color: #666;
            font-size: 18px;
            line-height: 1.5;
        }

        .company-details div {
            margin-bottom: 3px;
        }

        /* Numéro bon de commande et fournisseur (droite) */
        .document-section {
            text-align: left;
        }

        .bon-commande-title {
            font-size: 24px;
            font-weight: bold;
            color: #2c5aa0;
            text-transform: uppercase;
            margin-bottom: 10px;
            text-align: center;
            border: 2px solid #2c5aa0;
            padding: 10px;
            background-color: #f8f9fa;
        }

        .bon-commande-number {
            font-size: 18px;
            font-weight: bold;
            color: #333;
            margin-bottom: 5px;
        }

        .bon-commande-date {
            font-size: 18px;
            color: #666;
            margin-bottom: 25px;
        }

        /* Informations fournisseur */
        .supplier-info {
            border: 1px solid #e9ecef;
            padding: 18px;
            background-color: #f8f9fa;
            border-radius: 5px;
        }

        .supplier-title {
            font-size: 18px;
            font-weight: bold;
            color: #2c5aa0;
            margin-bottom: 10px;
            text-transform: uppercase;
            border-bottom: 1px solid #2c5aa0;
            padding-bottom: 5px;
        }

        .supplier-name {
            font-size: 18px;
            font-weight: bold;
            color: #333;
            margin-bottom: 8px;
        }

        .supplier-details {
            color: #666;
            font-size: 18px;
            line-height: 1.5;
        }

        .supplier-details div {
            margin-bottom: 3px;
        }

        .supplier-logo {
            text-align: right;
            margin-top: 10px;
        }

        .supplier-logo img {
            max-width: 80px;
            max-height: 60px;
        }

        /* Section articles */
        .items-section {
            margin-bottom: 30px;
        }

        .section-title {
            font-size: 18px;
            font-weight: bold;
            color: #2c5aa0;
            margin-bottom: 15px;
            text-transform: uppercase;
            border-bottom: 2px solid #2c5aa0;
            padding-bottom: 8px;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .items-table th {
            background-color: #2c5aa0;
            color: white;
            padding: 15px 10px;
            text-align: left;
            font-weight: bold;
            font-size: 18px;
            text-transform: uppercase;
        }

        .items-table td {
            padding: 12px 10px;
            border-bottom: 1px solid #e9ecef;
            vertical-align: top;
            font-size: 18px;
        }

        .items-table tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        .text-center {
            text-align: center;
        }

        .font-bold {
            font-weight: bold;
        }

        /* Footer */
        .footer {
            margin-top: 40px;
            padding-top: 15px;
            border-top: 1px solid #e9ecef;
            font-size: 14px;
            color: #666;
            text-align: center;
        }

    </style>
</head>
<body>
<div class="body">
    <div class="container">
        <!-- Header avec disposition demandée -->
        <div class="header">
            <!-- Côté gauche: Logo et informations société -->
            <div class="header-left">
                <div class="company-section">
                    <div class="company-logo">
                        @if(isset($entreprise['logo']) && !empty($entreprise['logo']))
                            <img src="{{ $entreprise['logo'] }}" alt="Logo Société">
                        @endif
                    </div>
                    <div class="company-name">{{ $entreprise['nom'] ?? 'VOTRE SOCIÉTÉ' }}</div>
                    <div class="company-details">
                        <div><strong>Adresse:</strong> {{ $entreprise['adresse'] ?? '123 Rue Exemple, Ville' }}</div>
                        @if(isset($entreprise['telephone']))
                            <div><strong>Tél:</strong> {{ $entreprise['telephone'] }}</div>
                        @endif
                        @if(isset($entreprise['email']))
                            <div><strong>Email:</strong> {{ $entreprise['email'] }}</div>
                        @endif
                        @if(isset($entreprise['nif']))
                            <div><strong>NIF:</strong> {{ $entreprise['nif'] }}</div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Côté droit: Numéro bon de commande et fournisseur -->
            <div class="header-right">
                <div class="document-section">
                    <div class="bon-commande-title">Bon de Commande</div>
                    <div class="bon-commande-number">N° {{ $bonCommande->numero_bon_commande ?? 'BC-001' }}</div>
                    <div class="bon-commande-date">Date: {{ $date ?? date('d/m/Y') }}</div>

                    <div class="supplier-info">
                        <div class="supplier-title">Fournisseur</div>
                        <div class="supplier-name">{{ $bonCommande->fournisseur->nom_fournisseur ?? 'Nom Fournisseur' }}</div>
                        <div class="supplier-details">
                            <div><strong>Adresse:</strong></div>
                            <div>{{ $bonCommande->fournisseur->adresse ?? 'Adresse du fournisseur' }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tableau des articles (sans prix) -->
        <div class="items-section">
            <div class="section-title">Liste des Articles Commandés</div>
            <table class="items-table">
                <thead>
                <tr>
                    <th style="width: 20%">Référence</th>
                    <th style="width: 60%">Désignation</th>
                    <th style="width: 20%" class="text-center">Quantité</th>
                </tr>
                </thead>
                <tbody>
                @foreach($bonCommande->details ?? [] as $detail)
                    <tr>
                        <td class="font-bold">{{ $detail->article->reference ?? $detail->reference ?? 'N/A' }}</td>
                        <td>{{ $detail->article->designation ?? $detail->designation ?? 'N/A' }}</td>
                        <td class="text-center font-bold">{{ $detail->qte_commander ?? 0 }}</td>
                    </tr>

                @endforeach
                </tbody>
            </table>
        </div>


        <!-- Footer -->
<!--        <div class="footer">
            <p>Document généré le {{ date('d/m/Y à H:i') }} - {{ $entreprise['nom'] ?? 'VOTRE SOCIÉTÉ' }}</p>
        </div>-->
    </div>
</div>
</body>
</html>
