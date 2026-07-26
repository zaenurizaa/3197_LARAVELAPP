@extends('layout.admin')

@section('title', 'Edit Event - Admin')
@section('page_title', 'Edit Event')
@section('page_subtitle', 'Ubah detail acara yang sudah terdaftar.')

@section('content')
<div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm max-w-3xl">
    
    <form action="{{ route('admin.events.update', $event->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT') 

        {{-- Judul Event --}}
        <div>
            <label class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-wide">Judul Event</label>
            <input type="text" name="title" value="{{ old('title', $event->title) }}" 
                class="w-full px-5 py-4 bg-slate-50 border-2 border-slate-100 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-600 outline-none transition font-medium" required>
            @error('title') <span class="text-red-500 text-sm mt-1 block font-semibold">{{ $message }}</span> @enderror
        </div>

        {{-- Kategori --}}
        <div>
            <label class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-wide">Kategori</label>
            <select name="category_id" class="w-full px-5 py-4 bg-slate-50 border-2 border-slate-100 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-600 outline-none transition font-medium" required>
                <option value="">Pilih Kategori</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ old('category_id', $event->category_id) == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
            @error('category_id') <span class="text-red-500 text-sm mt-1 block font-semibold">{{ $message }}</span> @enderror
        </div>

        {{-- Deskripsi --}}
        <div>
            <label class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-wide">Deskripsi</label>
            <textarea name="description" rows="4" 
                class="w-full px-5 py-4 bg-slate-50 border-2 border-slate-100 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-600 outline-none transition font-medium">{{ old('description', $event->description) }}</textarea>
            @error('description') <span class="text-red-500 text-sm mt-1 block font-semibold">{{ $message }}</span> @enderror
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Tanggal & Waktu --}}
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-wide">Tanggal & Waktu</label>
                <input type="datetime-local" name="date" 
                    value="{{ old('date', $event->date ? (\Carbon\Carbon::parse($event->date)->format('Y-m-d\TH:i')) : '') }}" 
                    class="w-full px-5 py-4 bg-slate-50 border-2 border-slate-100 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-600 outline-none transition font-medium" required>
                @error('date') <span class="text-red-500 text-sm mt-1 block font-semibold">{{ $message }}</span> @enderror
            </div>
            
            {{-- Lokasi --}}
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-wide">Lokasi</label>
                <input type="text" name="location" value="{{ old('location', $event->location) }}" 
                    class="w-full px-5 py-4 bg-slate-50 border-2 border-slate-100 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-600 outline-none transition font-medium" required>
                @error('location') <span class="text-red-500 text-sm mt-1 block font-semibold">{{ $message }}</span> @enderror
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Harga --}}
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-wide">Harga (Rp)</label>
                <input type="number" name="price" value="{{ old('price', $event->price) }}" 
                    class="w-full px-5 py-4 bg-slate-50 border-2 border-slate-100 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-600 outline-none transition font-medium" required min="0">
                @error('price') <span class="text-red-500 text-sm mt-1 block font-semibold">{{ $message }}</span> @enderror
            </div>
            
            {{-- Kapasitas (Stok) --}}
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-wide">Kapasitas (Stok)</label>
                <input type="number" name="stock" value="{{ old('stock', $event->stock) }}" 
                    class="w-full px-5 py-4 bg-slate-50 border-2 border-slate-100 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-600 outline-none transition font-medium" required min="1">
                @error('stock') <span class="text-red-500 text-sm mt-1 block font-semibold">{{ $message }}</span> @enderror
            </div>
        </div>

        {{-- Poster Event --}}
        <div>
            <label class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-wide">Poster Event (Ganti Poster)</label>
            
            @if($event->poster_path)
                <div class="mb-3 flex items-center gap-4 p-4 bg-slate-50 border-2 border-dashed border-slate-100 rounded-2xl max-w-sm">
                    <img src="{{ str_starts_with($event->poster_path, 'http') ? $event->poster_path : asset('storage/' . $event->poster_path) }}" class="w-14 h-20 object-cover rounded-xl shadow-sm border-2 border-white">
                    <div>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Poster Aktif</p>
                        <a href="{{ str_starts_with($event->poster_path, 'http') ? $event->poster_path : asset('storage/' . $event->poster_path) }}" target="_blank" class="text-sm text-indigo-600 hover:underline font-bold flex items-center gap-1 mt-0.5">
                            Lihat Resolusi Penuh ↗
                        </a>
                    </div>
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-1 uppercase tracking-wide">Upload File Baru</label>
                    <input type="file" name="poster" accept="image/*" 
                        class="w-full px-5 py-4 bg-slate-50 border-2 border-slate-100 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-600 outline-none transition font-medium">
                    @error('poster') <span class="text-red-500 text-sm mt-1 block font-semibold">{{ $message }}</span> @enderror
                </div>
                
                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-1 uppercase tracking-wide">Atau Ganti URL Internet</label>
                    <input type="url" name="poster_url" value="{{ old('poster_url', str_starts_with($event->poster_path, 'http') ? $event->poster_path : '') }}" placeholder="https://example.com/poster.jpg"
                        class="w-full px-5 py-4 bg-slate-50 border-2 border-slate-100 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-600 outline-none transition font-medium">
                    @error('poster_url') <span class="text-red-500 text-sm mt-1 block font-semibold">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        {{-- Aksi --}}
        <div class="pt-4 flex justify-end gap-4 border-t border-slate-100">
            <a href="{{ route('admin.events.index') }}" class="px-6 py-4 text-slate-500 font-bold hover:text-slate-800 transition">Batal</a>
            <button type="submit" class="px-8 py-4 bg-indigo-600 text-white rounded-2xl font-bold shadow-lg shadow-indigo-100 hover:bg-indigo-700 transition">
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>
@endsection