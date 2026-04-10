<?php

namespace App\Filament\Resources\Pages\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class PagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Başlık')
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),

                TextColumn::make('slug')
                    ->label('URL')
                    ->badge()
                    ->color('gray')
                    ->copyable(),

                IconColumn::make('is_published')
                    ->label('Yayında')
                    ->boolean()
                    ->trueColor('success')
                    ->falseColor('gray'),

                IconColumn::make('is_system')
                    ->label('Sistem')
                    ->boolean()
                    ->trueIcon('heroicon-o-lock-closed')
                    ->falseIcon('heroicon-o-lock-open')
                    ->trueColor('warning')
                    ->falseColor('gray')
                    ->tooltip(fn ($record) => $record->is_system ? 'Silinemez (KVKK/yasal)' : 'Silinebilir'),

                TextColumn::make('updated_at')
                    ->label('Güncellendi')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('title')
            ->filters([
                TernaryFilter::make('is_published')
                    ->label('Yayın Durumu')
                    ->placeholder('Tümü'),
            ])
            ->recordActions([
                EditAction::make()->label('Düzenle'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Sil')
                        ->before(function ($records, $action) {
                            // Sistem sayfalarını koru
                            foreach ($records as $record) {
                                if ($record->is_system) {
                                    $action->failure();
                                    $action->halt();

                                    return;
                                }
                            }
                        }),
                ]),
            ]);
    }
}
