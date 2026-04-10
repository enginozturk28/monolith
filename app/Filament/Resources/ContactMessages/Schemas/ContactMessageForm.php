<?php

namespace App\Filament\Resources\ContactMessages\Schemas;

use App\Models\ContactMessage;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;

class ContactMessageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->columnSpan(['lg' => 2])
                    ->schema([
                        Section::make('Mesaj')
                            ->schema([
                                TextInput::make('name')
                                    ->label('Ad Soyad')
                                    ->disabled()
                                    ->dehydrated(false),

                                TextInput::make('email')
                                    ->label('E-posta')
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->suffixAction(
                                        \Filament\Actions\Action::make('copyEmail')
                                            ->icon('heroicon-m-clipboard-document')
                                            ->tooltip('Kopyala')
                                            ->action(fn ($state) => null)
                                    ),

                                TextInput::make('phone')
                                    ->label('Telefon')
                                    ->disabled()
                                    ->dehydrated(false),

                                Select::make('subject_type')
                                    ->label('Talep Türü')
                                    ->options(ContactMessage::subjectTypes())
                                    ->disabled()
                                    ->dehydrated(false),

                                TextInput::make('subject')
                                    ->label('Konu')
                                    ->disabled()
                                    ->dehydrated(false),

                                Textarea::make('message')
                                    ->label('Mesaj İçeriği')
                                    ->rows(8)
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->columnSpanFull(),
                            ])
                            ->columns(2),
                    ]),

                Group::make()
                    ->columnSpan(['lg' => 1])
                    ->schema([
                        Section::make('Durum')
                            ->schema([
                                DateTimePicker::make('read_at')
                                    ->label('Okunma Tarihi')
                                    ->displayFormat('d.m.Y H:i')
                                    ->native(false)
                                    ->helperText('Boş = henüz okunmadı. Kaydedince otomatik dolar.'),

                                DateTimePicker::make('replied_at')
                                    ->label('Yanıtlanma Tarihi')
                                    ->displayFormat('d.m.Y H:i')
                                    ->native(false)
                                    ->helperText('Bu mesaja yanıt verdiğinizde buraya tarih girebilirsiniz.'),
                            ]),

                        Section::make('Teknik Bilgi')
                            ->collapsed()
                            ->schema([
                                TextInput::make('ip_address')
                                    ->label('IP Adresi')
                                    ->disabled()
                                    ->dehydrated(false),

                                Placeholder::make('user_agent')
                                    ->label('Tarayıcı')
                                    ->content(fn ($record) => new HtmlString('<span class="text-xs text-gray-500 break-all">'.e($record?->user_agent ?? '—').'</span>')),

                                Placeholder::make('created_at')
                                    ->label('Gelme Zamanı')
                                    ->content(fn ($record) => $record?->created_at?->format('d.m.Y H:i:s')),
                            ]),
                    ]),
            ])
            ->columns(3);
    }
}
