<?php
$asset = $this->ghdMembership->assetUrl('plugins/GhdMembership/Assets');
$homeUrl = $this->url->href('MembershipController', 'index', array('plugin' => 'GhdMembership'));
$applyUrl = $this->url->href('MembershipController', 'apply', array('plugin' => 'GhdMembership'));
$statusUrl = $this->url->href('MembershipController', 'status', array('plugin' => 'GhdMembership'));
$loginUrl = $this->url->href('AuthController', 'login');
$dashboardUrl = $this->url->href('DashboardController', 'show');
$adminUrl = $this->url->href('MembershipController', 'admin', array('plugin' => 'GhdMembership'));
$isLogged = $this->user->getId() > 0;
$isAdmin = $isLogged && $this->user->isAdmin();
$pageTitle = isset($page_title) ? $page_title : 'GH Çalıştay';
$pageDescription = isset($page_description) ? $page_description : 'Global Hukukçular Derneği üyelerine özel görev, proje ve etkinlik panosu. Başvurunuzu yapın, üyeliğiniz anında onaylansın.';
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $this->text->e($pageTitle) ?></title>
    <meta name="description" content="<?= $this->text->e($pageDescription) ?>">
    <meta name="theme-color" content="#0b1428">
    <meta property="og:type" content="website">
    <meta property="og:locale" content="tr_TR">
    <meta property="og:site_name" content="GH Çalıştay">
    <meta property="og:title" content="<?= $this->text->e($pageTitle) ?>">
    <meta property="og:description" content="<?= $this->text->e($pageDescription) ?>">
    <meta property="og:image" content="<?= $this->text->e($asset) ?>/og-image.jpg">
    <meta name="twitter:card" content="summary_large_image">
    <link rel="icon" type="image/png" sizes="64x64" href="<?= $this->text->e($asset) ?>/favicon.png">
    <link rel="apple-touch-icon" sizes="180x180" href="<?= $this->text->e($asset) ?>/apple-touch-icon.png">
    <link rel="preload" href="<?= $this->text->e($asset) ?>/fonts/fraunces-latin-ext.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="<?= $this->text->e($asset) ?>/fonts/inter-latin-ext.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="stylesheet" href="<?= $this->text->e($asset) ?>/theme.css">
    <script src="<?= $this->text->e($asset) ?>/theme.js" defer></script>
</head>
<body class="ghd-public">
<a class="ghd-skip-link" href="#ghd-main">İçeriğe geç</a>
<header class="ghd-pub-header">
    <div class="ghd-pub-header-inner">
        <a class="ghd-brand" href="<?= $homeUrl ?>" aria-label="GH Çalıştay ana sayfa">
            <span class="ghd-brand-mark">
                <img src="<?= $this->text->e($asset) ?>/logo.png" alt="" width="48" height="48">
            </span>
            <span class="ghd-brand-text">
                <strong>GH Çalıştay</strong>
                <small>Global Hukukçular Derneği</small>
            </span>
        </a>
        <nav class="ghd-pub-nav" aria-label="Ana menü">
            <a href="<?= $homeUrl ?>#ozellikler">Özellikler</a>
            <a href="<?= $homeUrl ?>#nasil-calisir">Nasıl çalışır?</a>
            <a href="<?= $statusUrl ?>">Başvuru durumu</a>
        </nav>
        <div class="ghd-pub-actions">
            <?php if ($isLogged): ?>
                <?php if ($isAdmin): ?>
                    <a class="ghd-button ghd-button-ghost" href="<?= $adminUrl ?>">Başvurular</a>
                <?php endif ?>
                <a class="ghd-button ghd-button-primary" href="<?= $dashboardUrl ?>">Panoya git</a>
            <?php else: ?>
                <a class="ghd-button ghd-button-ghost" href="<?= $loginUrl ?>">Giriş yap</a>
                <a class="ghd-button ghd-button-primary" href="<?= $applyUrl ?>">Başvuru yap</a>
            <?php endif ?>
        </div>
    </div>
</header>
<main id="ghd-main" class="ghd-pub-main">
