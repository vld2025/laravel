<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Reports Mensili - {{ $user->name }} - {{ $meseNome }} {{ $anno }}</title>
    <style>
        @page {
            margin: 20mm;
        }
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10pt;
            line-height: 1.4;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #2563eb;
            padding-bottom: 15px;
        }
        .logo {
            font-size: 20pt;
            font-weight: bold;
            color: #2563eb;
        }
        .subtitle {
            margin-top: 10px;
            color: #666;
            font-size: 12pt;
        }
        .info-box {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            border-left: 4px solid #2563eb;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 9pt;
        }
        .table th {
            background-color: #2563eb;
            color: white;
            padding: 8px 4px;
            text-align: center;
            font-weight: bold;
            border: 1px solid #1e40af;
        }
        .table td {
            border: 1px solid #ddd;
            padding: 6px 4px;
            text-align: center;
        }
        .table td.text-left {
            text-align: left;
        }
        .totali {
            background-color: #e3f2fd;
            padding: 15px;
            border-radius: 5px;
            border-left: 4px solid #2563eb;
            margin-top: 20px;
        }
        .footer {
            position: fixed;
            bottom: 10mm;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 8pt;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 5px;
        }
        .page-break {
            page-break-before: always;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">VLD SERVICE GmbH</div>
        <div class="subtitle">Report Mensile Attività - {{ $meseNome }} {{ $anno }}</div>
    </div>

    <div class="info-box">
        <table style="width: 100%; border: none;">
            <tr>
                <td style="border: none; padding: 0;"><strong>Dipendente:</strong> {{ $user->name }}</td>
                <td style="border: none; padding: 0; text-align: right;"><strong>Periodo:</strong> {{ $meseNome }} {{ $anno }}</td>
            </tr>
            <tr>
                <td style="border: none; padding: 0;"><strong>Giorni Lavorati:</strong> {{ $reports->count() }}</td>
                <td style="border: none; padding: 0; text-align: right;"><strong>Generato il:</strong> {{ date('d/m/Y H:i') }}</td>
            </tr>
        </table>
    </div>

    @if($reports->count() > 0)
    <table class="table">
        <thead>
            <tr>
                <th style="width: 10%;">Data</th>
                <th style="width: 15%;">Committente</th>
                <th style="width: 15%;">Cliente</th>
                <th style="width: 15%;">Commessa</th>
                <th style="width: 8%;">Ore Lav.</th>
                <th style="width: 8%;">Ore Viag.</th>
                <th style="width: 6%;">Km</th>
                <th style="width: 23%;">Descrizione Lavori</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reports->sortBy('data') as $report)
            <tr>
                <td>{{ $report->data->format('d/m/Y') }}</td>
                <td class="text-left">{{ $report->committente->nome ?? 'N/A' }}</td>
                <td class="text-left">{{ $report->cliente->nome ?? 'N/A' }}</td>
                <td class="text-left">{{ $report->commessa->nome ?? 'N/A' }}</td>
                <td>{{ number_format($report->ore_lavorate, 1) }}h</td>
                <td>{{ number_format($report->ore_viaggio, 1) }}h</td>
                <td>{{ $report->km_auto }} km</td>
                <td class="text-left">{{ Str::limit($report->descrizione_lavori ?? 'N/A', 60) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totali">
        <table style="width: 100%; border: none;">
            <tr>
                <td style="border: none; padding: 0;"><strong>TOTALI MENSILI:</strong></td>
                <td style="border: none; padding: 0; text-align: right;">
                    <strong>Ore Lavorate:</strong> {{ number_format($reports->sum('ore_lavorate'), 1) }}h |
                    <strong>Ore Viaggio:</strong> {{ number_format($reports->sum('ore_viaggio'), 1) }}h |
                    <strong>Km Totali:</strong> {{ number_format($reports->sum('km_auto'), 0) }} km
                </td>
            </tr>
            <tr>
                <td style="border: none; padding: 0;"><strong>Ore Totali:</strong> {{ number_format($reports->sum('ore_lavorate') + $reports->sum('ore_viaggio'), 1) }}h</td>
                <td style="border: none; padding: 0; text-align: right;"><strong>Media giornaliera:</strong> {{ number_format(($reports->sum('ore_lavorate') + $reports->sum('ore_viaggio')) / max($reports->count(), 1), 1) }}h/giorno</td>
            </tr>
        </table>
    </div>
    @else
    <div style="text-align: center; padding: 50px; color: #666;">
        <h3>Nessun report trovato per il periodo {{ $meseNome }} {{ $anno }}</h3>
        <p>Il dipendente {{ $user->name }} non ha inserito report per questo mese.</p>
    </div>
    @endif

    <div class="footer">
        VLD Service GmbH - Report generato automaticamente dal sistema di gestione aziendale
    </div>
</body>
</html>
