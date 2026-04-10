import { Head, usePage } from '@inertiajs/react';
import type { SharedProps } from '@/types/global';

export default function Welcome() {
    const { site, theme } = usePage<SharedProps>().props;

    return (
        <>
            <Head title="Hoş geldiniz" />

            <main className="min-h-dvh bg-bg text-text">
                <div className="mx-auto max-w-3xl px-6 py-24 sm:py-32">
                    <p className="text-xs uppercase tracking-[0.2em] text-text-muted">
                        Monolith · İskelet doğrulama
                    </p>

                    <h1 className="mt-6 font-heading text-5xl leading-[1.1] sm:text-6xl">
                        {site.name}
                    </h1>

                    {site.tagline && (
                        <p className="mt-6 max-w-xl text-lg text-text-muted">
                            {site.tagline}
                        </p>
                    )}

                    <div className="mt-12 grid gap-6 sm:grid-cols-2">
                        <div className="rounded-lg border border-border bg-surface p-6">
                            <h2 className="font-heading text-xl">Backend</h2>
                            <ul className="mt-3 space-y-1 text-sm text-text-muted">
                                <li>Laravel 13 · PHP 8.4</li>
                                <li>Filament v5 · MySQL 8.4</li>
                                <li>Inertia v3</li>
                            </ul>
                        </div>

                        <div className="rounded-lg border border-border bg-surface p-6">
                            <h2 className="font-heading text-xl">Frontend</h2>
                            <ul className="mt-3 space-y-1 text-sm text-text-muted">
                                <li>React 19 · TypeScript 6</li>
                                <li>Tailwind CSS 4 · Vite 8</li>
                                <li>Motion 12 · Lenis</li>
                            </ul>
                        </div>
                    </div>

                    <div className="mt-12 flex items-center gap-2 border-t border-border pt-8">
                        <span
                            className="inline-block h-3 w-3 rounded-full"
                            style={{ backgroundColor: theme.primary ?? '#0B1F3A' }}
                            aria-hidden
                        />
                        <span
                            className="inline-block h-3 w-3 rounded-full"
                            style={{ backgroundColor: theme.accent ?? '#A88A55' }}
                            aria-hidden
                        />
                        <span
                            className="inline-block h-3 w-3 rounded-full border border-border"
                            style={{ backgroundColor: theme.bg ?? '#F5F1EA' }}
                            aria-hidden
                        />
                        <p className="ml-3 text-xs text-text-muted">
                            Dinamik tema sistemi aktif — admin panelden değiştirilebilir.
                        </p>
                    </div>
                </div>
            </main>
        </>
    );
}
