# Production Deployment Runbook

`eloboostop.com` (staging) → `logogluhukuk.com.tr` (production) geçişi için
adım adım rehber. Bu doküman canlı yayın gününde takip edilir.

## Ön kontrol (deploy'dan önce)

### 1. Tüm panel testlerini staging'de tamamla

- [ ] `/admin/login` ile giriş yap, dashboard widget'ları doğru sayıları göster
- [ ] **Profile (`/admin/profile`)**:
  - [ ] Ad ve e-posta değiştirme çalışıyor
  - [ ] Şifre değiştirme çalışıyor
  - [ ] **2FA TOTP** kurulum: QR kod telefonla okutulup 6 haneli kod ile aktif edildi
  - [ ] **2FA Email OTP** aktif edildi
  - [ ] Logout + login → 2FA prompt geliyor, doğru kod kabul ediliyor, yanlış kod red ediliyor
  - [ ] Recovery kodları indirilmiş ve güvenli yerde saklanmış
- [ ] **Site Settings (`/admin/site-settings`)**:
  - [ ] Tüm bilgiler doğru (tel, email, KEP, adres, sosyal medya)
  - [ ] Logo yüklendi (tasarım ekibi hazırsa) veya tipografik fallback OK
  - [ ] Footer açıklama + copyright doğru görünüyor
  - [ ] About intro RichText kaydedildi ve `/hakkimizda`'da görünüyor
- [ ] **Theme Settings (`/admin/theme-settings`)**: tüm preset'ler test edildi,
      WCAG kontrast geçer
- [ ] **SMTP Settings (`/admin/smtp-settings`)**:
  - [ ] Gerçek SMTP girilmiş (Gmail / Yandex / Resend / Postmark)
  - [ ] "Test Maili Gönder" butonu çalıştırıldı ve mail GERÇEKTEN geldi (inbox)
  - [ ] İletişim formu submit → e-posta bildirimi inbox'a ulaştı
- [ ] **Security Settings (`/admin/security-settings`)** (opsiyonel):
  - [ ] Cloudflare Turnstile aktif (eğer kullanılacaksa)
  - [ ] İletişim formunda widget görünüyor
- [ ] **İçerik kontrolü**:
  - [ ] Tüm 12 faaliyet alanı yayında, içerikler doğru
  - [ ] Makaleler doğru yayında, kategoriler atanmış
  - [ ] SSS sorularının cevapları kontrol edilmiş
  - [ ] KVKK ve Çerez politikası metinleri yasal danışman tarafından onaylı
  - [ ] Vizyon ve Misyon metinleri onaylı
- [ ] **Görsel kontrol**:
  - [ ] Hero görseli uygun
  - [ ] About görseli uygun
- [ ] **Mobile responsive**: telefon, tablet, masaüstü test edilmiş
- [ ] **Form fonksiyonu**: iletişim formu submit → DB kaydı + mail bildirimi OK

### 2. Lighthouse audit (staging'de)

```bash
# Chrome DevTools > Lighthouse > Mobile + Performance + Accessibility + Best Practices + SEO
```

Hedefler:
- Performance ≥ 90
- Accessibility ≥ 90
- Best Practices ≥ 95
- SEO = 100

Düşükse iyileştirmeler:
- Image lazy loading kontrolü
- Critical CSS inline?
- Cumulative Layout Shift?

### 3. Güvenlik kontrolü

- [ ] **Brute-force rate limiter** aktif — Filament v5 default 5 deneme/dakika
      throttle uygulayan built-in koruma var. Ekstra konfigürasyon gerekmez.
      MFA challenge için ayrı limit (`isMultiFactorChallengeRateLimited`).
      **Recovery komut**: kullanıcı kendini bloke ederse:
      ```bash
      php artisan monolith:unlock-login           # login throttle temizle
      php artisan monolith:unlock-login --all     # tüm cache'i nuke
      ```
- [ ] HTTPS zorlu (HSTS header)
- [ ] CSP header (gerekirse)
- [ ] `.env` production'da `APP_DEBUG=false`
- [ ] `.env` production'da `APP_ENV=production`
- [ ] Admin credentials güvenli yerde saklanmış
- [ ] Recovery kodları ofisteki güvenli bir kasada
- [ ] Database backup planı: günlük + 7 gün retention

## Deploy günü adımları

### A. Domain ve DNS hazırlığı

1. **`logogluhukuk.com.tr` için DNS kayıtları**:
   - A record: VPS IP
   - veya CNAME (eğer Cloudflare kullanılacaksa)
   - SPF/DKIM/DMARC mail authentication
2. **SSL sertifikası**: Cloudpanel → Let's Encrypt otomatik
3. **www → root** veya tersine redirect

### B. Cloudpanel'de yeni site oluştur

1. Cloudpanel UI → "Add Site" → `logogluhukuk.com.tr`
2. PHP version: 8.4
3. Document root: `/home/loguser/htdocs/logogluhukuk.com.tr/public`
4. SSL: Let's Encrypt aktif et

### C. Database hazırlığı

