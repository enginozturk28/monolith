<?php

namespace App\Filament\Resources\FaqCategories\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class FaqCategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Kategori Bilgileri')
                    ->schema([
                        TextInput::make('name')
                            ->label('Kategori Adı')
                            ->required()
                            ->maxLength(160)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (string $operation, $state, callable $set) => $operation === 'create' ? $set('slug', Str::slug((string) $state)) : null),

                        TextInput::make('slug')
                            ->label('URL Kısaltma')
                            ->required()
                            ->maxLength(160)
                            ->unique(ignoreRecord: true),

                        TextInput::make('icon')
                            ->label('İkon (Lucide / Heroicon)')
                            ->helperText('Örn: help-circle, scale, receipt — boş bırakılabilir.')
                            ->maxLength(64),

                        Textarea::make('description')
                            ->label('Açıklama')
                            ->rows(2)
                            ->helperText('SSS sayfasında kategori başlığı altında görünür (opsiyonel).')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('Yayın')
                    ->schema([
                        Toggle::make('is_published')
                            ->label('Yayında')
                            ->default(true),

                        TextInput::make('sort_order')
                            ->label('Sıra')
                            ->numeric()
                            ->default(0),
                    ])
                    ->columns(2),
            ]);
    }
}
