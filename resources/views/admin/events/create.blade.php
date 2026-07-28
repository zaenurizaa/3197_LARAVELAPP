@extends('layout.admin')

@section('title', 'Tambah Event Baru - Admin')
@section('page_title', 'Tambah Event Baru')
@section('page_subtitle', 'Masukkan detail acara baru yang akan diselenggarakan.')

@section('content')
<div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm max-w-3xl">

    @php
        $user = Auth::guard('admin')->user() ?? Auth::guard('organizer')->user() ?? auth()->user();
        $isSuperAdmin = $user ? $user->isSuperAdmin() : false;
        $storeRoute = $isSuperAdmin ? route('admin.events.store') : route('organizer.events.store');
    @endphp

    <form id="event-form" action="{{ $storeRoute }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        {{-- Judul Event --}}
        <div>
            <label class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-wide">Judul Event</label>
            <input type="text" name="title" value="{{ old('title') }}" 
                class="w-full px-5 py-4 bg-slate-50 border-2 border-slate-100 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-600 outline-none transition font-medium" required>
            @error('title') <span class="text-red-500 text-sm mt-1 block font-semibold">{{ $message }}</span> @enderror
        </div>

        {{-- Penyelenggara / Tenant (Hanya untuk Superadmin) --}}
        @if($isSuperAdmin)
        <div>
            <label class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-wide">Penyelenggara (Tenant)</label>
            <select name="tenant_id" class="w-full px-5 py-4 bg-slate-50 border-2 border-slate-100 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-600 outline-none transition font-medium" required>
                <option value="">Pilih Penyelenggara</option>
                @foreach($tenants as $tenant)
                    <option value="{{ $tenant->id }}" {{ old('tenant_id') == $tenant->id ? 'selected' : '' }}>
                        {{ $tenant->name }}
                    </option>
                @endforeach
            </select>
            @error('tenant_id') <span class="text-red-500 text-sm mt-1 block font-semibold">{{ $message }}</span> @enderror
        </div>
        @endif

        {{-- Kategori --}}
        <div>
            <label class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-wide">Kategori</label>
            <select name="category_id" class="w-full px-5 py-4 bg-slate-50 border-2 border-slate-100 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-600 outline-none transition font-medium" required>
                <option value="">Pilih Kategori</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
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
                class="w-full px-5 py-4 bg-slate-50 border-2 border-slate-100 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-600 outline-none transition font-medium">{{ old('description') }}</textarea>
            @error('description') <span class="text-red-500 text-sm mt-1 block font-semibold">{{ $message }}</span> @enderror
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Tanggal & Waktu --}}
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-wide">Tanggal & Waktu</label>
                <input type="datetime-local" name="date" value="{{ old('date') }}" 
                    class="w-full px-5 py-4 bg-slate-50 border-2 border-slate-100 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-600 outline-none transition font-medium" required>
                @error('date') <span class="text-red-500 text-sm mt-1 block font-semibold">{{ $message }}</span> @enderror
            </div>
            
            {{-- Lokasi --}}
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-wide">Lokasi</label>
                <input type="text" name="location" value="{{ old('location') }}" 
                    class="w-full px-5 py-4 bg-slate-50 border-2 border-slate-100 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-600 outline-none transition font-medium" required>
                @error('location') <span class="text-red-500 text-sm mt-1 block font-semibold">{{ $message }}</span> @enderror
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Harga Dasar --}}
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-wide">Harga Dasar (Rp)</label>
                <input type="number" name="price" value="{{ old('price', 0) }}" 
                    class="w-full px-5 py-4 bg-slate-50 border-2 border-slate-100 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-600 outline-none transition font-medium" required min="0">
                @error('price') <span class="text-red-500 text-sm mt-1 block font-semibold">{{ $message }}</span> @enderror
            </div>
            
            {{-- Kapasitas (Stok) --}}
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-wide">Kapasitas Total (Stok)</label>
                <input type="number" name="stock" value="{{ old('stock', 1) }}" 
                    class="w-full px-5 py-4 bg-slate-50 border-2 border-slate-100 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-600 outline-none transition font-medium" required min="1">
                @error('stock') <span class="text-red-500 text-sm mt-1 block font-semibold">{{ $message }}</span> @enderror
            </div>
        </div>

        {{-- 🔥 DYNAMIC TICKET TIERS COMPONENT --}}
        <div class="bg-slate-50 p-6 rounded-[2rem] border-2 border-dashed border-slate-200/80 space-y-4">
            <div class="flex justify-between items-center border-b border-slate-200 pb-3">
                <div>
                    <h4 class="font-black text-slate-800 text-sm uppercase tracking-wider">Dynamic Ticket Tiers</h4>
                    <p class="text-xs text-slate-400 font-semibold mt-0.5">Tentukan tingkatan harga tiket dinamis (Early Bird, Presale, dll)</p>
                </div>
                <button type="button" onclick="addTierRow()" class="px-4 py-2 bg-indigo-600 text-white rounded-xl text-xs font-bold hover:bg-indigo-700 transition shadow-sm">
                    + Tambah Tier
                </button>
            </div>

            <!-- Tiers List Container -->
            <div id="tiers-wrapper" class="space-y-4">
                <!-- Baris akan ditambahkan via JS -->
            </div>

            <div id="no-tiers-helper" class="text-center py-6 text-slate-400 text-xs font-semibold">
                Belum ada tiering aktif. Tiket Anda akan menggunakan harga dasar dan kapasitas total di atas.
            </div>
        </div>

        {{-- Poster Event --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-wide">Upload Poster (File)</label>
                <input type="file" name="poster" accept="image/*" 
                    class="w-full px-5 py-4 bg-slate-50 border-2 border-slate-100 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-600 outline-none transition font-medium">
                @error('poster') <span class="text-red-500 text-sm mt-1 block font-semibold">{{ $message }}</span> @enderror
            </div>
            
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-wide">Atau URL Poster (Internet)</label>
                <input type="url" name="poster_url" value="{{ old('poster_url') }}" placeholder="https://example.com/poster.jpg"
                    class="w-full px-5 py-4 bg-slate-50 border-2 border-slate-100 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-600 outline-none transition font-medium">
                @error('poster_url') <span class="text-red-500 text-sm mt-1 block font-semibold">{{ $message }}</span> @enderror
            </div>
        </div>

        {{-- Aksi --}}
        <div class="pt-4 flex justify-end gap-4 border-t border-slate-100">
            <a href="{{ route('admin.events.index') }}" class="px-6 py-4 text-slate-500 font-bold hover:text-slate-800 transition">Batal</a>
            <button type="submit" class="px-8 py-4 bg-indigo-600 text-white rounded-2xl font-bold shadow-lg shadow-indigo-100 hover:bg-indigo-700 transition">
                Simpan Event
            </button>
        </div>
    </form>
</div>

<script>
    let tierCount = 0;

    function addTierRow() {
        const wrapper = document.getElementById('tiers-wrapper');
        const helper = document.getElementById('no-tiers-helper');
        
        if (helper) helper.classList.add('hidden');
        
        const rowId = tierCount++;
        const html = `
            <div id="tier-row-${rowId}" class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm space-y-4 relative animate-in fade-in slide-in-from-top-4 duration-350">
                <button type="button" onclick="removeTierRow(${rowId})" class="absolute top-4 right-4 text-rose-500 hover:text-rose-700 font-bold text-xs">
                    Hapus Tier
                </button>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wide mb-1">Nama Kategori / Tier</label>
                        <input type="text" name="tiers[${rowId}][name]" placeholder="Contoh: Early Bird" required
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:border-indigo-600 text-xs font-semibold">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wide mb-1">Harga Tiket (Rp)</label>
                        <input type="number" name="tiers[${rowId}][price]" value="0" min="0" required
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:border-indigo-600 text-xs font-semibold">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wide mb-1">Kuota / Stok</label>
                        <input type="number" name="tiers[${rowId}][stock]" value="10" min="1" required
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:border-indigo-600 text-xs font-semibold">
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wide mb-1">Mulai Penjualan</label>
                        <input type="datetime-local" name="tiers[${rowId}][start_date]"
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:border-indigo-600 text-xs font-semibold">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wide mb-1">Berakhir Penjualan</label>
                        <input type="datetime-local" name="tiers[${rowId}][end_date]"
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:border-indigo-600 text-xs font-semibold">
                    </div>
                </div>
            </div>
        `;
        wrapper.insertAdjacentHTML('beforeend', html);
    }

    function removeTierRow(id) {
        const row = document.getElementById(`tier-row-${id}`);
        if (row) {
            row.remove();
        }
        
        const wrapper = document.getElementById('tiers-wrapper');
        if (wrapper.children.length === 0) {
            document.getElementById('no-tiers-helper').classList.remove('hidden');
        }
    }

    // 🔥 VALIDATOR TANGGAL DYNAMIC PRICING TIER (PREVENTION ERROR)
    document.getElementById('event-form').addEventListener('submit', function(e) {
        const eventDateVal = document.getElementsByName('date')[0].value;
        if (!eventDateVal) return;
        
        const eventDate = new Date(eventDateVal);
        let hasError = false;

        // Loop per baris tanggal akhir penjualan tier
        document.querySelectorAll('input[name*="[end_date]"]').forEach(input => {
            if (input.value) {
                const endDate = new Date(input.value);
                if (endDate > eventDate) {
                    hasError = true;
                    input.classList.add('border-rose-500', 'bg-rose-50');
                } else {
                    input.classList.remove('border-rose-500', 'bg-rose-50');
                }
            }
        });

        if (hasError) {
            alert("⚠️ Kesalahan Pengisian Tanggal:\nTanggal akhir penjualan tier tiket tidak boleh melebihi tanggal pelaksanaan event utama!");
            e.preventDefault();
        }
    });
</script>
@endsection