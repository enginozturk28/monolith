<?php

use App\Http\Middleware\HandleInertiaRequests;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Cloudpanel Nginx reverse proxy arkasında çalışıyoruz.
        // X-Forwarded-Proto, X-Forwarded-For, X-Forwarded-Host, X-Forwarded-Port
        // header'larını Laravel'in okuması için proxy'yi trust etmek gerekiyor.
        // `*` tüm proxy'leri kapsar — Cloudpanel genelde 127.0.0.1'de çalışır
        // ama docker/farklı setup'larda değişebilir, en güvenlisi wildcard.
        $middleware->trustProxies(at: '*', headers:
            Request::HEADER_X_FORWARDED_FOR |
            Request::HEADER_X_FORWARDED_HOST |
            Request::HEADER_X_FORWARDED_PORT |
            Request::HEADER_X_FORWARDED_PROTO |
            Request::HEADER_X_FORWARDED_AWS_ELB
        );

        $middleware->web(append: [
            HandleInertiaRequests::class,
            AddLinkHeadersForPreloadedAssets::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // HTTP hata sayfalarını Inertia ile render et — MainLayout, header,
        // footer, tema tokenları hepsi çalışır. Admin route'larını hariç tut
        // çünkü Filament kendi hata sayfalarını yönetiyor.
        $exceptions->respond(function (Response $response, Throwable $e, Request $request) {
            $status = $response->getStatusCode();

            if (in_array($status, [403, 404, 500, 503]) && ! $request->is('admin/*')) {
                return Inertia::render('Error', ['status' => $status])
                    ->toResponse($request)
                    ->setStatusCode($status);
            }

            return $response;
        });
    })->create();
