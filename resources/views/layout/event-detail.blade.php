@extends('layout.app')

@section('title', $event->title . ' - AmikomEventHub')

@section('content')
    <main class="max-w-7xl mx-auto px-6 py-12 grid grid-cols-1 lg:grid-cols-3 gap-12">
        
        {{-- Kolom Kiri: Poster & Penyelenggara --}}
        <div class="lg:col-span-1">
            <div class="sticky top-32">
                <img src="{{ ($event->poster_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($event->poster_path))
                             ? asset('storage/' . $event->poster_path)
                             : 'https://placehold.co/400x600?text=No+Poster' }}" 
                     alt="{{ $event->title }}"
                     class="w-full rounded-[2.5rem] shadow-2xl border-8 border-white object-cover aspect-3/4">
                
                <div class="mt-8 p-6 bg-white rounded-3xl border border-slate-100 shadow-sm">
                    <h4 class="font-bold mb-4">Penyelenggara</h4>
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-indigo-100 rounded-full flex items-center justify-center text-indigo-600 font-bold">
                            {{ strtoupper(substr($event->user->name ?? 'AE', 0, 2)) }}
                        </div>
                        <div>
                            <p class="font-bold text-slate-800">{{ $event->user->name ?? 'Amikom Event Hub Admin' }}</p>
                            <p class="text-xs text-slate-500">Verified Organizer</p>
                        </div>
                    </div>
                </div>
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
                    {{-- Tanggal & Waktu Dinamis --}}
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
                    
                    {{-- Lokasi Dinamis --}}
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <span>{{ $event->location }}</span>
                    </div>
                </div>
            </div>

            {{-- Deskripsi Dinamis --}}
            <div class="prose prose-slate max-w-none">
                <h3 class="text-2xl font-bold mb-4 text-slate-800">Deskripsi Event</h3>
                <div class="text-lg text-slate-600 leading-relaxed whitespace-pre-line">
                    {{ $event->description ?? 'Tidak ada deskripsi tambahan untuk event ini.' }}
                </div>
            </div>

            {{-- Card Transaksi Tiket Dinamis --}}
            <div class="bg-indigo-600 rounded-[2.5rem] p-8 md:p-12 text-white shadow-2xl shadow-indigo-200 relative overflow-hidden">
                <div class="relative z-10 flex flex-col md:flex-row justify-between items-center gap-8">
                    <div>
                        <p class="text-indigo-200 font-bold uppercase tracking-widest text-sm mb-2">Harga Tiket</p>
                        <h2 class="text-5xl font-black">
                            @if($event->price == 0)
                                Gratis
                            @else
                                Rp {{ number_format($event->price, 0, ',', '.') }}<span class="text-lg font-medium text-indigo-200"> /orang</span>
                            @endif
                        </h2>
                        
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
                    
                    {{-- Aksi Pembelian --}}
                    <div>
                        @if($event->stock > 0)
                            <a href="{{ route('checkout') }}?event_id={{ $event->id }}"
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
        </div>
    </main>
@endsection