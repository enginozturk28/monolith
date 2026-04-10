<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Inertia\Inertia;
use Inertia\Response;

class AboutController extends Controller
{
    public function __invoke(): Response
    {
        // Vizyon ve misyon statik sayfalardan gelir — tek bir Hakkımızda
        // sayfasında birleşik gösterilir.
        $vision = Page::published()->where('slug', 'vizyon')->first();
        $mission = Page::published()->where('slug', 'misyon')->first();

        return Inertia::render('About', [
            'vision' => $vision ? [
                'title' => $vision->title,
                'body' => $vision->body,
            ] : null,
            'mission' => $mission ? [
                'title' => $mission->title,
                'body' => $mission->body,
            ] : null,
        ]);
    }
}
