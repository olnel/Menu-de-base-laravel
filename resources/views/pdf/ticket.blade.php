<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ticket - {{ $ticket->technicien_nom ?? 'Technicien' }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            color: #333;
            background: #fff;
            padding: 8px;
            font-size: 9px;
            width: 80mm;
        }
        .ticket {
            width: 100%;
            border: 1px solid #333;
            padding: 6px;
        }
        .header {
            text-align: center;
            border-bottom: 1px dashed #333;
            padding-bottom: 6px;
            margin-bottom: 6px;
        }
        .header h2 {
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 2px;
        }
        .header .entreprise-name {
            font-size: 10px;
            font-weight: bold;
        }
        .header .entreprise-info {
            font-size: 7px;
            color: #555;
        }
        .section {
            margin-bottom: 6px;
        }
        .section-title {
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
            background: #333;
            color: #fff;
            padding: 2px 4px;
            margin-bottom: 3px;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 1px 4px;
            font-size: 8px;
        }
        .info-row .label {
            font-weight: bold;
            color: #555;
        }
        .info-row .value {
            text-align: right;
        }
        .divider {
            border-top: 1px dashed #ccc;
            margin: 4px 0;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            font-weight: bold;
            font-size: 10px;
            padding: 3px 4px;
            background: #f5f5f5;
            border-top: 2px solid #333;
            border-bottom: 2px solid #333;
        }
        .footer {
            text-align: center;
            font-size: 7px;
            color: #888;
            margin-top: 6px;
            border-top: 1px dashed #333;
            padding-top: 4px;
        }
        .badge {
            display: inline-block;
            font-size: 7px;
            padding: 1px 4px;
            border-radius: 2px;
            background: #e8f5e9;
            color: #2e7d32;
            font-weight: bold;
            text-transform: uppercase;
        }
        .badge-changer {
            background: #fff3e0;
            color: #e65100;
        }
        table.details {
            width: 100%;
            border-collapse: collapse;
            font-size: 8px;
        }
        table.details th, table.details td {
            padding: 2px 4px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        table.details th {
            background: #f5f5f5;
            font-weight: bold;
            font-size: 7px;
            text-transform: uppercase;
        }
        .text-muted { color: #999; }
    </style>
</head>
<body>
<div class="ticket">
    {{-- Header --}}
    <div class="header">
        @if(!empty($entreprise))
            <div class="entreprise-name">{{ $entreprise->nom_societe ?? 'Société' }}</div>
            @if(!empty($entreprise->adresse_societe))
                <div class="entreprise-info">{{ $entreprise->adresse_societe }}</div>
            @endif
            @if(!empty($entreprise->telephone_societe))
                <div class="entreprise-info">Tél: {{ $entreprise->telephone_societe }}</div>
            @endif
        @endif
        <h2>Ticket de Travail</h2>
        <div>N° {{ !empty($ticket->id) ? str_pad($ticket->id, 4, '0', STR_PAD_LEFT) : '---' }}</div>
    </div>

    {{-- Technician Info --}}
    <div class="section">
        <div class="section-title">Technicien</div>
        <div class="info-row">
            <span class="label">Nom :</span>
            <span class="value">{{ $ticket->technicien_nom ?? '-' }}</span>
        </div>
        <div class="info-row">
            <span class="label">Type MO :</span>
            <span class="value">
                <span class="badge {{ !empty($ticket->type_main_oeuvre) && $ticket->type_main_oeuvre === 'changer' ? 'badge-changer' : '' }}">
                    {{ !empty($ticket->type_main_oeuvre) && $ticket->type_main_oeuvre === 'principal' ? 'Principal' : 'Remplacement' }}
                </span>
            </span>
        </div>
    </div>

    {{-- Vehicle Info --}}
    <div class="section">
        <div class="section-title">Véhicule / Réparation</div>
        <div class="info-row">
            <span class="label">Immatriculation :</span>
            <span class="value">{{ $ticket->immatriculation ?? '-' }}</span>
        </div>
        @if(!empty($ticket->numero_remorque))
        <div class="info-row">
            <span class="label">Remorque :</span>
            <span class="value">{{ $ticket->numero_remorque }}</span>
        </div>
        @endif
        <div class="info-row">
            <span class="label">Date réparation :</span>
            <span class="value">{{ !empty($ticket->date_reparation) ? \Carbon\Carbon::parse($ticket->date_reparation)->format('d/m/Y') : '-' }}</span>
        </div>
    </div>

    {{-- Article Details --}}
    <div class="section">
        <div class="section-title">Article</div>
        <table class="details">
            @if(!empty($ticket->designation_article_changer) || !empty($ticket->designation_article))
            @if(!empty($ticket->designation_article_changer))
            <tr>
                <th style="width: 30px;">État</th>
                <th>Désignation</th>
                <th>Réf.</th>
            </tr>
            <tr>
                <td><span style="color: #e53935;">✗ Ancien</span></td>
                <td>{{ $ticket->designation_article_changer }}</td>
                <td>{{ $ticket->reference_article_changer ?? '-' }}</td>
            </tr>
            <tr>
                <td><span style="color: #43a047;">✓ Nouveau</span></td>
                <td>{{ $ticket->designation_article ?? '-' }}</td>
                <td>{{ $ticket->reference_article ?? '-' }}</td>
            </tr>
            @else
            <tr>
                <th>Désignation</th>
                <th>Réf.</th>
            </tr>
            <tr>
                <td>{{ $ticket->designation_article ?? '-' }}</td>
                <td>{{ $ticket->reference_article ?? '-' }}</td>
            </tr>
            @endif
            @else
            <tr>
                <td colspan="2" class="text-muted" style="padding: 4px; text-align: center;">Aucun article</td>
            </tr>
            @endif
        </table>
    </div>

    {{-- Labor Details --}}
    <div class="section">
        <div class="section-title">Main d'Œuvre</div>
        <div class="info-row">
            <span class="label">Tarif horaire :</span>
            <span class="value">{{ number_format((float)($ticket->tarif_horaire ?? 0), 0, ',', ' ') }} {{ $entreprise->devise ?? 'Ar' }}</span>
        </div>
        <div class="info-row">
            <span class="label">Nombre d'heures :</span>
            <span class="value">{{ (float)($ticket->nbre_heure ?? 0) }} h</span>
        </div>
        <div class="divider"></div>
        <div class="total-row">
            <span>Total M.O</span>
            <span>{{ number_format((float)($ticket->total ?? 0), 0, ',', ' ') }} {{ $entreprise->devise ?? 'Ar' }}</span>
        </div>
    </div>

    {{-- Observations --}}
    @if(!empty($ticket->observations_reparation))
    <div class="section">
        <div class="section-title">Observations</div>
        <div style="font-size: 7px; padding: 2px 4px;">
            {{ $ticket->observations_reparation }}
        </div>
    </div>
    @endif

    {{-- Signature --}}
    <div class="section" style="margin-top: 12px;">
        <div class="info-row" style="margin-bottom: 20px;">
            <span class="label">Cachet et signature :</span>
            <span></span>
        </div>
    </div>

    {{-- Footer --}}
    <div class="footer">
        Document généré le {{ now()->format('d/m/Y à H:i') }}
    </div>
</div>
</body>
</html>
