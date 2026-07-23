@extends('layout.app')

@section('title', 'Pembayaran Berhasil')

@section('content')
<main class="max-w-3xl mx-auto px-6 py-20 text-center">
    <!-- Card Utama Status Sukses -->
    <div class="bg-white rounded-3xl border border-slate-200 p-12 shadow-sm inline-block w-full max-w-md">
        <div class="w-24 h-24 bg-green-100 text-green-500 rounded-full flex items-center justify-center mx-auto mb-6">
            <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
            </svg>
        </div>
        <h2 class="text-3xl font-black mb-4">Terima Kasih!</h2>
        <p class="text-slate-500 mb-8 leading-relaxed text-sm">
            Pembayaran untuk pesanan <strong>{{ $transaction->order_id }}</strong> sedang diproses atau telah berhasil. 
            E-Ticket akan dikirim ke email Anda (<strong>{{ $transaction->customer_email }}</strong>) setelah pembayaran terkonfirmasi lunas.
        </p>
        <div class="mt-8 flex flex-col sm:flex-row gap-4 justify-center items-center">
            <a href="{{ route('ticket') }}?order_id={{ $transaction->order_id }}" 
               class="inline-flex items-center justify-center px-6 py-3 rounded-xl text-sm font-black text-white bg-indigo-600 hover:bg-indigo-700 transition shadow-md w-full sm:w-auto">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                </svg>
                Lihat Tiket Saya
            </a>

            <a href="{{ route('home') }}" 
               class="inline-flex items-center justify-center px-6 py-3 border border-slate-200 rounded-xl text-sm font-bold text-slate-700 bg-white hover:bg-slate-50 transition shadow-sm w-full sm:w-auto">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali ke Beranda
            </a>
        </div>
    </div>

    <!-- 🔥 POIN 2 UAS: FORM ULASAN & RATING BINTANG EVENT PASCA ACARA -->
    <div class="mt-8 bg-white border border-slate-200 rounded-3xl p-8 shadow-sm text-left max-w-md mx-auto">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-8 h-8 bg-amber-500 rounded-lg flex items-center justify-center text-white font-bold text-lg">⭐</div>
            <h5 class="text-base font-bold text-slate-800 tracking-tight">Beri Nilai Event & Kepanitiaan</h5>
        </div>
        
        <!-- Status Flash Message -->
        @if(session('success'))
            <div class="mb-4 p-3 bg-emerald-50 text-emerald-700 text-xs font-semibold rounded-xl border border-emerald-100">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="mb-4 p-3 bg-rose-50 text-rose-700 text-xs font-semibold rounded-xl border border-rose-100">
                {{ session('error') }}
            </div>
        @endif

        @if($transaction->review)
            <!-- TAMPILAN JIKA USER SUDAH PERNAH MENGISI ULASAN -->
            <div class="p-4 bg-amber-50/60 border border-amber-100 rounded-2xl space-y-2">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-amber-800">Ulasan Anda Telah Terkirim!</span>
                    <span class="text-amber-500 font-bold text-sm">
                        @for($s = 1; $s <= 5; $s++)
                            {!! $s <= $transaction->review->rating ? '★' : '☆' !!}
                        @endfor
                    </span>
                </div>
                <p class="text-xs text-slate-600 italic">
                    "{{ $transaction->review->comment }}"
                </p>
                <div class="text-[10px] text-slate-400 text-right">
                    Dikirim pada {{ $transaction->review->created_at->format('d M Y, H:i') }}
                </div>
            </div>
        @else
            <!-- FORM INPUT ULASAN BARU -->
            <form action="{{ route('review.store') }}" method="POST" class="space-y-4">
                @csrf
                <!-- Input Hidden Relasi ID -->
                <input type="hidden" name="transaction_id" value="{{ $transaction->id }}">
                
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">Rating Kepuasan (1-5)</label>
                    <select name="rating" class="block w-full bg-slate-50 border border-slate-200 text-slate-700 py-2.5 px-3 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition text-xs cursor-pointer" required>
                        <option value="5" selected>⭐⭐⭐⭐⭐ (5 - Sempurna)</option>
                        <option value="4">⭐⭐⭐⭐ (4 - Bagus)</option>
                        <option value="3">⭐⭐⭐ (3 - Cukup)</option>
                        <option value="2">⭐⭐ (2 - Buruk)</option>
                        <option value="1">⭐ (1 - Sangat Kecewa)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">Testimoni / Ulasan Balik</label>
                    <textarea name="comment" rows="3" class="block w-full bg-slate-50 border border-slate-200 text-slate-700 py-2.5 px-3 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition text-xs placeholder:text-slate-400/70" placeholder="Tulis kritik dan saran membangun untuk panitia..." required></textarea>
                </div>

                <button type="submit" class="w-full px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-md shadow-indigo-100 transition duration-200 text-center">
                    Kirim Ulasan Resmi
                </button>
            </form>
        @endif
    </div>
</main>
@endsection