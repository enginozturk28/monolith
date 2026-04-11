<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Page;
use App\Models\Service;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;

/**
 * SitemapController — /sitemap.xml endpoint'i.
 *
 * Tüm yayında olan sayfaları tek bir XML sitemap olarak döner. Static
 * sayfalar (home, hakkımızda, vb.) + dinamik içerik (services, articles,
 * pages slugları) tek bir akışta.
 *
 * Cache: 6 saat. İçerik nadiren değişir, üretim DB'sinden taze çekmek
 * için gereksiz yük. Setting::saved hook bu cache'i invalidate etmez —
 * cache TTL'i ile yenilenir.
 *
 * Format: XML 1.0, sitemap.org 0.9 schema. Google + Bing + diğer
 * tüm crawler'lar bu standardı destekler.
 */
class SitemapController extends Controller
{
    public function __invoke(): Response
    {
        $xml = Cache::remember('monolith.sitemap', now()->addHours(6), function () {
            return $this->buildXml();
        });

        return response($xml, 200)
            ->header('Content-Type', 'application/xml; charset=utf-8');
    }

    private function buildXml(): string
    {
        $urls = [];

        // 1) Statik sayfalar
        $staticRoutes = [
            ['url' => route('home'), 'priority' => '1.0', 'changefreq' => 'weekly'],
            ['url' => route('about'), 'priority' => '0.8', 'changefreq' => 'monthly'],
            ['url' => route('services.index'), 'priority' => '0.9', 'changefreq' => 'monthly'],
            ['url' => route('articles.index'), 'priority' => '0.8', 'changefreq' => 'weekly'],
            ['url' => route('faq'), 'priority' => '0.7', 'changefreq' => 'monthly'],
            ['url' => route('contact'), 'priority' => '0.7', 'changefreq' => 'yearly'],
        ];

        foreach ($staticRoutes as $route) {
            $urls[] = [
                'loc' => $route['url'],
                'lastmod' => now()->toAtomString(),
                'changefreq' => $route['changefreq'],
                'priority' => $route['priority'],
            ];
        }

        // 2) Faaliyet alanları
        Service::query()
            ->where('is_published', true)
            ->orderBy('sort_order')
            ->get(['slug', 'updated_at'])
            ->each(function (Service $service) use (&$urls) {
                $urls[] = [
                    'loc' => route('services.show', $service->slug),
                    'lastmod' => $service->updated_at?->toAtomString() ?? now()->toAtomString(),
                    'changefreq' => 'monthly',
                    'priority' => '0.7',
                ];
            });

        // 3) Yayında olan makaleler
        Article::query()
            ->where('is_published', true)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->orderByDesc('published_at')
            ->get(['slug', 'updated_at'])
            ->each(function (Article $article) use (&$urls) {
                $urls[] = [
                    'loc' => route('articles.show', $article->slug),
                    'lastmod' => $article->updated_at?->toAtomString() ?? now()->toAtomString(),
                    'changefreq' => 'monthly',
                    'priority' => '0.6',
                ];
            });

        // 4) Statik (CMS) sayfalar — KVKK, Çerez, Vizyon, Misyon
        Page::query()
            ->where('is_published', true)
            ->get(['slug', 'updated_at'])
            ->each(function (Page $page) use (&$urls) {
                $urls[] = [
                    'loc' => route('page.show', $page->slug),
                    'lastmod' => $page->updated_at?->toAtomString() ?? now()->toAtomString(),
                    'changefreq' => 'yearly',
                    'priority' => '0.4',
                ];
            });

        return $this->renderXml($urls);
    }

    /**
     * @param  array<int, array{loc: string, lastmod: string, changefreq: string, priority: string}>  $urls
     */
    private function renderXml(array $urls): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";

        foreach ($urls as $url) {
            $xml .= "  <url>\n";
            $xml .= '    <loc>'.htmlspecialchars($url['loc'], ENT_XML1).'</loc>'."\n";
            $xml .= '    <lastmod>'.$url['lastmod'].'</lastmod>'."\n";
            $xml .= '    <changefreq>'.$url['changefreq'].'</changefreq>'."\n";
            $xml .= '    <priority>'.$url['priority'].'</priority>'."\n";
            $xml .= "  </url>\n";
        }

        $xml .= '</urlset>'."\n";

        return $xml;
    }
}
