<?php

use App\Exceptions\Business\BusinessException;
use App\Http\Middleware\CheckRole;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\HandleInertiaRequests;
use App\Http\Middleware\IpWhitelist;
use App\Http\Middleware\TrustProxies;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [
            HandleInertiaRequests::class,
            TrustProxies::class,
            IpWhitelist::class,
        ]);

        $middleware->api(append: [
            TrustProxies::class,
            IpWhitelist::class,
        ]);

        $middleware->alias([
            'role' => CheckRole::class,
        ]);
        $middleware->validateCsrfTokens(except: [
            '*',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (BusinessException $e, Request $request) {
            return response()->json(400);
            if ($request->wantsJson()) {
                $json = [
                    'success' => false,
                    'error' => $e->getUserMessage(),
                ];

                return response()->json($json, 400);
            }

            if ($e->isFlash()) {
                return redirect()->back()->with('flash.error', $e->getUserMessage());
            }

            return redirect()->back()->withErrors($e->getUserMessage());
        });
    })->create();
