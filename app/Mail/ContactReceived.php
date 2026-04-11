<?php

namespace App\Mail;

use App\Models\ContactMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * ContactReceived — iletişim formundan yeni mesaj geldiğinde avukata gönderilen
 * bildirim e-postası.
 *
 * Müvekkil adayının form üzerinden bıraktığı tüm bilgiler (ad, e-posta, telefon,
 * talep türü, mesaj) e-postada yer alır. Konu satırı hızlı tanıma için talep
 * türüne göre dinamik olarak oluşturulur ("Görüşme Talebi: ..." veya
 * "İletişim: ...").
 *
 * replyTo alanı müvekkilin kendi e-postasına ayarlanır — yani avukat mail'e
 * "Yanıtla" dediğinde doğrudan müvekkile dönüş yapabilir.
 */
class ContactReceived extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public ContactMessage $message)
    {
    }

    public function envelope(): Envelope
    {
        $typeLabel = ContactMessage::subjectTypes()[$this->message->subject_type] ?? 'İletişim';
        $subject = $this->message->subject
            ? "{$typeLabel}: {$this->message->subject}"
            : "{$typeLabel}: {$this->message->name}";

        return new Envelope(
            subject: $subject,
            replyTo: [
                new Address($this->message->email, $this->message->name),
            ],
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.contact-received',
            with: [
                'message' => $this->message,
                'typeLabel' => ContactMessage::subjectTypes()[$this->message->subject_type] ?? 'İletişim',
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
