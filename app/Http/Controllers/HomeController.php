<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Service;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    public function __invoke(): Response
    {
        $services = Service::query()
            ->where('is_published', true)
            ->orderBy('sort_order')
            ->orderBy('title')
            ->get(['id', 'slug', 'title', 'icon', 'summary'])
            ->map(fn ($s) => [
                'slug' => $s->slug,
                'title' => $s->title,
                'icon' => $s->icon,
                'summary' => $s->summary,
            ])
            ->values();

        $articles = Article::query()
            ->with('category:id,slug,name')
            ->published()
            ->latestFirst()
            ->limit(3)
            ->get(['id', 'article_category_id', 'slug', 'title', 'excerpt', 'published_at', 'reading_time_minutes'])
            ->map(fn ($a) => [
                'slug' => $a->slug,
                'title' => $a->title,
                'excerpt' => $a->excerpt,
                'published_at' => $a->published_at?->toIso8601String(),
                'reading_time_minutes' => $a->reading_time_minutes,
                'category' => $a->category ? [
                    'slug' => $a->category->slug,
                    'name' => $a->category->name,
                ] : null,
            ])
            ->values();

        return Inertia::render('Home', [
            'services' => $services,
            'articles' => $articles,
        ]);
    }
}
