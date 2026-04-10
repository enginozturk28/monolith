<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Inertia\Inertia;
use Inertia\Response;

class PageController extends Controller
{
    public function show(Page $page): Response
    {
        abort_if(! $page->is_published, 404);

        return Inertia::render('PageShow', [
            'page' => [
                'slug' => $page->slug,
                'title' => $page->title,
                'body' => $page->body,
                'meta_title' => $page->meta_title,
                'meta_description' => $page->meta_description,
                'updated_at' => $page->updated_at?->toIso8601String(),
            ],
        ]);
    }
}
