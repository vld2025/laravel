<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fattura {{ $fattura->numero }}</title>
    <style>
        @page {
            margin: 15mm;
            size: A4;
        }
        
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 0;
        }
        
        .header {
            border-bottom: 2px solid #0066cc;
            margin-bottom: 20px;
            padding-bottom: 15px;
        }
        
        .company-info {
            float: left;
            width: 50%;
        }
        
        .invoice-info {
            float: right;
            width: 45%;
            text-align: right;
        }
        
        .clear {
            clear: both;
        }
        
        .billing-section {
            margin: 20px 0;
        }
        
        .billing-to {
            background: #f8f9fa;
            padding: 15px;
            border-left: 4px solid #0066cc;
            margin-bottom: 20px;
        }
        
        .invoice-details {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        
        .invoice-details th,
        .invoice-details td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        
        .invoice-details th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        
        .invoice-details .number {
            text-align: right;
        }
        
        .totals {
            width: 100%;
            margin-top: 20px;
        }
        
        .totals-table {
            width: 300px;
            float: right;
            border-collapse: collapse;
        }
        
        .totals-table td {
            padding: 8px;
            border-top: 1px solid #ddd;
        }
        
        .totals-table .total-row {
            background-color: #f8f9fa;
            font-weight: bold;
            border-top: 2px solid #0066cc;
        }
        
        .qr-section {
            margin-top: 40px;
            page-break-inside: avoid;
        }
        
        .qr-bill {
            border: 1px solid #000;
            width: 210mm;
            height: 105mm;
            position: relative;
            background: white;
        }
        
        .qr-receipt {
            width: 62mm;
            height: 105mm;
            float: left;
            padding: 5mm;
            border-right: 1px solid #000;
            font-size: 8pt;
        }
        
        .qr-payment {
            width: 148mm;
            height: 105mm;
            float: left;
            padding: 5mm;
            font-size: 8pt;
        }
        
        .qr-title {
            font-weight: bold;
            font-size: 11pt;
            margin-bottom: 5mm;
        }
        
        .qr-code {
            width: 46mm;
            height: 46mm;
            float: left;
            margin-right: 5mm;
        }
        
        .amount-section {
            margin-top: 10mm;
        }
        
        .currency {
            width: 15mm;
            display: inline-block;
        }
        
        .amount {
            width: 40mm;
            display: inline-block;
            border-bottom: 1px solid #000;
            text-align: right;
            font-weight: bold;
        }
        
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            font-size: 9px;
            color: #666;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="company-info">
            @if($impostazioni->committente->logo)
                <img src="data:image/png;base64,{{ base64_encode(file_get_contents(storage_path('app/public/' . $impostazioni->committente->logo))) }}" 
                     style="max-height: 60px; margin-bottom: 10px;">
            @endif
            <h2 style="margin: 0; color: #0066cc;">{{ $impostazioni->committente->nome }}</h2>
            <div>{!! nl2br(e($impostazioni->indirizzo_fatturazione)) !!}</div>
            @if($impostazioni->partita_iva)
                <div><strong>P.IVA:</strong> {{ $impostazioni->partita_iva }}</div>
            @endif
        </div>
        
        <div class="invoice-info">
            <h1 style="margin: 0; color: #0066cc;">FATTURA</h1>
            <div style="margin-top: 10px;">
                <strong>N°:</strong> {{ $fattura->numero }}<br>
                <strong>Data:</strong> {{ $fattura->data_emissione->format('d/m/Y') }}<br>
                <strong>Periodo:</strong> {{ $mesi[$fattura->mese_riferimento] }} {{ $fattura->anno_riferimento }}
            </div>
        </div>
        <div class="clear"></div>
    </div>

    <!-- Destinatario -->
    <div class="billing-section">
        <div class="billing-to">
            <strong>Fatturato a:</strong><br>
            <strong>{{ $fattura->committente->nome }}</strong><br>
            {!! nl2br(e($fattura->committente->indirizzo ?? 'Indirizzo non specificato')) !!}
        </div>
    </div>

    <!-- Dettagli Fattura -->
    <table class="invoice-details">
        <thead>
            <tr>
                <th style="width: 50%;">Descrizione</th>
                <th style="width: 15%;">Quantità</th>
                <th style="width: 15%;">Prezzo Unit.</th>
                <th style="width: 20%;">Totale</th>
            </tr>
        </thead>
        <tbody>
            @if($fattura->totale_ore_lavoro > 0)
            <tr>
                <td>Ore di lavoro</td>
                <td class="number">{{ number_format($fattura->totale_ore_lavoro, 2) }} h</td>
                <td class="number">CHF {{ number_format($impostazioni->costo_orario, 2) }}</td>
                <td class="number">CHF {{ number_format($fattura->totale_ore_lavoro * $impostazioni->costo_orario, 2) }}</td>
            </tr>
            @endif
            
            @if($fattura->totale_ore_viaggio > 0)
            <tr>
                <td>Ore di viaggio</td>
                <td class="number">{{ number_format($fattura->totale_ore_viaggio, 2) }} h</td>
                <td class="number">CHF {{ number_format($impostazioni->costo_orario, 2) }}</td>
                <td class="number">CHF {{ number_format($fattura->totale_ore_viaggio * $impostazioni->costo_orario, 2) }}</td>
            </tr>
            @endif
            
            @if($fattura->totale_km > 0)
            <tr>
                <td>Chilometri percorsi</td>
                <td class="number">{{ $fattura->totale_km }} km</td>
                <td class="number">CHF {{ number_format($impostazioni->costo_km, 2) }}</td>
                <td class="number">CHF {{ number_format($fattura->totale_km * $impostazioni->costo_km, 2) }}</td>
            </tr>
            @endif
            
            @if($fattura->totale_pranzi > 0 && $impostazioni->costo_pranzo)
            <tr>
                <td>Pranzi</td>
                <td class="number">{{ $fattura->totale_pranzi }}</td>
                <td class="number">CHF {{ number_format($impostazioni->costo_pranzo, 2) }}</td>
                <td class="number">CHF {{ number_format($fattura->totale_pranzi * $impostazioni->costo_pranzo, 2) }}</td>
            </tr>
            @endif
            
            @if($fattura->totale_trasferte > 0 && $impostazioni->costo_trasferta)
            <tr>
                <td>Trasferte</td>
                <td class="number">{{ $fattura->totale_trasferte }}</td>
                <td class="number">CHF {{ number_format($impostazioni->costo_trasferta, 2) }}</td>
                <td class="number">CHF {{ number_format($fattura->totale_trasferte * $impostazioni->costo_trasferta, 2) }}</td>
            </tr>
            @endif
            
            @if($fattura->totale_spese_extra > 0)
            <tr>
                <td>Spese extra</td>
                <td class="number">-</td>
                <td class="number">-</td>
                <td class="number">CHF {{ number_format($fattura->totale_spese_extra, 2) }}</td>
            </tr>
            @endif
        </tbody>
    </table>

    <!-- Totali -->
    <div class="totals">
        <table class="totals-table">
            <tr>
                <td><strong>Imponibile:</strong></td>
                <td class="number"><strong>CHF {{ number_format($fattura->imponibile ?? 0, 2) }}</strong></td>
            </tr>
            @if($fattura->sconto > 0)
            <tr>
                <td>Sconto:</td>
                <td class="number">- CHF {{ number_format($fattura->sconto, 2) }}</td>
            </tr>
            @endif
            <tr class="total-row">
                <td><strong>TOTALE:</strong></td>
                <td class="number"><strong>CHF {{ number_format($fattura->totale ?? 0, 2) }}</strong></td>
            </tr>
        </table>
        <div class="clear"></div>
    </div>

    <!-- Swiss QR Bill -->
    @if($impostazioni->swiss_qr_bill && $qrCode)
    <div class="qr-section">
        <h3>Bollettino di versamento</h3>
        
        <div class="qr-bill">
            <!-- Sezione ricevuta -->
            <div class="qr-receipt">
                <div class="qr-title">Ricevuta</div>
                <div style="margin-bottom: 5mm;">
                    <div style="font-size: 6pt; font-weight: bold;">Conto / Pagabile a</div>
                    <div style="font-size: 8pt;">
                        {{ $impostazioni->iban }}<br>
                        {{ $impostazioni->qr_creditor_name }}<br>
                        {{ $impostazioni->qr_creditor_address }}<br>
                        {{ $impostazioni->qr_creditor_postal_code }} {{ $impostazioni->qr_creditor_city }}
                    </div>
                </div>
                
                <div style="margin-bottom: 5mm;">
                    <div style="font-size: 6pt; font-weight: bold;">Pagabile da</div>
                    <div style="font-size: 8pt;">
                        {{ $fattura->committente->nome }}<br>
                        {{ $fattura->committente->indirizzo ?? '' }}
                    </div>
                </div>
                
                <div class="amount-section">
                    <div style="font-size: 6pt; font-weight: bold;">Valuta</div>
                    <div class="currency">CHF</div>
                    
                    <div style="font-size: 6pt; font-weight: bold; margin-top: 3mm;">Importo</div>
                    <div class="amount">{{ number_format($fattura->totale ?? 0, 2) }}</div>
                </div>
            </div>
            
            <!-- Sezione pagamento -->
            <div class="qr-payment">
                <div class="qr-title">Pagamento</div>
                
                <div style="margin-bottom: 5mm;">
                    <img src="data:image/png;base64,{{ $qrCode }}" class="qr-code" alt="QR Code">
                    
                    <div style="margin-left: 51mm;">
                        <div style="font-size: 6pt; font-weight: bold;">Valuta</div>
                        <div class="currency">CHF</div>
                        
                        <div style="font-size: 6pt; font-weight: bold; margin-top: 3mm;">Importo</div>
                        <div class="amount">{{ number_format($fattura->totale ?? 0, 2) }}</div>
                    </div>
                    <div class="clear"></div>
                </div>
                
                <div style="margin-bottom: 5mm;">
                    <div style="font-size: 6pt; font-weight: bold;">Conto / Pagabile a</div>
                    <div style="font-size: 8pt;">
                        {{ $impostazioni->iban }}<br>
                        {{ $impostazioni->qr_creditor_name }}<br>
                        {{ $impostazioni->qr_creditor_address }}<br>
                        {{ $impostazioni->qr_creditor_postal_code }} {{ $impostazioni->qr_creditor_city }}
                    </div>
                </div>
                
                <div style="margin-bottom: 5mm;">
                    <div style="font-size: 6pt; font-weight: bold;">Pagabile da</div>
                    <div style="font-size: 8pt;">
                        {{ $fattura->committente->nome }}<br>
                        {{ $fattura->committente->indirizzo ?? '' }}
                    </div>
                </div>
                
                @if($impostazioni->qr_additional_info)
                <div style="margin-bottom: 5mm;">
                    <div style="font-size: 6pt; font-weight: bold;">Informazioni aggiuntive</div>
                    <div style="font-size: 8pt;">{{ $impostazioni->qr_additional_info }}</div>
                </div>
                @endif
            </div>
            <div class="clear"></div>
        </div>
    </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        <div style="text-align: center;">
            Condizioni di pagamento: 30 giorni netti - 
            @if($impostazioni->qr_billing_info)
                {{ $impostazioni->qr_billing_info }}
            @else
                Grazie per la fiducia accordataci
            @endif
        </div>
    </div>
</body>
</html>
