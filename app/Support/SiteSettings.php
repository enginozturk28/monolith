<?php

namespace App\Support;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Throwable;

/*
 * SiteSettings — site genel bilgileri için tek erişim noktası.
 *
 * Önce DB'deki `settings` tablosundan okur (Filament panelinden yönetilen
 * gerçek değerler). Tablo henüz yoksa veya DB erişimi yoksa `config/monolith.php`
 * fallback'ine düşer (ilk kurulum, test, staging'e devir aşamaları için).
 */
class SiteSettings
{
    /**
     * @return array<string, mixed>
     */
    public function all(): array
    {
        return Cache::rememberForever('monolith.site_settings', function (): array {
            $config = config('monolith.site', []);

            try {
                if (! Schema::hasTable('settings')) {
                    return $config;
                }

                $dbValues = collect(Setting::allGrouped()['site'] ?? [])
                    ->filter(fn ($v) => $v !== null && $v !== '')
                    ->all();

                // DB değerleri config üzerine binerek override eder
                return array_merge($config, $dbValues);
            } catch (Throwable) {
                return $config;
            }
        });
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return data_get($this->all(), $key, $default);
    }

    /**
     * Inertia shared props formatı (frontend tipi: SiteSettings).
     *
     * @return array<string, mixed>
     */
    public function forInertia(): array
    {
        $data = $this->all();

        return [
            'name' => $data['name'] ?? null,
            'tagline' => $data['tagline'] ?? null,
            'phone' => $data['phone'] ?? null,
            'email' => $data['email'] ?? null,
            'kep' => $data['kep'] ?? null,
            'address' => $data['address'] ?? null,
            'map_embed_url' => $data['map_embed_url'] ?? null,
            'linkedin_url' => $data['linkedin_url'] ?? null,
            'logo_url' => $data['logo_url'] ?? null,
        ];
    }

    public static function flush(): void
    {
        Cache::forget('monolith.site_settings');
    }
}
