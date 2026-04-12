import { Link, usePage } from '@inertiajs/react';
import { Mail, MapPin, Phone } from 'lucide-react';
import TypographicLogo from '@/Components/Brand/TypographicLogo';
import Container from './Container';
import { legalNavigation, mainNavigation } from '@/lib/nav';
import type { SharedProps } from '@/types/global';

type FooterServiceLink = {
    slug: string;
    title: string;
};

/*
 * Sosyal medya ikonları — inline SVG.
 * Lucide v1'de brand ikonları (Linkedin, Instagram, Whatsapp vb.) telif
 * sebebiyle kaldırıldı. Ayrı bir bağımlılık eklemek yerine 4 ikonu da
 * inline SVG path'leri olarak tutuyoruz — bundle'a sıfır ekstra maliyet.
 */

function LinkedInIcon({ className }: { className?: string }) {
    return (
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" className={className} aria-hidden>
            <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 0 1-2.063-2.065 2.063 2.063 0 1 1 2.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z" />
        </svg>
    );
}

function WhatsAppIcon({ className }: { className?: string }) {
    return (
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" className={className} aria-hidden>
            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413Z" />
        </svg>
    );
}

function InstagramIcon({ className }: { className?: string }) {
    return (
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" className={className} aria-hidden>
            <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 1 0 0 12.324 6.162 6.162 0 0 0 0-12.324zM12 16a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm6.406-11.845a1.44 1.44 0 1 0 0 2.881 1.44 1.44 0 0 0 0-2.881z" />
        </svg>
    );
}

function XIcon({ className }: { className?: string }) {
    return (
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" className={className} aria-hidden>
            <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z" />
        </svg>
    );
}

type SocialLink = {
    name: string;
    url: string;
    icon: React.ComponentType<{ className?: string }>;
};

/**
 * Footer — 4 sütunlu yapı:
 * 1. Büro kimliği + dinamik açıklama metni (panelden)
 * 2. Site haritası (ana menü)
 * 3. Faaliyet alanları (shared props'tan)
 * 4. İletişim bilgileri detay
 *
 * Alt bar: copyright (dinamik) + sosyal medya ikonları (dolu olanlar) + legal links
 */
