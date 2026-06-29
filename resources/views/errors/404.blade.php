<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page non trouvée</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap');

        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            background-color: #f0f4f8; /* Un gris clair moderne */
            font-family: 'Poppins', sans-serif;
            color: #334e68; /* Une couleur de texte plus douce */
        }

        .error-container {
            text-align: center;
            background-color: #ffffff;
            padding: 4rem 3rem;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            animation: fadeIn 0.8s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .error-code {
            font-size: 8rem;
            font-weight: 600;
            color: #d1d9e2;
            margin: 0;
            line-height: 1;
        }

        .error-title {
            font-size: 1.8rem;
            font-weight: 600;
            margin: 1rem 0 0.5rem;
        }

        .error-message {
            font-size: 1rem;
            line-height: 1.6;
            color: #72849a;
            margin-bottom: 2rem;
        }

        .back-link {
            display: inline-block;
            background-color: #3b82f6; /* Une couleur d'accent vive */
            color: #ffffff;
            text-decoration: none;
            padding: 0.8rem 1.5rem;
            border-radius: 8px;
            font-weight: 600;
            transition: background-color 0.3s ease;
        }

        .back-link:hover {
            background-color: #2563eb;
        }
    </style>
</head>
<body>
<div class="error-container">
    <h1 class="error-code">404</h1>
    <h2 class="error-title">Page non trouvée</h2>
    <p class="error-message">
        Désolé, la page que vous recherchez n'a pas pu être trouvée.<br>
        Le sous-domaine que vous avez saisi est peut-être incorrect.
    </p>
{{--    <a href="/" class="back-link">Retourner à l'accueil</a>--}}
</div>
</body>
</html>
