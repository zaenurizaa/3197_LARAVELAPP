@extends('layout.app')

@section('title', 'Pembayaran Berhasil')

@section('content')
<main class="max-w-4xl mx-auto px-6 py-12">
    <!-- Kontainer Transaksi Sukses Model Mockup -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-8 md:p-12">
        
        <!-- Baris Atas: Ikon Centang Hijau Besar Melayang & Status Utama -->
        <div class="flex flex-col items-center text-center mb-10 border-b border-slate-100 pb-8">
            <div class="w-20 h-20 bg-[#d1fad7] text-[#16a34a] rounded-full flex items-center justify-center mb-4">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3.5" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <h2 class="text-3xl font-black text-slate-800 tracking-tight">Pembayaran Berhasil</h2>
            <p class="text-slate-500 text-xs mt-2 max-w-md">
                Terima kasih, pembayaran Anda sudah kami terima dan akan segera diproses.
            </p>
        </div>

        <!-- Grid Detail Informasi Transaksi Persis Mockup User -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-start mb-8 text-left text-sm">
            
            <!-- Sisi Kiri: Rincian Order ID & Event -->
            <div class="space-y-6">
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Order ID</label>
                    <p class="text-lg font-black text-indigo-700 font-mono tracking-wide">{{ $transaction->order_id }}</p>
                    <span class="inline-block mt-1.5 px-3 py-1 bg-green-100 text-green-700 rounded-lg text-[10px] font-bold uppercase tracking-wider">SUCCESS</span>
                </div>
                
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Nama Event</label>
                    <p class="font-bold text-slate-800 text-base leading-snug">{{ $transaction->event->title ?? '-' }}</p>
                    <p class="text-xs text-slate-500 mt-1">
                        📅 {{ \Carbon\Carbon::parse($transaction->event->date ?? now())->format('d M Y') }} - {{ $transaction->event->location ?? '-' }}
                    </p>
                </div>

                @if($transaction->customer_email)
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Email Terdaftar</label>
                    <p class="font-bold text-slate-700">{{ $transaction->customer_email }}</p>
                </div>
                @endif
            </div>

            <!-- Sisi Kanan: Nama Pemesan, WhatsApp & Total Bayar -->
            <div class="space-y-6 bg-slate-50 p-6 rounded-2xl border border-slate-100">
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Nama Pemesan</label>
                    <p class="font-black text-slate-800 text-base">{{ $transaction->customer_name }}</p>
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">No. WhatsApp</label>
                    <p class="font-bold text-slate-700">{{ $transaction->customer_phone }}</p>
                </div>

                <div class="pt-4 border-t border-slate-200">
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Total Pembayaran</label>
                    <p class="text-2xl font-black text-indigo-600">Rp {{ number_format($transaction->total_price ?? 0, 0, ',', '.') }}</p>
                </div>
            </div>

        </div>

        <!-- Tombol Aksi Bawah -->
        <div class="flex flex-col sm:flex-row gap-3 justify-center items-center pt-6 border-t border-slate-100">
            <a href="{{ route('ticket') }}?order_id={{ $transaction->order_id }}" 
               class="inline-flex items-center justify-center px-8 py-4 rounded-xl text-xs font-black text-white bg-indigo-600 hover:bg-indigo-700 transition shadow-md w-full sm:w-auto uppercase tracking-wider">
                Lihat E-Tiket Saya
            </a>

            <a href="{{ route('home') }}" 
               class="inline-flex items-center justify-center px-8 py-4 border border-slate-200 rounded-xl text-xs font-bold text-slate-600 bg-white hover:bg-slate-50 transition shadow-sm w-full sm:w-auto uppercase tracking-wider">
                Kembali ke Beranda
            </a>
        </div>

    </div>
</main>
@endsection