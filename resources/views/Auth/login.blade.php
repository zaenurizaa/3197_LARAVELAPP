<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Login' }} - AmikomEventHub</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
    </style>
</head>

<body class="bg-indigo-950 text-white min-h-screen flex items-center justify-center p-6">
    <div class="max-w-md w-full bg-white text-slate-900 rounded-2xl p-8 shadow-xl">
        
        <div class="text-center mb-8">
            <div class="w-16 h-16 bg-indigo-600 rounded-2xl flex items-center justify-center text-white font-bold text-2xl mx-auto mb-4 shadow-md shadow-indigo-600/10">
                AH
            </div>
            <!-- Judul Dinamis -->
            <h1 class="text-2xl font-extrabold tracking-tight text-slate-900">
                {{ $title ?? 'Admin Login' }}
            </h1>
            <p class="text-sm text-slate-500 mt-1">
                {{ $subtitle ?? 'AmikomEventHub Dashboard' }}
            </p>
        </div>

        {{-- Flash Message Error (Merah) --}}
        @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-600 p-4 rounded-2xl mb-6 font-semibold text-sm text-center">
            {{ session('error') }}
        </div>
        @endif

        {{-- Flash Message Success / Logout (Hijau) --}}
        @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-600 p-4 rounded-2xl mb-6 font-semibold text-sm text-center">
            {{ session('success') }}
        </div>
        @endif

        <form action="{{ $actionUrl ?? request()->url() }}" method="POST" class="space-y-5">
            @csrf
            
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-2 uppercase tracking-wider">Email Address</label>
                <input type="email" name="email" value="{{ old('email') }}" 
                    class="w-full px-5 py-3.5 bg-slate-50 border-2 border-slate-100 rounded-2xl text-slate-900 placeholder-slate-400 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-600 outline-none transition font-medium text-sm" 
                    placeholder="nama@amikom.ac.id" required autocomplete="email">
            </div>
            
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-2 uppercase tracking-wider">Password</label>
                <input type="password" name="password" 
                    class="w-full px-5 py-3.5 bg-slate-50 border-2 border-slate-100 rounded-2xl text-slate-900 placeholder-slate-400 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-600 outline-none transition font-medium text-sm" 
                    placeholder="••••••••" required>
            </div>

            <button type="submit" 
                class="w-full py-4 bg-indigo-600 hover:bg-indigo-700 text-white rounded-2xl font-bold text-sm tracking-wide shadow-lg shadow-indigo-600/20 transition active:scale-[0.99] mt-2">
                Masuk ke Dashboard
            </button>
        </form>
    </div>
</body>

</html>