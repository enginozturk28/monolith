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

/**
 * Footer — 4 sütunlu yapı:
 * 1. Büro kimliği + kısa tanıtım + iletişim özeti
 * 2. Site haritası (ana menü)
 * 3. Faaliyet alanları (shared props'tan)
 * 4. İletişim bilgileri detay
 *
 * En altta copyright + yasal linkler (KVKK, çerez politikası)
 */
export default function Footer() {
    const { site, footerServices } = usePage<SharedProps & { footerServices: FooterServiceLink[] }>().props;
    const year = new Date().getFullYear();

    return (
        <footer className="border-t border-border bg-surface-alt">
            <Container size="wide" className="py-16 lg:py-20">
                <div className="grid gap-12 lg:grid-cols-12">
                    {/* Kolon 1: Kimlik + tanıtım */}
                    <div className="lg:col-span-4">
                        <TypographicLogo variant="footer" />
                        <p className="mt-6 max-w-sm text-sm leading-relaxed text-text-muted">
                            2024 yılında kurulan {site.name ?? 'Loğoğlu Hukuk Bürosu'},
                            bireysel ve kurumsal müvekkillerine güvenilir, etik ve çözüm odaklı
                            hukuki hizmetler sunmayı amaçlamaktadır.
                        </p>
                    </div>

                    {/* Kolon 2: Site haritası */}
                    <nav aria-label="Site haritası" className="lg:col-span-2">
                        <h3 className="text-xs font-semibold uppercase tracking-[0.14em] text-text">
                            Menü
                        </h3>
                        <ul className="mt-5 space-y-2.5">
                            {mainNavigation.map((item) => (
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

                {/* Alt bar: copyright + yasal linkler */}
                <div className="mt-16 flex flex-col gap-4 border-t border-border pt-8 text-xs text-text-muted sm:flex-row sm:items-center sm:justify-between">
                    <p>
                        © {year} {site.name ?? 'Loğoğlu Hukuk Bürosu'}. Tüm hakları saklıdır.
                    </p>
                    <ul className="flex flex-wrap items-center gap-x-6 gap-y-2">
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
            </Container>
        </footer>
    );
}
