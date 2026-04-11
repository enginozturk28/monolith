<?php

namespace App\Providers\Filament;

use App\Filament\Widgets\ContentStatsOverview;
use App\Filament\Widgets\LatestArticles;
use App\Filament\Widgets\UnreadContactMessages;
use Filament\FontProviders\GoogleFontProvider;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Enums\Width;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->profile(isSimple: false) // Filament built-in EditProfile sayfası
            ->brandName('Loğoğlu Hukuk Bürosu')
            ->brandLogo(fn () => view('filament.admin.brand-logo'))
            ->favicon(asset('favicon.ico'))
            // Renk paleti — Loğoğlu lacivert + altın vurgu.
            // Filament primary'yi tek hex'ten tüm shade'leri (50-950) otomatik üretir.
            ->colors([
                'primary' => '#0B1F3A',    // Koyu lacivert — ana buton, link, odak
                'gray' => '#1F2530',       // Antrasit — secondary surface
                'success' => '#5A7A52',    // Sakin yeşil (kampanya kırmızısı değil)
                'warning' => '#A88A55',    // Altın vurgu
                'danger' => '#8B3A3A',     // Mat bordo (parlak kırmızı değil)
                'info' => '#4A6B8C',       // Mat mavi
            ])
            ->font('Inter', provider: GoogleFontProvider::class)
            ->darkMode(false) // Hukuk bürosu kurumsal tonu için tek mod, sakinlik
            ->sidebarCollapsibleOnDesktop()
            ->maxContentWidth(Width::Full)
            ->navigationGroups([
                'İçerik',
                'İletişim',
                'Ayarlar',
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                ContentStatsOverview::class,
                LatestArticles::class,
                UnreadContactMessages::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
