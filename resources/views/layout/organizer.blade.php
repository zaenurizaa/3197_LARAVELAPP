@extends('layout.app')

@section('title', $tenant->name . ' - Profil Penyelenggara')

@section('content')
<main class="max-w-5xl mx-auto px-6 py-12">
    
    {{-- Header Profil Penyelenggara --}}
    <div class="bg-white rounded-[2.5rem] p-8 md:p-12 shadow-sm border border-slate-100 flex flex-col md:flex-row items-center gap-8 mb-12">
        <div class="w-32 h-32 bg-indigo-50 rounded-full flex items-center justify-center text-indigo-600 font-black text-4xl uppercase shrink-0 shadow-inner border-4 border-white">
            {{ strtoupper(substr($tenant->name, 0, 2)) }}
        </div>
        <div class="text-center md:text-left flex-1">
            <h1 class="text-3xl font-black text-slate-800">{{ $tenant->name }}</h1>
            <p class="text-emerald-600 font-bold text-sm flex items-center justify-center md:justify-start gap-1.5 mt-2">
                <svg class="w-5 h-5 fill-current" viewBox="0 0 20 20">
                    <path d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                </svg>
                Penyelenggara Terverifikasi
            </p>
            <p class="text-slate-500 mt-4 max-w-2xl leading-relaxed">
                Halaman profil resmi kepanitiaan. Di sini Anda bisa melihat daftar acara mendatang yang diselenggarakan oleh <strong>{{ $tenant->name }}</strong> serta membaca ulasan dan rekam jejak dari peserta acara sebelumnya.
            </p>
        </div>
        <div class="text-center bg-slate-50 p-6 rounded-3xl border border-slate-100 min-w-[150px]">
            @php
                $avgRating = $reviews->avg('rating') ?? 0;
            @endphp
            <div class="text-3xl font-black text-amber-500 mb-1">⭐ {{ number_format($avgRating, 1) }}</div>
            <div class="text-xs font-bold text-slate-500 uppercase tracking-widest">Dari {{ $reviews->count() }} Ulasan</div>
        </div>
    </div>

    {{-- Daftar Acara Aktif / Mendatang --}}
    <div class="mb-16">
        <h2 class="text-2xl font-black text-slate-800 mb-6 flex items-center gap-3">
            <span class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
            </span>
            Acara Mendatang
        </h2>
        
        @if($activeEvents->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($activeEvents as $event)
                    <a href="{{ route('events.show', $event->id) }}" class="group block bg-white rounded-3xl border border-slate-100 shadow-sm hover:shadow-xl hover:border-indigo-100 transition-all duration-300 overflow-hidden flex flex-col">
                        <div class="h-48 overflow-hidden relative">
                            <img src="{{ $event->poster_path ? (str_starts_with($event->poster_path, 'http') ? $event->poster_path : asset('storage/' . $event->poster_path)) : asset('storage/asset_admin/concert.png') }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            <div class="absolute top-4 left-4 px-3 py-1 bg-white/90 backdrop-blur text-indigo-700 text-[10px] font-black uppercase tracking-wider rounded-lg">
                                {{ $event->category->name ?? 'Umum' }}
                            </div>
                        </div>
                        <div class="p-6 flex flex-col flex-1">
                            <h3 class="font-black text-lg text-slate-800 mb-2 group-hover:text-indigo-600 transition">{{ $event->title }}</h3>
                            <p class="text-xs text-slate-500 font-medium flex items-center gap-2 mb-4">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                {{ \Carbon\Carbon::parse($event->date)->format('d M Y') }}
                            </p>
                            <div class="mt-auto pt-4 border-t border-slate-100 flex items-center justify-between">
                                <span class="font-black text-indigo-600">
                                    {{ $event->effective_price > 0 ? 'Rp ' . number_format($event->effective_price, 0, ',', '.') : 'Gratis' }}
                                </span>
                                <span class="text-[10px] font-bold text-white bg-slate-800 px-3 py-1.5 rounded-lg group-hover:bg-indigo-600 transition">Beli Tiket</span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @else
            <div class="bg-slate-50 border border-slate-100 rounded-3xl p-12 text-center">
                <p class="text-slate-500 font-medium">Belum ada acara mendatang dari penyelenggara ini.</p>
            </div>
        @endif
    </div>

    {{-- Rekam Jejak Penilaian (Review) --}}
    <div>
        <h2 class="text-2xl font-black text-slate-800 mb-6 flex items-center gap-3">
            <span class="w-8 h-8 rounded-full bg-amber-100 flex items-center justify-center text-amber-500">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
            </span>
            Rekam Jejak Ulasan
        </h2>

        @if($reviews->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach($reviews as $review)
                    <div class="p-6 bg-white rounded-3xl border border-slate-100 shadow-sm flex flex-col h-full">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h4 class="font-bold text-slate-800 text-sm">{{ $review->transaction->customer_name ?? 'Peserta' }}</h4>
                                <p class="text-[10px] font-bold text-slate-400 uppercase mt-0.5 tracking-wider">
                                    Event: {{ $review->event->title ?? '-' }}
                                </p>
                            </div>
                            <div class="text-amber-400 text-sm tracking-widest bg-amber-50 px-2 py-1 rounded-lg border border-amber-100/50">
                                @for ($s = 1; $s <= 5; $s++)
                                    {!! $s <= $review->rating ? '★' : '☆' !!}
                                @endfor
                            </div>
                        </div>
                        <p class="text-sm text-slate-600 leading-relaxed italic flex-1">
                            "{{ $review->comment ?? 'Tidak ada pesan tertulis.' }}"
                        </p>
                        <div class="mt-4 pt-4 border-t border-slate-50 text-[10px] text-slate-400 font-medium text-right">
                            Dikirim pada {{ $review->created_at->format('d M Y') }}
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-slate-50 border border-slate-100 rounded-3xl p-12 text-center">
                <p class="text-slate-500 font-medium">Penyelenggara ini belum memiliki ulasan dari acara sebelumnya.</p>
            </div>
        @endif
    </div>

</main>
@endsection
