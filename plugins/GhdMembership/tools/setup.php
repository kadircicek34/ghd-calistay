<?php

/**
 * Idempotent GH Çalıştay setup.
 *
 * Usage (inside the application container):
 *
 *   GHD_ADMIN_PASSWORD='...' php plugins/GhdMembership/tools/setup.php --force
 *
 * Environment:
 *   GHD_ADMIN_USER      default: kadir
 *   GHD_ADMIN_PASSWORD  required on first run and with --force
 *   GHD_ADMIN_EMAIL     default: kadircicek13@icloud.com
 *   GHD_ADMIN_NAME      default: Kadir Çiçek
 */

if (PHP_SAPI !== 'cli') {
    fwrite(STDERR, "This script must be run from the command line.\n");
    exit(1);
}

$appRoot = dirname(__DIR__, 3);
require $appRoot . '/app/common.php';

$options = getopt('', array('force'));

$adminUser = getenv('GHD_ADMIN_USER') ?: 'kadir';
$adminPassword = getenv('GHD_ADMIN_PASSWORD') ?: '';
$adminEmail = getenv('GHD_ADMIN_EMAIL') ?: 'kadircicek13@icloud.com';
$adminName = getenv('GHD_ADMIN_NAME') ?: 'Kadir Çiçek';
$force = isset($options['force']) || getenv('GHD_FORCE') === '1';

$config = $container['configModel'];
$config->save(array(
    'application_language' => 'tr_TR',
    'application_timezone' => 'Europe/Istanbul',
    'application_url' => 'https://calistay.globalhukukcular.com/',
    'application_date_format' => 'd.m.Y',
    'application_currency' => 'TRY',
    'board_columns' => 'Yapılacak,Devam Ediyor,İnceleme,Tamamlandı',
    'project_categories' => 'Etkinlik,Proje,İletişim,Eğitim',
    'password_reset' => '0',
    'integration_gravatar' => '0',
    'default_color' => 'blue',
    'ghd_hourly_application_limit' => getenv('GHD_HOURLY_LIMIT') ?: '10',
));

$userModel = $container['userModel'];
$admin = $userModel->getByUsername($adminUser);

if (empty($admin)) {
    $legacyAdmin = $userModel->getByUsername('admin');

    if (! empty($legacyAdmin)) {
        // Rename the bootstrap admin instead of leaving the well-known
        // "admin" account enabled.
        $values = array(
            'id' => $legacyAdmin['id'],
            'username' => $adminUser,
            'name' => $adminName,
            'email' => $adminEmail,
            'role' => \Kanboard\Core\Security\Role::APP_ADMIN,
            'is_active' => 1,
            'language' => 'tr_TR',
            'theme' => 'light',
            'timezone' => 'Europe/Istanbul',
        );

        if ($adminPassword !== '') {
            $values['password'] = $adminPassword;
        }

        $userModel->update($values);
        $admin = $userModel->getById($legacyAdmin['id']);
        echo "Bootstrap admin renamed to '{$adminUser}'.\n";
    } else {
        if ($adminPassword === '') {
            fwrite(STDERR, "GHD_ADMIN_PASSWORD is required to create the administrator account.\n");
            exit(1);
        }

        $adminId = $userModel->create(array(
            'username' => $adminUser,
            'password' => $adminPassword,
            'name' => $adminName,
            'email' => $adminEmail,
            'role' => \Kanboard\Core\Security\Role::APP_ADMIN,
            'is_active' => 1,
            'language' => 'tr_TR',
            'theme' => 'light',
            'timezone' => 'Europe/Istanbul',
            'notifications_enabled' => 0,
            'notifications_filter' => 4,
        ));

        if (! $adminId) {
            fwrite(STDERR, "Administrator account could not be created.\n");
            exit(1);
        }

        $admin = $userModel->getById($adminId);
        echo "Administrator '{$adminUser}' created.\n";
    }
} else {
    $values = array(
        'id' => $admin['id'],
        'name' => $adminName,
        'email' => $adminEmail,
        'role' => \Kanboard\Core\Security\Role::APP_ADMIN,
        'is_active' => 1,
        'language' => 'tr_TR',
        'theme' => 'light',
        'timezone' => 'Europe/Istanbul',
    );

    if ($force && $adminPassword !== '') {
        $values['password'] = $adminPassword;
    }

    $userModel->update($values);
    echo "Administrator '{$adminUser}' already exists; profile updated.\n";
}

$admin = $userModel->getByUsername($adminUser);
$adminId = (int) $admin['id'];

$project = $container['projectModel']->getByName('GH Çalıştay');

if (empty($project)) {
    $projectId = $container['projectModel']->create(array(
        'name' => 'GH Çalıştay',
        'description' => 'Global Hukukçular Derneği görev, proje ve etkinlik panosu.',
        'is_private' => 1,
    ), $adminId, true);

    if (! $projectId) {
        fwrite(STDERR, "Default project could not be created.\n");
        exit(1);
    }

    $project = $container['projectModel']->getById($projectId);
    echo "Default project 'GH Çalıştay' created (#{$projectId}).\n";
} else {
    $projectId = (int) $project['id'];
    $role = $container['projectUserRoleModel']->getUserRole($projectId, $adminId);

    if (empty($role)) {
        $container['projectUserRoleModel']->addUser($projectId, $adminId, \Kanboard\Core\Security\Role::PROJECT_MANAGER);
    }

    if ((int) $project['is_private'] !== 1) {
        $container['projectModel']->update(array('id' => $projectId, 'is_private' => 1));
    }

    echo "Default project 'GH Çalıştay' already exists (#{$projectId}).\n";
}

$config->save(array('ghd_project_id' => (string) $projectId));

// The bootstrap project is created before the plugin language files are
// available in some upgrade paths. Keep the default swimlane Turkish.
$container['db']->table('swimlanes')
    ->eq('project_id', $projectId)
    ->eq('name', 'Default swimlane')
    ->update(array('name' => 'Genel'));

if ((int) $container['taskFinderModel']->countByProjectId($projectId) === 0) {
    $columnId = $container['columnModel']->getFirstColumnId($projectId);
    $swimlaneId = $container['swimlaneModel']->getFirstActiveSwimlaneId($projectId);

    $taskId = $container['taskCreationModel']->create(array(
        'project_id' => $projectId,
        'title' => 'GH Çalıştay panosuna hoş geldiniz',
        'description' => "Bu pano, dernek çalışmalarını, etkinlikleri ve projeleri birlikte yönetmek için hazırlandı.\n\n- Yeni görev eklemek için üst menüdeki **+** düğmesini kullanın.\n- Kartları sürükleyerek Yapılacak, Devam Ediyor, İnceleme ve Tamamlandı sütunları arasında taşıyın.\n- Ekip arkadaşlarınızı proje ayarlarından panoya ekleyin.",
        'column_id' => $columnId,
        'swimlane_id' => $swimlaneId,
        'creator_id' => $adminId,
        'owner_id' => $adminId,
        'color_id' => 'blue',
    ));

    if ($taskId) {
        echo "Welcome task created (#{$taskId}).\n";
    }
}

echo "Setup complete.\n";
echo "Admin username: {$adminUser}\n";
echo "Admin email: {$adminEmail}\n";
echo "Project: GH Çalıştay (#{$projectId})\n";