export default function Footer() {
    const { site, footerServices } = usePage<SharedProps & { footerServices: FooterServiceLink[] }>().props;
    const year = new Date().getFullYear();

    // Panelden doldurulmuş sosyal medya linklerini topla
    const socialLinks: SocialLink[] = [];
    if (site.linkedin_url) {
        socialLinks.push({ name: 'LinkedIn', url: site.linkedin_url, icon: LinkedInIcon });
    }
    if (site.whatsapp_url) {
        socialLinks.push({ name: 'WhatsApp', url: site.whatsapp_url, icon: WhatsAppIcon });
    }
    if (site.instagram_url) {
        socialLinks.push({ name: 'Instagram', url: site.instagram_url, icon: InstagramIcon });
    }
    if (site.x_url) {
        socialLinks.push({ name: 'X', url: site.x_url, icon: XIcon });
    }

    // Copyright satırı: "© {yıl} {büro adı}. {copyright_text}"
    const copyrightText = site.copyright_text ?? 'Tüm hakları saklıdır.';
    const brandName = site.name ?? 'Loğoğlu Hukuk Bürosu';

    return (
        <footer className="border-t border-border bg-surface-alt">
            <Container size="wide" className="py-16 lg:py-20">
                <div className="grid gap-12 lg:grid-cols-12">
                    {/* Kolon 1: Kimlik + tanıtım */}
                    <div className="lg:col-span-4">
                        <TypographicLogo variant="footer" />
                        {site.footer_description && (
                            <p className="mt-6 max-w-sm text-sm leading-relaxed text-text-muted">
                                {site.footer_description}
                            </p>
                        )}
                    </div>

                    {/* Kolon 2: Site haritası */}
                    <nav aria-label="Site haritası" className="lg:col-span-2">
                        <h3 className="text-xs font-semibold uppercase tracking-[0.14em] text-text">
                            Menü
                        </h3>
                        <ul className="mt-5 space-y-2.5">
                            {mainNavigation
                                .filter((item) => {
                                    if (!item.visibilityKey) return true;
                                    if (item.visibilityKey === 'show_faq_page') return site.show_faq_page;
                                    return true;
                                })
                                .map((item) => (
                                    <li key={item.href}>
                                        <Link
                                            href={item.href}
                                            className="text-sm text-text-muted transition-colors hover:text-text"
                                        >
                                            {item.label}
                                        </Link>
                                    </li>
                                ))}
                        </ul>
                    </nav>

                    {/* Kolon 3: Faaliyet alanları */}
                    <nav aria-label="Faaliyet alanları" className="lg:col-span-3">
                        <h3 className="text-xs font-semibold uppercase tracking-[0.14em] text-text">
                            Faaliyet Alanları
                        </h3>
                        <ul className="mt-5 grid grid-cols-1 gap-2.5 sm:grid-cols-2 lg:grid-cols-1">
                            {(footerServices ?? []).slice(0, 12).map((service) => (
                                <li key={service.slug}>
                                    <Link
                                        href={`/faaliyet-alanlari/${service.slug}`}
                                        className="text-sm text-text-muted transition-colors hover:text-text"
                                    >
                                        {service.title}
                                    </Link>
                                </li>
                            ))}
                        </ul>
                    </nav>

                    {/* Kolon 4: İletişim */}
                    <div className="lg:col-span-3">
                        <h3 className="text-xs font-semibold uppercase tracking-[0.14em] text-text">
                            İletişim
                        </h3>
                        <ul className="mt-5 space-y-4 text-sm">
                            {site.address && (
                                <li className="flex gap-3 text-text-muted">
                                    <MapPin className="mt-0.5 h-4 w-4 shrink-0 text-accent" />
                                    <span className="leading-relaxed">{site.address}</span>
                                </li>
                            )}
                            {site.phone && (
                                <li>
                                    <a
                                        href={`tel:${site.phone.replace(/\s/g, '')}`}
                                        className="flex items-center gap-3 text-text-muted transition-colors hover:text-text"
                                    >
                                        <Phone className="h-4 w-4 shrink-0 text-accent" />
                                        <span>{site.phone}</span>
                                    </a>
                                </li>
                            )}
                            {site.email && (
                                <li>
                                    <a
                                        href={`mailto:${site.email}`}
                                        className="flex items-center gap-3 text-text-muted transition-colors hover:text-text"
                                    >
                                        <Mail className="h-4 w-4 shrink-0 text-accent" />
                                        <span>{site.email}</span>
                                    </a>
                                </li>
                            )}
                            {site.kep && (
                                <li className="flex gap-3 text-text-muted">
                                    <Mail className="mt-0.5 h-4 w-4 shrink-0 text-accent" />
                                    <div className="flex flex-col">
                                        <span className="text-[11px] uppercase tracking-[0.12em] text-text-muted/70">
                                            KEP
                                        </span>
                                        <span>{site.kep}</span>
                                    </div>
                                </li>
                            )}
                        </ul>
                    </div>
                </div>

                {/* Alt bar: copyright + sosyal medya + yasal linkler */}
                <div className="mt-16 border-t border-border pt-8">
                    <div className="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
                        {/* Copyright */}
                        <p className="text-xs text-text-muted">
                            © {year} {brandName}. {copyrightText}
                        </p>

                        {/* Sosyal medya ikonları — sadece dolu olanlar */}
                        {socialLinks.length > 0 && (
                            <ul className="flex items-center gap-2">
                                {socialLinks.map((s) => {
                                    const Icon = s.icon;
                                    return (
                                        <li key={s.name}>
                                            <a
                                                href={s.url}
                                                target="_blank"
                                                rel="noopener noreferrer"
                                                aria-label={s.name}
                                                className="inline-flex h-9 w-9 items-center justify-center rounded-full border border-border text-text-muted transition-colors hover:border-accent hover:text-accent"
                                            >
                                                <Icon className="h-4 w-4" />
                                            </a>
                                        </li>
                                    );
                                })}
                            </ul>
                        )}

                        {/* Yasal linkler */}
                        <ul className="flex flex-wrap items-center gap-x-6 gap-y-2 text-xs text-text-muted">
                            {legalNavigation.map((item) => (
                                <li key={item.href}>
                                    <Link
                                        href={item.href}
                                        className="transition-colors hover:text-text"
                                    >
                                        {item.label}
                                    </Link>
                                </li>
                            ))}
                        </ul>
                    </div>
                </div>
            </Container>
        </footer>
    );
}
