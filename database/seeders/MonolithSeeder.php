<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\ContactMessage;
use App\Models\Faq;
use App\Models\FaqCategory;
use App\Models\Page;
use App\Models\Service;
use App\Models\Setting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Monolith ana seed — Loğoğlu Hukuk Bürosu.
 *
 * Tüm gerçek müşteri bilgileri, faaliyet alanları, default tema ve
 * örnek içerikler burada tek merkezde tutulur. Idempotent: tekrar tekrar
 * çalıştırılabilir, upsert mantığıyla çalışır.
 *
 * IMPORTANT: Bu seed'de yer alan tüm metinler reklam dili kurallarına
 * uygundur. "en iyi", "garantili", "lider" vb. ifadeler kullanılmaz.
 */
class MonolithSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedSettings();
        $this->seedServices();
        $this->seedFaqs();
        $this->seedArticles();
        $this->seedPages();

        Setting::flushCaches();
    }

    /* -----------------------------------------------------------------
     |  Settings (site + theme)
     |------------------------------------------------------------------ */
    private function seedSettings(): void
    {
        // Footer açıklama ve about intro için default zengin metinler
        $footerDescriptionDefault = '2024 yılında kurulan Loğoğlu Hukuk Bürosu, bireysel ve kurumsal müvekkillerine güvenilir, etik ve çözüm odaklı hukuki hizmetler sunmayı amaçlamaktadır.';

        $aboutIntroBodyDefault = <<<'HTML'
<p>2024 yılında Avukat Ethem Kaan Loğoğlu tarafından kurulan Loğoğlu Hukuk Bürosu, bireysel ve kurumsal müvekkillerine güvenilir, etik ve çözüm odaklı hukuki hizmetler sunmayı amaçlamaktadır.</p>
<p>İstanbul merkezli olan büromuz, yalnızca şehir içinde değil, tüm Türkiye'ye yayılmış bir hizmet anlayışıyla hukukun farklı alanlarında profesyonel destek sağlamaktadır. Her dosyayı detaylı bir analizle ele alıyor; şeffaf, anlaşılır ve çözüm odaklı bir yaklaşım benimsiyoruz.</p>
<p>Hukukun karmaşıklığı içinde yalnızca teknik bilgiye değil, aynı zamanda güçlü bir iletişim ve güven ilişkisine de önem veriyoruz.</p>
HTML;

        $founderBioDefault = <<<'HTML'
<p>2023 yılında Atılım Üniversitesi Hukuk Fakültesi'nden mezun oldum. Stajyer avukatlık sürecimi tamamladıktan sonra, Loğoğlu Hukuk Bürosu'nu kurarak aktif olarak avukatlık yapmaktayım.</p>
<p>Akademik bilgi birikimimi pratik uygulamalarla harmanlayarak mevzuat ve içtihat analizi odaklı çalışmalar yürütüyorum. Amacım, müvekkillerime güvenilir ve etkili çözümler sunmaktır.</p>
HTML;

        $site = [
            // Kimlik
            ['key' => 'name', 'value' => 'Loğoğlu Hukuk Bürosu', 'type' => 'text', 'label' => 'Büro Adı'],
            ['key' => 'tagline', 'value' => 'Hukukun farklı alanlarında profesyonel destek', 'type' => 'text', 'label' => 'Slogan'],
            ['key' => 'footer_description', 'value' => $footerDescriptionDefault, 'type' => 'textarea', 'label' => 'Footer Açıklama'],
            ['key' => 'copyright_text', 'value' => 'Tüm hakları saklıdır.', 'type' => 'text', 'label' => 'Copyright Metni (yıl otomatik eklenir)'],

            // Logo ve görseller
            ['key' => 'logo_url', 'value' => null, 'type' => 'image', 'label' => 'Logo'],
            ['key' => 'hero_image_path', 'value' => null, 'type' => 'image', 'label' => 'Anasayfa Hero Görseli'],
            ['key' => 'hero_image_credit', 'value' => null, 'type' => 'text', 'label' => 'Hero Görsel Atıf'],
            ['key' => 'about_image_path', 'value' => null, 'type' => 'image', 'label' => 'Hakkımızda Görseli'],
            ['key' => 'about_image_credit', 'value' => null, 'type' => 'text', 'label' => 'Hakkımızda Görsel Atıf'],

            // İletişim
            ['key' => 'phone', 'value' => '+90 553 647 38 22', 'type' => 'text', 'label' => 'Telefon'],
            ['key' => 'email', 'value' => 'ethemlogoglu@gmail.com', 'type' => 'text', 'label' => 'E-posta'],
            ['key' => 'kep', 'value' => 'ethemkaan.logoglu@hs01.kep.tr', 'type' => 'text', 'label' => 'KEP Adresi'],
            ['key' => 'address', 'value' => 'Fenerbahçe Mahallesi, Itri Dede Sokak No:22/7 Kadıköy/İstanbul', 'type' => 'textarea', 'label' => 'Adres'],
            [
                'key' => 'map_embed_url',
                'value' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3011.95092531232!2d29.03693307652785!3d40.98255542100763!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x14cab9712620d57d%3A0x818d2be1fa4658f!2zTG_En2_En2x1IEh1a3VrIELDvHJvc3U!5e0!3m2!1str!2str!4v1775834413792!5m2!1str!2str',
                'type' => 'textarea',
                'label' => 'Google Maps Embed URL',
            ],

            // Sosyal medya
            ['key' => 'linkedin_url', 'value' => 'https://www.linkedin.com/in/ethemlogoglu/', 'type' => 'text', 'label' => 'LinkedIn URL'],
            ['key' => 'whatsapp_url', 'value' => null, 'type' => 'text', 'label' => 'WhatsApp URL (wa.me/...)'],
            ['key' => 'instagram_url', 'value' => null, 'type' => 'text', 'label' => 'Instagram URL'],
            ['key' => 'x_url', 'value' => null, 'type' => 'text', 'label' => 'X (Twitter) URL'],

            // Hakkımızda içerikleri
            ['key' => 'about_intro_body', 'value' => $aboutIntroBodyDefault, 'type' => 'textarea', 'label' => 'Hakkımızda — Büro Tanıtım Metni (HTML)'],
            ['key' => 'founder_bio', 'value' => $founderBioDefault, 'type' => 'textarea', 'label' => 'Hakkımızda — Kurucu Avukat Biyografisi (HTML)'],

            // Sayfa görünürlük toggleları
            ['key' => 'show_faq_page', 'value' => '0', 'type' => 'boolean', 'label' => 'SSS Sayfasını Göster'],

            // Ana sayfa hero bölümü
            ['key' => 'hero_title', 'value' => 'Hukuki süreçlerinizde <em>güvenilir</em> ve çözüm odaklı bir yaklaşım.', 'type' => 'text', 'label' => 'Hero Başlık (HTML destekli, <em> ile vurgu)'],
            ['key' => 'hero_description', 'value' => null, 'type' => 'textarea', 'label' => 'Hero Açıklama Metni (boşsa büro adı + varsayılan metin)'],

            // Ana sayfa süreç bölümü ("Her dosya, aynı titizlikle yönetilir")
            ['key' => 'process_title', 'value' => 'Her dosya, aynı titizlikle yönetilir', 'type' => 'text', 'label' => 'Süreç Bölümü Başlık'],
            ['key' => 'process_step_1_title', 'value' => 'Değerlendirme', 'type' => 'text', 'label' => 'Süreç Adım 1 — Başlık'],
            ['key' => 'process_step_1_text', 'value' => 'Her dosya detaylı bir hukuki durum analizi ile ele alınır. İlk görüşmede müvekkilin beklentisi ve dosyanın niteliği netleştirilir.', 'type' => 'textarea', 'label' => 'Süreç Adım 1 — Açıklama'],
            ['key' => 'process_step_2_title', 'value' => 'Yönlendirme', 'type' => 'text', 'label' => 'Süreç Adım 2 — Başlık'],
            ['key' => 'process_step_2_text', 'value' => 'Müvekkile süreç, olası senaryolar, alternatif çözümler ve tahmini maliyet hakkında şeffaf bilgi sunulur.', 'type' => 'textarea', 'label' => 'Süreç Adım 2 — Açıklama'],
            ['key' => 'process_step_3_title', 'value' => 'Takip', 'type' => 'text', 'label' => 'Süreç Adım 3 — Başlık'],
            ['key' => 'process_step_3_text', 'value' => 'Dosyanın her aşaması düzenli olarak raporlanır. Acil gelişmelerde müvekkille anında iletişim kurulur.', 'type' => 'textarea', 'label' => 'Süreç Adım 3 — Açıklama'],
        ];

        // Mevcut kullanıcı-yönetimli değerleri koru — seed yeniden çalıştırıldığında
        // panelden yüklenmiş logo, görsel veya kullanıcının düzenlediği metinleri
        // üzerine yazmayalım.
        $preserveKeys = [
            'logo_url',
            'hero_image_path', 'hero_image_credit',
            'about_image_path', 'about_image_credit',
            'footer_description', 'copyright_text',
            'about_intro_body', 'founder_bio',
            'whatsapp_url', 'instagram_url', 'x_url',
            'show_faq_page',
            'hero_title', 'hero_description',
            'process_title',
            'process_step_1_title', 'process_step_1_text',
            'process_step_2_title', 'process_step_2_text',
            'process_step_3_title', 'process_step_3_text',
        ];

        foreach ($site as $i => $row) {
            if (in_array($row['key'], $preserveKeys, true)) {
                $existing = Setting::where('group', 'site')->where('key', $row['key'])->first();
                if ($existing && $existing->value) {
                    continue;
                }
            }

            Setting::set('site', $row['key'], $row['value'], [
                'type' => $row['type'],
                'label' => $row['label'],
                'encrypted' => false,
                'sort_order' => $i,
            ]);
        }

        $theme = [
            ['key' => 'bg', 'value' => '#F5F1EA', 'type' => 'color', 'label' => 'Sayfa Arka Planı'],
            ['key' => 'surface', 'value' => '#FFFFFF', 'type' => 'color', 'label' => 'Yüzey (Kart)'],
            ['key' => 'surface_alt', 'value' => '#EFE9DE', 'type' => 'color', 'label' => 'İkincil Yüzey'],
            ['key' => 'text', 'value' => '#0B1F3A', 'type' => 'color', 'label' => 'Ana Metin'],
            ['key' => 'text_muted', 'value' => '#8C8B86', 'type' => 'color', 'label' => 'Yardımcı Metin'],
            ['key' => 'border', 'value' => '#D9D2C4', 'type' => 'color', 'label' => 'Ayraç / Çerçeve'],
            ['key' => 'primary', 'value' => '#0B1F3A', 'type' => 'color', 'label' => 'Birincil Renk'],
            ['key' => 'primary_fg', 'value' => '#F5F1EA', 'type' => 'color', 'label' => 'Birincil Üzeri Metin'],
            ['key' => 'accent', 'value' => '#A88A55', 'type' => 'color', 'label' => 'Vurgu Rengi'],
            ['key' => 'font_heading', 'value' => "'Cormorant Garamond', 'Lora', Georgia, ui-serif, serif", 'type' => 'text', 'label' => 'Başlık Fontu'],
            ['key' => 'font_body', 'value' => "'Inter', ui-sans-serif, system-ui, sans-serif", 'type' => 'text', 'label' => 'Gövde Fontu'],
        ];

        foreach ($theme as $i => $row) {
            Setting::set('theme', $row['key'], $row['value'], [
                'type' => $row['type'],
                'label' => $row['label'],
                'encrypted' => false,
                'sort_order' => $i,
            ]);
        }
    }

    /* -----------------------------------------------------------------
     |  Services — 12 faaliyet alanı
     |------------------------------------------------------------------ */
    private function seedServices(): void
    {
        $services = [
            [
                'title' => 'Ceza Hukuku',
                'icon' => 'scale',
                'summary' => 'Soruşturma ve kovuşturma süreçlerinin titizlikle yürütülmesi.',
                'body' => <<<'HTML'
<p>Ceza hukuku alanında müvekkillerimize soruşturma ve kovuşturma süreçlerinin tüm aşamalarında hukuki destek sunuyoruz. Savunma hakkının etkin kullanılabilmesi için her dosyayı güncel mevzuat ve içtihat analiziyle değerlendiriyor, usule ilişkin haklarınızı titizlikle koruyoruz.</p>
<h3>Hizmet Kapsamı</h3>
<ul>
<li>İfade ve sorgu süreçlerinde müdafilik</li>
<li>Gözaltı ve tutukluluğa itiraz</li>
<li>İddianame değerlendirmesi ve savunma hazırlığı</li>
<li>Asliye, ağır ceza ve istinaf/temyiz yargılamalarının takibi</li>
<li>Koruma tedbiri ve adli kontrol itirazları</li>
</ul>
<p>Her dosya, olayın özelliğine göre ayrı bir strateji ile ele alınır. Delillerin usule uygun şekilde toplanması, tanık beyanlarının değerlendirilmesi ve duruşma öncesi yazılı savunma hazırlığı sürecin kritik aşamalarıdır.</p>
HTML,
            ],
            [
                'title' => 'Aile Hukuku',
                'icon' => 'heart-handshake',
                'summary' => 'Boşanma, velayet, nafaka ve mal rejimi uyuşmazlıkları.',
                'body' => <<<'HTML'
<p>Aile hukuku, hassasiyet gerektiren bir alan olup bireylerin özel yaşamlarına doğrudan dokunur. Bu nedenle dosyalar, hem hukuki hem insani boyutuyla birlikte değerlendirilir.</p>
<h3>Başlıca Çalışma Alanları</h3>
<ul>
<li>Anlaşmalı ve çekişmeli boşanma davaları</li>
<li>Velayet, kişisel ilişki kurulması ve iştirak nafakası</li>
<li>Tedbir, yoksulluk ve yoksunluk nafakaları</li>
<li>Edinilmiş mallara katılma rejimi tasfiyesi</li>
<li>Aile içi koruma tedbirleri (6284 sayılı Kanun)</li>
<li>Tanıma ve tenfiz (yabancı mahkeme kararlarının Türkiye'de uygulanması)</li>
</ul>
<p>Süreçlerde müvekkillerimizin menfaatleri kadar, ailelerin uzun vadeli ilişkileri de gözetilerek çözüm odaklı bir yaklaşım benimsenir. Mümkün olan dosyalarda arabuluculuk ve uzlaşma yolları da değerlendirilir.</p>
HTML,
            ],
            [
                'title' => 'Miras Hukuku',
                'icon' => 'scroll',
                'summary' => 'Vasiyetname, miras paylaşımı ve tenkis davaları.',
                'body' => <<<'HTML'
<p>Miras hukuku, hem aile hukukuyla hem de mülkiyet ve borçlar hukukuyla iç içe olduğundan her dosyayı bütünsel bir perspektifle ele alıyoruz.</p>
<h3>Dava ve Danışmanlık Alanları</h3>
<ul>
<li>Mirasçılık belgesi (veraset ilamı) alınması</li>
<li>Tereke tespiti ve paylaşımı davaları</li>
<li>Vasiyetname düzenlemesi, iptali ve tenkis davaları</li>
<li>Muris muvazaası nedeniyle tapu iptali davaları</li>
<li>Mirasın reddi ve mirasta iade davaları</li>
<li>Saklı pay ve tenkis hesapları</li>
</ul>
<p>Miras süreçlerinde en önemli adım, terekeyi doğru ve eksiksiz tespit etmektir. Banka hesapları, gayrimenkuller, şirket payları ve alacakların bütüncül bir envanteri çıkarılarak hak kayıpları önlenir.</p>
HTML,
            ],
            [
                'title' => 'Marka ve Patent Hukuku',
                'icon' => 'badge-check',
                'summary' => 'Marka tescili, hükümsüzlük ve tecavüz davaları.',
                'body' => <<<'HTML'
<p>Sınai mülkiyet hakları, markalaşma sürecindeki her işletme için stratejik bir öneme sahiptir. Tescil öncesi yapılan doğru bir benzerlik araştırması, ileride yaşanabilecek hükümsüzlük ve itiraz süreçlerinin büyük kısmını önler.</p>
<h3>Hizmet Kapsamı</h3>
<ul>
<li>Marka, patent, faydalı model ve tasarım başvuruları</li>
<li>TÜRKPATENT nezdinde itiraz ve yanıt süreçleri</li>
<li>Marka hükümsüzlük ve iptal davaları</li>
<li>Marka ve patent tecavüzü davaları, tespit ve el koyma</li>
<li>Lisans ve devir sözleşmelerinin hazırlanması</li>
<li>Uluslararası WIPO-Madrid Protokolü başvuruları</li>
</ul>
<p>Sınai mülkiyet dosyalarında hız kritik bir faktördür. Tecavüz tespit edildiğinde ihtiyati tedbir ve delil tespiti talepleriyle hakların korunması sağlanır.</p>
HTML,
            ],
            [
                'title' => 'Gayrimenkul Hukuku',
                'icon' => 'home',
                'summary' => 'Tapu, kira, kat mülkiyeti ve imar uyuşmazlıkları.',
                'body' => <<<'HTML'
<p>Gayrimenkul işlemleri, tarafların en değerli varlıklarına ilişkin olması sebebiyle hukuki risklerin önceden belirlenmesi büyük önem taşır. Taşınmazlara ilişkin her işlem öncesinde kapsamlı bir hukuki durum değerlendirmesi yapılması, ileride telafisi güç kayıpları engeller.</p>
<h3>Çalışma Alanları</h3>
<ul>
<li>Gayrimenkul alım-satım ve kiralama sözleşmeleri</li>
<li>Tapu iptali ve tescili davaları</li>
<li>Kira bedelinin tespiti ve tahliye davaları</li>
<li>Kat mülkiyeti ve yönetim plan uyuşmazlıkları</li>
<li>İmar davaları ve kamulaştırma süreçleri</li>
<li>Ön alım (şufa) hakkı ve ecrimisil talepleri</li>
</ul>
<p>Özellikle kooperatif ve konut projelerinde taraflar arasında çıkan sorunlar, uzun soluklu yargılama süreçlerine yol açabilir. Uyuşmazlık öncesinde detaylı bir sözleşme incelemesi risklerin büyük kısmını engeller.</p>
HTML,
            ],
            [
                'title' => 'Vergi Hukuku',
                'icon' => 'receipt',
                'summary' => 'Vergi/ceza ihbarnameleri, uzlaşma ve dava süreçleri.',
                'body' => <<<'HTML'
<p>Vergi hukuku, sürekli değişen mevzuatı nedeniyle dikkat ve güncel takip gerektirir. Hem bireysel mükellefler hem de kurumsal müvekkiller için dosya bazlı ayrıntılı değerlendirme yapıyoruz.</p>
<h3>Hizmet Alanları</h3>
<ul>
<li>Vergi incelemesi süreçlerinde savunma hazırlığı</li>
<li>Vergi ve ceza ihbarnamelerine karşı itiraz</li>
<li>Uzlaşma başvuruları ve müzakere süreçleri</li>
<li>Vergi mahkemesi ve Danıştay davaları</li>
<li>KDV, gelir, kurumlar ve damga vergisi uyuşmazlıkları</li>
<li>Vergi planlaması konularında kurumsal danışmanlık</li>
</ul>
<p>İhbarname tebliğ edildikten sonra kanuni süreler son derece kritiktir. 30 günlük itiraz süresi geçirildiğinde hak kayıpları yaşanabileceğinden süreç derhal başlatılmalıdır.</p>
HTML,
            ],
            [
                'title' => 'Sözleşmeler ve Borçlar Hukuku',
                'icon' => 'file-signature',
                'summary' => 'Sözleşme hazırlama, inceleme ve uyuşmazlık çözümü.',
                'body' => <<<'HTML'
<p>Sözleşmeler, tarafların hak ve yükümlülüklerini belirleyen temel belgelerdir. İyi hazırlanmış bir sözleşme, ileride çıkabilecek uyuşmazlıkların büyük kısmını önler veya çözüm yolunu açıklıkla gösterir.</p>
<h3>Hizmet Kapsamı</h3>
<ul>
<li>Ticari, hizmet, eser ve distribütörlük sözleşmeleri</li>
<li>Kira, satış, bağışlama ve vekalet sözleşmeleri</li>
<li>Gizlilik (NDA), rekabet yasağı ve fikri mülkiyet devir sözleşmeleri</li>
<li>Sözleşmeden doğan alacak davaları</li>
<li>Haksız fiil ve sebepsiz zenginleşme uyuşmazlıkları</li>
<li>Mevcut sözleşmelerin incelenmesi ve risk analizi</li>
</ul>
<p>Hazırlanan her sözleşmede müvekkilin menfaatlerinin yanı sıra olası uyuşmazlıkları önleyici hükümlerin de bulunmasına özen gösterilir. Uygulanacak hukuk, yetkili mahkeme, cezai şart ve fesih şartları özellikle dikkatle ele alınır.</p>
HTML,
            ],
            [
                'title' => 'İş Hukuku',
                'icon' => 'briefcase',
                'summary' => 'İşçi-işveren uyuşmazlıkları ve iş güvencesi.',
                'body' => <<<'HTML'
<p>İş hukuku alanında hem işçi hem işveren tarafına danışmanlık ve dava takibi hizmeti sunuyoruz. İşçi-işveren ilişkisinin sona erme aşaması, hukuki açıdan en riskli noktadır; bu sebeple iş ilişkisinin başlangıcından itibaren sözleşme ve prosedürlerin mevzuata uygun yürütülmesi kritik önem taşır.</p>
<h3>Başlıca Çalışma Alanları</h3>
<ul>
<li>Bireysel ve toplu iş sözleşmelerinin hazırlanması</li>
<li>İşe iade davaları ve haksız fesih incelemesi</li>
<li>İhbar, kıdem, yıllık izin ve fazla mesai alacakları</li>
<li>Mobbing ve kişilik haklarına yönelik davalar</li>
<li>İş kazası ve meslek hastalığı tazminatları</li>
<li>Arabuluculuk süreçlerinin yönetimi</li>
</ul>
<p>4857 sayılı İş Kanunu kapsamında zorunlu arabuluculuk pek çok dosya için ön şart haline gelmiştir. Arabuluculuk aşamasında da tarafların menfaatlerini koruyacak şekilde etkin bir temsil sağlanır.</p>
HTML,
            ],
            [
                'title' => 'Ticaret Hukuku',
                'icon' => 'building-2',
                'summary' => 'Şirket kuruluşu, sözleşme ve kurumsal danışmanlık.',
                'body' => <<<'HTML'
<p>Ticaret hukuku, işletmelerin kuruluşundan tasfiyesine kadar tüm aşamalarında hukuki altyapıyı oluşturur. Kurumsal müvekkiller için sürekli danışmanlık modeli ile önleyici hukuk anlayışı benimsenir.</p>
<h3>Hizmet Alanları</h3>
<ul>
<li>Anonim, limited ve adi şirket kuruluşları</li>
<li>Ana sözleşme hazırlama ve tadil işlemleri</li>
<li>Pay devirleri ve pay sahipleri sözleşmeleri</li>
<li>Genel kurul ve yönetim kurulu toplantılarının organizasyonu</li>
<li>Şirket birleşme, bölünme ve devralma süreçleri</li>
<li>Haksız rekabet ve ticari sırların korunması</li>
<li>Kıymetli evrak (çek, bono, poliçe) uyuşmazlıkları</li>
</ul>
<p>Sürekli danışmanlık modelinde, işletmenin aldığı günlük hukuki kararların her aşamasında avukat görüşü alınabilir. Bu yaklaşım, dava aşamasına geçmeden risklerin önlenmesini sağlar.</p>
HTML,
            ],
            [
                'title' => 'Tazminat Hukuku',
                'icon' => 'life-buoy',
                'summary' => 'Maddi-manevi tazminat ve trafik kazaları.',
                'body' => <<<'HTML'
<p>Haksız fiilden kaynaklanan maddi ve manevi zararların tazmini, hukuki ilişkide en sık karşılaşılan uyuşmazlıklardandır. Tazminat davalarında zararın doğru tespit edilmesi ve delillendirme, sürecin en önemli aşamasıdır.</p>
<h3>Çalışma Alanları</h3>
<ul>
<li>Trafik kazalarından kaynaklanan tazminat davaları</li>
<li>İş kazası ve meslek hastalığı tazminatları</li>
<li>Hekim hatası (malpraktis) ve hastane sorumluluğu</li>
<li>Kişilik haklarına saldırıdan doğan manevi tazminat</li>
<li>Destekten yoksun kalma tazminatı</li>
<li>Sözleşmeden doğan tazminat talepleri</li>
</ul>
<p>Bilirkişi incelemelerinin doğru yönlendirilmesi sürecin sonucunu doğrudan etkiler. Özellikle sigorta şirketlerine karşı açılan davalarda poliçe şartlarının dikkatle incelenmesi gerekir.</p>
HTML,
            ],
            [
                'title' => 'Yabancılar Hukuku',
                'icon' => 'globe-2',
                'summary' => 'Oturma izni, vatandaşlık ve çalışma izni süreçleri.',
                'body' => <<<'HTML'
<p>Türkiye'de yaşayan yabancı müvekkillerimize oturma izni, çalışma izni, Türk vatandaşlığı kazanımı ve sınır dışı etme işlemlerine karşı itirazlar gibi konularda hukuki destek veriyoruz.</p>
<h3>Hizmet Kapsamı</h3>
<ul>
<li>Kısa dönem, aile ve öğrenci ikamet izinleri</li>
<li>Uzun dönem ve çalışma iznine bağlı ikamet</li>
<li>Çalışma izni başvuruları ve itiraz süreçleri</li>
<li>Türk vatandaşlığı kazanma yolları (istisnai, olağan, yatırım)</li>
<li>Sınır dışı etme kararlarına karşı iptal davaları</li>
<li>Uluslararası koruma başvuruları</li>
</ul>
<p>Yabancılar hukuku dosyalarında uluslararası özel hukuk kapsamındaki evlilik, velayet ve miras uyuşmazlıklarında da danışmanlık hizmeti sunulur. Dosyalarda gerekli durumlarda tercüman desteği sağlanır.</p>
HTML,
            ],
            [
                'title' => 'İcra ve İflas Hukuku',
                'icon' => 'gavel',
                'summary' => 'İcra takibi, haciz ve iflas süreçleri.',
                'body' => <<<'HTML'
<p>Alacak tahsili süreçlerinde hem hukuki hem pratik çözümler üretmeye özen gösterilir. İcra takibinin doğru başlatılması ve takip yöntemi seçimi, sürecin hızı ve sonucu için belirleyicidir.</p>
<h3>Çalışma Alanları</h3>
<ul>
<li>İlamlı ve ilamsız icra takipleri</li>
<li>Kambiyo senetlerine özgü haciz yolu</li>
<li>İpoteğin ve rehinin paraya çevrilmesi</li>
<li>İtirazın iptali ve itirazın kaldırılması davaları</li>
<li>Menfi tespit ve istirdat davaları</li>
<li>Konkordato ve iflas süreçleri</li>
<li>İhtiyati haciz ve tedbir kararları</li>
</ul>
<p>Alacaklı müvekkiller için hızlı ve sonuç odaklı bir tahsilat stratejisi izlenirken, borçlu tarafında ise hukuka aykırı takiplere karşı etkin bir savunma sağlanır.</p>
HTML,
            ],
        ];

        foreach ($services as $i => $row) {
            Service::updateOrCreate(
                ['slug' => Str::slug($row['title'])],
                [
                    'title' => $row['title'],
                    'icon' => $row['icon'],
                    'summary' => $row['summary'],
                    'body' => $row['body'],
                    'meta_title' => $row['title'].' — Loğoğlu Hukuk Bürosu',
                    'meta_description' => $row['summary'],
                    'is_published' => true,
                    'sort_order' => $i,
                ]
            );
        }
    }

    /* -----------------------------------------------------------------
     |  FAQ — kategorili
     |------------------------------------------------------------------ */
    private function seedFaqs(): void
    {
        $categories = [
            ['name' => 'Genel Sorular', 'icon' => 'help-circle', 'faqs' => [
                [
                    'q' => 'İlk görüşme için randevu nasıl alabilirim?',
                    'a' => '<p>İletişim formumuzdan veya telefonla arayarak randevu talebi oluşturabilirsiniz. Talebinizi aldıktan sonra genellikle <strong>1-2 iş günü içinde</strong> tarafınıza dönüş sağlanır ve uygun görüşme zamanı belirlenir.</p>',
                ],
                [
                    'q' => 'Görüşmeler hangi dilde yapılır?',
                    'a' => '<p>Görüşmelerimiz Türkçe yürütülmektedir. Yabancılar hukuku kapsamındaki dosyalarda, gerekli durumlarda <strong>tercüman desteği</strong> sağlanabilir. Bu durumda tercüman ücreti taraflarca ayrıca değerlendirilir.</p>',
                ],
                [
                    'q' => 'Dosyamın durumu hakkında nasıl bilgi alabilirim?',
                    'a' => '<p>Müvekkillerimize dosyanın ilerleyişine dair düzenli bilgilendirme yapılır. Dosya gelişmelerinde e-posta veya telefon yoluyla bildirim sağlanır; acil durumlarda doğrudan iletişim kurabilirsiniz.</p>',
                ],
                [
                    'q' => 'Başka ildeki dosyam için de destek alabilir miyim?',
                    'a' => '<p>Evet. İstanbul merkezli olmamıza rağmen tüm Türkiye\'deki mahkemelerde dava ve icra süreçleri takip edilebilir. Uzak illerdeki duruşmalarda, gerektiğinde <strong>UYAP üzerinden SEGBİS</strong> bağlantısıyla katılım sağlanabilir ya da ilgili ilde meslektaşlarımızla koordinasyon kurulur.</p>',
                ],
            ]],
            ['name' => 'Ücretlendirme', 'icon' => 'receipt', 'faqs' => [
                [
                    'q' => 'Vekâlet ücretleri nasıl belirlenir?',
                    'a' => '<p>Vekâlet ücretleri, <strong>Türkiye Barolar Birliği Asgari Ücret Tarifesi</strong> esas alınarak dosyanın niteliği, süresi ve hukuki uyuşmazlığın boyutuna göre belirlenir. Ücret, vekâlet sözleşmesi ile yazılı olarak kayıt altına alınır; ek bir işlem yapılmadığı sürece sonradan değiştirilmez.</p>',
                ],
                [
                    'q' => 'İlk görüşme ücretli midir?',
                    'a' => '<p>İlk danışmanlık görüşmeleri, dosyanın değerlendirilmesi amacıyla yapılır. Ücretlendirme, görüşmenin içeriği ve harcanan zamana göre karşılıklı mutabakatla belirlenir ve her koşulda önceden bildirilir.</p>',
                ],
                [
                    'q' => 'Ödemelerde taksit imkânı var mı?',
                    'a' => '<p>Dosyanın niteliğine ve sürecin uzunluğuna göre ödeme taksit düzeninde yapılabilir. Bu husus, vekâlet sözleşmesinin imzalanması sırasında netleştirilerek sözleşmeye yazılır.</p>',
                ],
            ]],
            ['name' => 'Dava Süreçleri', 'icon' => 'scale', 'faqs' => [
                [
                    'q' => 'Bir dava ne kadar sürer?',
                    'a' => '<p>Dava süreleri; mahkeme yoğunluğu, uyuşmazlığın niteliği, delil toplama süreci ve istinaf/temyiz aşamalarına göre farklılık gösterir. Örneğin basit alacak davaları birkaç ay içinde sonuçlanabilirken, çekişmeli boşanma veya tapu iptali davaları <strong>1-3 yıl</strong> sürebilir.</p><p>Her dosya için tahmini süre bilgisi, ilk değerlendirme sonrasında müvekkile sunulur.</p>',
                ],
                [
                    'q' => 'Duruşmalara katılmam zorunlu mu?',
                    'a' => '<p>Dosyanızın niteliğine göre duruşmalara katılmanız gerekebilir veya vekâleten takip mümkündür. Özellikle aile hukuku dosyalarında tarafların beyanı alınması gerektiğinden, bazı duruşmalarda katılım zorunlu olabilir. Her dosya için ayrı değerlendirme yapılarak müvekkil bilgilendirilir.</p>',
                ],
                [
                    'q' => 'Mahkeme kararı sonrası süreç nasıl işler?',
                    'a' => '<p>İlk derece mahkemesi kararının ardından <strong>istinaf</strong> aşamasına geçilebilir. İstinaf kararı kesin değilse <strong>temyiz</strong> de başvurulabilecek bir yoldur. Karar kesinleştiğinde icra takibi, tapu tescili ya da diğer icrai işlemler başlar. Her aşama için ayrı bir strateji belirlenerek müvekkil bilgilendirilir.</p>',
                ],
            ]],
        ];

        foreach ($categories as $catIdx => $cat) {
            $category = FaqCategory::updateOrCreate(
                ['slug' => Str::slug($cat['name'])],
                [
                    'name' => $cat['name'],
                    'icon' => $cat['icon'],
                    'sort_order' => $catIdx,
                    'is_published' => true,
                ]
            );

            foreach ($cat['faqs'] as $faqIdx => $faq) {
                Faq::updateOrCreate(
                    ['faq_category_id' => $category->id, 'question' => $faq['q']],
                    [
                        'answer' => $faq['a'],
                        'sort_order' => $faqIdx,
                        'is_published' => true,
                    ]
                );
            }
        }
    }

    /* -----------------------------------------------------------------
     |  Makaleler — kategorili ve HTML içerikli
     |------------------------------------------------------------------ */
    private function seedArticles(): void
    {
        // Kategoriler
        $categoryMap = [];
        $cats = [
            ['slug' => 'genel', 'name' => 'Genel', 'sort_order' => 0, 'description' => 'Genel hukuki değerlendirmeler ve gündem yazıları.'],
            ['slug' => 'ticari-hukuk', 'name' => 'Ticari Hukuk', 'sort_order' => 1, 'description' => 'Şirketler, sözleşmeler ve ticari uyuşmazlıklar üzerine yazılar.'],
            ['slug' => 'aile-hukuku', 'name' => 'Aile Hukuku', 'sort_order' => 2, 'description' => 'Evlilik, boşanma, velayet ve nafaka konularına ilişkin yazılar.'],
            ['slug' => 'gayrimenkul', 'name' => 'Gayrimenkul', 'sort_order' => 3, 'description' => 'Tapu, kira, kat mülkiyeti ve imar konularına ilişkin yazılar.'],
        ];

        foreach ($cats as $cat) {
            $category = ArticleCategory::updateOrCreate(
                ['slug' => $cat['slug']],
                [
                    'name' => $cat['name'],
                    'sort_order' => $cat['sort_order'],
                    'description' => $cat['description'],
                ]
            );
            $categoryMap[$cat['slug']] = $category->id;
        }

        // Yazar olarak mevcut ilk kullanıcıyı kullan
        $authorId = \App\Models\User::query()->orderBy('id')->value('id');

        $articles = [
            [
                'category' => 'genel',
                'title' => 'Hukuki Uyuşmazlıklarda Önleyici Yaklaşımın Önemi',
                'excerpt' => 'Hukuki sorunların ortaya çıkmadan önce fark edilmesi, zaman ve maliyet açısından büyük avantaj sağlar. Bu yazıda önleyici hukuk anlayışının temel prensiplerini ele alıyoruz.',
                'body' => <<<'HTML'
<p>Hukuki uyuşmazlıkların önemli bir kısmı, aslında başlangıçta alınabilecek basit önlemlerle engellenebilir niteliktedir. Özellikle ticari ilişkilerde ve sözleşmelerde, tarafların hak ve yükümlülüklerinin net biçimde kayıt altına alınmaması ilerleyen süreçte ciddi anlaşmazlıklara yol açabilmektedir.</p>
<h2>Önleyici Hukuk Nedir?</h2>
<p>Önleyici hukuk anlayışı, müvekkilin karşılaşabileceği olası riskleri önceden tespit ederek hukuki yapıyı buna göre oluşturmayı hedefler. Bu yaklaşım; hem süreç maliyetlerini azaltır hem de olası uyuşmazlıkların karmaşıklaşmasını engeller.</p>
<blockquote>Bir sözleşmenin hazırlık aşamasında harcanan saatler, ileride açılacak bir davanın aylarını ve önemli bir maliyeti önleyebilir.</blockquote>
<h3>Uygulamada Önleyici Hukuk</h3>
<p>Özellikle şu üç alanda önleyici hukuk anlayışının faydası doğrudan görülür:</p>
<ul>
<li><strong>Ticari sözleşmeler:</strong> Tarafların hak ve yükümlülüklerinin net tanımlanması, uyuşmazlık çıkmadan önce çözüm yollarının belirlenmesi.</li>
<li><strong>Aile hukuku anlaşmaları:</strong> Mal rejimi sözleşmeleri ve ön anlaşmalar ile olası çatışmaların önlenmesi.</li>
<li><strong>Gayrimenkul işlemleri:</strong> Tapu kayıtlarının, imar durumunun ve sözleşmelerin önceden detaylı incelenmesi.</li>
</ul>
<h3>Sonuç</h3>
<p>Hukuki bir sorunun doğmadan çözülmesi, hem zaman hem de kaynak açısından en avantajlı yoldur. Karar verme aşamasında hukuki danışmanlık almanın kritik bir farkı: dava açıldıktan sonra savunma yapmak yerine, dava açılmasını önlemek mümkün olur.</p>
HTML,
            ],
            [
                'category' => 'ticari-hukuk',
                'title' => 'Sözleşme Hazırlarken Dikkat Edilmesi Gereken Temel Hususlar',
                'excerpt' => 'Ticari ve bireysel sözleşmelerde tarafların haklarını korumak için dikkat edilmesi gereken maddeleri ve sık yapılan hataları inceliyoruz.',
                'body' => <<<'HTML'
<p>Bir sözleşme hazırlanırken, tarafların karşılıklı irade beyanlarının açık ve anlaşılır biçimde yansıtılması en temel kuraldır. Sözleşmenin taraflar arasında dengeli bir düzen kurması, ileride çıkabilecek uyuşmazlıkların azaltılmasında birinci derecede etkilidir.</p>
<h2>Sözleşmede Bulunması Gereken Temel Unsurlar</h2>
<p>Her tür sözleşmede aşağıdaki unsurların açıkça yer alması gerekir:</p>
<ul>
<li>Sözleşmenin konusu ve amacı</li>
<li>Tarafların tam kimlik ve adres bilgileri</li>
<li>Hak ve yükümlülüklerin detaylı tanımı</li>
<li>İfa yeri, zamanı ve ödeme koşulları</li>
<li>Süre ve fesih hükümleri</li>
<li>Uyuşmazlık halinde uygulanacak hukuk ve yetkili mahkeme</li>
</ul>
<h3>Sıkça Atlanan Hususlar</h3>
<p>Uygulamada, tarafların genellikle göz ardı ettiği ancak uyuşmazlık çıktığında büyük önem kazanan bazı hükümler vardır:</p>
<ol>
<li><strong>Cezai şart:</strong> Sözleşmeye aykırılık halinde uygulanacak ceza miktarının önceden belirlenmesi, alacağın tahsilini hızlandırır.</li>
<li><strong>Gecikme faizi:</strong> Borcun zamanında ödenmemesi durumunda uygulanacak faiz oranının belirlenmesi.</li>
<li><strong>Mücbir sebep:</strong> Tarafların kontrolü dışındaki olaylar için sorumluluğun nasıl belirleneceğine ilişkin düzenleme.</li>
<li><strong>Gizlilik hükümleri:</strong> Ticari sırların ve hassas bilgilerin korunması.</li>
</ol>
<blockquote>Her sözleşme kendine özgü bir içeriğe sahiptir. Standart şablonların birebir uygulanması yerine, somut olayın özelliklerine göre hazırlanması tercih edilmelidir.</blockquote>
<h3>Revizyon ve Güncelleme</h3>
<p>Uzun süreli sözleşmelerin, özellikle ticari işbirliklerinin belirli aralıklarla gözden geçirilmesi önerilir. Mevzuat değişiklikleri ve iş ilişkisinin gelişimi, mevcut sözleşmenin yetersiz kalmasına neden olabilir.</p>
HTML,
            ],
            [
                'category' => 'aile-hukuku',
                'title' => 'Anlaşmalı Boşanmada Protokolün Önemi',
                'excerpt' => 'Anlaşmalı boşanma sürecinde hazırlanan protokol, tarafların haklarını ve yükümlülüklerini belirleyen en önemli belgedir. Protokolde nelere dikkat edilmeli?',
                'body' => <<<'HTML'
<p>Anlaşmalı boşanma, tarafların boşanma iradelerinin ve sonuçları konusunda mutabık kaldıkları bir süreçtir. Bu sürecin temel belgesi, taraflar arasında imzalanan <strong>anlaşmalı boşanma protokolü</strong>dür.</p>
<h2>Protokolde Yer Alması Gereken Unsurlar</h2>
<p>Türk Medeni Kanunu'nun 166. maddesi kapsamında hazırlanan protokollerde aşağıdaki hususların açıkça düzenlenmesi gerekir:</p>
<ul>
<li>Velayetin kime bırakılacağı</li>
<li>Çocukla kişisel ilişki kurulması esasları</li>
<li>İştirak ve yoksulluk nafakası miktarları</li>
<li>Maddi ve manevi tazminat talepleri</li>
<li>Ziynet eşyalarının ve çeyizlerin durumu</li>
<li>Edinilmiş mallara katılma rejiminin tasfiyesi</li>
</ul>
<h3>Çocuğun Yüksek Yararı</h3>
<p>Velayet ve kişisel ilişki hususlarında hâkim, tarafların anlaşmasına rağmen çocuğun yararını esas alarak değişiklik yapabilir. Bu nedenle protokol hazırlanırken çocukların ihtiyaçları ve eğitim durumu dikkate alınmalıdır.</p>
<blockquote>Protokolün kapsamlı ve detaylı hazırlanması, boşanma sonrası çıkabilecek uyuşmazlıkların önüne geçer.</blockquote>
<h3>Nafaka Miktarı Nasıl Belirlenir?</h3>
<p>Nafaka miktarı belirlenirken tarafların gelir durumu, çocuğun ihtiyaçları, tarafların yaşam standardı ve ekonomik gerçeklik birlikte değerlendirilir. Nafakanın her yıl <strong>ÜFE oranında artacağına</strong> dair hüküm koymak ileride tekrar dava açılmasını engeller.</p>
<h3>Mal Rejimi Tasfiyesi</h3>
<p>4721 sayılı Türk Medeni Kanunu'nun 202. maddesi gereğince, aksi kararlaştırılmadıkça evlilikle birlikte edinilmiş mallara katılma rejimi uygulanır. Protokolde mal rejimi tasfiyesi düzenlenmezse ileride ayrıca tasfiye davası açılması gerekebilir.</p>
HTML,
            ],
            [
                'category' => 'gayrimenkul',
                'title' => 'Kira Sözleşmesinde Tarafların Hak ve Yükümlülükleri',
                'excerpt' => 'Konut ve işyeri kiralama sözleşmelerinde hem kiracı hem de kiraya verenin dikkat etmesi gereken temel hususları ele alıyoruz.',
                'body' => <<<'HTML'
<p>Kira sözleşmeleri, gündelik hayatta en sık karşılaşılan sözleşme türlerinden biridir. Taraflar arasında çıkan uyuşmazlıklarda sıklıkla sözleşmenin yetersizliği veya belirsizliği rol oynar.</p>
<h2>Kiracının Temel Yükümlülükleri</h2>
<ul>
<li>Kira bedelini sözleşmede belirlenen tarihte ödemek</li>
<li>Kiralananı özenle kullanmak</li>
<li>Kiralanana zarar vermemek ve olağan kullanım dışı davranışlardan kaçınmak</li>
<li>Sözleşme sona erdiğinde kiralananı teslim almak</li>
</ul>
<h2>Kiraya Verenin Temel Yükümlülükleri</h2>
<ul>
<li>Kiralananı kullanıma elverişli şekilde teslim etmek</li>
<li>Kiralananın ayıplarından sorumlu olmak</li>
<li>Zorunlu onarımları yapmak</li>
<li>Kiracının kullanımına müdahale etmemek</li>
</ul>
<h3>Kira Artışı ve Yasal Sınır</h3>
<p>Türk Borçlar Kanunu'na göre konut kira artışları, bir önceki kira dönemi için açıklanan <strong>ÜFE artış oranı</strong> ile sınırlıdır. Bu oranın üzerinde yapılan artışlar geçersizdir.</p>
<blockquote>Kira bedeli uyuşmazlıklarında sulh hukuk mahkemesi yetkilidir ve taraflara önceden uzlaşma imkânı sunulur.</blockquote>
<h3>Tahliye Davaları</h3>
<p>Kiraya veren, sözleşme süresi sona ermeden kural olarak kiracıyı tahliye edemez. Ancak Borçlar Kanunu'nun 350 ve devamı maddelerinde sayılan bazı haller istisnadır:</p>
<ol>
<li>Kira bedelinin iki kez yazılı ihtar edilmesine rağmen ödenmemesi</li>
<li>Kiralananın gereksinim halinde kullanılacak olması</li>
<li>Kiralananın yeniden inşası veya esaslı tadilatı</li>
<li>Yeni malik tarafından ihtiyaç sebebiyle tahliye talebi</li>
</ol>
<h3>Depozito ve İade</h3>
<p>Depozito, en fazla üç aylık kira bedeli kadar alınabilir ve kiralananın zarar görmemesi kaydıyla sözleşme sonunda iade edilmelidir. Depozito vadeli banka hesabında tutulabilir.</p>
HTML,
            ],
            [
                'category' => 'ticari-hukuk',
                'title' => 'Şirket Kuruluşunda Hukuki Altyapı Nasıl Oluşturulur?',
                'excerpt' => 'Yeni bir şirket kurarken ana sözleşme, pay sahipleri sözleşmesi ve kurumsal yönetim belgeleri üzerinden doğru bir hukuki temel oluşturmanın yolları.',
                'body' => <<<'HTML'
<p>Bir şirketin kuruluş aşamasında atılan adımlar, uzun vadeli kurumsal yapının temelini oluşturur. Hızlı bir kuruluş baskısıyla atlanan bazı detaylar, ilerleyen yıllarda pay sahipleri arasında uyuşmazlıklara veya operasyonel sorunlara yol açabilir.</p>
<h2>Ana Sözleşmenin Hazırlanması</h2>
<p>Türk Ticaret Kanunu'nun 339. maddesi kapsamında, anonim şirket ana sözleşmesinde yer alması zorunlu unsurlar şunlardır:</p>
<ul>
<li>Şirketin unvanı, merkezi ve süresi</li>
<li>Esas sermaye ve her bir payın itibari değeri</li>
<li>Yönetim kurulu üyelerinin seçim usulü</li>
<li>Genel kurulun toplanma ve karar alma şartları</li>
<li>İlan usulü ve denetim</li>
</ul>
<h3>Pay Sahipleri Sözleşmesi</h3>
<p>Ana sözleşmeye ek olarak, pay sahipleri arasında imzalanan pay sahipleri sözleşmesi (<em>shareholders agreement</em>) şirketin günlük işleyişini ve ortakların kendi aralarındaki ilişkileri düzenler. Bu sözleşmede genellikle şu konular yer alır:</p>
<ol>
<li>Rüçhan hakkı ve önalım hükümleri</li>
<li>Oy haklarının kullanımı</li>
<li>Kâr dağıtım politikası</li>
<li>Drag-along ve tag-along hakları</li>
<li>Çıkma ve çıkarılma halleri</li>
</ol>
<h2>Kurumsal Yönetim Belgeleri</h2>
<blockquote>İyi hazırlanmış kurumsal yönetim belgeleri, şirketin büyüme aşamasında yatırımcı güvenini artırır ve due diligence süreçlerini kolaylaştırır.</blockquote>
<p>Kuruluş sonrası hazırlanması önerilen temel belgeler:</p>
<ul>
<li>İmza sirküleri ve yetki belgeleri</li>
<li>Yönetim kurulu iç yönergesi</li>
<li>Ticari sır ve rekabet yasağı politikaları</li>
<li>Veri koruma ve KVKK uyum belgeleri</li>
</ul>
<h3>Kuruluş Sonrası İlk Altı Ay</h3>
<p>Şirket kuruluşundan sonraki ilk altı ay içinde yerine getirilmesi gereken yükümlülükler vardır: ticaret sicil kayıtlarının tamamlanması, vergi dairesi işlemleri, SGK kaydı, gerekli lisans ve izinlerin alınması ve defter onaylatma. Bu sürecin bir avukat ve mali müşavir koordinasyonunda yürütülmesi önerilir.</p>
HTML,
            ],
            [
                'category' => 'genel',
                'title' => 'İcra Takibinde İtiraz Süreci ve Sonuçları',
                'excerpt' => 'Tebliğ edilen bir icra takibine karşı itiraz süreci, sürenin önemi ve itirazın sonuçlarını pratik örneklerle değerlendiriyoruz.',
                'body' => <<<'HTML'
<p>İcra takibi, borçlunun borcunu rızai olarak ödememesi durumunda alacaklının başvurduğu hukuki yoldur. Bu sürece karşı borçlunun elindeki en önemli araç, yasal süresi içinde yapılacak <strong>itiraz</strong>dır.</p>
<h2>İtiraz Süresi</h2>
<p>İcra İflas Kanunu'na göre, ilamsız icra takibinde borçlu, ödeme emrinin tebliğinden itibaren <strong>yedi gün</strong> içinde itiraz edebilir. Bu süre hak düşürücü niteliktedir ve kaçırılması halinde takip kesinleşir.</p>
<blockquote>Yedi günlük itiraz süresi, icra takibinin kaderini belirleyen en kritik süredir. İtiraz yapılmadığında takibin kesinleştiği kabul edilir.</blockquote>
<h3>İtirazın Şekli ve İçeriği</h3>
<p>İtiraz, icra müdürlüğüne yazılı olarak yapılır. İtiraz dilekçesinde aşağıdaki hususlara yer verilmelidir:</p>
<ul>
<li>Takip dosyasının numarası ve tarafları</li>
<li>İtirazın konusu (borç, faiz, yetki vb.)</li>
<li>İtiraz gerekçeleri</li>
<li>İmza</li>
</ul>
<p>Borcun bir kısmına itiraz edilebileceği gibi, tamamına da itiraz edilebilir. Ayrıca yetkiye itiraz da mümkündür.</p>
<h3>İtirazın Sonuçları</h3>
<p>Borçlunun süresi içinde itirazı ile takip durur. Alacaklının takibi devam ettirebilmesi için şu yollardan birine başvurması gerekir:</p>
<ol>
<li><strong>İtirazın iptali davası:</strong> Genel mahkemelerde açılır, tarafların delilleri tartışılır.</li>
<li><strong>İtirazın kaldırılması davası:</strong> Icra mahkemesinde açılır, belge esaslı hızlı bir yargılama yapılır.</li>
</ol>
<h3>Kötüniyetli İtiraz</h3>
<p>Borçlu, borcun tamamını veya büyük kısmını bildiği halde sırf süreci uzatmak amacıyla itiraz ederse, İİK 67. madde kapsamında <strong>%20 oranında icra inkar tazminatı</strong> ödemekle yükümlü olabilir.</p>
HTML,
            ],
        ];

        foreach ($articles as $i => $row) {
            $body = $row['body'];
            Article::updateOrCreate(
                ['slug' => Str::slug($row['title'])],
                [
                    'article_category_id' => $categoryMap[$row['category']],
                    'author_id' => $authorId,
                    'title' => $row['title'],
                    'excerpt' => $row['excerpt'],
                    'body' => $body,
                    'meta_title' => $row['title'],
                    'meta_description' => $row['excerpt'],
                    'is_published' => true,
                    'published_at' => now()->subDays($i * 5 + 1),
                    'reading_time_minutes' => max(2, (int) ceil(str_word_count(strip_tags($body)) / 200)),
                ]
            );
        }
    }

    /* -----------------------------------------------------------------
     |  Statik sayfalar (KVKK, Çerez, Hakkımızda ek alanlar)
     |------------------------------------------------------------------ */
    private function seedPages(): void
    {
        $pages = [
            [
                'slug' => 'kvkk',
                'title' => 'Kişisel Verilerin Korunması Aydınlatma Metni',
                'is_system' => true,
                'body' => <<<'HTML'
<p>Bu Aydınlatma Metni, 6698 sayılı Kişisel Verilerin Korunması Kanunu (&quot;KVKK&quot;) kapsamında, veri sahiplerinin haklarını bilgilendirmek ve kişisel verilerin işlenme süreçlerine dair şeffaflık sağlamak amacıyla hazırlanmıştır.</p>
<h2>1. Veri Sorumlusu</h2>
<p><strong>Loğoğlu Hukuk Bürosu</strong> — Avukat Ethem Kaan Loğoğlu<br>
Fenerbahçe Mahallesi, Itri Dede Sokak No:22/7 Kadıköy/İstanbul</p>
<h2>2. İşlenen Kişisel Veriler</h2>
<p>Tarafımızca, aşağıdaki kişisel verileriniz işlenebilmektedir:</p>
<ul>
<li>Kimlik bilgileri (ad, soyad)</li>
<li>İletişim bilgileri (e-posta, telefon, adres)</li>
<li>İletişim formu aracılığıyla iletilen mesaj içerikleri</li>
<li>Site ziyareti sırasında oluşan teknik log bilgileri (IP adresi, tarayıcı bilgisi)</li>
</ul>
<h2>3. İşleme Amaçları</h2>
<p>Kişisel verileriniz aşağıdaki amaçlarla işlenmektedir:</p>
<ul>
<li>Hukuki danışmanlık ve avukatlık hizmetlerinin sunulması</li>
<li>İletişim taleplerinin yanıtlanması</li>
<li>Dava ve dosya süreçlerinin yönetilmesi</li>
<li>Yasal yükümlülüklerin yerine getirilmesi</li>
<li>Adli makamlar tarafından talep edilen bilgilerin sağlanması</li>
</ul>
<h2>4. Verilerin Aktarılması</h2>
<p>Kişisel verileriniz, açık rızanız olmaksızın üçüncü kişilerle paylaşılmaz. Yasal zorunluluklar nedeniyle adli ve idari mercilere, vekâletname kapsamında ise ilgili mahkeme, icra dairesi ve diğer kamu kurumlarına aktarılabilir.</p>
<h2>5. Verilerin Saklama Süresi</h2>
<p>Kişisel verileriniz, Avukatlık Kanunu ve ilgili mevzuat gereğince saklanması zorunlu olan süreler boyunca muhafaza edilir. Sürenin sona ermesinin ardından veriler silinir, yok edilir veya anonim hale getirilir.</p>
<h2>6. Haklarınız</h2>
<p>KVKK'nın 11. maddesi kapsamında veri sahibi olarak aşağıdaki haklara sahipsiniz:</p>
<ul>
<li>Kişisel verilerinizin işlenip işlenmediğini öğrenme</li>
<li>İşlenmişse, bunlara ilişkin bilgi talep etme</li>
<li>İşleme amacını ve bunların amacına uygun kullanılıp kullanılmadığını öğrenme</li>
<li>Kişisel verilerin düzeltilmesini veya silinmesini isteme</li>
<li>Kanunun 11. maddesinde sayılan diğer hakları kullanma</li>
</ul>
<p>Bu haklarınızı kullanmak için iletişim bilgilerimizden bize ulaşabilirsiniz.</p>
HTML,
            ],
            [
                'slug' => 'cerez-politikasi',
                'title' => 'Çerez Politikası',
                'is_system' => true,
                'body' => <<<'HTML'
<p>Web sitemiz, kullanıcı deneyimini geliştirmek ve temel teknik işlevleri sağlamak amacıyla sınırlı sayıda çerez kullanmaktadır.</p>
<h2>Çerez Nedir?</h2>
<p>Çerezler, bir web sitesi ziyaret edildiğinde tarayıcı tarafından cihazınıza kaydedilen küçük metin dosyalarıdır. Çerezler, site içindeki işlemlerinizin hatırlanmasını ve oturumunuzun korunmasını sağlar.</p>
<h2>Kullandığımız Çerez Türleri</h2>
<h3>Zorunlu Çerezler</h3>
<p>Sitenin temel işlevlerinin çalışması için gerekli çerezlerdir. Oturum yönetimi, güvenlik ve form gönderimi gibi işlemler için kullanılır. Bu çerezler devre dışı bırakıldığında site düzgün çalışmayabilir.</p>
<h3>İşlev Çerezleri</h3>
<p>Kullanıcı tercihlerinin (örneğin dil seçimi) hatırlanmasını sağlayan çerezlerdir. Kişisel veri toplamak amacıyla kullanılmazlar.</p>
<h2>Kullanılmayan Çerez Türleri</h2>
<p>Sitemizde <strong>reklam çerezleri</strong>, <strong>üçüncü taraf takip çerezleri</strong> ve <strong>pazarlama amaçlı analitik çerezler</strong> kullanılmamaktadır.</p>
<h2>Çerez Tercihlerinizi Yönetme</h2>
<p>Tarayıcınızın ayarlarından çerezlere ilişkin tercihlerinizi istediğiniz zaman değiştirebilirsiniz. Çerezleri silmek veya engellemek, sitenin bazı işlevlerinin çalışmamasına neden olabilir.</p>
HTML,
            ],
            [
                'slug' => 'vizyon',
                'title' => 'Vizyon',
                'is_system' => false,
                'body' => <<<'HTML'
<p>Loğoğlu Hukuk Bürosu olarak, hukukun evrensel ilkelerine bağlı, yenilikçi ve güvenilir bir hukuk bürosu olmayı hedefliyoruz. Değişen hukuki ve teknolojik dinamikleri yakından takip ederek, müvekkillerimize etkili ve çağdaş çözümler sunmayı amaçlıyoruz.</p>
<p>Müvekkillerimizin haklarını savunurken, sadece mevcut sorunlara çözüm üretmekle kalmıyor, <strong>önleyici hukuk anlayışını</strong> benimseyerek gelecekte karşılaşabilecekleri hukuki riskleri en aza indirmeye çalışıyoruz.</p>
<p>Şeffaf iletişim, güvenilir danışmanlık ve etik değerlere bağlılığımızla, müvekkillerimizin her aşamada yanlarında olarak uzun vadeli iş birliği kurmayı önemsiyoruz.</p>
HTML,
            ],
            [
                'slug' => 'misyon',
                'title' => 'Misyon',
                'is_system' => false,
                'body' => <<<'HTML'
<p>Hukuk sisteminin sacayaklarından biri olan savunmanın temsilcileri olarak, adaletin sağlanmasına katkıda bulunmayı ve meslek etiğine bağlı kalarak müvekkillerimizin haklarını etkin şekilde korumayı amaçlıyoruz.</p>
<p>Bireysel ve kurumsal müvekkillerimize, hukuki süreçleri titizlikle yöneterek güvenilir ve stratejik çözümler sunuyoruz. Her hukuki meseleyi detaylı bir analizle ele alıyor; <strong>şeffaf, anlaşılır ve çözüm odaklı</strong> bir yaklaşım benimsiyoruz.</p>
<p>Hukukun karmaşıklığı içinde yalnızca teknik bilgiye değil, aynı zamanda güçlü bir iletişim ve güven ilişkisine de önem veriyoruz. Hedefimiz, müvekkillerimize yalnızca hukuki danışmanlık sunmak değil, onların uzun vadeli çıkarlarını koruyacak güvenilir bir iş ortağı olmaktır.</p>
HTML,
            ],
        ];

        foreach ($pages as $row) {
            Page::updateOrCreate(
                ['slug' => $row['slug']],
                [
                    'title' => $row['title'],
                    'body' => $row['body'],
                    'meta_title' => $row['title'],
                    'meta_description' => Str::limit(strip_tags($row['body']), 160),
                    'is_published' => true,
                    'is_system' => $row['is_system'],
                ]
            );
        }
    }
}
