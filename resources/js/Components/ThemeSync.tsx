import { useEffect } from 'react';
import { usePage } from '@inertiajs/react';
import type { SharedProps, ThemeTokens } from '@/types/global';

/**
 * ThemeSync — Inertia shared props'tan gelen tema tokenlarını client-side'da
 * `:root` CSS değişkenlerine canlı olarak uygular.
 *
 * Neden: İlk sayfa yüklenişinde blade template head'e inline
 * `<style id="monolith-theme">:root{--theme-*: ...}</style>` basar — bu SSR
 * seviyesinde doğru çalışır. Ancak Inertia SPA navigation'da sonraki sayfa
 * geçişlerinde head güncellenmez (sadece `<div id="app">` içeriği değişir),
 * dolayısıyla tema değiştirildikten sonra başka bir sayfaya geçerken ilk
 * sayfanın eski değerleri kalır.
 *
 * Bu component, `props.theme` her güncellendiğinde `:root`'a doğrudan yazar.
 * Hem ilk yüklemede (idempotent, aynı değerleri tekrar yazar) hem de Inertia
 * navigate'te her zaman güncel tokenlar uygulanır.
 *
 * Herhangi bir DOM'a render etmez (return null), sadece side effect çalıştırır.
 */

const TOKEN_MAP: Record<keyof ThemeTokens, string> = {
    bg: '--theme-bg',
    surface: '--theme-surface',
    surfaceAlt: '--theme-surface-alt',
    text: '--theme-text',
    textMuted: '--theme-text-muted',
    border: '--theme-border',
    primary: '--theme-primary',
    primaryFg: '--theme-primary-fg',
    accent: '--theme-accent',
    fontHeading: '--theme-font-heading',
    fontBody: '--theme-font-body',
};

export default function ThemeSync() {
    const { theme } = usePage<SharedProps>().props;

    useEffect(() => {
        if (!theme || typeof document === 'undefined') {
            return;
        }

        const root = document.documentElement;
        (Object.keys(TOKEN_MAP) as Array<keyof ThemeTokens>).forEach((key) => {
            const value = theme[key];
            if (value) {
                root.style.setProperty(TOKEN_MAP[key], value);
            }
        });
    }, [theme]);

    return null;
}
