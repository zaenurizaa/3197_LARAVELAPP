@extends('layout.admin')

@section('title', 'Kelola Event')
@section('header_title', 'Kelola Event')
@section('header_subtitle', 'Buat dan atur acara seru Anda di sini.')

@section('content')
<div x-data="{ openAddModal: false, openEditModal: false, editData: {} }">
    
    <div class="mb-6 flex justify-end">
        <button @click="openAddModal = true" class="px-6 py-3 bg-indigo-600 text-white rounded-2xl font-bold shadow-lg shadow-indigo-100 hover:bg-indigo-700 active:scale-95 transition">
            + Tambah Event Baru
        </button>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-2xl font-bold">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-slate-50 text-slate-400 uppercase text-[10px] font-black tracking-widest">
                    <tr>
                        <th class="px-8 py-4 w-16">No</th>
                        <th class="px-8 py-4">Poster</th>
                        <th class="px-8 py-4">Event</th>
                        <th class="px-8 py-4">Harga / Stok</th>
                        <th class="px-8 py-4">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y border-t">
                    @forelse($events as $index => $event)
                    <tr class="hover:bg-slate-50/50 transition">
                        <td class="px-8 py-6 font-bold text-slate-400">{{ $index + 1 }}</td>
                        <td class="px-8 py-6">
                            <div class="w-16 h-20 bg-slate-200 rounded-xl overflow-hidden shadow-sm">
                                @if($event->poster_path)
                                    <img src="{{ asset('storage/' . $event->poster_path) }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-[10px] text-slate-400">No Image</div>
                                @endif
                            </div>
                        </td>
                        <td class="px-8 py-6">
                            <p class="font-black text-slate-800">{{ $event->title }}</p>
                            <p class="text-xs text-slate-400">{{ $event->category->name ?? 'Tanpa Kategori' }} • {{ \Carbon\Carbon::parse($event->date)->format('d M Y') }}</p>
                        </td>
                        <td class="px-8 py-6">
                            <p class="font-bold text-indigo-600">Rp {{ number_format($event->price, 0, ',', '.') }}</p>
                            <p class="text-xs text-slate-400">Stok: {{ $event->stock }}</p>
                        </td>
                        <td class="px-8 py-6">
                            <div class="flex gap-2">
                                <button @click="editData = {{ $event }}; openEditModal = true" class="p-2.5 bg-indigo-50 text-indigo-600 rounded-xl hover:bg-indigo-600 hover:text-white transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 00-2 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </button>
                                <form action="{{ route('admin.events.destroy', $event->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus event ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-2.5 bg-rose-50 text-rose-600 rounded-xl hover:bg-rose-600 hover:text-white transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-8 py-10 text-center text-slate-400 font-medium">Belum ada data event.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div x-show="openAddModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm" x-cloak>
        <div @click.away="openAddModal = false" class="bg-white rounded-[2.5rem] shadow-2xl w-full max-w-lg overflow-hidden transition-all">
            <div class="px-10 py-8">
                <h3 class="text-2xl font-black mb-6">Tambah Event</h3>
                <form action="{{ route('admin.events.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold uppercase text-slate-400 mb-2">Judul Event</label>
                        <input type="text" name="title" required class="w-full px-5 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-500 outline-none transition">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold uppercase text-slate-400 mb-2">Kategori</label>
                            <select name="category_id" required class="w-full px-5 py-3 rounded-xl border border-slate-200 outline-none">
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase text-slate-400 mb-2">Tanggal</label>
                            <input type="datetime-local" name="date" required class="w-full px-5 py-3 rounded-xl border border-slate-200 outline-none">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase text-slate-400 mb-2">Lokasi</label>
                        <input type="text" name="location" required class="w-full px-5 py-3 rounded-xl border border-slate-200 outline-none">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold uppercase text-slate-400 mb-2">Harga (Rp)</label>
                            <input type="number" name="price" required class="w-full px-5 py-3 rounded-xl border border-slate-200 outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase text-slate-400 mb-2">Stok</label>
                            <input type="number" name="stock" required class="w-full px-5 py-3 rounded-xl border border-slate-200 outline-none">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase text-slate-400 mb-2">Poster</label>
                        <input type="file" name="poster" class="text-sm">
                    </div>
                    <div class="pt-4 flex gap-3">
                        <button type="button" @click="openAddModal = false" class="flex-1 py-4 bg-slate-100 text-slate-600 rounded-2xl font-bold">Batal</button>
                        <button type="submit" class="flex-1 py-4 bg-indigo-600 text-white rounded-2xl font-bold shadow-lg">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div x-show="openEditModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm" x-cloak>
        <div @click.away="openEditModal = false" class="bg-white rounded-[2.5rem] shadow-2xl w-full max-w-lg overflow-hidden transition-all">
            <div class="px-10 py-8">
                <h3 class="text-2xl font-black mb-6">Edit Event</h3>
                <form :action="`/admin/events/${editData.id}`" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf @method('PUT')
                    <div>
                        <label class="block text-xs font-bold uppercase text-slate-400 mb-2">Judul Event</label>
                        <input type="text" name="title" x-model="editData.title" required class="w-full px-5 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-500 outline-none transition">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold uppercase text-slate-400 mb-2">Kategori</label>
                            <select name="category_id" x-model="editData.category_id" required class="w-full px-5 py-3 rounded-xl border border-slate-200 outline-none">
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase text-slate-400 mb-2">Harga</label>
                            <input type="number" name="price" x-model="editData.price" required class="w-full px-5 py-3 rounded-xl border border-slate-200 outline-none">
                        </div>
                    </div>
                    <div class="pt-4 flex gap-3">
                        <button type="button" @click="openEditModal = false" class="flex-1 py-4 bg-slate-100 text-slate-600 rounded-2xl font-bold">Batal</button>
                        <button type="submit" class="flex-1 py-4 bg-indigo-600 text-white rounded-2xl font-bold shadow-lg">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>

<script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
@endsection