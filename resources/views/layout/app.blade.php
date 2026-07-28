<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'AmikomEventHub - Temukan Event Seru!')</title>
    
    <!-- PWA Meta Tags -->
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#6366f1">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="EventHub">
    <link rel="apple-touch-icon" href="/icons/icon-192.png">
    <script src="/sw-register.js" defer></script>

    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Google Font POPPINS -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; }
        .glass { background: rgba(255, 255, 255, 0.85); backdrop-filter: blur(10px); }
    </style>
</head>

<body class="bg-slate-50 text-slate-900">

    <!-- NAVBAR FIXED -->
    <nav class="glass fixed top-4 left-4 right-4 z-50 px-6 py-4 rounded-2xl border border-white/20 shadow-lg flex justify-between items-center max-w-7xl mx-auto">
        <div class="flex items-center gap-2">
            <a href="{{ url('/') }}" class="flex items-center gap-2">
                <div class="w-10 h-10 bg-indigo-600 rounded-xl flex items-center justify-center text-white font-bold text-xl">AH</div>
                <span class="text-xl font-bold tracking-tight">AmikomEventHub</span>
            </a>
        </div>
        
        <!-- Desktop Navigation -->
        <div class="hidden md:flex gap-6 font-medium items-center">
            @php
                $isHome = request()->is('/') && !request('category');
                $isCategory = request('category') || request()->is('#events');
                $isTicket = request()->routeIs('ticket*');

                // 🔥 Mendapatkan User dari Guard 'web' (User Publik / Pembeli)
                $authUser = Auth::guard('web')->user();
            @endphp

            <!-- Menu Jelajahi -->
            <a href="{{ url('/') }}" 
               class="transition text-sm {{ $isHome ? 'bg-indigo-50 text-indigo-600 font-bold px-3 py-1.5 rounded-xl' : 'text-slate-600 hover:text-indigo-600 px-3 py-1.5' }}">
                Jelajahi
            </a>

            <!-- Menu Kategori -->
            <a href="{{ url('/#events') }}" 
               class="transition text-sm {{ $isCategory ? 'bg-indigo-50 text-indigo-600 font-bold px-3 py-1.5 rounded-xl' : 'text-slate-600 hover:text-indigo-600 px-3 py-1.5' }}">
                Kategori
            </a>

            <!-- Menu Tiket Saya -->
            <a href="{{ route('ticket') }}" 
               class="transition text-sm {{ $isTicket ? 'bg-indigo-50 text-indigo-600 font-bold px-3 py-1.5 rounded-xl' : 'text-slate-600 hover:text-indigo-600 px-3 py-1.5' }}">
                Tiket Saya
            </a>
            
            <div class="h-5 w-px bg-slate-300 mx-2"></div>

            @if(Auth::guard('web')->check())
                <div class="flex items-center gap-3">
                    <img src="{{ $authUser->avatar ?: 'https://ui-avatars.com/api/?background=4f46e5&color=fff&name='.urlencode($authUser->name) }}" 
     alt="Avatar" 
     referrerpolicy="no-referrer"
     class="w-8 h-8 rounded-full border border-indigo-200 object-cover">
                    <span class="text-sm font-semibold text-slate-700 max-w-30 truncate">{{ $authUser->name }}</span>
                    
                    <!-- Form Logout User Publik -->
                    <a href="#" onclick="event.preventDefault(); document.getElementById('user-logout-form').submit();" 
                       class="text-xs bg-rose-50 hover:bg-rose-100 text-rose-600 px-3 py-1.5 rounded-lg font-bold transition">
                        Keluar
                    </a>
                    <form id="user-logout-form" action="{{ route('user.logout') }}" method="POST" class="hidden">
                        @csrf
                    </form>
                </div>
            @else
                {{-- Jika Guest, Tampilkan Tombol Login Terpadu --}}
                <a href="{{ route('login') }}" class="flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold px-4 py-2 rounded-xl shadow-md shadow-indigo-200 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                    </svg>
                    Masuk / Daftar
                </a>
            @endif
        </div>

        <!-- Mobile Burger Menu Trigger -->
        <button onclick="toggleMobileMenu()" class="block md:hidden text-slate-600 hover:text-indigo-600 focus:outline-none">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path id="burger-icon" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
        </button>

        <!-- Mobile Dropdown Navigation -->
        <div id="mobile-menu" class="hidden absolute top-20 left-0 right-0 bg-white/95 backdrop-blur-md rounded-2xl border border-slate-100 shadow-xl p-6 flex flex-col gap-4 mx-4 z-40 animate-in fade-in slide-in-from-top-4 duration-300">
            <a href="{{ url('/') }}" onclick="toggleMobileMenu()" class="text-sm font-bold text-slate-700 hover:text-indigo-600 transition">Jelajahi</a>
            <a href="{{ url('/#events') }}" onclick="toggleMobileMenu()" class="text-sm font-bold text-slate-700 hover:text-indigo-600 transition">Kategori</a>
            <a href="{{ route('ticket') }}" onclick="toggleMobileMenu()" class="text-sm font-bold text-slate-700 hover:text-indigo-600 transition">Tiket Saya</a>
            <div class="h-px bg-slate-100 my-2"></div>
            @if(Auth::guard('web')->check())
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <img src="{{ $authUser->avatar ?: 'https://ui-avatars.com/api/?background=4f46e5&color=fff&name='.urlencode($authUser->name) }}" class="w-7 h-7 rounded-full object-cover">
                        <span class="text-xs font-bold text-slate-700 truncate max-w-28">{{ $authUser->name }}</span>
                    </div>
                    <a href="#" onclick="event.preventDefault(); document.getElementById('user-logout-form-mob').submit();" class="text-xs text-rose-600 bg-rose-50 px-3 py-1.5 rounded-lg font-bold">Keluar</a>
                    <form id="user-logout-form-mob" action="{{ route('user.logout') }}" method="POST" class="hidden">
                        @csrf
                    </form>
                </div>
            @else
                <a href="{{ route('login') }}" onclick="toggleMobileMenu()" class="w-full py-3 bg-indigo-600 text-white rounded-xl text-center text-xs font-bold shadow-lg">Masuk / Daftar</a>
            @endif
        </div>
    </nav>

    <script>
        function toggleMobileMenu() {
            const menu = document.getElementById('mobile-menu');
            const icon = document.getElementById('burger-icon');
            if (menu.classList.contains('hidden')) {
                menu.classList.remove('hidden');
                icon.setAttribute('d', 'M6 18L18 6M6 6l12 12');
            } else {
                menu.classList.add('hidden');
                icon.setAttribute('d', 'M4 6h16M4 12h16M4 18h16');
            }
        }
    </script>

    <!-- CONTENT AREA -->
    <main class="min-h-screen pt-28">
        @yield('content')
    </main>

    <!-- FOOTER PUBLIK -->
    <footer class="bg-indigo-900 text-indigo-100 py-16 px-6 mt-20">
        <div class="max-w-7xl mx-auto grid grid-cols-1 sm:grid-cols-2 md:grid-cols-6 gap-8">
            
            <!-- Branding -->
            <div class="space-y-4 md:col-span-2">
                <div class="flex items-center gap-2">
                    <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center text-indigo-900 font-bold text-xl">AH</div>
                    <span class="text-2xl font-bold text-white">AmikomEventHub</span>
                </div>
                <p class="max-w-sm text-indigo-300 text-sm leading-relaxed">
                    Platform reservasi tiket event online terbaik untuk mahasiswa, UKM, Ormawa, dan penyelenggara acara profesional.
                </p>
            </div>

            <!-- Kategori -->
            <div>
                <h4 class="text-white font-bold mb-6">Kategori</h4>
                <ul class="space-y-3 text-sm">
                    @foreach(\App\Models\Category::all() as $cat)
                        <li>
                            <a href="{{ url('/?category=' . $cat->slug) }}#events" class="hover:text-white transition text-indigo-300">
                                {{ $cat->name }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>

            <!-- Penyelenggara / Multi-Tenant Area -->
            <div>
                <h4 class="text-white font-bold mb-6">Penyelenggara</h4>
                <p class="text-xs text-indigo-300 mb-4 leading-relaxed">
                    Ingin membuat event & menjual tiket atas nama Organisasi/UKM kamu?
                </p>
                <a href="{{ route('tenant.register') }}" class="inline-flex items-center gap-2 bg-indigo-500 hover:bg-indigo-400 text-white font-semibold text-xs px-4 py-2.5 rounded-xl transition shadow-lg shadow-indigo-950/50">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    <span>Daftar Organisasi (UKM)</span>
                </a>
            </div>
            
            <!-- Pusat Bantuan & FAQ (H10: Help and Documentation) -->
            <div>
                <h4 class="text-white font-bold mb-6">Pusat Bantuan</h4>
                <ul class="space-y-3 text-sm text-indigo-300">
                    <li><a href="#faq" onclick="toggleHelpModal()" class="hover:text-white transition flex items-center gap-1"><span>[?]</span> FAQ Pembelian Tiket</a></li>
                    <li><a href="#faq" onclick="toggleHelpModal()" class="hover:text-white transition flex items-center gap-1"><span>[i]</span> Cara Klaim E-Sertifikat</a></li>
                    <li><a href="#faq" onclick="toggleHelpModal()" class="hover:text-white transition flex items-center gap-1"><span>[$]</span> Panduan Pembayaran</a></li>
                </ul>
            </div>

            <!-- Hubungi Kami -->
            <div>
                <h4 class="text-white font-bold mb-6">Hubungi Kami</h4>
                <ul class="space-y-3 text-sm text-indigo-300">
                    <li>support@amikomeventhub.com</li>
                    <li>+62 812 3456 7890</li>
                </ul>
            </div>

        </div>

        <div class="max-w-7xl mx-auto pt-12 mt-12 border-t border-indigo-800 text-center text-indigo-400 text-sm">
            &copy; 2026 AmikomEventHub. Built with Laravel & Tailwind CSS.
        </div>
    </footer>

    <!-- 🔥 FLOATING HELP BUTTON & MODAL (HEURISTIC H10) -->
    <div class="fixed bottom-6 right-6 z-50">
        <button onclick="toggleHelpModal()" class="w-12 h-12 bg-indigo-600 hover:bg-indigo-700 text-white rounded-full flex items-center justify-center shadow-2xl transition active:scale-95 group">
            <svg class="w-5 h-5 group-hover:scale-110 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </button>
    </div>

    <!-- Modal Box Help & FAQ -->
    <div id="help-modal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl p-6 md:p-8 max-w-lg w-full text-slate-800 shadow-2xl space-y-5 transform transition-all duration-300 scale-95 opacity-0" id="help-modal-card">
            <div class="flex justify-between items-center border-b border-slate-100 pb-3">
                <h3 class="text-lg font-black text-slate-900 flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path></svg>
                    <span>Pusat Bantuan & FAQ</span>
                </h3>
                <button onclick="toggleHelpModal()" class="text-slate-400 hover:text-slate-700 font-bold text-lg">&times;</button>
            </div>
            
            <div class="space-y-4 max-h-[400px] overflow-y-auto pr-2 text-xs leading-relaxed text-slate-600">
                <div>
                    <h4 class="font-bold text-slate-900 text-sm">1. Bagaimana cara memesan tiket event?</h4>
                    <p class="mt-1">Pilih event yang diinginkan, klik "Pesan Tiket", masukkan data diri aktif (autofill aktif jika login), lalu lakukan pembayaran melalui payment gateway Midtrans.</p>
                </div>
                <div>
                    <h4 class="font-bold text-slate-900 text-sm">2. Bagaimana cara mengklaim E-Sertifikat?</h4>
                    <p class="mt-1">E-sertifikat akan dikirimkan otomatis ke email terdaftar Anda setelah panitia memverifikasi kehadiran Anda di pintu masuk venue event.</p>
                </div>
                <div>
                    <h4 class="font-bold text-slate-900 text-sm">3. Mengapa saya tidak menerima E-Ticket di Email?</h4>
                    <p class="mt-1">Silakan periksa folder Spam/Promosi pada email Anda. Anda juga dapat mengunduh tiket di menu "Tiket Saya" di pojok kanan atas website.</p>
                </div>
                <div>
                    <h4 class="font-bold text-slate-900 text-sm">4. Transaksi dibatalkan secara otomatis?</h4>
                    <p class="mt-1">Untuk tiket berbayar, Anda memiliki batas waktu pembayaran selama 15 menit. Jika melebihi waktu tersebut, pesanan otomatis dilepas untuk menjaga keadilan kuota pembeli lain.</p>
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100 text-center">
                <button onclick="toggleHelpModal()" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold transition">
                    Tutup Panduan Bantuan
                </button>
            </div>
        </div>
    </div>

    <script>
        function toggleHelpModal() {
            const modal = document.getElementById('help-modal');
            const card = document.getElementById('help-modal-card');
            
            if (modal.classList.contains('hidden')) {
                modal.classList.remove('hidden');
                setTimeout(() => {
                    card.classList.remove('scale-95', 'opacity-0');
                    card.classList.add('scale-100', 'opacity-100');
                }, 50);
            } else {
                card.classList.remove('scale-100', 'opacity-100');
                card.classList.add('scale-95', 'opacity-0');
                setTimeout(() => {
                    modal.classList.add('hidden');
                }, 200);
            }
        }
    </script>

    @stack('scripts')
</body>
</html>