/// <reference types="vite/client" />

import type { PageProps as InertiaPageProps } from '@inertiajs/core';

export interface SiteSettings {
    name: string;
    tagline: string | null;
    phone: string | null;
    email: string | null;
    kep: string | null;
    address: string | null;
    map_embed_url: string | null;
    linkedin_url: string | null;
    logo_url: string | null;
}

export interface ThemeTokens {
    bg: string;
    surface: string;
    surfaceAlt: string;
    text: string;
    textMuted: string;
    border: string;
    primary: string;
    primaryFg: string;
    accent: string;
    fontHeading: string;
    fontBody: string;
}

export interface FooterService {
    slug: string;
    title: string;
}

export interface SharedProps extends InertiaPageProps {
    site: SiteSettings;
    theme: ThemeTokens;
    footerServices: FooterService[];
    flash: {
        success?: string;
        error?: string;
    };
}

declare module '@inertiajs/core' {
    interface PageProps extends SharedProps {}
}
