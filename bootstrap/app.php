<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

// Import middleware yang akan dipakai
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful; // Middleware Sanctum
use Illuminate\Routing\Middleware\SubstituteBindings; // Middleware default Laravel untuk binding model

return Application::configure(basePath: dirname(__DIR__))
    // Routing utama aplikasi
    ->withRouting(
        web: __DIR__.'/../routes/web.php',       // Route untuk halaman web
        api: __DIR__.'/../routes/api.php',       // Route untuk API (wajib ditambah kalau mau bikin API)
        commands: __DIR__.'/../routes/console.php', // Route untuk command artisan (console)
        health: '/up',                           // Endpoint health check default
    )

    // Konfigurasi middleware
    ->withMiddleware(function (Middleware $middleware): void {
        // Middleware khusus untuk grup API
        $middleware->api(prepend: [
            EnsureFrontendRequestsAreStateful::class, // Middleware Sanctum agar token dikenali
            'throttle:api',                           // Batasi request API (rate limiting)
            SubstituteBindings::class,                // Otomatis resolve model di route parameter
        ]);
    })

    // Konfigurasi exception handler (error handling)
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })

    // Buat instance aplikasi
    ->create();
