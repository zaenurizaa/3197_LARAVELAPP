<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // 1. Force HTTPS jika menggunakan Ngrok / Proxy
        if (str_contains(request()->getHttpHost(), 'ngrok-free.dev') || request()->header('X-Forwarded-Proto') === 'https') {
            URL::forceScheme('https');
        }

        // 2. Set Cookie Session Name dinamis sebelum Session Driver di-boot
        $path = request()->getPathInfo();

        if (str_starts_with($path, '/admin')) {
            config(['session.cookie' => 'admin_session']);
        } elseif (str_starts_with($path, '/organizer')) {
            config(['session.cookie' => 'organizer_session']);
        } else {
            config(['session.cookie' => 'web_session']);
        }
    }
}