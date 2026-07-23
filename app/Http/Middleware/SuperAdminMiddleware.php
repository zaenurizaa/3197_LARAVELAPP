<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SuperAdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Pastikan user sudah login dan role-nya adalah 'superadmin'
        if (Auth::check() && Auth::user()->role === 'superadmin') {
            return $next($request);
        }

        // Jika bukan superadmin, lempar kembali ke dashboard dengan pesan error
        return redirect()->route('admin.dashboard')
            ->with('error', 'Anda tidak memiliki hak akses Superadmin.');
    }
}