<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'AmikomEventHub - Temukan Event Seru!')</title>
    
    <link rel="manifest" href="/manifest.json">
    <script>
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/sw.js')
                .then(() => console.log("🚀 PWA AmikomEventHub Aktif!"))
                .catch((err) => console.error("Gagal memuat PWA Service Worker:", err));
        }
    </script>

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

            {{-- 🔥 Hanya tampilkan Profil User jika login di Guard WEB (Pembeli Publik) --}}
            @if(Auth::guard('web')->check() && $authUser->role === 'user')
                <div class="flex items-center gap-3">
                    <img src="{{ $authUser->avatar ?? 'https://ui-avatars.com/api/?background=4f46e5&color=fff&name='.urlencode($authUser->name) }}" 
                         alt="Avatar" class="w-8 h-8 rounded-full border border-indigo-200 object-cover">
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
                {{-- Jika Guest (Atau sedang login sebagai Admin/Organizer di tab lain), tampilkan Login Google --}}
                <a href="{{ route('google.login') }}" class="flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold px-4 py-2 rounded-xl shadow-md shadow-indigo-200 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-google" viewBox="0 0 16 16">
                      <path d="M15.545 6.558a9.42 9.42 0 0 1 .139 1.626c0 2.434-.87 4.492-2.384 5.885h.002C11.978 15.292 10.158 16 8 16A8 8 0 1 1 8 0c2.158 0 3.92.8 5.242 2.096l-1.872 1.802C10.48 2.969 9.341 2.5 8 2.5c-2.425 0-4.466 1.69-5.2 3.968a5.15 5.15 0 0 0 0 2.532c.734 2.278 2.774 3.968 5.2 3.968 1.559 0 2.68-.414 3.497-1.127a3.3 3.3 0 0 0 1.097-2.16H8v-2.6h7.545z"/>
                    </svg>
                    Login
                </a>
            @endif
        </div>
    </nav>

    <!-- CONTENT AREA -->
    <main class="min-h-screen pt-28">
        @yield('content')
    </main>

    <!-- FOOTER PUBLIK -->
    <footer class="bg-indigo-900 text-indigo-100 py-20 px-6 mt-20">
        <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-5 gap-10">
            
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
                    <span>🏛️</span>
                    <span>Daftar Organisasi (UKM)</span>
                </a>
            </div>
            
            <!-- Hubungi Kami -->
            <div>
                <h4 class="text-white font-bold mb-6">Hubungi Kami</h4>
                <ul class="space-y-3 text-sm text-indigo-300">
                    <li>support@eventtiket.com</li>
                    <li>+62 812 3456 7890</li>
                </ul>
            </div>

        </div>

        <div class="max-w-7xl mx-auto pt-12 mt-12 border-t border-indigo-800 text-center text-indigo-400 text-sm">
            &copy; 2026 AmikomEventHub. Built with Laravel & Tailwind CSS.
        </div>
    </footer>

    @stack('scripts')
</body>
</html>