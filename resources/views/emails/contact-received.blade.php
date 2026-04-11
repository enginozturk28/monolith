<x-mail::message>
# Yeni {{ $typeLabel }}

Web sitesi iletişim formu üzerinden yeni bir mesaj ulaştı.

**Ad Soyad:** {{ $message->name }}
**E-posta:** [{{ $message->email }}](mailto:{{ $message->email }})
@if ($message->phone)
**Telefon:** {{ $message->phone }}
@endif
**Talep Türü:** {{ $typeLabel }}
@if ($message->subject)
**Konu:** {{ $message->subject }}
@endif
**Tarih:** {{ $message->created_at?->format('d.m.Y H:i') }}

---

### Mesaj

{{ $message->message }}

---

<x-mail::button :url="url('/admin/contact-messages/' . $message->id . '/edit')">
Panelde Aç
</x-mail::button>

Bu e-postaya doğrudan "Yanıtla" diyerek müvekkile dönüş yapabilirsiniz.
İletişim bilgileri otomatik olarak müvekkilin e-posta adresine ayarlanmıştır.

---

<small style="color: #888;">
Bu bildirim otomatik olarak iletişim formundan üretildi.
IP: {{ $message->ip_address ?? '—' }}
</small>

{{ config('app.name') }}
</x-mail::message>
