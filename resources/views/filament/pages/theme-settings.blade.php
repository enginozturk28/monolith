<x-filament-panels::page>
    {{--
        Not: Filament admin sayfaları bizim Vite build'imizi yüklemediği için Tailwind
        utility class'ları (grid-cols-*, sm:*, lg:*, gap-*, rounded-* vb.) bu view'da
        güvenilir değil. Sadece inline style ve Filament'in kendi atomik helper'ları
        kullanılır. Bu aynı zamanda koyu mod ile uyumludur (renkler sabit hex).
    --}}

    <form wire:submit="save" style="display:flex; flex-direction:column; gap:1.5rem;">
        {{ $this->form }}

        {{-- Canlı WCAG kontrast raporu --}}
        @php
            $report = $this->getContrastReport();
            $hasAny = count(array_filter($report, fn ($r) => $r['ratio'] > 0)) > 0;
        @endphp

        @if ($hasAny)
            <section style="
                background-color: var(--fi-color-white, #fff);
                border-radius: 0.75rem;
                padding: 1.5rem;
                box-shadow: 0 1px 2px 0 rgba(0,0,0,0.05);
                border: 1px solid rgba(0,0,0,0.05);
            ">
                <div style="display:flex; align-items:center; gap:0.5rem; margin-bottom:1rem;">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:1.25rem; height:1.25rem; color:#6B7280;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                    </svg>
                    <h3 style="font-size:1rem; font-weight:600; color:#111827; margin:0;">
                        Erişilebilirlik / Kontrast Kontrolü
                    </h3>
                </div>
                <p style="font-size:0.875rem; color:#6B7280; margin:0 0 1rem 0; line-height:1.5;">
                    WCAG AA standardı normal metin için <strong>4.5:1</strong> kontrast oranı gerektirir.
                    Aşağıdaki oranların yeşil olması tavsiye edilir; kırmızı olan kombinasyonlar okunurluk sorunu yaratabilir.
                </p>

                <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(280px, 1fr)); gap:0.75rem;">
                    @foreach ($report as $key => $item)
                        <div style="
                            display:flex;
                            align-items:center;
                            justify-content:space-between;
                            gap:1rem;
                            padding:1rem;
                            border:1px solid #E5E7EB;
                            border-radius:0.5rem;
                        ">
                            <div style="flex:1; min-width:0;">
                                <p style="font-size:0.875rem; font-weight:500; color:#111827; margin:0;">
                                    {{ $item['label'] }}
                                </p>
                                <p style="font-size:0.75rem; color:#6B7280; margin:0.25rem 0 0 0;">
                                    Oran: {{ $item['ratio'] }}:1
                                </p>
                            </div>

                            @if ($item['pass'])
                                <span style="
                                    display:inline-flex;
                                    align-items:center;
                                    gap:0.25rem;
                                    background-color:#F0FDF4;
                                    color:#15803D;
                                    border:1px solid #86EFAC;
                                    border-radius:9999px;
                                    padding:0.25rem 0.75rem;
                                    font-size:0.75rem;
                                    font-weight:500;
                                    white-space:nowrap;
                                ">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" style="width:0.875rem; height:0.875rem;">
                                        <path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12Zm13.36-1.814a.75.75 0 1 0-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 0 0-1.06 1.06l2.25 2.25a.75.75 0 0 0 1.14-.094l3.75-5.25Z" clip-rule="evenodd" />
                                    </svg>
                                    Geçer
                                </span>
                            @else
                                <span style="
                                    display:inline-flex;
                                    align-items:center;
                                    gap:0.25rem;
                                    background-color:#FEF2F2;
                                    color:#B91C1C;
                                    border:1px solid #FCA5A5;
                                    border-radius:9999px;
                                    padding:0.25rem 0.75rem;
                                    font-size:0.75rem;
                                    font-weight:500;
                                    white-space:nowrap;
                                ">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" style="width:0.875rem; height:0.875rem;">
                                        <path fill-rule="evenodd" d="M9.401 3.003c1.155-2 4.043-2 5.197 0l7.355 12.748c1.154 2-.29 4.5-2.599 4.5H4.645c-2.309 0-3.752-2.5-2.598-4.5L9.4 3.003ZM12 8.25a.75.75 0 0 1 .75.75v3.75a.75.75 0 0 1-1.5 0V9a.75.75 0 0 1 .75-.75Zm0 8.25a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Z" clip-rule="evenodd" />
                                    </svg>
                                    Zayıf
                                </span>
                            @endif
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

        {{-- Canlı mini önizleme --}}
        @php
            $d = $this->data ?? [];
            $bg = $d['bg'] ?? '#F5F1EA';
            $surface = $d['surface'] ?? '#FFFFFF';
            $surfaceAlt = $d['surface_alt'] ?? '#EFE9DE';
            $text = $d['text'] ?? '#0B1F3A';
            $textMuted = $d['text_muted'] ?? '#8C8B86';
            $border = $d['border'] ?? '#D9D2C4';
            $primary = $d['primary'] ?? '#0B1F3A';
            $primaryFg = $d['primary_fg'] ?? '#F5F1EA';
            $accent = $d['accent'] ?? '#A88A55';
            $fontHeading = $d['font_heading'] ?? "'Cormorant Garamond', serif";
            $fontBody = $d['font_body'] ?? "'Inter', sans-serif";
        @endphp

        <section style="
            background-color:#fff;
            border-radius:0.75rem;
            padding:1.5rem;
            box-shadow:0 1px 2px 0 rgba(0,0,0,0.05);
            border:1px solid rgba(0,0,0,0.05);
        ">
            <div style="display:flex; align-items:center; gap:0.5rem; margin-bottom:1rem;">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:1.25rem; height:1.25rem; color:#6B7280;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.098 19.902a3.75 3.75 0 0 0 5.304 0l6.401-6.402M6.75 21A3.75 3.75 0 0 1 3 17.25V4.125C3 3.504 3.504 3 4.125 3h5.25c.621 0 1.125.504 1.125 1.125v4.072M6.75 21a3.75 3.75 0 0 0 3.75-3.75V8.197M6.75 21h13.125c.621 0 1.125-.504 1.125-1.125v-5.25c0-.621-.504-1.125-1.125-1.125h-4.072M10.5 8.197l2.88-2.88c.438-.439 1.15-.439 1.59 0l3.712 3.713c.44.44.44 1.152 0 1.59l-2.879 2.88M6.75 17.25h.008v.008H6.75v-.008Z" />
                </svg>
                <h3 style="font-size:1rem; font-weight:600; color:#111827; margin:0;">
                    Canlı Önizleme
                </h3>
            </div>
            <p style="font-size:0.875rem; color:#6B7280; margin:0 0 1rem 0; line-height:1.5;">
                Bu kutu, seçtiğiniz tema renklerinin ön yüzde nasıl görüneceğini simüle eder.
                Değişiklikleri kalıcı hale getirmek için sayfanın üst kısmındaki <strong>Kaydet</strong> butonuna basınız.
            </p>

            <div style="border:1px solid {{ $border }}; border-radius:0.5rem; overflow:hidden;">
                <div style="background-color:{{ $bg }}; color:{{ $text }}; padding:3rem 2rem; font-family:{{ $fontBody }};">
                    <h2 style="
                        font-family:{{ $fontHeading }};
                        font-weight:500;
                        font-size:2rem;
                        line-height:1.1;
                        margin:0;
                        color:{{ $text }};
                    ">
                        Hukuki süreçlerinizde
                        <span style="color:{{ $accent }}; font-style:italic;">güvenilir</span>
                        bir yaklaşım.
                    </h2>
                    <p style="
                        margin:1.25rem 0 0 0;
                        max-width:36rem;
                        font-size:0.9375rem;
                        line-height:1.6;
                        color:{{ $textMuted }};
                    ">
                        Bireysel ve kurumsal müvekkillere etik ve şeffaf bir anlayışla hukuki
                        danışmanlık sunar. Her dosyayı titizlikle değerlendirir, mevzuat ve
                        içtihat analizleri ışığında süreçleri yönetir.
                    </p>
                    <div style="display:flex; flex-wrap:wrap; gap:0.75rem; margin-top:1.5rem;">
                        <span style="
                            display:inline-flex;
                            align-items:center;
                            background-color:{{ $primary }};
                            color:{{ $primaryFg }};
                            padding:0.625rem 1.25rem;
                            border-radius:0.375rem;
                            font-size:0.875rem;
                            font-weight:500;
                        ">Görüşme Talebi</span>
                        <span style="
                            display:inline-flex;
                            align-items:center;
                            background-color:{{ $surface }};
                            color:{{ $text }};
                            padding:0.625rem 1.25rem;
                            border-radius:0.375rem;
                            font-size:0.875rem;
                            font-weight:500;
                            border:1px solid {{ $border }};
                        ">Faaliyet Alanları</span>
                    </div>
                </div>
                <div style="background-color:{{ $surfaceAlt }}; color:{{ $text }}; padding:1.25rem 2rem; font-family:{{ $fontBody }}; border-top:1px solid {{ $border }};">
                    <p style="margin:0; font-size:0.8125rem; color:{{ $textMuted }};">
                        Alternatif yüzey (<code>surface_alt</code>)
                    </p>
                    <p style="margin:0.25rem 0 0 0; font-size:0.875rem; color:{{ $text }};">
                        İkincil kart ve section zeminleri bu tonda render edilir.
                    </p>
                </div>
            </div>
        </section>
    </form>
</x-filament-panels::page>
