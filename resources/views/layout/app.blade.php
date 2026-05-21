<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'AmikomEventHub - Temukan Event Seru!')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .glass { background: rgba(255, 255, 255, 0.7); backdrop-filter: blur(10px); }
    </style>
</head>

<body class="bg-slate-50 text-slate-900">

    <nav class="glass sticky top-8 z-40 mx-4 mt-4 px-6 py-4 rounded-2xl border border-white/20 shadow-lg flex justify-between items-center">
        <div class="flex items-center gap-2">
            <a href="{{ url('/') }}" class="flex items-center gap-2">
                <div class="w-10 h-10 bg-indigo-600 rounded-xl flex items-center justify-center text-white font-bold text-xl">AH</div>
                <span class="text-xl font-bold tracking-tight">AmikomEventHub</span>
            </a>
        </div>
        <div class="hidden md:flex gap-8 font-medium">
            <a href="{{ url('/') }}" class="{{ !request('category') ? 'text-indigo-600' : 'text-slate-600 hover:text-indigo-600' }} transition">Jelajahi</a>
            <a href="{{ url('/#events') }}" class="{{ request('category') ? 'text-indigo-600' : 'text-slate-600 hover:text-indigo-600' }} transition">Kategori</a>
             <a href="{{ url('/') }}" class="text-slate-600 hover:text-indigo-600 transition">Hubungi Kami</a>        </div>
    </nav>

    <main>
        @yield('content')
    </main>

    <footer class="bg-indigo-900 text-indigo-100 py-20 px-6 mt-20">
        <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-4 gap-12">
            
            
            <div class="space-y-4">
                <div class="flex items-center gap-2">
                    <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center text-indigo-900 font-bold text-xl">AH</div>
                    <span class="text-2xl font-bold text-white">AmikomEventHub</span>
                </div>
                <p class="max-w-xs text-indigo-300 text-sm">Platform reservasi tiket event online terbaik untuk mahasiswa dan penyelenggara profesional.</p>
            </div>

            
            <div>
                <h4 class="text-white font-bold mb-6">Kategori</h4>
                <ul class="space-y-4 text-sm">
                    @foreach(\App\Models\Category::all() as $cat)
                        <li>
                            <a href="{{ url('/?category=' . $cat->slug) }}#events" 
                               class="hover:text-white transition {{ request('category') == $cat->slug ? 'text-white font-bold' : 'text-indigo-300' }}">
                                {{ $cat->name }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>

            
            <div>
                <h4 class="text-white font-bold mb-6">Navigasi</h4>
                <ul class="space-y-4 text-sm text-indigo-300">
                    <li><a href="{{ url('/') }}" class="hover:text-white transition">Home</a></li>
                    <li><a href="{{ url('/#events') }}" class="hover:text-white transition">Semua Event</a></li>
                    <li><a href="#" class="hover:text-white transition">Cara Bayar</a></li>
                </ul>
            </div>
            
            
            <div>
                <h4 class="text-white font-bold mb-6">Hubungi Kami</h4>
                <ul class="space-y-4 text-sm text-indigo-300">
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