<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; 
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // Cek spesifik ke Guard 'admin' tanpa fallback yang membingungkan
        if (Auth::guard('admin')->check()) {
            return $next($request);
        }

        return redirect()->route('admin.login')->with('error', 'Akses ditolak. Area ini khusus Admin.');
    }
}