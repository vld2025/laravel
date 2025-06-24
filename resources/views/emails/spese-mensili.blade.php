<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Spese Mensili VLD Service</title>
</head>
<body style="font-family: Arial, sans-serif; margin: 20px; color: #333;">
    <h2 style="color: #007bff;">VLD Service GmbH</h2>
    <h3>Spese Mensili - {{ $meseNome }} {{ $anno }}</h3>
    
    <p>{{ $messaggio }}</p>
    
    <p><strong>Riepilogo PDF allegati:</strong></p>
    <ul>
    @foreach($pdfGenerati as $pdf)
        <li>{{ $pdf['utente'] }} - {{ $pdf['spese_count'] }} spese</li>
    @endforeach
    </ul>
    
    <p>Cordiali saluti,<br>
    <strong>VLD Service GmbH</strong></p>
</body>
</html>
