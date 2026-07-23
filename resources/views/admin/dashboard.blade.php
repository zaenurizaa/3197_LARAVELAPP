@extends('layout.admin')

@section('title', 'Dashboard')

@section('page_title')
    {{ auth()->user()->isSuperAdmin() ? 'Dashboard Superadmin' : 'Dashboard Ringkasan' }}
@endsection

@section('page_subtitle')
    Selamat datang kembali, {{ auth()->user()->name }}!
@endsection

@section('content')

{{-- CASE 1: JIKA TENANT MASIH PENDING --}}
@if(auth()->user()->isOrganizer() && auth()->user()->tenant && auth()->user()->tenant->status === 'pending')
    <div class="bg-white rounded-3xl p-8 border border-slate-100 shadow-sm max-w-2xl mx-auto text-center my-8">
        <div class="w-16 h-16 bg-amber-50 text-amber-500 rounded-2xl flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </div>
        <h3 class="text-2xl font-black text-slate-800">Pendaftaran Dalam Peninjauan</h3>
        <p class="text-slate-500 text-sm mt-2">
            Organisasi <span class="font-bold text-slate-700">{{ auth()->user()->tenant->name }}</span> sedang diverifikasi oleh Superadmin. Fitur pengelolaan event dan analitik akan terbuka otomatis setelah disetujui.
        </p>

        <div class="mt-6 p-4 bg-slate-50 rounded-2xl text-left border border-slate-100 text-sm space-y-2">
            <p class="text-xs font-bold uppercase tracking-wider text-indigo-600 mb-2">Informasi Rekening Pencairan (Payout):</p>
            <div class="flex justify-between py-1 border-b border-slate-200/60">
                <span class="text-slate-400">Bank:</span>
                <span class="font-bold text-slate-700">{{ auth()->user()->tenant->bank_name }}</span>
            </div>
            <div class="flex justify-between py-1 border-b border-slate-200/60">
                <span class="text-slate-400">No. Rekening:</span>
                <span class="font-bold text-slate-700">{{ auth()->user()->tenant->bank_account_number }}</span>
            </div>
            <div class="flex justify-between py-1">
                <span class="text-slate-400">Atas Nama:</span>
                <span class="font-bold text-slate-700">{{ auth()->user()->tenant->bank_account_holder }}</span>
            </div>
        </div>
    </div>

