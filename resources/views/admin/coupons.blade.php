@extends('layout.admin')

@section('title', 'Kelola Kupon Diskon - Admin')

@section('content')
<main class="flex-1 p-10 overflow-y-auto">
    <header class="flex justify-between items-center mb-10">
        <div>
            <h1 class="text-3xl font-black text-slate-900">Kelola Kupon Diskon</h1>
            <p class="text-slate-500 font-medium mt-1">Buat & atur kode voucher promo untuk pembeli tiket.</p>
        </div>
    </header>

    @if(session('success'))
        <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-2xl font-bold flex items-center justify-between">
            <span>✅ {{ session('success') }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Form Tambah Kupon -->
        <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm h-fit">
            <h2 class="text-xl font-black mb-6 text-slate-900">Tambah Kupon Baru</h2>

            <form action="{{ route('admin.coupons.store') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-2">Kode Kupon</label>
                    <input type="text" name="code" placeholder="Contoh: MAHASISWA50" required
                        class="w-full px-4 py-3 rounded-xl border border-slate-200 uppercase font-mono font-bold focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-2">Tipe Diskon</label>
                        <select name="type" required class="w-full px-3 py-3 rounded-xl border border-slate-200 text-sm font-bold text-slate-700 outline-none">
                            <option value="fixed">Nominal (Rp)</option>
                            <option value="percent">Persentase (%)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-2">Nilai Diskon</label>
                        <input type="number" name="discount_amount" placeholder="Misal: 50000 atau 50" required min="1"
                            class="w-full px-4 py-3 rounded-xl border border-slate-200 font-bold focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-2">Batas Pakai</label>
                        <input type="number" name="max_uses" placeholder="Opsional (mis: 100)" min="1"
                            class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-500 outline-none text-sm font-medium">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-2">Kadaluarsa</label>
                        <input type="date" name="expires_at"
                            class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-500 outline-none text-sm font-medium">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-2">Khusus Event (Opsional)</label>
                    <select name="event_id" class="w-full px-3 py-3 rounded-xl border border-slate-200 text-sm font-medium text-slate-700 outline-none">
                        <option value="">Berlaku Semua Event</option>
                        @foreach($events as $ev)
                            <option value="{{ $ev->id }}">{{ $ev->title }}</option>
                        @endforeach
                    </select>
                </div>

                <button type="submit" class="w-full py-4 bg-indigo-600 hover:bg-indigo-700 text-white rounded-2xl font-black text-sm shadow-lg shadow-indigo-200 transition">
                    + Simpan Kupon
                </button>
            </form>
        </div>

        <!-- Daftar Kupon -->
        <div class="lg:col-span-2 bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden">
            <div class="px-8 py-6 border-b bg-slate-50/50">
                <h3 class="font-black text-lg text-slate-800">Daftar Kupon Aktif</h3>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-slate-50 text-slate-400 uppercase text-[10px] font-black tracking-widest">
                        <tr>
                            <th class="px-6 py-4">Kode</th>
                            <th class="px-6 py-4">Diskon</th>
                            <th class="px-6 py-4">Digunakan</th>
                            <th class="px-6 py-4">Event</th>
                            <th class="px-6 py-4">Kadaluarsa</th>
                            <th class="px-6 py-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y border-t">
                        @forelse($coupons as $c)
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="px-6 py-4">
                                    <span class="font-mono font-black text-indigo-600 bg-indigo-50 px-3 py-1 rounded-lg text-sm">
                                        {{ $c->code }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 font-bold text-slate-800">
                                    @if($c->type === 'percent')
                                        {{ $c->discount_amount }}%
                                    @else
                                        Rp {{ number_format($c->discount_amount, 0, ',', '.') }}
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm font-semibold text-slate-600">
                                    {{ $c->used_count }} {{ $c->max_uses ? '/ ' . $c->max_uses : '' }}
                                </td>
                                <td class="px-6 py-4 text-xs font-semibold text-slate-600">
                                    {{ $c->event->title ?? 'Semua Event' }}
                                </td>
                                <td class="px-6 py-4 text-xs text-slate-500 font-medium">
                                    {{ $c->expires_at ? $c->expires_at->format('d M Y') : 'Tanpa Batas' }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <form action="{{ route('admin.coupons.destroy', $c->id) }}" method="POST" onsubmit="return confirm('Hapus kupon {{ $c->code }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-3 py-1.5 bg-rose-50 text-rose-600 hover:bg-rose-600 hover:text-white rounded-lg text-xs font-bold transition border border-rose-200">
                                            Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-8 py-10 text-center text-slate-400 font-medium">
                                    Belum ada kupon diskon. Buat kupon pertama Anda di sebelah kiri.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($coupons->hasPages())
            <div class="px-6 py-4 bg-slate-50/50 border-t">
                {{ $coupons->links() }}
            </div>
            @endif
        </div>

    </div>
</main>
@endsection
