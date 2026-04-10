<?php

namespace App\Filament\Widgets;

use App\Models\Article;
use App\Models\ContactMessage;
use App\Models\Faq;
use App\Models\Service;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

/**
 * ContentStatsOverview — Dashboard'un üstünde 4 kartlık özet.
 *
 * Sayılar canlı DB'den gelir. Reklam dilinden kaçınarak sadece
 * gerçek rakamları gösterir — "en çok", "rekor" gibi ifadeler yok.
 */
class ContentStatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected int|string|array $columnSpan = 'full';

    protected function getStats(): array
    {
        $publishedArticles = Article::query()->where('is_published', true)->count();
        $draftArticles = Article::query()->where('is_published', false)->count();
        $services = Service::query()->where('is_published', true)->count();
        $faqs = Faq::query()->where('is_published', true)->count();
        $unread = ContactMessage::query()->whereNull('read_at')->count();

        return [
            Stat::make('Yayındaki Makale', (string) $publishedArticles)
                ->description($draftArticles > 0 ? "{$draftArticles} taslak var" : 'Taslak yok')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('primary'),

            Stat::make('Faaliyet Alanları', (string) $services)
                ->description('Yayındaki hukuk alanları')
                ->descriptionIcon('heroicon-m-scale')
                ->color('primary'),

            Stat::make('Sıkça Sorulan Sorular', (string) $faqs)
                ->description('Yayındaki sorular')
                ->descriptionIcon('heroicon-m-question-mark-circle')
                ->color('primary'),

            Stat::make('Okunmamış Mesaj', (string) $unread)
                ->description($unread > 0 ? 'İletişim formundan gelen' : 'Yeni mesaj yok')
                ->descriptionIcon('heroicon-m-inbox')
                ->color($unread > 0 ? 'warning' : 'gray'),
        ];
    }

    protected function getColumns(): int
    {
        return 4;
    }
}
