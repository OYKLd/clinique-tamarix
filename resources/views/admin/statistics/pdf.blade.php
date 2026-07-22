<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Rapport d'activité — Clinique Tamarix</title>
    <style>
        @page { margin: 1.6cm 1.4cm; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #333; }
        h1 { font-size: 17px; color: #1d537f; margin: 0 0 2px; }
        h2 { font-size: 12px; color: #1d537f; margin: 18px 0 6px; border-bottom: 1.5px solid #a55a63; padding-bottom: 3px; }
        .header { border-bottom: 2px solid #1d537f; padding-bottom: 8px; margin-bottom: 14px; }
        .slogan { color: #a55a63; font-style: italic; font-size: 10px; }
        .meta { font-size: 9px; color: #777; margin-top: 4px; }
        table { width: 100%; border-collapse: collapse; margin-top: 4px; }
        th { background: #1d537f; color: #fff; text-align: left; padding: 5px 6px; font-size: 9px; }
        td { padding: 5px 6px; border-bottom: 1px solid #e3e6ea; font-size: 9px; }
        tr:nth-child(even) td { background: #f7f8fa; }
        .figures td { width: 20%; text-align: center; border: 1px solid #e3e6ea; padding: 8px 4px; }
        .figure-value { font-size: 17px; font-weight: bold; color: #1d537f; display: block; }
        .figure-label { font-size: 8px; color: #666; text-transform: uppercase; }
        .bar { background: #a55a63; height: 7px; display: inline-block; vertical-align: middle; }
        .footer { position: fixed; bottom: -0.9cm; left: 0; right: 0; text-align: center; font-size: 8px; color: #999; }
    </style>
</head>
<body>

    <div class="header">
        <h1>Clinique Médico-Chirurgicale Tamarix</h1>
        <div class="slogan">« Nous plantons l'Espérance »</div>
        <div class="meta">
            <strong>Rapport d'activité</strong> — du {{ $from->format('d/m/Y') }} au {{ $to->format('d/m/Y') }}
            ({{ $from->diffInDays($to) + 1 }} jours)<br>
            Édité le {{ now()->format('d/m/Y à H\hi') }} par {{ $generatedBy }}
        </div>
    </div>

    <h2>Indicateurs clés</h2>
    <table class="figures">
        <tr>
            <td><span class="figure-value">{{ $figures['total'] }}</span><span class="figure-label">Total RDV</span></td>
            <td><span class="figure-value">{{ $figures['pending'] }}</span><span class="figure-label">En attente</span></td>
            <td><span class="figure-value">{{ $figures['confirmed'] }}</span><span class="figure-label">Confirmés</span></td>
            <td><span class="figure-value">{{ $figures['completed'] }}</span><span class="figure-label">Honorés</span></td>
            <td><span class="figure-value">{{ $figures['cancelled'] }}</span><span class="figure-label">Annulés</span></td>
        </tr>
    </table>

    <table class="figures" style="margin-top: 8px;">
        <tr>
            <td><span class="figure-value">{{ $figures['completion_rate'] }}%</span><span class="figure-label">Taux d'honoration</span></td>
            <td><span class="figure-value">{{ $figures['cancellation_rate'] }}%</span><span class="figure-label">Taux d'annulation</span></td>
            <td><span class="figure-value">{{ $figures['online_share'] }}%</span><span class="figure-label">Pris en ligne</span></td>
            <td><span class="figure-value">{{ $figures['new_patients'] }}</span><span class="figure-label">Nouveaux patients</span></td>
            <td><span class="figure-value">{{ $figures['total'] > 0 ? round($figures['total'] / max($from->diffInDays($to) + 1, 1), 1) : 0 }}</span><span class="figure-label">RDV / jour</span></td>
        </tr>
    </table>

    <h2>Activité par médecin</h2>
    <table>
        <thead>
            <tr>
                <th>Médecin</th>
                <th>Spécialité</th>
                <th style="text-align:center;">Total</th>
                <th style="text-align:center;">Honorés</th>
                <th style="text-align:center;">Annulés</th>
                <th style="text-align:center;">Remplissage</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($byDoctor as $doctor)
                <tr>
                    <td>{{ $doctor->full_name }}</td>
                    <td>{{ $doctor->specialty->name }}</td>
                    <td style="text-align:center;">{{ $doctor->total_count }}</td>
                    <td style="text-align:center;">{{ $doctor->completed_count }}</td>
                    <td style="text-align:center;">{{ $doctor->cancelled_count }}</td>
                    <td style="text-align:center;">{{ $doctor->fill_rate }} %</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h2>Répartition par spécialité</h2>
    @php($maxSpecialty = $bySpecialty->max('total') ?: 1)
    <table>
        <thead><tr><th>Spécialité</th><th style="width:60px;text-align:center;">RDV</th><th style="width:45%;">Volume</th></tr></thead>
        <tbody>
            @foreach ($bySpecialty as $row)
                <tr>
                    <td>{{ $row->name }}</td>
                    <td style="text-align:center;">{{ $row->total }}</td>
                    <td><span class="bar" style="width: {{ round($row->total / $maxSpecialty * 100) }}%;"></span></td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h2>Pics d'affluence par créneau horaire</h2>
    @php($maxHour = $byHour->max('total') ?: 1)
    <table>
        <thead><tr><th style="width:70px;">Heure</th><th style="width:60px;text-align:center;">RDV</th><th>Affluence</th></tr></thead>
        <tbody>
            @foreach ($byHour as $row)
                <tr>
                    <td>{{ $row->hour }}h00</td>
                    <td style="text-align:center;">{{ $row->total }}</td>
                    <td><span class="bar" style="width: {{ round($row->total / $maxHour * 100) }}%;"></span></td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Clinique Médico-Chirurgicale Tamarix — Document interne à usage de la direction
    </div>

</body>
</html>
