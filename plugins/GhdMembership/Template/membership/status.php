<?php
$page_title = 'Başvuru Durumu | GH Çalıştay';
$page_description = 'GH Çalıştay üyelik başvurunuzun durumunu e-posta adresiniz veya kullanıcı adınızla sorgulayın.';
$statusUrl = $this->url->href('MembershipController', 'status', array('plugin' => 'GhdMembership'));
$applyUrl = $this->url->href('MembershipController', 'apply', array('plugin' => 'GhdMembership'));
$loginUrl = $this->url->href('AuthController', 'login');
$statusClass = $result ? $this->ghdMembership->statusClass($result['status']) : '';
include __DIR__ . '/_head.php';
?>
<section class="ghd-result-section">
    <div class="ghd-status-card">
        <p class="ghd-eyebrow"><span></span> Başvuru durumu</p>
        <h1>Başvurunuzun <em>durumunu görün.</em></h1>
        <p class="ghd-result-lead">Başvuruda kullandığınız e-posta adresini veya kullanıcı adını girin. Durum bilgisi yalnızca başvuru sahibine yol gösterir; belge veya kişisel bilgi gösterilmez.</p>

        <form class="ghd-status-form" method="get" action="<?= $statusUrl ?>">
            <label for="identifier">E-posta veya kullanıcı adı</label>
            <div class="ghd-status-form-row">
                <input type="text" id="identifier" name="identifier" value="<?= $this->text->e($identifier) ?>" autocomplete="username" placeholder="ornek@eposta.com">
                <button type="submit" class="ghd-button ghd-button-primary">Sorgula</button>
            </div>
        </form>

        <?php if ($result): ?>
            <div class="ghd-status-result <?= $this->text->e($statusClass) ?>" role="status">
                <span class="ghd-status-dot"></span>
                <div>
                    <strong><?= $this->text->e($this->ghdMembership->statusLabel($result['status'])) ?></strong>
                    <p><?= $this->text->e($result['message']) ?></p>
                </div>
            </div>
            <?php if ($result['status'] === 'approved'): ?>
                <a class="ghd-button ghd-button-primary ghd-button-large" href="<?= $loginUrl ?>">Giriş yap</a>
            <?php elseif ($result['status'] === 'none'): ?>
                <a class="ghd-button ghd-button-primary ghd-button-large" href="<?= $applyUrl ?>">Yeni başvuru yap</a>
            <?php else: ?>
                <p class="ghd-result-note">Onay süreciyle ilgili sorularınız için <a href="https://globalhukukcular.com/iletisim" target="_blank" rel="noopener noreferrer">dernek iletişim kanallarını</a> kullanabilirsiniz.</p>
            <?php endif ?>
        <?php endif ?>

        <div class="ghd-status-links">
            <a href="<?= $applyUrl ?>">Yeni başvuru</a>
            <a href="<?= $loginUrl ?>">Giriş yap</a>
            <a href="https://globalhukukcular.com/iletisim" target="_blank" rel="noopener noreferrer">İletişim</a>
        </div>
    </div>
</section>
<?php include __DIR__ . '/_foot.php'; ?>
