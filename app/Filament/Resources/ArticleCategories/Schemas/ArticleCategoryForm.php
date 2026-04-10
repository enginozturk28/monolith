<?php

namespace App\Filament\Resources\ArticleCategories\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ArticleCategoryForm
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

                        Textarea::make('description')
                            ->label('Açıklama')
                            ->rows(2)
                            ->columnSpanFull()
                            ->helperText('Opsiyonel — makale listesi sayfasında kategori başlığı altında gösterilir.'),

                        TextInput::make('sort_order')
                            ->label('Sıra')
                            ->numeric()
                            ->default(0)
                            ->helperText('Küçük sayı önce görünür.'),
                    ])
                    ->columns(2),
            ]);
    }
}
