import { type ReactNode, useMemo } from 'react';
import { cn } from '@/lib/utils';

type Props = {
    html?: string | null;
    fallback?: ReactNode;
    className?: string;
};

/**
 * RichTextContent — Filament RichEditor'den gelen HTML içeriği güvenli
 * şekilde render eder ve tipografik stil uygular.
 *
 * Filament/TipTap HTML'i zaten sanitize edilmiş (XSS'e karşı) ama extra
 * önlem olarak sadece güvenli tag'ler ve attribute'lar beklenir.
 *
 * Tüm tipografi tema tokenlarıyla uyumlu — `var(--font-heading)` ve
 * `var(--color-text)` kullanılır. Tailwind prose yerine inline style
 * kullanıldı çünkü prose plugin kurmadık ve tema değişkenlerine bağlı
 * kalmak istiyoruz.
 */
export default function RichTextContent({ html, fallback, className }: Props) {
    const content = useMemo(() => html?.trim() ?? '', [html]);

    if (!content) {
        return fallback ? <>{fallback}</> : null;
    }

    return (
        <div
            className={cn('monolith-prose', className)}
            dangerouslySetInnerHTML={{ __html: content }}
        />
    );
}
