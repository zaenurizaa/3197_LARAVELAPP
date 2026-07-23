@extends('layout.app')

@section('content')

@if(isset($transaction) && $transaction && $transaction->event)
    {{-- TAMPILAN CETAK E-TIKET (PADUAN BIRU PREMUM) --}}
    <div class="print-container py-10 bg-blue-600 min-h-[85vh] text-slate-800 flex flex-col items-center justify-center px-4">
        <div class="max-w-md w-full my-auto">
            
            <!-- Tombol Navigasi -->
            <div class="mb-5 no-print flex items-center justify-between">
                <a href="{{ route('ticket') }}" class="inline-flex items-center gap-2 text-xs font-bold bg-white/20 hover:bg-white/30 text-white px-4 py-2.5 rounded-xl transition backdrop-blur-md">
                    &larr; Kembali
                </a>
                <button onclick="window.print()" class="inline-flex items-center gap-2 text-xs font-bold bg-white text-blue-600 hover:bg-blue-50 px-5 py-2.5 rounded-xl transition shadow-lg">
                    🖨️ Cetak / Simpan PDF
                </button>
            </div>

            <!-- Kartu Tiket -->
            <div class="ticket-card bg-white rounded-4xl overflow-hidden shadow-2xl border border-white/20">
                <div class="p-6 bg-blue-50/80 border-b-2 border-dashed border-blue-100 text-center relative">
                    <span class="inline-block bg-blue-600 text-white text-[10px] font-extrabold uppercase tracking-widest px-3 py-1 rounded-full mb-2">
                        E-Ticket Resmi
                    </span>
                    <h2 class="text-2xl font-black text-slate-800 leading-tight">{{ $transaction->event->title }}</h2>
                </div>

                <div class="p-6 space-y-6">
                    <div class="grid grid-cols-2 gap-4 text-left">
                        <div>
                            <p class="text-slate-400 text-[10px] font-bold uppercase tracking-wider mb-0.5">Nama Pembeli</p>
                            <p class="font-bold text-xs text-slate-800">{{ $transaction->customer_name ?? Auth::user()?->name }}</p>
                        </div>
                        <div>
                            <p class="text-slate-400 text-[10px] font-bold uppercase tracking-wider mb-0.5">Tanggal & Waktu</p>
                            <p class="font-bold text-xs text-slate-800">
                                {{ \Carbon\Carbon::parse($transaction->event->date)->format('d M Y, H:i') }}
                            </p>
                        </div>
                        <div>
                            <p class="text-slate-400 text-[10px] font-bold uppercase tracking-wider mb-0.5">Order ID</p>
                            <p class="font-mono font-bold text-blue-600 text-xs">{{ $transaction->order_id }}</p>
                        </div>
                        <div>
                            <p class="text-slate-400 text-[10px] font-bold uppercase tracking-wider mb-0.5">Lokasi</p>
                            <p class="font-bold text-xs text-slate-800 leading-snug">{{ $transaction->event->location }}</p>
                        </div>
                    </div>

                    <div class="bg-slate-50 p-5 rounded-2xl flex flex-col items-center border border-slate-100 text-center">
                        <p class="text-slate-400 text-[10px] font-bold uppercase tracking-wider mb-3">Scan QR Untuk Check-in</p>
                        <div class="bg-white p-3 rounded-2xl shadow-sm border border-slate-200 inline-block">
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=160x160&data={{ urlencode($transaction->order_id) }}" alt="QR Code" class="w-32 h-32">
                        </div>
                        <p class="mt-3 font-mono font-black text-slate-800 uppercase tracking-widest text-xs">
                            STATUS: <span class="text-emerald-600">{{ $transaction->status ?? 'SUCCESS' }}</span>
                        </p>
                    </div>
                </div>

                <div class="p-4 bg-slate-50 border-t border-slate-100 text-center text-[10px] text-slate-400 font-medium">
                    Mohon tunjukkan E-Ticket ini saat memasuki area acara.
                </div>
            </div>

        </div>
    </div>

