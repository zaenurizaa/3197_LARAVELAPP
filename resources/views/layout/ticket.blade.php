@extends('layout.app')

@section('content')

{{-- Style khusus CSS Print agar HANYA tiket yang tercetak rapi di 1 halaman --}}
<style>
    @media print {
        nav, footer, .no-print, header {
            display: none !important;
        }
        body, main {
            background: white !important;
            padding: 0 !important;
            margin: 0 !important;
            min-height: auto !important;
        }
        @page {
            size: A4 portrait;
            margin: 1cm;
        }
        .print-ticket-area {
            box-shadow: none !important;
            border: 1px solid #e2e8f0 !important;
            margin: 0 auto !important;
            max-width: 420px !important;
            page-break-inside: avoid;
        }
    }
</style>

@if(isset($transaction) && $transaction && $transaction->event)
    {{-- TAMPILAN E-TIKET INDIVIDU --}}
    <main class="min-h-[85vh] bg-slate-50 flex items-center justify-center px-6 py-12 relative">
        <div class="max-w-md w-full animate-in fade-in zoom-in duration-500">
            
            <div class="text-center mb-8 no-print">
                <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4 border-4 border-white shadow-sm">
                    <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <h1 class="text-3xl font-black text-slate-800">Pembayaran Berhasil!</h1>
                <p class="text-slate-500 mt-2 text-sm">Tiket Anda telah terbit dan siap digunakan.</p>
            </div>

            <!-- E-Ticket Card -->
            <div class="print-ticket-area bg-white text-slate-900 rounded-[2.5rem] overflow-hidden shadow-2xl relative border border-slate-100">
                <div class="p-8 bg-indigo-50 border-b-4 border-dashed border-white text-center relative">
                    <p class="text-indigo-600 font-bold uppercase tracking-widest text-xs mb-2">E-Ticket Resmi</p>
                    <h2 class="text-2xl font-black leading-tight text-slate-800">
                        {{ $transaction->event->title ?? 'Event Tidak Ditemukan' }}
                    </h2>
                    <div class="absolute -left-4 -bottom-4 w-8 h-8 bg-slate-50 rounded-full shadow-inner border-r border-slate-100"></div>
                    <div class="absolute -right-4 -bottom-4 w-8 h-8 bg-slate-50 rounded-full shadow-inner border-l border-slate-100"></div>
                </div>

                <div class="p-8 space-y-6">
                    <div class="grid grid-cols-2 gap-5 text-left">
                        <div>
                            <p class="text-slate-400 text-[10px] font-bold uppercase mb-1">Nama Pembeli</p>
                            <p class="font-bold text-slate-800 text-xs truncate">
                                {{ $transaction->customer_name ?? Auth::user()?->name }}
                            </p>
                        </div>
                        <div>
                            <p class="text-slate-400 text-[10px] font-bold uppercase mb-1">Tanggal & Waktu</p>
                            <p class="font-bold text-slate-800 text-xs">
                                {{ \Carbon\Carbon::parse($transaction->event->date ?? now())->format('d M Y, H:i') }}
                            </p>
                        </div>
                        <div>
                            <p class="text-slate-400 text-[10px] font-bold uppercase mb-1">Order ID</p>
                            <p class="font-bold text-slate-800 font-mono text-xs">{{ $transaction->order_id }}</p>
                        </div>
                        <div>
                            <p class="text-slate-400 text-[10px] font-bold uppercase mb-1">Lokasi</p>
                            <p class="font-bold text-slate-800 text-xs">
                                {{ $transaction->event->location ?? '-' }}
                            </p>
                        </div>
                        <div>
                            <p class="text-slate-400 text-[10px] font-bold uppercase mb-1">Total Bayar</p>
                            <p class="font-bold text-slate-800 text-xs">
                                Rp {{ number_format($transaction->total_price ?? 0, 0, ',', '.') }}
                            </p>
                        </div>
                        <div>
                            <p class="text-slate-400 text-[10px] font-bold uppercase mb-1">Status</p>
                            <p class="font-bold text-green-600 text-xs uppercase">{{ $transaction->status ?? 'SUCCESS' }}</p>
                        </div>
                    </div>

                    <div class="bg-slate-50 p-5 rounded-3xl flex flex-col items-center border border-slate-100">
                        <p class="text-slate-400 text-[10px] font-bold uppercase mb-3 tracking-widest">Scan QR untuk Check-in</p>
                        <div class="bg-white p-2.5 rounded-2xl shadow-sm border border-slate-200 inline-block">
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=160x160&data={{ urlencode($transaction->order_id) }}" alt="QR Code" class="w-32 h-32">
                        </div>
                        <p class="mt-3 font-mono font-bold text-slate-600 text-xs">#{{ $transaction->order_id }}</p>
                    </div>
                </div>

                <div class="px-8 pb-8 space-y-3 no-print">
                    <button onclick="window.print()" class="w-full py-4 bg-indigo-600 text-white rounded-2xl font-bold shadow-lg shadow-indigo-200 hover:bg-indigo-700 hover:-translate-y-0.5 transition-all text-sm">
                        Cetak / Simpan PDF
                    </button>
                    <a href="{{ route('ticket') }}" class="block text-center py-2 text-slate-400 font-semibold text-sm hover:text-indigo-600 transition">
                        &larr; Kembali ke Daftar Tiket
                    </a>
                </div>
            </div>
        </div>
    </main>

