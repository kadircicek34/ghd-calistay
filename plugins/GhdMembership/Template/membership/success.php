<?php
$page_title = 'Başvurunuz alındı | GH Çalıştay';
$page_description = 'GH Çalıştay üyelik başvurunuz alındı. Yönetici onayından sonra giriş yapabilirsiniz.';
$statusUrl = $this->url->href('MembershipController', 'status', array('plugin' => 'GhdMembership'));
$loginUrl = $this->url->href('AuthController', 'login');
$homeUrl = $this->url->href('MembershipController', 'index', array('plugin' => 'GhdMembership'));
include __DIR__ . '/_head.php';
?>
<section class="ghd-result-section">
    <div class="ghd-result-card ghd-result-success">
        <span class="ghd-result-icon" aria-hidden="true">
            <svg viewBox="0 0 24 24"><path d="M5 12.5l4.2 4.2L19 7" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </span>
        <p class="ghd-eyebrow"><span></span> Başvuru tamamlandı</p>
        <h1>Başvurunuz <em>alındı.</em></h1>
        <p class="ghd-result-lead">Teşekkür ederiz. Başvurunuz dernek yönetiminin incelemesine gönderildi. Onaydan sonra aynı kullanıcı adı ve şifreyle giriş yapabilirsiniz.</p>

        <?php if (! empty($username)): ?>
            <div class="ghd-result-detail">
                <span>Kullanıcı adınız</span>
                <strong><?= $this->text->e($username) ?></strong>
            </div>
        <?php endif ?>

        <div class="ghd-result-steps">
            <div class="ghd-result-step is-done"><span>1</span><div><strong>Başvuru alındı</strong><small>Bilgileriniz kaydedildi.</small></div></div>
            <div class="ghd-result-step"><span>2</span><div><strong>Yönetici incelemesi</strong><small>Belgeniz ve bilgileriniz kontrol edilir.</small></div></div>
            <div class="ghd-result-step"><span>3</span><div><strong>Panele erişim</strong><small>Onaydan sonra giriş yapabilirsiniz.</small></div></div>
        </div>

        <div class="ghd-result-actions">
            <a class="ghd-button ghd-button-primary ghd-button-large" href="<?= $statusUrl ?>">Başvuru durumunu sorgula</a>
            <a class="ghd-button ghd-button-ghost ghd-button-large" href="<?= $loginUrl ?>">Giriş sayfasına git</a>
        </div>
        <p class="ghd-result-note">Durum sorgulaması için başvuruda kullandığınız e-posta adresini veya kullanıcı adını girin.</p>
    </div>
</section>
<?php include __DIR__ . '/_foot.php'; ?>
