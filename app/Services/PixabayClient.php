<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

/**
 * PixabayClient — Pixabay arama API'si ve görsel indirme yardımcısı.
 *
 * Kullanım:
 *   $client = app(PixabayClient::class);
 *   $hit = $client->searchFirst('law office architecture', orientation: 'horizontal');
 *   $localPath = $client->downloadAndStore($hit, 'services', 'ceza-hukuku');
 *
 * API key sırası: Setting (group=api_keys, key=pixabay) → env('PIXABAY_API_KEY').
 * Bu sayede hem panelden yönetilebilir (sonradan Faz 2 encrypted setting'lerle)
 * hem de dev ortamında env fallback çalışır.
 *
 * İndirilen görseller `storage/app/public/pixabay/{collection}/{slug}.jpg` olarak
 * saklanır. Public erişim için `php artisan storage:link` çalıştırılmış olmalı.
 *
 * Pixabay ToS uyumu:
 * - Her arama sonucunda max 1 kez API çağrısı yapılır (sonuç DB'de saklanır)
 * - Görsel indirildikten sonra Pixabay CDN'ine tekrar istek yapılmaz
 * - `cover_image_credit` alanına Pixabay kullanıcı adı yazılır (attribution)
 */
class PixabayClient
{
    private const API_URL = 'https://pixabay.com/api/';

    public function getApiKey(): string
    {
        $key = Setting::value('api_keys', 'pixabay') ?? config('services.pixabay.key') ?? env('PIXABAY_API_KEY');

        if (! is_string($key) || $key === '') {
            throw new RuntimeException('Pixabay API key bulunamadı. .env veya Setting (group=api_keys, key=pixabay) tanımlayın.');
        }

        return $key;
    }

    /**
     * Pixabay'de arama yapar ve hits dizisini döner.
     *
     * @return array<int, array<string, mixed>>
     */
    public function search(string $query, array $params = []): array
    {
        $defaults = [
            'image_type' => 'photo',
            'orientation' => 'horizontal',
            'safesearch' => 'true',
            'order' => 'popular',
            'per_page' => 20,
            'lang' => 'en',
        ];

        $response = Http::timeout(15)->retry(2, 500)->get(self::API_URL, array_merge(
            $defaults,
            $params,
            [
                'key' => $this->getApiKey(),
                'q' => $query,
            ]
        ));

        if (! $response->successful()) {
            throw new RuntimeException(sprintf(
                'Pixabay API hatası (HTTP %d): %s',
                $response->status(),
                substr((string) $response->body(), 0, 200)
            ));
        }

        $data = $response->json();

        return is_array($data['hits'] ?? null) ? $data['hits'] : [];
    }

    /**
     * Aramada ilk eşleşen görseli döner (yoksa null).
     *
     * @return array<string, mixed>|null
     */
    public function searchFirst(string $query, array $params = []): ?array
    {
        $hits = $this->search($query, $params);

        return $hits[0] ?? null;
    }

    /**
     * Görseli `storage/app/public/{collection}/{slug}.jpg` olarak indirir.
     * Geri dönüş: public URL ('/storage/{collection}/{slug}.jpg').
     */
    public function downloadAndStore(array $hit, string $collection, string $slug): ?string
    {
        // 1280px versiyonu önceliklidir
        $url = $hit['largeImageURL'] ?? $hit['webformatURL'] ?? null;

        if (! is_string($url) || $url === '') {
            return null;
        }

        $response = Http::timeout(30)->retry(2, 500)->get($url);

        if (! $response->successful()) {
            return null;
        }

        $extension = pathinfo(parse_url($url, PHP_URL_PATH) ?? '', PATHINFO_EXTENSION) ?: 'jpg';
        $filename = "pixabay/{$collection}/{$slug}.{$extension}";

        Storage::disk('public')->put($filename, $response->body());

        return '/storage/'.$filename;
    }

    /**
     * Hit objesinden Pixabay kullanıcı adını döner (attribution için).
     */
    public function attributionFor(array $hit): string
    {
        $user = $hit['user'] ?? 'Pixabay';

        return "Foto: {$user} / Pixabay";
    }
}
