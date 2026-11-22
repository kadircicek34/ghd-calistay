<?php
$asset = $this->ghdMembership->assetUrl('plugins/GhdMembership/Assets');
$titleText = '';

if (isset($page_title)) {
    $titleText = $page_title;
} elseif (isset($title)) {
    $titleText = $title;
}

$documentTitle = $titleText !== '' && $titleText !== 'GH Çalıştay'
    ? $titleText . ' · GH Çalıştay'
    : 'GH Çalıştay — Global Hukukçular Derneği';
?>
<!DOCTYPE html>
<html lang="<?= $this->app->jsLang() ?>"<?php if ($this->app->isRtlLanguage()): ?> dir="rtl"<?php endif; ?>>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="robots" content="noindex,nofollow">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="referrer" content="no-referrer">
        <meta name="theme-color" content="#0b1428">
        <meta name="description" content="GH Çalıştay — Global Hukukçular Derneği üyelerine özel görev ve proje panosu.">

        <?php if (isset($board_public_refresh_interval)): ?>
            <meta http-equiv="refresh" content="<?= $board_public_refresh_interval ?>">
        <?php endif ?>

        <?= $this->asset->colorCss() ?>
        <?= $this->asset->css('assets/css/vendor.min.css') ?>
        <?php if (! isset($not_editable)): ?>
            <?= $this->asset->css('assets/css/'.$this->user->getTheme().'.min.css') ?>
        <?php else: ?>
            <?= $this->asset->css('assets/css/light.min.css') ?>
        <?php endif ?>
        <?= $this->asset->css('assets/css/print.min.css', true, 'print') ?>
        <?= $this->asset->customCss() ?>

        <?php if (! isset($not_editable)): ?>
            <?= $this->asset->js('assets/js/vendor.min.js') ?>
            <?= $this->asset->js('assets/js/app.min.js') ?>
        <?php endif ?>

        <?= $this->hook->asset('css', 'template:layout:css') ?>
        <?= $this->hook->asset('js', 'template:layout:js') ?>

        <link rel="icon" type="image/png" sizes="64x64" href="<?= $this->text->e($asset) ?>/favicon.png">
        <link rel="apple-touch-icon" sizes="180x180" href="<?= $this->text->e($asset) ?>/apple-touch-icon.png">
        <link rel="preload" href="<?= $this->text->e($asset) ?>/fonts/fraunces-latin-ext.woff2" as="font" type="font/woff2" crossorigin>
        <link rel="preload" href="<?= $this->text->e($asset) ?>/fonts/inter-latin-ext.woff2" as="font" type="font/woff2" crossorigin>

        <title><?= $this->text->e($documentTitle) ?></title>

        <?= $this->hook->render('template:layout:head') ?>
    </head>
    <body class="ghd-app" data-status-url="<?= $this->url->href('UserAjaxController', 'status') ?>"
          data-login-url="<?= $this->url->href('AuthController', 'login') ?>"
          data-keyboard-shortcut-url="<?= $this->url->href('DocumentationController', 'shortcuts') ?>"
          data-timezone="<?= $this->app->getTimezone() ?>"
          data-js-date-format="<?= $this->app->getJsDateFormat() ?>"
          data-js-time-format="<?= $this->app->getJsTimeFormat() ?>">

    <?php if (isset($no_layout) && $no_layout): ?>
        <?= $this->app->flashMessage() ?>
        <?= $content_for_layout ?>
    <?php else: ?>
        <?= $this->hook->render('template:layout:top') ?>
        <?= $this->render('header', array(
            'title' => $title,
            'description' => isset($description) ? $description : '',
            'board_selector' => isset($board_selector) ? $board_selector : array(),
            'project' => isset($project) ? $project : array(),
        )) ?>
        <section class="page">
            <?= $this->app->flashMessage() ?>
            <?= $content_for_layout ?>
        </section>
        <?= $this->hook->render('template:layout:bottom') ?>
        <footer class="ghd-app-footer">
            <span>GH Çalıştay · Global Hukukçular Derneği</span>
            <span>
                <a href="<?= $this->url->href('MembershipController', 'kvkk', array('plugin' => 'GhdMembership')) ?>">KVKK</a>
                <a href="<?= $this->url->href('MembershipController', 'terms', array('plugin' => 'GhdMembership')) ?>">Kullanım koşulları</a>
                <a href="https://globalhukukcular.com" target="_blank" rel="noopener noreferrer">globalhukukcular.com</a>
            </span>
        </footer>
    <?php endif; ?>
    </body>
</html>
