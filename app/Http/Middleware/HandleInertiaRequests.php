<?php

namespace App\Http\Middleware;

use App\Models\Service;
use App\Services\TurnstileVerifier;
use App\Support\ThemeManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $theme = app(ThemeManager::class);

        return [
            ...parent::share($request),

            'site' => fn () => app(\App\Support\SiteSettings::class)->forInertia(),

            'theme' => fn () => $theme->forInertia(),

            // Footer'da listelenecek faaliyet alanları — cache'li, ServiceResource
            // save'lerinde invalidate edilmeli (Faz 3'te event listener ile).
            'footerServices' => fn () => Cache::remember(
                'monolith.footer_services',
                now()->addHours(6),
                fn () => Service::query()
                    ->where('is_published', true)
                    ->orderBy('sort_order')
                    ->orderBy('title')
                    ->get(['slug', 'title'])
                    ->map(fn ($s) => ['slug' => $s->slug, 'title' => $s->title])
                    ->values()
                    ->all()
            ),

            // Cloudflare Turnstile site key (frontend için, public — secret değil).
            // Eğer panel'den girilmemişse null gelir, frontend formu honeypot
            // ile çalışır. Doluysa Contact.tsx Turnstile widget'ı yükler.
            'turnstile' => fn () => [
                'enabled' => app(TurnstileVerifier::class)->isEnabled(),
                'siteKey' => app(TurnstileVerifier::class)->getSiteKey(),
            ],

            'flash' => fn () => [
                'success' => $request->session()->get('success'),
                'error' => $request->session()->get('error'),
            ],
        ];
    }
}
