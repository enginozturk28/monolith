import { type ReactNode } from 'react';
import Container from './Container';
import FadeIn from '@/Components/Motion/FadeIn';

type Props = {
    eyebrow?: string;
    title: string;
    description?: string | null;
    children?: ReactNode;
};

/**
 * PageHeader — her iç sayfanın başında yer alan ortak başlık bloğu.
 *
 * Eyebrow (üst etiket), büyük serif başlık ve opsiyonel açıklama içerir.
 * Alt kısımda breadcrumb veya meta bilgiler için children slot.
 */
export default function PageHeader({ eyebrow, title, description, children }: Props) {
    return (
        <header className="border-b border-border bg-surface-alt">
            <Container size="wide" className="py-16 sm:py-20 lg:py-24">
                {eyebrow && (
                    <FadeIn>
                        <p className="text-xs font-semibold uppercase tracking-[0.2em] text-text-muted">
                            {eyebrow}
                        </p>
                    </FadeIn>
                )}

                <FadeIn delay={0.05}>
                    <h1
                        className="mt-4 max-w-4xl text-balance text-4xl leading-[1.1] text-text sm:text-5xl lg:text-6xl"
                        style={{ fontFamily: 'var(--font-heading)', fontWeight: 500 }}
                    >
                        {title}
                    </h1>
                </FadeIn>

                {description && (
                    <FadeIn delay={0.1}>
                        <p className="mt-6 max-w-2xl text-lg leading-relaxed text-text-muted">
                            {description}
                        </p>
                    </FadeIn>
                )}

                {children && <div className="mt-8">{children}</div>}
            </Container>
        </header>
    );
}
