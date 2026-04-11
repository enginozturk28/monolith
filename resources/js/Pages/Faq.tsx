import { useState } from 'react';
import { Head } from '@inertiajs/react';
import { ChevronDown } from 'lucide-react';
import { AnimatePresence, motion } from 'motion/react';
import MainLayout from '@/Layouts/MainLayout';
import Container from '@/Components/Layout/Container';
import Section from '@/Components/Layout/Section';
import PageHeader from '@/Components/Layout/PageHeader';
import RichTextContent from '@/Components/Content/RichTextContent';
import FadeIn from '@/Components/Motion/FadeIn';
import { cn } from '@/lib/utils';

type Faq = {
    id: number;
    question: string;
    answer: string;
};

type FaqCategory = {
    slug: string;
    name: string;
    icon?: string | null;
    description?: string | null;
    faqs: Faq[];
};

type Props = {
    categories: FaqCategory[];
};

export default function FaqPage({ categories }: Props) {
    const [open, setOpen] = useState<Record<number, boolean>>({});

    const toggle = (id: number) => {
        setOpen((prev) => ({ ...prev, [id]: !prev[id] }));
    };

    return (
        <MainLayout>
            <Head title="Sıkça Sorulan Sorular" />

            <PageHeader
                title="Sıkça Sorulan Sorular"
                description="Sıkça sorulan sorulara hazırladığımız yanıtlar bilgilendirme amaçlıdır ve kişiye özel hukuki görüş niteliği taşımaz. Dosyanıza özgü soruların için iletişime geçebilirsiniz."
            />

            <Section size="wide">
                <Container size="default" className="px-0">
                    {categories.length === 0 ? (
                        <div className="rounded-lg border border-border bg-surface py-16 text-center">
                            <p className="text-text-muted">Henüz eklenmiş bir soru bulunmamaktadır.</p>
                        </div>
                    ) : (
                        <div className="space-y-16">
                            {categories.map((category, catIdx) => (
                                <FadeIn key={category.slug} delay={0.05 * catIdx}>
                                    <div>
                                        <div className="mb-6 flex items-baseline gap-3 border-b border-border pb-4">
                                            <h2
                                                className="text-2xl leading-tight text-text sm:text-3xl"
                                                style={{ fontFamily: 'var(--font-heading)', fontWeight: 500 }}
                                            >
                                                {category.name}
                                            </h2>
                                            <span className="text-sm text-text-muted">
                                                {category.faqs.length} soru
                                            </span>
                                        </div>

                                        {category.description && (
                                            <p className="mb-6 max-w-2xl text-sm leading-relaxed text-text-muted">
                                                {category.description}
                                            </p>
                                        )}

                                        <div className="divide-y divide-border rounded-lg border border-border bg-surface">
                                            {category.faqs.map((faq) => {
                                                const isOpen = open[faq.id] ?? false;
                                                return (
                                                    <div key={faq.id}>
                                                        <button
                                                            type="button"
                                                            onClick={() => toggle(faq.id)}
                                                            aria-expanded={isOpen}
                                                            aria-controls={`faq-${faq.id}`}
                                                            className="flex w-full items-center justify-between gap-6 px-6 py-5 text-left transition-colors hover:bg-surface-alt/50"
                                                        >
                                                            <span className="text-base font-medium leading-snug text-text sm:text-lg">
                                                                {faq.question}
                                                            </span>
                                                            <ChevronDown
                                                                className={cn(
                                                                    'h-5 w-5 shrink-0 text-text-muted transition-transform duration-300',
                                                                    isOpen && 'rotate-180',
                                                                )}
                                                                aria-hidden
                                                            />
                                                        </button>
                                                        <AnimatePresence initial={false}>
                                                            {isOpen && (
                                                                <motion.div
                                                                    key="answer"
                                                                    id={`faq-${faq.id}`}
                                                                    initial={{ height: 0, opacity: 0 }}
                                                                    animate={{ height: 'auto', opacity: 1 }}
                                                                    exit={{ height: 0, opacity: 0 }}
                                                                    transition={{ duration: 0.3, ease: [0.25, 0.1, 0.25, 1] }}
                                                                    className="overflow-hidden"
                                                                >
                                                                    <div className="px-6 pb-6">
                                                                        <RichTextContent
                                                                            html={faq.answer}
                                                                            className="text-[15px]"
                                                                        />
                                                                    </div>
                                                                </motion.div>
                                                            )}
                                                        </AnimatePresence>
                                                    </div>
                                                );
                                            })}
                                        </div>
                                    </div>
                                </FadeIn>
                            ))}
                        </div>
                    )}
                </Container>
            </Section>
        </MainLayout>
    );
}
