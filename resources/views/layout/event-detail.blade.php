@extends('layout.app')

@section('title', $event->title . ' - AmikomEventHub')

@section('content')
    <main class="max-w-7xl mx-auto px-6 py-12 grid grid-cols-1 lg:grid-cols-3 gap-12">
        
        {{-- Kolom Kiri: Poster & Penyelenggara --}}
        <div class="lg:col-span-1">
            <div class="sticky top-32">
                <img src="{{ $event->poster_path 
                             ? (str_starts_with($event->poster_path, 'http') ? $event->poster_path : asset('storage/' . $event->poster_path))
                             : asset('storage/asset_admin/concert.png') }}" 
                     alt="{{ $event->title }}"
                     class="w-full rounded-[2.5rem] shadow-2xl border-8 border-white object-cover aspect-3/4">
                
                {{-- 🔥 PENYELENGGARA: Menggunakan Nama Tenant / Organisasi --}}
                <a href="{{ isset($event->tenant) ? route('organizer.profile', $event->tenant->slug) : '#' }}" class="block mt-8 p-6 bg-white rounded-3xl border border-slate-100 shadow-sm hover:border-indigo-300 hover:shadow-lg transition-all group">
                    <h4 class="font-bold mb-4 text-slate-800 flex items-center justify-between">
                        Penyelenggara
                        <svg class="w-4 h-4 text-slate-400 group-hover:text-indigo-600 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </h4>
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-indigo-100 rounded-2xl flex items-center justify-center text-indigo-600 font-bold text-lg uppercase shrink-0 group-hover:bg-indigo-600 group-hover:text-white transition">
                            {{ strtoupper(substr($event->tenant->name ?? $event->user->name ?? 'AE', 0, 2)) }}
                        </div>
                        <div>
                            <p class="font-bold text-slate-800 leading-snug group-hover:text-indigo-600 transition">
                                {{ $event->tenant->name ?? $event->user->name ?? 'Amikom Event Hub Admin' }}
                            </p>
                            <p class="text-xs text-emerald-600 font-semibold flex items-center gap-1 mt-0.5">
                                <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 20 20">
                                    <path d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                                </svg>
                                Verified Organizer
                            </p>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        {{-- Kolom Kanan: Detail Informasi Event --}}
        <div class="lg:col-span-2 space-y-12">
            <div class="space-y-4">
                <span class="inline-block px-4 py-1.5 bg-indigo-100 text-indigo-700 rounded-full text-sm font-bold uppercase tracking-wider">
                    {{ $event->category->name ?? 'Umum' }}
                </span>
                
                <h1 class="text-4xl md:text-5xl font-black leading-tight text-slate-800">
                    {{ $event->title }}
                </h1>
                
                <div class="flex flex-wrap gap-6 text-slate-500 font-medium">
                    {{-- Tanggal & Waktu --}}
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <span>
                            @if($event->date instanceof \Carbon\Carbon)
                                {{ $event->date->format('l, d M Y - H:i') }} WIB
                            @else
                                {{ \Carbon\Carbon::parse($event->date)->format('l, d M Y - H:i') }} WIB
                            @endif
                        </span>
                    </div>
                    
                    {{-- Lokasi --}}
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <span>{{ $event->location }}</span>
                    </div>
                </div>
            </div>

            {{-- Deskripsi --}}
            <div class="prose prose-slate max-w-none">
                <h3 class="text-2xl font-bold mb-4 text-slate-800">Deskripsi Event</h3>
                <div class="text-lg text-slate-600 leading-relaxed whitespace-pre-line">
                    {{ $event->description ?? 'Tidak ada deskripsi tambahan untuk event ini.' }}
                </div>
            </div>

            {{-- Card Transaksi --}}
            <div class="bg-indigo-600 rounded-[2.5rem] p-8 md:p-12 text-white shadow-2xl shadow-indigo-200 relative overflow-hidden">
                <div class="relative z-10 flex flex-col md:flex-row justify-between items-center gap-8">
                    <div>
                        <p class="text-indigo-200 font-bold uppercase tracking-widest text-sm mb-2">Harga Tiket</p>
                        <h2 class="text-5xl font-black">
                            @if($event->price == 0)
                                Gratis
                            @else
                                Rp {{ number_format($event->effective_price, 0, ',', '.') }}<span class="text-lg font-medium text-indigo-200"> /orang</span>
                            @endif
                        </h2>
                        {{-- Active tier badge --}}
                        @if($event->tiers->count() > 0 && $event->active_tier)
                        <div class="mt-2 inline-flex items-center gap-1.5 px-3 py-1 bg-white/20 backdrop-blur rounded-full text-xs font-bold">
                            <span class="w-2 h-2 rounded-full bg-green-400 animate-pulse"></span>
                            {{ $event->effective_tier_name }} — Aktif sekarang
                        </div>
                        @endif
                        
                        <p class="mt-4 text-indigo-100 flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            @if($event->stock > 0)
                                Sisa kapasitas slot: <span class="font-bold underline">{{ $event->stock }} Tiket lagi!</span>
                            @else
                                <span class="font-bold p-1 px-3 bg-rose-500 text-white rounded-lg">Maaf, Tiket Sudah Habis!</span>
                            @endif
                        </p>
                    </div>
                    
                    <div>
                        @if($event->stock > 0)
                            <a href="{{ route('checkout.create', $event->id) }}"
                               class="inline-block px-10 py-5 bg-white text-indigo-600 rounded-2xl font-black text-xl hover:scale-105 transition-transform shadow-xl">
                                Pesan Sekarang
                            </a>
                        @else
                            <button disabled 
                                    class="inline-block px-10 py-5 bg-slate-300 text-slate-500 rounded-2xl font-black text-xl cursor-not-allowed shadow-inner">
                                Habis Terjual
                            </button>
                        @endif
                    </div>
                </div>
                <div class="absolute -right-20 -bottom-20 w-64 h-64 bg-white opacity-10 rounded-full"></div>
                <div class="absolute -left-10 -top-10 w-32 h-32 bg-indigo-400 opacity-20 rounded-full"></div>
            </div>

            {{-- Tiered Pricing Timeline --}}
            @if($event->tiers->count() > 0)
            <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-8">
                <h3 class="text-lg font-bold text-slate-800 mb-2 flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path></svg>
                    <span>Tahapan Penjualan Tiket</span>
                </h3>
                <p class="text-slate-500 text-sm mb-6">Harga tiket naik otomatis seiring bergantinya fase penjualan.</p>
                <div class="space-y-3">
                @foreach($event->tiers->sortBy('start_date') as $index => $tier)
                    @php
                        $isActive = $tier->isActive();
                        $isPast   = now()->isAfter($tier->end_date);
                        $isFuture = now()->isBefore($tier->start_date);
                    @endphp
                    <div class="flex items-center gap-4 p-4 rounded-2xl border
                        {{ $isActive ? 'bg-indigo-50 border-indigo-300' : ($isPast ? 'bg-slate-50 border-slate-200 opacity-60' : 'bg-white border-slate-200') }}">
                        {{-- Step number --}}
                        <div class="w-9 h-9 rounded-full flex items-center justify-center font-black text-sm shrink-0
                            {{ $isActive ? 'bg-indigo-600 text-white' : ($isPast ? 'bg-slate-300 text-slate-600' : 'bg-slate-100 text-slate-500') }}">
                            {{ $index + 1 }}
                        </div>
                        {{-- Info --}}
                        <div class="flex-1 min-w-0">
                            <p class="font-bold text-slate-800 flex items-center gap-2">
                                {{ $tier->name }}
                                @if($isActive)
                                <span class="text-xs font-bold px-2 py-0.5 bg-green-100 text-green-700 rounded-full animate-pulse">AKTIF SEKARANG</span>
                                @elseif($isPast)
                                <span class="text-xs font-bold px-2 py-0.5 bg-slate-200 text-slate-500 rounded-full">SELESAI</span>
                                @else
                                <span class="text-xs font-bold px-2 py-0.5 bg-amber-100 text-amber-700 rounded-full">BELUM DIMULAI</span>
                                @endif
                            </p>
                            <p class="text-xs text-slate-500 mt-0.5">
                                {{ \Carbon\Carbon::parse($tier->start_date)->format('d M Y') }}
                                &ndash;
                                {{ \Carbon\Carbon::parse($tier->end_date)->format('d M Y') }}
                                @if($tier->stock !== null)
                                    &bull; Stok: {{ $tier->stock }} tiket
                                @endif
                            </p>
                        </div>
                        {{-- Price --}}
                        <div class="text-right shrink-0">
                            <p class="font-black text-lg {{ $isActive ? 'text-indigo-600' : 'text-slate-600' }}">
                                Rp {{ number_format($tier->price, 0, ',', '.') }}
                            </p>
                        </div>
                    </div>
                @endforeach
                </div>
            </div>
            @endif

            {{-- Kebijakan Tiket --}}
            <div class="space-y-4">
                <h3 class="text-xl font-bold text-slate-800">Kebijakan Tiket</h3>
                <ul class="space-y-3 text-slate-500">
                    <li class="flex items-start gap-2">
                        <svg class="w-5 h-5 text-green-500 mt-1 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span>E-Ticket akan dikirimkan otomatis setelah pembayaran berhasil dikonfirmasi.</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <svg class="w-5 h-5 text-green-500 mt-1 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span>Tiket dapat langsung digunakan untuk masuk lokasi acara lewat scan kode QR (Check-in).</span>
                    </li>
                    <li class="flex items-start gap-2 text-rose-500">
                        <svg class="w-5 h-5 text-rose-500 mt-1 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>Tiket yang sudah berhasil dibeli tidak dapat dibatalkan atau direfund (pengembalian dana).</span>
                    </li>
                </ul>
            </div>

            {{-- REKAM JEJAK ULASAN --}}
            @php
                $avgRating = $event->reviews->avg('rating') ?? 0;
                $totalReviews = $event->reviews->count();
            @endphp
            <div class="p-8 bg-white rounded-3xl border border-slate-100 shadow-sm space-y-6">
                <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                    <div>
                        <h3 class="text-2xl font-bold text-slate-800">Ulasan Peserta</h3>
                        <p class="text-sm text-slate-500">Rekam jejak penilaian dari pembeli sebelumnya</p>
                    </div>
                    <div class="flex items-center gap-3 bg-amber-50 px-4 py-2 rounded-2xl border border-amber-100">
                        <span class="text-2xl font-black text-amber-500">⭐ {{ number_format($avgRating, 1) }}</span>
                        <span class="text-xs font-bold text-amber-700">/ 5.0 ({{ $totalReviews }} Ulasan)</span>
                    </div>
                </div>

                <!-- Lista Testimoni -->
                <div class="space-y-4">
                    @forelse ($event->reviews as $review)
                        <div class="p-5 bg-slate-50 rounded-2xl border border-slate-100 space-y-2">
                            <div class="flex justify-between items-center">
                                <span class="font-bold text-slate-800 text-sm">
                                    {{ $review->transaction->customer_name ?? 'Peserta' }}
                                </span>
                                <div class="text-amber-400 text-xs tracking-widest">
                                    @for ($s = 1; $s <= 5; $s++)
                                        {!! $s <= $review->rating ? '★' : '☆' !!}
                                    @endfor
                                </div>
                            </div>
                            <p class="text-sm text-slate-600 leading-relaxed">
                                "{{ $review->comment ?? 'Tidak ada pesan ulasan tertulis.' }}"
                            </p>
                            <div class="text-[10px] text-slate-400 text-right">
                                {{ $review->created_at->diffForHumans() }}
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-6 text-slate-400 text-sm italic">
                            Belum ada ulasan untuk event ini. Jadilah peserta pertama yang memberikan ulasan pasca-acara!
                        </div>
                    @endforelse
                </div>
            </div>

        </div>
    </main>
@endsection