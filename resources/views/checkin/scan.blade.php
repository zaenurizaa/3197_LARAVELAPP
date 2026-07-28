<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Check-in Scanner — Penjaga Pintu AmikomEventHub</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #0b0f19; }
        #reader video { object-fit: cover !important; border-radius: 2rem; }
        .success-flash { animation: successFlashAnim 0.8s ease-out; }
        .error-flash { animation: errorFlashAnim 0.8s ease-out; }
        @keyframes successFlashAnim {
            0% { background-color: rgba(16, 185, 129, 0.4); }
            100% { background-color: #0b0f19; }
        }
        @keyframes errorFlashAnim {
            0% { background-color: rgba(239, 68, 68, 0.4); }
            100% { background-color: #0b0f19; }
        }
    </style>
</head>
<body id="scanner-body" class="text-slate-100 min-h-screen flex flex-col justify-between p-4 transition-colors duration-300">

    <!-- Header & Status Koneksi -->
    <header class="flex items-center justify-between bg-slate-900/90 border border-slate-800/80 px-5 py-3.5 rounded-2xl shadow-xl">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 bg-indigo-600 rounded-xl flex items-center justify-center font-bold text-white shadow-lg">
                ⚡
            </div>
            <div>
                <h1 class="font-extrabold text-sm text-white leading-tight">Panitia Scanner</h1>
                <p class="text-[10px] text-indigo-400 font-bold uppercase tracking-wider">Gate Check-In System</p>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <!-- Indikator Jaringan -->
            <span id="network-status" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-[10px] font-bold">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-ping"></span> ONLINE
            </span>

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
            <a href="{{ $dashboardRoute }}" class="px-3 py-1.5 bg-slate-800 hover:bg-slate-700 rounded-xl text-xs font-bold text-slate-200 transition">
                Kembali
            </a>
        </div>
    </header>

    <!-- Main Scanner Area -->
    <main class="my-5 max-w-lg mx-auto w-full flex flex-col gap-5">

        <!-- Stat Counter Ringkas -->
        <div class="grid grid-cols-2 gap-3 bg-slate-900/60 p-3 rounded-2xl border border-slate-800/80 text-center">
            <div>
                <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wide">Total Terverifikasi</span>
                <p id="counter-success" class="text-xl font-black text-emerald-400 mt-0.5">0</p>
            </div>
            <div>
                <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wide">Sesi Scan Saya</span>
                <p id="counter-total" class="text-xl font-black text-indigo-400 mt-0.5">0</p>
            </div>
        </div>

        <!-- Camera Container -->
        <div class="relative bg-slate-950 border border-slate-800 rounded-[2.5rem] p-3 shadow-2xl overflow-hidden">
            <div id="reader" class="w-full rounded-[2rem] overflow-hidden min-h-[300px] bg-black"></div>
            
            <div class="mt-3 flex justify-between items-center px-2">
                <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-indigo-500 animate-ping"></span> Continuous Scan Mode
                </span>
                <button id="switch-cam-btn" onclick="toggleCamera()" class="text-[10px] bg-slate-900 border border-slate-800 px-3 py-1.5 rounded-xl text-slate-300 hover:text-white transition font-bold uppercase tracking-wider">
                    Ganti Kamera
                </button>
            </div>
        </div>

        <!-- Result Modal / Status Box -->
        <div id="result-box" class="hidden p-6 rounded-3xl border text-center transition-all duration-300 shadow-2xl">
            <div id="result-icon" class="text-5xl mb-3"></div>
            <h2 id="result-title" class="text-xl font-black mb-1 tracking-tight"></h2>
            <p id="result-msg" class="text-xs font-semibold opacity-90 mb-4"></p>
            
            <div id="result-details" class="bg-black/30 p-4 rounded-2xl text-left text-xs space-y-2.5 hidden">
                <div class="flex justify-between border-b border-white/5 pb-1.5"><span class="opacity-60 font-semibold">Nama Pemegang:</span> <span id="det-name" class="font-bold"></span></div>
                <div class="flex justify-between border-b border-white/5 pb-1.5"><span class="opacity-60 font-semibold">Event:</span> <span id="det-event" class="font-bold"></span></div>
                <div class="flex justify-between border-b border-white/5 pb-1.5"><span class="opacity-60 font-semibold">Order ID:</span> <span id="det-order" class="font-mono font-bold text-indigo-300"></span></div>
                <div id="det-time-row" class="flex justify-between hidden"><span class="opacity-60 font-semibold">Waktu Scan:</span> <span id="det-time" class="font-bold"></span></div>
            </div>

            <button onclick="resetScanner()" class="mt-5 w-full py-3.5 bg-white/10 hover:bg-white/20 rounded-2xl font-bold text-xs uppercase tracking-wider transition">
                Lanjut Pindai 🚀
            </button>
        </div>

        <!-- Manual Input Code Form -->
        <div class="bg-slate-900/60 border border-slate-800/80 rounded-3xl p-4 shadow-lg">
            <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-2">Input Manual Order ID</label>
            <form id="manual-form" onsubmit="handleManualSubmit(event)" class="flex gap-2">
                <input type="text" id="manual-code" placeholder="Contoh: FREE-1785083390"
                    class="flex-1 bg-slate-950 border border-slate-800 rounded-xl px-4 py-2.5 text-xs text-white font-mono placeholder:text-slate-700 focus:outline-none focus:border-indigo-600">
                <button type="submit" class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-500 font-bold text-xs uppercase tracking-wider text-white rounded-xl shadow-lg transition">
                    Verifikasi
                </button>
            </form>
        </div>

        <!-- Help Section (Heuristic: Help & Documentation) -->
        <div class="bg-slate-900/40 border border-slate-800/40 rounded-3xl p-4 text-[11px] text-slate-400 space-y-2">
            <p class="font-bold text-slate-300 uppercase tracking-wider flex items-center gap-1">
                <span>💡</span> Panduan Penggunaan & Troubleshooting:
            </p>
            <ul class="list-disc list-inside space-y-1 font-medium">
                <li>Pastikan QR Code berada di tengah-tengah kotak bidik kamera.</li>
                <li>Jika layar berwarna merah, tiket sudah pernah dipakai masuk (*Double Entry*).</li>
                <li>Aktifkan volume suara HP Anda untuk mendengar sinyal verifikasi.</li>
                <li>Jika kamera blank, gunakan tombol <strong>Ganti Kamera</strong> di atas.</li>
            </ul>
        </div>

    </main>

    <!-- Footer -->
    <footer class="text-center text-[10px] text-slate-600 font-medium py-2">
        AmikomEventHub Secure Scanner Engine — v2.1.0
    </footer>

    <script>
        let html5QrcodeScanner = null;
        let isProcessing = false;
        let successCount = 0;
        let totalCount = 0;

        // Mendeteksi Perubahan Status Koneksi Internet
        window.addEventListener('online', updateNetworkStatus);
        window.addEventListener('offline', updateNetworkStatus);

        function updateNetworkStatus() {
            const statusIndicator = document.getElementById('network-status');
            if (navigator.onLine) {
                statusIndicator.className = "inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-[10px] font-bold";
                statusIndicator.innerHTML = '<span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-ping"></span> ONLINE';
            } else {
                statusIndicator.className = "inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-rose-500/10 border border-rose-500/20 text-rose-400 text-[10px] font-bold";
                statusIndicator.innerHTML = '<span class="w-1.5 h-1.5 rounded-full bg-rose-400 animate-pulse"></span> OFFLINE';
            }
        }

        // Penghasil Suara Beep dengan Web Audio API
        function playBeep(type) {
            try {
                const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
                
                if (type === 'success') {
                    // Bunyi beep pendek nada tinggi (Sukses)
                    const osc = audioCtx.createOscillator();
                    const gain = audioCtx.createGain();
                    
                    osc.connect(gain);
                    gain.connect(audioCtx.destination);
                    
                    osc.type = 'sine';
                    osc.frequency.value = 1000; // Frekuensi 1000Hz
                    gain.gain.setValueAtTime(0.3, audioCtx.currentTime);
                    gain.gain.exponentialRampToValueAtTime(0.01, audioCtx.currentTime + 0.15);
                    
                    osc.start(audioCtx.currentTime);
                    osc.stop(audioCtx.currentTime + 0.15);
                } else {
                    // Bunyi alarm frekuensi rendah berganda (Gagal)
                    const osc1 = audioCtx.createOscillator();
                    const gain1 = audioCtx.createGain();
                    
                    osc1.connect(gain1);
                    gain1.connect(audioCtx.destination);
                    
                    osc1.type = 'sawtooth';
                    osc1.frequency.value = 220; // Nada rendah
                    gain1.gain.setValueAtTime(0.4, audioCtx.currentTime);
                    gain1.gain.exponentialRampToValueAtTime(0.01, audioCtx.currentTime + 0.3);
                    
                    osc1.start(audioCtx.currentTime);
                    osc1.stop(audioCtx.currentTime + 0.3);
                }
            } catch (e) {
                console.error("Web Audio API not supported/active:", e);
            }
        }

        function onScanSuccess(decodedText, decodedResult) {
            if (isProcessing) return;
            isProcessing = true;
            processCheckin(decodedText);
        }

        function processCheckin(code) {
            const bodyEl = document.getElementById('scanner-body');
            const resBox = document.getElementById('result-box');
            
            // Visual loading state
            resBox.className = "p-6 rounded-3xl border border-indigo-500/50 bg-indigo-950/80 text-white text-center shadow-2xl block animate-pulse";
            document.getElementById('result-icon').innerText = "⏳";
            document.getElementById('result-title').innerText = "Memverifikasi...";
            document.getElementById('result-msg').innerText = "Menghubungi server gate...";
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
                totalCount++;
                document.getElementById('counter-total').innerText = totalCount;

                if (res.status === 200) {
                    // SUCCESS (Check-in Berhasil)
                    successCount++;
                    document.getElementById('counter-success').innerText = successCount;
                    playBeep('success');

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

                    // 🔓 Langsung buka kunci agar kamera siap scan tiket berikutnya tanpa menutup info tiket saat ini
                    isProcessing = false;

                } else if (res.status === 409) {
                    // TIKET SUDAH DIGUNAKAN (Double Entry)
                    playBeep('error');

                    resBox.className = "p-6 rounded-3xl border-2 border-rose-500 bg-rose-950/90 text-rose-100 text-center shadow-2xl block";
                    document.getElementById('result-icon').innerText = "🚨";
                    document.getElementById('result-title').innerText = "TIKET SUDAH DIGUNAKAN!";
                    document.getElementById('result-msg').innerText = "Pemegang tiket ini sudah melakukan check-in sebelumnya.";

                    document.getElementById('det-name').innerText = data.customer_name || '-';
                    document.getElementById('det-event').innerText = data.event_title || '-';
                    document.getElementById('det-order').innerText = data.order_id || '-';
                    document.getElementById('det-time').innerText = data.checked_in_at || '-';
                    document.getElementById('det-time-row').classList.remove('hidden');
                    document.getElementById('result-details').classList.remove('hidden');

                    // 🔓 Buka kunci scanner
                    isProcessing = false;

                } else {
                    // TIKET TIDAK VALID / BELUM LUNAS
                    playBeep('error');

                    resBox.className = "p-6 rounded-3xl border-2 border-amber-500 bg-amber-950/90 text-amber-100 text-center shadow-2xl block";
                    document.getElementById('result-icon').innerText = "⚠️";
                    document.getElementById('result-title').innerText = "TIKET TIDAK VALID";
                    document.getElementById('result-msg').innerText = data.message || "QR Code tidak terdaftar dalam sistem.";

                    if (data.customer_name) {
                        document.getElementById('det-name').innerText = data.customer_name || '-';
                        document.getElementById('det-event').innerText = '-';
                        document.getElementById('det-order').innerText = data.order_id || '-';
                        document.getElementById('det-time-row').classList.add('hidden');
                        document.getElementById('result-details').classList.remove('hidden');
                    } else {
                        document.getElementById('result-details').classList.add('hidden');
                    }

                    // 🔓 Buka kunci scanner
                    isProcessing = false;
                }
            })
            .catch(err => {
                resBox.className = "p-6 rounded-3xl border border-rose-500 bg-rose-950/80 text-rose-200 text-center shadow-2xl block";
                document.getElementById('result-icon').innerText = "❌";
                document.getElementById('result-title').innerText = "Koneksi Terputus";
                document.getElementById('result-msg').innerText = "Gagal memverifikasi tiket karena jaringan terputus. Silakan coba kembali.";
                
                // 🔓 Buka kunci scanner
                isProcessing = false;
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
                { fps: 15, qrbox: { width: 250, height: 250 } },
                onScanSuccess,
                (errorMessage) => {}
            ).catch(err => {
                console.error("Gagal mendapatkan akses kamera:", err);
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
            updateNetworkStatus();
        });
    </script>
</body>
</html>
