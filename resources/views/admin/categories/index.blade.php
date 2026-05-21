@extends('layout.admin')

@section('title', 'Kelola Kategori')
@section('page_title', 'Kelola Kategori')
@section('page_subtitle', 'Buat dan atur kategori acara Anda di sini.')

@section('content')
<div class="space-y-8">
    
    
    <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm">
        <h2 class="text-xl font-bold mb-4 text-slate-800">Tambah Kategori Baru</h2>
        <form action="{{ route('admin.categories.store') }}" method="POST" class="flex flex-col md:flex-row gap-4 items-end">
            @csrf
            <div class="flex-1 space-y-2 w-full">
                <label class="text-sm font-semibold text-slate-600">Nama Kategori</label>
                <input type="text" name="name" placeholder="Masukkan nama kategori baru (cth: Workshop, Konser)" required
                       class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:outline-none focus:border-indigo-600 transition">
            </div>
            <button type="submit" class="px-6 py-2.5 bg-indigo-600 text-white rounded-xl font-bold hover:bg-indigo-700 transition shadow-md shadow-indigo-100 w-full md:w-auto whitespace-nowrap">
                + Kategori
            </button>
        </form>
    </div>

    
    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
        
        
        <div class="p-6 border-b border-slate-50 bg-slate-50/50">
            <form action="{{ route('admin.categories.index') }}" method="GET" class="flex gap-3 max-w-md">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama kategori..."
                       class="w-full px-4 py-2 border border-slate-200 rounded-xl focus:outline-none focus:border-indigo-600 bg-white transition">
                <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-xl font-bold hover:bg-indigo-700 transition shadow-md shadow-indigo-100">
                    Cari
                </button>
                @if(request('search'))
                    <a href="{{ route('admin.categories.index') }}" class="px-4 py-2 bg-slate-200 text-slate-700 rounded-xl font-bold flex items-center justify-center hover:bg-slate-300 transition">
                        Reset
                    </a>
                @endif
            </form>
        </div>

        
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-100 text-slate-400 text-xs font-bold uppercase tracking-wider bg-slate-50/30">
                        <th class="py-4 px-6 w-20">No</th>
                        <th class="py-4 px-6">Nama Kategori</th>
                        <th class="py-4 px-6 text-center w-40">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 text-sm font-medium text-slate-700">
                    @forelse($categories as $index => $cat)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-4 px-6 text-slate-400">{{ $index + 1 }}</td>
                            <td class="py-4 px-6 font-semibold text-slate-800">
                                {{ $cat->name }}
                            </td>
                            <td class="py-4 px-6">
                                <div class="flex justify-center gap-2">
                                    
                                    <a href="{{ route('admin.categories.edit', $cat->id) }}" title="Edit Kategori" class="p-2 bg-indigo-50 text-indigo-600 rounded-xl hover:bg-indigo-600 hover:text-white transition">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>

                                    
                                    <form action="{{ route('admin.categories.destroy', $cat->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus kategori ini?')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" title="Hapus Kategori" class="p-2 bg-rose-50 text-rose-600 rounded-xl hover:bg-rose-600 hover:text-white transition">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="py-12 text-center text-slate-400 italic font-medium">
                                Tidak ada data kategori ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection