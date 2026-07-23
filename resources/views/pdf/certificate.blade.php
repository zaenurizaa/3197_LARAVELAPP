<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: 'Georgia', serif; text-align: center; padding: 50px; border: 15px double #6f42c1; }
        h1 { font-size: 50px; color: #333; }
        .name { font-size: 40px; font-weight: bold; color: #6f42c1; text-decoration: underline; margin: 30px 0; }
        .event { font-size: 24px; font-style: italic; }
    </style>
</head>
<body>
    <h1>SERTIFIKAT PENGHARGAAAN</h1>
    <p>Diberikan secara resmi kepada:</p>
    <div class="name">{{ $name }}</div>
    <p>Atas partisipasi aktifnya sebagai Peserta dalam acara:</p>
    <div class="event">"{{ $event }}"</div>
    <p style="margin-top: 60px;">Yogyakarta, {{ date('d F Y') }}<br><strong>Direktur AmikomEventHub</strong></p>
</body>
</html>