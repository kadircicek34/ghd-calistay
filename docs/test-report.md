# GH Çalıştay — Test ve Kabul Kanıtları

> İlk doğrulama: 22 Kasım 2026, staging container `127.0.0.1:8180`.
> Canlı doğrulama: 22 Kasım 2026, `https://calistay.globalhukukcular.com`.

## Otomatik testler

Komut:

```bash
BASE_URL=http://127.0.0.1:8180 \
GHD_ADMIN_USER=kadir \
GHD_ADMIN_PASSWORD='***' \
NODE_PATH=/root/kariyer/career-assistant/frontend/node_modules \
node tests/e2e/membership.spec.js
```

Sonuç: **PASS** (22.11.2026, 18:23).

| # | Senaryo | Sonuç |
|---|---|---|
| 1 | Landing 200, başlık, hero ve başvuru CTA | ✅ |
| 2 | Mobil 390×844 yatay taşma yok | ✅ (overflow ≤ 2px) |
| 3 | Boş başvuru reddedilir; KVKK hatası görünür | ✅ |
| 3b | `.txt` dosyası belge olarak reddedilir | ✅ |
| 4 | Geçerli öğrenci başvurusu kaydedilir | ✅ |
| 5 | Durum sorgusu `Onay bekliyor` | ✅ |
| 6 | Bekleyen kullanıcı girişi engellenir; Türkçe açıklama | ✅ |
| 7 | Admin dashboard bekleyen uyarısı + dropdown linki | ✅ |
| 8 | Admin başvuru detayı: doğum tarihi, hukuk durumu, okul, belge | ✅ |
| 9 | Belge indirme çalışır | ✅ |
| 10 | Admin onayı üye hesabı oluşturur | ✅ |
| 11 | Onay sonrası durum `Onaylandı` + giriş CTA | ✅ |
| 12 | Onaylı üye girişi ve dashboard | ✅ |
| 13 | Üye panoyu açar; Türkçe sütunlar + hoş geldin görevi | ✅ |
| 14 | Üye admin başvuru sayfasında 403 alır | ✅ |
| 15 | Red akışı gerekçeyle çalışır; durum `Reddedildi` | ✅ |
| 16 | Reddedilen kullanıcı girişinde gerekçe görünür | ✅ |
| 17 | Anonim belge erişimi engellenir (302/403) | ✅ |
| 18 | Üye belge erişimi engellenir (302/403) | ✅ |
| 19 | Admin belge erişimi CSRF olmadan 403 | ✅ |
| 20 | Anonim `/dashboard` login'e yönlenir | ✅ |
| 21 | CSRF'siz başvuru POST'u 403 | ✅ |
| 22 | Onaylı e-posta ile ikinci başvuru reddedilir | ✅ |

## Canlı doğrulama

| Kontrol | Sonuç |
|---|---|
| DNS `calistay.globalhukukcular.com` (8.8.8.8 / 1.1.1.1 / NS) | ✅ `169.58.198.19` |
| HTTPS landing | ✅ HTTP 200 |
| TLS sertifikası | ✅ Let's Encrypt, `CN=calistay.globalhukukcular.com`, 20.02.2027'ye kadar |
| `ssl_verify_result` | ✅ 0 |
| HTTP → HTTPS | ✅ 308 Permanent Redirect |
| HSTS / nosniff / frame / referrer / permissions | ✅ Caddy başlıkları |
| Landing mobil 390×844 | ✅ yatay taşma yok |
| Login / başvuru / admin | ✅ 200 |
| Canlı üyelik akışı | ✅ başvuru → admin onayı → üye girişi → pano |
| Canlı test verisi temizliği | ✅ uygulama + kullanıcı + belge silindi; 0 başvuru |
| Mevcut servisler | dil/spor/oyun/kariyer 200; akademi bilinen 500 |

Canlı ekran görüntüleri: `docs/screenshots/12-live-landing.png`,
`docs/screenshots/13-live-member-board.png`.

## PHP lint

```bash
find plugins/GhdMembership -name '*.php' \
  | xargs -n1 docker run --rm -v "$PWD:/src:ro" --entrypoint php \
    ghd-calistay:local -l
```

Sonuç: Tüm eklenti dosyaları ve `app/Core/Http/Route.php` için
**No syntax errors detected**.

## Güvenlik doğrulamaları

- e-Devlet belgesi `data/membership_docs/` altında, web kökü dışında.
- Dosya adı `random_bytes(16)` ile üretiliyor; orijinal ad yalnızca indirmede.
- Dosya türü magic byte + `getimagesize` ile doğrulanıyor; `.txt` reddedildi.
- Belge indirme: admin rolü + tek kullanımlık CSRF; `nosniff`, `no-store`.
- Başvuru formu: CSRF zorunlu; honeypot; IP başına saatlik limit (varsayılan 10).
- Bekleyen/reddedilen başvuru için Kanboard kullanıcısı oluşturulmuyor.
- Onayda kullanıcı adı/e-posta çakışması kontrol ediliyor; rol `app-user`
  sabit; kullanıcı kendi rolünü yükseltemiyor.
- Admin sayfaları ACL + `requireAdministrator()` ile korunuyor.

## Ekran görüntüleri

`docs/screenshots/`:

- `01-landing-desktop.png`
- `02-landing-mobile.png`
- `03-application-validation.png`
- `04-application-success.png`
- `05-status-pending.png`
- `07-admin-application-detail.png`
- `08b-status-approved.png`
- `09b-member-board.png`
- `10-member-admin-forbidden.png`
- `11-status-rejected.png`

Test görsellerindeki `example.com` adresleri ve isimler sentetiktir.

## Bilinen sınırlamalar

- E-posta gönderimi yapılandırılmadı; başvuru durumu web üzerinden sorgulanır.
- SQLite kullanılıyor; dernek ölçeğinde yeterli, yüksek eşzamanlı yazmada
  PostgreSQL'e geçiş değerlendirilebilir.
- Kanboard zamanlanmış görevleri (cron) image içindeki crontab ile çalışır.
- Canlı test verileri temizlendikten sonra admin listesi boş olmalıdır.
