<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactRequest;
use App\Mail\ContactReceived;
use App\Models\ContactMessage;
use App\Services\TurnstileVerifier;
use App\Support\SiteSettings;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class ContactController extends Controller
{
    public function show(): Response
    {
        return Inertia::render('Contact');
    }

    public function store(
        ContactRequest $request,
        SiteSettings $settings,
        TurnstileVerifier $turnstile,
    ): RedirectResponse {
        // Rate limit — IP başına saatte 10 mesaj
        $key = 'contact:'.$request->ip();
        if (RateLimiter::tooManyAttempts($key, 10)) {
            return back()
                ->with('error', 'Çok fazla istek algılandı. Lütfen bir süre sonra tekrar deneyiniz.')
                ->withInput();
        }
        RateLimiter::hit($key, 3600);

        // Cloudflare Turnstile doğrulaması (sadece panel'den aktifse)
        // Turnstile yapılandırılmamışsa verify() otomatik true döner — atlanır
        if ($turnstile->isEnabled()) {
            $token = $request->input('cf-turnstile-response');
            if (! $turnstile->verify($token, $request->ip())) {
                return back()
                    ->with('error', 'Güvenlik doğrulaması başarısız oldu. Lütfen sayfayı yenileyip tekrar deneyiniz.')
                    ->withInput();
            }
        }

        $data = $request->validated();
        unset($data['website'], $data['cf-turnstile-response']); // honeypot ve token'ı kaydetme

        $message = ContactMessage::create([
            ...$data,
            'ip_address' => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 500),
        ]);

        // E-posta bildirimi — avukata iletilir.
        // Alıcı: Site Settings'teki "email" alanı.
        // SMTP paneli yapılandırılmışsa gerçek gönderim, aksi halde log driver.
        // Mail fail olsa bile form kaydı korunur.
        $recipient = $settings->get('email');
        if ($recipient) {
            try {
                Mail::to($recipient)->send(new ContactReceived($message));
            } catch (Throwable $e) {
                Log::warning('İletişim bildirimi gönderilemedi', [
                    'contact_message_id' => $message->id,
                    'recipient' => $recipient,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return redirect()
            ->route('contact')
            ->with('success', 'Mesajınız tarafımıza ulaştı. En kısa sürede dönüş yapılacaktır.');
    }
}
