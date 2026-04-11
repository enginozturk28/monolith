<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

/**
 * RobotsController — /robots.txt endpoint'i.
 *
 * Environment'a göre içerik değişir:
 * - production: tüm crawler'lara izin, sitemap referansı dahil
 * - staging / local / diğer: tamamen disallow (henüz canlı değil, indexleme yok)
 *
 * Bu yaklaşım .env dosyasında APP_ENV değiştirildiğinde otomatik geçer,
 * production'a deploy edildiğinde index edilebilir hale gelir.
 */
class RobotsController extends Controller
{
    public function __invoke(): Response
    {
        $content = app()->environment('production')
            ? $this->productionRobots()
            : $this->stagingRobots();

        return response($content, 200)
            ->header('Content-Type', 'text/plain; charset=utf-8');
    }

    private function productionRobots(): string
    {
        $sitemap = url('/sitemap.xml');

        return <<<TXT
User-agent: *
Allow: /
Disallow: /admin
Disallow: /admin/*
Disallow: /livewire/*

Sitemap: {$sitemap}
TXT;
    }

    private function stagingRobots(): string
    {
        return <<<TXT
# Staging environment — search engines, please do not index.
User-agent: *
Disallow: /
TXT;
    }
}
