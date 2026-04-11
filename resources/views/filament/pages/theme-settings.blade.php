<x-filament-panels::page>
    <x-filament-panels::form wire:submit="save">
        {{ $this->form }}

        {{-- Canlı WCAG kontrast raporu --}}
        @php
            $report = $this->getContrastReport();
        @endphp

        @if (count(array_filter($report, fn ($r) => $r['ratio'] > 0)))
            <div class="fi-section rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5">
                <div class="mb-4 flex items-center gap-2">
                    <x-heroicon-o-eye class="h-5 w-5 text-gray-500" />
                    <h3 class="text-base font-semibold text-gray-950">
                        Erişilebilirlik / Kontrast Kontrolü
                    </h3>
                </div>
                <p class="mb-4 text-sm text-gray-500">
                    WCAG AA standardı normal metin için <strong>4.5:1</strong> kontrast oranı gerektirir.
                    Aşağıdaki oranların yeşil olması tavsiye edilir; kırmızı olan kombinasyonlar okunurluk sorunu yaratabilir.
                </p>
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                    @foreach ($report as $key => $item)
                        <div class="flex items-center justify-between rounded-lg border border-gray-200 p-4">
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-950">{{ $item['label'] }}</p>
                                <p class="mt-1 text-xs text-gray-500">Oran: {{ $item['ratio'] }}:1</p>
                            </div>
                            <div>
                                @if ($item['pass'])
                                    <span class="inline-flex items-center gap-1 rounded-full bg-green-50 px-3 py-1 text-xs font-medium text-green-700 ring-1 ring-green-600/20">
                                        <x-heroicon-s-check-circle class="h-4 w-4" />
                                        Geçer
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 rounded-full bg-red-50 px-3 py-1 text-xs font-medium text-red-700 ring-1 ring-red-600/20">
                                        <x-heroicon-s-exclamation-triangle class="h-4 w-4" />
                                        Zayıf
                                    </span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Canlı mini önizleme --}}
        @php
            $d = $this->data ?? [];
        @endphp

        <div class="fi-section rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5">
            <div class="mb-4 flex items-center gap-2">
                <x-heroicon-o-swatch class="h-5 w-5 text-gray-500" />
                <h3 class="text-base font-semibold text-gray-950">Canlı Önizleme</h3>
            </div>
            <p class="mb-4 text-sm text-gray-500">
                Bu kutu, seçtiğiniz tema renklerinin ön yüzde nasıl görüneceğini kabaca simüle eder.
                Değişiklikleri kalıcı hale getirmek için sayfanın üst kısmındaki "Kaydet" butonuna basınız.
            </p>

            <div class="overflow-hidden rounded-lg border" style="border-color: {{ $d['border'] ?? '#D9D2C4' }};">
                <div class="p-8 sm:p-12" style="background-color: {{ $d['bg'] ?? '#F5F1EA' }}; color: {{ $d['text'] ?? '#0B1F3A' }}; font-family: {{ $d['font_body'] ?? "'Inter', sans-serif" }};">
                    <p class="text-xs font-semibold uppercase" style="letter-spacing: 0.2em; color: {{ $d['text_muted'] ?? '#8C8B86' }};">
                        Önizleme
                    </p>
                    <h2 class="mt-3 text-3xl sm:text-4xl" style="font-family: {{ $d['font_heading'] ?? "'Cormorant Garamond', serif" }}; font-weight: 500; line-height: 1.1;">
                        Hukuki süreçlerinizde
                        <span style="color: {{ $d['accent'] ?? '#A88A55' }}; font-style: italic;">güvenilir</span>
                        bir yaklaşım.
                    </h2>
                    <p class="mt-5 max-w-xl text-sm leading-relaxed" style="color: {{ $d['text_muted'] ?? '#8C8B86' }};">
                        Bireysel ve kurumsal müvekkillere etik ve şeffaf bir anlayışla hukuki
                        danışmanlık sunar. Her dosyayı titizlikle değerlendirir, mevzuat ve
                        içtihat analizleri ışığında süreçleri yönetir.
                    </p>
                    <div class="mt-6 flex flex-wrap gap-3">
                        <span class="inline-flex items-center rounded-md px-5 py-2.5 text-sm font-medium" style="background-color: {{ $d['primary'] ?? '#0B1F3A' }}; color: {{ $d['primary_fg'] ?? '#F5F1EA' }};">
                            Görüşme Talebi
                        </span>
                        <span class="inline-flex items-center rounded-md px-5 py-2.5 text-sm font-medium" style="background-color: {{ $d['surface'] ?? '#FFFFFF' }}; color: {{ $d['text'] ?? '#0B1F3A' }}; border: 1px solid {{ $d['border'] ?? '#D9D2C4' }};">
                            Faaliyet Alanları
                        </span>
                    </div>
                </div>
                <div class="p-6" style="background-color: {{ $d['surface_alt'] ?? '#EFE9DE' }}; color: {{ $d['text'] ?? '#0B1F3A' }}; font-family: {{ $d['font_body'] ?? "'Inter', sans-serif" }};">
                    <p class="text-xs font-semibold uppercase" style="letter-spacing: 0.14em; color: {{ $d['text_muted'] ?? '#8C8B86' }};">
                        Alternatif yüzey
                    </p>
                    <p class="mt-2 text-sm" style="color: {{ $d['text'] ?? '#0B1F3A' }};">
                        Section tone="accent" ve "surface_alt" alanları bu rengi kullanır.
                    </p>
                </div>
            </div>
        </div>
    </x-filament-panels::form>
</x-filament-panels::page>
