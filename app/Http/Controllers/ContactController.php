<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactRequest;
use App\Models\ContactMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\RateLimiter;
use Inertia\Inertia;
use Inertia\Response;

class ContactController extends Controller
{
    public function show(): Response
    {
        return Inertia::render('Contact');
    }

    public function store(ContactRequest $request): RedirectResponse
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

        ContactMessage::create([
            ...$data,
            'ip_address' => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 500),
        ]);

        return redirect()
            ->route('contact')
            ->with('success', 'Mesajınız tarafımıza ulaştı. En kısa sürede dönüş yapılacaktır.');
    }
}
