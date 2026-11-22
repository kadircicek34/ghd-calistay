<?php
$approved = ! empty($approved);
$page_title = $approved ? 'Üyeliğiniz onaylandı | GH Çalıştay' : 'Başvurunuz alındı | GH Çalıştay';
$page_description = $approved
    ? 'GH Çalıştay üyeliğiniz otomatik olarak onaylandı. Hemen giriş yapabilirsiniz.'
    : 'GH Çalıştay üyelik başvurunuz alındı. Başvurunuz yönetici incelemesine alındı.';
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
        <?php if ($approved): ?>
            <h1>Üyeliğiniz <em>onaylandı.</em></h1>
            <p class="ghd-result-lead">Başvurunuz otomatik olarak onaylandı. Aşağıdaki kullanıcı adı ve başvuruda belirlediğiniz şifreyle hemen giriş yapabilirsiniz.</p>
        <?php else: ?>
            <h1>Başvurunuz <em>alındı.</em></h1>
            <p class="ghd-result-lead">Teşekkür ederiz. Başvurunuz alındı; otomatik onay tamamlanamadığı için yönetici incelemesine alındı. Durumunuzu aşağıdan sorgulayabilirsiniz.</p>
        <?php endif ?>

        <?php if (! empty($username)): ?>
            <div class="ghd-result-detail">
                <span>Kullanıcı adınız</span>
                <strong><?= $this->text->e($username) ?></strong>
            </div>
        <?php endif ?>

        <div class="ghd-result-steps">
            <div class="ghd-result-step is-done"><span>1</span><div><strong>Başvuru alındı</strong><small>Bilgileriniz kaydedildi.</small></div></div>
            <div class="ghd-result-step <?= $approved ? 'is-done' : '' ?>"><span>2</span><div><strong>Otomatik onay</strong><small><?= $approved ? 'Üyeliğiniz anında açıldı.' : 'Yönetici incelemesi bekleniyor.' ?></small></div></div>
            <div class="ghd-result-step <?= $approved ? 'is-done' : '' ?>"><span>3</span><div><strong>Panele erişim</strong><small><?= $approved ? 'Hemen giriş yapabilirsiniz.' : 'Onaydan sonra erişim açılır.' ?></small></div></div>
        </div>

        <div class="ghd-result-actions">
            <?php if ($approved): ?>
                <a class="ghd-button ghd-button-primary ghd-button-large" href="<?= $loginUrl ?>">Panoya giriş yap</a>
                <a class="ghd-button ghd-button-ghost ghd-button-large" href="<?= $statusUrl ?>">Başvuru durumunu sorgula</a>
            <?php else: ?>
                <a class="ghd-button ghd-button-primary ghd-button-large" href="<?= $statusUrl ?>">Başvuru durumunu sorgula</a>
                <a class="ghd-button ghd-button-ghost ghd-button-large" href="<?= $loginUrl ?>">Giriş sayfasına git</a>
            <?php endif ?>
        </div>
        <p class="ghd-result-note">
            <?= $approved
                ? 'Belgeniz dernek yönetimi tarafından ayrıca incelenebilir. Bir sorun tespit edilirse üyelik iptal edilebilir.'
                : 'Durum sorgulaması için başvuruda kullandığınız e-posta adresini veya kullanıcı adını girin.' ?>
        </p>
    </div>
</section>
<?php include __DIR__ . '/_foot.php'; ?>
