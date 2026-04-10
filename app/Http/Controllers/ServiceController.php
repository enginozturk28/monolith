<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Inertia\Inertia;
use Inertia\Response;

class ServiceController extends Controller
{
    public function index(): Response
    {
        $services = Service::query()
            ->published()
            ->ordered()
            ->get(['id', 'slug', 'title', 'icon', 'summary'])
            ->map(fn ($s) => [
                'slug' => $s->slug,
                'title' => $s->title,
                'icon' => $s->icon,
                'summary' => $s->summary,
            ])
            ->values();

        return Inertia::render('Services/Index', [
            'services' => $services,
        ]);
    }

    public function show(Service $service): Response
    {
        abort_if(! $service->is_published, 404);

        $related = Service::query()
            ->published()
            ->ordered()
            ->where('id', '!=', $service->id)
            ->limit(6)
            ->get(['slug', 'title', 'icon'])
            ->map(fn ($s) => [
                'slug' => $s->slug,
                'title' => $s->title,
                'icon' => $s->icon,
            ])
            ->values();

        return Inertia::render('Services/Show', [
            'service' => [
                'slug' => $service->slug,
                'title' => $service->title,
                'icon' => $service->icon,
                'summary' => $service->summary,
                'body' => $service->body,
                'meta_title' => $service->meta_title,
                'meta_description' => $service->meta_description,
            ],
            'related' => $related,
        ]);
    }
}
