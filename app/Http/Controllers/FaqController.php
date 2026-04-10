<?php

namespace App\Http\Controllers;

use App\Models\FaqCategory;
use Inertia\Inertia;
use Inertia\Response;

class FaqController extends Controller
{
    public function __invoke(): Response
    {
        $categories = FaqCategory::query()
            ->published()
            ->ordered()
            ->with([
                'faqs' => fn ($q) => $q->where('is_published', true)->orderBy('sort_order'),
            ])
            ->get()
            ->map(fn (FaqCategory $cat) => [
                'slug' => $cat->slug,
                'name' => $cat->name,
                'icon' => $cat->icon,
                'description' => $cat->description,
                'faqs' => $cat->faqs->map(fn ($f) => [
                    'id' => $f->id,
                    'question' => $f->question,
                    'answer' => $f->answer,
                ])->values(),
            ])
            ->values();

        return Inertia::render('Faq', [
            'categories' => $categories,
        ]);
    }
}
