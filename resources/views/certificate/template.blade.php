<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <style>
        @page {
            size: 297mm 210mm;
            margin: 0;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: Helvetica, Arial, sans-serif;
            background-color: #0f172a;
            color: #e2e8f0;
            width: 297mm;
            height: 210mm;
            overflow: hidden;
        }
        .page {
            width: 297mm;
            height: 210mm;
            padding: 8mm; /* Dikurangi sedikit */
        }
        .frame {
            width: 281mm;
            height: 194mm;
            border: 3px solid #6366f1;
            padding: 2mm;
        }
        .inner {
            width: 100%;
            height: 100%;
            border: 1px solid #334155;
            background-color: #1e293b;
            text-align: center;
            padding: 18mm 30mm 10mm 30mm; /* Padding dalam diturunkan agar menghemat space vertikal */
        }
        .header-text {
            font-size: 11pt;
            font-weight: bold;
            color: #818cf8;
            letter-spacing: 5px;
            text-transform: uppercase;
            text-decoration: underline;
        }
        .proudly-text {
            font-size: 9pt;
            color: #94a3b8;
            font-style: italic;
            margin-top: 12px;
        }
        .attendee-name {
            font-size: 26pt; /* Font name diturunkan agar stabil */
            font-weight: bold;
            color: #ffffff;
            margin-top: 10px;
            line-height: 1.2;
        }
        .has-completed {
            font-size: 9pt;
            color: #94a3b8;
            margin-top: 10px;
        }
        .event-name {
            font-size: 14pt;
            font-weight: bold;
            color: #818cf8;
            font-style: italic;
            margin-top: 6px;
        }
        .organized-by {
            font-size: 9pt;
            color: #94a3b8;
            margin-top: 4px;
        }
        .organizer-val {
            font-weight: bold;
            color: #e2e8f0;
        }
        .bottom-bar {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px; /* Margin diperkecil agar tidak meluber */
        }
        .bottom-bar td {
            vertical-align: bottom;
            padding: 2px 6px;
        }
        .lbl {
            font-size: 6.5pt;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            font-weight: bold;
        }
        .val {
            font-size: 9pt;
            color: #f1f5f9;
            font-weight: bold;
            margin-top: 2px;
        }
        .val-mono {
            font-size: 6.5pt;
            color: #94a3b8;
            font-family: 'Courier New', Courier, monospace;
            margin-top: 2px;
        }
        .qr-box {
            background-color: #ffffff;
            padding: 4px;
            border-radius: 4px;
            display: inline-block;
        }
        .qr-img {
            width: 48px;
            height: 48px;
        }
        .badge {
            display: inline-block;
            padding: 5px 12px;
            border: 2px solid #6366f1;
            border-radius: 8px;
            color: #a5b4fc;
            font-size: 8pt;
            font-weight: bold;
            letter-spacing: 1px;
        }
        .badge-label {
            font-size: 6.5pt;
            color: #64748b;
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="page">
        <div class="frame">
            <div class="inner">

                <div class="header-text">Certificate of Attendance</div>

                <div class="proudly-text">This is to proudly certify that</div>

                <div class="attendee-name">{{ $attendee_name }}</div>

                <div class="has-completed">has successfully attended the event</div>

                <div class="event-name">{{ $event_title }}</div>

                <div class="organized-by">organized by <span class="organizer-val">{{ $organizer_name ?? 'AmikomEventHub' }}</span></div>

                <!-- Bottom details row -->
                <table class="bottom-bar">
                    <tr>
                        <td style="width: 22%; text-align: left;">
                            <div class="qr-box">
                                <img class="qr-img" src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data={{ urlencode($ticket_code) }}" alt="QR">
                            </div>
                            <div class="val-mono">{{ $ticket_code }}</div>
                            <div class="lbl">Scan to verify</div>
                        </td>
                        <td style="width: 22%; text-align: center;">
                            <div class="lbl">Lokasi</div>
                            <div class="val">{{ $event_location ?? '-' }}</div>
                        </td>
                        <td style="width: 26%; text-align: center; border-left: 1px solid #334155;">
                            <div class="lbl">Completed On</div>
                            <div class="val">
                                @if($event_date instanceof \Carbon\Carbon)
                                    {{ $event_date->format('F d, Y') }}
                                @else
                                    {{ \Carbon\Carbon::parse($event_date)->format('F d, Y') }}
                                @endif
                            </div>
                        </td>
                        <td style="width: 30%; text-align: right;">
                            <div class="badge-label">Verified Attendance Platform</div>
                            <div class="badge">AmikomEventHub</div>
                        </td>
                    </tr>
                </table>

            </div>
        </div>
    </div>
</body>
</html>
