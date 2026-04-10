<?php

namespace App\Filament\Resources\Pages\Schemas;

use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class PageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->columnSpan(['lg' => 2])
                    ->schema([
                        Section::make('Sayfa Bilgileri')
                            ->schema([
                                TextInput::make('title')
                                    ->label('Sayfa Başlığı')
                                    ->required()
                                    ->maxLength(240)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (string $operation, $state, callable $set) {
                                        if ($operation === 'create') {
                                            $set('slug', Str::slug((string) $state));
                                            $set('meta_title', $state);
                                        }
                                    }),

                                TextInput::make('slug')
                                    ->label('URL Kısaltma')
                                    ->required()
                                    ->maxLength(160)
                                    ->unique(ignoreRecord: true)
                                    ->disabled(fn ($record) => $record?->is_system)
                                    ->dehydrated()
                                    ->helperText(fn ($record) => $record?->is_system
                                        ? 'Sistem sayfaları için URL değiştirilemez.'
                                        : 'Boşluk ve Türkçe karakter içermez.'),
                            ]),

                        Section::make('İçerik')
                            ->schema([
                                RichEditor::make('body')
                                    ->label('Sayfa İçeriği')
                                    ->required()
                                    ->toolbarButtons([
                                        ['bold', 'italic', 'underline', 'strike'],
                                        ['h2', 'h3', 'h4', 'blockquote'],
                                        ['link', 'bulletList', 'orderedList'],
                                        ['undo', 'redo'],
                                    ])
                                    ->columnSpanFull(),
                            ]),

                        Section::make('SEO')
                            ->collapsed()
                            ->schema([
                                TextInput::make('meta_title')
                                    ->label('Meta Başlık')
                                    ->maxLength(240)
                                    ->helperText('Boş bırakılırsa sayfa başlığı kullanılır.'),
                                Textarea::make('meta_description')
                                    ->label('Meta Açıklama')
                                    ->rows(2)
                                    ->maxLength(300),
                            ]),
                    ]),

                Group::make()
                    ->columnSpan(['lg' => 1])
                    ->schema([
                        Section::make('Yayın')
                            ->schema([
                                Toggle::make('is_published')
                                    ->label('Yayında')
                                    ->default(true)
                                    ->helperText('Kapalıysa sadece admin panelden görünür.'),

                                Toggle::make('is_system')
                                    ->label('Sistem Sayfası')
                                    ->disabled()
                                    ->helperText('KVKK, çerez politikası gibi yasal zorunlu sayfalar silinemez.'),
                            ]),
                    ]),
            ])
            ->columns(3);
    }
}