1. Cloudpanel → Database → `logogluhukuk_prod` oluştur
2. Yeni user + şifre (staging'den farklı)
3. Şifre güvenli yerde saklanır

### D. Kod deployment

```bash
# SSH ile production sunucuya
cd /home/loguser/htdocs/logogluhukuk.com.tr
git clone https://github.com/enginozturk28/monolith.git .

# Composer install (production mode)
composer install --no-dev --optimize-autoloader

# .env production setup
cp .env.example .env
nano .env
# - APP_NAME="Loğoğlu Hukuk Bürosu"
# - APP_ENV=production
# - APP_DEBUG=false
# - APP_URL=https://logogluhukuk.com.tr
# - DB_* (production DB)
# - SMTP_* (gerçek SMTP)
# - PIXABAY_API_KEY (geliştirici aracı, opsiyonel)

php artisan key:generate
php artisan storage:link

# Database migrate
php artisan migrate --force
php artisan db:seed --force --class=MonolithSeeder

# İlk admin user
php artisan tinker
> $u = App\Models\User::create([
>     'name' => 'Ethem Kaan Loğoğlu',
>     'email' => 'ethemlogoglu@gmail.com',
>     'password' => 'GÜÇLÜ_RASTGELE_ŞİFRE',
> ]);
> $u->forceFill(['email_verified_at' => now()])->save();

# Frontend build (CSR + SSR)
npm install
npm run build

# Cache + config
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# SSR daemon (systemd ile, docs/ssr-deploy.md'deki örnek)
sudo cp scripts/monolith-ssr.service /etc/systemd/system/
sudo systemctl daemon-reload
sudo systemctl enable monolith-ssr
sudo systemctl start monolith-ssr
sudo systemctl status monolith-ssr

# Permissions
sudo chown -R loguser:loguser storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

### E. Cloudpanel ekstra config

- **Vhost editor**: `proxy_set_header X-Forwarded-Proto $scheme;` ekle
  (zaten Laravel'de `URL::forceScheme('https')` var, ama belt-and-braces)
- Static asset cache TTL: 1 yıl `Cache-Control: public, max-age=31536000`
- Gzip / Brotli compression aktif
- HTTP/2 ya da HTTP/3 (Cloudpanel default)

### F. İlk smoke test

```bash
# DNS propagasyonu sonrası
curl -sI https://logogluhukuk.com.tr/ | head -5
# Beklenen: HTTP/2 200

# Sitemap
curl -s https://logogluhukuk.com.tr/sitemap.xml | head -10
# Beklenen: <?xml version="1.0"?>...

# Robots.txt — production version
curl -s https://logogluhukuk.com.tr/robots.txt
# Beklenen: User-agent: *, Allow: /, Sitemap: https://...

# OG meta tag (SSR çalışıyor mu?)
curl -s https://logogluhukuk.com.tr/ | grep -c "application/ld+json"
# Beklenen: 1

# Admin login
curl -sI https://logogluhukuk.com.tr/admin/login
# Beklenen: HTTP/2 200
```

### G. SEO submission

1. **Google Search Console** (`https://search.google.com/search-console`):
   - Domain property ekle
   - DNS verification
   - Sitemap submit: `https://logogluhukuk.com.tr/sitemap.xml`
   - URL inspection ile birkaç sayfa test
2. **Bing Webmaster Tools**: aynı işlem
3. **Yandex Webmaster**: TR pazarda önemli, sitemap ekle

### H. Monitoring

- [ ] Uptime monitoring (UptimeRobot, BetterStack vb.)
- [ ] Error tracking (Sentry, Bugsnag)
- [ ] Mail deliverability monitoring (SMTP sağlayıcı dashboard)

## Cutover (canlı geçiş)

Eğer staging'den production'a tek seferde geçiş yapılacaksa:

1. Database export staging'den:
   ```bash
   mysqldump -u logogluhukuk -p logogluhukuk > /tmp/staging-backup.sql
   ```
2. Production database'e import:
   ```bash
   mysql -u prod_user -p logogluhukuk_prod < /tmp/staging-backup.sql
   ```
3. `storage/app/public/` dizinini de kopyala (logo, hero, about görseller)
4. `.env` production değerlerini set et
5. Cache invalidation: `php artisan cache:clear && php artisan config:cache`
6. SSR daemon restart: `sudo systemctl restart monolith-ssr`

## Rollback planı

Eğer production'da kritik bir bug bulunursa:

1. DNS'i staging'e geri yönlendir (geçici)
2. Sebebi tespit et, fix yap, yeniden deploy
3. Database backup'tan restore (gerekirse)

## Pre-launch checklist (canlı yayın öncesi son kontrol)

- [ ] DNS propagasyonu tamamlandı (`dig logogluhukuk.com.tr` doğru IP'yi gösteriyor)
- [ ] SSL sertifikası geçerli (browser yeşil kilit)
- [ ] Tüm sayfalar HTTP 200 dönüyor
- [ ] SSR daemon çalışıyor (`systemctl status monolith-ssr`)
- [ ] Admin login + 2FA çalışıyor
- [ ] İletişim formu test edildi, mail geldi
- [ ] Mobile responsive son kontrol
- [ ] Lighthouse score'u kabul edilebilir
- [ ] Müşteriye demo URL paylaşıldı, onay alındı
- [ ] Backup planı devrede
- [ ] Monitoring devrede

## Post-launch

- 1 hafta yoğun monitoring
- Müşteri feedback'i topla
- Google Search Console'da indexleme durumunu izle
- İlk hafta günlük backup, sonra haftalık
