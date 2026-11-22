# GH Çalıştay — Ajan Çalışma Kuralları

> Bu dosya her oturumda okunur. Kurallar kullanıcı talimatıdır; istisnasız
> uygulanır. Çelişki durumunda **kullanıcının son talimatı > bu dosya > repo
> dokümanları** sırası geçerlidir.

## 0. Proje

- Kaynak: [kanboard/kanboard](https://github.com/kanboard/kanboard) (MIT)
- Hedef: Global Hukukçular Derneği üyelerine özel görev/proje panosu
- Canlı adres: `https://calistay.globalhukukcular.com`
- Referans site: `https://globalhukukcular.com`
- Public repo: `https://github.com/kadircicek34/ghd-calistay`
- Sunucu: `169.58.198.19`, ortak Caddy: `gakademi-caddy-1`

## 1. Kanıt ilkesi

Varsayım, tahmin ve halüsinasyonla hareket etmek yasaktır. Her iddia; dosya
yolu, komut çıktısı, test sonucu, HTTP yanıtı veya ekran görüntüsü ile
kanıtlanır. "Çalışması lazım" kabul edilmez; çalıştırılmış olması gerekir.

## 2. Kabul kriterleri

Bir görev, aşağıdakilerin tamamı kanıtlanmadan "bitti" sayılmaz:

1. Landing page canlıda 200 döner; logo, Türkçe içerik ve mobil görünüm doğrudur.
2. Giriş yapmamış ziyaretçi yalnızca landing, giriş, başvuru, durum ve yasal
   sayfalara erişir; uygulama rotaları login'e yönlenir.
3. Başvuru formu doğum tarihi, hukuk durumu (öğrenci/mezun), e-Devlet belgesi,
   KVKK ve kullanım koşulları onayı olmadan gönderilemez.
4. Başvuru otomatik onaylanır; üye hesabı anında oluşturulur. Otomatik onay
   tamamlanamazsa başvuru `pending` kalır ve yönetici incelemesine düşer.
5. Yüklenen e-Devlet belgesi public erişime açık değildir; yalnızca yetkili
   admin görüntüleyebilir.
6. Admin başvuruyu doğum tarihi, hukuk durumu ve belgeyle birlikte görür;
   gerekirse üyeliği gerekçeli olarak iptal eder. Ret/iptal gerekçesi başvuru
   sahibine gösterilir.
7. Otomatik onaylanan üye hemen giriş yapıp panoyu kullanabilir; iptal edilen
   veya reddedilen başvuru sahibi açık Türkçe hata alır ve hesap açılamaz.
8. Uygulama markası GH Çalıştay'dır; logo, favicon, metadata ve varsayılan
   arayüz dili Türkçedir.
9. Canlıda geçerli HTTPS sertifikası vardır; HTTP, HTTPS'e yönlenir; DNS
   `calistay` kaydı sunucuya gider.
10. Public GitHub reposunda sır (parola, API anahtarı, `.env`, belge) yoktur;
    MIT lisansı ve Kanboard atfı korunur.
11. Playwright tarayıcı testleri landing, başvuru, otomatik onay, anında üye
    girişi, pano erişimi, admin belge incelemesi/iptali, red akışı ve korumalı
    rotalar için geçerlidir.
12. Mevcut `akademi`, `dil`, `spor`, `oyun` ve `kariyer` servisleri bozulmamıştır.

## 3. Süreç

- Dinamik loop: fazları sırayla bitir; kabul kriterini kanıtlamadan ilerleme.
- Keşif ve kodlama işleri alt ajanlara devredilebilir; ana ajan raporu doğrular.
- Alt ajanlar background'da çalıştırılır; ana ajan başlarında beklemez.
- Her anlamlı adımdan sonra `type(scope): açıklama` formatında commit atılır.
- Context en fazla 3 kez compact edilir; 3. compact sonrası kullanıcı uyarılır.
- Her fazın sonunda `docs/DURUM.md` ve `docs/TASKS.md` güncellenir.

## 4. Güvenlik ve sırlar

- DNS paneli ve sunucu kimlik bilgileri repoya yazılmaz.
- `.env`, `data/`, `admin-credentials.txt`, yüklenen belgeler git dışıdır.
- Mevcut Caddy yapılandırması değiştirilmeden önce yedeklenir.
- Mevcut container, volume ve veritabanlarına dokunulmaz.
- Admin parolası yalnızca `/root/çalıştay/admin-credentials.txt` (0600) içinde
  tutulur; kullanıcıya ilk girişte değiştirmesi önerilir.

## 5. Komutlar

```bash
# Build / deploy
cd /root/çalıştay/kanboard
docker compose -f deploy/docker-compose.yml up -d --build

# Durum / log
docker compose -f deploy/docker-compose.yml ps
docker logs -f ghdcalistay-app

# Sağlık
curl -fsS http://127.0.0.1:8180/healthcheck.php   # staging
curl -fsS https://calistay.globalhukukcular.com/healthcheck.php

# Caddy
docker exec gakademi-caddy-1 caddy validate --config /etc/caddy/Caddyfile
docker exec gakademi-caddy-1 caddy reload --config /etc/caddy/Caddyfile

# Test
BASE_URL=http://127.0.0.1:8180 \
GHD_ADMIN_USER=kadir GHD_ADMIN_PASSWORD='...' \
NODE_PATH=/root/kariyer/career-assistant/frontend/node_modules \
node tests/e2e/membership.spec.js
```
