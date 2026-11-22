<?php
$page_title = 'GH Çalıştay | Global Hukukçular Derneği';
$page_description = 'Global Hukukçular Derneği üyelerine özel görev, proje ve etkinlik panosu. Başvurunuzu yapın, üyeliğiniz otomatik onaylansın ve hemen ekibinizle çalışın.';
$applyUrl = $this->url->href('MembershipController', 'apply', array('plugin' => 'GhdMembership'));
$loginUrl = $this->url->href('AuthController', 'login');
$dashboardUrl = $this->url->href('DashboardController', 'show');
$adminUrl = $this->url->href('MembershipController', 'admin', array('plugin' => 'GhdMembership'));
$statusUrl = $this->url->href('MembershipController', 'status', array('plugin' => 'GhdMembership'));
$asset = $this->ghdMembership->assetUrl('plugins/GhdMembership/Assets');
include __DIR__ . '/_head.php';
?>
<section class="ghd-hero">
    <div class="ghd-hero-glow" aria-hidden="true"></div>
    <div class="ghd-hero-inner">
        <div class="ghd-hero-copy">
            <p class="ghd-eyebrow"><span></span> Global Hukukçular Derneği</p>
            <h1>Hukukçuların ortak <em>çalışma alanı.</em></h1>
            <p class="ghd-lead">Etkinlikleri, projeleri ve görevleri tek bir panoda planlayın. Dernek çalışmalarında yerinizi alın, ekibinizle şeffaf biçimde ilerleyin.</p>
            <div class="ghd-hero-actions">
                <?php if ($is_logged): ?>
                    <a class="ghd-button ghd-button-primary ghd-button-large" href="<?= $dashboardUrl ?>">Panoya git</a>
                    <?php if ($is_admin): ?>
                        <a class="ghd-button ghd-button-ghost ghd-button-large" href="<?= $adminUrl ?>">
                            Başvurular
                            <?php if (! empty($pending_count)): ?><span class="ghd-count"><?= (int) $pending_count ?></span><?php endif ?>
                        </a>
                    <?php endif ?>
                <?php else: ?>
                    <a class="ghd-button ghd-button-primary ghd-button-large" href="<?= $applyUrl ?>">Üyelik başvurusu</a>
                    <a class="ghd-button ghd-button-ghost ghd-button-large" href="<?= $loginUrl ?>">Giriş yap</a>
                <?php endif ?>
            </div>
            <ul class="ghd-trust-list" aria-label="Platform özellikleri">
                <li><span class="ghd-dot"></span> Yalnızca onaylı üyeler</li>
                <li><span class="ghd-dot"></span> Türkçe arayüz</li>
                <li><span class="ghd-dot"></span> Güvenli belge saklama</li>
            </ul>
        </div>
        <div class="ghd-hero-art" aria-hidden="true">
            <div class="ghd-art-frame">
                <div class="ghd-art-top">
                    <span class="ghd-art-brand"><img src="<?= $this->text->e($asset) ?>/logo.png" alt=""> GH Çalıştay</span>
                    <span class="ghd-art-pill">Ekip panosu</span>
                </div>
                <div class="ghd-board-preview">
                    <div class="ghd-preview-column">
                        <div class="ghd-preview-head"><span class="dot dot-gold"></span> Yapılacak <small>3</small></div>
                        <div class="ghd-preview-card">
                            <span class="ghd-chip">Etkinlik</span>
                            <strong>Dijital Hukuk webinari</strong>
                            <small>Konuşmacı ve akış planı</small>
                            <div class="ghd-preview-meta"><span class="ghd-avatar">EY</span><span>12 Kas</span></div>
                        </div>
                        <div class="ghd-preview-card">
                            <span class="ghd-chip ghd-chip-blue">İletişim</span>
                            <strong>Üye bilgilendirme</strong>
                            <small>E-posta taslağını hazırla</small>
                            <div class="ghd-preview-meta"><span class="ghd-avatar">MK</span><span>14 Kas</span></div>
                        </div>
                    </div>
                    <div class="ghd-preview-column">
                        <div class="ghd-preview-head"><span class="dot dot-blue"></span> Devam ediyor <small>2</small></div>
                        <div class="ghd-preview-card ghd-preview-card-active">
                            <span class="ghd-chip ghd-chip-gold">Proje</span>
                            <strong>Uluslararası staj</strong>
                            <small>Başvurular değerlendiriliyor</small>
                            <div class="ghd-preview-meta"><span class="ghd-avatar">SD</span><span>18 Kas</span></div>
                        </div>
                        <div class="ghd-progress">
                            <span style="width: 68%"></span>
                            <small>%68 tamamlandı</small>
                        </div>
                    </div>
                    <div class="ghd-preview-column">
                        <div class="ghd-preview-head"><span class="dot dot-green"></span> Tamamlandı <small>2</small></div>
                        <div class="ghd-preview-card ghd-preview-card-done">
                            <span class="ghd-chip ghd-chip-green">Etkinlik</span>
                            <strong>Kariyer buluşması</strong>
                            <small>Katılım raporu arşivlendi</small>
                            <div class="ghd-preview-meta"><span class="ghd-avatar">AC</span><span>09 Kas</span></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="ghd-art-badge">
                <span class="ghd-art-badge-icon">✓</span>
                <span><strong>Onaylı üye</strong><small>Panele erişim açık</small></span>
            </div>
        </div>
    </div>
