<?php

use App\Http\Middleware\CekPeran;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

/*
|--------------------------------------------------------------------------
| PHP 8.5 bootstrap noise guard
|--------------------------------------------------------------------------
| Laravel 11's bundled default config (vendor/laravel/framework/config/database.php)
| still references the PDO::MYSQL_ATTR_SSL_CA constant, which is deprecated in
| PHP 8.5. That file is evaluated by LoadConfiguration *before* HandleExceptions
| installs the framework error handler, so the notice leaks straight to stderr on
| every artisan command. We lower error_reporting for this bootstrap window only;
| Laravel resets it to E_ALL once HandleExceptions boots, so deprecations coming
| from application code are still reported normally.
|
| Tests are unaffected by this (PHPUnit installs its own handler); the committed
| baseline.xml documents the same framework deprecation for the test report.
*/
error_reporting(E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED);

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Laravel 11 tidak lagi memakai app/Http/Kernel.php, sehingga alias
        // middleware kustom didaftarkan di sini.
        $middleware->alias([
            'cek.peran' => CekPeran::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
