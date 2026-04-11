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
 * Panelden (Site Settings → Logo) gerçek logo yüklendiyse onu gösterir.
 * Yüklenmemişse tipografik "L" monogram + büro adı (subtitle yok)
 * fallback olarak kullanılır.
 *
 * Tasarım ekibi logoyu hazırlayıp panelden yüklediğinde bu component'ın
 * kodu değiştirilmeden otomatik olarak yeni logo kullanılır.
 *
 * Varyantlar:
 * - default: Header'da kullanılır, yatay dizilim, monogram + büro adı
 * - compact: Dar alanlar için (font biraz daha küçük)
 * - minimal: Yalnızca "L" monogram (sidebar dar vs.)
 * - footer: Footer için, daha büyük monogram + başlık
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
                className={cn('w-auto', className)}
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
                <span
                    className={cn('font-semibold tracking-tight text-text', titleSize)}
                    style={{ fontFamily: 'var(--font-heading)' }}
                >
                    {name}
                </span>
            )}
        </div>
    );
}
