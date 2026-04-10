import { Link } from '@inertiajs/react';
import { ArrowRight } from 'lucide-react';
import ServiceIcon from './ServiceIcon';
import { cn } from '@/lib/utils';

type Props = {
    slug: string;
    title: string;
    icon?: string | null;
    summary?: string | null;
    className?: string;
};

/**
 * ServiceCard — faaliyet alanı için kart bileşeni. Anasayfa ve
 * /faaliyet-alanlari listesinde kullanılır.
 *
 * Tıklanabilir kart: tüm alan link. Hover'da hafif yükselme + accent border.
 */
export default function ServiceCard({ slug, title, icon, summary, className }: Props) {
    return (
        <Link
            href={`/faaliyet-alanlari/${slug}`}
            className={cn(
                'group relative flex h-full flex-col gap-4 rounded-lg border border-border bg-surface p-6 transition-all duration-300 hover:-translate-y-0.5 hover:border-accent/40 hover:shadow-sm',
                className,
            )}
        >
            <div
                className="inline-flex h-11 w-11 items-center justify-center rounded-md"
                style={{
                    backgroundColor: 'color-mix(in srgb, var(--color-primary) 6%, transparent)',
                    color: 'var(--color-primary)',
                }}
            >
                <ServiceIcon name={icon} className="h-5 w-5" />
            </div>

            <div className="flex flex-1 flex-col">
                <h3
                    className="text-xl leading-snug text-text"
                    style={{ fontFamily: 'var(--font-heading)', fontWeight: 500 }}
                >
                    {title}
                </h3>
                {summary && (
                    <p className="mt-3 flex-1 text-sm leading-relaxed text-text-muted">
                        {summary}
                    </p>
                )}
            </div>

            <div className="flex items-center gap-1.5 text-xs font-medium text-accent opacity-0 transition-opacity duration-300 group-hover:opacity-100">
                <span>Detaylı bilgi</span>
                <ArrowRight className="h-3.5 w-3.5" />
            </div>
        </Link>
    );
}
