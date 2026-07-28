@extends('layout.admin')

@section('title', 'Edit Profil Organisasi')

@section('page_title', 'Edit Profil Organisasi')
@section('page_subtitle', 'Perbarui informasi profil UKM/Organisasi dan rekening bank pencairan dana Anda.')

@section('content')
<div class="max-w-3xl bg-white rounded-3xl border border-slate-100 shadow-sm p-8">
    <form action="{{ route('organizer.profile.update') }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="border-b border-slate-100 pb-4">
            <h3 class="text-lg font-black text-slate-800">Detail Organisasi / UKM</h3>
            <p class="text-xs text-slate-400">Informasi ini akan muncul sebagai penyelenggara acara Anda.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Nama Organisasi / HIMA / UKM</label>
                <input type="text" name="organization_name" value="{{ old('organization_name', $tenant->name) }}" 
                       class="w-full px-4 py-3 rounded-xl border border-slate-200 text-slate-700 font-medium text-sm focus:outline-none focus:border-indigo-600 bg-slate-50" required>
            </div>
            
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Nama Bank</label>
                <input type="text" name="bank_name" value="{{ old('bank_name', $tenant->bank_name) }}" placeholder="Contoh: BANK BNI / MANDIRI / BRI" 
                       class="w-full px-4 py-3 rounded-xl border border-slate-200 text-slate-700 font-medium text-sm focus:outline-none focus:border-indigo-600 bg-slate-50" required>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Nomor Rekening</label>
                <input type="text" name="bank_account" value="{{ old('bank_account', $tenant->bank_account_number) }}" placeholder="Masukkan no. rekening pencairan dana" 
                       class="w-full px-4 py-3 rounded-xl border border-slate-200 text-slate-700 font-medium text-sm focus:outline-none focus:border-indigo-600 bg-slate-50" required>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Nama Pemilik Rekening</label>
                <input type="text" name="bank_holder" value="{{ old('bank_holder', $tenant->bank_account_holder) }}" placeholder="Nama pemilik rekening sesuai buku tabungan" 
                       class="w-full px-4 py-3 rounded-xl border border-slate-200 text-slate-700 font-medium text-sm focus:outline-none focus:border-indigo-600 bg-slate-50" required>
            </div>
        </div>

        <div class="border-b border-slate-100 pb-4 pt-6">
            <h3 class="text-lg font-black text-slate-800">Akun Pengelola (Organizer)</h3>
            <p class="text-xs text-slate-400">Informasi kredensial login Anda ke dashboard.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Nama Admin Pengelola</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" 
                       class="w-full px-4 py-3 rounded-xl border border-slate-200 text-slate-700 font-medium text-sm focus:outline-none focus:border-indigo-600 bg-slate-50" required>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Email Admin</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" 
                       class="w-full px-4 py-3 rounded-xl border border-slate-200 text-slate-700 font-medium text-sm focus:outline-none focus:border-indigo-600 bg-slate-50" required>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Password Baru (Opsional)</label>
                <input type="password" name="password" placeholder="Kosongkan jika tidak ingin mengubah password" 
                       class="w-full px-4 py-3 rounded-xl border border-slate-200 text-slate-700 font-medium text-sm focus:outline-none focus:border-indigo-600 bg-slate-50">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Konfirmasi Password Baru</label>
                <input type="password" name="password_confirmation" placeholder="Masukkan ulang password baru" 
                       class="w-full px-4 py-3 rounded-xl border border-slate-200 text-slate-700 font-medium text-sm focus:outline-none focus:border-indigo-600 bg-slate-50">
            </div>
        </div>

        <div class="pt-6 border-t border-slate-100 flex justify-end">
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-8 py-3.5 rounded-xl font-bold text-xs uppercase tracking-wider transition shadow-md shadow-indigo-100">
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>
@endsection
