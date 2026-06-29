<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Participants - {{ $session->formation->nom }}</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 11px;
            color: #333;
            margin: 0;
            padding: 10px;
        }
        .header {
            margin-bottom: 25px;
            border-bottom: 2px solid #1e40af;
            padding-bottom: 15px;
        }
        .header-left {
            float: left;
            width: 60%;
        }
        .header-right {
            float: right;
            width: 35%;
            text-align: right;
        }
        .clear {
            clear: both;
        }
        .header h1 {
            font-size: 18px;
            color: #1e40af;
            text-transform: uppercase;
            margin: 0 0 5px 0;
        }
        .header .societe {
            font-size: 13px;
            font-weight: bold;
            color: #555;
            margin: 0;
        }
        .info-table {
            width: 100%;
            margin-bottom: 20px;
            border: none;
        }
        .info-table td {
            border: none;
            padding: 4px 0;
            font-size: 12px;
        }
        .info-label {
            font-weight: bold;
            color: #4b5563;
        }
        table.participants-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        table.participants-table th {
            background-color: #1e40af;
            color: #ffffff;
            border: 1px solid #ddd;
            padding: 8px 10px;
            text-align: left;
            font-size: 10px;
            text-transform: uppercase;
        }
        table.participants-table td {
            border: 1px solid #ddd;
            padding: 8px 10px;
            font-size: 11px;
        }
        table.participants-table tr:nth-child(even) {
            background-color: #f9fafb;
        }
        .footer {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 9px;
            color: #999;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        .no-data {
            text-align: center;
            padding: 30px;
            color: #999;
            font-style: italic;
            border: 1px dashed #ddd;
            background-color: #f9fafb;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-left">
            <h1>Liste des Participants</h1>
            <p class="societe">{{ $societe?->nom_societe ?? $societe?->nom ?? 'TRANSMADA' }}</p>
        </div>
        <div class="header-right">
            <p style="margin: 0; font-size: 10px; color: #666;">Date génération : {{ date('d/m/Y H:i') }}</p>
        </div>
        <div class="clear"></div>
    </div>

    <table class="info-table">
        <tr>
            <td style="width: 50%;">
                <span class="info-label">Formation :</span> {{ $session->formation->nom }}
            </td>
            <td style="width: 50%; text-align: right;">
                <span class="info-label">Date de session :</span> {{ $session->date_formation ? $session->date_formation->format('d/m/Y') : '-' }}
            </td>
        </tr>
        @if($session->date_prochaine_formation)
        <tr>
            <td>
                <span class="info-label">Prochaine échéance :</span> {{ $session->date_prochaine_formation->format('d/m/Y') }}
            </td>
            <td style="text-align: right;">
                <span class="info-label">Total participants :</span> {{ count($participants) }}
            </td>
        </tr>
        @else
        <tr>
            <td>&nbsp;</td>
            <td style="text-align: right;">
                <span class="info-label">Total participants :</span> {{ count($participants) }}
            </td>
        </tr>
        @endif
        @if($session->observation)
        <tr>
            <td colspan="2">
                <span class="info-label">Observation :</span> {{ $session->observation }}
            </td>
        </tr>
        @endif
    </table>

    @if(count($participants) > 0)
    <table class="participants-table">
        <thead>
            <tr>
                <th style="width: 8%;">N°</th>
                <th style="width: 32%;">Nom</th>
                <th style="width: 32%;">Prénom</th>
                <th style="width: 28%;">Matricule</th>
            </tr>
        </thead>
        <tbody>
            @foreach($participants as $i => $p)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td style="font-weight: bold;">{{ $p->nom }}</td>
                <td>{{ $p->prenom ?? '-' }}</td>
                <td>{{ $p->matricule ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <div class="no-data">Aucun participant enregistré pour cette session.</div>
    @endif

    <div class="footer">
        Document généré le {{ date('d/m/Y à H:i') }}
    </div>
</body>
</html>
