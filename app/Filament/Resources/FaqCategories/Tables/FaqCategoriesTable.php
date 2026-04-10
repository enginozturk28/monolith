<?php

namespace App\Filament\Resources\FaqCategories\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class FaqCategoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('sort_order')
                    ->label('Sıra')
                    ->numeric()
                    ->sortable()
                    ->width('1%'),

                TextColumn::make('name')
                    ->label('Kategori Adı')
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),

                TextColumn::make('faqs_count')
                    ->label('Soru Sayısı')
                    ->counts('faqs')
                    ->badge()
                    ->color('gray'),

                IconColumn::make('is_published')
                    ->label('Yayında')
                    ->boolean()
                    ->trueColor('success')
                    ->falseColor('gray'),

                TextColumn::make('updated_at')
                    ->label('Güncellendi')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->recordActions([
                EditAction::make()->label('Düzenle'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->label('Sil'),
                ]),
            ]);
    }
}
