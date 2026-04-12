import { useEffect, useMemo, useState } from 'react';
import { Link, usePage } from '@inertiajs/react';
import { Menu, X } from 'lucide-react';
import { AnimatePresence, motion } from 'motion/react';
import TypographicLogo from '@/Components/Brand/TypographicLogo';
import Container from './Container';
import { mainNavigation } from '@/lib/nav';
import { cn } from '@/lib/utils';
import type { SharedProps } from '@/types/global';

/**
 * Header — sabit olmayan, scroll ile ince border ve arka plan kazanan ana header.
 *
 * Desktop'ta logo + 6 maddeli horizontal menü.
 * Mobile'da logo + hamburger; menü full-screen drawer olarak açılır.
 *
 * Not: Henüz Faz 5'te asıl sayfalar yazılmadığı için nav link'leri
 * href olarak placeholder, tıklanınca 404 dönebilir. Sayfalar hazır olunca
 * `lib/nav.ts`'teki href'ler Laravel route'larıyla değiştirilir.
 */
export default function Header() {
    const { url } = usePage();
    const { site } = usePage<SharedProps>().props;
    const [isOpen, setIsOpen] = useState(false);
    const [scrolled, setScrolled] = useState(false);

    // Panelden kontrol edilen sayfa toggle'larına göre nav filtreleme
    const visibleNav = useMemo(() => {
        const visibilityMap: Record<string, boolean> = {
            show_faq_page: site.show_faq_page,
        };
        return mainNavigation.filter((item) => {
            if (!item.visibilityKey) return true;
            return visibilityMap[item.visibilityKey] ?? true;
        });
    }, [site.show_faq_page]);

    // Scroll'da header background/border yoğunlaştır
    useEffect(() => {
        const onScroll = () => setScrolled(window.scrollY > 12);
        onScroll();
        window.addEventListener('scroll', onScroll, { passive: true });
        return () => window.removeEventListener('scroll', onScroll);
    }, []);

    // Menü açıkken body scroll'unu kilitle
    useEffect(() => {
        if (isOpen) {
            document.body.style.overflow = 'hidden';
        } else {
            document.body.style.overflow = '';
        }
        return () => {
            document.body.style.overflow = '';
        };
    }, [isOpen]);

    // Sayfa değişince menüyü kapat
    useEffect(() => {
        setIsOpen(false);
    }, [url]);

    const isActive = (href: string): boolean => {
        if (href === '/') return url === '/';
        return url.startsWith(href);
    };

    return (
        <header
            className={cn(
                'sticky top-0 z-40 transition-all duration-300',
                scrolled
                    ? 'border-b border-border bg-bg/95 backdrop-blur'
                    : 'border-b border-transparent bg-bg',
            )}
        >
            <Container size="wide" className="flex h-20 items-center justify-between">
                <Link
                    href="/"
                    aria-label="Loğoğlu Hukuk Bürosu — Ana Sayfa"
                    className="focus:outline-none"
                >
                    <TypographicLogo variant="default" />
                </Link>

                {/* Desktop navigation */}
                <nav aria-label="Ana menü" className="hidden items-center gap-1 lg:flex">
                    {visibleNav.map((item) => (
                        <Link
                            key={item.href}
                            href={item.href}
                            className={cn(
                                'rounded-md px-3 py-2 text-sm font-medium transition-colors',
                                isActive(item.href)
                                    ? 'text-text'
                                    : 'text-text-muted hover:text-text',
                            )}
                        >
                            {item.label}
                        </Link>
                    ))}
                </nav>

                {/* CTA placeholder — sadece iletişim sayfasına ölçülü yönlendirme */}
                <div className="hidden lg:block">
                    <Link
                        href="/iletisim"
                        className="inline-flex items-center gap-2 rounded-md border border-border bg-primary px-4 py-2.5 text-sm font-medium text-primary-fg transition-all hover:opacity-90 focus:outline-none focus:ring-2 focus:ring-accent focus:ring-offset-2 focus:ring-offset-bg"
                    >
                        Görüşme Talebi
                    </Link>
                </div>

                {/* Mobile hamburger */}
                <button
                    type="button"
                    onClick={() => setIsOpen((v) => !v)}
                    className="inline-flex h-10 w-10 items-center justify-center rounded-md text-text lg:hidden"
                    aria-label={isOpen ? 'Menüyü kapat' : 'Menüyü aç'}
                    aria-expanded={isOpen}
                >
                    {isOpen ? <X className="h-5 w-5" /> : <Menu className="h-5 w-5" />}
                </button>
            </Container>

            {/* Mobile drawer */}
            <AnimatePresence>
                {isOpen && (
                    <motion.div
                        key="mobile-drawer"
                        initial={{ opacity: 0 }}
                        animate={{ opacity: 1 }}
                        exit={{ opacity: 0 }}
                        transition={{ duration: 0.2 }}
                        className="fixed inset-x-0 top-20 z-30 h-[calc(100dvh-5rem)] overflow-y-auto bg-bg lg:hidden"
                    >
                        <Container size="wide" className="py-6">
                            <nav aria-label="Mobil menü" className="flex flex-col gap-1">
                                {mainNavigation.map((item, i) => (
                                    <motion.div
                                        key={item.href}
                                        initial={{ opacity: 0, y: 8 }}
                                        animate={{ opacity: 1, y: 0 }}
                                        transition={{ delay: i * 0.04, duration: 0.25 }}
                                    >
                                        <Link
                                            href={item.href}
                                            className={cn(
                                                'flex flex-col gap-0.5 border-b border-border py-4 text-base font-medium',
                                                isActive(item.href) ? 'text-text' : 'text-text-muted',
                                            )}
                                        >
                                            <span>{item.label}</span>
                                            {item.description && (
                                                <span className="text-xs font-normal text-text-muted">
                                                    {item.description}
                                                </span>
                                            )}
                                        </Link>
                                    </motion.div>
                                ))}
                            </nav>

                            <Link
                                href="/iletisim"
                                className="mt-8 flex w-full items-center justify-center rounded-md bg-primary px-4 py-3.5 text-sm font-medium text-primary-fg"
                            >
                                Görüşme Talebi
                            </Link>
                        </Container>
                    </motion.div>
                )}
            </AnimatePresence>
        </header>
    );
}
