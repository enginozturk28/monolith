<?php

namespace App\Support;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Throwable;

/*
 * ThemeManager — dinamik tema sistemi merkezi.
 *
 * Tüm renk + tipografi tokenları DB'deki `settings` tablosundan (Filament panel)
 * veya `config/monolith.php` fallback'inden okur, cache'ler, ve hem inline CSS
 * hem de Inertia shared props olarak servis eder.
 *
 * Frontend bu tokenları :root'a inject ederek runtime'da tema uygular.
 * Build gerekmeden değişiklikler canlıya geçer.
 */
class ThemeManager
{
    /** @var array<string,string> token -> CSS değişken adı */
    public const TOKEN_MAP = [
        'bg' => '--theme-bg',
        'surface' => '--theme-surface',
        'surface_alt' => '--theme-surface-alt',
        'text' => '--theme-text',
        'text_muted' => '--theme-text-muted',
        'border' => '--theme-border',
        'primary' => '--theme-primary',
        'primary_fg' => '--theme-primary-fg',
        'accent' => '--theme-accent',
        'font_heading' => '--theme-font-heading',
        'font_body' => '--theme-font-body',
    ];

    /**
     * @return array<string, mixed>
     */
    public function tokens(): array
    {
        return Cache::rememberForever('monolith.theme', function (): array {
            $config = config('monolith.theme', []);

            try {
                if (! Schema::hasTable('settings')) {
                    return $config;
                }

                $dbValues = collect(Setting::allGrouped()['theme'] ?? [])
                    ->filter(fn ($v) => $v !== null && $v !== '')
                    ->all();

                return array_merge($config, $dbValues);
            } catch (Throwable) {
                return $config;
            }
        });
    }

    /**
     * :root içinde kullanılacak inline CSS bloğunu üretir.
     */
    public function asInlineCss(): string
    {
        $tokens = $this->tokens();
        $declarations = [];

        foreach (self::TOKEN_MAP as $key => $cssVar) {
            if (! isset($tokens[$key])) {
                continue;
            }
            $value = $tokens[$key];
            // CSS injection guard — sadece güvenli karakterler
            if ($this->looksDangerous((string) $value)) {
                continue;
            }
            $declarations[] = sprintf('%s:%s;', $cssVar, $value);
        }

        if (empty($declarations)) {
            return '';
        }

        return ':root{'.implode('', $declarations).'}';
    }

    /**
     * Inertia shared props formatı (frontend tipi: ThemeTokens).
     *
     * @return array<string, mixed>
     */
    public function forInertia(): array
    {
        $tokens = $this->tokens();

        return [
            'bg' => $tokens['bg'] ?? null,
            'surface' => $tokens['surface'] ?? null,
            'surfaceAlt' => $tokens['surface_alt'] ?? null,
            'text' => $tokens['text'] ?? null,
            'textMuted' => $tokens['text_muted'] ?? null,
            'border' => $tokens['border'] ?? null,
            'primary' => $tokens['primary'] ?? null,
            'primaryFg' => $tokens['primary_fg'] ?? null,
            'accent' => $tokens['accent'] ?? null,
            'fontHeading' => $tokens['font_heading'] ?? null,
            'fontBody' => $tokens['font_body'] ?? null,
        ];
    }

    /**
     * Google Fonts <link> URL'i.
     */
    public function googleFontsUrl(): ?string
    {
        $families = config('monolith.theme.google_fonts', []);
        if (empty($families)) {
            return null;
        }

        return 'https://fonts.googleapis.com/css2?'
            .implode('&', array_map(fn ($f) => 'family='.$f, $families))
            .'&display=swap';
    }

    public static function flush(): void
    {
        Cache::forget('monolith.theme');
    }

    private function looksDangerous(string $value): bool
    {
        // Tag, expression, comment kapatma vb. engelle
        return (bool) preg_match('/<|>|\\}|\\{|;\\s*[a-z-]+\\s*:|expression\\s*\\(|@import|url\\s*\\(/i', $value);
    }
}
