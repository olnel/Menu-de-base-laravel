<!DOCTYPE html>
<html>
<head>
    <title>FACTURE CLIENT</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.5;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
        }
        .container {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            background-color: white;
        }
        .content {
            padding: 25px;
        }
        .header {
            background-color: #001c47;
            color: white;
            padding: 20px;
            text-align: center;
        }
        h1 {
            margin: 0;
            font-size: 22px;
            font-weight: normal;
        }
        .highlight {
            font-weight: bold;
            color: #ff7d00;
        }
        .details {
            background-color: #f8fafc;
            padding: 15px;
            border-left: 4px solid #001c47;
            margin: 20px 0;
        }
        ul {
            padding-left: 20px;
            margin: 10px 0;
        }
        li {
            margin-bottom: 8px;
        }
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #eaeaea;
            font-size: 14px;
            color: #666;
        }
        .signature {
            color: #001c47;
            font-weight: bold;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>FACTURE N° <span class="highlight">{{ $factureclient->numero_facture }}</span></h1>
    </div>

    <div class="content">
        <p>Bonjour <strong>{{ $factureclient->client->nom_client }}</strong>,</p>

        <p>Nous vous prions de bien vouloir trouver en pièce jointe la facture correspondant à vos prestations.</p>

        <div class="details">
            <p><strong>Informations de la facture :</strong></p>
            <ul>
                <li><strong>Numéro de facture :</strong> {{ $factureclient->numero_facture }}</li>
                <li><strong>Date de facturation :</strong> {{ $factureclient->date_facture }}</li>
                <li><strong>Date d'échéance :</strong> {{ $factureclient->date_echeance ?? '-' }}</li>
            </ul>
        </div>

        <p>Nous restons à votre disposition pour tout complément d’information.</p>

        <div class="footer">
            <p>Avec nos salutations distinguées,<br>
                <span class="signature">{{ config('mail.from.name') }}</span>
            </p>
        </div>
    </div>
</div>
</body>
</html>
