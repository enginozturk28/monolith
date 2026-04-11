<?php

namespace App\Http\Requests;

use App\Models\ContactMessage;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ContactRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:200'],
            'email' => ['required', 'string', 'email:rfc,dns', 'max:200'],
            'phone' => ['nullable', 'string', 'max:40'],
            'subject_type' => [
                'required',
                'string',
                Rule::in(array_keys(ContactMessage::subjectTypes())),
            ],
            'subject' => ['nullable', 'string', 'max:240'],
            'message' => ['required', 'string', 'min:20', 'max:5000'],
            // Honeypot — botlar doldurur, gerçek kullanıcılar boş bırakır
            'website' => ['nullable', 'max:0'],
            // Cloudflare Turnstile token (opsiyonel — aktif değilse boş gelir)
            'cf-turnstile-response' => ['nullable', 'string', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Ad soyad alanı zorunludur.',
            'name.min' => 'Ad soyad en az 2 karakter olmalıdır.',
            'email.required' => 'E-posta adresi zorunludur.',
            'email.email' => 'Geçerli bir e-posta adresi giriniz.',
            'subject_type.required' => 'Talep türünü seçiniz.',
            'subject_type.in' => 'Geçersiz talep türü.',
            'message.required' => 'Mesaj alanı zorunludur.',
            'message.min' => 'Mesaj en az 20 karakter olmalıdır.',
            'message.max' => 'Mesaj en fazla 5000 karakter olabilir.',
            'website.max' => 'Spam tespit edildi.',
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'Ad Soyad',
            'email' => 'E-posta',
            'phone' => 'Telefon',
            'subject_type' => 'Talep türü',
            'subject' => 'Konu',
            'message' => 'Mesaj',
        ];
    }
}
