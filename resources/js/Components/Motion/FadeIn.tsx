import { motion, useReducedMotion, type HTMLMotionProps } from 'motion/react';
import { type ReactNode } from 'react';

type Direction = 'up' | 'down' | 'left' | 'right' | 'none';

type Props = {
    children: ReactNode;
    direction?: Direction;
    delay?: number;
    duration?: number;
    amount?: number; // 0 - 1 (viewport trigger percentage)
    once?: boolean;
    className?: string;
    as?: keyof React.JSX.IntrinsicElements;
} & Omit<HTMLMotionProps<'div'>, 'initial' | 'animate' | 'whileInView' | 'transition' | 'viewport'>;

/**
 * FadeIn — viewport'a girerken ölçülü bir fade + kayma animasyonu.
 *
 * Reduced motion aktifse sadece opacity değişir, transform yok.
 * Hukuk bürosu kurumsal tonu için 0.5-0.7 saniye yumuşak default.
 */
export default function FadeIn({
    children,
    direction = 'up',
    delay = 0,
    duration = 0.6,
    amount = 0.2,
    once = true,
    className,
    as: _as,
    ...rest
}: Props) {
    const prefersReduced = useReducedMotion();

    const distance = 24;
    const offsets: Record<Direction, { x: number; y: number }> = {
        up: { x: 0, y: distance },
        down: { x: 0, y: -distance },
        left: { x: distance, y: 0 },
        right: { x: -distance, y: 0 },
        none: { x: 0, y: 0 },
    };

    const initial = prefersReduced
        ? { opacity: 0 }
        : { opacity: 0, x: offsets[direction].x, y: offsets[direction].y };

    const animate = prefersReduced ? { opacity: 1 } : { opacity: 1, x: 0, y: 0 };

    return (
        <motion.div
            initial={initial}
            whileInView={animate}
            viewport={{ once, amount }}
            transition={{
                duration,
                delay,
                ease: [0.25, 0.1, 0.25, 1],
            }}
            className={className}
            {...rest}
        >
            {children}
        </motion.div>
    );
}
