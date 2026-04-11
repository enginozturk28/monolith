<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Schema;

/**
 * monolith:unlock-login — Login throttle (rate limiter) anahtarlarını temizler.
 *
 * Filament v5 built-in olarak login işlemini 5 yanlış denemeden sonra throttle
 * eder (Livewire `rateLimit` trait'i). Eğer kullanıcı kendi şifresini unutup
 * tekrar tekrar denerse veya bir bot saldırısı sonrası gerçek kullanıcı bloke
 * kalırsa, bu komut ile rate limiter cache'i temizlenir.
 *
 * Kullanım:
 *   php artisan monolith:unlock-login          # tüm login throttle'larını temizle
 *   php artisan monolith:unlock-login --all    # tüm rate limiter cache'i temizle
 *
 * Komut sadece SSH erişimi olan birisi tarafından çalıştırılabilir, dolayısıyla
 * web üzerinden istismar edilemez. Production'da panik durumlar için runbook'ta
 * yer alır.
 */
#[Signature('monolith:unlock-login {--all : Tüm rate limiter cache\'ini temizle (sadece login değil)}')]
#[Description('Filament login rate limiter throttle cache anahtarlarını temizler.')]
class UnlockLogin extends Command
{
    public function handle(): int
    {
        if (! Schema::hasTable('cache')) {
            $this->error('Cache tablosu bulunamadı. CACHE_STORE=database olduğundan emin olun.');

            return self::FAILURE;
        }

        $all = (bool) $this->option('all');

        // Cache table prefix - config'den oku
        $prefix = config('cache.prefix', 'laravel-cache') . '-';

        $pattern = $all
            ? "{$prefix}%"
            : "{$prefix}laravel_cache:filament-multi-factor-challenge:%";

        // Hem multi-factor challenge hem normal login throttle key'leri için temizlik
        $loginPatterns = [
            "{$prefix}%filament-multi-factor-challenge%",
            "{$prefix}%filament-panels::auth::pages::login%",
            "{$prefix}%loginform%",
            "{$prefix}%authenticate%",
            "{$prefix}%rate-limiter%",
        ];

        if ($all) {
            $count = DB::table('cache')->delete();
            $this->info("✓ Tüm cache temizlendi: {$count} kayıt silindi.");

            return self::SUCCESS;
        }

        $totalDeleted = 0;
        foreach ($loginPatterns as $patternQuery) {
            $deleted = DB::table('cache')
                ->where('key', 'like', $patternQuery)
                ->delete();
            $totalDeleted += $deleted;
        }

        // Filament built-in rate limiter'ını da Laravel API üzerinden temizle
        // (Genelde key formatı: {auth_id} veya {ip}|{method}|{component})
        try {
            // Mevcut tüm kullanıcılar için multi-factor challenge key'lerini temizle
            \App\Models\User::query()->each(function ($user) use (&$totalDeleted) {
                $key = "filament-multi-factor-challenge:{$user->id}";
                if (RateLimiter::attempts($key) > 0) {
                    RateLimiter::clear($key);
                    $totalDeleted++;
                }
            });
        } catch (\Throwable $e) {
            $this->warn('RateLimiter::clear çağrılırken hata: '.$e->getMessage());
        }

        if ($totalDeleted === 0) {
            $this->info('Temizlenecek bloke kayıt bulunamadı. Login throttling şu an aktif değil.');
        } else {
            $this->info("✓ {$totalDeleted} adet rate limiter cache anahtarı temizlendi.");
            $this->line('Engellenen kullanıcılar tekrar login deneyebilir.');
        }

        return self::SUCCESS;
    }
}
