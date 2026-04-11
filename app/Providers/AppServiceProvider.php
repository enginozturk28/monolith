<?php

namespace App\Providers;

use App\Support\MailConfig;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Staging ve production'da tüm URL üretimini HTTPS zorla.
        // Bu, Cloudpanel reverse proxy arkasında Laravel'in `http://` ile
        // URL üretmesini engeller — Filament login form action, asset URL'leri,
        // route(), url() hepsi https ile üretilir.
        if ($this->app->environment(['production', 'staging'])) {
            URL::forceScheme('https');
        }

        // Livewire asset + update route'larını uzantısız sabit path'e taşı.
        //
        // Sebep: Cloudpanel Nginx vhost'ları varsayılan olarak `\.js` uzantılı
        // URL'leri direkt static dosya olarak serve etmeye çalışır ve bulamayınca
        // 404 döner — Laravel route'una hiç ulaşmaz. Livewire'ın dinamik
        // script path'i (örn. `/livewire-abc123/livewire.js`) bu yüzden 404 alır
        // ve tüm sayfa Livewire/Alpine olmadan kalır → Filament login çalışmaz,
        // password input plain-text görünür.
        //
        // Çözüm: Route path'lerini uzantısız yapıyoruz. Nginx try_files fallback'i
        // index.php'ye yönlendirir, Laravel route match eder, Livewire JS içeriğini
        // HTTP response body'sinde döner.
        Livewire::setScriptRoute(function ($handle) {
            return Route::get('/livewire/script', $handle);
        });

        Livewire::setUpdateRoute(function ($handle) {
            return Route::post('/livewire/update', $handle);
        });

        // SMTP konfigürasyonunu DB Settings'ten yükle.
        // Eğer panel'den (Filament SmtpSettings page) bir SMTP host girilmişse,
        // mail.default ve mail.mailers.smtp.* config'leri runtime'da override edilir.
        // Aksi halde .env'deki MAIL_MAILER (şu an "log") kullanılmaya devam eder.
        MailConfig::applyFromSettings();
    }
}
