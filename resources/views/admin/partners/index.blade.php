@extends('layout.admin')

@section('title', 'Kelola Partner')
@section('page_title', 'Kelola Partner')
@section('page_subtitle', 'Manajemen kemitraan AmikomEventHub')

{{-- Tambahkan script ikon di sini --}}
<script src="https://unpkg.com/lucide@latest"></script>

@section('content')
<div class="space-y-8">
    {{-- Form Tambah Partner --}}
    <div class="bg-white p-8 rounded-3xl shadow-sm border border-slate-100">
        <h2 class="text-xl font-bold mb-6">Tambah Partner Baru</h2>
        <form action="{{ route('partners.store') }}" method="POST" class="flex flex-col md:flex-row gap-4 items-end">
            @csrf
            <div class="flex-1 space-y-2">
                <label class="text-sm font-bold text-slate-600 ml-1">Nama Partner</label>
                <input type="text" name="name" class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition" placeholder="Masukkan nama partner" required>
            </div>
            <div class="flex-1 space-y-2">
                <label class="text-sm font-bold text-slate-600 ml-1">Logo URL</label>
                <input type="text" name="logo_url" class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition" value="https://placehold.co/200x200" required>
            </div>
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-8 py-3 rounded-xl font-bold transition shadow-lg shadow-indigo-200">
                Simpan
            </button>
        </form>
    </div>

    <div class="flex justify-between items-center gap-4">
        <form action="{{ route('partners.index') }}" method="GET" class="flex gap-2 w-full md:w-96">
            <div class="relative w-full">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama partner..." 
                       class="w-full pl-4 pr-10 py-2.5 rounded-xl bg-white border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm transition">
                @if(request('search'))
                    <a href="{{ route('partners.index') }}" class="absolute right-3 top-3 text-slate-400 hover:text-slate-600 text-xs font-semibold">Clear</a>
                @endif
            </div>
            <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-xl font-bold hover:bg-indigo-700 transition shadow-lg shadow-indigo-100">
                Cari
            </button>
        </form>
    </div>

    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead class="bg-slate-50 border-b border-slate-100">
                <tr>
                    <th class="px-6 py-4 text-xs font-bold uppercase tracking-widest text-slate-400">Logo</th>
                    <th class="px-6 py-4 text-xs font-bold uppercase tracking-widest text-slate-400">Nama Partner</th>
                    <th class="px-6 py-4 text-xs font-bold uppercase tracking-widest text-slate-400 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($partners as $partner)
                <tr class="hover:bg-slate-50/50 transition">
                    <td class="px-6 py-4 text-center w-24">
                        <img src="{{ $partner->logo_url }}" class="w-12 h-12 rounded-xl object-cover border border-slate-200">
                    </td>
                    <td class="px-6 py-4 font-bold text-slate-700">{{ $partner->name }}</td>
                    <td class="px-6 py-4">
                        <div class="flex justify-center gap-3">
                            {{-- Tombol Edit (Ikon Biru) --}}
                            <a href="{{ route('partners.edit', $partner->id) }}" 
                               class="w-10 h-10 flex items-center justify-center rounded-xl bg-indigo-50 text-indigo-600 hover:bg-indigo-600 hover:text-white transition shadow-sm">
                                <i data-lucide="file-edit" class="w-5 h-5"></i>
                            </a>

                            {{-- Tombol Hapus (Ikon Merah) --}}
                            <form action="{{ route('partners.destroy', $partner->id) }}" method="POST" onsubmit="return confirm('Yakin hapus?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="w-10 h-10 flex items-center justify-center rounded-xl bg-red-50 text-red-600 hover:bg-red-600 hover:text-white transition shadow-sm">
                                    <i data-lucide="trash-2" class="w-5 h-5"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="px-6 py-10 text-center text-slate-400 text-sm italic">
                        Data partner tidak ditemukan atau belum ditambahkan.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Inisialisasi Ikon --}}
<script>
    lucide.createIcons();
</script>
@endsection