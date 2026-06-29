<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Participants - {{ $session->formation->nom }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            color: #333;
            background: #fff;
            padding: 30px 40px;
            font-size: 12px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #1e40af;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }
        .header h1 {
            font-size: 18px;
            color: #1e40af;
            text-transform: uppercase;
            margin-bottom: 5px;
        }
        .header .societe {
            font-size: 14px;
            font-weight: bold;
        }
        .info {
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
        }
        .info div {
            font-size: 13px;
        }
        .info .label {
            font-weight: bold;
            color: #555;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th {
            background: #1e40af;
            color: #fff;
            padding: 10px 12px;
            text-align: left;
            font-size: 12px;
            text-transform: uppercase;
        }
        td {
            padding: 8px 12px;
            border-bottom: 1px solid #ddd;
            font-size: 12px;
        }
        tr:nth-child(even) {
            background: #f9fafb;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #999;
            border-top: 1px solid #ddd;
            padding-top: 15px;
        }
        .no-data {
            text-align: center;
            padding: 40px;
            color: #999;
            font-style: italic;
        }
        @media print {
            body { padding: 15px; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Liste des Participants</h1>
        <p class="societe">{{ $societe?->nom ?? 'TransMada' }}</p>
    </div>

    <div class="info">
        <div>
            <span class="label">Formation :</span> {{ $session->formation->nom }}
        </div>
        <div>
            <span class="label">Date :</span> {{ $session->date_formation->format('d/m/Y') }}
        </div>
    </div>

    @if($session->date_prochaine_formation)
    <div class="info">
        <div>
            <span class="label">Prochaine échéance :</span> {{ $session->date_prochaine_formation->format('d/m/Y') }}
        </div>
        <div>
            <span class="label">Participants :</span> {{ count($participants) }}
        </div>
    </div>
    @endif

    @if(count($participants) > 0)
    <table>
        <thead>
            <tr>
                <th style="width:50px">N°</th>
                <th>Nom</th>
                <th>Prénom</th>
                <th>Matricule</th>
            </tr>
        </thead>
        <tbody>
            @foreach($participants as $i => $p)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $p->nom }}</td>
                <td>{{ $p->prenom ?? '-' }}</td>
                <td>{{ $p->matricule ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <div class="no-data">Aucun participant pour cette session.</div>
    @endif

    <div class="footer">
        Document généré le {{ now()->format('d/m/Y à H:i') }}
    </div>

    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>
