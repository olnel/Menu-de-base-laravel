<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouvelle question</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap');

        .body {
            font-family: 'Roboto', Arial, sans-serif;
            background-color: #f3f4f6;
            color: #4b5563;
            margin: 0;
            padding: 20px 10px 30px 10px;
        }

        .container {
            background-color: #ffffff;
            padding: 20px;
            max-width: 600px;
            margin: 20px auto;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .btn {
            display: inline-block;
            text-decoration: none;
            color: #ffffff !important;
            background-color: #4b5563;
            padding: 10px 20px;
            border-radius: 5px;
            margin: 5px 0;
            text-align: center;
        }

        .btn:hover {
            background-color: #374151;
        }

        .footer {
            text-align: center;
            font-size: 12px;
            color: #9ca3af;
            margin-top: 30px;
        }
    </style>
</head>
<body>
<div class="body">
    <div class="container">
        <h1 style="text-align: center; font-size: 24px; font-weight: bold;">{{ config("app.name") }}</h1>
        <p>Bonjour,</p>
        <p>Une nouvelle question a été ajoutée par <strong>{{ $question->user?->name }}</strong> et est en attente de votre validation. Merci de la vérifier dès que possible.</p>
        <p><strong>Question :</strong> {{ $question->label }}</p>
        <p style="margin-bottom: 0"><strong>Choix de réponses :</strong></p>
        <ul style="margin-top: 5px">
            @foreach($question->choix_reponses as $reponse)
                <li>
                    {{ $reponse->label }}
                    @if($reponse->correct)
                        (correct)
                    @endif
                </li>
            @endforeach
        </ul>
        <div style="text-align: center; margin: 20px 0;">
            <a href="{{ route('question.validation', $question->id) }}" class="btn">Valider</a>
            <span style="margin: 0 10px"></span>
            <a href="{{ route('question.update', $question->id) }}" class="btn">Modifier</a>
        </div>
        <p>Cordialement,<br>{{ config("app.name") }}</p>

        <hr style="margin: 20px 0; border: none; border-top: 1px solid #e5e7eb;">

        <div style="font-size: 13px;">
            <p>Si vous avez des difficultés à cliquer sur les boutons, utilisez les URL ci-dessous :</p>
            <p>
                <span>Valider : <a href="{{ route('question.validation', $question->id) }}">{{ route('question.validation', $question->id) }}</a></span>
                <br>
                <span>Modifier : <a href="{{ route('question.update', $question->id) }}">{{ route('question.update', $question->id) }}</a></span>
            </p>
        </div>
    </div>
    <div class="footer">
        © {{ date('Y') }} {{ config("app.name") }}. Tous droits réservés.
    </div>
</div>
</body>
</html>
