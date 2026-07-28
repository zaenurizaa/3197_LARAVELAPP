@extends('layout.admin')

@section('title', 'Manajemen Organizer - Admin')
@section('page_title', 'Manajemen Organizer')
@section('page_subtitle', 'Pantau organisasi penyelenggara dan status aktivitas mereka.')

@section('content')
<div class="space-y-8">
    
    <!-- Bagian Search bar dinamis sesuai mockup -->
    <div class="flex items-center gap-3">
        <form action="{{ route('admin.tenants.index') }}" method="GET" class="flex gap-2 w-full md:w-auto">
            <input type="text" name="search" placeholder="Cari nama organisasi..." 
                   value="{{ request('search') }}"
                   class="w-full md:w-80 px-4 py-3 rounded-2xl border border-slate-200 text-slate-800 font-medium text-xs focus:outline-none focus:border-indigo-600 bg-white shadow-sm">
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-2xl font-bold text-xs transition shadow-sm shrink-0 uppercase tracking-wider">
                Cari
            </button>
        </form>
    </div>

    <!-- Alert status -->
    @if(session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-100 text-emerald-600 rounded-2xl font-bold flex items-center gap-2 shadow-sm text-xs">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            {{ session('success') }}
        </div>
    @endif
    @if(session('info'))
        <div class="p-4 bg-blue-50 border border-blue-100 text-blue-600 rounded-2xl font-bold flex items-center gap-2 shadow-sm text-xs">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            {{ session('info') }}
        </div>
    @endif

    <!-- Grid Manajemen Organizer Card persis gambar referensi terakhir -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($tenants as $tenant)
            <div class="bg-white rounded-[2rem] border border-slate-200 p-6 shadow-sm hover:shadow-md transition flex flex-col justify-between">
                
                <!-- Detail Atas -->
                <div class="flex items-start gap-4">
                    <!-- Icon Huruf Avatar Bulat Abu-abu -->
                    <div class="w-14 h-14 bg-slate-100 text-indigo-900 rounded-2xl flex items-center justify-center font-bold text-xl shadow-inner border border-slate-200">
                        {{ strtoupper(substr($tenant->name, 0, 1)) }}
                    </div>
                    
                    <div class="space-y-1">
                        <h4 class="font-black text-slate-800 text-base leading-tight">{{ $tenant->name }}</h4>
                        
                        <!-- Badge Status Dinamis (Aktif, Menunggu ACC, Ditolak) -->
                        <div class="text-xs">
                            <span class="text-slate-400 font-medium">Status:</span>
                            @if($tenant->status === 'verified')
                                <span class="text-emerald-600 font-bold">Aktif</span>
                            @elseif($tenant->status === 'pending')
                                <span class="text-amber-500 font-bold">Menunggu ACC</span>
                            @elseif($tenant->status === 'rejected')
                                <span class="text-rose-500 font-bold">Ditolak</span>
                            @else
                                <span class="text-slate-500 font-bold">{{ $tenant->status }}</span>
                            @endif
                        </div>

                        <div class="text-xs text-slate-400 font-medium space-y-0.5">
                            <p>Jumlah event: <span class="font-bold text-slate-700">{{ $tenant->events_count ?? 0 }}</span></p>
                            <p>Event aktif: <span class="font-bold text-slate-700">{{ $tenant->events_count ?? 0 }}</span></p>
                        </div>
                    </div>
                </div>

                <!-- Bagian Bawah: Tombol Aksi Detail / Verifikasi -->
                <div class="flex gap-2 mt-6 pt-4 border-t border-slate-100">
                    
                    <!-- Tombol Detail Rekening / Owner info -->
                    <button onclick="openDetailModal('{{ $tenant->name }}', '{{ $tenant->users->first()->name ?? '-' }}', '{{ $tenant->users->first()->email ?? '-' }}', '{{ $tenant->bank_name ?? '-' }}', '{{ $tenant->bank_account_number ?? '-' }}', '{{ $tenant->bank_account_holder ?? '-' }}')" 
                            class="flex-1 py-3 bg-indigo-600 text-white hover:bg-indigo-700 rounded-xl font-bold text-xs uppercase tracking-wider transition text-center shadow-sm">
                        Detail
                    </button>

                    <!-- Tombol Verifikasi (Tampil jika status pending) -->
                    @if($tenant->status === 'pending')
                        <div class="flex gap-1.5 flex-1">
                            <form action="{{ route('admin.tenants.approve', $tenant->id) }}" method="POST" class="w-1/2">
                                @csrf @method('PATCH')
                                <button type="submit" class="w-full py-3 bg-emerald-500 hover:bg-emerald-600 text-white rounded-xl font-bold text-xs uppercase tracking-wider transition">
                                    ACC
                                </button>
                            </form>
                            <form action="{{ route('admin.tenants.reject', $tenant->id) }}" method="POST" class="w-1/2">
                                @csrf @method('PATCH')
                                <button type="submit" class="w-full py-3 bg-rose-500 hover:bg-rose-600 text-white rounded-xl font-bold text-xs uppercase tracking-wider transition">
                                    Tolak
                                </button>
                            </form>
                        </div>
                    @else
                        <!-- Tombol Hapus/Akhiri jika status sudah ACC -->
                        <form action="{{ route('admin.tenants.destroy', $tenant->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus/mengakhiri kemitraan dengan {{ $tenant->name }}?')" class="flex-1">
                            @csrf @method('DELETE')
                            <button type="submit" class="w-full py-3 bg-slate-100 hover:bg-rose-50 text-slate-500 hover:text-rose-600 rounded-xl font-bold text-xs uppercase tracking-wider transition border border-slate-200">
                                Akhiri
                            </button>
                        </form>
                    @endif

                </div>

            </div>
        @empty
            <div class="col-span-full py-16 text-center text-slate-400 italic text-sm">
                Belum ada data organizer yang terdaftar atau ditemukan.
            </div>
        @endforelse
    </div>

</div>

<!-- Modal Pop-up Detail Rekening & Penanggung Jawab -->
<div id="detail-modal" class="hidden fixed inset-0 z-50 bg-black/40 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl p-6 md:p-8 max-w-md w-full shadow-2xl border border-slate-100 transform scale-95 opacity-0 transition-all duration-300" id="detail-modal-card">
        <div class="flex justify-between items-center mb-6">
            <h4 class="text-lg font-black text-slate-800" id="modal-title">Info Tenant</h4>
            <button onclick="closeDetailModal()" class="text-slate-400 hover:text-slate-700 text-2xl font-bold">&times;</button>
        </div>

        <div class="space-y-4 text-xs">
            <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100">
                <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Penanggung Jawab</span>
                <p class="font-bold text-slate-800 text-sm" id="modal-owner-name">-</p>
                <p class="text-slate-500 mt-0.5" id="modal-owner-email">-</p>
            </div>

            <div class="p-4 bg-indigo-50/50 rounded-2xl border border-indigo-100/60">
                <span class="block text-[10px] font-bold text-indigo-400 uppercase tracking-wider mb-2">Rekening Pencairan Dana</span>
                <div class="space-y-1.5">
                    <p class="flex justify-between"><span class="text-slate-400">Bank:</span> <strong class="text-slate-700" id="modal-bank-name">-</strong></p>
                    <p class="flex justify-between"><span class="text-slate-400">Rekening:</span> <strong class="text-slate-700" id="modal-bank-account">-</strong></p>
                    <p class="flex justify-between"><span class="text-slate-400">Atas Nama:</span> <strong class="text-slate-700" id="modal-bank-holder">-</strong></p>
                </div>
            </div>
        </div>

        <button onclick="closeDetailModal()" class="w-full mt-6 py-3.5 bg-slate-900 hover:bg-black text-white rounded-xl font-bold text-xs uppercase tracking-wider transition">
            Tutup Detail
        </button>
    </div>
</div>

<script>
    function openDetailModal(name, owner, email, bank, account, holder) {
        document.getElementById('modal-title').innerText = name;
        document.getElementById('modal-owner-name').innerText = owner;
        document.getElementById('modal-owner-email').innerText = email;
        document.getElementById('modal-bank-name').innerText = bank;
        document.getElementById('modal-bank-account').innerText = account;
        document.getElementById('modal-bank-holder').innerText = holder;

        const modal = document.getElementById('detail-modal');
        const card = document.getElementById('detail-modal-card');
        modal.classList.remove('hidden');
        setTimeout(() => {
            card.classList.remove('scale-95', 'opacity-0');
            card.classList.add('scale-100', 'opacity-100');
        }, 50);
    }

    function closeDetailModal() {
        const modal = document.getElementById('detail-modal');
        const card = document.getElementById('detail-modal-card');
        card.classList.remove('scale-100', 'opacity-100');
        card.classList.add('scale-95', 'opacity-0');
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 200);
    }
</script>
@endsection