<?php

/*
 * Monolith — Loğoğlu Hukuk Bürosu
 *
 * Bu dosya yalnızca varsayılan değerler içerir. Production'da tüm değerler
 * Filament Settings panelinden DB'deki `settings` tablosuna kaydedilir
 * ve runtime'da öncelikli okunur. Bu dosya fallback'tir.
 */

return [

    'site' => [
        'name' => 'Loğoğlu Hukuk Bürosu',
        'tagline' => 'Hukukun farklı alanlarında profesyonel destek',
        'phone' => '+90 553 647 38 22',
        'email' => 'ethemlogoglu@gmail.com',
        'kep' => 'ethemkaan.logoglu@hs01.kep.tr',
        'linkedin_url' => 'https://www.linkedin.com/in/ethemlogoglu/',
        'address' => 'Fenerbahçe Mahallesi, Itri Dede Sokak No:22/7 Kadıköy/İstanbul',
        'map_embed_url' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3011.95092531232!2d29.03693307652785!3d40.98255542100763!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x14cab9712620d57d%3A0x818d2be1fa4658f!2zTG_En2_En2x1IEh1a3VrIELDvHJvc3U!5e0!3m2!1str!2str!4v1775834413792!5m2!1str!2str',
        'logo_url' => null,
    ],

    'theme' => [
        // Loğoğlu Hukuk Bürosu varsayılan paleti
        'bg' => '#F5F1EA',
        'surface' => '#FFFFFF',
        'surface_alt' => '#EFE9DE',
        'text' => '#0B1F3A',
        'text_muted' => '#8C8B86',
        'border' => '#D9D2C4',
        'primary' => '#0B1F3A',
        'primary_fg' => '#F5F1EA',
        'accent' => '#A88A55',

        'font_heading' => "'Cormorant Garamond', 'Lora', Georgia, ui-serif, serif",
        'font_body' => "'Inter', ui-sans-serif, system-ui, sans-serif",

        // Google Fonts'tan otomatik yüklenecek aileler (boş dizi = ekleme)
        'google_fonts' => [
            'Cormorant+Garamond:wght@400;500;600;700',
            'Inter:wght@400;500;600;700',
        ],
    ],

];
