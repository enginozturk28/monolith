import { Link } from '@inertiajs/react';
import { ArrowLeft, Clock, User } from 'lucide-react';
import MainLayout from '@/Layouts/MainLayout';
import Container from '@/Components/Layout/Container';
import Section from '@/Components/Layout/Section';
import RichTextContent from '@/Components/Content/RichTextContent';
import ArticleCard from '@/Components/Content/ArticleCard';
import FadeIn from '@/Components/Motion/FadeIn';
import SeoHead from '@/Components/Seo/SeoHead';

type Article = {
    slug: string;
    title: string;
    excerpt?: string | null;
    body?: string | null;
    published_at?: string | null;
    reading_time_minutes?: number | null;
    meta_title?: string | null;
    meta_description?: string | null;
    category?: { slug: string; name: string } | null;
    author?: { name: string } | null;
};

type RelatedArticle = {
    slug: string;
    title: string;
    excerpt?: string | null;
    published_at?: string | null;
    reading_time_minutes?: number | null;
    category?: { slug: string; name: string } | null;
};

type Props = {
    article: Article;
    related: RelatedArticle[];
};

const DATE_FORMAT = new Intl.DateTimeFormat('tr-TR', {
    day: 'numeric',
    month: 'long',
    year: 'numeric',
});

export default function ArticleShow({ article, related }: Props) {
    const publishedDate = article.published_at
        ? DATE_FORMAT.format(new Date(article.published_at))
        : null;

    // schema.org Article structured data
    const articleJsonLd = {
        '@context': 'https://schema.org',
        '@type': 'Article',
        headline: article.title,
        description: article.excerpt,
        datePublished: article.published_at,
        author: article.author
            ? {
                  '@type': 'Person',
                  name: article.author.name,
              }
            : undefined,
        publisher: {
            '@type': 'LegalService',
            name: 'Loğoğlu Hukuk Bürosu',
        },
        articleSection: article.category?.name,
    };

    return (
        <MainLayout>
            <SeoHead
                title={article.meta_title ?? article.title}
                description={article.meta_description ?? article.excerpt ?? undefined}
                type="article"
                publishedTime={article.published_at}
                author={article.author?.name}
                jsonLd={articleJsonLd}
            />

            {/* Başlık */}
            <header className="border-b border-border bg-surface-alt">
                <Container size="narrow" className="py-16 sm:py-20 lg:py-24">
                    <FadeIn>
                        <Link
                            href="/makaleler"
                            className="inline-flex items-center gap-1.5 text-xs font-medium uppercase tracking-[0.14em] text-text-muted hover:text-text"
                        >
                            <ArrowLeft className="h-3 w-3" />
                            Makaleler
                        </Link>
                    </FadeIn>

                    <FadeIn delay={0.05}>
                        <div className="mt-8 flex flex-wrap items-center gap-3 text-xs text-text-muted">
                            {article.category && (
                                <Link
                                    href={`/makaleler?kategori=${article.category.slug}`}
                                    className="rounded-full border border-border bg-bg px-2.5 py-1 text-[11px] font-medium uppercase tracking-[0.08em] hover:text-text"
                                >
                                    {article.category.name}
                                </Link>
                            )}
                            {publishedDate && <span>{publishedDate}</span>}
                            {article.reading_time_minutes && (
                                <span className="flex items-center gap-1">
                                    <Clock className="h-3 w-3" />
                                    {article.reading_time_minutes} dk okuma
                                </span>
                            )}
                        </div>
                    </FadeIn>

                    <FadeIn delay={0.1}>
                        <h1
                            className="mt-6 text-balance text-4xl leading-[1.15] text-text sm:text-5xl lg:text-6xl"
                            style={{ fontFamily: 'var(--font-heading)', fontWeight: 500 }}
                        >
                            {article.title}
                        </h1>
                    </FadeIn>

                    {article.excerpt && (
                        <FadeIn delay={0.15}>
                            <p className="mt-8 text-lg leading-relaxed text-text-muted">
                                {article.excerpt}
                            </p>
                        </FadeIn>
                    )}

                    {article.author && (
                        <FadeIn delay={0.2}>
                            <div className="mt-10 flex items-center gap-3 border-t border-border pt-6 text-sm">
                                <div
                                    className="flex h-10 w-10 items-center justify-center rounded-full text-xs font-semibold"
                                    style={{
                                        backgroundColor: 'var(--color-primary)',
                                        color: 'var(--color-primary-fg)',
                                        fontFamily: 'var(--font-heading)',
                                    }}
                                    aria-hidden
                                >
                                    {article.author.name.charAt(0)}
                                </div>
                                <div>
                                    <p className="text-text">{article.author.name}</p>
                                    <p className="text-xs text-text-muted">Avukat</p>
                                </div>
                            </div>
                        </FadeIn>
                    )}
                </Container>
            </header>

            {/* Gövde */}
            <Section size="wide">
                <Container size="narrow" className="px-0">
                    <RichTextContent html={article.body} />
                </Container>
            </Section>

            {/* İlgili makaleler */}
            {related.length > 0 && (
                <Section tone="surface" size="wide" className="border-t border-border">
                    <FadeIn>
                        <h2
                            className="text-2xl leading-tight text-text sm:text-3xl"
                            style={{ fontFamily: 'var(--font-heading)', fontWeight: 500 }}
                        >
                            Diğer Makaleler
                        </h2>
                    </FadeIn>
                    <div className="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                        {related.map((item, i) => (
                            <FadeIn key={item.slug} delay={0.05 * i}>
                                <ArticleCard article={item} />
                            </FadeIn>
                        ))}
                    </div>
                </Section>
            )}
        </MainLayout>
    );
}
