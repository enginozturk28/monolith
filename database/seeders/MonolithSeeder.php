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
        $site = [
            ['key' => 'name', 'value' => 'Loğoğlu Hukuk Bürosu', 'type' => 'text', 'label' => 'Büro Adı'],
            ['key' => 'tagline', 'value' => 'Hukukun farklı alanlarında profesyonel destek', 'type' => 'text', 'label' => 'Slogan'],
            ['key' => 'phone', 'value' => '+90 553 647 38 22', 'type' => 'text', 'label' => 'Telefon'],
            ['key' => 'email', 'value' => 'ethemlogoglu@gmail.com', 'type' => 'text', 'label' => 'E-posta'],
            ['key' => 'kep', 'value' => 'ethemkaan.logoglu@hs01.kep.tr', 'type' => 'text', 'label' => 'KEP Adresi'],
            ['key' => 'linkedin_url', 'value' => 'https://www.linkedin.com/in/ethemlogoglu/', 'type' => 'text', 'label' => 'LinkedIn URL'],
            ['key' => 'address', 'value' => 'Fenerbahçe Mahallesi, Itri Dede Sokak No:22/7 Kadıköy/İstanbul', 'type' => 'textarea', 'label' => 'Adres'],
            [
                'key' => 'map_embed_url',
                'value' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3011.95092531232!2d29.03693307652785!3d40.98255542100763!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x14cab9712620d57d%3A0x818d2be1fa4658f!2zTG_En2_En2x1IEh1a3VrIELDvHJvc3U!5e0!3m2!1str!2str!4v1775834413792!5m2!1str!2str',
                'type' => 'textarea',
                'label' => 'Google Maps Embed URL',
            ],
            ['key' => 'logo_url', 'value' => null, 'type' => 'image', 'label' => 'Logo'],
            // Görseller — monolith:fetch-images komutu ile Pixabay'den indirilir
            ['key' => 'hero_image_path', 'value' => null, 'type' => 'image', 'label' => 'Anasayfa Hero Görseli'],
            ['key' => 'hero_image_credit', 'value' => null, 'type' => 'text', 'label' => 'Hero Görsel Atıf'],
            ['key' => 'about_image_path', 'value' => null, 'type' => 'image', 'label' => 'Hakkımızda Görseli'],
            ['key' => 'about_image_credit', 'value' => null, 'type' => 'text', 'label' => 'Hakkımızda Görsel Atıf'],
        ];

        // Mevcut görsel ayarlarını koru — fetch-images komutuyla indirilen
        // değerleri seed yeniden çalıştırıldığında sıfırlamayalım.
        $preserveKeys = ['hero_image_path', 'hero_image_credit', 'about_image_path', 'about_image_credit'];

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
                'body' => "Ceza hukuku alanında, müvekkillerimize soruşturma ve kovuşturma aşamalarında hukuki destek sunuyoruz. İfade alma, tutuklama, iddianame değerlendirmesi, duruşma takibi ve istinaf/temyiz süreçlerinde etkin savunma hizmeti veriyoruz.\n\nSavunma hakkının etkin kullanılabilmesi için her dosyayı mevzuat ve içtihat analizi ile değerlendiriyor, müvekkilimizin hak ve menfaatlerini gözeterek süreçleri yönetiyoruz.",
            ],
            [
                'title' => 'Aile Hukuku',
                'icon' => 'heart-handshake',
                'summary' => 'Boşanma, velayet, nafaka ve mal rejimi uyuşmazlıkları.',
                'body' => "Aile hukuku, hassasiyet gerektiren bir alan olup bireylerin özel yaşamlarına doğrudan dokunur. Anlaşmalı ve çekişmeli boşanma davaları, velayet, nafaka, iştirak nafakası, mal rejimi tasfiyesi ve aile içi koruma tedbirleri konularında hukuki destek sağlıyoruz.\n\nSüreçlerde müvekkillerimizin menfaatleri kadar, ailelerin uzun vadeli ilişkileri de gözetilerek çözüm odaklı bir yaklaşım benimsenir.",
            ],
            [
                'title' => 'Miras Hukuku',
                'icon' => 'scroll',
                'summary' => 'Vasiyetname, miras paylaşımı ve tenkis davaları.',
                'body' => "Miras bırakanın ardından açılan tereke, vasiyetname iptali, tenkis, muvazaa, mirasın reddi ve mirasçılık belgesi alınması gibi konularda danışmanlık ve dava takibi yürütüyoruz.\n\nMiras hukuku, hem aile hukukuyla hem de mülkiyet ve borçlar hukukuyla iç içe olduğundan, her dosyayı bütünsel bir perspektifle ele alıyoruz.",
            ],
            [
                'title' => 'Marka ve Patent Hukuku',
                'icon' => 'badge-check',
                'summary' => 'Marka tescili, hükümsüzlük ve tecavüz davaları.',
                'body' => "Sınai mülkiyet hakları kapsamında marka tescil başvuruları, itirazlar, hükümsüzlük davaları, marka ve patent tecavüzü, tasarım koruması ve lisans sözleşmelerine ilişkin hukuki danışmanlık sağlıyoruz.\n\nTPE/EPO başvuruları ve uluslararası WIPO süreçlerinde de takip ve danışmanlık hizmeti veriyoruz.",
            ],
            [
                'title' => 'Gayrimenkul Hukuku',
                'icon' => 'home',
                'summary' => 'Tapu, kira, kat mülkiyeti ve imar uyuşmazlıkları.',
                'body' => "Gayrimenkul alım-satım sözleşmeleri, tapu iptali ve tescili, kira uyuşmazlıkları, kat mülkiyeti, imar davaları, kamulaştırma ve ecrimisil konularında hukuki destek sunuyoruz.\n\nTaşınmazlara ilişkin her işlem öncesinde kapsamlı bir hukuki durum değerlendirmesi yaparak riskleri önceden belirlemeye çalışıyoruz.",
            ],
            [
                'title' => 'Vergi Hukuku',
                'icon' => 'receipt',
                'summary' => 'Vergi/ceza ihbarnameleri, uzlaşma ve dava süreçleri.',
                'body' => "Vergi incelemesi, vergi ve ceza ihbarnamelerine karşı itiraz, uzlaşma süreçleri, vergi mahkemesi davaları ile vergi hukuku kapsamındaki danışmanlık konularında hizmet veriyoruz.\n\nHem bireysel mükellefler hem de kurumsal müvekkiller için dosya bazlı uzman değerlendirmesi yapıyoruz.",
            ],
            [
                'title' => 'Sözleşmeler ve Borçlar Hukuku',
                'icon' => 'file-signature',
                'summary' => 'Sözleşme hazırlama, inceleme ve uyuşmazlık çözümü.',
                'body' => "Sözleşme hazırlama, mevcut sözleşmelerin incelenmesi ve revize edilmesi, sözleşmeden doğan alacak davaları, haksız fiil ve sebepsiz zenginleşme uyuşmazlıklarında hukuki destek sağlıyoruz.\n\nHazırlanan her sözleşmede müvekkilin menfaatlerinin yanı sıra olası uyuşmazlıkları önleyici hükümlerin de bulunmasına özen gösterilir.",
            ],
            [
                'title' => 'İş Hukuku',
                'icon' => 'briefcase',
                'summary' => 'İşçi-işveren uyuşmazlıkları ve iş güvencesi.',
                'body' => "İş sözleşmelerinin hazırlanması, işe iade, ihbar ve kıdem tazminatı, mobbing, haksız fesih ve fazla mesai alacakları konularında hem işçi hem işveren tarafına danışmanlık ve dava takibi hizmeti sunuyoruz.\n\nArabuluculuk süreçlerinde de tarafların menfaatlerini koruyarak sonuç odaklı çözümler üretmeyi hedefliyoruz.",
            ],
            [
                'title' => 'Ticaret Hukuku',
                'icon' => 'building-2',
                'summary' => 'Şirket kuruluşu, sözleşme ve kurumsal danışmanlık.',
                'body' => "Şirket kuruluşu, ana sözleşme düzenlemeleri, pay devirleri, genel kurul ve yönetim kurulu toplantıları, haksız rekabet, şirket birleşme ve devralmaları ile ticari sözleşmeler konularında hukuki hizmet veriyoruz.\n\nKurumsal müvekkiller için sürekli danışmanlık modeli ile önleyici hukuk anlayışı benimseniyor.",
            ],
            [
                'title' => 'Tazminat Hukuku',
                'icon' => 'life-buoy',
                'summary' => 'Maddi-manevi tazminat ve trafik kazaları.',
                'body' => "Trafik kazaları, iş kazaları, hekim hatası (malpraktis), kişilik haklarına saldırı ve haksız fiilden kaynaklanan maddi ve manevi tazminat davalarında hukuki temsil ve danışmanlık sağlıyoruz.\n\nZararın tespit edilmesi ve delillendirme sürecinde bilirkişi incelemelerinin doğru yönlendirilmesi önem taşır.",
            ],
            [
                'title' => 'Yabancılar Hukuku',
                'icon' => 'globe-2',
                'summary' => 'Oturma izni, vatandaşlık ve çalışma izni süreçleri.',
                'body' => "Türkiye'de yaşayan yabancı müvekkillerimize oturma izni başvuruları, çalışma izni, Türk vatandaşlığı kazanımı, sınır dışı etme işlemleri ve uluslararası koruma süreçlerinde hukuki destek veriyoruz.\n\nAyrıca uluslararası özel hukuk kapsamındaki evlilik, velayet ve miras uyuşmazlıklarında da danışmanlık hizmeti sunuyoruz.",
            ],
            [
                'title' => 'İcra ve İflas Hukuku',
                'icon' => 'gavel',
                'summary' => 'İcra takibi, haciz ve iflas süreçleri.',
                'body' => "İlamlı ve ilamsız icra takipleri, itirazın iptali, istirdat davaları, menfi tespit, haciz ve muhafaza işlemleri, konkordato ve iflas süreçlerinde müvekkillerimizi temsil ediyoruz.\n\nAlacağın tahsili sürecinde hem hukuki hem de pratik çözümler üretmeye özen gösterilir.",
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
                ['q' => 'İlk görüşme için randevu nasıl alabilirim?', 'a' => 'İletişim formumuzdan veya telefonla arayarak randevu talebi oluşturabilirsiniz. Genellikle 1-2 iş günü içinde tarafınıza dönüş sağlanır.'],
                ['q' => 'Görüşmeler hangi dilde yapılır?', 'a' => 'Görüşmelerimiz Türkçe yürütülmektedir. Yabancılar hukuku dosyalarında, gerekli durumlarda tercüman desteği sağlanabilir.'],
                ['q' => 'Dosyamın durumu hakkında nasıl bilgi alabilirim?', 'a' => 'Müvekkillerimize dosyanın ilerleyişine dair düzenli bilgilendirme yapılır. Acil durumlarda telefon veya e-posta ile iletişim kurabilirsiniz.'],
            ]],
            ['name' => 'Ücretlendirme', 'icon' => 'receipt', 'faqs' => [
                ['q' => 'Vekâlet ücretleri nasıl belirlenir?', 'a' => 'Vekâlet ücretleri, Türkiye Barolar Birliği Asgari Ücret Tarifesi esas alınarak, dosyanın niteliği, süresi ve hukuki uyuşmazlığın boyutuna göre belirlenir ve sözleşme ile kayıt altına alınır.'],
                ['q' => 'İlk görüşme ücretli midir?', 'a' => 'İlk danışmanlık görüşmeleri, dosyanın değerlendirilmesi amacıyla yapılır. Ücretlendirme, görüşme esnasında karşılıklı mutabakatla belirlenir.'],
            ]],
            ['name' => 'Dava Süreçleri', 'icon' => 'scale', 'faqs' => [
                ['q' => 'Bir dava ne kadar sürer?', 'a' => 'Dava süreleri; mahkeme yoğunluğu, uyuşmazlığın niteliği, delil toplama süreci ve istinaf/temyiz aşamalarına göre farklılık gösterir. Her dosya için tahmini süre bilgisi, dosya değerlendirmesi sonrasında sunulur.'],
                ['q' => 'Duruşmalara katılmam zorunlu mu?', 'a' => 'Dosyanızın niteliğine göre duruşmalara katılmanız gerekebilir veya vekâleten takip mümkündür. Her dosya için ayrı değerlendirme yapılır.'],
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
     |  Örnek makaleler (az sayıda, Faz 7'de genişletilebilir)
     |------------------------------------------------------------------ */
    private function seedArticles(): void
    {
        $category = ArticleCategory::updateOrCreate(
            ['slug' => 'genel'],
            ['name' => 'Genel', 'sort_order' => 0]
        );

        // Yazar olarak mevcut ilk kullanıcıyı kullan (admin user yeniden
        // oluşturulduğunda ID sabitlenmesin diye).
        $authorId = \App\Models\User::query()->orderBy('id')->value('id');

        $articles = [
            [
                'title' => 'Hukuki Uyuşmazlıklarda Önleyici Yaklaşımın Önemi',
                'excerpt' => 'Hukuki sorunların ortaya çıkmadan önce fark edilmesi, zaman ve maliyet açısından büyük avantaj sağlar. Bu yazıda önleyici hukuk anlayışının temel prensiplerini ele alıyoruz.',
                'body' => "Hukuki uyuşmazlıkların önemli bir kısmı, aslında başlangıçta alınabilecek basit önlemlerle engellenebilir niteliktedir. Özellikle ticari ilişkilerde ve sözleşmelerde, tarafların hak ve yükümlülüklerinin net biçimde kayıt altına alınmaması ilerleyen süreçte ciddi anlaşmazlıklara yol açabilmektedir.\n\nÖnleyici hukuk anlayışı, müvekkilin karşılaşabileceği olası riskleri önceden tespit ederek hukuki yapıyı buna göre oluşturmayı hedefler. Bu yaklaşım; hem süreç maliyetlerini azaltır hem de olası uyuşmazlıkların karmaşıklaşmasını engeller.\n\nBu yazıda, özellikle ticari sözleşmeler, aile hukuku anlaşmaları ve gayrimenkul işlemlerinde önleyici hukuk anlayışının nasıl uygulandığına değinilecektir.",
            ],
            [
                'title' => 'Sözleşme Hazırlarken Dikkat Edilmesi Gereken Temel Hususlar',
                'excerpt' => 'Ticari ve bireysel sözleşmelerde tarafların haklarını korumak için dikkat edilmesi gereken maddeleri inceliyoruz.',
                'body' => "Bir sözleşme hazırlanırken, tarafların karşılıklı irade beyanlarının açık ve anlaşılır biçimde yansıtılması en temel kuraldır. Sözleşmenin konusu, tarafların hak ve yükümlülükleri, ifa yeri ve zamanı, süre, fesih koşulları ve uyuşmazlık halinde uygulanacak hukuk ile yetkili mahkeme gibi hususlar mutlaka ayrıntılı biçimde düzenlenmelidir.\n\nAyrıca, cezai şart, gecikme faizi ve sözleşmenin feshine ilişkin özel hükümlerin belirlenmesi, ileride yaşanabilecek uyuşmazlıkların çözümünde büyük kolaylık sağlar.\n\nHer sözleşme kendine özgü bir içeriğe sahiptir ve standart şablonların birebir uygulanması yerine, ilgili somut olayın özelliklerine göre hazırlanması tercih edilmelidir.",
            ],
        ];

        foreach ($articles as $i => $row) {
            Article::updateOrCreate(
                ['slug' => Str::slug($row['title'])],
                [
                    'article_category_id' => $category->id,
                    'author_id' => $authorId,
                    'title' => $row['title'],
                    'excerpt' => $row['excerpt'],
                    'body' => $row['body'],
                    'meta_title' => $row['title'],
                    'meta_description' => $row['excerpt'],
                    'is_published' => true,
                    'published_at' => now()->subDays($i * 7 + 1),
                    'reading_time_minutes' => max(2, ceil(str_word_count(strip_tags($row['body'])) / 200)),
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
                'body' => "**Veri Sorumlusu:** Loğoğlu Hukuk Bürosu — Avukat Ethem Kaan Loğoğlu\n\n**İletişim:** Fenerbahçe Mahallesi, Itri Dede Sokak No:22/7 Kadıköy/İstanbul\n\nBu Aydınlatma Metni, 6698 sayılı Kişisel Verilerin Korunması Kanunu (\"KVKK\") kapsamında, veri sahiplerinin haklarını bilgilendirmek ve kişisel verilerin işlenme süreçlerine dair şeffaflık sağlamak amacıyla hazırlanmıştır.\n\n## Toplanan Veriler\n- Ad, soyad, e-posta, telefon, adres bilgileri\n- İletişim formu aracılığıyla iletilen mesaj içerikleri\n- Site ziyareti sırasında oluşan teknik log bilgileri (IP adresi, tarayıcı bilgisi)\n\n## İşleme Amaçları\nKişisel verileriniz; hukuki danışmanlık ve avukatlık hizmetlerinin sunulması, iletişim taleplerinin yanıtlanması ve yasal yükümlülüklerin yerine getirilmesi amaçlarıyla işlenmektedir.\n\n## Haklarınız\nKVKK'nın 11. maddesi kapsamında veri sahibi olarak; kişisel verilerinizin işlenip işlenmediğini öğrenme, işlenmişse bilgi talep etme, düzeltilmesini veya silinmesini isteme haklarına sahipsiniz. Bu haklarınızı kullanmak için iletişim bilgilerimizden bize ulaşabilirsiniz.",
            ],
            [
                'slug' => 'cerez-politikasi',
                'title' => 'Çerez Politikası',
                'is_system' => true,
                'body' => "Web sitemiz, kullanıcı deneyimini geliştirmek ve temel teknik işlevleri sağlamak amacıyla sınırlı sayıda çerez kullanmaktadır.\n\n## Kullanılan Çerez Türleri\n- **Zorunlu çerezler:** Oturum yönetimi ve güvenlik için gereklidir.\n- **İşlev çerezleri:** Dil tercihi gibi kullanıcı tercihlerini hatırlar.\n\nReklam ve üçüncü taraf takip çerezleri kullanılmamaktadır. Tarayıcı ayarlarınızdan çerez tercihlerinizi istediğiniz zaman değiştirebilirsiniz.",
            ],
            [
                'slug' => 'vizyon',
                'title' => 'Vizyon',
                'is_system' => false,
                'body' => "Loğoğlu Hukuk Bürosu olarak, hukukun evrensel ilkelerine bağlı, yenilikçi ve güvenilir bir hukuk bürosu olmayı hedefliyoruz. Değişen hukuki ve teknolojik dinamikleri yakından takip ederek, müvekkillerimize etkili ve çağdaş çözümler sunmayı amaçlıyoruz.\n\nMüvekkillerimizin haklarını savunurken, sadece mevcut sorunlara çözüm üretmekle kalmıyor, önleyici hukuk anlayışını benimseyerek gelecekte karşılaşabilecekleri hukuki riskleri en aza indirmeye çalışıyoruz.\n\nŞeffaf iletişim, güvenilir danışmanlık ve etik değerlere bağlılığımızla, müvekkillerimizin her aşamada yanlarında olarak uzun vadeli iş birliği kurmayı önemsiyoruz.",
            ],
            [
                'slug' => 'misyon',
                'title' => 'Misyon',
                'is_system' => false,
                'body' => "Hukuk sisteminin sacayaklarından biri olan savunmanın temsilcileri olarak, adaletin sağlanmasına katkıda bulunmayı ve meslek etiğine bağlı kalarak müvekkillerimizin haklarını etkin şekilde korumayı amaçlıyoruz.\n\nBireysel ve kurumsal müvekkillerimize, hukuki süreçleri titizlikle yöneterek güvenilir ve stratejik çözümler sunuyoruz. Her hukuki meseleyi detaylı bir analizle ele alıyor; şeffaf, anlaşılır ve çözüm odaklı bir yaklaşım benimsiyoruz.\n\nHukukun karmaşıklığı içinde yalnızca teknik bilgiye değil, aynı zamanda güçlü bir iletişim ve güven ilişkisine de önem veriyoruz. Hedefimiz, müvekkillerimize yalnızca hukuki danışmanlık sunmak değil, onların uzun vadeli çıkarlarını koruyacak güvenilir bir iş ortağı olmaktır.",
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
