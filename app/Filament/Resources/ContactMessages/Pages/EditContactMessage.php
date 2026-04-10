<?php

namespace App\Filament\Resources\ContactMessages\Pages;

use App\Filament\Resources\ContactMessages\ContactMessageResource;
use App\Models\ContactMessage;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Icons\Heroicon;

class EditContactMessage extends EditRecord
{
    protected static string $resource = ContactMessageResource::class;

    /**
     * Mesaj ilk kez açıldığında otomatik olarak okundu işaretlenir.
     */
    protected function afterFill(): void
    {
        /** @var ContactMessage $record */
        $record = $this->record;

        if ($record->read_at === null) {
            $record->markAsRead();
            $this->data['read_at'] = $record->read_at;
        }
    }

    protected function getHeaderActions(): array
    {
        /** @var ContactMessage $record */
        $record = $this->record;

        return [
            Action::make('mailto')
                ->label('E-posta ile Yanıtla')
                ->icon(Heroicon::Envelope)
                ->color('primary')
                ->url(fn () => 'mailto:'.$record->email.'?subject='.rawurlencode('Yanıt: '.($record->subject ?? 'İletişim Talebiniz')))
                ->openUrlInNewTab(),

            Action::make('markAsReplied')
                ->label('Yanıtlandı Olarak İşaretle')
                ->icon(Heroicon::CheckCircle)
                ->color('success')
                ->visible(fn () => $record->replied_at === null)
                ->action(function () use ($record) {
                    $record->forceFill(['replied_at' => now()])->save();
                    $this->refreshFormData(['replied_at']);
                }),

            DeleteAction::make()->label('Sil'),
        ];
    }
}
