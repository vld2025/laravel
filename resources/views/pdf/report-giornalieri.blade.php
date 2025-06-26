<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Report VLD Service - {{ $data->format('d/m/Y') }}</title>
    <style>
        @page {
            margin: 20mm;
        }
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10pt;
            line-height: 1.5;
            color: #333;
        }
        h1 {
            color: #0066cc;
            font-size: 18pt;
            margin-bottom: 20px;
            text-align: center;
            border-bottom: 2px solid #0066cc;
            padding-bottom: 10px;
        }
        h2 {
            color: #1976d2;
            font-size: 14pt;
            margin-top: 20px;
            margin-bottom: 10px;
        }
        h3 {
            color: #1976d2;
            font-size: 12pt;
            margin-bottom: 10px;
        }
        .report-box {
            border: 1px solid #e3f2fd;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 20px;
            background-color: #fafafa;
            page-break-inside: avoid;
        }
        .info-grid {
            display: table;
            width: 100%;
            margin-bottom: 10px;
        }
        .info-row {
            display: table-row;
        }
        .info-cell {
            display: table-cell;
            width: 50%;
            padding: 3px 0;
        }
        .flag {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 9pt;
            margin-right: 5px;
        }
        .flag-notturno { background: #ff9800; color: white; }
        .flag-trasferta { background: #2196f3; color: white; }
        .flag-festivo { background: #4caf50; color: white; }
        .report-content {
            background-color: #e8f5e8;
            padding: 12px;
            margin-top: 10px;
            border-left: 4px solid #4caf50;
            border-radius: 3px;
        }
        .descrizione-originale {
            background-color: #fff3cd;
            padding: 12px;
            margin-top: 10px;
            border-left: 4px solid #ffc107;
            border-radius: 3px;
        }
        .riepilogo {
            background-color: #e3f2fd;
            padding: 15px;
            border-radius: 5px;
            margin-top: 30px;
        }
        .riepilogo-grid {
            display: table;
            width: 100%;
        }
        .riepilogo-item {
            display: table-cell;
            width: 33.33%;
            padding: 5px;
        }
        hr {
            border: none;
            border-top: 1px solid #ddd;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            font-size: 9pt;
            color: #666;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
        }
    </style>
</head>
<body>
    <h1>📋 Report VLD Service - {{ $data->format('d/m/Y') }}</h1>
    
    @if($identificativo && $identificativo !== 'TUTTI I TECNICI')
        <p style="text-align: center; color: #666;">Tecnico: {{ $identificativo }}</p>
    @endif

    @foreach($reports as $report)
        <div class="report-box">
            <h3>👤 {{ $report->user->name }}</h3>
            
            <div class="info-grid">
                <div class="info-row">
                    <div class="info-cell"><strong>🏢 Cliente:</strong> {{ $report->cliente->nome }}</div>
                    <div class="info-cell"><strong>📋 Commessa:</strong> {{ $report->commessa->nome }}</div>
                </div>
                @if($includi_dettagli_ore)
                <div class="info-row">
                    <div class="info-cell"><strong>⏰ Ore Lavoro:</strong> {{ $report->ore_lavorate }}h</div>
                    <div class="info-cell"><strong>🚗 Ore Viaggio:</strong> {{ $report->ore_viaggio }}h</div>
                </div>
                <div class="info-row">
                    <div class="info-cell"><strong>📏 Chilometri:</strong> {{ $report->km_auto }} km</div>
                    <div class="info-cell"><strong>📅 Data:</strong> {{ $report->data->format('d/m/Y') }}</div>
                </div>
                @else
                <div class="info-row">
                    <div class="info-cell"><strong>📅 Data:</strong> {{ $report->data->format('d/m/Y') }}</div>
                    <div class="info-cell"></div>
                </div>
                @endif
            </div>

            @if($report->notturno || $report->trasferta || $report->festivo)
                <div style="margin: 10px 0;">
                    @if($report->notturno)<span class="flag flag-notturno">🌙 Notturno</span>@endif
                    @if($report->trasferta)<span class="flag flag-trasferta">🧳 Trasferta</span>@endif
                    @if($report->festivo)<span class="flag flag-festivo">🎉 Festivo</span>@endif
                </div>
            @endif

            @if(!empty($reportContent[$report->id]))
                <div class="report-content">
                    <strong>📝 Report Professionale:</strong><br>
                    {!! nl2br(e($reportContent[$report->id])) !!}
                </div>
            @elseif(!empty($report->descrizione_lavori))
                <div class="descrizione-originale">
                    <strong>📝 Descrizione Originale:</strong><br>
                    {!! nl2br(e($report->descrizione_lavori)) !!}
                </div>
            @endif
        </div>
    @endforeach

    <div class="riepilogo">
        <h3>📊 Riepilogo Giornaliero</h3>
        <div class="riepilogo-grid">
            <div class="riepilogo-item">
                <strong>👥 Tecnici:</strong> {{ $reports->groupBy('user_id')->count() }}
            </div>
            <div class="riepilogo-item">
                <strong>📋 Report:</strong> {{ $reports->count() }}
            </div>
            @if($includi_dettagli_ore)
            <div class="riepilogo-item">
                <strong>⏱️ Ore Lavoro:</strong> {{ $reports->sum('ore_lavorate') }}h
            </div>
            <div class="riepilogo-item">
                <strong>🚗 Ore Viaggio:</strong> {{ $reports->sum('ore_viaggio') }}h
            </div>
            <div class="riepilogo-item">
                <strong>⏰ Totale:</strong> {{ $reports->sum('ore_lavorate') + $reports->sum('ore_viaggio') }}h
            </div>
            <div class="riepilogo-item">
                <strong>🛣️ Km Totali:</strong> {{ $reports->sum('km_auto') }} km
            </div>
            @endif
        </div>
    </div>

    <div class="footer">
        <p>Report generato automaticamente il {{ now()->format('d/m/Y H:i') }}</p>
        <p>VLD Service GmbH - {{ config('app.url') }}</p>
    </div>
</body>
</html>
