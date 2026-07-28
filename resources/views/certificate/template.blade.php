<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Sertifikat Kehadiran</title>
    <style>
        @page {
            size: a4 landscape;
            margin: 0;
        }
        html, body {
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
            background-color: #0f172a;
            font-family: Helvetica, Arial, sans-serif;
            color: #f1f5f9;
        }
        /* Wrapper Utama pembatas halaman */
        .wrapper {
            position: relative;
            width: 297mm;
            height: 210mm;
            padding: 10mm;
            box-sizing: border-box;
        }
        /* Bingkai Luar */
        .border-outer {
            width: 100%;
            height: 100%;
            border: 3px solid #6366f1;
            padding: 2mm;
            box-sizing: border-box;
        }
        /* Bingkai Dalam / Background */
        .border-inner {
            width: 100%;
            height: 100%;
            border: 1px solid #334155;
            background-color: #1e293b;
            padding: 15mm 20mm;
            box-sizing: border-box;
            text-align: center;
        }
        .header-title {
            font-size: 14pt;
            font-weight: bold;
            color: #818cf8;
            letter-spacing: 6px;
            text-transform: uppercase;
            margin-bottom: 10px;
        }
        .certify-text {
            font-size: 10pt;
            color: #94a3b8;
            font-style: italic;
            margin-bottom: 20px;
        }
        .attendee-name {
            font-size: 32pt;
            font-weight: bold;
            color: #ffffff;
            margin-bottom: 20px;
            text-decoration: underline;
        }
        .attended-text {
            font-size: 10pt;
            color: #94a3b8;
            margin-bottom: 10px;
        }
        .event-title {
            font-size: 18pt;
            font-weight: bold;
            color: #818cf8;
            font-style: italic;
            margin-bottom: 10px;
        }
        .organizer-text {
            font-size: 10pt;
            color: #94a3b8;
            margin-bottom: 25px;
        }
        .organizer-name {
            color: #ffffff;
            font-weight: bold;
        }
        /* Bagian Bawah */
        .bottom-container {
            width: 100%;
            margin-top: 15px;
        }
        .bottom-container table {
            width: 100%;
            border-collapse: collapse;
        }
        .bottom-container td {
            vertical-align: bottom;
            padding: 0 10px;
        }
        .lbl {
            font-size: 7pt;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            font-weight: bold;
            margin-bottom: 2px;
        }
        .val {
            font-size: 9.5pt;
            color: #f1f5f9;
            font-weight: bold;
        }
        .val-mono {
            font-size: 7pt;
            color: #94a3b8;
            font-family: monospace;
        }
        .qr-box {
            background-color: #ffffff;
            padding: 4px;
            border-radius: 4px;
            display: inline-block;
            margin-bottom: 4px;
        }
        .qr-img {
            width: 48px;
            height: 48px;
        }
        .badge {
            display: inline-block;
            padding: 5px 12px;
            border: 2px solid #6366f1;
            border-radius: 6px;
            color: #a5b4fc;
            font-size: 8pt;
            font-weight: bold;
        }
        .badge-lbl {
            font-size: 6.5pt;
            color: #64748b;
            font-style: italic;
            margin-bottom: 4px;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="border-outer">
            <div class="border-inner">

                <div class="header-title">Certificate of Attendance</div>

                <div class="certify-text">This certificate is proudly presented to</div>

                <div class="attendee-name">{{ $attendee_name }}</div>

                <div class="attended-text">has successfully completed & attended the event</div>

                <div class="event-title">"{{ $event_title }}"</div>

                <div class="organizer-text">organized by <span class="organizer-name">{{ $organizer_name ?? 'AmikomEventHub Organizer' }}</span></div>

                <!-- Detail Row Table -->
                <div class="bottom-container">
                    <table>
                        <tr>
                            <td style="width: 25%; text-align: left;">
                                <div class="qr-box">
                                    <img class="qr-img" src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data={{ urlencode($ticket_code) }}" alt="QR">
                                </div>
                                <div class="val-mono">{{ $ticket_code }}</div>
                                <div class="lbl" style="margin-top: 2px;">Scan to Verify</div>
                            </td>
                            <td style="width: 25%; text-align: center;">
                                <div class="lbl">Location</div>
                                <div class="val">{{ $event_location ?? 'Yogyakarta' }}</div>
                            </td>
                            <td style="width: 25%; text-align: center; border-left: 1px solid #334155;">
                                <div class="lbl">Issued Date</div>
                                <div class="val">
                                    @if($event_date instanceof \Carbon\Carbon)
                                        {{ $event_date->format('F d, Y') }}
                                    @else
                                        {{ \Carbon\Carbon::parse($event_date)->format('F d, Y') }}
                                    @endif
                                </div>
                            </td>
                            <td style="width: 25%; text-align: right;">
                                <div class="badge-lbl">Verified Security System</div>
                                <div class="badge">AmikomEventHub</div>
                            </td>
                        </tr>
                    </table>
                </div>

            </div>
        </div>
    </div>
</body>
</html>
