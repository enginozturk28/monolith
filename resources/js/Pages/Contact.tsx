import { type FormEvent, useEffect, useRef } from 'react';
import { useForm, usePage } from '@inertiajs/react';
import { CheckCircle2, Mail, MapPin, Phone } from 'lucide-react';
import MainLayout from '@/Layouts/MainLayout';
import Container from '@/Components/Layout/Container';
import Section from '@/Components/Layout/Section';
import PageHeader from '@/Components/Layout/PageHeader';
import FadeIn from '@/Components/Motion/FadeIn';
import SeoHead from '@/Components/Seo/SeoHead';
import { cn } from '@/lib/utils';
import type { SharedProps } from '@/types/global';

const SUBJECT_TYPES = {
    iletisim: 'Genel İletişim',
    gorusme_talebi: 'Görüşme / Randevu Talebi',
} as const;

type SubjectKey = keyof typeof SUBJECT_TYPES;

type FormShape = {
    name: string;
    email: string;
    phone: string;
    subject_type: SubjectKey;
    subject: string;
    message: string;
    website: string; // honeypot
    'cf-turnstile-response': string; // Cloudflare Turnstile token
};

export default function Contact() {
    const { site, flash, turnstile } = usePage<SharedProps>().props;
    const turnstileContainerRef = useRef<HTMLDivElement>(null);

    const { data, setData, post, processing, errors, reset } = useForm<FormShape>({
        name: '',
        email: '',
        phone: '',
        subject_type: 'iletisim',
        subject: '',
        message: '',
        website: '',
        'cf-turnstile-response': '',
    });

    // Cloudflare Turnstile script'ini sadece panel'den site key girilmişse yükle
    useEffect(() => {
        if (!turnstile?.enabled || !turnstile?.siteKey) return;

        // Script zaten yüklü mü?
        const existing = document.querySelector('script[src*="turnstile/v0/api.js"]');
        if (!existing) {
            const script = document.createElement('script');
            script.src = 'https://challenges.cloudflare.com/turnstile/v0/api.js';
            script.async = true;
            script.defer = true;
            document.head.appendChild(script);
        }

        // Token formdan alınmasın diye - form data'ya yazmak için bir interval
        // ile widget'ın token'ını yakala (cf-turnstile widget kendi gizli input'una
        // yazar, biz onu okuyup form state'ine taşıyoruz)
        const interval = setInterval(() => {
            const input = document.querySelector<HTMLInputElement>(
                'input[name="cf-turnstile-response"]',
            );
            if (input && input.value && input.value !== data['cf-turnstile-response']) {
                setData('cf-turnstile-response', input.value);
            }
        }, 300);

        return () => clearInterval(interval);
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [turnstile?.enabled, turnstile?.siteKey]);

    const submit = (e: FormEvent) => {
        e.preventDefault();
        post('/iletisim', {
            preserveScroll: true,
            onSuccess: () => {
                reset();
                // Turnstile widget'ını da reset et
                if (typeof window !== 'undefined' && (window as unknown as { turnstile?: { reset: () => void } }).turnstile) {
                    try {
                        (window as unknown as { turnstile: { reset: () => void } }).turnstile.reset();
                    } catch {
                        // sessizce yoksay
                    }
                }
            },
        });
    };

    return (
        <MainLayout>
            <SeoHead
                title="İletişim"
                description={`${site.name ?? 'Loğoğlu Hukuk Bürosu'} ile iletişime geçmek için form, telefon, e-posta veya KEP adresimizi kullanabilirsiniz. ${site.address ?? ''}`}
            />

            <PageHeader
                title="İletişim"
                description="Hukuki durumunuz hakkında görüşme için aşağıdaki form üzerinden talep iletebilir, dilerseniz doğrudan telefon ya da e-posta ile iletişim kurabilirsiniz."
            />

            <Section size="wide">
                <div className="grid gap-12 lg:grid-cols-12 lg:gap-16">
                    {/* Sol: iletişim bilgileri */}
                    <aside className="lg:col-span-5">
                        <FadeIn>
                            <div className="rounded-lg border border-border bg-surface p-8">
                                <h2
                                    className="text-2xl leading-tight text-text"
                                    style={{ fontFamily: 'var(--font-heading)', fontWeight: 500 }}
                                >
                                    İletişim Bilgileri
                                </h2>

                                <ul className="mt-8 space-y-6 text-sm">
                                    {site.address && (
                                        <li className="flex gap-4">
                                            <MapPin className="mt-0.5 h-5 w-5 shrink-0 text-accent" />
                                            <div>
                                                <p className="text-xs font-semibold uppercase tracking-[0.12em] text-text-muted">
                                                    Adres
                                                </p>
                                                <p className="mt-1 leading-relaxed text-text">
                                                    {site.address}
                                                </p>
                                            </div>
                                        </li>
                                    )}

                                    {site.phone && (
                                        <li className="flex gap-4">
                                            <Phone className="mt-0.5 h-5 w-5 shrink-0 text-accent" />
                                            <div>
                                                <p className="text-xs font-semibold uppercase tracking-[0.12em] text-text-muted">
                                                    Telefon
                                                </p>
                                                <a
                                                    href={`tel:${site.phone.replace(/\s/g, '')}`}
                                                    className="mt-1 block text-text hover:text-accent"
                                                >
                                                    {site.phone}
                                                </a>
                                            </div>
                                        </li>
                                    )}

                                    {site.email && (
                                        <li className="flex gap-4">
                                            <Mail className="mt-0.5 h-5 w-5 shrink-0 text-accent" />
                                            <div>
                                                <p className="text-xs font-semibold uppercase tracking-[0.12em] text-text-muted">
                                                    E-posta
                                                </p>
                                                <a
                                                    href={`mailto:${site.email}`}
                                                    className="mt-1 block text-text hover:text-accent"
                                                >
                                                    {site.email}
                                                </a>
                                            </div>
                                        </li>
                                    )}

                                    {site.kep && (
                                        <li className="flex gap-4">
                                            <Mail className="mt-0.5 h-5 w-5 shrink-0 text-accent" />
                                            <div>
                                                <p className="text-xs font-semibold uppercase tracking-[0.12em] text-text-muted">
                                                    KEP
                                                </p>
                                                <p className="mt-1 break-all text-text">
                                                    {site.kep}
                                                </p>
                                            </div>
                                        </li>
                                    )}
                                </ul>
                            </div>
                        </FadeIn>

                        {/* Harita */}
                        {site.map_embed_url && (
                            <FadeIn delay={0.1}>
                                <div className="mt-6 overflow-hidden rounded-lg border border-border bg-surface">
                                    <iframe
                                        src={site.map_embed_url}
                                        width="100%"
                                        height="360"
                                        style={{ border: 0 }}
                                        allowFullScreen
                                        loading="lazy"
                                        referrerPolicy="no-referrer-when-downgrade"
                                        title="Ofis Konumu"
                                    />
                                </div>
                            </FadeIn>
                        )}
                    </aside>

                    {/* Sağ: form */}
                    <div className="lg:col-span-7">
                        <FadeIn delay={0.05}>
                            <div className="rounded-lg border border-border bg-surface p-8 sm:p-10">
                                {flash?.success && (
                                    <div
                                        className="mb-8 flex items-start gap-3 rounded-md border border-border bg-surface-alt p-4 text-sm text-text"
                                        role="status"
                                    >
                                        <CheckCircle2 className="mt-0.5 h-5 w-5 shrink-0 text-accent" />
                                        <p>{flash.success}</p>
                                    </div>
                                )}

                                {flash?.error && (
                                    <div
                                        className="mb-8 rounded-md border border-border bg-surface-alt p-4 text-sm text-text"
                                        role="alert"
                                    >
                                        <p>{flash.error}</p>
                                    </div>
                                )}

                                <h2
                                    className="text-2xl leading-tight text-text"
                                    style={{ fontFamily: 'var(--font-heading)', fontWeight: 500 }}
                                >
                                    Mesaj Bırakın
                                </h2>
                                <p className="mt-3 text-sm leading-relaxed text-text-muted">
                                    Form üzerinden iletilen talepler 1-2 iş günü içinde
                                    değerlendirilerek tarafınıza dönüş sağlanır.
                                </p>

                                <form onSubmit={submit} className="mt-8 space-y-5" noValidate>
                                    {/* Honeypot — gerçek kullanıcılar görmez, botlar doldurur */}
                                    <div
                                        aria-hidden
                                        style={{
                                            position: 'absolute',
                                            left: '-9999px',
                                            width: '1px',
                                            height: '1px',
                                            overflow: 'hidden',
                                        }}
                                    >
                                        <label>
                                            Website (doldurmayın)
                                            <input
                                                type="text"
                                                name="website"
                                                value={data.website}
                                                onChange={(e) => setData('website', e.target.value)}
                                                tabIndex={-1}
                                                autoComplete="off"
                                            />
                                        </label>
                                    </div>

                                    <Field label="Ad Soyad" name="name" error={errors.name} required>
                                        <input
                                            id="name"
                                            type="text"
                                            value={data.name}
                                            onChange={(e) => setData('name', e.target.value)}
                                            className="input"
                                            autoComplete="name"
                                            required
                                        />
                                    </Field>

                                    <div className="grid gap-5 sm:grid-cols-2">
                                        <Field label="E-posta" name="email" error={errors.email} required>
                                            <input
                                                id="email"
                                                type="email"
                                                value={data.email}
                                                onChange={(e) => setData('email', e.target.value)}
                                                className="input"
                                                autoComplete="email"
                                                required
                                            />
                                        </Field>

                                        <Field label="Telefon" name="phone" error={errors.phone}>
                                            <input
                                                id="phone"
                                                type="tel"
                                                value={data.phone}
                                                onChange={(e) => setData('phone', e.target.value)}
                                                className="input"
                                                autoComplete="tel"
                                                placeholder="+90 5XX XXX XX XX"
                                            />
                                        </Field>
                                    </div>

                                    <Field label="Talep Türü" name="subject_type" error={errors.subject_type} required>
                                        <select
                                            id="subject_type"
                                            value={data.subject_type}
                                            onChange={(e) => setData('subject_type', e.target.value as SubjectKey)}
                                            className="input"
                                            required
                                        >
                                            {Object.entries(SUBJECT_TYPES).map(([key, label]) => (
                                                <option key={key} value={key}>
                                                    {label}
                                                </option>
                                            ))}
                                        </select>
                                    </Field>

                                    <Field label="Konu" name="subject" error={errors.subject}>
                                        <input
                                            id="subject"
                                            type="text"
                                            value={data.subject}
                                            onChange={(e) => setData('subject', e.target.value)}
                                            className="input"
                                            placeholder="Kısa bir başlık"
                                        />
                                    </Field>

                                    <Field label="Mesajınız" name="message" error={errors.message} required>
                                        <textarea
                                            id="message"
                                            value={data.message}
                                            onChange={(e) => setData('message', e.target.value)}
                                            rows={6}
                                            className="input resize-y"
                                            placeholder="Hukuki durumunuzu kısaca özetleyebilirsiniz (en az 20 karakter)"
                                            required
                                        />
                                    </Field>

                                    {/* Cloudflare Turnstile widget — sadece panel'den aktif edilmişse */}
                                    {turnstile?.enabled && turnstile?.siteKey && (
                                        <div className="border-t border-border pt-6">
                                            <div
                                                ref={turnstileContainerRef}
                                                className="cf-turnstile"
                                                data-sitekey={turnstile.siteKey}
                                                data-theme="auto"
                                                data-language="tr"
                                            />
                                            {errors['cf-turnstile-response'] && (
                                                <p className="mt-2 text-xs text-red-700">
                                                    {errors['cf-turnstile-response']}
                                                </p>
                                            )}
                                        </div>
                                    )}

                                    <div className="flex items-center justify-between border-t border-border pt-6">
                                        <p className="max-w-xs text-xs leading-relaxed text-text-muted">
                                            Formu göndererek KVKK Aydınlatma Metni'ni okuyup
                                            kabul etmiş sayılırsınız.
                                        </p>
                                        <button
                                            type="submit"
                                            disabled={processing}
                                            className={cn(
                                                'inline-flex items-center gap-2 rounded-md bg-primary px-6 py-3 text-sm font-medium text-primary-fg transition-opacity',
                                                processing ? 'opacity-60' : 'hover:opacity-90',
                                            )}
                                        >
                                            {processing ? 'Gönderiliyor...' : 'Gönder'}
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </FadeIn>
                    </div>
                </div>
            </Section>

            {/* Input tema stili */}
            <style>{`
                .input {
                    display: block;
                    width: 100%;
                    border-radius: 0.375rem;
                    border: 1px solid var(--color-border);
                    background: var(--color-bg);
                    padding: 0.75rem 0.875rem;
                    font-size: 0.9375rem;
                    color: var(--color-text);
                    transition: border-color 0.15s ease, box-shadow 0.15s ease;
                }
                .input:focus {
                    outline: none;
                    border-color: var(--color-accent);
                    box-shadow: 0 0 0 3px color-mix(in srgb, var(--color-accent) 20%, transparent);
                }
                .input::placeholder {
                    color: var(--color-text-muted);
                    opacity: 0.7;
                }
            `}</style>
        </MainLayout>
    );
}

function Field({
    label,
    name,
    required,
    error,
    children,
}: {
    label: string;
    name: string;
    required?: boolean;
    error?: string;
    children: React.ReactNode;
}) {
    return (
        <div>
            <label htmlFor={name} className="block text-xs font-medium text-text">
                {label}
                {required && <span className="ml-0.5 text-accent">*</span>}
            </label>
            <div className="mt-1.5">{children}</div>
            {error && <p className="mt-1.5 text-xs text-red-700">{error}</p>}
        </div>
    );
}
