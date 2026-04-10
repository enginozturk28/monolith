<?php

namespace App\Filament\Resources\Articles\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ArticleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->columnSpan(['lg' => 2])
                    ->schema([
                        Section::make('Temel Bilgiler')
                            ->description('Makale başlığı ve özet bilgiler')
                            ->schema([
                                TextInput::make('title')
                                    ->label('Başlık')
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
                                    ->maxLength(200)
                                    ->unique(ignoreRecord: true)
                                    ->helperText('Makalenin adres çubuğundaki hali — otomatik oluşturulur, gerekirse değiştirilebilir.'),

                                Textarea::make('excerpt')
                                    ->label('Özet')
                                    ->rows(3)
                                    ->maxLength(500)
                                    ->helperText('Makale listesinde ve sosyal paylaşımda gösterilecek kısa özet (yaklaşık 1-2 cümle).'),
                            ])
                            ->columns(1),

                        Section::make('İçerik')
                            ->description('Makale gövdesini aşağıda yazabilirsiniz. Başlık, kalın, italik, bağlantı ve liste kullanabilirsiniz.')
                            ->schema([
                                RichEditor::make('body')
                                    ->label('Makale İçeriği')
                                    ->required()
                                    ->toolbarButtons([
                                        ['bold', 'italic', 'underline', 'strike'],
                                        ['h2', 'h3', 'blockquote'],
                                        ['link', 'bulletList', 'orderedList'],
                                        ['undo', 'redo'],
                                    ])
                                    ->columnSpanFull(),
                            ]),

                        Section::make('SEO ve Sosyal Paylaşım')
                            ->description('Arama motorları ve sosyal medya için meta bilgiler')
                            ->collapsed()
                            ->schema([
                                TextInput::make('meta_title')
                                    ->label('Meta Başlık')
                                    ->maxLength(240)
                                    ->helperText('Boş bırakılırsa başlık kullanılır.'),
                                Textarea::make('meta_description')
                                    ->label('Meta Açıklama')
                                    ->rows(2)
                                    ->maxLength(300)
                                    ->helperText('Google sonuçlarında görünen açıklama. 150-160 karakter önerilir.'),
                            ]),
                    ]),

                Group::make()
                    ->columnSpan(['lg' => 1])
                    ->schema([
                        Section::make('Yayın')
                            ->schema([
                                Toggle::make('is_published')
                                    ->label('Yayında')
                                    ->default(false)
                                    ->helperText('Kapalıysa sadece admin panelden görünür.'),

                                DateTimePicker::make('published_at')
                                    ->label('Yayın Tarihi')
                                    ->displayFormat('d.m.Y H:i')
                                    ->native(false)
                                    ->default(now())
                                    ->helperText('Bu tarihten önce yayında görünmez.'),

                                TextInput::make('reading_time_minutes')
                                    ->label('Okuma Süresi (dk)')
                                    ->numeric()
                                    ->minValue(1)
                                    ->maxValue(120)
                                    ->suffix('dakika')
                                    ->helperText('Boş bırakılırsa otomatik hesaplanır.'),
                            ]),

                        Section::make('Kategori ve Yazar')
                            ->schema([
                                Select::make('article_category_id')
                                    ->label('Kategori')
                                    ->relationship('category', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->createOptionForm([
                                        TextInput::make('name')
                                            ->label('Kategori Adı')
                                            ->required()
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(fn ($state, callable $set) => $set('slug', Str::slug((string) $state))),
                                        TextInput::make('slug')
                                            ->label('URL')
                                            ->required()
                                            ->unique('article_categories', 'slug'),
                                    ]),

                                Select::make('author_id')
                                    ->label('Yazar')
                                    ->relationship('author', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->default(fn () => auth()->id()),
                            ]),
                    ]),
            ])
            ->columns(3);
    }
}
