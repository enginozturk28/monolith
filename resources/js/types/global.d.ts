/// <reference types="vite/client" />

import type { PageProps as InertiaPageProps } from '@inertiajs/core';

export interface SiteSettings {
    // Kimlik
    name: string;
    tagline: string | null;
    footer_description: string | null;
    copyright_text: string | null;

    // İletişim
    phone: string | null;
    email: string | null;
    kep: string | null;
    address: string | null;
    map_embed_url: string | null;

    // Sosyal medya
    linkedin_url: string | null;
    whatsapp_url: string | null;
    instagram_url: string | null;
    x_url: string | null;

    // Görseller
    logo_url: string | null;
    hero_image_path: string | null;
    hero_image_credit: string | null;
    about_image_path: string | null;
    about_image_credit: string | null;

    // İçerik blokları (HTML)
    about_intro_body: string | null;
    founder_bio: string | null;
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

export interface TurnstileConfig {
    enabled: boolean;
    siteKey: string | null;
}

export interface SharedProps extends InertiaPageProps {
    site: SiteSettings;
    theme: ThemeTokens;
    footerServices: FooterService[];
    turnstile: TurnstileConfig;
    flash: {
        success?: string;
        error?: string;
    };
}

declare module '@inertiajs/core' {
    interface PageProps extends SharedProps {}
}
