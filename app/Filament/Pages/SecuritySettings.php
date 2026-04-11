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
 * SecuritySettings — Güvenlik ayarları paneli.
 *
 * Şu anda Cloudflare Turnstile yapılandırmasını içerir. İleride brute-force
 * ayarları, IP allow/block list vb. güvenlik özelliklerinin merkezi olabilir.
 *
 * Cloudflare Turnstile mantığı:
 * - Site key + secret key panel'den girilir
 * - secret_key DB'de encrypted=true ile şifreli saklanır
 * - Eğer her ikisi de doluysa: iletişim formu otomatik Turnstile widget'ı
 *   yükler ve submit sırasında server-side doğrulama yapılır
 * - Eğer biri boşsa: iletişim formu honeypot ile devam eder (mevcut davranış)
 *
 * Bu sayede Turnstile opsiyonel kalır — müşteri Cloudflare hesabı açıp
 * key'leri girene kadar honeypot çalışır, hiçbir şey kırılmaz.
 */
class SecuritySettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected string $view = 'filament.pages.security-settings';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShieldCheck;

    protected static string|UnitEnum|null $navigationGroup = 'Ayarlar';

    protected static ?int $navigationSort = 4;

    protected static ?string $navigationLabel = 'Güvenlik Ayarları';

    protected static ?string $title = 'Güvenlik Ayarları';

    /**
     * @var array<string, mixed>
     */
    public ?array $data = [];

    public function mount(): void
    {
        $security = Setting::allGrouped()['security'] ?? [];

        $this->form->fill([
            'turnstile_site_key' => $security['turnstile_site_key'] ?? '',
            'turnstile_secret_key' => $security['turnstile_secret_key'] ?? '',
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Section::make('Cloudflare Turnstile')
                    ->description('İletişim formuna ek bot koruması. Cloudflare hesabınızdan ücretsiz alınır. Boş bırakılırsa iletişim formu sadece honeypot ile çalışır (mevcut güvenlik korunur). Site key + secret key birlikte girildiğinde otomatik olarak forma widget eklenir.')
                    ->icon(Heroicon::OutlinedShieldCheck)
                    ->schema([
                        TextInput::make('turnstile_site_key')
                            ->label('Site Anahtarı (Site Key)')
                            ->maxLength(255)
                            ->placeholder('0x4AAAAAAA...')
                            ->helperText('Cloudflare Dashboard → Turnstile → Add Site → Site Key. Public bir değerdir, frontend\'de görünür.'),

                        TextInput::make('turnstile_secret_key')
                            ->label('Gizli Anahtar (Secret Key)')
                            ->password()
                            ->revealable()
                            ->maxLength(255)
                            ->placeholder('0x4AAAAAAA...')
                            ->helperText('Cloudflare Dashboard → Turnstile → Secret Key. Sadece backend\'de kullanılır, asla frontend\'e gönderilmez. DB\'de şifreli saklanır.'),
                    ])
                    ->columns(2),
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

        // Site key public, secret key encrypted
        Setting::set('security', 'turnstile_site_key', $data['turnstile_site_key'] ?: null, [
            'type' => 'text',
            'encrypted' => false,
        ]);

        Setting::set('security', 'turnstile_secret_key', $data['turnstile_secret_key'] ?: null, [
            'type' => 'password',
            'encrypted' => true,
        ]);

        Setting::flushCaches();

        $bothFilled = ! empty($data['turnstile_site_key']) && ! empty($data['turnstile_secret_key']);

        Notification::make()
            ->title('Güvenlik ayarları kaydedildi')
            ->body($bothFilled
                ? 'Cloudflare Turnstile aktif edildi. İletişim formuna otomatik widget eklenecek.'
                : 'Turnstile devre dışı (her iki anahtar da doldurulmadı). İletişim formu honeypot ile çalışmaya devam ediyor.')
            ->success()
            ->send();
    }
}
