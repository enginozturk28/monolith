import { Link } from '@inertiajs/react';
import { ArrowLeft, ArrowRight } from 'lucide-react';
import MainLayout from '@/Layouts/MainLayout';
import Container from '@/Components/Layout/Container';
import Section from '@/Components/Layout/Section';
import RichTextContent from '@/Components/Content/RichTextContent';
import ServiceIcon from '@/Components/Content/ServiceIcon';
import FadeIn from '@/Components/Motion/FadeIn';
import SeoHead from '@/Components/Seo/SeoHead';

type Service = {
    slug: string;
    title: string;
    icon?: string | null;
    summary?: string | null;
    body?: string | null;
    meta_title?: string | null;
    meta_description?: string | null;
};

type Related = {
    slug: string;
    title: string;
    icon?: string | null;
};

type Props = {
    service: Service;
    related: Related[];
};

export default function ServiceShow({ service, related }: Props) {
    // schema.org Service structured data
    const serviceJsonLd = {
        '@context': 'https://schema.org',
        '@type': 'Service',
        name: service.title,
        description: service.summary,
        provider: {
            '@type': 'LegalService',
            name: 'Loğoğlu Hukuk Bürosu',
        },
        areaServed: {
            '@type': 'Country',
            name: 'Türkiye',
        },
    };

    return (
        <MainLayout>
            <SeoHead
                title={service.meta_title ?? service.title}
                description={service.meta_description ?? service.summary ?? undefined}
                jsonLd={serviceJsonLd}
            />

            {/* Başlık bloğu */}
            <header className="border-b border-border bg-surface-alt">
                <Container size="wide" className="py-16 sm:py-20">
                    <FadeIn>
                        <Link
                            href="/faaliyet-alanlari"
                            className="inline-flex items-center gap-1.5 text-xs font-medium uppercase tracking-[0.14em] text-text-muted hover:text-text"
                        >
                            <ArrowLeft className="h-3 w-3" />
                            Faaliyet Alanları
                        </Link>
                    </FadeIn>

                    <FadeIn delay={0.05}>
                        <div className="mt-8 flex items-start gap-6">
                            <div
                                className="flex h-14 w-14 shrink-0 items-center justify-center rounded-lg"
                                style={{
                                    backgroundColor: 'color-mix(in srgb, var(--color-primary) 8%, transparent)',
                                    color: 'var(--color-primary)',
                                }}
                            >
                                <ServiceIcon name={service.icon} className="h-7 w-7" />
                            </div>

                            <div className="flex-1">
                                <h1
                                    className="text-4xl leading-tight text-text sm:text-5xl lg:text-6xl"
                                    style={{ fontFamily: 'var(--font-heading)', fontWeight: 500 }}
                                >
                                    {service.title}
                                </h1>
                                {service.summary && (
                                    <p className="mt-6 max-w-2xl text-lg leading-relaxed text-text-muted">
                                        {service.summary}
                                    </p>
                                )}
                            </div>
                        </div>
                    </FadeIn>
                </Container>
            </header>

            {/* İçerik */}
            <Section size="wide">
                <div className="grid gap-12 lg:grid-cols-12 lg:gap-16">
                    {/* Ana içerik */}
                    <article className="lg:col-span-8">
                        <FadeIn>
                            <RichTextContent
                                html={service.body}
                                fallback={
                                    <p className="text-text-muted">
                                        Bu alana ilişkin detaylı bilgi için iletişime geçebilirsiniz.
                                    </p>
                                }
                            />
                        </FadeIn>

                        <FadeIn delay={0.15}>
                            <div className="mt-12 rounded-lg border border-border bg-surface p-8">
                                <h3
                                    className="text-xl text-text"
                                    style={{ fontFamily: 'var(--font-heading)', fontWeight: 500 }}
                                >
                                    Bu alanda bir hukuki talebiniz mi var?
                                </h3>
                                <p className="mt-3 text-sm leading-relaxed text-text-muted">
                                    Dosyanız hakkında görüşme için randevu talebi iletebilirsiniz.
                                    En kısa sürede tarafınıza dönüş sağlanacaktır.
                                </p>
                                <Link
                                    href="/iletisim"
                                    className="mt-6 inline-flex items-center gap-2 rounded-md bg-primary px-5 py-3 text-sm font-medium text-primary-fg transition-opacity hover:opacity-90"
                                >
                                    Görüşme Talebi
                                    <ArrowRight className="h-4 w-4" />
                                </Link>
                            </div>
                        </FadeIn>
                    </article>

                    {/* Sidebar: diğer alanlar */}
                    {related.length > 0 && (
                        <aside className="lg:col-span-4">
                            <FadeIn direction="left">
                                <div className="sticky top-28">
                                    <p className="text-xs font-semibold uppercase tracking-[0.14em] text-text-muted">
                                        Diğer Alanlar
                                    </p>
                                    <ul className="mt-6 space-y-1 border-t border-border">
                                        {related.map((item) => (
                                            <li key={item.slug} className="border-b border-border">
                                                <Link
                                                    href={`/faaliyet-alanlari/${item.slug}`}
                                                    className="flex items-center gap-3 py-3.5 text-sm text-text-muted transition-colors hover:text-text"
                                                >
                                                    <ServiceIcon name={item.icon} className="h-4 w-4 text-text-muted" />
                                                    <span className="flex-1">{item.title}</span>
                                                    <ArrowRight className="h-3.5 w-3.5 opacity-0 transition-opacity group-hover:opacity-100" />
                                                </Link>
                                            </li>
                                        ))}
                                    </ul>
                                </div>
                            </FadeIn>
                        </aside>
                    )}
                </div>
            </Section>
        </MainLayout>
    );
}
