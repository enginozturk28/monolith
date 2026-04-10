import { type ReactNode } from 'react';
import { cn } from '@/lib/utils';
import Container from './Container';

type Tone = 'default' | 'surface' | 'accent';
type Size = 'narrow' | 'default' | 'wide' | 'full';

type Props = {
    children: ReactNode;
    tone?: Tone;
    size?: Size;
    className?: string;
    containerClassName?: string;
    id?: string;
};

/**
 * Section — sayfa içinde dikey bir bölüm. Tema tokenlarıyla uyumlu
 * background rengi alır, üstten/alttan padding uygular, içeriği Container
 * ile merkezler.
 */
export default function Section({
    children,
    tone = 'default',
    size = 'default',
    className,
    containerClassName,
    id,
}: Props) {
    const toneClass = {
        default: 'bg-bg',
        surface: 'bg-surface',
        accent: 'bg-surface-alt',
    }[tone];

    return (
        <section id={id} className={cn('py-16 sm:py-20 lg:py-24', toneClass, className)}>
            <Container size={size} className={containerClassName}>
                {children}
            </Container>
        </section>
    );
}
