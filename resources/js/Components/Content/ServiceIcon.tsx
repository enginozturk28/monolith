import {
    BadgeCheck,
    Briefcase,
    Building2,
    FileSignature,
    Gavel,
    Globe2,
    HeartHandshake,
    Home as HomeIcon,
    LifeBuoy,
    Receipt,
    Scale,
    Scroll,
    Sparkles,
    type LucideIcon,
} from 'lucide-react';
import { cn } from '@/lib/utils';

/**
 * Seed'deki 12 faaliyet alanı için hazır icon map'i. Kullanıcı panelden
 * icon adı girerse (örn. "scale", "briefcase") bu map'ten çekilir.
 * Bilinmeyen bir isim girilirse Sparkles default kullanılır.
 */
const ICON_MAP: Record<string, LucideIcon> = {
    scale: Scale,
    'heart-handshake': HeartHandshake,
    scroll: Scroll,
    'badge-check': BadgeCheck,
    home: HomeIcon,
    receipt: Receipt,
    'file-signature': FileSignature,
    briefcase: Briefcase,
    'building-2': Building2,
    'life-buoy': LifeBuoy,
    'globe-2': Globe2,
    gavel: Gavel,
};

type Props = {
    name?: string | null;
    className?: string;
};

export default function ServiceIcon({ name, className }: Props) {
    const Icon = (name && ICON_MAP[name]) || Sparkles;

    return (
        <Icon
            className={cn('h-6 w-6', className)}
            strokeWidth={1.5}
            aria-hidden
        />
    );
}
