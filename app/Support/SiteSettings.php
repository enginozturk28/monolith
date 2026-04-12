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
            // Kimlik
            'name' => $data['name'] ?? null,
            'tagline' => $data['tagline'] ?? null,
            'footer_description' => $data['footer_description'] ?? null,
            'copyright_text' => $data['copyright_text'] ?? null,

            // İletişim
            'phone' => $data['phone'] ?? null,
            'email' => $data['email'] ?? null,
            'kep' => $data['kep'] ?? null,
            'address' => $data['address'] ?? null,
            'map_embed_url' => $data['map_embed_url'] ?? null,

            // Sosyal medya
            'linkedin_url' => $data['linkedin_url'] ?? null,
            'whatsapp_url' => $data['whatsapp_url'] ?? null,
            'instagram_url' => $data['instagram_url'] ?? null,
            'x_url' => $data['x_url'] ?? null,

            // Görseller
            'logo_url' => $data['logo_url'] ?? null,
            'hero_image_path' => $data['hero_image_path'] ?? null,
            'hero_image_credit' => $data['hero_image_credit'] ?? null,
            'about_image_path' => $data['about_image_path'] ?? null,
            'about_image_credit' => $data['about_image_credit'] ?? null,

            // İçerik blokları
            'about_intro_body' => $data['about_intro_body'] ?? null,
            'founder_bio' => $data['founder_bio'] ?? null,

            // Sayfa görünürlük
            'show_faq_page' => filter_var($data['show_faq_page'] ?? false, FILTER_VALIDATE_BOOLEAN),

            // Hero bölümü
            'hero_title' => $data['hero_title'] ?? null,
            'hero_description' => $data['hero_description'] ?? null,

            // Süreç bölümü (frontend'e array olarak gider, kolay render)
            'process_title' => $data['process_title'] ?? null,
            'process_steps' => [
                [
                    'title' => $data['process_step_1_title'] ?? 'Değerlendirme',
                    'text' => $data['process_step_1_text'] ?? '',
                ],
                [
                    'title' => $data['process_step_2_title'] ?? 'Yönlendirme',
                    'text' => $data['process_step_2_text'] ?? '',
                ],
                [
                    'title' => $data['process_step_3_title'] ?? 'Takip',
                    'text' => $data['process_step_3_text'] ?? '',
                ],
            ],
        ];
    }

    public static function flush(): void
    {
        Cache::forget('monolith.site_settings');
    }
}
