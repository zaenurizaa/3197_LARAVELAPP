<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; 
use Symfony\Component\Component\HttpFoundation\Response;

class AdminMiddleware
{
    
   public function handle(Request $request, Closure $next)
{
    if (!Auth::check() || Auth::user()->role !== 'admin') {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('admin.login')->with('error', 'Sesi Anda telah habis atau Anda tidak memiliki hak akses.');
    }

    return $next($request);
}
    
}