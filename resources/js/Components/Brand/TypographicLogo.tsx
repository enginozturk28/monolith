import { usePage } from '@inertiajs/react';
import type { SharedProps } from '@/types/global';
import { cn } from '@/lib/utils';

type Variant = 'default' | 'compact' | 'minimal' | 'footer';

type Props = {
    variant?: Variant;
    className?: string;
};

/**
 * TypographicLogo — Loğoğlu Hukuk Bürosu logosu.
 *
 * Panelden (Filament Settings) gerçek logo yüklendiyse onu gösterir.
 * Yüklenmemişse tipografik "L" monogram + büro adı + alt başlık gösterilir.
 *
 * Tasarım ekibi logoyu hazırlayıp panelden yüklediğinde bu component'ın
 * kodu değiştirilmeden otomatik olarak yeni logo kullanılır.
 *
 * Varyantlar:
 * - default: Header'da kullanılır, yatay dizilim, monogram + başlık
 * - compact: Dar alanlar için sadece monogram + başlık (alt başlık yok)
 * - minimal: Yalnızca "L" monogram (sidebar dar vs.)
 * - footer: Footer için, alt başlık ile birlikte (daha büyük)
 */
export default function TypographicLogo({ variant = 'default', className }: Props) {
    const { site } = usePage<SharedProps>().props;
    const logoUrl = site.logo_url;
    const name = site.name || 'Loğoğlu Hukuk Bürosu';

    // Gerçek logo yüklendiyse onu kullan
    if (logoUrl) {
        const height = variant === 'minimal' ? 32 : variant === 'footer' ? 56 : 40;
        return (
            <img
                src={logoUrl}
                alt={name}
                height={height}
                className={cn('h-10 w-auto', className)}
                style={{ height: `${height}px` }}
            />
        );
    }

    // Tipografik fallback
    const monogramSize = {
        default: 'h-10 w-10 text-xl',
        compact: 'h-9 w-9 text-lg',
        minimal: 'h-10 w-10 text-xl',
        footer: 'h-12 w-12 text-2xl',
    }[variant];

    const titleSize = {
        default: 'text-[15px]',
        compact: 'text-sm',
        minimal: 'hidden',
        footer: 'text-lg',
    }[variant];

    const subtitleVisible = variant === 'default' || variant === 'footer';

    return (
        <div className={cn('flex items-center gap-3', className)}>
            <div
                className={cn(
                    'flex shrink-0 items-center justify-center rounded-md shadow-sm',
                    monogramSize,
                )}
                style={{
                    backgroundColor: 'var(--color-primary)',
                    color: 'var(--color-primary-fg)',
                    fontFamily: 'var(--font-heading)',
                }}
                aria-hidden
            >
                <span className="font-semibold leading-none tracking-tight">L</span>
            </div>

            {variant !== 'minimal' && (
                <div className="flex flex-col leading-tight">
                    <span
                        className={cn('font-semibold tracking-tight text-text', titleSize)}
                        style={{ fontFamily: 'var(--font-heading)' }}
                    >
                        {name}
                    </span>
                    {subtitleVisible && site.tagline && (
                        <span className="text-[10px] font-medium uppercase tracking-[0.14em] text-text-muted">
                            Hukuk Bürosu
                        </span>
                    )}
                </div>
            )}
        </div>
    );
}
