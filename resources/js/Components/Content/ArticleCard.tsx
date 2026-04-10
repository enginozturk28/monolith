import { Link } from '@inertiajs/react';
import { ArrowUpRight, Clock } from 'lucide-react';
import { cn } from '@/lib/utils';

type Article = {
    slug: string;
    title: string;
    excerpt?: string | null;
    published_at?: string | null;
    reading_time_minutes?: number | null;
    category?: { slug: string; name: string } | null;
};

type Props = {
    article: Article;
    className?: string;
    variant?: 'default' | 'compact';
};

const DATE_FORMAT = new Intl.DateTimeFormat('tr-TR', {
    day: 'numeric',
    month: 'long',
    year: 'numeric',
});

function formatDate(iso?: string | null): string | null {
    if (!iso) return null;
    try {
        return DATE_FORMAT.format(new Date(iso));
    } catch {
        return null;
    }
}

export default function ArticleCard({ article, className, variant = 'default' }: Props) {
    const date = formatDate(article.published_at);

    return (
        <Link
            href={`/makaleler/${article.slug}`}
            className={cn(
                'group flex h-full flex-col gap-4 rounded-lg border border-border bg-surface p-6 transition-all duration-300 hover:-translate-y-0.5 hover:border-accent/40',
                variant === 'compact' && 'gap-3 p-5',
                className,
            )}
        >
            <div className="flex items-center gap-3 text-xs text-text-muted">
                {article.category && (
                    <span className="rounded-full border border-border px-2.5 py-1 text-[11px] font-medium uppercase tracking-[0.08em] text-text-muted">
                        {article.category.name}
                    </span>
                )}
                {date && (
                    <span>{date}</span>
                )}
            </div>

            <div className="flex-1">
                <h3
                    className={cn(
                        'text-text leading-snug transition-colors group-hover:text-primary',
                        variant === 'compact' ? 'text-lg' : 'text-xl sm:text-2xl',
                    )}
                    style={{ fontFamily: 'var(--font-heading)', fontWeight: 500 }}
                >
                    {article.title}
                </h3>
                {article.excerpt && (
                    <p className="mt-3 line-clamp-3 text-sm leading-relaxed text-text-muted">
                        {article.excerpt}
                    </p>
                )}
            </div>

            <div className="flex items-center justify-between border-t border-border pt-4 text-xs text-text-muted">
                {article.reading_time_minutes ? (
                    <span className="flex items-center gap-1.5">
                        <Clock className="h-3 w-3" />
                        {article.reading_time_minutes} dk okuma
                    </span>
                ) : (
                    <span />
                )}
                <ArrowUpRight className="h-4 w-4 text-text-muted transition-all duration-300 group-hover:translate-x-0.5 group-hover:-translate-y-0.5 group-hover:text-accent" />
            </div>
        </Link>
    );
}
