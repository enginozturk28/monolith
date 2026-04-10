<?php

namespace App\Filament\Resources\Services\Schemas;

use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ServiceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Temel Bilgiler')
                    ->schema([
                        TextInput::make('title')
                            ->label('Başlık')
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
                            ->label('İkon (Lucide)')
                            ->helperText('Örn: scale, briefcase, home — lucide.dev sitesinden seçilebilir')
                            ->maxLength(64),

                        Textarea::make('summary')
                            ->label('Özet')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('İçerik')
                    ->schema([
                        RichEditor::make('body')
                            ->label('Detay Metni')
                            ->columnSpanFull(),
                    ]),

                Section::make('SEO')
                    ->schema([
                        TextInput::make('meta_title')
                            ->label('Meta Başlık')
                            ->maxLength(200),
                        Textarea::make('meta_description')
                            ->label('Meta Açıklama')
                            ->rows(2)
                            ->columnSpanFull(),
                    ])
                    ->collapsed(),

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
