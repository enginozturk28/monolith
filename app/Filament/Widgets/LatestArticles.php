<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Articles\ArticleResource;
use App\Models\Article;
use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

/**
 * Dashboard'da son 5 makaleyi gösterir. Yayında olanlar + taslaklar dahil,
 * durum ikonu ile ayırt edilebilir. Tıklayınca düzenleme sayfasına gider.
 */
class LatestArticles extends TableWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = 'Son Makaleler';

    protected static ?string $description = 'En son eklenen 5 makale';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                fn (): Builder => Article::query()
                    ->with('category')
                    ->latest('id')
                    ->limit(5)
            )
            ->paginated(false)
            ->columns([
                TextColumn::make('title')
                    ->label('Başlık')
                    ->searchable()
                    ->limit(60)
                    ->weight('medium'),

                TextColumn::make('category.name')
                    ->label('Kategori')
                    ->badge()
                    ->color('gray')
                    ->placeholder('—'),

                IconColumn::make('is_published')
                    ->label('Durum')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-pencil-square')
                    ->trueColor('success')
                    ->falseColor('warning')
                    ->tooltip(fn (Article $record) => $record->is_published ? 'Yayında' : 'Taslak'),

                TextColumn::make('published_at')
                    ->label('Yayın Tarihi')
                    ->dateTime('d.m.Y H:i')
                    ->placeholder('—')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Oluşturma')
                    ->dateTime('d.m.Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->recordActions([
                Action::make('edit')
                    ->label('Düzenle')
                    ->icon(Heroicon::PencilSquare)
                    ->color('gray')
                    ->url(fn (Article $record): string => ArticleResource::getUrl('edit', ['record' => $record])),
            ])
            ->emptyStateHeading('Henüz makale yok')
            ->emptyStateDescription('Yeni bir makale ekleyerek başlayabilirsiniz.')
            ->emptyStateIcon(Heroicon::DocumentText);
    }
}
