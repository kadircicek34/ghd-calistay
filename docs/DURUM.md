# GH Çalıştay — DURUM

Son güncelleme: 22 Kasım 2026, 19:00 (Europe/Istanbul)

## Kısa özet

Kanboard 1.2.58, Global Hukukçular Derneği için **GH Çalıştay** adıyla
özelleştirildi ve canlıya alındı. Üyelik başvurusu, e-Devlet belgesi, otomatik
üyelik onayı, admin belge incelemesi/iptali, Türkçe kurumsal arayüz, üyelere
özel kanban panosu, DNS ve TLS hazır. Staging E2E testleri ve canlı
smoke/üyelik akışı PASS.

| | |
|---|---|
| Canlı adres | `https://calistay.globalhukukcular.com` |
| Staging | `http://127.0.0.1:8180` |
| Sunucu | `169.58.198.19` |
| Repo | `https://github.com/kadircicek34/ghd-calistay` (public, `main`) |
| Container | `ghdcalistay-app` (production), `ghdcalistay-test-app` (staging) |
| Veri | `ghdcalistay_data` volume |
| Admin | `kadir` / `/root/çalıştay/admin-credentials.txt` |
| Caddy | `/root/Gakademi/deploy/Caddyfile` + `deploy/Caddyfile.snippet` |

## Tamamlananlar

- Logo ve bronz-altın/lacivert/krem kurumsal tema.
- Landing, giriş, başvuru, başarı, durum, KVKK, kullanım koşulları sayfaları.
- `GhdMembership` eklentisi: başvuru, otomatik onay, durum, admin iptal/red,
  belge indirme.
- Başvuruda anında Kanboard kullanıcısı oluşturma + varsayılan `GH Çalıştay`
  projesine üyelik.
- Varsayılan proje, Türkçe sütunlar, karşılama görevi, admin hesabı.
- Dockerfile, compose, Caddy snippet, setup ve yedekleme scriptleri.
- E2E testleri 22+ senaryo PASS; PHP lint temiz.
- Production container healthy; DNS A kaydı canlı; Let's Encrypt sertifikası
  geçerli; HTTP → HTTPS 308.
- Canlı smoke: landing/login/başvuru/admin/mobil PASS; canlı üyelik akışı
  (başvuru → otomatik onay → giriş → pano) PASS; canlı test verisi silindi.
- Mevcut 2 gerçek bekleyen başvuru yeni politikayla otomatik onaylandı; belgeleri
  korunuyor. Admin listesinde 2 onaylı, 0 bekleyen başvuru var.
- `docs/screenshots/` altında staging ve canlı kanıt görselleri.
- Public GitHub repo oluşturuldu; ilk public commit `8257b52`.
- Secret scan temiz; `.env`, `data/`, belgeler ve parolalar repoda yok.

## Sıradaki adımlar

1. Kullanıcıya canlı URL, admin kullanıcı adı ve parola dosyasını bildir.
2. İlk girişte admin parolasını değiştirmesini öner.
3. İsteğe bağlı: SMTP/IMAP ve e-posta bildirimleri; PostgreSQL geçişi.

## Bilinen notlar

- Staging test veritabanında çok sayıda sentetik başvuru var; production
  volume'u ayrı ve temizdir.
- Staging'de IP `172.19.0.1` görünür (Caddy yok). Production'da Caddy +
  `TRUSTED_PROXY_*` ile gerçek istemci IP'si kullanılır.
- E-posta bildirimi yapılandırılmadı; durum sorgulama sayfası kullanılır.
