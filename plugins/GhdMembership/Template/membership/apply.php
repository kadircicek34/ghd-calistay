<?php
$page_title = 'Üyelik Başvurusu | GH Çalıştay';
$page_description = 'Global Hukukçular Derneği üyeliği için GH Çalıştay başvuru formu. Doğum tarihi, hukuk durumu ve e-Devlet belgesi gereklidir.';
$applyUrl = $this->url->href('MembershipController', 'apply', array('plugin' => 'GhdMembership'));
$saveUrl = $this->url->href('MembershipController', 'save', array('plugin' => 'GhdMembership'));
$statusUrl = $this->url->href('MembershipController', 'status', array('plugin' => 'GhdMembership'));
$loginUrl = $this->url->href('AuthController', 'login');
$kvkkUrl = $this->url->href('MembershipController', 'kvkk', array('plugin' => 'GhdMembership'));
$termsUrl = $this->url->href('MembershipController', 'terms', array('plugin' => 'GhdMembership'));
$asset = $this->ghdMembership->assetUrl('plugins/GhdMembership/Assets');
$showError = function ($field) use ($errors) {
    return isset($errors[$field]) ? '<span class="ghd-field-error" id="error-' . $field . '">' . $this->text->e($errors[$field]) . '</span>' : '';
};
$invalid = function ($field) use ($errors) {
    return isset($errors[$field]) ? ' aria-invalid="true" aria-describedby="error-' . $field . '"' : '';
};
include __DIR__ . '/_head.php';
?>
<section class="ghd-page-hero ghd-page-hero-compact">
    <div class="ghd-page-hero-inner">
        <p class="ghd-eyebrow"><span></span> Üyelik başvurusu</p>
        <h1>GH Çalıştay'a <em>katılın.</em></h1>
        <p>Formu doldurun; başvurunuz anında onaylansın ve panoya erişiminiz hemen açılsın.</p>
    </div>
</section>

