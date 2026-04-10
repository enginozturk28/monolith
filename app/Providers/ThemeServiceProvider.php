<?php

namespace App\Providers;

use App\Support\SiteSettings;
use App\Support\ThemeManager;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ThemeServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ThemeManager::class);
        $this->app->singleton(SiteSettings::class);
    }

    public function boot(): void
    {
        // Inertia root view'ine her zaman tema CSS + Google Fonts URL'ini geçir
        View::composer('app', function ($view) {
            $theme = app(ThemeManager::class);
            $view->with([
                'themeCss' => $theme->asInlineCss(),
                'googleFontsUrl' => $theme->googleFontsUrl(),
            ]);
        });
    }
}
