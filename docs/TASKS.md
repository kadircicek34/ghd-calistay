# GH Çalıştay — Fazlar, Görevler ve Kabul Kriterleri

> Katı politika: Aşağıdaki kabul kriterlerinden biri karşılanmıyorsa görev
> **bitmiş sayılmaz**. Kanıtlar `docs/test-report.md` ve `docs/screenshots/`
> altındadır.

## Faz 0 — Keşif ve marka temeli ✅

- [x] Kanboard 1.2.58 kaynağı ve plugin mimarisi incelendi.
- [x] Global Hukukçular Derneği logosu ve kurumsal paleti çıkarıldı.
- [x] Alt alan adı ve erişim modeli kullanıcıyla netleştirildi:
      `calistay.globalhukukcular.com`, başvuru + otomatik onay (admin iptal yetkisi).
- [x] Mevcut Caddy/Docker topolojisi ve diğer servisler belgelendi.
- [x] Çalışma kuralları `AGENTS.md` içine yazıldı.

## Faz 1 — Üyelik eklentisi + otomatik onay ✅

Kanıt: `plugins/GhdMembership`, E2E PASS, PHP lint temiz.

Kabul kriterleri:

1. Başvuru formu ad soyad, kullanıcı adı, e-posta, telefon, doğum tarihi,
   hukuk durumu, okul, mezuniyet yılı, e-Devlet belgesi, şifre, KVKK ve
   kullanım koşulları onayını toplar.
2. Doğum tarihi, hukuk durumu, belge ve onaylar olmadan başvuru kaydedilmez.
3. Başvuru otomatik onaylanır; Kanboard hesabı anında oluşturulur ve varsayılan
   projeye eklenir. Otomatik onay tamamlanamazsa başvuru `pending` kalır.
4. Yönetici tüm başvuruları görür; doğum tarihi, hukuk durumu ve belgeyi
   inceleyebilir; otomatik onaylanan üyeliği gerekçeli olarak iptal edebilir.
5. İptal edilen üye hesabı devre dışı kalır ve giriş yapamaz.
6. Reddedilen/iptal edilen başvuru sahibi durum sayfasında ve giriş ekranında
   Türkçe gerekçe görür; hesabı kullanılamaz.
7. Belge web kökü dışında, rastgele adla, `0600` izinle saklanır; yalnızca
   admin + tek kullanımlık CSRF anahtarıyla indirilebilir.
8. Başvuru formu CSRF, honeypot ve IP başına saatlik limit kullanır.
9. Eklenti şeması temiz SQLite veritabanında otomatik oluşur.
10. PHP sözdizimi tüm eklenti dosyalarında temizdir.

## Faz 2 — Kurumsal arayüz ✅

Kanıt: `docs/screenshots/01–13`, mobil taşma testi.

Kabul kriterleri:

1. Landing page lacivert/bronz-altın/krem paletiyle, logo ve Türkçe içerikle
   yayınlanır; mobilde yatay taşma yoktur.
2. Giriş, başvuru, başarı, durum, KVKK ve kullanım koşulları sayfaları
   kurumsal kimlikle uyumludur.
3. Uygulama başlığı, favicon, metadata ve varsayılan dil Türkçedir.
4. Yönetici ekranları markalıdır; bekleyen başvuru sayacı ve dashboard uyarısı
   görünür.
5. Kanboard pano/başlık/footer yüzeyi markayla uyumludur; işlevsellik bozulmaz.

## Faz 3 — Dağıtım ⏳

- [x] Dockerfile, compose, Caddy snippet ve setup script hazırlandı.
- [x] Staging container 127.0.0.1:8180 üzerinde çalıştı; healthcheck 200.
- [x] Production container `ghdcalistay-app` ayağa kaldırıldı; healthy.
- [x] Güçlü admin parolası oluşturuldu ve `/root/çalıştay/admin-credentials.txt`
      (0600) içine yazıldı.
- [x] Caddy yedeği alındı, snippet eklendi, `caddy validate` ve reload başarılı.
- [x] DNS A kaydı `calistay.globalhukukcular.com → 169.58.198.19` eklendi.
- [x] Let's Encrypt sertifikası alındı; HTTP → HTTPS 308 doğrulandı.
- [x] Mevcut `dil`, `spor`, `oyun`, `kariyer` 200; `akademi` bilinen 500
      durumunu koruyor (bu projeden önce de 500'dü).

## Faz 4 — Test ve canlı doğrulama ⏳

- [x] E2E: landing, mobil, zorunlu alanlar, geçersiz dosya, başvuru, otomatik
      onay, anında üye girişi, pano, admin belge incelemesi/iptali, admin 403,
      red akışı, CSRF, belge gizliliği.
- [x] E2E ikinci kez temiz veriyle PASS.
- [x] Canlı smoke test: landing/login/başvuru/admin/TLS/mobil.
- [x] Canlı üyelik akışı: test başvurusu → otomatik onay → giriş → pano → temizlik.
- [x] Test verileri canlıdan temizlendi; mevcut 2 gerçek bekleyen başvuru yeni
      politikayla otomatik onaylandı (0 bekleyen).

## Faz 5 — GitHub ve dokümantasyon ⏳

- [x] Public repo `kadircicek34/ghd-calistay` oluşturuldu.
- [x] README, AGENTS, NOTICE, deploy notları ve test raporu push edildi.
- [x] Secret scan temiz; `.env`, `data/`, belgeler ve parolalar yok.
- [x] MIT lisansı ve Kanboard atfı korunuyor.
- [x] Repo public erişilebilir; ilk public commit `8257b52`.

## Faz 6 — Devir ⏳

- [x] `docs/HANDOFF.md` güncel.
- [x] Kullanıcıya canlı URL, admin kullanıcı adı ve parola dosyası bildirildi.
- [x] Bilinen sınırlamalar ve sonraki adımlar yazıldı.
