@extends('layout.app')

@section('title', 'Checkout - ' . $event->title . ' - AmikomEventHub')

@section('content')
    <main class="max-w-5xl mx-auto px-6 pt-28 pb-20">
        <div class="mb-8">
            <a href="{{ route('events.show', $event->id) }}" class="text-indigo-600 font-bold inline-flex items-center gap-2 mb-4 hover:underline text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Kembali ke Event
            </a>
            <h1 class="text-3xl md:text-4xl font-extrabold text-slate-900">Checkout Tiket</h1>
            <p class="text-slate-500 mt-1 text-sm">Lengkapi data Anda untuk melanjutkan pemesanan tiket resmi.</p>
        </div>

        @if(session('error'))
        <div class="mb-6 p-4 bg-red-100 border border-red-200 text-red-700 rounded-2xl font-bold text-sm">
            {{ session('error') }}
        </div>
        @endif

        {{-- Box Notifikasi Error AJAX --}}
        <div id="error-ajax-container" class="hidden mb-6 p-4 bg-red-100 border border-red-200 text-red-700 rounded-2xl font-bold text-sm"></div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            {{-- Form Data Pemesan (Left Column - 7 cols) --}}
            <div class="lg:col-span-7 bg-white rounded-3xl border border-slate-200 p-6 md:p-8 shadow-sm">
                <h3 class="text-lg font-bold mb-6 text-slate-900 flex items-center gap-2 border-b border-slate-100 pb-4">
                    <span>📦</span> Data Pemesan (Tanpa Login)
                </h3>
                
                <form id="checkout-form" action="{{ route('checkout.store', $event->id) }}" method="POST" class="space-y-5">
                    @csrf
                    <input type="hidden" name="event_id" value="{{ $event->id }}">
                    <input type="hidden" name="coupon_code" id="coupon_code_hidden">

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-2 uppercase tracking-wider">Nama Lengkap</label>
                        <input type="text" id="customer_name" name="customer_name" placeholder="Masukkan nama sesuai identitas"
                            class="w-full px-4 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-600 outline-none transition font-medium text-sm text-slate-900"
                            required value="{{ old('customer_name') }}">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-2 uppercase tracking-wider">Email Aktif</label>
                            <input type="email" name="customer_email" placeholder="contoh@gmail.com"
                                class="w-full px-4 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-600 outline-none transition font-medium text-sm text-slate-900"
                                required value="{{ old('customer_email') }}">
                            <p class="text-[11px] text-slate-400 mt-1 font-semibold">*E-Ticket akan dikirim ke email ini</p>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-2 uppercase tracking-wider">No. WhatsApp</label>
                            <input type="tel" name="customer_phone" placeholder="08xxxxxxx"
                                class="w-full px-4 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-600 outline-none transition font-medium text-sm text-slate-900"
                                required value="{{ old('customer_phone') }}">
                        </div>
                    </div>

                    <button type="submit" id="checkout-submit-btn"
                        class="w-full py-4 bg-indigo-600 text-white rounded-2xl font-bold text-base shadow-xl shadow-indigo-200 hover:bg-indigo-700 active:scale-95 transition-all mt-4">
                        {{ ($event->price == 0 || (isset($event->is_free) && $event->is_free)) ? '🎁 Klaim Tiket Gratis' : 'Lanjut ke Pembayaran' }}
                    </button>
                </form>
            </div>

            {{-- Ringkasan Pesanan (Right Column - 5 cols) --}}
            <div class="lg:col-span-5 bg-white rounded-3xl border border-slate-200 p-6 md:p-8 shadow-sm space-y-5">
                <h3 class="text-lg font-bold border-b border-slate-100 pb-4 text-slate-900">Pesanan Anda</h3>
                
                {{-- Event Info Card --}}
                <div class="flex gap-4 items-start">
                    <img src="{{ ($event->poster_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($event->poster_path))
                                 ? asset('storage/' . $event->poster_path)
                                 : asset('storage/asset_admin/concert.png') }}" 
                         alt="{{ $event->title }}" class="w-20 h-20 rounded-2xl object-cover shrink-0">
                    <div class="flex-1 min-w-0">
                        <h4 class="font-bold text-slate-900 text-base leading-snug truncate">{{ $event->title }}</h4>
                        <p class="text-xs text-slate-500 mt-1">
                            📅 {{ $event->date instanceof \Carbon\Carbon ? $event->date->format('d M Y') : \Carbon\Carbon::parse($event->date)->format('d M Y') }}<br>
                            📍 {{ $event->location }}
                        </p>
                        <p class="text-indigo-600 font-bold text-sm mt-1.5">
                            1 x {{ ($event->price == 0 || (isset($event->is_free) && $event->is_free)) ? 'GRATIS' : 'Rp ' . number_format($event->effective_price, 0, ',', '.') }}
                        </p>
                    </div>
                </div>

                {{-- Active Tier Badge --}}
                @if($event->effective_tier_name !== 'Regular')
                <div class="flex items-center gap-3 px-4 py-3 bg-indigo-50 border border-indigo-100 rounded-2xl">
                    <span class="text-lg">🔥</span>
                    <div class="flex-1 min-w-0">
                        <p class="text-[10px] text-slate-500 font-bold uppercase tracking-wider">Kategori Tiket Aktif</p>
                        <p class="text-indigo-700 font-bold text-xs truncate">{{ $event->effective_tier_name }} – Rp {{ number_format($event->effective_price, 0, ',', '.') }}</p>
                    </div>
                    <span class="px-2.5 py-0.5 bg-emerald-100 text-emerald-700 text-[10px] font-extrabold rounded-full shrink-0">AKTIF</span>
                </div>
                @endif

                {{-- Cost Details --}}
                <div class="pt-4 border-t border-slate-100 space-y-3">
                    <div class="flex justify-between text-sm text-slate-600">
                        <span>Harga Tiket</span>
                        <span id="price-base" class="font-semibold text-slate-800">{{ ($event->price == 0 || (isset($event->is_free) && $event->is_free)) ? 'GRATIS' : 'Rp ' . number_format($event->effective_price, 0, ',', '.') }}</span>
                    </div>

                    <div class="flex justify-between text-sm text-emerald-600 font-semibold" id="discount-row" style="display:none!important">
                        <span id="discount-label">🏷️ Diskon Kupon</span>
                        <span id="discount-amount">-Rp 0</span>
                    </div>

                    <div class="flex justify-between text-sm text-slate-600">
                        <span>Biaya Layanan</span>
                        <span class="font-semibold text-slate-800">{{ ($event->price == 0 || (isset($event->is_free) && $event->is_free)) ? 'Rp 0' : 'Rp 5.000' }}</span>
                    </div>

                    <div class="flex justify-between text-xl font-black text-slate-900 pt-4 border-t border-slate-100">
                        <span>Total Bayar</span>
                        <span class="text-indigo-600" id="total-display">
                            {{ ($event->price == 0 || (isset($event->is_free) && $event->is_free)) ? 'GRATIS' : 'Rp ' . number_format($event->effective_price + 5000, 0, ',', '.') }}
                        </span>
                    </div>
                </div>

                {{-- Coupon Input Box --}}
                @if(!($event->price == 0 || (isset($event->is_free) && $event->is_free)))
                <div class="pt-4 border-t border-slate-100">
                    <label class="block text-xs font-bold text-slate-700 mb-2">🏷️ Punya Kode Voucher?</label>
                    <div class="flex gap-2">
                        <input type="text" id="coupon_code" placeholder="Masukkan kode voucher..."
                            class="flex-1 px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-600 outline-none transition font-mono text-xs uppercase"
                            autocomplete="off">
                        <button type="button" id="btn-apply-coupon"
                            class="px-4 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold text-xs transition active:scale-95 shrink-0">
                            Gunakan
                        </button>
                    </div>
                    <div id="coupon-feedback" class="mt-2 text-xs font-semibold hidden"></div>
                </div>
                @endif
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
        
        errorContainer.classList.add('hidden');
        errorContainer.innerText = '';

        const submitBtn = document.getElementById('checkout-submit-btn');
        const originalBtnText = submitBtn.innerText;
        
        submitBtn.disabled = true; 
        submitBtn.innerText = "{{ ($event->price == 0 || (isset($event->is_free) && $event->is_free)) ? '⏳ Memproses Tiket Gratis...' : '⏳ Memproses Pembayaran...' }}"; 
        submitBtn.classList.add('opacity-75', 'cursor-not-allowed');

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

    const basePrice    = {{ $event->effective_price ?? $event->price }};
    const serviceFee   = {{ ($event->price == 0 || (isset($event->is_free) && $event->is_free)) ? 0 : 5000 }};
    let   appliedDiscount = 0;

    function formatRupiah(amount) {
        return 'Rp ' + amount.toLocaleString('id-ID');
    }

    function updateTotalDisplay(discountAmount, discountLabel) {
        appliedDiscount = discountAmount;
        const discountRow = document.getElementById('discount-row');
        if (discountAmount > 0) {
            discountRow.style.removeProperty('display');
            document.getElementById('discount-label').textContent = '🏷️ Diskon (' + (discountLabel || 'Kupon') + ')';
            document.getElementById('discount-amount').textContent = '-' + formatRupiah(discountAmount);
        } else {
            discountRow.style.setProperty('display', 'none', 'important');
        }
        const total = Math.max(0, basePrice - discountAmount + serviceFee);
        document.getElementById('total-display').textContent = formatRupiah(total);
    }

    const applyBtn = document.getElementById('btn-apply-coupon');
    if (applyBtn) {
        applyBtn.addEventListener('click', async function() {
            const codeInput = document.getElementById('coupon_code');
            const code      = codeInput.value.trim().toUpperCase();
            const feedback  = document.getElementById('coupon-feedback');
            const eventId   = {{ $event->id }};

            if (!code) {
                feedback.textContent = '⚠️ Masukkan kode voucher terlebih dahulu.';
                feedback.className   = 'mt-2 text-xs font-semibold text-amber-600';
                feedback.classList.remove('hidden');
                return;
            }

            applyBtn.disabled = true;
            applyBtn.textContent = '...';

            try {
                const response = await fetch('{{ route("checkout.apply-coupon") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                    },
                    body: JSON.stringify({ coupon_code: code, event_id: eventId })
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    feedback.textContent = '✅ ' + data.message;
                    feedback.className   = 'mt-2 text-xs font-semibold text-emerald-600';
                    feedback.classList.remove('hidden');
                    updateTotalDisplay(data.discount_value, code);
                    document.getElementById('coupon_code_hidden').value = code;
                } else {
                    feedback.textContent = '❌ ' + (data.message || 'Kode tidak valid.');
                    feedback.className   = 'mt-2 text-xs font-semibold text-red-600';
                    feedback.classList.remove('hidden');
                    updateTotalDisplay(0, null);
                    document.getElementById('coupon_code_hidden').value = '';
                }
            } catch (err) {
                feedback.textContent = '❌ Gagal menghubungi server.';
                feedback.className   = 'mt-2 text-xs font-semibold text-red-600';
                feedback.classList.remove('hidden');
            }

            applyBtn.disabled = false;
            applyBtn.textContent = 'Gunakan';
        });

        document.getElementById('coupon_code')?.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') { e.preventDefault(); applyBtn.click(); }
        });
    }
</script>
@endpush