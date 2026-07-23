<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SeparateSessionPerDomain
{
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Jika URL berawalan /admin, gunakan cookie session khusus Admin
        if ($request->is('admin*')) {
            config(['session.cookie' => 'admin_session']);
        } 
        // 2. Jika URL berawalan /organizer, gunakan cookie session khusus Organizer
        elseif ($request->is('organizer*')) {
            config(['session.cookie' => 'organizer_session']);
        }
        // 3. Sisanya (publik/user biasa) gunakan cookie session default
        else {
            config(['session.cookie' => 'web_session']);
        }

        return $next($request);
    }
}