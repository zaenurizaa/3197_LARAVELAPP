@extends('layout.admin')

@section('title', 'Edit Status Transaksi - Admin')
@section('page_title', 'Kelola Status Transaksi')
@section('page_subtitle', 'Perbarui status pembayaran untuk Order ID: ' . $transaction->order_id)

<script src="https://unpkg.com/lucide@latest"></script>

@section('content')
<div class="max-w-3xl bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden p-8">
    
    <div class="mb-8">
        <h4 class="text-xs font-black uppercase tracking-widest text-slate-400 mb-4 flex items-center gap-2">
            <i data-lucide="clipboard-list" class="w-4 h-4"></i> Ringkasan Data Transaksi
        </h4>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="p-5 bg-slate-50/70 border border-slate-100 rounded-2xl flex items-start gap-4">
                <div class="p-2.5 bg-indigo-50 text-indigo-600 rounded-xl">
                    <i data-lucide="user" class="w-5 h-5"></i>
                </div>
                <div>
                    <span class="block text-[11px] font-bold text-slate-400 uppercase tracking-wide">Nama Pembeli</span>
                    <strong class="block text-sm text-slate-800 uppercase mt-0.5 truncate max-w-40">{{ $transaction->customer_name }}</strong>
                </div>
            </div>

            <div class="p-5 bg-slate-50/70 border border-slate-100 rounded-2xl flex items-start gap-4">
                <div class="p-2.5 bg-sky-50 text-sky-600 rounded-xl">
                    <i data-lucide="calendar" class="w-5 h-5"></i>
                </div>
                <div>
                    <span class="block text-[11px] font-bold text-slate-400 uppercase tracking-wide">Nama Event</span>
                    <strong class="block text-sm text-slate-800 mt-0.5 line-clamp-1">{{ $transaction->event->title ?? '-' }}</strong>
                </div>
            </div>

            <div class="p-5 bg-indigo-50/30 border border-indigo-100/50 rounded-2xl flex items-start gap-4">
                <div class="p-2.5 bg-emerald-50 text-emerald-600 rounded-xl">
                    <i data-lucide="banknote" class="w-5 h-5"></i>
                </div>
                <div>
                    <span class="block text-[11px] font-bold text-indigo-400 uppercase tracking-wide">Total Tagihan</span>
                    <strong class="block text-base text-emerald-600 font-black mt-0.5">Rp {{ number_format($transaction->total_price, 0, ',', '.') }}</strong>
                </div>
            </div>
        </div>
    </div>

    @php
        $user = Auth::guard('admin')->user() ?? Auth::guard('organizer')->user() ?? auth()->user();
        $isSuperAdmin = $user ? $user->isSuperAdmin() : false;
        $updateRoute = $isSuperAdmin ? route('admin.transactions.update', $transaction->id) : route('organizer.transactions.update', $transaction->id);
        $backRoute = $isSuperAdmin ? route('admin.transactions.index') : route('organizer.transactions.index');
    @endphp

    <form action="{{ $updateRoute }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-8">
            <label class="text-slate-700 font-bold uppercase tracking-wide text-xs mb-4 flex items-center gap-2">
                <i data-lucide="check-square" class="w-4 h-4 text-slate-400"></i> Pilih Status Pembayaran Baru
            </label>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                
                <label class="relative flex items-center p-5 border rounded-2xl cursor-pointer transition hover:bg-slate-50 {{ $transaction->status == 'Pending' ? 'border-indigo-500 bg-indigo-50/30 ring-1 ring-indigo-500' : 'border-slate-100' }}">
                    <input type="radio" name="status" value="Pending" class="w-4 h-4 text-indigo-600 border-slate-300 focus:ring-indigo-500" {{ $transaction->status == 'Pending' ? 'checked' : '' }}>
                    <div class="ml-4">
                        <span class="block font-black text-sm uppercase tracking-wide text-amber-600">Pending</span>
                        <span class="block text-xs text-slate-400 mt-0.5">Transaksi ditangguhkan / belum bayar</span>
                    </div>
                </label>

                <label class="relative flex items-center p-5 border rounded-2xl cursor-pointer transition hover:bg-slate-50 {{ in_array($transaction->status, ['Success', 'success', 'settlement']) ? 'border-indigo-500 bg-indigo-50/30 ring-1 ring-indigo-500' : 'border-slate-100' }}">
                    <input type="radio" name="status" value="Success" class="w-4 h-4 text-indigo-600 border-slate-300 focus:ring-indigo-500" {{ in_array($transaction->status, ['Success', 'success', 'settlement']) ? 'checked' : '' }}>
                    <div class="ml-4">
                        <span class="block font-black text-sm uppercase tracking-wide text-emerald-600">Success</span>
                        <span class="block text-xs text-slate-400 mt-0.5">Dana masuk, tiket otomatis valid</span>
                    </div>
                </label>

            </div>
        </div>

        <div class="flex items-center gap-3 border-t pt-6 border-slate-100">
            <button type="submit" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold text-sm shadow-md shadow-indigo-100 transition flex items-center gap-2">
                <i data-lucide="save" class="w-4 h-4"></i> Simpan Perubahan
            </button>
            <a href="{{ $backRoute }}" class="px-6 py-3 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl font-bold text-sm transition flex items-center gap-2">
                <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali
            </a>
        </div>
    </form>
</div>

<script>
    lucide.createIcons();
</script>
@endsection