import { type ReactNode } from 'react';
import { cn } from '@/lib/utils';

type Size = 'narrow' | 'default' | 'wide' | 'full';

type Props = {
    children: ReactNode;
    size?: Size;
    className?: string;
    as?: keyof React.JSX.IntrinsicElements;
};

/**
 * Container — sayfa içeriğini merkezleyen ve horizontal padding uygulayan wrapper.
 *
 * Size varyantları:
 * - narrow: max-w-3xl (makale içi okuma odaklı)
 * - default: max-w-6xl (genel sayfalar)
 * - wide: max-w-7xl (hero, grid'ler)
 * - full: max-w-none (görsel arka plan için)
 */
export default function Container({
    children,
    size = 'default',
    className,
    as: Tag = 'div',
}: Props) {
    const sizeClass = {
        narrow: 'max-w-3xl',
        default: 'max-w-6xl',
        wide: 'max-w-7xl',
        full: 'max-w-none',
    }[size];

    const Component = Tag as React.ElementType;

    return (
        <Component className={cn('mx-auto w-full px-6 sm:px-8 lg:px-12', sizeClass, className)}>
            {children}
        </Component>
    );
}
