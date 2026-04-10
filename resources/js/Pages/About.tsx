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
                eyebrow="Büromuz"
                title="Bireysel ve kurumsal müvekkillere etik hukuki destek"
                description={`2024 yılında kurulan ${site.name ?? 'Loğoğlu Hukuk Bürosu'}, İstanbul merkezli olarak tüm Türkiye'ye yayılmış bir hizmet anlayışıyla faaliyet göstermektedir.`}
            />

            {/* Büro tanımı */}
            <Section size="wide">
                <div className="grid gap-16 lg:grid-cols-12 lg:gap-20">
                    <div className="lg:col-span-5">
                        <FadeIn>
                            <p className="text-xs font-semibold uppercase tracking-[0.2em] text-text-muted">
                                Büro Tanıtımı
                            </p>
                            <h2
                                className="mt-4 text-3xl leading-tight text-text sm:text-4xl"
                                style={{ fontFamily: 'var(--font-heading)', fontWeight: 500 }}
                            >
                                Butik bir hukuk bürosu
                            </h2>
                        </FadeIn>
                    </div>
                    <div className="lg:col-span-7">
                        <FadeIn delay={0.1}>
                            <div className="space-y-6 text-base leading-relaxed text-text-muted">
                                <p>
                                    2024 yılında Avukat Ethem Kaan Loğoğlu tarafından kurulan
                                    Loğoğlu Hukuk Bürosu, bireysel ve kurumsal müvekkillerine
                                    güvenilir, etik ve çözüm odaklı hukuki hizmetler sunmayı
                                    amaçlamaktadır.
                                </p>
                                <p>
                                    İstanbul merkezli olan büromuz, yalnızca şehir içinde değil,
                                    tüm Türkiye'ye yayılmış bir hizmet anlayışıyla hukukun farklı
                                    alanlarında profesyonel destek sağlamaktadır. Her dosyayı
                                    detaylı bir analizle ele alıyor; şeffaf, anlaşılır ve çözüm
                                    odaklı bir yaklaşım benimsiyoruz.
                                </p>
                                <p>
                                    Hukukun karmaşıklığı içinde yalnızca teknik bilgiye değil,
                                    aynı zamanda güçlü bir iletişim ve güven ilişkisine de önem
                                    veriyoruz.
                                </p>
                            </div>
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
                                    <p className="text-xs font-semibold uppercase tracking-[0.2em] text-accent">
                                        Vizyon
                                    </p>
                                    <h3
                                        className="mt-4 text-3xl leading-tight text-text"
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
                                    <p className="text-xs font-semibold uppercase tracking-[0.2em] text-accent">
                                        Misyon
                                    </p>
                                    <h3
                                        className="mt-4 text-3xl leading-tight text-text"
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
                        <p className="text-xs font-semibold uppercase tracking-[0.2em] text-text-muted">
                            Kurucu Avukat
                        </p>
                        <h2
                            className="mt-4 text-4xl leading-tight text-text sm:text-5xl"
                            style={{ fontFamily: 'var(--font-heading)', fontWeight: 500 }}
                        >
                            Ethem Kaan Loğoğlu
                        </h2>
                    </FadeIn>

                    <FadeIn delay={0.1}>
                        <div className="mt-8 space-y-6 text-base leading-relaxed text-text-muted">
                            <p>
                                2023 yılında Atılım Üniversitesi Hukuk Fakültesi'nden mezun oldum.
                                Stajyer avukatlık sürecimi tamamladıktan sonra, Loğoğlu Hukuk
                                Bürosu'nu kurarak aktif olarak avukatlık yapmaktayım.
                            </p>
                            <p>
                                Akademik bilgi birikimimi pratik uygulamalarla harmanlayarak
                                mevzuat ve içtihat analizi odaklı çalışmalar yürütüyorum. Amacım,
                                müvekkillerime güvenilir ve etkili çözümler sunmaktır.
                            </p>
                        </div>
                    </FadeIn>
                </Container>
            </Section>
        </MainLayout>
    );
}
