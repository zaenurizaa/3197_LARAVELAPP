@extends('layout.app')

@section('content')
    {{-- Hero Section --}}
    <section class="max-w-7xl mx-auto px-6 py-20 flex flex-col md:flex-row items-center gap-12">
        <div class="flex-1 space-y-8">
            <span class="inline-block px-4 py-1.5 bg-indigo-100 text-indigo-700 rounded-full text-sm font-bold uppercase tracking-wider">#1 Event Platform</span>
            <h1 class="text-5xl md:text-7xl font-extrabold leading-tight">
                Temukan & Pesan <span class="text-indigo-600">Tiket Event</span> Impianmu.
            </h1>
            <p class="text-lg text-slate-500 max-w-lg leading-relaxed">
                Dari konser musik hingga workshop teknologi, semua ada di genggamanmu. Pesan aman & cepat dengan Midtrans.
            </p>
            <div class="flex gap-4">
                <a href="#events" class="px-8 py-4 bg-indigo-600 text-white rounded-2xl font-bold text-lg shadow-xl shadow-indigo-200 hover:scale-105 transition-transform">
                    Mulai Jelajah
                </a>
            </div>
        </div>
        <div class="flex-1 relative">
            <img src="{{ asset('assets/concert.png') }}" alt="Concert" class="rounded-4xl shadow-2xl relative z-10 w-full object-cover aspect-4/5 object-center">
        </div>
    </section>

    {{-- Events Grid Section --}}
    <section id="events" class="max-w-7xl mx-auto px-6 py-20">
        <div class="flex flex-col md:flex-row justify-between items-center mb-12 gap-6">
            <div>
                <h2 class="text-3xl font-extrabold mb-2">Event Terdekat</h2>
                <p class="text-slate-500 font-medium">Jangan sampai ketinggalan acara seru minggu ini!</p>
            </div>

            {{-- Filter Kategori --}}
            <div class="flex gap-2 flex-wrap">
                <a href="{{ url('/') }}#events" 
                   class="px-5 py-2 {{ !request('category') ? 'bg-indigo-600 text-white' : 'bg-slate-100 text-slate-600' }} rounded-xl font-bold transition">
                   Semua Kategori
                </a>
                @foreach($categories as $cat)
                    <a href="{{ url('/?category=' . $cat->slug) }}#events" 
                       class="px-5 py-2 {{ request('category') == $cat->slug ? 'bg-indigo-600 text-white' : 'bg-slate-100 text-slate-600' }} rounded-xl font-bold transition hover:bg-indigo-50">
                       {{ $cat->name }}
                    </a>
                @endforeach
            </div>
        </div>  

        {{-- Loop Grid Event Card --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @forelse($events as $event)
                <div class="group bg-white rounded-3xl border border-slate-100 shadow-sm hover:shadow-2xl transition-all duration-300 overflow-hidden">
                    <div class="relative overflow-hidden aspect-3/4">
                        
                        <img src="{{ ($event->poster_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($event->poster_path))
                                     ? asset('storage/' . $event->poster_path)
                                     : 'https://placehold.co/400x600?text=No+Poster' }}" 
                             alt="{{ $event->title }}" 
                             class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                        
                        <div class="absolute top-4 left-4 px-3 py-1 bg-white/90 backdrop-blur rounded-lg text-xs font-bold uppercase text-indigo-600 shadow-sm">
                            {{ $event->category->name ?? 'Umum' }}
                        </div>
                    </div>
                    <div class="p-6">
                        <h3 class="text-xl font-bold mb-2 group-hover:text-indigo-600 transition line-clamp-1">
                            {{ $event->title }}
                        </h3>
                        <p class="text-slate-500 text-sm mb-4">
                            @if($event->date instanceof \Carbon\Carbon)
                                {{ $event->date->format('d M Y') }}
                            @else
                                {{ \Carbon\Carbon::parse($event->date)->format('d M Y') }}
                            @endif
                        </p>
                        <div class="flex justify-between items-center pt-4 border-t">
                            <span class="text-2xl font-black text-indigo-600">
                                {{ $event->price == 0 ? 'Gratis' : 'Rp ' . number_format($event->price, 0, ',', '.') }}
                            </span>
                            <a href="{{ route('events.show', $event->id) }}" class="px-5 py-2 bg-indigo-50 text-indigo-600 rounded-xl font-bold hover:bg-indigo-600 hover:text-white transition">
                                Detail
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-20 text-center">
                    <p class="text-slate-400 font-bold text-xl">Yah, belum ada event di kategori ini :) </p>
                    <a href="{{ url('/') }}" class="text-indigo-600 underline">Lihat semua event</a>
                </div>
            @endforelse
        </div>
    </section>

    {{-- Partner Section --}}
    <section class="max-w-7xl mx-auto px-6 py-16 border-t border-slate-100 mt-12">
        <div class="text-center max-w-xl mx-auto mb-10">
            <span class="inline-block px-3 py-1 bg-indigo-50 text-indigo-600 rounded-full text-xs font-bold uppercase tracking-wider mb-3">
                Partner Resmi
            </span>
            <h2 class="text-3xl font-extrabold text-slate-800 mb-2">Didukung oleh Kemitraan Hebat</h2>
            <p class="text-slate-500 text-sm">AmikomEventHub bekerja sama dengan instansi dan vendor terpercaya untuk menghadirkan event terbaik.</p>
        </div>
        
        <div class="flex flex-wrap items-center justify-center gap-8 md:gap-12">
            @forelse($partners as $partner)
                <div class="group flex flex-col items-center gap-3 p-5 bg-white rounded-2xl border border-slate-100 hover:border-indigo-100 hover:shadow-xl hover:shadow-indigo-50/40 transition-all duration-300 min-w-[140px]">
                    @if($partner->logo_url && $partner->logo_url != 'https://placehold.co/200x200')
                        <img src="{{ $partner->logo_url }}" alt="{{ $partner->name }}" class="h-12 w-28 object-contain filter grayscale group-hover:grayscale-0 transition-all duration-300">
                    @else
                        <div class="w-12 h-12 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center font-bold text-lg border border-indigo-100 group-hover:bg-indigo-600 group-hover:text-white transition-colors duration-300">
                            {{ strtoupper(substr($partner->name, 0, 2)) }}
                        </div>
                    @endif
                    <span class="text-xs font-bold text-slate-500 group-hover:text-slate-800 transition-colors tracking-wide">
                        {{ $partner->name }}
                    </span>
                </div>
            @empty
                <div class="py-6 text-center text-slate-400 text-sm italic">
                    Belum ada partner resmi terdaftar.
                </div>
            @endforelse
        </div>
    </section>
@endsection