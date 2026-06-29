<?php

use App\Http\Middleware\CheckPrivilege;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->trustProxies(at: env('TRUSTED_PROXIES', ''), headers: Request::HEADER_X_FORWARDED_FOR | Request::HEADER_X_FORWARDED_HOST | Request::HEADER_X_FORWARDED_PORT | Request::HEADER_X_FORWARDED_PROTO);

        $middleware->redirectGuestsTo(fn () => route('login', absolute: false));
        $middleware->redirectUsersTo(fn () => route('dashboard', absolute: false));

        $middleware->web(append: [
            \App\Http\Middleware\HandleInertiaRequests::class,
            \Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class,
        ]);

        $middleware->validateCsrfTokens(except: []);

        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
            'check_privilege' => CheckPrivilege::class,
            'log_activity' => \App\Http\Middleware\LogUserActivity::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {

        $exceptions->respond(function ($response) {
            $message = "Nous sommes désolés, une erreur technique s'est produite lors du traitement de votre demande. Merci de réessayer ultérieurement.";

            if ($response->getStatusCode() === 506) {
                return back()->with("data", [ "notif" => "ERROR_500", "message" => $message ]);
            }

            if(app()->environment('production') && $response->getStatusCode() === 500) {
                return back()->with([
                    "data" => [ "notif" => "ERROR_500", "message" => $message ],
                    "message.error" => $message
                ]);
            }

            return $response;
        });
    })->create();
