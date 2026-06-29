
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Confirmation</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f7;
            margin: 0;
            padding: 0;
        }
        .email-container {
            max-width: 600px;
            margin: 30px auto;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .email-header {
            background-color: #4CAF50;
            color: #ffffff;
            text-align: center;
            padding: 20px;
        }
        .email-body {
            padding: 30px;
            color: #333333;
            font-size: 16px;
            line-height: 1.6;
        }
        .email-footer {
            text-align: center;
            font-size: 12px;
            color: #777777;
            padding: 20px;
        }
        .btn {
            display: inline-block;
            padding: 12px 20px;
            margin-top: 20px;
            color: #fff;
            background-color: #4CAF50;
            text-decoration: none;
            border-radius: 5px;
        }
        .btn:hover {
            background-color: #43a047;
        }
    </style>
</head>
<body>
<div class="email-container">
    <div class="email-header">
        <h1>Confirmation</h1>
    </div>
    <div class="email-body">
        <p>Bonjour </p>
        <p>Nous avons bien reçu votre demande et nous vous remercions pour votre confiance.</p>
        <p>Veuillez confirmer votre adresse email en cliquant sur le bouton ci-dessous :</p>
        <p style="text-align: center;">
            <a href="{{ route('newsletter.confirm', ['token' => $newsletter->token]) }}" class="btn">Confirmer mon adresse</a>
        </p>
        <p>Si vous n'avez pas effectué cette demande, veuillez ignorer ce message.</p>
        <p>Bien cordialement,<br>L'équipe <strong>{{ config('app.name') }}</strong></p>
    </div>

</div>
</body>
</html>
