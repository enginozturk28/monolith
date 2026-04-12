import { Link } from '@inertiajs/react';
import { ArrowLeft, Home } from 'lucide-react';
import MainLayout from '@/Layouts/MainLayout';
import Container from '@/Components/Layout/Container';
import SeoHead from '@/Components/Seo/SeoHead';

type Props = {
    status: number;
};

const MESSAGES: Record<number, { title: string; description: string }> = {
    403: {
        title: 'Erişim Engellendi',
        description: 'Bu sayfaya erişim yetkiniz bulunmuyor.',
    },
    404: {
        title: 'Sayfa Bulunamadı',
        description: 'Aradığınız sayfa kaldırılmış, adı değiştirilmiş veya geçici olarak kullanılamıyor olabilir.',
    },
    500: {
        title: 'Sunucu Hatası',
        description: 'Bir teknik sorun oluştu. Lütfen daha sonra tekrar deneyiniz. Sorun devam ederse iletişim sayfamızdan bize ulaşabilirsiniz.',
    },
    503: {
        title: 'Bakım Modu',
        description: 'Sitemiz şu anda bakım çalışması nedeniyle geçici olarak kullanılamıyor. Kısa süre içinde tekrar erişime açılacaktır.',
    },
};

export default function Error({ status }: Props) {
    const message = MESSAGES[status] ?? MESSAGES[404];

    return (
        <MainLayout>
            <SeoHead title={message.title} />

            <Container size="default" className="flex min-h-[60vh] flex-col items-center justify-center py-24 text-center">
                <p
                    className="text-8xl font-medium sm:text-9xl"
                    style={{
                        fontFamily: 'var(--font-heading)',
                        color: 'var(--color-accent)',
                        lineHeight: 1,
                    }}
                >
                    {status}
                </p>

                <h1
                    className="mt-6 text-3xl text-text sm:text-4xl"
                    style={{ fontFamily: 'var(--font-heading)', fontWeight: 500 }}
                >
                    {message.title}
                </h1>

                <p className="mt-4 max-w-md text-base leading-relaxed text-text-muted">
                    {message.description}
                </p>

                <div className="mt-10 flex flex-wrap items-center justify-center gap-4">
                    <Link
                        href="/"
                        className="inline-flex items-center gap-2 rounded-md bg-primary px-5 py-3 text-sm font-medium text-primary-fg transition-opacity hover:opacity-90"
                    >
                        <Home className="h-4 w-4" />
                        Ana Sayfa
                    </Link>
                    <button
                        type="button"
                        onClick={() => window.history.back()}
                        className="inline-flex items-center gap-2 rounded-md border border-border px-5 py-3 text-sm font-medium text-text transition-colors hover:bg-surface"
                    >
                        <ArrowLeft className="h-4 w-4" />
                        Geri Dön
                    </button>
                </div>
            </Container>
        </MainLayout>
    );
}
