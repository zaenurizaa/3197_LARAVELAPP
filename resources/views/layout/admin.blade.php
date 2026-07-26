<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - @yield('title')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
    </style>
</head>

<body class="bg-slate-50 text-slate-900 flex min-h-screen">

    {{-- BACA USER YANG SEDANG LOGIN (GUARD ADMIN ATAU ORGANIZER) --}}
    @php
        $user = Auth::guard('admin')->user() ?? Auth::guard('organizer')->user() ?? auth()->user();
        $isSuperAdmin = $user ? $user->isSuperAdmin() : false;
        $isOrganizer = $user ? $user->isOrganizer() : false;
    @endphp

    <!-- SIDEBAR UTAMA -->
    <aside class="w-64 bg-indigo-900 text-indigo-100 flex flex-col p-6 space-y-8 sticky top-0 h-screen">
        <!-- Logo & Title Dynamic -->
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center text-indigo-900 font-black text-xl shadow-md">AH</div>
            <div>
                <span class="text-xl font-bold text-white tracking-tight block leading-none">AmikomEvent</span>
                <span class="text-[10px] text-indigo-300 font-semibold tracking-wider uppercase">
                    {{ $isSuperAdmin ? 'Superadmin Panel' : 'Organizer Panel' }}
                </span>
            </div>
        </div>

        <!-- NAVIGASI ADAPTIF -->
        <nav class="flex-1 space-y-2 overflow-y-auto">
            <p class="text-[10px] font-bold uppercase tracking-widest text-indigo-400 mb-4 px-2">Main Menu</p>

            {{-- KONDISI 1: JIKA ORGANIZER DENGAN STATUS PENDING --}}
            @if($isOrganizer && optional($user->tenant)->status === 'pending')
                <div class="p-3 bg-amber-500/20 border border-amber-400/30 rounded-xl text-amber-200 text-xs mb-4">
                    ⏳ Pendaftaran akun Anda sedang ditinjau Superadmin.
                </div>

                <a href="{{ route('organizer.dashboard') }}" class="flex items-center gap-3 px-4 py-3 bg-indigo-800 text-white rounded-xl font-bold transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Status Pendaftaran
                </a>

            {{-- KONDISI 2: AMAN / BISA AKSES DALAM DASHBOARD --}}
            @else
                {{-- Dashboard Route --}}
                <a href="{{ $isSuperAdmin ? route('admin.dashboard') : route('organizer.dashboard') }}" 
                   class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('*.dashboard') ? 'bg-indigo-800 text-white' : 'hover:bg-indigo-800/50 text-indigo-200 hover:text-white' }} rounded-xl font-bold transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2v-4zM14 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2v-4z" />
                    </svg>
                    Dashboard
                </a>

                {{-- Events Route --}}
                <a href="{{ $isSuperAdmin ? route('admin.events.index') : route('organizer.events.index') }}" 
                   class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('*.events.*') ? 'bg-indigo-800 text-white' : 'hover:bg-indigo-800/50 text-indigo-200 hover:text-white' }} rounded-xl font-bold transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    Kelola Event
                </a>

                {{-- Transactions Route --}}
                <a href="{{ $isSuperAdmin ? route('admin.transactions.index') : route('organizer.transactions.index') }}" 
                   class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('*.transactions.*') ? 'bg-indigo-800 text-white' : 'hover:bg-indigo-800/50 text-indigo-200 hover:text-white' }} rounded-xl font-bold transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Laporan
                </a>

                {{-- Scan E-Ticket (Check-in Scanner) --}}
                <a href="{{ $isSuperAdmin ? route('admin.checkin.scan') : route('organizer.checkin.scan') }}" 
                   class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('*.checkin.scan') ? 'bg-indigo-800 text-white' : 'hover:bg-indigo-800/50 text-indigo-200 hover:text-white' }} rounded-xl font-bold transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                    </svg>
                    Scan E-Ticket
                </a>

                {{-- Menu Khusus Superadmin --}}
                @if($isSuperAdmin)
                    <div class="pt-4 pb-1">
                        <p class="text-[10px] font-bold uppercase tracking-widest text-indigo-400 px-2">Superadmin Control</p>
                    </div>

                    <a href="{{ route('admin.tenants.index') }}" class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('admin.tenants.*') ? 'bg-indigo-800 text-white' : 'hover:bg-indigo-800/50 text-indigo-200 hover:text-white' }} rounded-xl font-bold transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-7h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        Kelola Tenant (ACC)
                    </a>

                    <a href="{{ route('admin.categories.index') }}" class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('admin.categories.*') ? 'bg-indigo-800 text-white' : 'hover:bg-indigo-800/50 text-indigo-200 hover:text-white' }} rounded-xl font-bold transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        Kelola Kategori
                    </a>

                    <a href="{{ route('admin.partners.index') }}" class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('admin.partners.*') ? 'bg-indigo-800 text-white' : 'hover:bg-indigo-800/50 text-indigo-200 hover:text-white' }} rounded-xl font-bold transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        Kelola Partner
                    </a>

                    <a href="{{ route('admin.coupons.index') }}" class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('admin.coupons.*') ? 'bg-indigo-800 text-white' : 'hover:bg-indigo-800/50 text-indigo-200 hover:text-white' }} rounded-xl font-bold transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2M3 12l6.414-6.414a2 2 0 012.828 0L19.5 12m-15 0L11 19.5l6.5-6.5" />
                        </svg>
                        Kelola Kupon
                    </a>
                @endif
            @endif

        </nav>

        <!-- Logout Button -->
        <div class="pt-6 border-t border-indigo-800">
            <form action="{{ $isSuperAdmin ? route('admin.logout') : route('organizer.logout') }}" method="POST">
                @csrf
                <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 text-indigo-300 hover:text-white rounded-xl font-bold hover:bg-red-600/20 transition text-left outline-none">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    Keluar
                </button>
            </form>
        </div>
    </aside>

    <!-- CONTENT AREA -->
    <main class="flex-1 p-10 overflow-y-auto">
        <header class="flex justify-between items-center mb-10">
            <div>
                <h1 class="text-3xl font-black tracking-tight">@yield('page_title')</h1>
                <p class="text-slate-500 font-medium">@yield('page_subtitle')</p>
            </div>
            <div class="flex items-center gap-4">
                <div class="text-right hidden md:block">
                    <p class="font-bold text-slate-800">{{ $user->name ?? 'User' }}</p>
                    <p class="text-xs text-slate-400 font-medium">
                        {{ $isSuperAdmin ? 'Superadmin Utama' : (optional($user->tenant)->name ?? 'Pengelola Event') }}
                    </p>
                </div>
                <div class="w-12 h-12 bg-white rounded-2xl shadow-sm border flex items-center justify-center p-1">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name ?? 'User') }}&background=6366f1&color=fff" class="rounded-xl">
                </div>
            </div>
        </header>

        @if(session('success'))
        <div class="mb-6 p-4 bg-emerald-50 border border-emerald-100 text-emerald-600 rounded-2xl font-bold flex items-center gap-2 shadow-sm">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            {{ session('success') }}
        </div>
        @endif

        @if(session('error'))
        <div class="mb-6 p-4 bg-red-50 border border-red-100 text-red-600 rounded-2xl font-bold flex items-center gap-2 shadow-sm">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            {{ session('error') }}
        </div>
        @endif

        @yield('content')
    </main>

    <!-- 💡 Wajib ditambahkan agar @push('scripts') dari halaman anak bisa dieksekusi -->
    @stack('scripts')
</body>

</html>