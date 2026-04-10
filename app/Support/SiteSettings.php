<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;

/*
 * SiteSettings — site genel bilgileri için tek erişim noktası.
 *
 * Önce DB'deki `settings` tablosunu okur (Filament panelinden yönetilen
 * gerçek değerler). Tablo yoksa veya değer eksikse `config/monolith.php`
 * fallback'ine düşer. Faz 1'de Setting modeli geldiğinde DB okuması aktif olur.
 */
class SiteSettings
{
    /**
     * @return array<string, mixed>
     */
    public function all(): array
    {
        return Cache::rememberForever('monolith.site_settings', function (): array {
            // Faz 1'de Setting::pluck('value', 'key')->where('group', 'site') buraya gelecek.
            // Şimdilik sadece config'ten okuyoruz — interface stable kalır, implementation değişir.
            return config('monolith.site', []);
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
