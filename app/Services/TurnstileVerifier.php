<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * TurnstileVerifier — Cloudflare Turnstile token doğrulama servisi.
 *
 * Cloudflare Turnstile, reCAPTCHA'ya alternatif olarak Cloudflare'in
 * sunduğu insan/bot doğrulama sistemidir. Kullanıcı dostu (genelde hiçbir
 * etkileşim gerektirmez), gizlilik dostu (Google takibi yok).
 *
 * Akış:
 * 1. Frontend'de iletişim formu yüklendiğinde Turnstile widget render edilir
 *    (sadece site_key panel'den girilmişse)
 * 2. Kullanıcı submit ettiğinde widget bir token üretir
 * 3. Form POST ile birlikte token sunucuya gelir
 * 4. ContactController bu servis ile token'i Cloudflare'e doğrulatır
 * 5. Doğrulama başarılıysa form kaydı yapılır, değilse 422 dönülür
 *
 * Eğer secret key yoksa isEnabled() false döner ve doğrulama atlanır
 * (form normal akışta honeypot ile devam eder).
 */
class TurnstileVerifier
{
    private const VERIFY_URL = 'https://challenges.cloudflare.com/turnstile/v0/siteverify';

    /**
     * Turnstile yapılandırılmış mı? (hem site_key hem secret_key dolu olmalı)
     */
    public function isEnabled(): bool
    {
        return ! empty($this->getSiteKey()) && ! empty($this->getSecretKey());
    }

    public function getSiteKey(): ?string
    {
        return Setting::value('security', 'turnstile_site_key');
    }

    public function getSecretKey(): ?string
    {
        return Setting::value('security', 'turnstile_secret_key');
    }

    /**
     * Verilen token'i Cloudflare'e doğrulatır.
     *
     * @param  string|null  $token  Frontend'den gelen cf-turnstile-response token
     * @param  string|null  $remoteIp  Kullanıcının IP'si (opsiyonel ama önerilen)
     */
    public function verify(?string $token, ?string $remoteIp = null): bool
    {
        if (! $this->isEnabled()) {
            // Turnstile yapılandırılmamış — doğrulama atla, form normal devam etsin
            return true;
        }

        if (empty($token)) {
            return false;
        }

        try {
            $response = Http::asForm()
                ->timeout(8)
                ->retry(2, 300)
                ->post(self::VERIFY_URL, array_filter([
                    'secret' => $this->getSecretKey(),
                    'response' => $token,
                    'remoteip' => $remoteIp,
                ]));

            if (! $response->successful()) {
                Log::warning('Turnstile API hatası', [
                    'status' => $response->status(),
                    'body' => substr((string) $response->body(), 0, 200),
                ]);

                return false;
            }

            $result = $response->json();

            return (bool) ($result['success'] ?? false);
        } catch (Throwable $e) {
            Log::warning('Turnstile doğrulama exception', [
                'message' => $e->getMessage(),
            ]);

            // Cloudflare ulaşılamıyorsa — false döner, form reject edilir.
            // Bilinçli bir karar: spam'a izin vermek yerine geçici olarak
            // erişimi engelliyoruz. (Alternatif: true dönerek "fail open" davranışı.)
            return false;
        }
    }
}
