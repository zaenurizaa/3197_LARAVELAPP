@extends('layout.app')

@section('title', 'Checkout - AmikomEventHub')

@section('content')
    <main class="max-w-3xl mx-auto px-6 py-20">
        <div class="mb-12">
            <a href="{{ route('events.show', $event->id) }}" class="text-indigo-600 font-bold flex items-center gap-2 mb-6">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Kembali ke Event
            </a>
            <h1 class="text-4xl font-extrabold">Checkout</h1>
            <p class="text-slate-500 mt-2">Lengkapi data Anda untuk mendapatkan tiket.</p>
        </div>

        @if(session('error'))
        <div class="mb-6 p-4 bg-red-100 text-red-700 rounded-xl font-bold">
            {{ session('error') }}
        </div>
        @endif

        <div class="grid grid-cols-1 gap-8">
            {{-- Box Ringkasan Pesanan --}}
            <div class="bg-white rounded-4xl border border-slate-200 p-8 shadow-sm">
                <h3 class="text-xl font-bold mb-6 border-b pb-4">Pesanan Anda</h3>
                <div class="flex gap-6 items-start">
                    <img src="{{ ($event->poster_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($event->poster_path))
                                 ? asset('storage/' . $event->poster_path)
                                 : asset('storage/asset_admin/concert.png') }}" 
                         alt="{{ $event->title }}" class="w-24 h-24 rounded-2xl object-cover">
                    <div>
                        <h4 class="font-extrabold text-lg">{{ $event->title }}</h4>
                        <p class="text-slate-500">
                            {{ $event->date instanceof \Carbon\Carbon ? $event->date->format('d M Y') : \Carbon\Carbon::parse($event->date)->format('d M Y') }} 
                            • {{ $event->location }}
                        </p>
                        <p class="text-indigo-600 font-bold mt-2">1 x Rp {{ number_format($event->price, 0, ',', '.') }}</p>
                    </div>
                </div>
                <div class="mt-8 pt-6 border-t space-y-3">
                    <div class="flex justify-between text-slate-500">
                        <span>Harga Tiket</span>
                        <span>Rp {{ number_format($event->price, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-slate-500">
                        <span>Biaya Layanan</span>
                        <span>Rp 5.000</span>
                    </div>
                    <div class="flex justify-between text-2xl font-black mt-4 pt-4 border-t">
                        <span>Total Bayar</span>
                        <span class="text-indigo-600">Rp {{ number_format($event->price + 5000, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            {{-- Box Form Data Pemesan --}}
            <div class="bg-white rounded-3xl border border-slate-200 p-8 shadow-sm">
                <h3 class="text-xl font-bold mb-6 italic text-indigo-600 underline underline-offset-8">📦 Data Pemesan (Tanpa Login)</h3>
                
                <form id="checkout-form" action="{{ route('checkout.store', $event->id) }}" method="POST" class="space-y-6">
                    @csrf
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-wide">Nama Lengkap</label>
                        <input type="text" id="customer_name" name="customer_name" placeholder="Masukkan nama sesuai identitas"
                            class="w-full px-5 py-4 bg-white border-2 border-slate-100 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-600 outline-none transition font-medium"
                            required value="{{ old('customer_name') }}">
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-wide">Email Aktif</label>
                            <input type="email" name="customer_email" placeholder="contoh@gmail.com"
                                class="w-full px-5 py-4 bg-white border-2 border-slate-100 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-600 outline-none transition font-medium"
                                required value="{{ old('customer_email') }}">
                            <p class="text-[10px] text-slate-400 mt-2 font-bold uppercase tracking-tighter">*E-Ticket akan dikirim ke email ini</p>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-wide">No. WhatsApp</label>
                            <input type="tel" name="customer_phone" placeholder="08xxxxxxx"
                                class="w-full px-5 py-4 bg-white border-2 border-slate-100 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-600 outline-none transition font-medium"
                                required value="{{ old('customer_phone') }}">
                        </div>
                    </div>

                    <button type="submit"
                        class="w-full py-5 bg-indigo-600 text-white rounded-2xl font-black text-xl shadow-xl shadow-indigo-200 hover:bg-indigo-700 active:scale-95 transition-all">
                        Bayar Sekarang
                    </button>
                </form>
            </div>
        </div>
    </main>

    {{-- Overlay / Pop-up Midtrans --}}
    <div id="midtrans-overlay" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 hidden items-center justify-center p-6">
        <div class="bg-white w-full max-w-sm rounded-4xl overflow-hidden shadow-2xl animate-bounce-in">
            <div class="bg-slate-50 p-6 flex justify-between items-center border-b">
                {{-- Memanggil file midtrans.png lokal --}}
                <img src="{{ asset('storage/posters/midtrans.png') }}" alt="Midtrans Logo" class="h-6 object-contain">
                <button type="button" onclick="hideMidtrans()" class="p-2 hover:bg-slate-200 rounded-full text-slate-500">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <div class="p-8 text-center">
                <p class="text-sm text-slate-500 font-medium tracking-wide uppercase">Total Pembayaran</p>
                <h2 class="text-3xl font-black text-indigo-700 my-2">Rp {{ number_format($event->price + 5000, 0, ',', '.') }}</h2>
                
                {{-- Box Order ID Dinamis --}}
                <p class="text-xs text-slate-400 bg-slate-100 py-1.5 px-3 rounded-full inline-block font-mono mt-1">
                    Order ID: <span id="midtrans-order-id" class="font-bold text-slate-700">Memuat...</span>
                </p>

                <div class="mt-8 space-y-4">
                    <p class="text-left text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Pilih Metode Pembayaran:</p>
                    
                    {{-- Tombol Transaksi QRIS --}}
                    <button type="button" id="btn-simulasi-bayar"
                        class="w-full py-4 border-2 border-indigo-100 rounded-2xl flex justify-between items-center px-6 hover:border-indigo-600 hover:bg-indigo-50/30 transition group shadow-sm text-left">
                        <div class="flex items-center gap-3">
                            <span class="text-2xl">📱</span>
                            <div>
                                <p class="font-black text-slate-800 group-hover:text-indigo-600 transition">GoPay / QRIS</p>
                                <p class="text-[11px] text-slate-400 font-medium">Scan QR melalui GoPay, ShopeePay, Dana, atau OVO</p>
                            </div>
                        </div>
                        <span class="text-indigo-400 font-bold group-hover:translate-x-1 transition-transform">→</span>
                    </button>
                </div>

                <p class="text-[10px] text-slate-400 mt-6 flex items-center justify-center gap-1">
                    🔒 Terenkripsi dan diamankan oleh sistem Midtrans Sandbox
                </p>
            </div>
        </div>
    </div>

@push('scripts')
<script>
    let serverOrderId = '';
    let serverCustomerName = '';

    document.getElementById('checkout-form').addEventListener('submit', function(e) {
        e.preventDefault();
        const form = this;
        const formData = new FormData(form);

        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(function(response) {
            if (response.ok) {
                return response.json();
            }
            throw new Error('Gagal memproses transaksi.');
        })
        .then(function(data) {
            if (data.success) {
                // Simpan data transaksi riil dari database
                serverOrderId = data.order_id;
                serverCustomerName = data.customer_name;
                showMidtrans();
            } else {
                alert(data.error || 'Gagal memproses pesanan.');
            }
        })
        .catch(function(error) {
            console.error('Error:', error);
            alert('Terjadi kesalahan sistem atau input validasi.');
        });
    });

    function showMidtrans() {
        const overlay = document.getElementById('midtrans-overlay');
        if (overlay) {
            // Set teks Order ID di dalam pop-up sebelum muncul
            document.getElementById('midtrans-order-id').innerText = serverOrderId;
            
            overlay.classList.remove('hidden');
            overlay.classList.add('flex');
        }
    }

    function hideMidtrans() {
        const overlay = document.getElementById('midtrans-overlay');
        if (overlay) {
            overlay.classList.add('hidden');
            overlay.classList.remove('flex');
        }
    }

    document.getElementById('btn-simulasi-bayar').addEventListener('click', function() {
        const eventId = "{{ $event->id }}";
        const namaReal = serverCustomerName || document.getElementById('customer_name').value;
        const orderIdReal = serverOrderId || 'TRX-30195';
        
        window.location.href = "{{ route('ticket') }}?event_id=" + eventId + "&nama=" + encodeURIComponent(namaReal) + "&order_id=" + orderIdReal;
    });
</script>
@endpush

<style>
    @keyframes bounce-in {
        0% { transform: scale(0.9); opacity: 0; }
        70% { transform: scale(1.05); opacity: 1; }
        100% { transform: scale(1); }
    }
    .animate-bounce-in { animation: bounce-in 0.4s ease-out forwards; }
</style>
@endsection