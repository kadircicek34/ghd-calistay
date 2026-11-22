<?php

namespace Kanboard\Plugin\GhdMembership\Schema;

const VERSION = 1;

function version_1($pdo)
{
    $pdo->exec('
        CREATE TABLE IF NOT EXISTS ghd_membership_applications (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            full_name TEXT NOT NULL,
            username TEXT NOT NULL,
            email TEXT NOT NULL,
            phone TEXT NOT NULL,
            birth_date TEXT NOT NULL,
            legal_status TEXT NOT NULL,
            school TEXT,
            graduation_year TEXT,
            password_hash TEXT NOT NULL,
            document_type TEXT NOT NULL,
            document_name TEXT NOT NULL,
            document_path TEXT NOT NULL,
            document_mime TEXT NOT NULL,
            document_size INTEGER NOT NULL DEFAULT 0,
            kvkk_consent INTEGER NOT NULL DEFAULT 0,
            terms_consent INTEGER NOT NULL DEFAULT 0,
            status TEXT NOT NULL DEFAULT \'pending\',
            review_note TEXT,
            ip_address TEXT,
            user_agent TEXT,
            user_id INTEGER,
            created_at INTEGER NOT NULL,
            reviewed_at INTEGER,
            reviewed_by INTEGER
        )
    ');

    $pdo->exec('CREATE INDEX IF NOT EXISTS ghd_membership_email_idx ON ghd_membership_applications(email)');
    $pdo->exec('CREATE INDEX IF NOT EXISTS ghd_membership_username_idx ON ghd_membership_applications(username)');
    $pdo->exec('CREATE INDEX IF NOT EXISTS ghd_membership_status_idx ON ghd_membership_applications(status)');
    $pdo->exec('CREATE INDEX IF NOT EXISTS ghd_membership_created_idx ON ghd_membership_applications(created_at)');
    $pdo->exec('CREATE INDEX IF NOT EXISTS ghd_membership_ip_idx ON ghd_membership_applications(ip_address)');
}
