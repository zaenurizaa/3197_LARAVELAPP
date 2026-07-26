<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Check-in Scanner — Penjaga Pintu AmikomEventHub</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #0f172a; }
        #reader video { object-fit: cover !important; border-radius: 1.5rem; }
    </style>
</head>
<body class="text-slate-100 min-h-screen flex flex-col justify-between p-4">

    <!-- Header -->
    <header class="flex items-center justify-between bg-slate-800/80 backdrop-blur-md px-6 py-4 rounded-2xl border border-slate-700 shadow-xl">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-indigo-600 rounded-xl flex items-center justify-center font-black text-xl text-white shadow-lg shadow-indigo-500/30">
                ⚡
            </div>
            <div>
                <h1 class="font-extrabold text-lg text-white leading-tight">Penjaga Pintu</h1>
                <p class="text-xs text-indigo-400 font-semibold">Check-in Scanner AmikomEventHub</p>
            </div>
        </div>
        @php
            if (request()->routeIs('admin.*')) {
                $dashboardRoute = route('admin.dashboard');
            } elseif (request()->routeIs('organizer.*')) {
                $dashboardRoute = route('organizer.dashboard');
            } else {
                $user = Auth::guard('admin')->user() ?? Auth::guard('organizer')->user() ?? auth()->user();
                if (Auth::guard('admin')->check() || ($user && $user->isSuperAdmin())) {
                    $dashboardRoute = route('admin.dashboard');
                } elseif (Auth::guard('organizer')->check() || ($user && $user->isOrganizer())) {
                    $dashboardRoute = route('organizer.dashboard');
                } else {
                    $dashboardRoute = url('/');
                }
            }
        @endphp
        <a href="{{ $dashboardRoute }}" class="px-4 py-2 bg-slate-700 hover:bg-slate-600 rounded-xl text-xs font-bold text-slate-200 transition">
            Dashboard
        </a>
    </header>

    <!-- Main Scanner Area -->
    <main class="my-6 max-w-lg mx-auto w-full flex flex-col gap-6">

        <!-- Camera Container -->
        <div class="relative bg-slate-900 border-2 border-indigo-500/30 rounded-3xl p-4 shadow-2xl overflow-hidden">
            <div id="reader" class="w-full rounded-2xl overflow-hidden min-h-[300px] bg-black"></div>
            
            <div class="mt-4 flex justify-between items-center px-2">
                <span class="text-xs text-slate-400 font-medium flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span> Kamera Aktif
                </span>
                <button id="switch-cam-btn" onclick="toggleCamera()" class="text-xs bg-slate-800 border border-slate-700 px-3 py-1.5 rounded-lg text-slate-300 hover:text-white transition font-semibold">
                    🔄 Ganti Kamera
                </button>
            </div>
        </div>

        <!-- Result Modal / Status Box -->
        <div id="result-box" class="hidden p-6 rounded-3xl border text-center transition-all duration-300 shadow-2xl">
            <div id="result-icon" class="text-5xl mb-3"></div>
            <h2 id="result-title" class="text-2xl font-black mb-1"></h2>
            <p id="result-msg" class="text-sm font-medium opacity-90 mb-4"></p>
            
            <div id="result-details" class="bg-black/20 p-4 rounded-2xl text-left text-xs space-y-2 hidden">
                <div class="flex justify-between"><span class="opacity-60">Nama Pemegang:</span> <span id="det-name" class="font-bold"></span></div>
                <div class="flex justify-between"><span class="opacity-60">Event:</span> <span id="det-event" class="font-bold"></span></div>
                <div class="flex justify-between"><span class="opacity-60">Order ID:</span> <span id="det-order" class="font-mono font-bold"></span></div>
                <div id="det-time-row" class="flex justify-between hidden"><span class="opacity-60">Waktu Scan:</span> <span id="det-time" class="font-bold"></span></div>
            </div>

            <button onclick="resetScanner()" class="mt-5 w-full py-3 bg-white/20 hover:bg-white/30 rounded-xl font-bold text-sm transition">
                Scan Tiket Berikutnya 🚀
            </button>
        </div>

        <!-- Manual Input Code Form -->
        <div class="bg-slate-800/60 border border-slate-700/60 rounded-3xl p-5 shadow-lg backdrop-blur-md">
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">Input Manual Order ID / Kode QR</label>
            <form id="manual-form" onsubmit="handleManualSubmit(event)" class="flex gap-2">
                <input type="text" id="manual-code" placeholder="Contoh: TRX-172198-ABCDE"
                    class="flex-1 bg-slate-900 border border-slate-700 rounded-xl px-4 py-3 text-sm text-white font-mono placeholder:text-slate-600 focus:outline-none focus:border-indigo-500">
                <button type="submit" class="px-5 py-3 bg-indigo-600 hover:bg-indigo-500 font-bold text-sm text-white rounded-xl shadow-lg transition">
                    Cek
                </button>
            </form>
        </div>

    </main>

    <!-- Footer -->
    <footer class="text-center text-xs text-slate-500 font-medium py-2">
        AmikomEventHub &copy; {{ date('Y') }} — Anti-Double Entry Security System
    </footer>

    <script>
        let html5QrcodeScanner = null;
        let isProcessing = false;

        function onScanSuccess(decodedText, decodedResult) {
            if (isProcessing) return;
            isProcessing = true;
            processCheckin(decodedText);
        }

        function processCheckin(code) {
            // Visual loading state
            const resBox = document.getElementById('result-box');
            resBox.className = "p-6 rounded-3xl border border-indigo-500/50 bg-indigo-950/80 text-white text-center shadow-2xl block animate-pulse";
            document.getElementById('result-icon').innerText = "⏳";
            document.getElementById('result-title').innerText = "Memverifikasi Tiket...";
            document.getElementById('result-msg').innerText = "Mohon tunggu sebentar...";
            document.getElementById('result-details').classList.add('hidden');

            fetch("{{ route('checkin.verify') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({ code: code })
            })
            .then(res => res.json().then(data => ({ status: res.status, body: data })))
            .then(res => {
                const data = res.body;
                resBox.classList.remove('animate-pulse', 'hidden');

                if (res.status === 200) {
                    // SUCCESS
                    resBox.className = "p-6 rounded-3xl border-2 border-emerald-500 bg-emerald-950/90 text-emerald-100 text-center shadow-2xl block";
                    document.getElementById('result-icon').innerText = "✅";
                    document.getElementById('result-title').innerText = "CHECK-IN BERHASIL!";
                    document.getElementById('result-msg').innerText = data.message;
                    
                    document.getElementById('det-name').innerText = data.customer_name || '-';
                    document.getElementById('det-event').innerText = data.event_title || '-';
                    document.getElementById('det-order').innerText = data.order_id || '-';
                    document.getElementById('det-time').innerText = data.checkin_time || '-';
                    document.getElementById('det-time-row').classList.remove('hidden');
                    document.getElementById('result-details').classList.remove('hidden');

                } else if (res.status === 409) {
                    // DOUBLE ENTRY DETECTED!
                    resBox.className = "p-6 rounded-3xl border-2 border-rose-500 bg-rose-950/90 text-rose-100 text-center shadow-2xl block";
                    document.getElementById('result-icon').innerText = "🚨";
                    document.getElementById('result-title').innerText = "TIKET SUDAH DIPAKAI!";
                    document.getElementById('result-msg').innerText = data.message;

                    document.getElementById('det-name').innerText = data.customer_name || '-';
                    document.getElementById('det-event').innerText = data.event_title || '-';
                    document.getElementById('det-order').innerText = data.order_id || '-';
                    document.getElementById('det-time').innerText = data.checked_in_at || '-';
                    document.getElementById('det-time-row').classList.remove('hidden');
                    document.getElementById('result-details').classList.remove('hidden');

                } else {
                    // NOT FOUND / UNPAID
                    resBox.className = "p-6 rounded-3xl border-2 border-amber-500 bg-amber-950/90 text-amber-100 text-center shadow-2xl block";
                    document.getElementById('result-icon').innerText = "⚠️";
                    document.getElementById('result-title').innerText = "TIKET INVALID / UNPAID";
                    document.getElementById('result-msg').innerText = data.message;

                    if (data.customer_name) {
                        document.getElementById('det-name').innerText = data.customer_name || '-';
                        document.getElementById('det-event').innerText = '-';
                        document.getElementById('det-order').innerText = data.order_id || '-';
                        document.getElementById('det-time-row').classList.add('hidden');
                        document.getElementById('result-details').classList.remove('hidden');
                    }
                }
            })
            .catch(err => {
                resBox.className = "p-6 rounded-3xl border border-rose-500 bg-rose-900 text-white text-center shadow-2xl block";
                document.getElementById('result-icon').innerText = "❌";
                document.getElementById('result-title').innerText = "Gagal Menghubungi Server";
                document.getElementById('result-msg').innerText = "Koneksi internet bermasalah. Coba lagi.";
            });
        }

        function handleManualSubmit(e) {
            e.preventDefault();
            const input = document.getElementById('manual-code').value.trim();
            if (input) {
                isProcessing = true;
                processCheckin(input);
            }
        }

        function resetScanner() {
            document.getElementById('result-box').classList.add('hidden');
            document.getElementById('manual-code').value = '';
            isProcessing = false;
        }

        let currentFacingMode = "environment";

        function startScanner() {
            html5QrcodeScanner = new Html5Qrcode("reader");
            html5QrcodeScanner.start(
                { facingMode: currentFacingMode },
                { fps: 10, qrbox: { width: 250, height: 250 } },
                onScanSuccess,
                (errorMessage) => {}
            ).catch(err => {
                console.error("Camera access error:", err);
            });
        }

        function toggleCamera() {
            if (html5QrcodeScanner) {
                html5QrcodeScanner.stop().then(() => {
                    currentFacingMode = currentFacingMode === "environment" ? "user" : "environment";
                    startScanner();
                });
            }
        }

        document.addEventListener("DOMContentLoaded", function() {
            startScanner();
        });
    </script>
</body>
</html>
