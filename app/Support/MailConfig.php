<?php

namespace App\Support;

use App\Models\Setting;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use Throwable;

/**
 * MailConfig — runtime'da SMTP konfigürasyonunu DB Settings'ten yükler.
 *
 * Mantık:
 * 1. Settings tablosunda group=smtp altında bir host kaydı varsa,
 *    Laravel mail config'ini bu değerlerle override et.
 * 2. Eğer settings yoksa veya host boşsa, .env'deki MAIL_* değerleri
 *    kullanılmaya devam edilir (bu staging'de "log" driver'a düşer).
 *
 * Bu sayede:
 * - Müşteri panelden SMTP bilgilerini girer girmez gerçek mail gönderimi başlar
 * - Hiç yapılandırma olmadığında log driver güvenli fallback olarak çalışır
 * - .env'ye dokunmadan production'da SMTP geçişi mümkün
 *
 * Çağrı yeri: AppServiceProvider::boot() — her request başında bir kez çalışır.
 * Cache'li (Setting::allGrouped) olduğu için DB hit'i pratikte sıfır maliyet.
 */
class MailConfig
{
    /**
     * Eğer DB'de SMTP ayarları varsa Laravel mail config'ini override eder.
     */
    public static function applyFromSettings(): void
    {
        try {
            // settings tablosu yoksa (ilk migrate öncesi) atla
            if (! Schema::hasTable('settings')) {
                return;
            }

            $smtp = Setting::allGrouped()['smtp'] ?? [];

            $host = $smtp['host'] ?? null;
            if (! $host) {
                return; // SMTP henüz panelden yapılandırılmamış, .env devam eder
            }

            // SMTP bilgileri var → smtp mailer'ı runtime'da kur
            Config::set('mail.default', 'smtp');
            Config::set('mail.mailers.smtp.host', $host);
            Config::set('mail.mailers.smtp.port', (int) ($smtp['port'] ?? 587));
            Config::set('mail.mailers.smtp.username', $smtp['username'] ?? null);
            Config::set('mail.mailers.smtp.password', $smtp['password'] ?? null);
            Config::set('mail.mailers.smtp.encryption', $smtp['encryption'] ?? 'tls');
            Config::set('mail.mailers.smtp.transport', 'smtp');

            // From address opsiyonel — boş ise .env'deki MAIL_FROM_ADDRESS devam eder
            $fromAddress = $smtp['from_address'] ?? null;
            $fromName = $smtp['from_name'] ?? null;
            if ($fromAddress) {
                Config::set('mail.from.address', $fromAddress);
            }
            if ($fromName) {
                Config::set('mail.from.name', $fromName);
            }
        } catch (Throwable $e) {
            // Sessiz fail — mail önemli ama tüm uygulamayı düşürmez
            // Log ile bildirim yapabiliriz ama başka bir önemli bug değil
        }
    }
}
