<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactRequest;
use App\Mail\ContactReceived;
use App\Models\ContactMessage;
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

    public function store(ContactRequest $request, SiteSettings $settings): RedirectResponse
    {
        // Rate limit — IP başına dakikada 1, saatte 10 mesaj
        $key = 'contact:'.$request->ip();
        if (RateLimiter::tooManyAttempts($key, 10)) {
            return back()
                ->with('error', 'Çok fazla istek algılandı. Lütfen bir süre sonra tekrar deneyiniz.')
                ->withInput();
        }
        RateLimiter::hit($key, 3600); // 1 saat pencere

        $data = $request->validated();
        unset($data['website']); // honeypot alanı

        $message = ContactMessage::create([
            ...$data,
            'ip_address' => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 500),
        ]);

        // E-posta bildirimi — avukata iletilir.
        // Alıcı: Site Settings'teki "email" alanı (Filament panelinden yönetilir)
        // Şu anda MAIL_MAILER=log, mailler storage/logs/laravel.log'a düşer.
        // Faz 4 (Güvenlik) kapsamında gelecek SMTP paneli ile gerçek gönderime
        // geçilecektir. Mail gönderimi fail etse bile form kaydı zaten oluşmuştur,
        // kullanıcıya 500 dönmemek için try/catch ile güvenli hale getirildi.
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
