<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\ArticleCategory;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ArticleController extends Controller
{
    public function index(Request $request): Response
    {
        $categorySlug = $request->string('kategori')->toString() ?: null;

        $articles = Article::query()
            ->with('category:id,slug,name')
            ->published()
            ->latestFirst()
            ->when($categorySlug, fn ($q) => $q->whereHas(
                'category',
                fn ($cq) => $cq->where('slug', $categorySlug)
            ))
            ->paginate(9)
            ->through(fn (Article $a) => [
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
            ->withQueryString();

        $categories = ArticleCategory::query()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->withCount(['articles' => fn ($q) => $q->published()])
            ->get(['id', 'slug', 'name'])
            ->map(fn ($c) => [
                'slug' => $c->slug,
                'name' => $c->name,
                'count' => $c->articles_count,
            ])
            ->values();

        return Inertia::render('Articles/Index', [
            'articles' => $articles,
            'categories' => $categories,
            'currentCategory' => $categorySlug,
        ]);
    }

    public function show(Article $article): Response
    {
        abort_if(! $article->is_published, 404);
        abort_if($article->published_at === null || $article->published_at->isFuture(), 404);

        $article->load('category:id,slug,name', 'author:id,name');

        $related = Article::query()
            ->with('category:id,slug,name')
            ->published()
            ->where('id', '!=', $article->id)
            ->when($article->article_category_id, fn ($q) => $q->where('article_category_id', $article->article_category_id))
            ->latestFirst()
            ->limit(3)
            ->get(['id', 'article_category_id', 'slug', 'title', 'excerpt', 'published_at', 'reading_time_minutes'])
            ->map(fn (Article $a) => [
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

        return Inertia::render('Articles/Show', [
            'article' => [
                'slug' => $article->slug,
                'title' => $article->title,
                'excerpt' => $article->excerpt,
                'body' => $article->body,
                'published_at' => $article->published_at?->toIso8601String(),
                'reading_time_minutes' => $article->reading_time_minutes,
                'meta_title' => $article->meta_title,
                'meta_description' => $article->meta_description,
                'category' => $article->category ? [
                    'slug' => $article->category->slug,
                    'name' => $article->category->name,
                ] : null,
                'author' => $article->author ? [
                    'name' => $article->author->name,
                ] : null,
            ],
            'related' => $related,
        ]);
    }
}
