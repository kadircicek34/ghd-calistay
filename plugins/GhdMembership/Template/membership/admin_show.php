<?php
$application = $application;
$adminUrl = $this->url->href('MembershipController', 'admin', array('plugin' => 'GhdMembership'));
$showUrl = $this->url->href('MembershipController', 'show', array('plugin' => 'GhdMembership', 'id' => $application['id']));
$approveUrl = $this->url->href('MembershipController', 'approve', array('plugin' => 'GhdMembership', 'id' => $application['id']));
$rejectUrl = $this->url->href('MembershipController', 'reject', array('plugin' => 'GhdMembership', 'id' => $application['id']));
$documentUrl = $this->url->href('MembershipController', 'document', array('plugin' => 'GhdMembership', 'id' => $application['id']), true);
$statusClass = $this->ghdMembership->statusClass($application['status']);
$userUrl = ! empty($application['user_id']) ? $this->url->href('UserViewController', 'show', array('user_id' => $application['user_id'])) : '';
$isAutoApproved = empty($application['reviewed_by']);
?>
<div class="ghd-admin-page">
    <div class="ghd-admin-back">
        <a href="<?= $adminUrl ?>"><i class="fa fa-fw fa-arrow-left" aria-hidden="true"></i> Başvuru listesine dön</a>
    </div>

    <div class="page-header ghd-admin-detail-head">
        <div>
            <p class="ghd-eyebrow"><span></span> Başvuru #<?= (int) $application['id'] ?></p>
            <h2><?= $this->text->e($application['full_name']) ?></h2>
            <p class="ghd-admin-subtitle">@<?= $this->text->e($application['username']) ?> · <?= $this->text->e($application['email']) ?></p>
        </div>
        <span class="ghd-status-badge <?= $statusClass ?>"><?= $this->text->e($this->ghdMembership->statusLabel($application['status'])) ?></span>
    </div>

    <div class="ghd-admin-detail-grid">
        <section class="ghd-admin-card">
            <h3>Başvuru bilgileri</h3>
            <dl class="ghd-admin-dl">
                <div><dt>Ad soyad</dt><dd><?= $this->text->e($application['full_name']) ?></dd></div>
                <div><dt>Kullanıcı adı</dt><dd>@<?= $this->text->e($application['username']) ?></dd></div>
                <div><dt>E-posta</dt><dd><a href="mailto:<?= $this->text->e($application['email']) ?>"><?= $this->text->e($application['email']) ?></a></dd></div>
                <div><dt>Telefon</dt><dd><a href="tel:<?= $this->text->e($application['phone']) ?>"><?= $this->text->e($application['phone']) ?></a></dd></div>
                <div><dt>Doğum tarihi</dt><dd><?= $this->text->e($this->ghdMembership->formatBirthDate($application['birth_date'])) ?></dd></div>
                <div><dt>Hukuk durumu</dt><dd><?= $this->text->e($this->ghdMembership->legalStatusLabel($application['legal_status'])) ?></dd></div>
                <div><dt>Okul / fakülte</dt><dd><?= $this->text->e($application['school']) ?></dd></div>
                <div><dt>Mezuniyet yılı</dt><dd><?= $this->text->e($application['graduation_year'] ?: '—') ?></dd></div>
                <div><dt>Başvuru tarihi</dt><dd><?= $this->text->e($this->ghdMembership->formatDate($application['created_at'])) ?></dd></div>
                <div><dt>IP adresi</dt><dd><?= $this->text->e($application['ip_address'] ?: '—') ?></dd></div>
            </dl>
        </section>

        <section class="ghd-admin-card">
            <h3>e-Devlet belgesi</h3>
            <div class="ghd-document-card">
                <span class="ghd-document-icon">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7 3h7l4 4v14H7z" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/><path d="M14 3v5h5M10 12h5M10 16h5" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                </span>
                <div>
                    <strong><?= $this->text->e($application['document_name']) ?></strong>
                    <small><?= $this->text->e($this->ghdMembership->documentTypeLabel($application['document_type'])) ?> · <?= $this->text->e($this->ghdMembership->humanFileSize($application['document_size'])) ?></small>
                </div>
            </div>
            <a class="btn btn-blue" href="<?= $documentUrl ?>"><i class="fa fa-fw fa-download" aria-hidden="true"></i> Belgeyi indir</a>
            <p class="ghd-admin-hint">Belge yalnızca yetkili yöneticiler tarafından görüntülenebilir. İndirme bağlantısı tek kullanımlık güvenlik anahtarı içerir.</p>

            <h3 class="ghd-admin-card-second-title">Başvuru notları</h3>
            <dl class="ghd-admin-dl">
                <div><dt>KVKK onayı</dt><dd><?= ! empty($application['kvkk_consent']) ? 'Onaylandı' : 'Onay yok' ?></dd></div>
                <div><dt>Kullanım koşulları</dt><dd><?= ! empty($application['terms_consent']) ? 'Onaylandı' : 'Onay yok' ?></dd></div>
                <div><dt>Tarayıcı</dt><dd class="ghd-admin-agent"><?= $this->text->e($application['user_agent'] ?: '—') ?></dd></div>
            </dl>
        </section>

        <section class="ghd-admin-card ghd-admin-decision">
            <h3>Karar</h3>
            <?php if ($application['status'] === 'pending'): ?>
                <p>Başvuruyu inceledikten sonra üyelik hesabını oluşturabilir veya gerekçeli olarak reddedebilirsiniz.</p>
                <form method="post" action="<?= $approveUrl ?>" class="ghd-decision-form">
                    <?= $this->form->csrf() ?>
                    <div class="ghd-decision-note">
                        <strong>Onayla</strong>
                        <span>Üye hesabı oluşturulur ve GH Çalıştay panosuna eklenir.</span>
                    </div>
                    <button type="submit" class="btn btn-green" data-confirm="Bu başvuruyu onaylamak ve üye hesabı oluşturmak istiyor musunuz?">Başvuruyu onayla</button>
                </form>
                <hr>
                <form method="post" action="<?= $rejectUrl ?>" class="ghd-decision-form">
                    <?= $this->form->csrf() ?>
                    <div class="ghd-field">
                        <label for="review_note">Red gerekçesi <span>(zorunlu)</span></label>
                        <textarea id="review_note" name="review_note" rows="3" maxlength="2000" placeholder="Başvuru sahibine gösterilecek kısa gerekçe"></textarea>
                    </div>
                    <button type="submit" class="btn btn-red" data-confirm="Bu başvuruyu reddetmek istiyor musunuz?">Başvuruyu reddet</button>
                </form>
            <?php elseif ($application['status'] === 'approved'): ?>
                <div class="ghd-decision-result ghd-decision-approved">
                    <span>✓</span>
                    <div>
                        <strong><?= $isAutoApproved ? 'Otomatik onaylandı' : 'Başvuru onaylandı' ?></strong>
                        <p><?= $this->text->e($this->ghdMembership->formatDate($application['reviewed_at'])) ?> tarihinde <?= $isAutoApproved ? 'otomatik olarak onaylandı ve üye hesabı oluşturuldu' : 'üye hesabı oluşturuldu' ?>.</p>
                        <?php if ($userUrl): ?><a href="<?= $userUrl ?>">Üye profilini aç</a><?php endif ?>
                    </div>
                </div>
                <div class="ghd-admin-revoke">
                    <h4>Üyeliği iptal et</h4>
                    <p>Hesap devre dışı bırakılır ve kullanıcı bir sonraki isteğinde giriş yapamaz. Gerekçe başvuru sahibine gösterilir.</p>
                    <form method="post" action="<?= $rejectUrl ?>" class="ghd-decision-form">
                        <?= $this->form->csrf() ?>
                        <div class="ghd-field">
                            <label for="review_note">İptal gerekçesi <span>(zorunlu)</span></label>
                            <textarea id="review_note" name="review_note" rows="3" maxlength="2000" placeholder="Başvuru sahibine gösterilecek kısa gerekçe"></textarea>
                        </div>
                        <button type="submit" class="btn btn-red" data-confirm="Bu üyeliği iptal etmek istiyor musunuz? Hesap devre dışı bırakılacak.">Üyeliği iptal et</button>
                    </form>
                </div>
            <?php else: ?>
                <div class="ghd-decision-result ghd-decision-rejected">
                    <span>×</span>
                    <div>
                        <strong>Başvuru reddedildi</strong>
                        <p><?= $this->text->e($this->ghdMembership->formatDate($application['reviewed_at'])) ?> tarihinde reddedildi.</p>
                        <?php if (! empty($application['review_note'])): ?>
                            <p class="ghd-decision-reason"><strong>Gerekçe:</strong> <?= $this->text->e($application['review_note']) ?></p>
                        <?php endif ?>
                    </div>
                </div>
            <?php endif ?>
        </section>
    </div>
</div>