<section class="ghd-form-section">
    <div class="ghd-form-layout">
        <aside class="ghd-form-aside">
            <div class="ghd-aside-card">
                <h2>Başvuru için hazır olun</h2>
                <ul class="ghd-checklist">
                    <li>Doğum tarihiniz</li>
                    <li>Hukuk öğrencisi veya mezunu bilginiz</li>
                    <li>Okul / fakülte ve mezuniyet yılınız</li>
                    <li>e-Devlet üzerinden alınmış öğrenci belgesi veya diploma (PDF, JPEG, PNG)</li>
                </ul>
                <div class="ghd-aside-note">
                    <strong>Belge güvenliği</strong>
                    <p>e-Devlet belgeniz herkese açık değildir; yalnızca yetkili dernek yöneticileri görüntüleyebilir. Başvurunuz otomatik onaylansa da belgeniz ayrıca incelenebilir.</p>
                </div>
            </div>
            <div class="ghd-aside-card ghd-aside-card-muted">
                <h2>Zaten hesabınız var mı?</h2>
                <p>Onaylı üyeler kullanıcı adı ve şifresiyle giriş yapabilir.</p>
                <a class="ghd-button ghd-button-ghost" href="<?= $loginUrl ?>">Giriş yap</a>
                <a class="ghd-text-link" href="<?= $statusUrl ?>">Başvuru durumumu sorgula</a>
            </div>
        </aside>

        <div class="ghd-form-card">
            <div class="ghd-form-card-head">
                <span class="ghd-form-step">Başvuru formu</span>
                <h2>Bilgilerinizi paylaşın</h2>
                <p>Tüm alanlar zorunludur. Bilgileriniz yalnızca üyelik doğrulaması ve platform erişimi için kullanılır. Başvurunuz otomatik onaylanır; hemen giriş yapabilirsiniz.</p>
            </div>

            <?php if (! empty($errors)): ?>
                <div class="ghd-alert ghd-alert-error" role="alert">
                    <strong>Form gönderilemedi.</strong>
                    <p>Lütfen işaretli alanları kontrol edin.</p>
                    <?php if (isset($errors['general'])): ?><p><?= $this->text->e($errors['general']) ?></p><?php endif ?>
                </div>
            <?php endif ?>

            <form class="ghd-form" method="post" action="<?= $saveUrl ?>" enctype="multipart/form-data" novalidate>
                <?= $this->form->csrf() ?>
                <div class="ghd-hp" aria-hidden="true">
                    <label for="website">Web sitesi</label>
                    <input type="text" id="website" name="website" tabindex="-1" autocomplete="off">
                </div>

                <fieldset class="ghd-fieldset">
                    <legend>Kişisel bilgiler</legend>
                    <div class="ghd-field-grid">
                        <div class="ghd-field ghd-field-full">
                            <label for="full_name">Ad soyad</label>
                            <input type="text" id="full_name" name="full_name" value="<?= $this->text->e($values['full_name']) ?>" autocomplete="name" placeholder="Adınız ve soyadınız"<?= $invalid('full_name') ?>>
                            <?= $showError('full_name') ?>
                        </div>
                        <div class="ghd-field">
                            <label for="username">Kullanıcı adı</label>
                            <input type="text" id="username" name="username" value="<?= $this->text->e($values['username']) ?>" autocomplete="username" placeholder="ornek.kullanici"<?= $invalid('username') ?>>
                            <small>3-30 karakter; küçük harf, rakam, nokta, tire, alt çizgi.</small>
                            <?= $showError('username') ?>
                        </div>
                        <div class="ghd-field">
                            <label for="email">E-posta</label>
                            <input type="email" id="email" name="email" value="<?= $this->text->e($values['email']) ?>" autocomplete="email" placeholder="ornek@eposta.com"<?= $invalid('email') ?>>
                            <?= $showError('email') ?>
                        </div>
                        <div class="ghd-field">
                            <label for="phone">Telefon</label>
                            <input type="tel" id="phone" name="phone" value="<?= $this->text->e($values['phone']) ?>" autocomplete="tel" placeholder="05xx xxx xx xx"<?= $invalid('phone') ?>>
                            <?= $showError('phone') ?>
                        </div>
                        <div class="ghd-field">
                            <label for="birth_date">Doğum tarihi</label>
                            <input type="date" id="birth_date" name="birth_date" value="<?= $this->text->e($values['birth_date']) ?>" autocomplete="bday"<?= $invalid('birth_date') ?>>
                            <?= $showError('birth_date') ?>
                        </div>
                    </div>
                </fieldset>

                <fieldset class="ghd-fieldset">
                    <legend>Hukuk durumu</legend>
                    <div class="ghd-radio-grid">
                        <label class="ghd-radio-card<?= $values['legal_status'] === 'student' ? ' is-selected' : '' ?>">
                            <input type="radio" name="legal_status" value="student" <?= $values['legal_status'] === 'student' ? 'checked' : '' ?>>
                            <span><strong>Hukuk öğrencisiyim</strong><small>Lisans öğrencisi</small></span>
                        </label>
                        <label class="ghd-radio-card<?= $values['legal_status'] === 'graduate' ? ' is-selected' : '' ?>">
                            <input type="radio" name="legal_status" value="graduate" <?= $values['legal_status'] === 'graduate' ? 'checked' : '' ?>>
                            <span><strong>Hukuk mezunuyum</strong><small>Mezun / avukat</small></span>
                        </label>
                    </div>
                    <?= $showError('legal_status') ?>
                    <div class="ghd-field-grid ghd-field-grid-spaced">
                        <div class="ghd-field ghd-field-full">
                            <label for="school">Okul / fakülte</label>
                            <input type="text" id="school" name="school" value="<?= $this->text->e($values['school']) ?>" placeholder="Hukuk fakültesi adı"<?= $invalid('school') ?>>
                            <?= $showError('school') ?>
                        </div>
                        <div class="ghd-field">
                            <label for="graduation_year">Mezuniyet yılı <span>(mezunlar için)</span></label>
                            <input type="text" id="graduation_year" name="graduation_year" value="<?= $this->text->e($values['graduation_year']) ?>" inputmode="numeric" placeholder="2024"<?= $invalid('graduation_year') ?>>
                            <?= $showError('graduation_year') ?>
                        </div>
                        <div class="ghd-field">
                            <label for="document_type">Belge türü</label>
                            <select id="document_type" name="document_type"<?= $invalid('document_type') ?>>
                                <option value="student_certificate"<?= $values['document_type'] === 'student_certificate' ? ' selected' : '' ?>>Öğrenci belgesi</option>
                                <option value="diploma"<?= $values['document_type'] === 'diploma' ? ' selected' : '' ?>>Diploma</option>
                            </select>
                            <?= $showError('document_type') ?>
                        </div>
                    </div>
                </fieldset>

                <fieldset class="ghd-fieldset">
                    <legend>e-Devlet belgesi</legend>
                    <div class="ghd-upload">
                        <label class="ghd-upload-box" for="document">
                            <span class="ghd-upload-icon">
                                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 16V4m0 0L7.5 8.5M12 4l4.5 4.5" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/><path d="M5 14v3.5A1.5 1.5 0 006.5 19h11a1.5 1.5 0 001.5-1.5V14" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
                            </span>
                            <span><strong>Belge seçin</strong><small>PDF, JPEG veya PNG · en fazla 10 MB</small></span>
                            <input type="file" id="document" name="document" accept=".pdf,.jpg,.jpeg,.png,application/pdf,image/jpeg,image/png"<?= $invalid('document') ?>>
                        </label>
                        <div class="ghd-upload-filename" data-upload-name hidden></div>
                        <?= $showError('document') ?>
                    </div>
                </fieldset>

                <fieldset class="ghd-fieldset">
                    <legend>Hesap bilgileri</legend>
                    <div class="ghd-field-grid">
                        <div class="ghd-field">
                            <label for="password">Şifre</label>
                            <input type="password" id="password" name="password" autocomplete="new-password" placeholder="En az 8 karakter"<?= $invalid('password') ?>>
                            <small>En az 8 karakter, bir harf ve bir rakam.</small>
                            <?= $showError('password') ?>
                        </div>
                        <div class="ghd-field">
                            <label for="password_confirmation">Şifre tekrar</label>
                            <input type="password" id="password_confirmation" name="password_confirmation" autocomplete="new-password" placeholder="Şifrenizi tekrar girin"<?= $invalid('password_confirmation') ?>>
                            <?= $showError('password_confirmation') ?>
                        </div>
                    </div>
                </fieldset>

                <fieldset class="ghd-fieldset ghd-consent">
                    <legend>Onaylar</legend>
                    <label class="ghd-check">
                        <input type="checkbox" name="kvkk_consent" value="1" <?= ! empty($values['kvkk_consent']) ? 'checked' : '' ?>>
                        <span><a href="<?= $kvkkUrl ?>" target="_blank" rel="noopener noreferrer">KVKK aydınlatma metnini</a> okudum ve kişisel verilerimin belirtilen amaçlarla işlenmesini onaylıyorum.</span>
                    </label>
                    <?= $showError('kvkk_consent') ?>
                    <label class="ghd-check">
                        <input type="checkbox" name="terms_consent" value="1" <?= ! empty($values['terms_consent']) ? 'checked' : '' ?>>
                        <span><a href="<?= $termsUrl ?>" target="_blank" rel="noopener noreferrer">Kullanım koşullarını</a> okudum ve kabul ediyorum.</span>
                    </label>
                    <?= $showError('terms_consent') ?>
                </fieldset>

                <div class="ghd-form-actions">
                    <button type="submit" class="ghd-button ghd-button-primary ghd-button-large">Başvuruyu gönder</button>
                    <span>Başvurunuz otomatik onaylanır; hemen giriş yapabilirsiniz.</span>
                </div>
            </form>
        </div>
    </div>
</section>
<?php include __DIR__ . '/_foot.php'; ?>
