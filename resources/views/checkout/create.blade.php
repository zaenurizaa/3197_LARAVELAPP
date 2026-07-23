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

        {{-- Box Notifikasi Menangkap Error AJAX / Failed to Fetch --}}
        <div id="error-ajax-container" class="hidden mb-6 p-4 bg-red-100 text-red-700 rounded-xl font-bold"></div>

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
                    {{-- Input hidden krusial penyuplai data ke validator request --}}
                    <input type="hidden" name="event_id" value="{{ $event->id }}">

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
                        Lanjut ke Pembayaran
                    </button>
                </form>
            </div>
        </div>
    </main>
@endsection

@push('scripts')
<script>
    document.getElementById('checkout-form').addEventListener('submit', function(e) {
        e.preventDefault();
        const form = this;
        const formData = new FormData(form);
        const errorContainer = document.getElementById('error-ajax-container');
        
        // Reset tampilan error bawaan
        errorContainer.classList.add('hidden');
        errorContainer.innerText = '';

        const submitBtn = form.querySelector('button[type="submit"]');
        const originalBtnText = submitBtn.innerText;
        
        submitBtn.disabled = true; 
        submitBtn.innerText = '⏳ Memproses Pembayaran...'; 
        submitBtn.classList.add('opacity-75', 'cursor-not-allowed');

        // Ambil token CSRF dari input form secara manual
        const csrfToken = form.querySelector('input[name="_token"]').value;

        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: { 
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken, 
                'ngrok-skip-browser-warning': 'true' 
            }
        })
        .then(async response => {
            if (response.status === 419) {
                throw new Error('Sesi aplikasi kedaluwarsa. Silakan refresh (F5) halaman ini.');
            }

            const data = await response.json();
            if (!response.ok) {
                if (data.errors) {
                    const firstError = Object.values(data.errors)[0][0];
                    throw new Error(firstError);
                }
                throw new Error(data.error || 'Gagal memproses transaksi.');
            }
            return data;
        })
        .then(data => {
            if (data.success && data.redirect_url) {
                window.location.href = data.redirect_url;
            } else {
                alert(data.error || 'Gagal memproses pesanan.');
                resetButton();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            errorContainer.innerText = error.message;
            errorContainer.classList.remove('hidden');
            resetButton();
        });

        function resetButton() {
            submitBtn.disabled = false;
            submitBtn.innerText = originalBtnText;
            submitBtn.classList.remove('opacity-75', 'cursor-not-allowed');
        }
    });
</script>
@endpush