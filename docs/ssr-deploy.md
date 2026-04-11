# Inertia SSR Deployment

Loğoğlu Hukuk Bürosu (Monolith) projesinde Inertia SSR aktif olarak kullanılıyor.
Bu doküman SSR servisinin nasıl ayakta tutulacağını anlatır.

## Neden SSR?

Inertia client-side rendering ile çalışıyor — yani React mount sırasında
`<head>` etiketleri DOM'a eklenir, ilk HTML'de yer almazlar. Bu durumun yan etkisi:

- **Google Search**: 2024'ten beri JS render ediyor, etkilenmez
- **LinkedIn / WhatsApp / Telegram / Facebook bot**: JS render etmezler. Link
  paylaşıldığında preview göstermek için `<meta>` ve `<title>` etiketlerini
  ilk HTML response'undan okurlar
- **Twitter (X) Card validator**: Aynı şekilde JS render etmez

SSR aktifken her sayfa için server-side `<head>` block'u render edilir,
`@inertiaHead` blade directive'i bu içeriği `<head>` etiketinin içine basar.
Bot'lar bu sayede tam doğru OG/Twitter/JSON-LD bilgilerini görür.

## Servisi başlatma (development / staging)

```bash
# Bir kerelik başlat
./scripts/ssr-daemon.sh start

# Durum kontrolü
./scripts/ssr-daemon.sh status

# Yeniden başlat (build sonrası)
./scripts/ssr-daemon.sh restart

# Durdur
./scripts/ssr-daemon.sh stop
```

PID dosyası: `storage/framework/ssr.pid`
Log: `storage/logs/ssr.log`

## Build sonrası

Frontend kodu değiştiğinde her iki bundle'ı (CSR + SSR) build et:

```bash
npm run build  # vite build (CSR) + vite build --ssr (SSR)
```

Sonra SSR daemon'u restart et:

```bash
./scripts/ssr-daemon.sh restart
```

Bu PHP-FPM restart gerektirmez — Inertia adapter yeni Node bundle'ı otomatik
olarak kullanmaya başlar.

## Production deployment

`scripts/ssr-daemon.sh` basit nohup tabanlı bir helper'dır. Production'da
**systemd service** veya **supervisor** kullanılması önerilir — process
crash olursa otomatik restart edilir.

### systemd örneği

`/etc/systemd/system/monolith-ssr.service`:

```ini
[Unit]
Description=Loğoğlu Hukuk Bürosu Inertia SSR
After=network.target

[Service]
Type=simple
User=eloboostop
WorkingDirectory=/home/eloboostop/htdocs/eloboostop.com
ExecStart=/usr/bin/php artisan inertia:start-ssr
Restart=always
RestartSec=5
StandardOutput=append:/home/eloboostop/htdocs/eloboostop.com/storage/logs/ssr.log
StandardError=append:/home/eloboostop/htdocs/eloboostop.com/storage/logs/ssr.log

[Install]
WantedBy=multi-user.target
```

Etkinleştir:

```bash
sudo systemctl daemon-reload
sudo systemctl enable monolith-ssr
sudo systemctl start monolith-ssr
sudo systemctl status monolith-ssr
```

### supervisor örneği

`/etc/supervisor/conf.d/monolith-ssr.conf`:

```ini
[program:monolith-ssr]
process_name=%(program_name)s
command=/usr/bin/php /home/eloboostop/htdocs/eloboostop.com/artisan inertia:start-ssr
autostart=true
autorestart=true
user=eloboostop
redirect_stderr=true
stdout_logfile=/home/eloboostop/htdocs/eloboostop.com/storage/logs/ssr.log
stopwaitsecs=3600
```

Etkinleştir:

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start monolith-ssr
```

## SSR fail durumunda fallback

Eğer SSR daemon kapalı veya unreachable ise Inertia adapter **otomatik olarak
client-side rendering'e fallback** yapar — kullanıcı boş sayfa görmez, sadece
SEO meta tag'leri eksik olur. Bu davranış Inertia v3'te varsayılan ve değiştirilmesi
gerekmez.

Yani SSR çakılırsa site çalışmaya devam eder, sadece arama motoru/social
media link önizlemeleri etkilenir. Restart ile geri gelinir.

## Test komutları

```bash
# SSR çalışıyor mu?
curl -s https://eloboostop.com/ | grep -c "application/ld+json"
# Beklenen: 1 (JSON-LD bloğu var demek)

# OG tag'leri ilk HTML'de mi?
curl -s https://eloboostop.com/ | grep -c 'property="og:title"'
# Beklenen: 1

# Sitemap çalışıyor mu?
curl -sI https://eloboostop.com/sitemap.xml | head -3
# Beklenen: HTTP 200, Content-Type: application/xml
```

## Memory / performance notu

`php artisan inertia:start-ssr` Node.js child process'i fork eder. Genelde
50-100 MB RAM kullanır. Pek çok request'te aynı process kullanılır, soğuk
start cezası sadece ilk istektir. Cloudpanel default kaynaklarıyla çoğu
proje için yeterlidir.

Eğer trafik artarsa Inertia adapter'ın `concurrency` ayarı ile birden fazla
worker başlatılabilir, ancak bu çoğunlukla gerekmez.
