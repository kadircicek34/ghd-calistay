<?php if ($this->user->isAdmin()): ?>
    <?php
    $pending = $this->ghdMembership->countPending();
    $recentApproved = $this->ghdMembership->countRecentApproved(7);
    $adminBase = $this->url->href('MembershipController', 'admin', array('plugin' => 'GhdMembership'));
    $separator = strpos($adminBase, '?') === false ? '?' : '&';
    ?>
    <?php if ($pending > 0): ?>
        <div class="ghd-admin-banner">
            <div class="ghd-admin-banner-icon">!</div>
            <div>
                <strong><?= (int) $pending ?> bekleyen üyelik başvurusu var.</strong>
                <p>Otomatik onayı tamamlanamayan başvuruları inceleyin; belgeleri görüntüleyip onaylayın veya reddedin.</p>
            </div>
            <a class="btn btn-blue" href="<?= $adminBase . $separator ?>status=pending">Bekleyenleri aç</a>
        </div>
    <?php elseif ($recentApproved > 0): ?>
        <div class="ghd-admin-banner">
            <div class="ghd-admin-banner-icon">✓</div>
            <div>
                <strong>Son 7 günde <?= (int) $recentApproved ?> üyelik otomatik onaylandı.</strong>
                <p>Belgeleri gözden geçirebilir, gerekirse üyeliği iptal edebilirsiniz.</p>
            </div>
            <a class="btn btn-blue" href="<?= $adminBase . $separator ?>status=approved">Onaylananları gör</a>
        </div>
    <?php endif ?>
<?php endif ?>
