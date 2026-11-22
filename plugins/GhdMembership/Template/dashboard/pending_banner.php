<?php if ($this->user->isAdmin()): ?>
    <?php $pending = $this->ghdMembership->countPending(); ?>
    <?php if ($pending > 0): ?>
        <div class="ghd-admin-banner">
            <div class="ghd-admin-banner-icon">!</div>
            <div>
                <strong><?= (int) $pending ?> bekleyen üyelik başvurusu var.</strong>
                <p>Başvuruları inceleyin, belgeleri görüntüleyin ve üyelerin panoya erişimini onaylayın.</p>
            </div>
            <a class="btn btn-blue" href="<?= $this->url->href('MembershipController', 'admin', array('plugin' => 'GhdMembership')) ?>">Başvuruları aç</a>
        </div>
    <?php endif ?>
<?php endif ?>
