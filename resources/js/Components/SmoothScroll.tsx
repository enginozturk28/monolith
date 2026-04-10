import { useEffect } from 'react';
import Lenis from 'lenis';

/**
 * SmoothScroll — Lenis ile yumuşak kaydırma.
 *
 * Prefers-reduced-motion respektedilir; kullanıcı işletim sisteminde
 * animasyon kısıtlaması aktifse Lenis devre dışı kalır (erişilebilirlik).
 *
 * Body üzerinde `data-lenis` attribute'u set edilir, CSS ile native scroll
 * davranışı override edilir.
 */
export default function SmoothScroll() {
    useEffect(() => {
        // Reduced motion tercihini kontrol et
        const prefersReduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        if (prefersReduced) return;

        // Touch cihazlarda (mobil) native scroll daha iyi — Lenis devre dışı
        const isTouch = 'ontouchstart' in window || navigator.maxTouchPoints > 0;
        if (isTouch) return;

        const lenis = new Lenis({
            duration: 1.15,
            easing: (t: number) => Math.min(1, 1.001 - Math.pow(2, -10 * t)),
            smoothWheel: true,
            wheelMultiplier: 1,
            touchMultiplier: 1.5,
        });

        let rafId = 0;
        const raf = (time: number) => {
            lenis.raf(time);
            rafId = requestAnimationFrame(raf);
        };
        rafId = requestAnimationFrame(raf);

        document.body.setAttribute('data-lenis', 'true');

        return () => {
            cancelAnimationFrame(rafId);
            lenis.destroy();
            document.body.removeAttribute('data-lenis');
        };
    }, []);

    return null;
}