</section>

<section class="ghd-stat-strip" aria-label="Platform bilgileri">
    <div class="ghd-stat"><strong>01</strong><span>Başvuru</span><small>Kısa form ve e-Devlet belgesi</small></div>
    <div class="ghd-stat"><strong>02</strong><span>Otomatik onay</span><small>Üyeliğiniz anında açılır</small></div>
    <div class="ghd-stat"><strong>03</strong><span>Ortak pano</span><small>Görevler, ekipler, etkinlikler</small></div>
    <div class="ghd-stat"><strong>04</strong><span>Güvenli erişim</span><small>Yalnızca onaylı üyeler</small></div>
</section>

<section class="ghd-section" id="ozellikler">
    <div class="ghd-section-head">
        <p class="ghd-eyebrow"><span></span> Neler yapabilirsiniz?</p>
        <h2>Dernek çalışmalarını tek yerde <em>yönetin.</em></h2>
        <p>Komisyonlardan etkinlik ekiplerine, hukuk projelerinden iletişim çalışmalarına kadar her işi görünür ve takip edilebilir kılın.</p>
    </div>
    <div class="ghd-feature-grid">
        <article class="ghd-feature-card">
            <span class="ghd-feature-icon">
                <svg viewBox="0 0 24 24" role="img" aria-hidden="true"><path d="M4 5h16M4 12h16M4 19h10" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/><circle cx="19" cy="19" r="2.4" fill="none" stroke="currentColor" stroke-width="1.6"/></svg>
            </span>
            <h3>Görevler ve sorumluluklar</h3>
            <p>Her işi bir karta dönüştürün; sorumlusunu, teslim tarihini ve önceliğini netleştirin.</p>
        </article>
        <article class="ghd-feature-card">
            <span class="ghd-feature-icon">
                <svg viewBox="0 0 24 24" role="img" aria-hidden="true"><circle cx="8" cy="8" r="3" fill="none" stroke="currentColor" stroke-width="1.6"/><circle cx="16.5" cy="9.5" r="2.5" fill="none" stroke="currentColor" stroke-width="1.6"/><path d="M3 20c0-3 2.3-5.3 5.3-5.3S13.6 17 13.6 20M14.2 20c0-2.4 1.5-4.2 3.7-4.2 1.6 0 2.9.8 3.6 2.1" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
            </span>
            <h3>Ekipler ve projeler</h3>
            <p>Komisyonları, etkinlik ekiplerini ve projeleri ayrı panolarda buluşturun; yetkileri yönetin.</p>
        </article>
        <article class="ghd-feature-card">
            <span class="ghd-feature-icon">
                <svg viewBox="0 0 24 24" role="img" aria-hidden="true"><path d="M4 19V5M4 19h16" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/><path d="M7 15l4-4 3 2.5 5-6" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </span>
            <h3>Şeffaf ilerleme</h3>
            <p>Yapılacak, devam eden ve tamamlanan işleri tek bakışta görün; darboğazları erken fark edin.</p>
        </article>
        <article class="ghd-feature-card">
            <span class="ghd-feature-icon">
                <svg viewBox="0 0 24 24" role="img" aria-hidden="true"><path d="M12 3l7 3v5c0 4.6-2.7 8.4-7 10-4.3-1.6-7-5.4-7-10V6l7-3z" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/><path d="M9 12l2 2 4-4" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </span>
            <h3>Üyelere özel alan</h3>
            <p>Pano yalnızca onaylı dernek üyelerine açıktır. e-Devlet belgeleri herkese açık değildir.</p>
        </article>
    </div>
