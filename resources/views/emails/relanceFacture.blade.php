<!DOCTYPE html>
<html>
<head>
    <title>RAPPEL DE PAIEMENT - FACTURE CLIENT</title>
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
            background-color: #c0392b;
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
            color: #ffffff;
        }
        .details {
            background-color: #f8fafc;
            padding: 15px;
            border-left: 4px solid #c0392b;
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
            color: #c0392b;
            font-weight: bold;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>RAPPEL DE PAIEMENT : FACTURE <span class="highlight">{{ $factureClient->numero_facture }}</span></h1>
    </div>

    <div class="content">
        <p>Bonjour <strong>{{ $factureClient->client->nom_client }}</strong>,</p>

        <p>Sauf erreur ou omission de notre part, nous n'avons pas encore reçu le règlement de la facture citée en référence, arrivée à échéance le <strong>{{ $factureClient->date_echeance }}</strong>.</p>

        <div class="details">
            <p><strong>Détails du compte :</strong></p>
            <ul>
                <li><strong>Numéro de facture :</strong> {{ $factureClient->numero_facture }}</li>
                <li><strong>Montant TTC :</strong> {{ number_format($factureClient->montant_ttc, 2, ',', ' ') }}</li>
                <li><strong>Déjà payé :</strong> {{ number_format($factureClient->montant_payer, 2, ',', ' ') }}</li>
                <li><strong>Reste à payer :</strong> <span style="color: #c0392b; font-weight: bold;">{{ number_format($factureClient->montant_reste_a_payer, 2, ',', ' ') }}</span></li>
            </ul>
        </div>

        <p>Nous vous prions de bien vouloir régulariser cette situation dans les plus brefs délais. Si votre règlement a déjà été envoyé, nous vous prions de ne pas tenir compte de ce rappel.</p>

        <p>Nous restons à votre entière disposition pour toute question relative à cette facture.</p>

        <div class="footer">
            <p>Cordialement,<br>
                <span class="signature">{{ config('mail.from.name') }}</span>
            </p>
        </div>
    </div>
</div>
</body>
</html>
