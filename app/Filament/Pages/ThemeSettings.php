<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use App\Support\ThemeManager;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

/**
 * ThemeSettings — dinamik tema paneli.
 *
 * Filament custom page. Mount'ta mevcut Setting (group=theme) kayıtlarını
 * form state'ine yükler, kullanıcı color picker + font select ile değerleri
 * değiştirir. "Kaydet" action'ı ile Setting::set() çağrıları yapılır ve
 * Setting::saved hook'u tüm cache'leri invalidate eder (ThemeManager +
 * SiteSettings). Sonraki request'te frontend yeni renklerle render olur.
 *
 * Preset sistemi: "Uygula" action'ı ile 4 hazır palet tek tıkla yüklenir.
 * Kullanıcı önce preset yükleyip sonra ince ayar yapabilir.
 */
class ThemeSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected string $view = 'filament.pages.theme-settings';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSwatch;

    protected static string|UnitEnum|null $navigationGroup = 'Ayarlar';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Tema Ayarları';

    protected static ?string $title = 'Tema Ayarları';

    /**
     * @var array<string, mixed>
     */
    public ?array $data = [];

    public function mount(): void
    {
        $theme = app(ThemeManager::class);
        $tokens = $theme->tokens();

        $this->form->fill([
            'bg' => $tokens['bg'] ?? '#F5F1EA',
            'surface' => $tokens['surface'] ?? '#FFFFFF',
            'surface_alt' => $tokens['surface_alt'] ?? '#EFE9DE',
            'text' => $tokens['text'] ?? '#0B1F3A',
            'text_muted' => $tokens['text_muted'] ?? '#8C8B86',
            'border' => $tokens['border'] ?? '#D9D2C4',
            'primary' => $tokens['primary'] ?? '#0B1F3A',
            'primary_fg' => $tokens['primary_fg'] ?? '#F5F1EA',
            'accent' => $tokens['accent'] ?? '#A88A55',
            'font_heading' => $tokens['font_heading'] ?? "'Cormorant Garamond', 'Lora', Georgia, ui-serif, serif",
            'font_body' => $tokens['font_body'] ?? "'Inter', ui-sans-serif, system-ui, sans-serif",
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Section::make('Renk Paleti')
                    ->description('Tüm ön yüz bu renklere göre anında güncellenir. Değişikliklerinizi kaydettikten sonra sayfayı yenileyerek sonucu görebilirsiniz.')
                    ->icon(Heroicon::OutlinedPaintBrush)
                    ->schema([
                        Fieldset::make('Yüzeyler')
                            ->schema([
                                ColorPicker::make('bg')
                                    ->label('Sayfa Arka Planı')
                                    ->required()
                                    ->helperText('Genel sayfa zemin rengi.'),
                                ColorPicker::make('surface')
                                    ->label('Yüzey (Kart)')
                                    ->required()
                                    ->helperText('Kart ve form yüzeyleri.'),
                                ColorPicker::make('surface_alt')
                                    ->label('İkincil Yüzey')
                                    ->required()
                                    ->helperText('Alt renkli bloklar.'),
                                ColorPicker::make('border')
                                    ->label('Ayraç / Çerçeve')
                                    ->required(),
                            ])
                            ->columns(4),

                        Fieldset::make('Metin')
                            ->schema([
                                ColorPicker::make('text')
                                    ->label('Ana Metin')
                                    ->required(),
                                ColorPicker::make('text_muted')
                                    ->label('Yardımcı Metin')
                                    ->required()
                                    ->helperText('Daha sönük metin için.'),
                            ])
                            ->columns(2),

                        Fieldset::make('Vurgu')
                            ->schema([
                                ColorPicker::make('primary')
                                    ->label('Birincil Renk')
                                    ->required()
                                    ->helperText('Buton arka planı, header ve önemli vurgular.'),
                                ColorPicker::make('primary_fg')
                                    ->label('Birincil Üzeri Metin')
                                    ->required()
                                    ->helperText('Primary zemin üzerindeki metin rengi.'),
                                ColorPicker::make('accent')
                                    ->label('Altın Vurgu')
                                    ->required()
                                    ->helperText('Link, alt çizgi, ikon tonlaması.'),
                            ])
                            ->columns(3),
                    ]),

                Section::make('Tipografi')
                    ->description('Sitede kullanılacak yazı aileleri. Google Fonts destekli adlar girilebilir (örn. Inter, Lora, Cormorant Garamond).')
                    ->icon(Heroicon::OutlinedDocumentText)
                    ->schema([
                        TextInput::make('font_heading')
                            ->label('Başlık Fontu (CSS font-family)')
                            ->required()
                            ->helperText("Örn: 'Cormorant Garamond', 'Lora', Georgia, serif"),

                        TextInput::make('font_body')
                            ->label('Gövde Fontu (CSS font-family)')
                            ->required()
                            ->helperText("Örn: 'Inter', ui-sans-serif, system-ui, sans-serif"),
                    ])
                    ->columns(2),
            ]);
    }

    /**
     * Header action'lar: Kaydet, Preset Yükle, Varsayılana Dön.
     */
    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')
                ->label('Kaydet')
                ->icon(Heroicon::OutlinedCheckCircle)
                ->color('primary')
                ->action('save'),

            Action::make('preset_logoglu')
                ->label('Loğoğlu Lacivert')
                ->icon(Heroicon::OutlinedSwatch)
                ->color('gray')
                ->requiresConfirmation()
                ->modalHeading('Loğoğlu Lacivert paletini uygulamak istiyor musunuz?')
                ->modalDescription('Mevcut renk ayarları varsayılan Loğoğlu paletiyle doldurulacak. Henüz kaydedilmeyecek, kayıt için "Kaydet" butonuna basmanız gerekir.')
                ->action(fn () => $this->applyPreset('logoglu')),

            Action::make('preset_antrasit')
                ->label('Klasik Antrasit')
                ->icon(Heroicon::OutlinedSwatch)
                ->color('gray')
                ->requiresConfirmation()
                ->action(fn () => $this->applyPreset('antrasit')),

            Action::make('preset_toprak')
                ->label('Sıcak Toprak')
                ->icon(Heroicon::OutlinedSwatch)
                ->color('gray')
                ->requiresConfirmation()
                ->action(fn () => $this->applyPreset('toprak')),

            Action::make('preset_zeytin')
                ->label('Zeytin Yeşili')
                ->icon(Heroicon::OutlinedSwatch)
                ->color('gray')
                ->requiresConfirmation()
                ->action(fn () => $this->applyPreset('zeytin')),
        ];
    }

    /**
     * Save — tüm tema token'larını DB'ye yaz.
     */
    public function save(): void
    {
        $data = $this->form->getState();

        foreach ($data as $key => $value) {
            Setting::set('theme', $key, $value, [
                'type' => in_array($key, ['font_heading', 'font_body'], true) ? 'text' : 'color',
                'encrypted' => false,
            ]);
        }

        // Setting::saved hook'u zaten cache invalidate ediyor ama açıkça çağıralım
        Setting::flushCaches();

        Notification::make()
            ->title('Tema kaydedildi')
            ->body('Değişiklikler canlıya yansıdı. Ön yüzdeki sayfaları yenileyerek görebilirsiniz.')
            ->success()
            ->send();
    }

    /**
     * Preset uygula — form state'ini doldurur, henüz kaydetmez.
     */
    public function applyPreset(string $name): void
    {
        $preset = self::presets()[$name] ?? null;

        if (! $preset) {
            return;
        }

        $this->form->fill($preset);

        Notification::make()
            ->title($preset['__label'].' preseti yüklendi')
            ->body('Renkler form alanlarına uygulandı. Kalıcı hale getirmek için "Kaydet" butonuna basın.')
            ->success()
            ->send();
    }

    /**
     * WCAG 2.1 kontrast oranı hesapla — blade view'da canlı gösterilir.
     *
     * @return array<string, array{ratio: float, label: string, pass: bool}>
     */
    public function getContrastReport(): array
    {
        $data = $this->data ?? [];

        $pairs = [
            'text_on_bg' => [
                'label' => 'Ana metin / Arka plan',
                'fg' => $data['text'] ?? null,
                'bg' => $data['bg'] ?? null,
            ],
            'muted_on_bg' => [
                'label' => 'Yardımcı metin / Arka plan',
                'fg' => $data['text_muted'] ?? null,
                'bg' => $data['bg'] ?? null,
            ],
            'primary_fg_on_primary' => [
                'label' => 'Buton metni / Birincil renk',
                'fg' => $data['primary_fg'] ?? null,
                'bg' => $data['primary'] ?? null,
            ],
            'accent_on_bg' => [
                'label' => 'Altın vurgu / Arka plan',
                'fg' => $data['accent'] ?? null,
                'bg' => $data['bg'] ?? null,
            ],
        ];

        $report = [];
        foreach ($pairs as $key => $pair) {
            $ratio = $this->contrastRatio($pair['fg'], $pair['bg']);
            $report[$key] = [
                'label' => $pair['label'],
                'ratio' => round($ratio, 2),
                'pass' => $ratio >= 4.5, // WCAG AA normal text
            ];
        }

        return $report;
    }

    private function contrastRatio(?string $fg, ?string $bg): float
    {
        if (! $fg || ! $bg) {
            return 0;
        }

        $l1 = $this->relativeLuminance($fg);
        $l2 = $this->relativeLuminance($bg);

        $lighter = max($l1, $l2);
        $darker = min($l1, $l2);

        return ($lighter + 0.05) / ($darker + 0.05);
    }

    private function relativeLuminance(string $hex): float
    {
        $hex = ltrim($hex, '#');
        if (strlen($hex) === 3) {
            $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
        }
        if (strlen($hex) !== 6) {
            return 0;
        }

        $r = hexdec(substr($hex, 0, 2)) / 255;
        $g = hexdec(substr($hex, 2, 2)) / 255;
        $b = hexdec(substr($hex, 4, 2)) / 255;

        $srgb = function (float $c): float {
            return $c <= 0.03928 ? $c / 12.92 : (($c + 0.055) / 1.055) ** 2.4;
        };

        return 0.2126 * $srgb($r) + 0.7152 * $srgb($g) + 0.0722 * $srgb($b);
    }

    /**
     * Hazır preset'ler. Her biri 11 token içerir + "__label" görünüm etiketi.
     *
     * @return array<string, array<string, string>>
     */
    public static function presets(): array
    {
        $defaultFontHeading = "'Cormorant Garamond', 'Lora', Georgia, ui-serif, serif";
        $defaultFontBody = "'Inter', ui-sans-serif, system-ui, sans-serif";

        return [
            'logoglu' => [
                '__label' => 'Loğoğlu Lacivert',
                'bg' => '#F5F1EA',
                'surface' => '#FFFFFF',
                'surface_alt' => '#EFE9DE',
                'text' => '#0B1F3A',
                'text_muted' => '#8C8B86',
                'border' => '#D9D2C4',
                'primary' => '#0B1F3A',
                'primary_fg' => '#F5F1EA',
                'accent' => '#A88A55',
                'font_heading' => $defaultFontHeading,
                'font_body' => $defaultFontBody,
            ],
            'antrasit' => [
                '__label' => 'Klasik Antrasit',
                'bg' => '#F4F4F5',
                'surface' => '#FFFFFF',
                'surface_alt' => '#E7E7EA',
                'text' => '#18181B',
                'text_muted' => '#71717A',
                'border' => '#D4D4D8',
                'primary' => '#18181B',
                'primary_fg' => '#FAFAFA',
                'accent' => '#71717A',
                'font_heading' => $defaultFontHeading,
                'font_body' => $defaultFontBody,
            ],
            'toprak' => [
                '__label' => 'Sıcak Toprak',
                'bg' => '#FBF7F2',
                'surface' => '#FFFFFF',
                'surface_alt' => '#F2EBE0',
                'text' => '#3D2817',
                'text_muted' => '#8B6F47',
                'border' => '#E6D5BC',
                'primary' => '#6B4423',
                'primary_fg' => '#FBF7F2',
                'accent' => '#C17B3F',
                'font_heading' => $defaultFontHeading,
                'font_body' => $defaultFontBody,
            ],
            'zeytin' => [
                '__label' => 'Zeytin Yeşili',
                'bg' => '#F7F8F4',
                'surface' => '#FFFFFF',
                'surface_alt' => '#ECEFE4',
                'text' => '#1F2B1A',
                'text_muted' => '#6B7563',
                'border' => '#D8DDCC',
                'primary' => '#3E4D33',
                'primary_fg' => '#F7F8F4',
                'accent' => '#8AA66D',
                'font_heading' => $defaultFontHeading,
                'font_body' => $defaultFontBody,
            ],
        ];
    }
}
