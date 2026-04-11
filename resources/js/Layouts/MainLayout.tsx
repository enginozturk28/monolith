import { type ReactNode } from 'react';
import Header from '@/Components/Layout/Header';
import Footer from '@/Components/Layout/Footer';
import SmoothScroll from '@/Components/SmoothScroll';
import ThemeSync from '@/Components/ThemeSync';

type Props = {
    children: ReactNode;
};

/**
 * MainLayout — tüm ön yüz sayfalarını saran standart layout.
 *
 * Sıra: SmoothScroll (etkisiz component, sadece Lenis'i kurar)
 *       → Header (sticky)
 *       → main (her sayfanın içeriği)
 *       → Footer
 *
 * `flex flex-col min-h-dvh` yapısı footer'ı içerik az olduğunda bile
 * sayfanın altına yapışık tutar.
 */
export default function MainLayout({ children }: Props) {
    return (
        <div className="flex min-h-dvh flex-col bg-bg text-text">
            <ThemeSync />
            <SmoothScroll />
            <Header />
            <main className="flex-1">{children}</main>
            <Footer />
        </div>
    );
}
