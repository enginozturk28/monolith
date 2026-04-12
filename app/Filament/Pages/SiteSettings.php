<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use App\Support\SiteSettings as SiteSettingsService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

/**
 * SiteSettings — site genel ayarları paneli.
 *
 * Tüm site kimliği, iletişim bilgileri, sosyal medya linkleri, logo,
 * footer ve hakkımızda içerik blokları bu sayfadan yönetilir.
 * Değişiklikler DB'deki `settings` tablosuna yazılır ve Setting::saved
 * hook'u ile tüm cache'ler invalidate edilir; frontend anında yansır.
 *
 * Logo upload: Filament FileUpload + public disk — storage/app/public/branding/
 * altına yazar, dönen path'i `/storage/branding/...` URL'i olarak
 * `logo_url` setting key'ine kaydeder. TypographicLogo component'i bu URL
 * varsa <img> render eder, yoksa tipografik fallback'e geçer.
 */
class SiteSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected string $view = 'filament.pages.site-settings';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static string|UnitEnum|null $navigationGroup = 'Ayarlar';

    protected static ?int $navigationSort = 0;

    protected static ?string $navigationLabel = 'Site Ayarları';

    protected static ?string $title = 'Site Ayarları';

    /**
     * @var array<string, mixed>
     */
    public ?array $data = [];

    public function mount(): void
    {
        /** @var SiteSettingsService $service */
        $service = app(SiteSettingsService::class);
        $data = $service->all();

        // logo_url DB'de /storage/branding/xxx.png olarak saklı.
        // Filament FileUpload disk='public' olduğunda relative path ister (branding/xxx.png).
        // Prefix'i çıkarıp relative path'e çevir.
        $logoPath = $data['logo_url'] ?? null;
        if (is_string($logoPath) && str_starts_with($logoPath, '/storage/')) {
            $logoPath = substr($logoPath, strlen('/storage/'));
        }

        $this->form->fill([
            'name' => $data['name'] ?? '',
            'tagline' => $data['tagline'] ?? '',
            'footer_description' => $data['footer_description'] ?? '',
            'copyright_text' => $data['copyright_text'] ?? 'Tüm hakları saklıdır.',

            'logo_url' => $logoPath,

            'phone' => $data['phone'] ?? '',
            'email' => $data['email'] ?? '',
            'kep' => $data['kep'] ?? '',
            'address' => $data['address'] ?? '',
            'map_embed_url' => $data['map_embed_url'] ?? '',

            'linkedin_url' => $data['linkedin_url'] ?? '',
            'whatsapp_url' => $data['whatsapp_url'] ?? '',
            'instagram_url' => $data['instagram_url'] ?? '',
            'x_url' => $data['x_url'] ?? '',

            'about_intro_body' => $data['about_intro_body'] ?? '',
            'founder_bio' => $data['founder_bio'] ?? '',

            // Hero bölümü
            'hero_title' => $data['hero_title'] ?? '',
            'hero_description' => $data['hero_description'] ?? '',

            // Süreç bölümü
            'process_title' => $data['process_title'] ?? '',
            'process_step_1_title' => $data['process_step_1_title'] ?? 'Değerlendirme',
            'process_step_1_text' => $data['process_step_1_text'] ?? '',
            'process_step_2_title' => $data['process_step_2_title'] ?? 'Yönlendirme',
            'process_step_2_text' => $data['process_step_2_text'] ?? '',
            'process_step_3_title' => $data['process_step_3_title'] ?? 'Takip',
            'process_step_3_text' => $data['process_step_3_text'] ?? '',
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Section::make('Kimlik')
                    ->description('Büro adı, slogan ve footer tanıtım metni. Tüm site başlıklarında, footer\'da ve copyright satırında kullanılır.')
                    ->icon(Heroicon::OutlinedBuildingLibrary)
                    ->schema([
                        TextInput::make('name')
                            ->label('Büro Adı')
                            ->required()
                            ->maxLength(200)
                            ->helperText('Tarayıcı başlığında ve logoda kullanılır.'),

                        TextInput::make('tagline')
                            ->label('Slogan / Kısa Tanıtım')
                            ->maxLength(240)
                            ->helperText('Büronun kısa özeti (meta description\'da kullanılır).'),

                        Textarea::make('footer_description')
                            ->label('Footer Açıklama Metni')
                            ->rows(4)
                            ->maxLength(500)
                            ->helperText('Footer\'ın sol kolonunda logonun altında görünür. 2-3 cümlelik bir büro tanıtımı uygundur.')
                            ->columnSpanFull(),

                        TextInput::make('copyright_text')
                            ->label('Copyright Metni')
                            ->required()
                            ->maxLength(200)
                            ->helperText('Footer\'ın altında yıl ve büro adıyla birlikte görünür. Yıl ve "© {büro adı}" kısmı otomatik eklenir, sen sadece devamını yaz. Örn: "Tüm hakları saklıdır." → "© 2026 Loğoğlu Hukuk Bürosu. Tüm hakları saklıdır."')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('Logo')
                    ->description('Büro logosunu yükleyin. Yüklenmediğinde site, büro adının baş harfiyle oluşturulmuş tipografik bir fallback gösterir.')
                    ->icon(Heroicon::OutlinedPhoto)
                    ->schema([
                        FileUpload::make('logo_url')
                            ->label('Logo Dosyası')
                            ->image()
                            ->disk('public')
                            ->directory('branding')
                            ->visibility('public')
                            ->imageEditor()
                            ->imagePreviewHeight('120')
                            ->maxSize(2048)
                            ->acceptedFileTypes(['image/png', 'image/jpeg', 'image/svg+xml', 'image/webp'])
                            ->helperText('PNG, JPEG, SVG veya WebP · En fazla 2MB · Header ve footer\'da otomatik kullanılır.')
                            ->getUploadedFileNameForStorageUsing(
                                fn ($file): string => 'logo-'.now()->format('Ymd-His').'.'.$file->getClientOriginalExtension()
                            ),
                    ]),

                Section::make('İletişim Bilgileri')
                    ->description('Footer ve iletişim sayfasında gösterilen bilgiler. İletişim formundan gelen mesajlar da "E-posta" alanına bildirim olarak gönderilir.')
                    ->icon(Heroicon::OutlinedPhone)
                    ->schema([
                        TextInput::make('phone')
                            ->label('Telefon')
                            ->tel()
                            ->maxLength(40)
                            ->helperText('Uluslararası formatta: +90 5XX XXX XX XX'),

                        TextInput::make('email')
                            ->label('E-posta')
                            ->email()
                            ->required()
                            ->maxLength(200)
                            ->helperText('İletişim formu bildirimleri bu adrese gönderilir.'),

                        TextInput::make('kep')
                            ->label('KEP Adresi')
                            ->email()
                            ->maxLength(200),

                        Textarea::make('address')
                            ->label('Adres')
                            ->rows(2)
                            ->maxLength(400)
                            ->columnSpanFull(),

                        Textarea::make('map_embed_url')
                            ->label('Google Maps Embed URL')
                            ->rows(3)
                            ->helperText('Google Maps üzerinden "Paylaş → Harita yerleştir → HTML" ile alınan iframe içindeki src URL\'sini yapıştırın.')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('Sosyal Medya')
                    ->description('Dolu olan linkler footer\'da otomatik ikon olarak görünür. Boş bırakılanlar hiç gösterilmez.')
                    ->icon(Heroicon::OutlinedGlobeAlt)
                    ->schema([
                        TextInput::make('linkedin_url')
                            ->label('LinkedIn')
                            ->url()
                            ->maxLength(300)
                            ->placeholder('https://www.linkedin.com/in/kullanici-adi/'),

                        TextInput::make('whatsapp_url')
                            ->label('WhatsApp')
                            ->url()
                            ->maxLength(300)
                            ->placeholder('https://wa.me/905XXXXXXXXX')
                            ->helperText('wa.me kısa linki veya api.whatsapp.com formatı.'),

                        TextInput::make('instagram_url')
                            ->label('Instagram')
                            ->url()
                            ->maxLength(300)
                            ->placeholder('https://instagram.com/kullanici-adi'),

                        TextInput::make('x_url')
                            ->label('X (eski Twitter)')
                            ->url()
                            ->maxLength(300)
                            ->placeholder('https://x.com/kullanici-adi'),
                    ])
                    ->columns(2),

                Section::make('Ana Sayfa — Hero Bölümü')
                    ->description('Ana sayfanın üst kısmındaki büyük başlık ve açıklama. Vurgu kelimesi için <em>kelime</em> kullanın.')
                    ->icon(Heroicon::OutlinedSparkles)
                    ->collapsed()
                    ->schema([
                        TextInput::make('hero_title')
                            ->label('Hero Başlık')
                            ->maxLength(300)
                            ->helperText('HTML destekli — <em>kelime</em> ile italik/vurgulu kelime ekleyebilirsiniz. Örn: Hukuki süreçlerinizde <em>güvenilir</em> ve çözüm odaklı bir yaklaşım.')
                            ->columnSpanFull(),

                        Textarea::make('hero_description')
                            ->label('Hero Açıklama')
                            ->rows(3)
                            ->maxLength(500)
                            ->helperText('Boş bırakılırsa büro adı + varsayılan tanıtım metni kullanılır.')
                            ->columnSpanFull(),
                    ]),

                Section::make('Ana Sayfa — Süreç Bölümü')
                    ->description('"Değerlendirme → Yönlendirme → Takip" bölümünün başlık ve adım metinleri.')
                    ->icon(Heroicon::OutlinedListBullet)
                    ->collapsed()
                    ->schema([
                        TextInput::make('process_title')
                            ->label('Bölüm Başlığı')
                            ->maxLength(200)
                            ->columnSpanFull(),

                        TextInput::make('process_step_1_title')
                            ->label('Adım 1 — Başlık'),
                        Textarea::make('process_step_1_text')
                            ->label('Adım 1 — Açıklama')
                            ->rows(2),

                        TextInput::make('process_step_2_title')
                            ->label('Adım 2 — Başlık'),
                        Textarea::make('process_step_2_text')
                            ->label('Adım 2 — Açıklama')
                            ->rows(2),

                        TextInput::make('process_step_3_title')
                            ->label('Adım 3 — Başlık'),
                        Textarea::make('process_step_3_text')
                            ->label('Adım 3 — Açıklama')
                            ->rows(2),
                    ])
                    ->columns(2),

                Section::make('Hakkımızda İçeriği')
                    ->description('Hakkımızda sayfasının metin blokları. Zengin editör ile paragraf, kalın, italik, liste ve link ekleyebilirsiniz.')
                    ->icon(Heroicon::OutlinedDocumentText)
                    ->collapsed()
                    ->schema([
                        RichEditor::make('about_intro_body')
                            ->label('Büro Tanıtım Metni')
                            ->helperText('Hakkımızda sayfasında üst blokta görünür. 2-3 paragraf önerilir.')
                            ->toolbarButtons([
                                ['bold', 'italic', 'underline'],
                                ['link', 'bulletList', 'orderedList'],
                                ['undo', 'redo'],
                            ])
                            ->columnSpanFull(),

                        RichEditor::make('founder_bio')
                            ->label('Kurucu Avukat Biyografisi')
                            ->helperText('Hakkımızda sayfasının alt kısmında avukatın biyografisi olarak gösterilir.')
                            ->toolbarButtons([
                                ['bold', 'italic', 'underline'],
                                ['link', 'bulletList', 'orderedList'],
                                ['undo', 'redo'],
                            ])
                            ->columnSpanFull(),
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

    /**
     * Save — tüm alanları DB'ye yaz ve cache'leri temizle.
     */
    public function save(): void
    {
        $data = $this->form->getState();

        // FileUpload disk='public' dönüş değeri relative path (örn. "branding/logo-xxx.png")
        // Frontend /storage/... URL'i kullanıyor, prefix ekleyerek normalize et.
        if (! empty($data['logo_url']) && is_string($data['logo_url'])) {
            $logo = $data['logo_url'];
            if (! str_starts_with($logo, '/storage/') && ! str_starts_with($logo, 'http')) {
                $data['logo_url'] = '/storage/'.ltrim($logo, '/');
            }
        }

        $fieldTypes = [
            'name' => 'text',
            'tagline' => 'text',
            'footer_description' => 'textarea',
            'copyright_text' => 'text',
            'logo_url' => 'image',
            'phone' => 'text',
            'email' => 'text',
            'kep' => 'text',
            'address' => 'textarea',
            'map_embed_url' => 'textarea',
            'linkedin_url' => 'text',
            'whatsapp_url' => 'text',
            'instagram_url' => 'text',
            'x_url' => 'text',
            'about_intro_body' => 'textarea',
            'founder_bio' => 'textarea',
            // Hero bölümü
            'hero_title' => 'text',
            'hero_description' => 'textarea',
            // Süreç bölümü
            'process_title' => 'text',
            'process_step_1_title' => 'text',
            'process_step_1_text' => 'textarea',
            'process_step_2_title' => 'text',
            'process_step_2_text' => 'textarea',
            'process_step_3_title' => 'text',
            'process_step_3_text' => 'textarea',
        ];

        foreach ($fieldTypes as $key => $type) {
            if (! array_key_exists($key, $data)) {
                continue;
            }
            Setting::set('site', $key, $data[$key] ?: null, [
                'type' => $type,
                'encrypted' => false,
            ]);
        }

        Setting::flushCaches();

        Notification::make()
            ->title('Site ayarları kaydedildi')
            ->body('Değişiklikler canlıya yansıdı. Ön yüzdeki sayfaları yenileyerek görebilirsiniz.')
            ->success()
            ->send();
    }
}
