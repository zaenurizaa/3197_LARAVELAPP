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

    <!-- 🔥 POIN 2 UAS: FORM ULASAN & INTERACTIVE RATING BINTANG EVENT PASCA ACARA -->
    <div class="mt-8 bg-white border border-slate-200 rounded-[2.5rem] p-8 shadow-sm text-left max-w-md mx-auto">
        <div class="flex items-center gap-2.5 mb-6 border-b border-slate-100 pb-4">
            <svg class="w-5 h-5 text-amber-500" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
            <h5 class="text-sm font-black text-slate-800 uppercase tracking-wider">Beri Nilai Event & Kepanitiaan</h5>
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
            <div class="p-4 bg-amber-50/50 border border-amber-100/60 rounded-2xl space-y-2">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-amber-800">Ulasan Anda Telah Terkirim!</span>
                    <span class="text-amber-500 font-bold text-sm">
                        @for($s = 1; $s <= 5; $s++)
                            {!! $s <= $transaction->review->rating ? '★' : '☆' !!}
                        @endfor
                    </span>
                </div>
                <p class="text-xs text-slate-600 italic">
                    "{{ $transaction->review->comment }}"
                </p>
                <div class="text-[9px] text-slate-400 text-right font-medium">
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
                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-2">Rating Kepuasan</label>
                    <div class="flex items-center gap-2">
                        <input type="hidden" name="rating" id="rating-value" value="5">
                        @for($i = 1; $i <= 5; $i++)
                            <button type="button" onclick="setRating({{ $i }})" id="star-btn-{{ $i }}" class="text-3xl text-amber-400 focus:outline-none transition active:scale-90">
                                ★
                            </button>
                        @endfor
                        <span id="rating-label" class="text-xs font-bold text-amber-600 ml-2">Sempurna</span>
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-2">Testimoni / Ulasan Balik</label>
                    <textarea name="comment" rows="3" class="block w-full bg-slate-50 border border-slate-200 text-slate-700 py-3 px-4 rounded-2xl focus:outline-none focus:ring-2 focus:ring-indigo-500/10 focus:border-indigo-600 transition text-xs placeholder:text-slate-400/70 font-medium" placeholder="Tulis kritik dan saran membangun untuk panitia..." required></textarea>
                </div>

                <button type="submit" class="w-full py-4 bg-indigo-600 hover:bg-indigo-700 text-white rounded-2xl font-bold text-xs uppercase tracking-wider transition active:scale-[0.99] shadow-lg shadow-indigo-100">
                    Kirim Ulasan Resmi
                </button>
            </form>

            <script>
                const labels = {
                    1: 'Sangat Kecewa',
                    2: 'Buruk',
                    3: 'Cukup',
                    4: 'Bagus',
                    5: 'Sempurna'
                };

                function setRating(val) {
                    document.getElementById('rating-value').value = val;
                    document.getElementById('rating-label').innerText = labels[val];

                    for (let s = 1; s <= 5; s++) {
                        const star = document.getElementById(`star-btn-${s}`);
                        if (s <= val) {
                            star.classList.remove('text-slate-300');
                            star.classList.add('text-amber-400');
                        } else {
                            star.classList.remove('text-amber-400');
                            star.classList.add('text-slate-300');
                        }
                    }
                }
            </script>
        @endif
    </div>
</main>
@endsection