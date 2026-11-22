# GhdMembership — GH Çalıştay eklentisi

Kanboard 1.2.58 için Global Hukukçular Derneği üyelik ve marka eklentisi.

## Ne ekler?

| Rota | Erişim | Açıklama |
|---|---|---|
| `/` | Public | Kurumsal landing page |
| `/basvuru` | Public | Üyelik başvuru formu |
| `/basvuru/gonder` | Public + CSRF | Başvuruyu kaydeder |
| `/basvuru/basarili` | Public | Başvuru alındı ekranı |
| `/basvuru/durum` | Public | Durum sorgulama |
| `/basvuru/durum/kontrol` | Public + POST | Giriş ekranı için JSON durum |
| `/kvkk`, `/kullanim-kosullari` | Public | Yasal metinler |
| `/admin/uyelik-basvurulari` | Admin | Başvuru listesi |
| `/admin/uyelik-basvurulari/:id` | Admin | Detay + karar |
| `/admin/uyelik-basvurulari/:id/onayla` | Admin + CSRF | Hesap oluşturur |
| `/admin/uyelik-basvurulari/:id/reddet` | Admin + CSRF | Gerekçeli ret |
| `/admin/uyelik-basvurulari/:id/belge` | Admin + CSRF | Belge indirir |

## Veri modeli

`Schema/Sqlite.php` → `ghd_membership_applications`:

- Kimlik/iletişim: `full_name`, `username`, `email`, `phone`
- Doğrulama: `birth_date`, `legal_status`, `school`, `graduation_year`
- Hesap: `password_hash` (bcrypt), `status` (`pending/approved/rejected`)
- Belge: `document_type`, `document_name`, `document_path`, `document_mime`,
  `document_size`
- Onaylar: `kvkk_consent`, `terms_consent`
- Denetim: `ip_address`, `user_agent`, `created_at`, `reviewed_at`,
  `reviewed_by`, `review_note`, `user_id`

## Güvenlik

- Başvuru ve admin formları CSRF korumalı.
- Belge türü magic byte + `getimagesize` ile doğrulanır; en fazla 10 MB.
- Belgeler `DATA_DIR/membership_docs` altında rastgele adla, `0600` izinle.
- Belge indirme admin + tek kullanımlık CSRF gerektirir; `nosniff`,
  `no-store`, `attachment` başlıkları gönderilir.
- Başvuru formunda honeypot ve IP başına saatlik limit
  (`ghd_hourly_application_limit`, varsayılan 10).
- Onayda rol `app-user` sabittir; kullanıcı adı/e-posta çakışması kontrol edilir.
- Bekleyen/reddedilen başvuru için kullanıcı hesabı oluşturulmaz.

## Kurulum

Eklenti `plugins/GhdMembership` altındadır; Kanboard açılışta otomatik yükler.
İlk kurulumda `tools/setup.php` çalıştırılır (bkz. `deploy/README.md`).

## Test

`tests/e2e/membership.spec.js` — Playwright; staging container'a karşı çalışır.
