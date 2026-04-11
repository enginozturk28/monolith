import { Link, usePage } from '@inertiajs/react';
import { ArrowRight, MapPin, Phone } from 'lucide-react';
import MainLayout from '@/Layouts/MainLayout';
import Container from '@/Components/Layout/Container';
import Section from '@/Components/Layout/Section';
import FadeIn from '@/Components/Motion/FadeIn';
import ServiceCard from '@/Components/Content/ServiceCard';
import ArticleCard from '@/Components/Content/ArticleCard';
import SeoHead from '@/Components/Seo/SeoHead';
import type { SharedProps } from '@/types/global';

type Service = {
    slug: string;
    title: string;
    icon?: string | null;
    summary?: string | null;
};

type Article = {
    slug: string;
    title: string;
    excerpt?: string | null;
    published_at?: string | null;
    reading_time_minutes?: number | null;
    category?: { slug: string; name: string } | null;
};

type Props = {
    services: Service[];
    articles: Article[];
};

export default function Home({ services, articles }: Props) {
    const { site } = usePage<SharedProps>().props;
    const heroImage = site.hero_image_path;

    return (
        <MainLayout>
            <SeoHead
                title="Ana Sayfa"
                description={site.tagline ?? `${site.name ?? 'Loğoğlu Hukuk Bürosu'}, bireysel ve kurumsal müvekkillere etik ve şeffaf bir anlayışla hukuki danışmanlık ve dava takibi hizmeti sunar.`}
            />

            {/* HERO — arka planda ofis görseli, üzerinde sade bir okunabilirlik katmanı */}
            <div className="relative isolate overflow-hidden bg-bg">
                {heroImage && (
                    <>
                        <img
                            src={heroImage}
                            alt=""
                            aria-hidden
                            className="absolute inset-0 -z-10 h-full w-full object-cover"
                            fetchPriority="high"
                            loading="eager"
                        />
                        {/* Düz bir okunabilirlik katmanı — gradyan değil. */}
                        <div
                            aria-hidden
                            className="absolute inset-0 -z-10"
                            style={{
                                backgroundColor: 'var(--color-bg)',
                                opacity: 0.78,
                            }}
                        />
                    </>
                )}

                <Container size="wide" className="relative py-24 sm:py-32 lg:py-40">
                    <div className="grid gap-16 lg:grid-cols-12 lg:gap-20">
                        <div className="lg:col-span-8">
                            <FadeIn>
                                <h1
                                    className="max-w-4xl text-balance text-5xl leading-[1.02] text-text sm:text-6xl lg:text-7xl"
                                    style={{ fontFamily: 'var(--font-heading)', fontWeight: 500 }}
                                >
                                    Hukuki süreçlerinizde{' '}
                                    <span style={{ color: 'var(--color-accent)', fontStyle: 'italic' }}>
                                        güvenilir
                                    </span>{' '}
                                    ve çözüm odaklı bir yaklaşım.
                                </h1>
                            </FadeIn>

                            <FadeIn delay={0.1}>
                                <p className="mt-10 max-w-2xl text-lg leading-relaxed text-text-muted sm:text-xl">
                                    {site.name ?? 'Loğoğlu Hukuk Bürosu'}, bireysel ve kurumsal
                                    müvekkillerine etik ve şeffaf bir anlayışla hukuki danışmanlık
                                    ve dava takibi hizmeti sunar. Her dosyayı titizlikle değerlendirir,
                                    güncel mevzuat ve içtihat analizleri ışığında süreçleri yönetiriz.
                                </p>
                            </FadeIn>

                            <FadeIn delay={0.18}>
                                <div className="mt-12 flex flex-wrap items-center gap-4">
                                    <Link
                                        href="/iletisim"
                                        className="inline-flex items-center gap-2 rounded-md bg-primary px-6 py-3.5 text-sm font-medium text-primary-fg shadow-sm transition-opacity hover:opacity-90"
                                    >
                                        Görüşme Talebi
                                        <ArrowRight className="h-4 w-4" />
                                    </Link>
                                    <Link
                                        href="/faaliyet-alanlari"
                                        className="inline-flex items-center gap-2 rounded-md border border-border bg-bg/80 px-6 py-3.5 text-sm font-medium text-text backdrop-blur-sm transition-colors hover:bg-surface"
                                    >
                                        Faaliyet Alanları
                                    </Link>
                                </div>
                            </FadeIn>
                        </div>

                        {/* Sağ kolon: kurucu özet kart */}
                        <div className="hidden lg:col-span-4 lg:block">
                            <FadeIn direction="left" delay={0.15}>
                                <div className="flex h-full flex-col rounded-lg border border-border bg-surface/95 p-8 backdrop-blur-sm">
                                    <h2
                                        className="text-2xl leading-tight text-text"
                                        style={{ fontFamily: 'var(--font-heading)', fontWeight: 500 }}
                                    >
                                        Avukat Ethem Kaan Loğoğlu
                                    </h2>
                                    <p className="mt-2 text-xs text-text-muted">Kurucu</p>
                                    <p className="mt-6 text-sm leading-relaxed text-text-muted">
                                        2023 yılında Atılım Üniversitesi Hukuk Fakültesi'nden mezun oldum.
                                        Büromuzu kurarak bireysel ve kurumsal müvekkillere aktif olarak
                                        hukuki destek sunmaktayım.
                                    </p>
                                    <div className="mt-6 border-t border-border pt-6">
                                        <ul className="space-y-3 text-sm">
                                            <li className="flex items-start gap-2 text-text-muted">
                                                <MapPin className="mt-0.5 h-4 w-4 shrink-0 text-accent" />
                                                <span className="leading-relaxed">Kadıköy, İstanbul</span>
                                            </li>
                                            {site.phone && (
                                                <li className="flex items-start gap-2 text-text-muted">
                                                    <Phone className="mt-0.5 h-4 w-4 shrink-0 text-accent" />
                                                    <a
                                                        href={`tel:${site.phone.replace(/\s/g, '')}`}
                                                        className="hover:text-text"
                                                    >
                                                        {site.phone}
                                                    </a>
                                                </li>
                                            )}
                                        </ul>
                                    </div>
                                    <Link
                                        href="/hakkimizda"
                                        className="mt-auto inline-flex items-center gap-1.5 pt-8 text-xs font-medium text-accent hover:opacity-80"
                                    >
                                        Hakkımızda daha fazlası
                                        <ArrowRight className="h-3 w-3" />
                                    </Link>
                                </div>
                            </FadeIn>
                        </div>
                    </div>

                </Container>
            </div>

            {/* FAALİYET ALANLARI */}
            <Section tone="surface" size="wide" className="border-t border-border">
                <div className="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                    <FadeIn>
                        <h2
                            className="max-w-2xl text-balance text-3xl leading-tight text-text sm:text-4xl lg:text-5xl"
                            style={{ fontFamily: 'var(--font-heading)', fontWeight: 500 }}
                        >
                            Hukukun farklı alanlarında profesyonel destek
                        </h2>
                    </FadeIn>
                    <FadeIn delay={0.1}>
                        <Link
                            href="/faaliyet-alanlari"
                            className="group inline-flex items-center gap-1.5 text-sm font-medium text-text"
                        >
                            Tüm alanlar
                            <ArrowRight className="h-4 w-4 transition-transform group-hover:translate-x-0.5" />
                        </Link>
                    </FadeIn>
                </div>

                <div className="mt-12 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                    {services.slice(0, 6).map((service, i) => (
                        <FadeIn key={service.slug} delay={0.05 * i}>
                            <ServiceCard {...service} />
                        </FadeIn>
                    ))}
                </div>

                {services.length > 6 && (
                    <FadeIn delay={0.2}>
                        <div className="mt-10 flex justify-center">
                            <Link
                                href="/faaliyet-alanlari"
                                className="inline-flex items-center gap-2 rounded-md border border-border bg-bg px-6 py-3 text-sm font-medium text-text transition-colors hover:bg-surface-alt"
                            >
                                12 faaliyet alanının tamamını görün
                                <ArrowRight className="h-4 w-4" />
                            </Link>
                        </div>
                    </FadeIn>
                )}
            </Section>

            {/* SON MAKALELER */}
            {articles.length > 0 && (
                <Section size="wide">
                    <div className="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                        <FadeIn>
                            <h2
                                className="max-w-2xl text-balance text-3xl leading-tight text-text sm:text-4xl lg:text-5xl"
                                style={{ fontFamily: 'var(--font-heading)', fontWeight: 500 }}
                            >
                                Güncel hukuki değerlendirmeler
                            </h2>
                        </FadeIn>
                        <FadeIn delay={0.1}>
                            <Link
                                href="/makaleler"
                                className="group inline-flex items-center gap-1.5 text-sm font-medium text-text"
                            >
                                Tüm makaleler
                                <ArrowRight className="h-4 w-4 transition-transform group-hover:translate-x-0.5" />
                            </Link>
                        </FadeIn>
                    </div>

                    <div className="mt-12 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                        {articles.map((article, i) => (
                            <FadeIn key={article.slug} delay={0.05 * i}>
                                <ArticleCard article={article} />
                            </FadeIn>
                        ))}
                    </div>
                </Section>
            )}

            {/* 3 ADIM */}
            <Section tone="accent" size="wide" className="border-y border-border">
                <FadeIn>
                    <h2
                        className="max-w-2xl text-balance text-3xl leading-tight text-text sm:text-4xl"
                        style={{ fontFamily: 'var(--font-heading)', fontWeight: 500 }}
                    >
                        Her dosya, aynı titizlikle yönetilir
                    </h2>
                </FadeIn>

                <div className="mt-12 grid gap-8 sm:grid-cols-3">
                    {[
                        {
                            step: '01',
                            title: 'Değerlendirme',
                            text: 'Her dosya detaylı bir hukuki durum analizi ile ele alınır. İlk görüşmede müvekkilin beklentisi ve dosyanın niteliği netleştirilir.',
                        },
                        {
                            step: '02',
                            title: 'Yönlendirme',
                            text: 'Müvekkile süreç, olası senaryolar, alternatif çözümler ve tahmini maliyet hakkında şeffaf bilgi sunulur.',
                        },
                        {
                            step: '03',
                            title: 'Takip',
                            text: 'Dosyanın her aşaması düzenli olarak raporlanır. Acil gelişmelerde müvekkille anında iletişim kurulur.',
                        },
                    ].map((item, i) => (
                        <FadeIn key={item.step} delay={0.08 * i}>
                            <div>
                                <p
                                    className="text-5xl font-medium leading-none"
                                    style={{
                                        fontFamily: 'var(--font-heading)',
                                        color: 'var(--color-accent)',
                                    }}
                                >
                                    {item.step}
                                </p>
                                <h3
                                    className="mt-6 text-xl text-text"
                                    style={{ fontFamily: 'var(--font-heading)', fontWeight: 500 }}
                                >
                                    {item.title}
                                </h3>
                                <p className="mt-4 text-sm leading-relaxed text-text-muted">
                                    {item.text}
                                </p>
                            </div>
                        </FadeIn>
                    ))}
                </div>
            </Section>

            {/* İletişim CTA */}
            <Section size="wide">
                <FadeIn>
                    <div className="rounded-xl border border-border bg-surface p-10 sm:p-14 lg:p-20">
                        <div className="grid gap-10 lg:grid-cols-12 lg:items-center">
                            <div className="lg:col-span-7">
                                <h2
                                    className="text-balance text-3xl leading-tight text-text sm:text-4xl"
                                    style={{ fontFamily: 'var(--font-heading)', fontWeight: 500 }}
                                >
                                    Hukuki durumunuz hakkında görüşme için randevu talebi iletebilirsiniz.
                                </h2>
                                <p className="mt-6 max-w-xl text-base leading-relaxed text-text-muted">
                                    Form aracılığıyla kısa bir mesaj bırakmanız durumunda en kısa
                                    sürede dönüş sağlanır. Dilerseniz doğrudan telefon ya da e-posta
                                    ile de iletişim kurabilirsiniz.
                                </p>
                            </div>
                            <div className="lg:col-span-5">
                                <div className="flex flex-col gap-4">
                                    <Link
                                        href="/iletisim"
                                        className="inline-flex items-center justify-center gap-2 rounded-md bg-primary px-6 py-4 text-sm font-medium text-primary-fg transition-opacity hover:opacity-90"
                                    >
                                        Görüşme Talebi Gönder
                                        <ArrowRight className="h-4 w-4" />
                                    </Link>
                                    {site.phone && (
                                        <a
                                            href={`tel:${site.phone.replace(/\s/g, '')}`}
                                            className="inline-flex items-center justify-center gap-2 rounded-md border border-border px-6 py-4 text-sm font-medium text-text transition-colors hover:bg-surface-alt"
                                        >
                                            <Phone className="h-4 w-4" />
                                            {site.phone}
                                        </a>
                                    )}
                                </div>
                            </div>
                        </div>
                    </div>
                </FadeIn>
            </Section>
        </MainLayout>
    );
}
