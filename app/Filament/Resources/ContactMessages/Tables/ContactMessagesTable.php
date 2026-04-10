<?php

namespace App\Filament\Resources\ContactMessages\Tables;

use App\Models\ContactMessage;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ContactMessagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                IconColumn::make('read_at')
                    ->label('')
                    ->width('1%')
                    ->icon(fn ($state) => $state ? 'heroicon-o-envelope-open' : 'heroicon-s-envelope')
                    ->color(fn ($state) => $state ? 'gray' : 'warning')
                    ->tooltip(fn ($state) => $state ? 'Okundu' : 'Okunmadı'),

                TextColumn::make('name')
                    ->label('Ad Soyad')
                    ->searchable()
                    ->sortable()
                    ->weight(fn ($record) => $record->read_at ? 'normal' : 'bold')
                    ->description(fn ($record) => $record->email),

                TextColumn::make('subject_type')
                    ->label('Tür')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => ContactMessage::subjectTypes()[$state] ?? $state)
                    ->color(fn (string $state): string => $state === ContactMessage::SUBJECT_GORUSME ? 'warning' : 'gray'),

                TextColumn::make('phone')
                    ->label('Telefon')
                    ->placeholder('—')
                    ->toggleable(),

                TextColumn::make('message')
                    ->label('Mesaj')
                    ->limit(60)
                    ->tooltip(fn (ContactMessage $record) => $record->message)
                    ->wrap(),

                TextColumn::make('created_at')
                    ->label('Gelme')
                    ->since()
                    ->tooltip(fn (ContactMessage $record) => $record->created_at?->format('d.m.Y H:i:s'))
                    ->sortable(),

                TextColumn::make('read_at')
                    ->label('Okundu')
                    ->dateTime('d.m.Y H:i')
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),

                TextColumn::make('replied_at')
                    ->label('Yanıtlandı')
                    ->dateTime('d.m.Y H:i')
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Filter::make('unread')
                    ->label('Sadece okunmamışlar')
                    ->query(fn (Builder $query): Builder => $query->whereNull('read_at'))
                    ->toggle(),

                SelectFilter::make('subject_type')
                    ->label('Talep Türü')
                    ->options(ContactMessage::subjectTypes()),
            ])
            ->recordActions([
                Action::make('markAsRead')
                    ->label('Okundu')
                    ->icon('heroicon-o-envelope-open')
                    ->color('gray')
                    ->visible(fn (ContactMessage $record) => $record->read_at === null)
                    ->action(fn (ContactMessage $record) => $record->markAsRead()),

                EditAction::make()->label('Görüntüle'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->label('Sil'),
                ]),
            ]);
    }
}
