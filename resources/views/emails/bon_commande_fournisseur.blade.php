<!DOCTYPE html>
<html>
<head>
    <title>Bon de Commande</title>
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
            color: #ff7d00; /* Couleur d'accent orange pour contraster avec le bleu */
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
        <h1>Bon de Commande N°<span class="highlight">{{ $bonCommande->numero_bon_commande }}</span></h1>
    </div>

    <div class="content">
        <p>Bonjour <strong>{{ $bonCommande->nom_fournisseur }}</strong>,</p>

        <p>Veuillez trouver ci-joint le bon de commande que nous vous avons passé.</p>

        <div class="details">
            <p><strong>Détails :</strong></p>
            <ul>
                <li>Date : <strong>{{ $bonCommande->date_boncommande }}</strong></li>
                <!-- Ajoutez d'autres détails au besoin -->
            </ul>
        </div>

        <div class="footer">
            <p>Cordialement,<br>
                <span class="signature">{{ config('mail.from.name') }}</span></p>
        </div>
    </div>
</div>
</body>
</html>
