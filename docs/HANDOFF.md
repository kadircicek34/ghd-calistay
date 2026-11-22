# GH Çalıştay — OTURUM HANDOFF

Son güncelleme: 22 Kasım 2026, 18:45 (Europe/Istanbul)

Bu dosya, projeyi devralacak session'ın eksiksiz devam edebilmesi için yazıldı.

## 1. Kısa özet

Kanboard 1.2.58 fork'u, Global Hukukçular Derneği için **GH Çalıştay** adıyla
özelleştirildi. Ana ürün:

- Kurumsal landing page (`/`) — lacivert/bronz-altın/krem, logo, Türkçe, mobil.
- Üyelik başvurusu (`/basvuru`) — doğum tarihi, hukuk durumu, e-Devlet belgesi,
  KVKK + kullanım koşulları, honeypot, saatlik IP limiti. Başvuru kaydedilince
  üyelik otomatik onaylanır ve Kanboard hesabı anında oluşturulur.
- Başvuru durumu (`/basvuru/durum`) — pending/approved/rejected + ret gerekçesi.
- Admin (`/admin/uyelik-basvurulari`) — tüm başvurular, belge indirme,
  otomatik onaylı üyeliği gerekçeli iptal; otomatik onay tamamlanamayan
  istisnalar için manuel onay.
- Otomatik onaylanan üye anında giriş yapabilir; hesap `GH Çalıştay` projesine
  üye olarak eklenir.
- Üyelere özel kanban; public kayıt yok.

## 2. Erişim ve sırlar

- Canlı adres: `https://calistay.globalhukukcular.com` (yayında)
- Public repo: `https://github.com/kadircicek34/ghd-calistay` (`main`)
- Sunucu: `169.58.198.19` (bu makine)
- Admin kullanıcı: `kadir`
- Admin e-posta: `/root/çalıştay/admin-credentials.txt` içinde
- Admin parola: `/root/çalıştay/admin-credentials.txt` (0600)
- DNS panel: `https://www.turkticaret.net/usermanage/userlogin.php`
  - Panel bilgileri `/root/sporgla/.deploy-secrets` içinde; repoya girmez.
- Caddy: `/root/Gakademi/deploy/Caddyfile`; yedek `.bak-ghd-calistay-*`.
- Production env/secret yok; SQLite ve admin parolası volume/dosyada.

## 3. Sunucu / Docker

- Production container: `ghdcalistay-app` (compose service `app`, healthy)
- Staging container: `ghdcalistay-test-app` (port `127.0.0.1:8180`)
- Network: `gakademi_default` (external)
- Data volume: `ghdcalistay_data`
- Staging volume: `ghdcalistay_test_data`
- Image: `ghd-calistay:local`
- Reverse proxy: `gakademi-caddy-1`
- Healthcheck: `http://ghdcalistay-app/healthcheck.php` → `{"status":200,...}`

Compose: `deploy/docker-compose.yml`
Dockerfile: `deploy/Dockerfile` (`FROM kanboard/kanboard:v1.2.58` + plugin +
Route.php boş-route düzeltmesi)
Caddy snippet: `deploy/Caddyfile.snippet`

## 4. Kurulum / güncelleme

```bash
cd /root/çalıştay/kanboard
docker compose -f deploy/docker-compose.yml up -d --build

GHD_ADMIN_PASSWORD='...' \
  docker exec -e GHD_ADMIN_PASSWORD \
  ghdcalistay-app php /var/www/app/plugins/GhdMembership/tools/setup.php --force
```

`setup.php` idempotenttir. İlk çalıştırmada bootstrap `admin` hesabını
`kadir` yapar, `app-admin` rolü verir, şifreyi günceller, `GH Çalıştay`
projesini ve karşılama görevini oluşturur. `--force` olmadan mevcut şifreyi
değiştirmez. Staging'de saatlik başvuru limitini yükseltmek için
`GHD_HOURLY_LIMIT=100` verilebilir; production varsayılanı 10'dur.

## 5. Canlı durum (22.11.2026)

- DNS: `calistay.globalhukukcular.com A 169.58.198.19` (8.8.8.8/1.1.1.1 ve
  Turkticaret NS doğrulandı).
- TLS: Let's Encrypt, `CN=calistay.globalhukukcular.com`, geçerlilik
  22.11.2026 – 20.02.2027; `ssl_verify_result=0`.
- HTTP → HTTPS: 308 Permanent Redirect.
- Canlı smoke: landing/login/başvuru/admin/mobil PASS.
- Canlı üyelik akışı: test başvurusu → otomatik onay → giriş → pano PASS;
  ardından test uygulaması, kullanıcı ve belge silindi.
- Mevcut 2 gerçek bekleyen başvuru yeni politikayla otomatik onaylandı; belgeleri
  korunuyor. Admin listesinde 2 onaylı, 0 bekleyen başvuru var.
