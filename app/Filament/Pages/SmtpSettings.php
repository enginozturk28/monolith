<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use Throwable;
use UnitEnum;

/**
 * SmtpSettings — SMTP yapılandırma paneli.
 *
 * Müşteri kendi mail sağlayıcısının SMTP bilgilerini buradan girer (Gmail,
 * Yandex, Workspace, Resend SMTP, Postmark SMTP vb.) ve hemen test maili
 * göndererek doğru çalıştığını teyit edebilir.
 *
 * Şifre alanı DB'de Crypt::encryptString ile şifrelenir (Setting modeli
 * encrypted=true ile yönetir). Plain text DB'de saklanmaz.
 *
 * Bu sayfada bir değer kaydedildiğinde:
 * 1. Setting::set ile DB'ye yazılır
 * 2. Setting::saved hook'u cache'leri invalidate eder
 * 3. Sonraki HTTP request'inde AppServiceProvider::boot() çağrılır
 * 4. MailConfig::applyFromSettings() yeni değerleri config'e bakar ve
 *    mail.default = 'smtp' yaparak runtime'da SMTP transport'u devreye sokar
 * 5. Mail::to(...)->send(...) artık gerçek SMTP üzerinden çalışır
 */
class SmtpSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected string $view = 'filament.pages.smtp-settings';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedEnvelope;

    protected static string|UnitEnum|null $navigationGroup = 'Ayarlar';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'SMTP Ayarları';

    protected static ?string $title = 'SMTP / E-posta Ayarları';

    /**
     * @var array<string, mixed>
     */
    public ?array $data = [];

    public function mount(): void
    {
        $smtp = Setting::allGrouped()['smtp'] ?? [];

        $this->form->fill([
            'host' => $smtp['host'] ?? '',
            'port' => $smtp['port'] ?? '587',
            'username' => $smtp['username'] ?? '',
            'password' => $smtp['password'] ?? '',
            'encryption' => $smtp['encryption'] ?? 'tls',
            'from_address' => $smtp['from_address'] ?? '',
            'from_name' => $smtp['from_name'] ?? '',
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Section::make('SMTP Sunucu Bilgileri')
                    ->description('Kullandığınız mail sağlayıcısının SMTP bilgilerini girin. Yaygın sağlayıcılar için ipuçları aşağıda. Şifre güvenli şekilde DB\'de şifreli saklanır.')
                    ->icon(Heroicon::OutlinedServer)
                    ->schema([
                        TextInput::make('host')
                            ->label('SMTP Sunucusu (Host)')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('smtp.gmail.com')
                            ->helperText('Örnekler: smtp.gmail.com (Gmail), smtp.yandex.com.tr (Yandex), smtp.resend.com (Resend), smtp.postmarkapp.com (Postmark)'),

                        TextInput::make('port')
                            ->label('Port')
                            ->required()
                            ->numeric()
                            ->default(587)
                            ->helperText('Genelde 587 (TLS) veya 465 (SSL).'),

                        TextInput::make('username')
                            ->label('Kullanıcı Adı')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('ornek@gmail.com')
                            ->helperText('Çoğunlukla mail adresinizdir.'),

                        TextInput::make('password')
                            ->label('Şifre')
                            ->password()
                            ->revealable()
                            ->required()
                            ->maxLength(255)
                            ->helperText('Gmail için "Uygulama Şifresi" oluşturmanız gerekir (normal Gmail şifresi çalışmaz). Resend / Postmark için API key kullanın.'),

                        Select::make('encryption')
                            ->label('Şifreleme')
                            ->options([
                                'tls' => 'TLS (önerilen, port 587)',
                                'ssl' => 'SSL (port 465)',
                                'none' => 'Yok (geliştirme)',
                            ])
                            ->required()
                            ->default('tls'),
                    ])
                    ->columns(2),

                Section::make('Gönderici Bilgileri')
                    ->description('Gönderilen e-postaların "Kimden" alanında görünecek değerler. Boş bırakılırsa .env\'deki varsayılan değerler kullanılır.')
                    ->icon(Heroicon::OutlinedUser)
                    ->schema([
                        TextInput::make('from_address')
                            ->label('Gönderen E-posta')
                            ->email()
                            ->maxLength(255)
                            ->placeholder('noreply@logogluhukuk.com.tr'),

                        TextInput::make('from_name')
                            ->label('Gönderen Adı')
                            ->maxLength(255)
                            ->placeholder('Loğoğlu Hukuk Bürosu'),
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

            Action::make('sendTest')
                ->label('Test Maili Gönder')
                ->icon(Heroicon::OutlinedPaperAirplane)
                ->color('gray')
                ->requiresConfirmation()
                ->modalHeading('Test maili gönderilsin mi?')
                ->modalDescription('İletişim bilgileri panelindeki "E-posta" adresine bir test maili gönderilir. Mevcut ayarları henüz kaydetmediyseniz önce "Kaydet" butonuna basın.')
                ->action('sendTestMail'),
        ];
    }

    public function save(): void
    {
        $data = $this->form->getState();

        $fieldTypes = [
            'host' => ['type' => 'text', 'encrypted' => false],
            'port' => ['type' => 'text', 'encrypted' => false],
            'username' => ['type' => 'text', 'encrypted' => false],
            'password' => ['type' => 'password', 'encrypted' => true],
            'encryption' => ['type' => 'text', 'encrypted' => false],
            'from_address' => ['type' => 'text', 'encrypted' => false],
            'from_name' => ['type' => 'text', 'encrypted' => false],
        ];

        foreach ($fieldTypes as $key => $cfg) {
            if (! array_key_exists($key, $data)) {
                continue;
            }
            Setting::set('smtp', $key, $data[$key] ?: null, [
                'type' => $cfg['type'],
                'encrypted' => $cfg['encrypted'],
            ]);
        }

        Setting::flushCaches();

        Notification::make()
            ->title('SMTP ayarları kaydedildi')
            ->body('Bir sonraki istek ile birlikte yeni ayarlar devreye girer. Test maili göndererek doğrulayabilirsiniz.')
            ->success()
            ->send();
    }

    public function sendTestMail(): void
    {
        try {
            // Form state'inden bir kez daha güncelle (henüz save edilmemişse)
            $this->save();

            // Site Settings'teki "email" alanına gönder
            $recipient = Setting::value('site', 'email');
            if (! $recipient) {
                Notification::make()
                    ->title('Test maili gönderilemedi')
                    ->body('Önce "Site Ayarları" panelinden bir e-posta adresi tanımlayın.')
                    ->danger()
                    ->send();

                return;
            }

            // Force reload the mail config (since we just saved)
            \App\Support\MailConfig::applyFromSettings();

            Mail::raw(
                "Bu bir test mailidir. Eğer bu mesajı görüyorsanız, Loğoğlu Hukuk Bürosu admin paneli SMTP yapılandırması doğru çalışıyor.\n\nGönderim zamanı: ".now()->format('d.m.Y H:i:s')."\n\nMail driver: ".Config::get('mail.default')."\nMail host: ".Config::get('mail.mailers.smtp.host'),
                function ($message) use ($recipient) {
                    $message->to($recipient)->subject('SMTP Test — Loğoğlu Hukuk Bürosu Admin');
                }
            );

            Notification::make()
                ->title('Test maili gönderildi')
                ->body("Alıcı: {$recipient}. Mevcut driver: ".Config::get('mail.default').'. Eğer driver "log" ise mail storage/logs/laravel.log dosyasına düştü.')
                ->success()
                ->send();
        } catch (Throwable $e) {
            Notification::make()
                ->title('Test maili gönderilemedi')
                ->body('Hata: '.$e->getMessage())
                ->danger()
                ->send();
        }
    }
}
