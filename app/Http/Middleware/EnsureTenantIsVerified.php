<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTenantIsVerified
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        // Pengecekan khusus untuk role Organizer/Tenant Admin
        if ($user && ($user->isOrganizer() || $user->role === 'organizer')) {
            
            // 1. Cek jika tidak terikat ke Tenant mana pun
            if (!$user->tenant) {
                auth()->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('admin.login')->with('error', 'Akun Anda tidak terhubung dengan organisasi mana pun.');
            }

            // 2. Jika status tenant bukan 'verified' (misal: pending/suspended/rejected)
            if ($user->tenant->status !== 'verified') {
                return redirect()->route('admin.dashboard')->with('error', 'Fitur ini terkunci. Organisasi Anda belum diverifikasi oleh Superadmin.');
            }
        }

        return $next($request);
    }
}