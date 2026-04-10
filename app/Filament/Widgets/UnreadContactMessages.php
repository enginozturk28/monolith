<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\ContactMessages\ContactMessageResource;
use App\Models\ContactMessage;
use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

/**
 * Dashboard'da okunmamış iletişim mesajlarını gösterir.
 * Sadece unread olanlar listelenir; okunan mesajlar tam tabloya geçer.
 */
class UnreadContactMessages extends TableWidget
{
    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = 'Okunmamış Mesajlar';

    protected static ?string $description = 'İletişim formundan gelen yeni talepler';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                fn (): Builder => ContactMessage::query()
                    ->whereNull('read_at')
                    ->latest('id')
                    ->limit(5)
            )
            ->paginated(false)
            ->columns([
                TextColumn::make('name')
                    ->label('Ad Soyad')
                    ->searchable()
                    ->weight('medium'),

                TextColumn::make('subject_type')
                    ->label('Tür')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => ContactMessage::subjectTypes()[$state] ?? $state)
                    ->color(fn (string $state): string => $state === ContactMessage::SUBJECT_GORUSME ? 'warning' : 'gray'),

                TextColumn::make('email')
                    ->label('E-posta')
                    ->icon(Heroicon::Envelope)
                    ->copyable(),

                TextColumn::make('phone')
                    ->label('Telefon')
                    ->icon(Heroicon::Phone)
                    ->placeholder('—'),

                TextColumn::make('message')
                    ->label('Mesaj')
                    ->limit(60)
                    ->tooltip(fn (ContactMessage $record) => $record->message),

                TextColumn::make('created_at')
                    ->label('Gelme Zamanı')
                    ->since()
                    ->tooltip(fn (ContactMessage $record) => $record->created_at?->format('d.m.Y H:i')),
            ])
            ->recordActions([
                Action::make('view')
                    ->label('Görüntüle')
                    ->icon(Heroicon::Eye)
                    ->color('gray')
                    ->url(fn (ContactMessage $record): string => ContactMessageResource::getUrl('edit', ['record' => $record])),
            ])
            ->emptyStateHeading('Okunmamış mesaj yok')
            ->emptyStateDescription('İletişim formundan yeni bir talep geldiğinde burada listelenecek.')
            ->emptyStateIcon(Heroicon::InboxArrowDown);
    }
}
