# GH Çalıştay — canlı deploy notları

| | |
|---|---|
| Hedef alan adı | `https://calistay.globalhukukcular.com` |
| Geçici doğrulama | `https://calistay.169.58.198.19.nip.io` |
| Sunucu | `169.58.198.19` |
| Reverse proxy | `gakademi-caddy-1` (`gakademi_default` ağı) |
| Uygulama | `ghdcalistay-app` → `ghd-calistay:local` |
| Veri | `ghdcalistay_data` volume (SQLite + e-Devlet belgeleri) |

## İlk kurulum

```bash
cd /root/çalıştay/kanboard
docker compose -f deploy/docker-compose.yml up -d --build
```

İlk açılışta Kanboard veritabanını ve `GhdMembership` tablosunu otomatik
oluşturur. Ardından yönetici hesabını ve varsayılan projeyi kurun:

```bash
GHD_ADMIN_PASSWORD='güçlü-bir-şifre' \
  docker exec -e GHD_ADMIN_PASSWORD -e GHD_ADMIN_EMAIL -e GHD_ADMIN_NAME \
  ghdcalistay-app php /var/www/app/plugins/GhdMembership/tools/setup.php --force
```

`setup.php` idempotenttir. İlk çalıştırmada bootstrap `admin` hesabını
`kadir` olarak yeniden adlandırır, rolünü `app-admin` yapar ve şifresini
günceller. Sonraki çalıştırmalarda `--force` verilmedikçe şifreyi değiştirmez.

Yalnızca ilk kurulumda çalıştırın; parola `/root/çalıştay/admin-credentials.txt`
dosyasında 0600 izinleriyle saklanır ve repoya commit edilmez.

## Güncelleme

```bash
cd /root/çalıştay/kanboard
git pull
docker compose -f deploy/docker-compose.yml up -d --build
```

## Sağlık ve loglar

```bash
docker compose -f deploy/docker-compose.yml ps
docker logs -f ghdcalistay-app
curl -fsS http://127.0.0.1:8180/healthcheck.php   # staging
curl -fsS http://127.0.0.1:8080/healthcheck.php   # production (Caddy arkasında)
```

Üretimde doğrudan port yayınlanmaz; Caddy `ghdcalistay-app:80` adresine proxy
yapar. Yerel doğrulama için test container'ı 127.0.0.1:8180'e bağlanır.

## Caddy

```bash
cd /root/Gakademi/deploy
cp Caddyfile Caddyfile.bak-ghd-calistay
cat /root/çalıştay/kanboard/deploy/Caddyfile.snippet >> Caddyfile
docker cp Caddyfile gakademi-caddy-1:/tmp/Caddyfile.test
docker exec gakademi-caddy-1 caddy validate --config /tmp/Caddyfile.test
docker exec gakademi-caddy-1 caddy reload --config /etc/caddy/Caddyfile
```

DNS A kaydı `calistay.globalhukukcular.com → 169.58.198.19` yayıldığında
Let's Encrypt sertifikası otomatik alınır. O zamana kadar
`calistay.169.58.198.19.nip.io` geçici doğrulama adresidir.

## Yedekleme

```bash
docker run --rm -v ghdcalistay_data:/data -v /root/çalıştay/backups:/backup alpine \
  tar czf /backup/ghdcalistay-$(date +%F).tgz -C /data .
```

`data/` içinde SQLite veritabanı, oturumlar, önbellek ve
`membership_docs/` altında e-Devlet belgeleri bulunur. Yedekler erişim
kontrollü tutulmalıdır.

## Güvenlik notları

- `.env`, `admin-credentials.txt` ve `data/` git dışındadır.
- e-Devlet belgeleri web kökü dışında, rastgele adlarla ve 0600 izinleriyle saklanır.
- Belge indirme yalnızca admin oturumu + tek kullanımlık CSRF anahtarı ile çalışır.
- Başvuru formu CSRF, honeypot, IP başına saatlik limit ve sunucu tarafı doğrulama kullanır.
- Kanboard'un genel kayıt akışı kapalıdır; hesaplar yalnızca yönetici onayıyla açılır.
