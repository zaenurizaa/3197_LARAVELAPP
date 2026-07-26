<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // ==========================================
    // 1. AUTH UNTUK SUPERADMIN (GUARD: admin)
    // ==========================================
    public function showAdminLogin() {
        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.dashboard');
        }

        return view('Auth.login', [
            'title'     => 'Admin Login',
            'subtitle'  => 'Masuk sebagai Superadmin System',
            'actionUrl' => route('admin.login'), // Jika di routes web kamu pakai admin.login
        ]);
    }

    public function adminLogin(Request $request) {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::guard('admin')->attempt($credentials)) {
            $user = Auth::guard('admin')->user();

            // 🔥 VALIDASI: Jika Tenant/Organizer mencoba login lewat Pintu Admin
            if (!$user->isSuperAdmin()) {
                Auth::guard('admin')->logout();
                $request->session()->invalidate();
                return back()->with('error', 'Akses ditolak! Anda bukan Superadmin.');
            }

            $request->session()->regenerate();
            return redirect()->route('admin.dashboard');
        }

        return back()->with('error', 'Email atau Password Admin tidak valid.');
    }

    public function adminLogout(Request $request) {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')->with('success', 'Anda telah berhasil logout.');
    }

    // ==========================================
    // 2. AUTH UNTUK ORGANIZER / TENANT (GUARD: organizer)
    // ==========================================
    public function showOrganizerLogin() {
        if (Auth::guard('organizer')->check()) {
            return redirect()->route('organizer.dashboard');
        }

        return view('Auth.login', [
            'title'     => 'Organizer Login',
            'subtitle'  => 'Masuk sebagai Pengelola Event / Tenant',
            'actionUrl' => route('organizer.login'),
        ]);
    }

    public function organizerLogin(Request $request) {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::guard('organizer')->attempt($credentials)) {
            $user = Auth::guard('organizer')->user();

            // 🔥 VALIDASI: Jika Admin mencoba login lewat Pintu Organizer
            if (!$user->isOrganizer()) {
                Auth::guard('organizer')->logout();
                $request->session()->invalidate();
                return back()->with('error', 'Akses ditolak! Akun ini khusus Organizer/Tenant.');
            }

            $request->session()->regenerate();
            return redirect()->route('organizer.dashboard');
        }

        return back()->with('error', 'Email atau Password Organizer tidak valid.');
    }

    public function organizerLogout(Request $request) {
        Auth::guard('organizer')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('organizer.login')->with('success', 'Anda telah berhasil logout.');
    }
}