</section>

<section class="ghd-section ghd-section-alt" id="nasil-calisir">
    <div class="ghd-section-head">
        <p class="ghd-eyebrow"><span></span> Nasıl çalışır?</p>
        <h2>Üç adımda panonuza <em>katılın.</em></h2>
        <p>Başvurunuz dernek yönetimi tarafından incelenir. Onaydan sonra kullanıcı adınız ve şifrenizle giriş yapabilirsiniz.</p>
    </div>
    <div class="ghd-steps">
        <article class="ghd-step">
            <span class="ghd-step-number">01</span>
            <h3>Başvurunuzu yapın</h3>
            <p>Kısa formu doldurun; doğum tarihi, hukuk durumu ve e-Devlet belgenizi ekleyin.</p>
        </article>
        <article class="ghd-step">
            <span class="ghd-step-number">02</span>
            <h3>Otomatik onay</h3>
            <p>Başvurunuz anında onaylanır; hesabınız ve pano erişiminiz hemen hazır olur.</p>
        </article>
        <article class="ghd-step">
            <span class="ghd-step-number">03</span>
            <h3>Panonuza girin</h3>
            <p>Kullanıcı adınız ve şifrenizle giriş yapın; projelere ve ekip arkadaşlarınıza erişin.</p>
        </article>
    </div>
</section>

<section class="ghd-security">
    <div class="ghd-security-inner">
        <div class="ghd-security-icon">
            <svg viewBox="0 0 24 24" role="img" aria-hidden="true"><path d="M12 3l7 3v5c0 4.6-2.7 8.4-7 10-4.3-1.6-7-5.4-7-10V6l7-3z" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linejoin="round"/><rect x="9" y="11" width="6" height="5" rx="1" fill="none" stroke="currentColor" stroke-width="1.4"/><path d="M10.2 11V9.8a1.8 1.8 0 013.6 0V11" fill="none" stroke="currentColor" stroke-width="1.4"/></svg>
        </div>
        <div>
            <h2>Belgeleriniz güvende.</h2>
            <p>e-Devlet belgeleri yalnızca üyelik doğrulaması için kullanılır; herkese açık bir adresi yoktur. Başvurunuz otomatik onaylansa da belgeniz dernek yönetimi tarafından ayrıca incelenebilir. Verileriniz KVKK kapsamında korunur.</p>
        </div>
        <a class="ghd-button ghd-button-ghost ghd-button-large" href="<?= $this->url->href('MembershipController', 'kvkk', array('plugin' => 'GhdMembership')) ?>">KVKK metnini oku</a>
    </div>
</section>

<section class="ghd-cta">
    <div class="ghd-cta-inner">
        <div>
            <p class="ghd-eyebrow"><span></span> Global Hukukçular Derneği</p>
            <h2>Dernek çalışmalarında <em>yerinizi alın.</em></h2>
            <p>Üyelik başvurunuzu tamamlayın; üyeliğiniz anında onaylansın ve GH Çalıştay panosunda ekip arkadaşlarınızla birlikte üretmeye başlayın.</p>
        </div>
        <div class="ghd-cta-actions">
            <?php if ($is_logged): ?>
                <a class="ghd-button ghd-button-primary ghd-button-large" href="<?= $dashboardUrl ?>">Panoya git</a>
            <?php else: ?>
                <a class="ghd-button ghd-button-primary ghd-button-large" href="<?= $applyUrl ?>">Üyelik başvurusu</a>
                <a class="ghd-button ghd-button-ghost ghd-button-large" href="<?= $statusUrl ?>">Başvuru durumu</a>
            <?php endif ?>
        </div>
    </div>
</section>
<?php include __DIR__ . '/_foot.php'; ?>
