<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

/**
 * ApiKeysSettings — 3rd party API key'lerinin şifreli yönetim paneli.
 *
 * Tüm key'ler Setting::set ile encrypted=true olarak DB'ye yazılır,
 * Crypt::encryptString ile şifrelenir. Plain text DB'de saklanmaz.
 *
 * Şu an için sadece Pixabay key'i var. İleride başka servisler
 * (örn. SerpAPI, OpenAI vb.) eklenmek istenirse buraya yeni alanlar
 * eklenir, kod iskelet aynı kalır.
 *
 * Kullanım:
 * - PixabayClient::getApiKey() önce Setting::value('api_keys', 'pixabay')
 *   sonra config('services.pixabay.key') sonra env('PIXABAY_API_KEY') sırasıyla
 *   okur. Yani panel'den değer girildiği an .env'den önceliklidir.
 */
class ApiKeysSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected string $view = 'filament.pages.api-keys-settings';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedKey;

    protected static string|UnitEnum|null $navigationGroup = 'Ayarlar';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationLabel = 'API Anahtarları';

    protected static ?string $title = 'API Anahtarları';

    /**
     * @var array<string, mixed>
     */
    public ?array $data = [];

    public function mount(): void
    {
        $keys = Setting::allGrouped()['api_keys'] ?? [];

        $this->form->fill([
            'pixabay' => $keys['pixabay'] ?? '',
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Section::make('Görsel Servisleri')
                    ->description('Stok görsel servislerinin API anahtarları. Panel\'den girilen anahtarlar DB\'de şifreli olarak saklanır ve .env\'den önceliklidir.')
                    ->icon(Heroicon::OutlinedPhoto)
                    ->schema([
                        TextInput::make('pixabay')
                            ->label('Pixabay API Anahtarı')
                            ->password()
                            ->revealable()
                            ->maxLength(255)
                            ->helperText('https://pixabay.com/api/docs/ adresinden ücretsiz alınabilir. monolith:fetch-images komutu bu key\'i kullanır. Şifreli olarak DB\'ye yazılır.'),
                    ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')
                ->label('Kaydet')
                ->icon(Heroicon::OutlinedCheckCircle)
                ->color('primary')
                ->action('save'),
        ];
    }

    public function save(): void
    {
        $data = $this->form->getState();

        // Tüm API key'leri encrypted=true olarak yaz
        $keys = ['pixabay'];

        foreach ($keys as $key) {
            if (! array_key_exists($key, $data)) {
                continue;
            }

            Setting::set('api_keys', $key, $data[$key] ?: null, [
                'type' => 'password',
                'encrypted' => true,
            ]);
        }

        Setting::flushCaches();

        Notification::make()
            ->title('API anahtarları kaydedildi')
            ->body('Anahtarlar şifreli olarak DB\'ye yazıldı ve cache invalidate edildi.')
            ->success()
            ->send();
    }
}
