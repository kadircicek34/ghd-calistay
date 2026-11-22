<?php

/**
 * Approve all remaining pending applications (one-off migration helper).
 *
 * Usage:
 *   php plugins/GhdMembership/tools/approve-pending.php [--dry-run]
 */

if (PHP_SAPI !== 'cli') {
    fwrite(STDERR, "This script must be run from the command line.\n");
    exit(1);
}

require dirname(__DIR__, 3) . '/app/common.php';

$options = getopt('', array('dry-run'));
$dryRun = isset($options['dry-run']);

$applications = $container['membershipModel']->getAll('pending');

if (empty($applications)) {
    echo "No pending applications.\n";
    exit(0);
}

$approved = 0;
$failed = 0;

foreach ($applications as $application) {
    if ($dryRun) {
        echo "Would approve #{$application['id']}\n";
        $approved++;
        continue;
    }

    $userId = $container['membershipModel']->approve($application['id'], $application, 0);

    if ($userId) {
        $approved++;
    } else {
        $failed++;
        fwrite(STDERR, "Could not approve #{$application['id']}\n");
    }
}

echo "approved={$approved} failed={$failed}\n";
