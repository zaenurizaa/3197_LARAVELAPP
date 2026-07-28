<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class TenantProfileController extends Controller
{
    public function edit()
    {
        $user = Auth::guard('organizer')->user() ?? auth()->user();
        $tenant = $user->tenant;

        if (!$tenant) {
            abort(404, 'Data Organisasi/Tenant tidak ditemukan.');
        }

        return view('admin.profile.edit', compact('user', 'tenant'));
    }

    public function update(Request $request)
    {
        $user = Auth::guard('organizer')->user() ?? auth()->user();
        $tenant = $user->tenant;

        if (!$tenant) {
            abort(404, 'Data Organisasi/Tenant tidak ditemukan.');
        }

        $request->validate([
            // Info Organisasi
            'organization_name' => 'required|string|max:255',
            'bank_name'         => 'required|string|max:100',
            'bank_account'      => 'required|string|max:100',
            'bank_holder'       => 'required|string|max:255',
            
            // Info User Akun
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        // 1. Update Organisasi / Tenant
        $tenant->update([
            'name'                => $request->organization_name,
            'bank_name'           => $request->bank_name,
            'bank_account_number' => $request->bank_account,
            'bank_account_holder' => $request->bank_holder,
        ]);

        // 2. Update Akun Pengguna Organizer
        $userData = [
            'name'  => $request->name,
            'email' => $request->email,
        ];

        if ($request->filled('password')) {
            $userData['password'] = Hash::make($request->password);
        }

        $user->update($userData);

        return redirect()->route('organizer.profile.edit')->with('success', 'Profil Organisasi & Akun Anda berhasil diperbarui.');
    }
}
