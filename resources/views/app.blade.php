<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="format-detection" content="telephone=no">

    @env ('local')
        <meta name="robots" content="noindex, nofollow">
    @endenv

    <title inertia>{{ config('app.name', 'Loğoğlu Hukuk Bürosu') }}</title>

    @if (! empty($googleFontsUrl ?? null))
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="{{ $googleFontsUrl }}" rel="stylesheet">
    @endif

    {{-- Dinamik tema runtime injection — Filament Settings'ten gelen renkler --}}
    @if (! empty($themeCss ?? null))
        <style id="monolith-theme">{!! $themeCss !!}</style>
    @endif

    @routes
    @viteReactRefresh
    @vite(['resources/css/app.css', 'resources/js/app.tsx'])
    @inertiaHead
</head>
<body class="font-body antialiased">
    @inertia
</body>
</html>
