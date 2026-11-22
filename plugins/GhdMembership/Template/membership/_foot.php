<?php
$asset = $this->ghdMembership->assetUrl('plugins/GhdMembership/Assets');
$homeUrl = $this->url->href('MembershipController', 'index', array('plugin' => 'GhdMembership'));
$applyUrl = $this->url->href('MembershipController', 'apply', array('plugin' => 'GhdMembership'));
$statusUrl = $this->url->href('MembershipController', 'status', array('plugin' => 'GhdMembership'));
$loginUrl = $this->url->href('AuthController', 'login');
$kvkkUrl = $this->url->href('MembershipController', 'kvkk', array('plugin' => 'GhdMembership'));
$termsUrl = $this->url->href('MembershipController', 'terms', array('plugin' => 'GhdMembership'));
?>
</main>
<footer class="ghd-pub-footer">
    <div class="ghd-pub-footer-inner">
        <div class="ghd-footer-brand">
            <a class="ghd-brand ghd-brand-light" href="<?= $homeUrl ?>">
                <span class="ghd-brand-mark">
                    <img src="<?= $this->text->e($asset) ?>/logo.png" alt="" width="52" height="52">
                </span>
                <span class="ghd-brand-text">
                    <strong>GH Çalıştay</strong>
                    <small>Global Hukukçular Derneği</small>
                </span>
            </a>
            <p>Dernek çalışmalarını, etkinlikleri ve projeleri tek bir ortak panoda buluşturan üyelere özel çalışma alanı.</p>
        </div>
        <div class="ghd-footer-column">
            <h2>Platform</h2>
            <a href="<?= $applyUrl ?>">Üyelik başvurusu</a>
            <a href="<?= $statusUrl ?>">Başvuru durumu</a>
            <a href="<?= $loginUrl ?>">Giriş yap</a>
        </div>
        <div class="ghd-footer-column">
            <h2>Dernek</h2>
            <a href="https://globalhukukcular.com" target="_blank" rel="noopener noreferrer">globalhukukcular.com</a>
            <a href="https://globalhukukcular.com/e-uyelik" target="_blank" rel="noopener noreferrer">E-Üyelik</a>
            <a href="https://globalhukukcular.com/iletisim" target="_blank" rel="noopener noreferrer">İletişim</a>
        </div>
        <div class="ghd-footer-column">
            <h2>Yasal</h2>
            <a href="<?= $kvkkUrl ?>">KVKK aydınlatma metni</a>
            <a href="<?= $termsUrl ?>">Kullanım koşulları</a>
        </div>
    </div>
    <div class="ghd-pub-footer-bottom">
        <span>© <?= date('Y') ?> Global Hukukçular Derneği. Tüm hakları saklıdır.</span>
        <span>Üyelere özel · Türkçe · KVKK uyumlu</span>
    </div>
</footer>
</body>
</html>
