<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Tickets - Réparation N°{{ $reparation->id ?? '' }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            color: #333;
            background: #fff;
            padding: 5px;
            font-size: 9px;
        }
        .page-break {
            page-break-after: always;
        }
        .ticket {
            width: 70mm;
            margin: 0 auto;
            border: 1px solid #333;
            padding: 6px;
            margin-bottom: 8px;
        }
        .header {
            text-align: center;
            border-bottom: 1px dashed #333;
            padding-bottom: 6px;
            margin-bottom: 6px;
        }
        .header h2 {
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .header .entreprise-name {
            font-size: 10px;
            font-weight: bold;
        }
        .section {
            margin-bottom: 5px;
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
        .info-row .label { font-weight: bold; color: #555; }
        .info-row .value { text-align: right; }
        .divider { border-top: 1px dashed #ccc; margin: 3px 0; }
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
        .badge-changer { background: #fff3e0; color: #e65100; }
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
        .footer {
            text-align: center;
            font-size: 6px;
            color: #888;
            margin-top: 4px;
            border-top: 1px dashed #333;
            padding-top: 3px;
        }
        .text-muted { color: #999; }
    </style>
</head>
<body>
@if(!empty($tickets) && $tickets->count() > 0)
    @foreach($tickets as $ticket)
        <div class="ticket @if(!$loop->last) page-break @endif">
            {{-- Header --}}
            <div class="header">
                @if(!empty($entreprise))
                    <div class="entreprise-name">{{ $entreprise->nom_societe ?? 'Société' }}</div>
                @endif
                <h2>Ticket de Travail</h2>
                <div>N° {{ !empty($ticket->id) ? str_pad($ticket->id, 4, '0', STR_PAD_LEFT) : '---' }}</div>
            </div>

            {{-- Technician --}}
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

            {{-- Vehicle --}}
            <div class="section">
                <div class="section-title">Véhicule</div>
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
                    <span class="label">Date :</span>
                    <span class="value">{{ !empty($ticket->date_reparation) ? \Carbon\Carbon::parse($ticket->date_reparation)->format('d/m/Y') : '-' }}</span>
                </div>
            </div>

            {{-- Article --}}
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
                        <td><span style="color: #e53935;">✗</span></td>
                        <td>{{ $ticket->designation_article_changer }}</td>
                        <td>{{ $ticket->reference_article_changer ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td><span style="color: #43a047;">✓</span></td>
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

            {{-- Labor --}}
            <div class="section">
                <div class="section-title">Main d'Œuvre</div>
                <div class="info-row">
                    <span class="label">Tarif horaire :</span>
                    <span class="value">{{ number_format((float)($ticket->tarif_horaire ?? 0), 0, ',', ' ') }} {{ $entreprise->devise ?? 'Ar' }}</span>
                </div>
                <div class="info-row">
                    <span class="label">Heures :</span>
                    <span class="value">{{ (float)($ticket->nbre_heure ?? 0) }} h</span>
                </div>
                <div class="divider"></div>
                <div class="total-row">
                    <span>Total</span>
                    <span>{{ number_format((float)($ticket->total ?? 0), 0, ',', ' ') }} {{ $entreprise->devise ?? 'Ar' }}</span>
                </div>
            </div>

            {{-- Footer --}}
            <div class="footer">
                Ticket N°{{ !empty($ticket->id) ? str_pad($ticket->id, 4, '0', STR_PAD_LEFT) : '---' }}
            </div>
        </div>
    @endforeach
@else
    <div style="text-align: center; padding: 20px; font-size: 12px;">
        <p>Aucun ticket disponible</p>
    </div>
@endif
</body>
</html>