@else
    {{-- TAMPILAN LIST TIKET (TEMA BIRU) --}}
    <div class="py-10 bg-slate-50 min-h-screen">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Tiket & Transaksi Saya</h1>
                    <p class="text-slate-500 text-xs mt-1">Kelola tiket event aktif milikmu dan pantau status pembayarannya.</p>
                </div>

                <form action="{{ route('ticket') }}" method="GET" class="flex gap-2 w-full md:w-auto">
                    <input type="text" name="order_id" placeholder="Cari Order ID..." 
                           value="{{ request('order_id') }}"
                           class="w-full md:w-64 px-4 py-2 rounded-xl border border-slate-200 text-slate-800 font-medium text-xs focus:outline-none focus:border-blue-600 bg-white shadow-sm" required>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-xl font-bold text-xs transition shadow-sm shrink-0">
                        Cari
                    </button>
                </form>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6">
                
                <!-- Tab Menu Biru -->
                <div class="flex border-b border-slate-200 mb-6 gap-6">
                    <button onclick="switchTab('aktif')" id="tab-btn-aktif"
                        class="tab-btn pb-3 text-xs font-bold border-b-2 border-blue-600 text-blue-600 transition">
                        🎟️ Event Aktif
                    </button>
                    <button onclick="switchTab('lalu')" id="tab-btn-lalu"
                        class="tab-btn pb-3 text-xs font-bold border-b-2 border-transparent text-slate-400 hover:text-slate-600 transition">
                        🕒 Event Lalu
                    </button>
                    <button onclick="switchTab('transaksi')" id="tab-btn-transaksi"
                        class="tab-btn pb-3 text-xs font-bold border-b-2 border-transparent text-slate-400 hover:text-slate-600 transition">
                        💳 Transaksi
                    </button>
                </div>

                <!-- Content Event Aktif -->
                <div id="tab-content-aktif" class="tab-content space-y-3">
                    @php
                        $successTransactions = $transactions->whereIn('status', ['success', 'settlement', 'Success', 'paid']);
                    @endphp

                    @forelse($successTransactions as $item)
                        <div class="p-4 border border-slate-200/80 rounded-xl hover:border-blue-300 transition flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white shadow-sm">
                            <div>
                                <span class="text-[10px] font-bold uppercase tracking-wider text-blue-600 bg-blue-50 px-2.5 py-1 rounded-md">
                                    #{{ $item->order_id }}
                                </span>
                                <h3 class="text-sm font-bold text-slate-800 mt-2">{{ $item->event->title ?? 'Event Tidak Ditemukan' }}</h3>
                                <p class="text-xs text-slate-400 mt-1">
                                    📅 {{ \Carbon\Carbon::parse($item->event->date ?? now())->format('d M Y, H:i') }} | 📍 {{ $item->event->location ?? 'Amikom' }}
                                </p>
                            </div>
                            <div>
                                <a href="{{ route('ticket', ['order_id' => $item->order_id]) }}"
                                    class="inline-block px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl transition shadow-sm">
                                    Cetak E-Ticket
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="py-12 text-center">
                            <p class="text-slate-600 text-xs font-bold">Belum ada tiket aktif saat ini.</p>
                        </div>
                    @endforelse
                </div>

                <!-- Content Event Lalu -->
                <div id="tab-content-lalu" class="tab-content hidden py-12 text-center">
                    <p class="text-slate-400 text-xs font-medium">Belum ada riwayat event yang berlalu.</p>
                </div>

                <!-- Content Transaksi -->
                <div id="tab-content-transaksi" class="tab-content hidden space-y-3">
                    @forelse($transactions as $trx)
                        <div class="p-4 border border-slate-100 bg-slate-50/50 rounded-xl flex items-center justify-between">
                            <div>
                                <p class="text-[10px] font-bold text-slate-400">#{{ $trx->order_id }}</p>
                                <h4 class="font-bold text-slate-800 text-xs mt-0.5">{{ $trx->event->title ?? 'Tiket Event' }}</h4>
                            </div>
                            <span class="px-3 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-700">
                                {{ ucfirst($trx->status) }}
                            </span>
                        </div>
                    @empty
                        <div class="py-12 text-center">
                            <p class="text-slate-400 text-xs font-medium">Belum ada transaksi.</p>
                        </div>
                    @endforelse
                </div>

            </div>
        </div>
    </div>

    <script>
        function switchTab(tabName) {
            document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.classList.remove('border-blue-600', 'text-blue-600');
                btn.classList.add('border-transparent', 'text-slate-400');
            });

            document.getElementById('tab-content-' + tabName)?.classList.remove('hidden');
            const targetBtn = document.getElementById('tab-btn-' + tabName);
            if(targetBtn) {
                targetBtn.classList.remove('border-transparent', 'text-slate-400');
                targetBtn.classList.add('border-blue-600', 'text-blue-600');
            }
        }
    </script>
@endif

@endsection