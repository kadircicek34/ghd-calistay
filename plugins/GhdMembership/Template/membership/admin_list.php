<?php
$adminUrl = $this->url->href('MembershipController', 'admin', array('plugin' => 'GhdMembership'));
$tabSeparator = strpos($adminUrl, '?') === false ? '?' : '&';
$tabUrl = function ($status) use ($adminUrl, $tabSeparator) {
    return $adminUrl . $tabSeparator . 'status=' . rawurlencode($status);
};
$dashboardUrl = $this->url->href('DashboardController', 'show');
$total = $counts['all'];
?>
<div class="ghd-admin-page">
    <div class="page-header ghd-admin-head">
        <div>
            <p class="ghd-eyebrow"><span></span> Yönetim</p>
            <h2>Üyelik Başvuruları</h2>
            <p class="ghd-admin-subtitle">Başvuruları inceleyin, e-Devlet belgelerini görüntüleyin ve üyelerin panoya erişimini yönetin.</p>
        </div>
        <div class="ghd-admin-head-actions">
            <a class="btn" href="<?= $dashboardUrl ?>"><i class="fa fa-fw fa-tachometer" aria-hidden="true"></i> Panoya dön</a>
        </div>
    </div>

    <nav class="ghd-admin-tabs" aria-label="Başvuru filtreleri">
        <a class="<?= $status === 'pending' ? 'is-active' : '' ?>" href="<?= $tabUrl('pending') ?>">Bekleyen <span><?= (int) $counts['pending'] ?></span></a>
        <a class="<?= $status === 'approved' ? 'is-active' : '' ?>" href="<?= $tabUrl('approved') ?>">Onaylanan <span><?= (int) $counts['approved'] ?></span></a>
        <a class="<?= $status === 'rejected' ? 'is-active' : '' ?>" href="<?= $tabUrl('rejected') ?>">Reddedilen <span><?= (int) $counts['rejected'] ?></span></a>
        <a class="<?= $status === 'all' ? 'is-active' : '' ?>" href="<?= $tabUrl('all') ?>">Tümü <span><?= (int) $total ?></span></a>
    </nav>

    <?php if (empty($applications)): ?>
        <div class="ghd-admin-empty">
            <span class="ghd-admin-empty-icon">◎</span>
            <h3>Bu listede başvuru yok.</h3>
            <p>Yeni bir üyelik başvurusu geldiğinde burada görünecek.</p>
        </div>
    <?php else: ?>
        <div class="ghd-admin-table-wrap">
            <table class="ghd-admin-table">
                <thead>
                    <tr>
                        <th>Başvuru</th>
                        <th>Kişi</th>
                        <th>Hukuk durumu</th>
                        <th>Tarih</th>
                        <th>Durum</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($applications as $application): ?>
                        <?php
                        $showUrl = $this->url->href('MembershipController', 'show', array('plugin' => 'GhdMembership', 'id' => $application['id']));
                        $statusClass = $this->ghdMembership->statusClass($application['status']);
                        ?>
                        <tr>
                            <td><span class="ghd-admin-id">#<?= (int) $application['id'] ?></span></td>
                            <td>
                                <strong><?= $this->text->e($application['full_name']) ?></strong>
                                <small><?= $this->text->e($application['email']) ?></small>
                                <small class="ghd-admin-username">@<?= $this->text->e($application['username']) ?></small>
                            </td>
                            <td><?= $this->text->e($this->ghdMembership->legalStatusLabel($application['legal_status'])) ?></td>
                            <td><?= $this->text->e($this->ghdMembership->formatDate($application['created_at'])) ?></td>
                            <td><span class="ghd-status-badge <?= $statusClass ?>"><?= $this->text->e($this->ghdMembership->statusLabel($application['status'])) ?></span></td>
                            <td class="ghd-admin-row-action">
                                <a class="btn btn-small" href="<?= $showUrl ?>"><?= $application['status'] === 'pending' ? 'İncele' : 'Görüntüle' ?></a>
                            </td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>
    <?php endif ?>
</div>
