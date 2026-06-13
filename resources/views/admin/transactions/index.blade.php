@extends('layout.admin') 

@section('title', 'Laporan Transaksi - Admin')
@section('page_title', 'Laporan Transaksi')
@section('page_subtitle', 'Pantau arus kas dan penjualan tiket Anda.')

<script src="https://unpkg.com/lucide@latest"></script>

@section('content')

@if(session('success'))
<div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-2xl font-bold text-sm flex items-center shadow-sm">
    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
    </svg>
    {{ session('success') }}
</div>
@endif

<div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead class="bg-slate-50 text-slate-400 uppercase text-[10px] font-black tracking-widest">
                <tr>
                    <th class="px-8 py-4">Order ID</th>
                    <th class="px-8 py-4">Detail Pembeli</th>
                    <th class="px-8 py-4">Event</th>
                    <th class="px-8 py-4">Tgl Transaksi</th>
                    <th class="px-8 py-4">Status</th>
                    <th class="px-8 py-4">Total Tagihan</th>
                    <th class="px-8 py-4 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y border-t">
                @forelse($transactions as $trx)
                <tr class="hover:bg-slate-50/50 transition {{ $trx->status == 'Pending' ? 'text-slate-400' : '' }}">
                    <td class="px-8 py-6">
                        <span class="font-mono font-bold px-3 py-1 rounded-lg text-sm {{ $trx->status == 'Pending' ? 'bg-slate-100' : 'text-indigo-600 bg-indigo-50' }}">
                            {{ $trx->order_id }}
                        </span>
                    </td>
                    <td class="px-8 py-6">
                        <p class="font-bold text-slate-800">{{ $trx->customer_name }}</p>
                        <p class="text-xs text-slate-500">{{ $trx->customer_email }}<br>{{ $trx->customer_phone }}</p>
                    </td>
                    <td class="px-8 py-6">
                        <p class="font-medium text-slate-700">{{ $trx->event->title ?? '-' }}</p>
                    </td>
                    <td class="px-8 py-6 text-sm text-slate-500">
                        {{ $trx->created_at->format('d M Y, H:i') }}
                    </td>
                    <td class="px-8 py-6">
                        @if($trx->status === 'Success' || $trx->status === 'success' || $trx->status === 'settlement')
                            <span class="px-3 py-1 bg-green-100 text-green-700 rounded-lg text-xs font-bold uppercase ring-1 ring-green-200">Success</span>
                        @elseif($trx->status === 'Pending')
                            <span class="px-3 py-1 bg-orange-100 text-orange-700 rounded-lg text-xs font-bold uppercase ring-1 ring-orange-200">Pending</span>
                        @else
                            <span class="px-3 py-1 bg-rose-100 text-rose-700 rounded-lg text-xs font-bold uppercase ring-1 ring-rose-200">{{ $trx->status }}</span>
                        @endif
                    </td>
                    <td class="px-8 py-6 font-black {{ $trx->status == 'Pending' ? '' : 'text-slate-900' }}">
                        Rp {{ number_format($trx->total_price, 0, ',', '.') }}
                    </td>
                    
                    <td class="px-8 py-6">
                        <div class="flex justify-center gap-3">
                            
                            <a href="{{ route('admin.transactions.edit', $trx->id) }}" 
                               class="w-10 h-10 flex items-center justify-center rounded-xl bg-indigo-50 text-indigo-600 hover:bg-indigo-600 hover:text-white transition shadow-sm">
                                <i data-lucide="file-edit" class="w-5 h-5"></i>
                            </a>

                            <form action="{{ route('admin.transactions.destroy', $trx->id) }}" method="POST" onsubmit="return confirm('Apakah kamu yakin ingin menghapus data transaksi {{ $trx->order_id }} secara permanen?')">
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
                    <td colspan="7" class="px-8 py-10 text-center text-slate-500 italic text-sm">
                        Belum ada transaksi atau data tidak ditemukan.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="px-8 py-6 bg-slate-50/50 border-t items-center">
        {{ $transactions->links() }}
    </div>
</div>

<script>
    lucide.createIcons();
</script>
@endsection