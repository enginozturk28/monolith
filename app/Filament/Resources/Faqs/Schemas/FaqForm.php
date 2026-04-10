<?php

namespace App\Filament\Resources\Faqs\Schemas;

use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class FaqForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->columnSpan(['lg' => 2])
                    ->schema([
                        Section::make('Soru ve Cevap')
                            ->schema([
                                Textarea::make('question')
                                    ->label('Soru')
                                    ->required()
                                    ->rows(2)
                                    ->maxLength(300)
                                    ->helperText('Ziyaretçinin sorduğu soru — net ve anlaşılır olmalı.'),

                                RichEditor::make('answer')
                                    ->label('Cevap')
                                    ->required()
                                    ->toolbarButtons([
                                        ['bold', 'italic', 'underline'],
                                        ['bulletList', 'orderedList', 'link'],
                                        ['undo', 'redo'],
                                    ])
                                    ->helperText('Ölçülü, açıklayıcı ve reklam dilinden uzak bir cevap yazılmalı.'),
                            ]),
                    ]),

                Group::make()
                    ->columnSpan(['lg' => 1])
                    ->schema([
                        Section::make('Ayarlar')
                            ->schema([
                                Select::make('faq_category_id')
                                    ->label('Kategori')
                                    ->relationship('category', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->createOptionForm([
                                        TextInput::make('name')
                                            ->label('Kategori Adı')
                                            ->required()
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(fn ($state, callable $set) => $set('slug', Str::slug((string) $state))),
                                        TextInput::make('slug')
                                            ->label('URL')
                                            ->required()
                                            ->unique('faq_categories', 'slug'),
                                        Toggle::make('is_published')
                                            ->label('Yayında')
                                            ->default(true),
                                    ]),

                                Toggle::make('is_published')
                                    ->label('Yayında')
                                    ->default(true),

                                TextInput::make('sort_order')
                                    ->label('Sıra')
                                    ->numeric()
                                    ->default(0)
                                    ->helperText('Küçük sayı üstte görünür.'),
                            ]),
                    ]),
            ])
            ->columns(3);
    }
}
