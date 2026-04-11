import { Head, usePage } from '@inertiajs/react';
import MainLayout from '@/Layouts/MainLayout';
import Container from '@/Components/Layout/Container';
import Section from '@/Components/Layout/Section';
import PageHeader from '@/Components/Layout/PageHeader';
import RichTextContent from '@/Components/Content/RichTextContent';
import FadeIn from '@/Components/Motion/FadeIn';
import type { SharedProps } from '@/types/global';

type Props = {
    vision: { title: string; body: string } | null;
    mission: { title: string; body: string } | null;
};

export default function About({ vision, mission }: Props) {
    const { site } = usePage<SharedProps>().props;

    return (
        <MainLayout>
            <Head title="Hakkımızda" />

            <PageHeader
                title="Bireysel ve kurumsal müvekkillere etik hukuki destek"
                description={`2024 yılında kurulan ${site.name ?? 'Loğoğlu Hukuk Bürosu'}, İstanbul merkezli olarak tüm Türkiye'ye yayılmış bir hizmet anlayışıyla faaliyet göstermektedir.`}
            />

            {/* Büro tanımı */}
            <Section size="wide">
                <div className="grid gap-12 lg:grid-cols-12 lg:gap-16">
                    {/* Sol: mimari görsel — uzun dikey formatta kırpılır */}
                    {site.about_image_path && (
                        <div className="lg:col-span-5">
                            <FadeIn>
                                <div className="overflow-hidden rounded-lg border border-border bg-surface-alt">
                                    <img
                                        src={site.about_image_path}
                                        alt=""
                                        aria-hidden
                                        loading="lazy"
                                        className="h-full w-full object-cover"
                                        style={{ aspectRatio: '4 / 5', minHeight: '480px' }}
                                    />
                                </div>
                            </FadeIn>
                        </div>
                    )}

                    {/* Sağ: büro tanıtım metni — panelden RichText */}
                    <div
                        className={
                            site.about_image_path
                                ? 'lg:col-span-7'
                                : 'lg:col-span-12'
                        }
                    >
                        <FadeIn delay={0.1}>
                            {site.about_intro_body ? (
                                <RichTextContent
                                    html={site.about_intro_body}
                                    className="lg:text-lg"
                                />
                            ) : (
                                <div className="space-y-6 text-base leading-relaxed text-text-muted lg:text-lg">
                                    <p>
                                        {site.name ?? 'Loğoğlu Hukuk Bürosu'}, bireysel ve
                                        kurumsal müvekkillerine güvenilir, etik ve çözüm odaklı
                                        hukuki hizmetler sunmayı amaçlamaktadır.
                                    </p>
                                </div>
                            )}
                        </FadeIn>
                    </div>
                </div>
            </Section>

            {/* Vizyon + Misyon */}
            {(vision || mission) && (
                <Section tone="surface" size="wide" className="border-y border-border">
                    <div className="grid gap-12 lg:grid-cols-2 lg:gap-16">
                        {vision && (
                            <FadeIn>
                                <div>
                                    <h3
                                        className="text-3xl leading-tight text-text"
                                        style={{ fontFamily: 'var(--font-heading)', fontWeight: 500 }}
                                    >
                                        {vision.title}
                                    </h3>
                                    <div className="mt-6">
                                        <RichTextContent html={vision.body} />
                                    </div>
                                </div>
                            </FadeIn>
                        )}

                        {mission && (
                            <FadeIn delay={0.15}>
                                <div>
                                    <h3
                                        className="text-3xl leading-tight text-text"
                                        style={{ fontFamily: 'var(--font-heading)', fontWeight: 500 }}
                                    >
                                        {mission.title}
                                    </h3>
                                    <div className="mt-6">
                                        <RichTextContent html={mission.body} />
                                    </div>
                                </div>
                            </FadeIn>
                        )}
                    </div>
                </Section>
            )}

            {/* Kurucu avukat */}
            <Section size="wide">
                <Container size="narrow" className="px-0">
                    <FadeIn>
                        <h2
                            className="text-4xl leading-tight text-text sm:text-5xl"
                            style={{ fontFamily: 'var(--font-heading)', fontWeight: 500 }}
                        >
                            Avukat Ethem Kaan Loğoğlu
                        </h2>
                        <p className="mt-3 text-sm text-text-muted">Kurucu</p>
                    </FadeIn>

                    <FadeIn delay={0.1}>
                        <div className="mt-8">
                            {site.founder_bio ? (
                                <RichTextContent html={site.founder_bio} />
                            ) : (
                                <p className="text-base leading-relaxed text-text-muted">
                                    Kurucu avukat biyografisi panelden yüklenmemiş.
                                </p>
                            )}
                        </div>
                    </FadeIn>
                </Container>
            </Section>
        </MainLayout>
    );
}
