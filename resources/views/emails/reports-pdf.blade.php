<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Report PDF VLD Service</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <h2 style="color: #2563eb;">VLD Service GmbH</h2>
        <h3>Report PDF - {{ $monthName }} {{ $year }}</h3>
        
        <p>Gentile Cliente,</p>
        
        <p>In allegato trovate i report PDF relativi al mese di <strong>{{ $monthName }} {{ $year }}</strong>.</p>
        
        @if(count($files) > 0)
            <h4>File allegati:</h4>
            <ul>
                @foreach($files as $file)
                    <li>{{ $file['file_name'] ?? 'Report PDF' }} ({{ $file['user_name'] ?? 'Utente' }})</li>
                @endforeach
            </ul>
        @endif
        
        <p>Cordiali saluti,<br>
        <strong>VLD Service GmbH</strong></p>
        
        <hr style="margin: 20px 0; border: none; border-top: 1px solid #ddd;">
        <p style="font-size: 12px; color: #666;">
            Questa email è stata generata automaticamente dal sistema di gestione VLD Service.
        </p>
    </div>
</body>
</html>