- Otomatik onaylı üyelik iptali staging E2E'de doğrulandı.
- Mevcut servisler: dil/spor/oyun/kariyer 200; akademi bilinen 500 (önceden de
  500 idi).

## 6. Caddy

```bash
cd /root/Gakademi/deploy
cp Caddyfile Caddyfile.bak-ghd-calistay-$(date +%Y%m%d-%H%M%S)
cat /root/çalıştay/kanboard/deploy/Caddyfile.snippet >> Caddyfile
docker cp Caddyfile gakademi-caddy-1:/tmp/Caddyfile.test
docker exec gakademi-caddy-1 caddy validate --config /tmp/Caddyfile.test
docker exec gakademi-caddy-1 caddy reload --config /etc/caddy/Caddyfile
```

Snippet iki blok içerir: `calistay.globalhukukcular.com` ve geçici
`calistay.169.58.198.19.nip.io`. DNS yayıldıktan sonra nip.io bloğu
kaldırılabilir. Mevcut `akademi`, `dil`, `spor`, `oyun`, `kariyer` bloklarına
dokunulmadı.

## 7. DNS

`calistay.globalhukukcular.com A 169.58.198.19` kaydı Turkticaret panelinden
eklenir. Panel çok adımlı giriş kullanır; Playwright ile e-posta → şifre →
domain → DNS yönetimi akışı izlenebilir. Kayıt eklendikten sonra:

```bash
dig +short @8.8.8.8 calistay.globalhukukcular.com
curl -I https://calistay.globalhukukcular.com
```

## 8. Testler

- E2E: `tests/e2e/membership.spec.js` — 22 senaryo, PASS.
- Çalıştırma:
  ```bash
  BASE_URL=http://127.0.0.1:8180 \
  GHD_ADMIN_USER=kadir \
  GHD_ADMIN_PASSWORD='...' \
  NODE_PATH=/root/kariyer/career-assistant/frontend/node_modules \
  node tests/e2e/membership.spec.js
  ```
- PHP lint: tüm eklenti dosyaları temiz.
- Kanıtlar: `docs/test-report.md`, `docs/screenshots/`.

## 9. Bilinen sorunlar / dikkat

1. **Staging test verisi:** `ghdcalistay_test_data` volume'unda çok sayıda
   sentetik başvuru var. Production `ghdcalistay_data` ayrıdır; testleri
   production'a taşımayın.
2. **IP görünümü:** Staging'de Caddy olmadığı için IP `172.19.0.1`; production
   Caddy + trusted proxy ile gerçek IP'yi kullanır.
3. **E-posta:** SMTP yapılandırılmadı; başvuru durumu web'den sorgulanır.
   `password_reset=0`; kullanıcı şifresini giriş sonrası profilinden değişebilir.
4. **SQLite:** Dernek ölçeği için yeterli; yoğun eşzamanlı yazmada PostgreSQL
   değerlendirilebilir.
5. **Varsayılan admin:** Setup çalıştırılmadan önce `admin/admin` hesabı vardır;
   production'da setup'ı Caddy/DNS'ten önce çalıştırın.
6. **Eklenti volume:** Base image `plugins` için anonymous volume tanımlar;
   plugin image içinden kopyalanır. Güncellemede yeni container oluşturun.
7. **Caddy doğrulama:** Değişiklikten önce yedek alın; `caddy validate` olmadan
   reload etmeyin.
8. **Test belgeleri:** `tests/e2e/fixtures/e-devlet.pdf` sentetiktir; gerçek
   belge değildir.

## 10. Hızlı komutlar

```bash
# durum
cd /root/çalıştay/kanboard
docker compose -f deploy/docker-compose.yml ps

# loglar
docker logs -f ghdcalistay-app

# sağlık
curl -fsS https://calistay.globalhukukcular.com/healthcheck.php

# staging
docker rm -f ghdcalistay-test-app
docker run -d --name ghdcalistay-test-app --network gakademi_default \
  -p 127.0.0.1:8180:80 -v ghdcalistay_test_data:/var/www/app/data \
  -e TZ=Europe/Istanbul -e ENABLE_URL_REWRITE=true -e LOG_DRIVER=stderr \
  -e TRUSTED_PROXY_HEADERS=X-Forwarded-For \
  -e TRUSTED_PROXY_NETWORKS=172.16.0.0/12,10.0.0.0/8 \
  ghd-calistay:local
```

## 11. Sonraki session için öneriler

1. Public GitHub repo'yu oluştur, secret scan yap, README ekran görüntülerini ekle.
2. Repo URL'sini ve son commit hash'ini `docs/DURUM.md` içine işle.
3. Kullanıcıya admin parolasını ilet ve ilk girişte değiştirmesini öner.
4. İleride SMTP/IMAP ve e-posta bildirimleri; isteğe bağlı PostgreSQL geçişi.
