<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran Organisasi - Event Platform</title>
    <!-- CDN Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Import Font Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body { 
            font-family: 'Poppins', sans-serif; 
        }
    </style>
</head>
<body class="min-h-screen py-12 px-4 sm:px-6 lg:px-8 flex flex-col justify-center">

    <div class="sm:mx-auto sm:w-full sm:max-w-2xl">
        <!-- Logo / Branding Header -->
        <div class="text-center mb-8">
            <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-600 ring-1 ring-inset ring-indigo-500/20 mb-3">
                🚀 Multi-Tenant Event SaaS
            </span>
            <h2 class="text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl">
                Daftarkan Organisasi Anda
            </h2>
            <p class="mt-2 text-sm text-slate-500 font-normal">
                Mulai kelola acara, penjualan tiket, dan analitik pendapatan HIMA/UKM kamu dalam satu dashboard.
            </p>
        </div>

        <!-- Main Card Form -->
        <div class="bg-white py-8 px-6 shadow-xl shadow-slate-200/50 rounded-2xl border border-slate-100 sm:px-10">
            
            {{-- Notifikasi Error Validasi Global --}}
            @if(session('error'))
                <div class="mb-6 p-4 bg-rose-50 border border-rose-100 text-rose-600 rounded-xl font-medium text-xs">
                    {{ session('error') }}
                </div>
            @endif
            @if($errors->any())
                <div class="mb-6 p-4 bg-rose-50 border border-rose-100 text-rose-600 rounded-xl font-medium text-xs space-y-1">
                    <p class="font-bold">Mohon perbaiki kesalahan berikut:</p>
                    <ul class="list-disc pl-4 space-y-0.5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('register.organizer.store') }}" method="POST" class="space-y-8">
                @csrf

                <!-- SEKSI 1: Informasi Organisasi -->
                <div>
                    <div class="border-b border-slate-100 pb-4 mb-6">
                        <h3 class="text-base font-semibold leading-6 text-slate-900 flex items-center gap-2">
                            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-7h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                            Informasi Organisasi / Tenant
                        </h3>
                        <p class="text-xs text-slate-400 font-light">Profil ini akan ditampilkan pada halaman publik acara Anda.</p>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-medium text-slate-700 mb-1.5">Nama UKM / HIMA / Organisasi <span class="text-rose-500">*</span></label>
                            <input type="text" name="organization_name" required placeholder="Contoh: HIMA Informatika"
                                class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600 transition placeholder:text-slate-400 font-normal">
                        </div>

                        <!-- Rekening Bank Grid -->
                        <div class="bg-slate-50/80 p-4 rounded-xl border border-slate-200/60 space-y-3">
                            <span class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider block">Rekening Pencairan Dana (Payout)</span>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                <div>
                                    <label class="block text-xs font-medium text-slate-600 mb-1">Bank</label>
                                    <select name="bank_name" class="w-full px-3 py-2 text-sm rounded-lg border border-slate-200 bg-white focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600 font-normal">
                                        <option value="">Pilih Bank</option>
                                        <option value="BCA">BCA</option>
                                        <option value="Mandiri">Mandiri</option>
                                        <option value="BRI">BRI</option>
                                        <option value="BNI">BNI</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-slate-600 mb-1">No. Rekening</label>
                                    <input type="number" name="bank_account" placeholder="1234567890" 
                                        class="w-full px-3 py-2 text-sm rounded-lg border border-slate-200 focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600 font-normal">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-slate-600 mb-1">Atas Nama (A.N)</label>
                                    <input type="text" name="bank_holder" placeholder="HIMA IF Official" 
                                        class="w-full px-3 py-2 text-sm rounded-lg border border-slate-200 focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600 font-normal">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SEKSI 2: Akun Pengelola / Admin -->
                <div>
                    <div class="border-b border-slate-100 pb-4 mb-6">
                        <h3 class="text-base font-semibold leading-6 text-slate-900 flex items-center gap-2">
                            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            Akun Penanggung Jawab (Admin Tenant)
                        </h3>
                        <p class="text-xs text-slate-400 font-light">Gunakan akun ini untuk login ke Dashboard Pengelola nantinya.</p>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-medium text-slate-700 mb-1.5">Nama Penanggung Jawab <span class="text-rose-500">*</span></label>
                            <input type="text" name="name" required placeholder="Nama lengkap ketua / PJ acara" 
                                class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600 transition placeholder:text-slate-400 font-normal">
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-slate-700 mb-1.5">Email Resmi Organisasi <span class="text-rose-500">*</span></label>
                            <input type="email" name="email" required placeholder="hima@kampus.ac.id" 
                                class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600 transition placeholder:text-slate-400 font-normal">
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-medium text-slate-700 mb-1.5">Password <span class="text-rose-500">*</span></label>
                                <input type="password" name="password" required placeholder="••••••••" 
                                    class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600 transition font-normal">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-700 mb-1.5">Konfirmasi Password <span class="text-rose-500">*</span></label>
                                <input type="password" name="password_confirmation" required placeholder="••••••••" 
                                    class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600 transition font-normal">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit Button & Info Approval -->
                <div class="pt-2">
                    <button type="submit" 
                        class="w-full py-3.5 px-4 inline-flex justify-center items-center gap-x-2 text-sm font-semibold rounded-xl border border-transparent bg-indigo-600 text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-all shadow-lg shadow-indigo-200">
                        Daftarkan Organisasi
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </button>
                    
                    <p class="mt-4 text-center text-xs text-slate-400 font-light">
                        🔒 Pendaftaran akan ditinjau oleh Superadmin dalam 1x24 jam sebelum akun diaktifkan.
                    </p>
                </div>

            </form>
        </div>
    </div>

</body>
</html>