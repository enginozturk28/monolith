import { clsx, type ClassValue } from 'clsx';
import { twMerge } from 'tailwind-merge';

/**
 * `cn` — Tailwind class merge yardımcısı.
 *
 * clsx ile koşullu class birleştirmesi + tailwind-merge ile çakışan utility
 * temizliği (örn. `px-2` + `px-4` → `px-4` olur).
 *
 * Kullanım:
 *   cn('px-4 py-2', isActive && 'bg-primary', className)
 */
export function cn(...inputs: ClassValue[]): string {
    return twMerge(clsx(inputs));
}

/**
 * Boşluk ayırıcı için ufak helper — JSX içinde birden fazla class grubunu
 * okunur yapmaya yarar.
 */
export const joinNonEmpty = (...parts: (string | false | null | undefined)[]): string =>
    parts.filter(Boolean).join(' ');
