# GH Çalıştay

**Global Hukukçular Derneği** üyelerine özel görev, proje ve etkinlik panosu.

[Canlı platform](https://calistay.globalhukukcular.com) ·
[Başvuru](https://calistay.globalhukukcular.com/basvuru) ·
[Dernek](https://globalhukukcular.com)

---

GH Çalıştay, Kanboard'un üye yönetimi ve kurumsal kimlikle özelleştirilmiş bir
fork'udur. Dernek üyeleri başvuru formunu doldurur, e-Devlet belgesini yükler
ve yönetici onayından sonra ortak kanban panosuna erişir.

## Öne çıkanlar

- **Üyelere özel erişim:** Herkese açık kayıt yoktur. Başvurular otomatik
  onaylanır; hesap anında oluşturulur. Yönetim belgeleri sonradan inceleyebilir
  ve üyeliği iptal edebilir.
- **Üyelik başvurusu:** Ad soyad, kullanıcı adı, e-posta, telefon, doğum
  tarihi, hukuk öğrencisi/mezunu bilgisi, okul, mezuniyet yılı, e-Devlet
  belgesi ve KVKK/kullanım koşulları onayı.
- **Güvenli belge saklama:** Belgeler web kökü dışında, rastgele dosya
  adlarıyla ve `0600` izinleriyle tutulur. Yalnızca yönetici oturumu ve
  tek kullanımlık CSRF anahtarıyla indirilebilir.
- **Otomatik onay:** Form gönderildiğinde üyelik anında açılır; kullanıcı hemen
  giriş yapabilir.
- **Yönetici denetimi:** Otomatik onaylanan başvurular, belge görüntüleme,
  gerekçeli reddetme/iptal ve bekleyen istisnalar için manuel onay.
- **Türkçe kurumsal arayüz:** Lacivert, bronz-altın ve krem paleti; özel
  landing, giriş, başvuru, durum ve yönetim ekranları; mobil uyumlu.
- **Kanban:** Projeler, sütunlar, görevler, sorumlular, yorumlar ve dosyalar
  Kanboard'un olgun iş akışıyla yönetilir.
- **KVKK:** Aydınlatma metni, kullanım koşulları, çerez politikası ve veri
  sahibi hakları sayfaları dahildir.

## Mimari

| Katman | Teknoloji |
|---|---|
| Uygulama | Kanboard 1.2.58 (PHP 8.5) |
| Üyelik eklentisi | `plugins/GhdMembership` |
| Veritabanı | SQLite (`ghdcalistay_data` volume) |
| Web sunucusu | Kanboard image içindeki nginx |
| Reverse proxy | Caddy (`gakademi-caddy-1`) |
| Dağıtım | Docker Compose |

`GhdMembership` eklentisi şunları ekler:

- `/` ve `/anasayfa` — kurumsal landing page
- `/basvuru`, `/basvuru/gonder`, `/basvuru/basarili` — başvuru akışı
- `/basvuru/durum` — başvuru durumu sorgulama
- `/kvkk`, `/kullanim-kosullari` — yasal metinler
- `/admin/uyelik-basvurulari` — yönetici başvuru yönetimi
- `ghd_membership_applications` tablosu ve özel tema varlıkları

Eklenti, Kanboard'un kendi kullanıcı/rol/görev tablolarına dokunmaz. Onaylanan
başvuru için Kanboard kullanıcısı oluşturur ve varsayılan `GH Çalıştay`
projesine üye olarak ekler.

## Kurulum

Gereksinimler: Docker, Docker Compose ve mevcut `gakademi_default` Docker ağı.

```bash
git clone https://github.com/kadircicek34/ghd-calistay.git
cd ghd-calistay

docker compose -f deploy/docker-compose.yml up -d --build

GHD_ADMIN_PASSWORD='güçlü-bir-şifre' \
  docker exec -e GHD_ADMIN_PASSWORD \
  ghdcalistay-app php /var/www/app/plugins/GhdMembership/tools/setup.php --force
```

Mevcut bekleyen başvuruları yeni otomatik onay politikasına geçirmek için:

```bash
docker exec ghdcalistay-app php \
  /var/www/app/plugins/GhdMembership/tools/approve-pending.php
```

Ayrıntılı dağıtım, Caddy ve yedekleme notları: [`deploy/README.md`](deploy/README.md).

## Testler

```bash
# PHP sözdizimi
find plugins/GhdMembership -name '*.php' -print0 | xargs -0 -n1 php -l

# Tarayıcı testleri (staging container)
BASE_URL=http://127.0.0.1:8180 \
GHD_ADMIN_USER=kadir \
GHD_ADMIN_PASSWORD='...' \
NODE_PATH=/path/to/node_modules \
node tests/e2e/membership.spec.js
```

Test kapsamı: landing, mobil taşma, zorunlu alanlar, geçersiz belge, otomatik
onay, anında üye girişi, pano erişimi, admin belge indirme, üyelik iptali,
admin yetkisiz erişim, red akışı, CSRF ve belge gizliliği.
Ayrıntılar ve kanıtlar: [`docs/test-report.md`](docs/test-report.md).

## Güvenlik

- `.env`, veritabanı, yüklenen belgeler ve yönetici parolası repoda yoktur.
- Başvuru formu CSRF, honeypot, IP başına saatlik limit ve sunucu tarafı
  doğrulama kullanır.
- Şifreler bcrypt ile saklanır; oturumlar Kanboard'un veritabanı tabanlı
  oturum yönetimini kullanır.
- Belge indirme yalnızca admin rolü + tek kullanımlık CSRF anahtarıyla çalışır.
- Hassas dosyalar için `X-Content-Type-Options: nosniff`, `Cache-Control:
  no-store` ve `Content-Disposition: attachment` başlıkları gönderilir.

## Lisans ve atıf

Bu proje MIT lisansı altındadır. Temel uygulama
[Kanboard](https://github.com/kanboard/kanboard) projesinden türetilmiştir
(MIT). Özgün telif ve lisans bilgileri [`LICENSE`](LICENSE) ve
[`NOTICE`](NOTICE) dosyalarında korunmuştur.

Global Hukukçular Derneği markası, logosu ve kurumsal içerikleri derneğe
aittir; MIT lisansı bu markaları kapsamaz.
