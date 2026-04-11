import MainLayout from '@/Layouts/MainLayout';
import Container from '@/Components/Layout/Container';
import Section from '@/Components/Layout/Section';
import RichTextContent from '@/Components/Content/RichTextContent';
import FadeIn from '@/Components/Motion/FadeIn';
import SeoHead from '@/Components/Seo/SeoHead';

type Props = {
    page: {
        slug: string;
        title: string;
        body?: string | null;
        meta_title?: string | null;
        meta_description?: string | null;
        updated_at?: string | null;
    };
};

const DATE_FORMAT = new Intl.DateTimeFormat('tr-TR', {
    day: 'numeric',
    month: 'long',
    year: 'numeric',
});

export default function PageShow({ page }: Props) {
    const updated = page.updated_at
        ? DATE_FORMAT.format(new Date(page.updated_at))
        : null;

    return (
        <MainLayout>
            <SeoHead
                title={page.meta_title ?? page.title}
                description={page.meta_description ?? undefined}
            />

            <header className="border-b border-border bg-surface-alt">
                <Container size="narrow" className="py-16 sm:py-20 lg:py-24">
                    <FadeIn>
                        <h1
                            className="text-balance text-4xl leading-[1.15] text-text sm:text-5xl"
                            style={{ fontFamily: 'var(--font-heading)', fontWeight: 500 }}
                        >
                            {page.title}
                        </h1>
                    </FadeIn>
                    {updated && (
                        <FadeIn delay={0.08}>
                            <p className="mt-6 text-xs font-medium uppercase tracking-[0.14em] text-text-muted">
                                Son güncelleme: {updated}
                            </p>
                        </FadeIn>
                    )}
                </Container>
            </header>

            <Section size="wide">
                <Container size="narrow" className="px-0">
                    <FadeIn>
                        <RichTextContent html={page.body} />
                    </FadeIn>
                </Container>
            </Section>
        </MainLayout>
    );
}