{{-- CASE 2: JIKA SUDAH ACC / SUPERADMIN --}}
@else
    <!-- STATS CARDS -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm">
            <div class="w-12 h-12 bg-indigo-50 text-indigo-600 rounded-2xl flex items-center justify-center mb-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <p class="text-slate-400 text-sm font-bold uppercase mb-1">Total Pendapatan</p>
            <h3 class="text-2xl font-black">Rp {{ number_format($totalPendapatan ?? 0, 0, ',', '.') }}</h3>
        </div>

        <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm">
            <div class="w-12 h-12 bg-green-50 text-green-600 rounded-2xl flex items-center justify-center mb-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path>
                </svg>
            </div>
            <p class="text-slate-400 text-sm font-bold uppercase mb-1">Tiket Terjual</p>
            <h3 class="text-2xl font-black">{{ number_format($tiketTerjual ?? 0, 0, ',', '.') }}</h3>
        </div>

        <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm">
            <div class="w-12 h-12 bg-orange-50 text-orange-600 rounded-2xl flex items-center justify-center mb-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <p class="text-slate-400 text-sm font-bold uppercase mb-1">Event Aktif</p>
            <h3 class="text-2xl font-black">{{ $eventAktif ?? 0 }} Event</h3>
        </div>

        <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm">
            <div class="w-12 h-12 bg-rose-50 text-rose-600 rounded-2xl flex items-center justify-center mb-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <p class="text-slate-400 text-sm font-bold uppercase mb-1">Pesanan Pending</p>
            <h3 class="text-2xl font-black">{{ $pesananPending ?? 0 }} Pesanan</h3>
        </div>
    </div>

    <!-- SEKSI GRAFIK ANALITIK -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-10">
        <!-- Grafik Pertumbuhan Pengguna -->
        <div class="bg-white p-8 rounded-3xl border border-slate-100 shadow-sm">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h3 class="font-black text-xl text-slate-800">Pertumbuhan Pengguna</h3>
                    <p class="text-xs text-slate-400 font-medium mt-0.5">Statistik registrasi pengguna tahun {{ date('Y') }}</p>
                </div>
                <span class="px-3 py-1 bg-indigo-50 text-indigo-600 rounded-xl text-xs font-bold">Bulanan</span>
            </div>
            <div class="relative h-64">
                <canvas id="userGrowthChart"></canvas>
            </div>
        </div>

        <!-- Grafik Pertumbuhan Event -->
        <div class="bg-white p-8 rounded-3xl border border-slate-100 shadow-sm">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h3 class="font-black text-xl text-slate-800">Penyelenggaraan Event</h3>
                    <p class="text-xs text-slate-400 font-medium mt-0.5">Statistik publikasi event tahun {{ date('Y') }}</p>
                </div>
                <span class="px-3 py-1 bg-emerald-50 text-emerald-600 rounded-xl text-xs font-bold">Bulanan</span>
            </div>
            <div class="relative h-64">
                <canvas id="eventGrowthChart"></canvas>
            </div>
        </div>
    </div>

    <!-- TABEL TRANSAKSI -->
    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="p-8 border-b flex justify-between items-center">
            <h3 class="font-black text-xl">Transaksi Terakhir</h3>
            <a href="{{ auth()->user()->isOrganizer() ? route('organizer.transactions.index') : route('admin.transactions.index') }}" class="text-indigo-600 font-bold hover:underline">Lihat Semua</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-slate-50 text-slate-400 uppercase text-[10px] font-black tracking-widest">
                    <tr>
                        <th class="px-8 py-4 w-1/4">Tgl Transaksi</th>
                        <th class="px-8 py-4 w-1/4">Pembeli</th>
                        <th class="px-8 py-4 w-1/4">Event</th>
                        <th class="px-8 py-4 w-[10%]">Status</th>
                        <th class="px-8 py-4 text-right">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y border-t">
                    @forelse($latestTransactions ?? [] as $trx)
                    <tr class="hover:bg-slate-50 transition">
                        <td class="px-8 py-6 text-sm text-slate-600 max-w-xs break-all">
                            {{ $trx->created_at->format('d M y - H:i') }}<br>
                            <span class="text-xs text-slate-400">{{ $trx->order_id }}</span>
                        </td>
                        <td class="px-8 py-6">
                            <p class="font-bold uppercase tracking-wide text-sm text-slate-800 truncate max-w-37.5">{{ $trx->customer_name }}</p>
                            <p class="text-xs text-slate-400 truncate max-w-37.5">{{ $trx->customer_email }}</p>
                        </td>
                        <td class="px-8 py-6 font-medium text-slate-600 max-w-xs truncate">{{ $trx->event->title ?? '-' }}</td>
                        <td class="px-8 py-6 whitespace-nowrap">
                            @if(in_array(strtolower($trx->status), ['success', 'settlement']))
                                <span class="px-3 py-1 bg-green-100 text-green-700 rounded-lg text-xs font-bold uppercase">Success</span>
                            @elseif(strtolower($trx->status) === 'pending')
                                <span class="px-3 py-1 bg-orange-100 text-orange-700 rounded-lg text-xs font-bold uppercase">Pending</span>
                            @else
                                <span class="px-3 py-1 bg-rose-100 text-rose-700 rounded-lg text-xs font-bold uppercase">{{ $trx->status }}</span>
                            @endif
                        </td>
                        <td class="px-8 py-6 font-black text-indigo-600 whitespace-nowrap text-right">Rp {{ number_format($trx->total_price, 0, ',', '.') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-8 py-10 text-center text-slate-500">Belum ada transaksi masuk</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endif

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const months = @json($months ?? []);
        const userData = @json($userData ?? []);
        const eventData = @json($eventData ?? []);

        // 1. Line Chart: Pertumbuhan User
        const ctxUser = document.getElementById('userGrowthChart');
        if (ctxUser) {
            new Chart(ctxUser.getContext('2d'), {
                type: 'line',
                data: {
                    labels: months,
                    datasets: [{
                        label: 'Pengguna Baru',
                        data: userData,
                        borderColor: '#4f46e5',
                        backgroundColor: 'rgba(79, 70, 229, 0.08)',
                        fill: true,
                        tension: 0.4,
                        borderWidth: 3,
                        pointRadius: 4,
                        pointBackgroundColor: '#4f46e5'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { 
                            beginAtZero: true, 
                            grid: { borderDash: [4, 4] },
                            ticks: { precision: 0 }
                        },
                        x: { grid: { display: false } }
                    }
                }
            });
        }

        // 2. Bar Chart: Pertumbuhan Event
        const ctxEvent = document.getElementById('eventGrowthChart');
        if (ctxEvent) {
            new Chart(ctxEvent.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: months,
                    datasets: [{
                        label: 'Event Baru',
                        data: eventData,
                        backgroundColor: '#10b981',
                        borderRadius: 8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { 
                            beginAtZero: true, 
                            grid: { borderDash: [4, 4] },
                            ticks: { precision: 0 }
                        },
                        x: { grid: { display: false } }
                    }
                }
            });
        }
    });
</script>
@endpush