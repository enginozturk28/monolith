/**
 * Ana gezinme menüsü — tüm sayfalarda tutarlı olarak kullanılır.
 *
 * Henüz Faz 5'te sayfalar yazılmadığı için route isimleri placeholder
 * olarak `#` ile işaretli. Sayfalar geldikçe route('home'), route('about')
 * vb. ile değiştirilecek.
 */

export type NavItem = {
    label: string;
    href: string;
    routeName?: string;
    description?: string;
};

export const mainNavigation: NavItem[] = [
    {
        label: 'Ana Sayfa',
        href: '/',
        routeName: 'home',
    },
    {
        label: 'Hakkımızda',
        href: '/hakkimizda',
        routeName: 'about',
        description: 'Büro tanıtımı, vizyon, misyon ve kurucu avukat',
    },
    {
        label: 'Faaliyet Alanları',
        href: '/faaliyet-alanlari',
        routeName: 'services.index',
        description: 'Hukukun farklı alanlarında sunulan hizmetler',
    },
    {
        label: 'Makaleler',
        href: '/makaleler',
        routeName: 'articles.index',
        description: 'Güncel hukuki gelişmeler ve yazılar',
    },
    {
        label: 'Sıkça Sorulan Sorular',
        href: '/sss',
        routeName: 'faq',
    },
    {
        label: 'İletişim',
        href: '/iletisim',
        routeName: 'contact',
    },
];

export const legalNavigation: NavItem[] = [
    {
        label: 'KVKK Aydınlatma Metni',
        href: '/sayfa/kvkk',
    },
    {
        label: 'Çerez Politikası',
        href: '/sayfa/cerez-politikasi',
    },
];
