<?php
$asset = $this->ghdMembership->assetUrl('plugins/GhdMembership/Assets');
$applyUrl = $this->url->href('MembershipController', 'apply', array('plugin' => 'GhdMembership'));
$statusUrl = $this->url->href('MembershipController', 'status', array('plugin' => 'GhdMembership'));
$loginStatusUrl = $this->url->href('MembershipController', 'checkStatus', array('plugin' => 'GhdMembership'));
$homeUrl = $this->url->href('MembershipController', 'index', array('plugin' => 'GhdMembership'));
$kvkkUrl = $this->url->href('MembershipController', 'kvkk', array('plugin' => 'GhdMembership'));
$termsUrl = $this->url->href('MembershipController', 'terms', array('plugin' => 'GhdMembership'));
?>
<div class="ghd-auth">
    <aside class="ghd-auth-aside">
        <a class="ghd-brand ghd-brand-light" href="<?= $homeUrl ?>">
            <span class="ghd-brand-mark">
                <img src="<?= $this->text->e($asset) ?>/logo.png" alt="" width="56" height="56">
            </span>
            <span class="ghd-brand-text">
                <strong>GH Çalıştay</strong>
                <small>Global Hukukçular Derneği</small>
            </span>
        </a>
        <div class="ghd-auth-aside-copy">
            <p class="ghd-eyebrow"><span></span> Üyelere özel çalışma alanı</p>
            <h1>Birlikte üretmek için <em>tek pano.</em></h1>
            <p>Dernek çalışmalarını, etkinlikleri ve projeleri ekip arkadaşlarınızla birlikte planlayın.</p>
            <ul class="ghd-auth-benefits">
                <li><span>✓</span> Görevler ve sorumluluklar tek yerde</li>
                <li><span>✓</span> Komisyonlar ve projeler için ayrı panolar</li>
                <li><span>✓</span> Yalnızca onaylı üyelere güvenli erişim</li>
            </ul>
        </div>
        <blockquote class="ghd-auth-quote">
            “Hukuk, birlikte düşünmenin ve birlikte üretmenin en güçlü alanlarından biridir.”
            <cite>Global Hukukçular Derneği</cite>
        </blockquote>
        <div class="ghd-auth-aside-footer">
            <a href="https://globalhukukcular.com" target="_blank" rel="noopener noreferrer">globalhukukcular.com</a>
            <span>·</span>
            <a href="<?= $kvkkUrl ?>">KVKK</a>
            <span>·</span>
            <a href="<?= $termsUrl ?>">Kullanım koşulları</a>
        </div>
    </aside>

    <div class="ghd-auth-main">
        <div class="ghd-auth-card">
            <div class="ghd-auth-mobile-brand">
                <a class="ghd-brand" href="<?= $homeUrl ?>">
                    <span class="ghd-brand-mark"><img src="<?= $this->text->e($asset) ?>/logo.png" alt="" width="44" height="44"></span>
                    <span class="ghd-brand-text"><strong>GH Çalıştay</strong><small>Global Hukukçular Derneği</small></span>
                </a>
            </div>
            <p class="ghd-eyebrow"><span></span> Üye girişi</p>
            <h2>Tekrar hoş geldiniz.</h2>
            <p class="ghd-auth-subtitle">Panonuza erişmek için kullanıcı adınızı ve şifrenizi girin.</p>

            <?php if (isset($errors['login'])): ?>
                <div class="ghd-alert ghd-alert-error" role="alert"><?= $this->text->e($errors['login']) ?></div>
            <?php endif ?>

            <div class="ghd-login-status" id="ghd-login-status" role="status" hidden></div>

            <?php if (! HIDE_LOGIN_FORM): ?>
                <form class="ghd-form ghd-login-form" method="post" action="<?= $this->url->href('AuthController', 'check') ?>" data-status-url="<?= $loginStatusUrl ?>">
                    <?= $this->form->csrf() ?>
                    <div class="ghd-field">
                        <label for="form-username">Kullanıcı adı</label>
                        <input type="text" name="username" id="form-username" value="<?= $this->text->e(isset($values['username']) ? $values['username'] : '') ?>" autofocus autocomplete="username" placeholder="Kullanıcı adınız">
                    </div>
                    <div class="ghd-field">
                        <label for="form-password">Şifre</label>
                        <input type="password" name="password" id="form-password" autocomplete="current-password" placeholder="Şifreniz">
                    </div>

                    <?php if (isset($captcha) && $captcha): ?>
                        <div class="ghd-field">
                            <label for="form-captcha">Güvenlik kodu</label>
                            <img class="ghd-captcha" src="<?= $this->url->href('CaptchaController', 'image') ?>" alt="Güvenlik kodu">
                            <input type="text" name="captcha" id="form-captcha" autocomplete="off" required>
                        </div>
                    <?php endif ?>

                    <div class="ghd-login-options">
                        <?php if (REMEMBER_ME_AUTH): ?>
                            <label class="ghd-check ghd-check-inline">
                                <input type="checkbox" name="remember_me" value="1" checked>
                                <span>Beni hatırla</span>
                            </label>
                        <?php endif ?>
                        <?php if ($this->app->config('password_reset') == 1): ?>
                            <a class="ghd-text-link" href="<?= $this->url->href('PasswordResetController', 'create') ?>">Şifremi unuttum</a>
                        <?php endif ?>
                    </div>

                    <button type="submit" class="ghd-button ghd-button-primary ghd-button-block ghd-button-large">Giriş yap</button>
                </form>
            <?php endif ?>

            <div class="ghd-auth-actions">
                <p>Henüz hesabınız yok mu? <a href="<?= $applyUrl ?>">Üyelik başvurusu yapın</a></p>
                <p>Başvurunuzun durumunu <a href="<?= $statusUrl ?>">buradan sorgulayın</a>.</p>
            </div>
        </div>
        <p class="ghd-auth-legal">GH Çalıştay yalnızca Global Hukukçular Derneği'nin onaylı üyelerine açıktır. Giriş bilgilerinizi kimseyle paylaşmayın.</p>
    </div>
</div>
