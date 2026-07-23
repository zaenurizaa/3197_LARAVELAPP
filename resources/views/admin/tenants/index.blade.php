@extends('layout.admin')

@section('content')
<div class="p-6">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Kelola & ACC Tenant</h1>
        <p class="text-sm text-gray-500">Verifikasi pendaftaran organisasi baru dan kelola mitra kerja sama aktif.</p>
    </div>

    @if(session('success'))
        <div class="mb-4 p-4 bg-emerald-100 border border-emerald-400 text-emerald-700 rounded-lg">
            {{ session('success') }}
        </div>
    @endif
    @if(session('info'))
        <div class="mb-4 p-4 bg-blue-100 border border-blue-400 text-blue-700 rounded-lg">
            {{ session('info') }}
        </div>
    @endif

    <!-- TABEL 1: MENUNGGU PERSETUJUAN -->
    <div class="bg-white rounded-xl shadow-sm border p-6 mb-8">
        <h2 class="text-lg font-bold text-gray-800 mb-4">⏳ Menunggu Persetujuan ({{ $pendingTenants->count() }})</h2>
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b bg-gray-50 text-xs font-semibold text-gray-500 uppercase">
                    <th class="p-3">Organisasi</th>
                    <th class="p-3">Penanggung Jawab</th>
                    <th class="p-3">Rekening Pencairan</th>
                    <th class="p-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y text-sm">
                @forelse($pendingTenants as $tenant)
                <tr class="hover:bg-gray-50">
                    <td class="p-3 font-semibold">{{ $tenant->name }}</td>
                    <td class="p-3">{{ $tenant->users->first()->name ?? '-' }}</td>
                    <td class="p-3">{{ $tenant->bank_name }} - {{ $tenant->bank_account_number }}</td>
                    <td class="p-3 text-center space-x-2">
                        <form action="{{ route('admin.tenants.approve', $tenant->id) }}" method="POST" class="inline">
                            @csrf @method('PATCH')
                            <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold py-1.5 px-3 rounded-lg">
                                ✓ Setujui (ACC)
                            </button>
                        </form>
                        <form action="{{ route('admin.tenants.reject', $tenant->id) }}" method="POST" class="inline">
                            @csrf @method('PATCH')
                            <button type="submit" class="bg-rose-500 hover:bg-rose-600 text-white text-xs font-bold py-1.5 px-3 rounded-lg">
                                ✕ Tolak
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" class="p-4 text-center text-gray-400">Tidak ada pengajuan baru.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- TABEL 2: TENANT AKTIF / KERJA SAMA -->
    <div class="bg-white rounded-xl shadow-sm border p-6">
        <h2 class="text-lg font-bold text-gray-800 mb-4">🤝 Tenant Aktif & Bekerja Sama</h2>
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b bg-gray-50 text-xs font-semibold text-gray-500 uppercase">
                    <th class="p-3">Organisasi</th>
                    <th class="p-3">Penanggung Jawab</th>
                    <th class="p-3">Status</th>
                    <th class="p-3 text-center">Kelola Kerja Sama</th>
                </tr>
            </thead>
            <tbody class="divide-y text-sm">
                @forelse($approvedTenants as $tenant)
                <tr class="hover:bg-gray-50">
                    <td class="p-3 font-semibold">{{ $tenant->name }}</td>
                    <td class="p-3">{{ $tenant->users->first()->name ?? '-' }}</td>
                    <td class="p-3"><span class="bg-emerald-100 text-emerald-800 text-xs px-2.5 py-1 rounded-full font-semibold">Active</span></td>
                    <td class="p-3 text-center">
                        <form action="{{ route('admin.tenants.destroy', $tenant->id) }}" method="POST" onsubmit="return confirm('Akhiri kerja sama?')" class="inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="bg-gray-800 hover:bg-black text-white text-xs font-semibold py-1.5 px-3 rounded-lg">
                                🛑 Akhiri Kerja Sama
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" class="p-4 text-center text-gray-400">Belum ada tenant aktif.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection