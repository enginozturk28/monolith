<?php

namespace App\Console\Commands;

use App\Models\Setting;
use App\Services\PixabayClient;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Throwable;

/**
 * monolith:fetch-images
 *
 * Geliştirici aracı — Pixabay'den tek seferlik görsel indirme.
 *
 * Bu komut SİSTEMDE OTOMATIK ÇALIŞMAZ. Sadece geliştirici (ben) çalıştırır,
 * sonuçlar `storage/app/public/` altına kalıcı dosya olarak iner ve URL'leri
 * `settings` tablosuna yazılır. Bundan sonra Pixabay'a hiçbir runtime istek
 * yapılmaz — frontend tamamen statik görselleri kullanır.
 *
 * Kullanım:
 *   php artisan monolith:fetch-images          # tüm görselleri indir
 *   php artisan monolith:fetch-images hero     # sadece hero'yu indir
 *   php artisan monolith:fetch-images about    # sadece about'u indir
 *   php artisan monolith:fetch-images --force  # mevcut görselleri override et
 */
#[Signature('monolith:fetch-images {target? : Hangi görsel (hero, about, all)} {--force : Mevcut görselleri override et}')]
#[Description('Pixabay\'den tek seferlik görsel indirir ve storage/app/public altına kaydeder.')]
class FetchImages extends Command
{
    /**
     * Hedef anahtarlar ve arama terimleri.
     */
    private const TARGETS = [
        'hero' => [
            'group' => 'site',
            'path_key' => 'hero_image_path',
            'credit_key' => 'hero_image_credit',
            'collection' => 'site',
            'filename' => 'hero',
            'queries' => [
                'modern law office interior',
                'minimal architecture office',
                'lawyer desk natural light',
            ],
            'params' => [
                'orientation' => 'horizontal',
                'min_width' => 1920,
                'category' => 'business',
            ],
        ],
        'about' => [
            'group' => 'site',
            'path_key' => 'about_image_path',
            'credit_key' => 'about_image_credit',
            'collection' => 'site',
            'filename' => 'about',
            'queries' => [
                'minimalist architecture facade',
                'modern building Istanbul',
                'office library books',
            ],
            'params' => [
                'orientation' => 'horizontal',
                'min_width' => 1600,
                'category' => 'buildings',
            ],
        ],
    ];

    public function handle(PixabayClient $pixabay): int
    {
        $target = $this->argument('target') ?? 'all';
        $force = (bool) $this->option('force');

        $targets = $target === 'all'
            ? array_keys(self::TARGETS)
            : [$target];

        foreach ($targets as $key) {
            if (! isset(self::TARGETS[$key])) {
                $this->error("Bilinmeyen hedef: {$key}");
                $this->line('Kullanılabilir: '.implode(', ', array_keys(self::TARGETS)).', all');

                return self::FAILURE;
            }

            try {
                $this->fetch($pixabay, $key, self::TARGETS[$key], $force);
            } catch (Throwable $e) {
                $this->error("[$key] hata: ".$e->getMessage());

                return self::FAILURE;
            }
        }

        $this->newLine();
        $this->info('Tamamlandı. Sayfayı yenileyince görseller görünmelidir.');

        return self::SUCCESS;
    }

    private function fetch(PixabayClient $pixabay, string $key, array $config, bool $force): void
    {
        $this->newLine();
        $this->line("<fg=cyan>━━━ [{$key}] ━━━</>");

        // Mevcut değer var mı?
        $existing = Setting::value($config['group'], $config['path_key']);
        if ($existing && ! $force) {
            $this->warn("  Mevcut değer var: {$existing}");
            $this->line('  Override için --force kullanın, atlanıyor.');

            return;
        }

        // Sırayla query'leri dene
        $hit = null;
        foreach ($config['queries'] as $query) {
            $this->line("  Aranıyor: <fg=gray>{$query}</>");
            $hit = $pixabay->searchFirst($query, $config['params']);
            if ($hit) {
                $this->line("  ✓ Bulundu (id={$hit['id']}, user={$hit['user']})");
                break;
            }
            $this->line('  → eşleşme yok, sonraki query');
        }

        if (! $hit) {
            $this->warn("  Hiçbir query için sonuç bulunamadı.");

            return;
        }

        // İndir
        $this->line('  İndiriliyor...');
        $publicUrl = $pixabay->downloadAndStore($hit, $config['collection'], $config['filename']);

        if (! $publicUrl) {
            $this->error('  İndirme başarısız.');

            return;
        }

        $this->line("  ✓ Kaydedildi: <fg=green>{$publicUrl}</>");

        // Settings'e yaz
        $credit = $pixabay->attributionFor($hit);
        Setting::set($config['group'], $config['path_key'], $publicUrl, [
            'type' => 'image',
            'label' => $config['path_key'],
            'encrypted' => false,
        ]);
        Setting::set($config['group'], $config['credit_key'], $credit, [
            'type' => 'text',
            'label' => $config['credit_key'],
            'encrypted' => false,
        ]);

        $this->line("  ✓ Settings güncellendi: {$config['path_key']}");
    }
}
