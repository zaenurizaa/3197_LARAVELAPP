<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        
        // Handling Unauthenticated Redirect (Memilih pintu login yang sesuai URL)
        $middleware->redirectGuestsTo(function (Request $request) {
            if ($request->is('organizer') || $request->is('organizer/*')) {
                return route('organizer.login');
            }
            
            if ($request->is('admin') || $request->is('admin/*')) {
                return route('admin.login');
            }

            return route('admin.login');
        });

        // Register Aliases
        $middleware->alias([
            'admin'            => \App\Http\Middleware\AdminMiddleware::class,
            'ensure.organizer' => \App\Http\Middleware\EnsureOrganizer::class,
            'tenant.verified'  => \App\Http\Middleware\EnsureTenantIsVerified::class,
        ]);
        
        // Bypass CSRF untuk Webhook Midtrans
        $middleware->validateCsrfTokens(except: [
            'midtrans/callback', 
        ]);
        
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();