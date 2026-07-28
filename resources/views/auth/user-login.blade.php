<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk atau Daftar - AmikomEventHub</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 100%);
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(16px);
        }
    </style>
</head>

<body class="text-slate-900 min-h-screen flex items-center justify-center p-4 md:p-6">
    <div class="max-w-md w-full glass-card rounded-[2.5rem] p-8 md:p-10 shadow-2xl border border-white/20 transition-all duration-300">
        
        <!-- Logo & Branding -->
        <div class="text-center mb-8">
            <a href="{{ url('/') }}" class="inline-flex items-center gap-2 mb-4 hover:opacity-90 transition">
                <div class="w-12 h-12 bg-indigo-600 rounded-2xl flex items-center justify-center text-white font-extrabold text-2xl shadow-lg shadow-indigo-600/20">
                    AH
                </div>
            </a>
            <h1 class="text-2xl font-black tracking-tight text-slate-900">AmikomEventHub</h1>
            <p class="text-xs text-slate-500 mt-1 font-medium">Temukan dan pesan tiket event menarik di Amikom</p>
        </div>

        <!-- Tab Controls -->
        <div class="flex bg-slate-100 p-1.5 rounded-2xl mb-6">
            <button onclick="switchTab('login')" id="tab-login" class="flex-1 py-3 text-sm font-bold rounded-xl bg-white text-indigo-600 shadow-sm transition-all duration-300">
                Masuk
            </button>
            <button onclick="switchTab('register')" id="tab-register" class="flex-1 py-3 text-sm font-bold rounded-xl text-slate-500 hover:text-slate-800 transition-all duration-300">
                Daftar
            </button>
        </div>

        {{-- Flash Messages --}}
        @if(session('error'))
        <div class="bg-rose-50 border border-rose-200 text-rose-600 p-4 rounded-2xl mb-6 font-semibold text-xs text-center">
            {{ session('error') }}
        </div>
        @endif

        @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-600 p-4 rounded-2xl mb-6 font-semibold text-xs text-center">
            {{ session('success') }}
        </div>
        @endif

        <!-- Login Form -->
        <div id="form-login-container" class="space-y-5">
            <form action="{{ route('login.manual') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-[11px] font-bold text-slate-500 mb-2 uppercase tracking-wider">Email Address</label>
                    <input type="email" name="email" value="{{ old('email') }}" 
                        class="w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 placeholder-slate-400 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-600 outline-none transition font-medium text-xs" 
                        placeholder="Masukkan email Anda" required autocomplete="email">
                </div>
                
                <div>
                    <div class="flex justify-between items-center mb-2">
                        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider">Password</label>
                    </div>
                    <input type="password" name="password" 
                        class="w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 placeholder-slate-400 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-600 outline-none transition font-medium text-xs" 
                        placeholder="••••••••" required>
                </div>

                <button type="submit" 
                    class="w-full py-4 bg-indigo-600 hover:bg-indigo-700 text-white rounded-2xl font-bold text-xs tracking-wider uppercase shadow-lg shadow-indigo-600/15 hover:shadow-indigo-600/25 transition active:scale-[0.99] mt-2">
                    Masuk Akun
                </button>
            </form>
        </div>

        <!-- Register Form (Hidden by default) -->
        <div id="form-register-container" class="hidden space-y-5">
            <form action="{{ route('register.manual') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-[11px] font-bold text-slate-500 mb-2 uppercase tracking-wider">Nama Lengkap</label>
                    <input type="text" name="name" value="{{ old('name') }}" 
                        class="w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 placeholder-slate-400 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-600 outline-none transition font-medium text-xs" 
                        placeholder="Nama lengkap Anda" required>
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-slate-500 mb-2 uppercase tracking-wider">Email Address</label>
                    <input type="email" name="email" value="{{ old('email') }}" 
                        class="w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 placeholder-slate-400 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-600 outline-none transition font-medium text-xs" 
                        placeholder="nama@domain.com" required>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 mb-2 uppercase tracking-wider">Password</label>
                        <input type="password" name="password" 
                            class="w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 placeholder-slate-400 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-600 outline-none transition font-medium text-xs" 
                            placeholder="Min. 8 karakter" required>
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 mb-2 uppercase tracking-wider">Ulangi Password</label>
                        <input type="password" name="password_confirmation" 
                            class="w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 placeholder-slate-400 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-600 outline-none transition font-medium text-xs" 
                            placeholder="Sama dengan kiri" required>
                    </div>
                </div>

                <button type="submit" 
                    class="w-full py-4 bg-indigo-600 hover:bg-indigo-700 text-white rounded-2xl font-bold text-xs tracking-wider uppercase shadow-lg shadow-indigo-600/15 hover:shadow-indigo-600/25 transition active:scale-[0.99] mt-2">
                    Daftar Baru
                </button>
            </form>
        </div>

        <!-- Separator -->
        <div class="relative flex py-5 items-center">
            <div class="flex-grow border-t border-slate-200"></div>
            <span class="flex-shrink mx-4 text-slate-400 text-xs font-bold uppercase tracking-wider">Atau</span>
            <div class="flex-grow border-t border-slate-200"></div>
        </div>

        <!-- Google SSO Button -->
        <a href="{{ route('google.login') }}" class="w-full flex items-center justify-center gap-3 py-3.5 border-2 border-slate-200 hover:border-indigo-600 rounded-2xl text-slate-700 hover:text-indigo-600 bg-white font-bold text-xs tracking-wider uppercase transition-all duration-300">
            <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="currentColor">
                <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z" fill="#FBBC05"/>
                <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.53 12-4.53z" fill="#EA4335"/>
            </svg>
            Lanjutkan dengan Google
        </a>

        <div class="text-center mt-6">
            <a href="{{ url('/') }}" class="text-xs text-slate-400 hover:text-indigo-600 font-bold tracking-wide transition">
                &larr; Kembali ke Beranda
            </a>
        </div>
    </div>

    <script>
        function switchTab(tab) {
            const btnLogin = document.getElementById('tab-login');
            const btnRegister = document.getElementById('tab-register');
            const formLogin = document.getElementById('form-login-container');
            const formRegister = document.getElementById('form-register-container');

            if (tab === 'login') {
                btnLogin.classList.add('bg-white', 'text-indigo-600', 'shadow-sm');
                btnLogin.classList.remove('text-slate-500');
                btnRegister.classList.remove('bg-white', 'text-indigo-600', 'shadow-sm');
                btnRegister.classList.add('text-slate-500');
                formLogin.classList.remove('hidden');
                formRegister.classList.add('hidden');
            } else {
                btnRegister.classList.add('bg-white', 'text-indigo-600', 'shadow-sm');
                btnRegister.classList.remove('text-slate-500');
                btnLogin.classList.remove('bg-white', 'text-indigo-600', 'shadow-sm');
                btnLogin.classList.add('text-slate-500');
                formRegister.classList.remove('hidden');
                formLogin.classList.add('hidden');
            }
        }
    </script>
</body>

</html>
