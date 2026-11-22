<?php if ($this->user->isAdmin()): ?>
    <?php $pending = $this->ghdMembership->countPending(); ?>
    <li>
        <a href="<?= $this->url->href('MembershipController', 'admin', array('plugin' => 'GhdMembership')) ?>">
            <i class="fa fa-fw fa-id-card-o" aria-hidden="true"></i>
            Üyelik Başvuruları
            <?php if ($pending > 0): ?>
                <span class="ghd-nav-badge"><?= (int) $pending ?></span>
            <?php endif ?>
        </a>
    </li>
<?php endif ?>
