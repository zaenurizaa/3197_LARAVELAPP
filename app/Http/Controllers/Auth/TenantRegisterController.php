<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TenantRegisterController extends Controller
{
    // Menampilkan Form Pendaftaran UKM / Organisasi
    public function showRegistrationForm()
    {
        return view('auth.register-tenant');
    }

    // Memproses Pendaftaran UKM
    public function register(Request $request)
    {
        $request->validate([
            // Data Organisasi / Tenant
            'organization_name' => 'required|string|max:255',
            'bank_name'         => 'required|string|max:100',
            'bank_account'      => 'required|string|max:100',
            'bank_holder'       => 'required|string|max:255',
            
            // Data User
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        DB::transaction(function () use ($request) {
            // 1. Buat Data Tenant / Organisasi Baru (Status Pending)
            $tenant = Tenant::create([
                'name'                => $request->organization_name,
                'slug'                => Str::slug($request->organization_name) . '-' . Str::random(5),
                'status'              => 'pending', // Menunggu verifikasi Superadmin
                'bank_name'           => $request->bank_name,
                'bank_account_number' => $request->bank_account,
                'bank_account_holder' => $request->bank_holder,
            ]);

            // 2. Buat User Admin yang terhubung ke Tenant ini
            return User::create([
                'tenant_id' => $tenant->id,
                'name'      => $request->name,
                'email'     => $request->email,
                'password'  => Hash::make($request->password),
                'role'      => 'organizer',
            ]);
        });

        // 🔥 Redirect ke Halaman Depan / Utama dengan Notifikasi Toast
        return redirect('/')
            ->with('success', 'Pendaftaran tenant berhasil dikirim! Akun Anda sedang ditinjau oleh Superadmin.');
    }
}