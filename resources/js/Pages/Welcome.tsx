import { Head, usePage } from '@inertiajs/react';
import { ArrowRight } from 'lucide-react';
import MainLayout from '@/Layouts/MainLayout';
import Section from '@/Components/Layout/Section';
import FadeIn from '@/Components/Motion/FadeIn';
import type { SharedProps } from '@/types/global';

export default function Welcome() {
    const { site } = usePage<SharedProps>().props;

    return (
        <MainLayout>
            <Head title="Ana Sayfa" />

            {/* Hero — geçici placeholder, Faz 5'te asıl Home component'i ile değişecek */}
            <Section size="wide" className="pt-20 pb-24 sm:pt-28 sm:pb-32 lg:pt-32 lg:pb-40">
                <div className="grid gap-12 lg:grid-cols-12 lg:gap-16">
                    <div className="lg:col-span-8">
                        <FadeIn delay={0}>
                            <p className="text-xs font-semibold uppercase tracking-[0.2em] text-text-muted">
                                İstanbul · Kadıköy
                            </p>
                        </FadeIn>

                        <FadeIn delay={0.1}>
                            <h1
                                className="mt-6 max-w-3xl text-balance text-5xl leading-[1.05] text-text sm:text-6xl lg:text-7xl"
                                style={{ fontFamily: 'var(--font-heading)', fontWeight: 500 }}
                            >
                                Hukuki süreçlerinizde{' '}
                                <span style={{ color: 'var(--color-accent)' }}>güvenilir</span>{' '}
                                ve çözüm odaklı bir destek.
                            </h1>
                        </FadeIn>

                        <FadeIn delay={0.2}>
                            <p className="mt-8 max-w-2xl text-lg leading-relaxed text-text-muted">
                                {site.name ?? 'Loğoğlu Hukuk Bürosu'}, bireysel ve kurumsal
                                müvekkillerine etik ve şeffaf bir anlayışla hukuki danışmanlık
                                ve dava takibi hizmeti sunar. Her dosyayı titizlikle değerlendirir,
                                güncel mevzuat ve içtihat analizleri ışığında süreçleri yönetiriz.
                            </p>
                        </FadeIn>

                        <FadeIn delay={0.3}>
                            <div className="mt-10 flex flex-wrap items-center gap-4">
                                <a
                                    href="/iletisim"
                                    className="inline-flex items-center gap-2 rounded-md bg-primary px-5 py-3 text-sm font-medium text-primary-fg transition-opacity hover:opacity-90"
                                >
                                    Görüşme Talebi
                                    <ArrowRight className="h-4 w-4" />
                                </a>
                                <a
                                    href="/faaliyet-alanlari"
                                    className="inline-flex items-center gap-2 rounded-md border border-border px-5 py-3 text-sm font-medium text-text transition-colors hover:bg-surface"
                                >
                                    Faaliyet Alanları
                                </a>
                            </div>
                        </FadeIn>
                    </div>

                    {/* Sağ kolon — ince dikey vurgu */}
                    <div className="hidden lg:col-span-4 lg:block">
                        <FadeIn direction="left" delay={0.2}>
                            <div
                                className="h-full rounded-lg border border-border bg-surface p-8"
                                style={{ minHeight: '320px' }}
                            >
                                <p className="text-xs font-semibold uppercase tracking-[0.14em] text-text-muted">
                                    Kurucu Avukat
                                </p>
                                <h2
                                    className="mt-4 text-2xl leading-tight text-text"
                                    style={{ fontFamily: 'var(--font-heading)', fontWeight: 500 }}
                                >
                                    Ethem Kaan Loğoğlu
                                </h2>
                                <p className="mt-4 text-sm leading-relaxed text-text-muted">
                                    2023 yılında Atılım Üniversitesi Hukuk Fakültesi'nden mezun oldum.
                                    Stajyer avukatlık sürecimi tamamladıktan sonra büromuzu kurarak
                                    aktif olarak avukatlık yapmaktayım.
                                </p>
                                <div className="mt-8 border-t border-border pt-6">
                                    <p className="text-xs text-text-muted">
                                        Kuruluş <span className="text-text">2024</span>
                                    </p>
                                </div>
                            </div>
                        </FadeIn>
                    </div>
                </div>
            </Section>

            {/* Alt bilgi bandı — küçük bir teaser */}
            <Section tone="surface" size="wide" className="border-t border-border py-16">
                <div className="grid gap-8 sm:grid-cols-3">
                    <FadeIn delay={0}>
                        <div>
                            <p className="text-xs font-semibold uppercase tracking-[0.14em] text-text-muted">
                                01 — Değerlendirme
                            </p>
                            <p className="mt-3 text-sm leading-relaxed text-text">
                                Her dosya detaylı bir hukuki durum analizi ile ele alınır.
                            </p>
                        </div>
                    </FadeIn>
                    <FadeIn delay={0.1}>
                        <div>
                            <p className="text-xs font-semibold uppercase tracking-[0.14em] text-text-muted">
                                02 — Yönlendirme
                            </p>
                            <p className="mt-3 text-sm leading-relaxed text-text">
                                Müvekkile süreç, olası senaryolar ve maliyet hakkında şeffaf
                                bilgi verilir.
                            </p>
                        </div>
                    </FadeIn>
                    <FadeIn delay={0.2}>
                        <div>
                            <p className="text-xs font-semibold uppercase tracking-[0.14em] text-text-muted">
                                03 — Takip
                            </p>
                            <p className="mt-3 text-sm leading-relaxed text-text">
                                Dosyanın her aşaması düzenli olarak raporlanır ve iletişim
                                sürdürülür.
                            </p>
                        </div>
                    </FadeIn>
                </div>
            </Section>
        </MainLayout>
    );
}