@else
    {{-- TAMPILAN DASHBOARD TIKETKU (LIST TIKETNYA USER) --}}
    <div class="py-10 bg-slate-50 min-h-screen">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-black text-slate-900 tracking-tight">Tiketku</h1>
                    <p class="text-slate-500 text-xs mt-1">Kelola tiket event aktif milikmu dan pantau status pembayarannya.</p>
                </div>

                <form action="{{ route('ticket') }}" method="GET" class="flex gap-2 w-full md:w-auto">
                    <input type="text" name="order_id" placeholder="Cari Order ID..." 
                           value="{{ request('order_id') }}"
                           class="w-full md:w-64 px-4 py-2.5 rounded-xl border border-slate-200 text-slate-800 font-medium text-xs focus:outline-none focus:border-indigo-600 bg-white shadow-sm" required>
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-xl font-bold text-xs transition shadow-sm shrink-0">
                        Cari
                    </button>
                </form>
            </div>

            <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-6">
                <!-- Tab Menu -->
                <div class="flex border-b border-slate-100 mb-6 gap-6">
                    <button onclick="switchTab('aktif')" id="tab-btn-aktif"
                        class="tab-btn pb-3 text-xs font-bold border-b-2 border-indigo-600 text-indigo-600 transition flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path></svg>
                        <span>Event Aktif</span>
                    </button>
                    <button onclick="switchTab('lalu')" id="tab-btn-lalu"
                        class="tab-btn pb-3 text-xs font-bold border-b-2 border-transparent text-slate-400 hover:text-slate-600 transition flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span>Event Lalu</span>
                    </button>
                    <button onclick="switchTab('transaksi')" id="tab-btn-transaksi"
                        class="tab-btn pb-3 text-xs font-bold border-b-2 border-transparent text-slate-400 hover:text-slate-600 transition flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 8h6m-5 0a3 3 0 110 6H9l3 3m-3-6h6m6 1a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span>Transaksi</span>
                    </button>
                </div>

                <!-- Content Event Aktif -->
                <div id="tab-content-aktif" class="tab-content space-y-3">
                    @php
                        $successTransactions = $transactions->whereIn('status', ['success', 'settlement', 'Success', 'paid', 'used']);
                    @endphp

                    @forelse($successTransactions as $item)
                        <div class="p-4 border border-slate-100 rounded-2xl hover:border-indigo-200 transition flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white shadow-sm">
                            <div>
                                <span class="text-[10px] font-bold uppercase tracking-wider text-indigo-600 bg-indigo-50 px-2.5 py-1 rounded-md">
                                    #{{ $item->order_id }}
                                </span>
                                <h3 class="text-sm font-bold text-slate-800 mt-2">{{ $item->event->title ?? 'Event Tidak Ditemukan' }}</h3>
                                <p class="text-xs text-slate-400 mt-1">
                                    📅 {{ \Carbon\Carbon::parse($item->event->date ?? now())->format('d M Y, H:i') }} | 📍 {{ $item->event->location ?? 'Amikom' }}
                                </p>
                            </div>
                            <div>
                                <a href="{{ route('ticket', ['order_id' => $item->order_id]) }}"
                                    class="inline-block px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl transition shadow-md shadow-indigo-100">
                                    Cetak E-Ticket
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="py-12 text-center">
                            <p class="text-slate-500 text-xs font-bold">Belum ada tiket aktif saat ini.</p>
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
                        <div class="p-4 border border-slate-100 bg-slate-50/50 rounded-2xl flex items-center justify-between">
                            <div>
                                <p class="text-[10px] font-bold text-slate-400">#{{ $trx->order_id }}</p>
                                <h4 class="font-bold text-slate-800 text-xs mt-0.5">{{ $trx->event->title ?? 'Tiket Event' }}</h4>
                            </div>
                            <span class="px-3 py-1 rounded-full text-[10px] font-bold {{ in_array(strtolower($trx->status), ['success', 'settlement', 'used']) ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
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
                btn.classList.remove('border-indigo-600', 'text-indigo-600');
                btn.classList.add('border-transparent', 'text-slate-400');
            });

            document.getElementById('tab-content-' + tabName)?.classList.remove('hidden');
            const targetBtn = document.getElementById('tab-btn-' + tabName);
            if(targetBtn) {
                targetBtn.classList.remove('border-transparent', 'text-slate-400');
                targetBtn.classList.add('border-indigo-600', 'text-indigo-600');
            }
        }
    </script>
@endif

@endsection