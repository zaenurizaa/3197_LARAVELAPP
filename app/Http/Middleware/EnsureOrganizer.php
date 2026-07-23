<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Tenant; // <--- Atau App\Models\Organizer (sesuai nama model kamu)

class EnsureOrganizer
{
    public function handle(Request $request, Closure $next)
    {
        // 1. Ambil user dari guard organizer atau default auth
        $user = Auth::guard('organizer')->user() ?? Auth::user();

        // 2. Cek apakah user login DAN rolenya organizer / tenant
        // Menggunakan method bawaan model User ($user->isOrganizer()) agar lebih aman & praktis
        if ($user && method_exists($user, 'isOrganizer') && $user->isOrganizer()) {
            return $next($request);
        }

        // Alternatif jika mengecek via relation/method tenant:
        if ($user && $user->tenant) {
            return $next($request);
        }

        // 3. Jika gagal/bukan organizer, redirect balik ke login organizer
        return redirect()->route('organizer.login')->with('error', 'Akses ditolak. Silakan login sebagai Organizer.');
    }
}