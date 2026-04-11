import { Link, router } from '@inertiajs/react';
import { ArrowLeft, ArrowRight } from 'lucide-react';
import MainLayout from '@/Layouts/MainLayout';
import Section from '@/Components/Layout/Section';
import PageHeader from '@/Components/Layout/PageHeader';
import ArticleCard from '@/Components/Content/ArticleCard';
import FadeIn from '@/Components/Motion/FadeIn';
import SeoHead from '@/Components/Seo/SeoHead';
import { cn } from '@/lib/utils';

type Article = {
    slug: string;
    title: string;
    excerpt?: string | null;
    published_at?: string | null;
    reading_time_minutes?: number | null;
    category?: { slug: string; name: string } | null;
};

type Category = {
    slug: string;
    name: string;
    count: number;
};

type PaginatedArticles = {
    data: Article[];
    links: { url: string | null; label: string; active: boolean }[];
    current_page: number;
    last_page: number;
};

type Props = {
    articles: PaginatedArticles;
    categories: Category[];
    currentCategory: string | null;
};

export default function ArticlesIndex({ articles, categories, currentCategory }: Props) {
    const goToCategory = (slug: string | null) => {
        router.get(
            '/makaleler',
            slug ? { kategori: slug } : {},
            { preserveState: false, preserveScroll: false },
        );
    };

    return (
        <MainLayout>
            <SeoHead
                title="Makaleler"
                description="Güncel hukuki gelişmeler ve değerlendirmeler. Loğoğlu Hukuk Bürosu yazılarımız bilgi paylaşımı amacıyla hazırlanmıştır."
            />

            <PageHeader
                title="Makaleler"
                description="Güncel hukuki gelişmeler ve değerlendirmeler. Yazılarımız bilgi paylaşımı amacıyla hazırlanmıştır ve kişiye özel hukuki görüş niteliği taşımaz."
            />

            <Section size="wide">
                {/* Kategori filtreleri */}
                {categories.length > 0 && (
                    <FadeIn>
                        <div className="mb-10 flex flex-wrap items-center gap-2 border-b border-border pb-6">
                            <button
                                type="button"
                                onClick={() => goToCategory(null)}
                                className={cn(
                                    'rounded-full border px-4 py-2 text-xs font-medium uppercase tracking-[0.08em] transition-colors',
                                    !currentCategory
                                        ? 'border-primary bg-primary text-primary-fg'
                                        : 'border-border text-text-muted hover:border-text/30 hover:text-text',
                                )}
                            >
                                Tümü
                            </button>
                            {categories.map((cat) => (
                                <button
                                    key={cat.slug}
                                    type="button"
                                    onClick={() => goToCategory(cat.slug)}
                                    className={cn(
                                        'rounded-full border px-4 py-2 text-xs font-medium uppercase tracking-[0.08em] transition-colors',
                                        currentCategory === cat.slug
                                            ? 'border-primary bg-primary text-primary-fg'
                                            : 'border-border text-text-muted hover:border-text/30 hover:text-text',
                                    )}
                                >
                                    {cat.name}
                                    <span className="ml-1.5 opacity-60">({cat.count})</span>
                                </button>
                            ))}
                        </div>
                    </FadeIn>
                )}

                {/* Makale grid */}
                {articles.data.length > 0 ? (
                    <div className="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                        {articles.data.map((article, i) => (
                            <FadeIn key={article.slug} delay={0.03 * i}>
                                <ArticleCard article={article} />
                            </FadeIn>
                        ))}
                    </div>
                ) : (
                    <div className="flex flex-col items-center justify-center rounded-lg border border-border bg-surface py-20 text-center">
                        <p className="text-text-muted">Bu kategoride henüz makale bulunmamaktadır.</p>
                    </div>
                )}

                {/* Pagination */}
                {articles.last_page > 1 && (
                    <div className="mt-12 flex items-center justify-center gap-2 border-t border-border pt-8">
                        {articles.links.map((link, i) => {
                            if (!link.url) {
                                return (
                                    <span
                                        key={i}
                                        className="px-3 py-2 text-sm text-text-muted/50"
                                        dangerouslySetInnerHTML={{ __html: link.label }}
                                    />
                                );
                            }
                            return (
                                <Link
                                    key={i}
                                    href={link.url}
                                    className={cn(
                                        'rounded-md px-3 py-2 text-sm transition-colors',
                                        link.active
                                            ? 'bg-primary text-primary-fg'
                                            : 'text-text-muted hover:bg-surface hover:text-text',
                                    )}
                                    dangerouslySetInnerHTML={{ __html: link.label }}
                                />
                            );
                        })}
                    </div>
                )}
            </Section>
        </MainLayout>
    );
}
