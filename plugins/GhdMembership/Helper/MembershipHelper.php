<?php

namespace Kanboard\Plugin\GhdMembership\Helper;

use Kanboard\Core\Base;

/**
 * Small presentation helpers shared by the public and admin templates.
 */
class MembershipHelper extends Base
{
    public function countPending()
    {
        return (int) $this->db->table('ghd_membership_applications')
            ->eq('status', 'pending')
            ->count();
    }

    public function statusLabel($status)
    {
        $labels = array(
            'pending' => 'Onay bekliyor',
            'approved' => 'Onaylandı',
            'rejected' => 'Reddedildi',
            'none' => 'Başvuru bulunamadı',
            'invalid' => 'Geçersiz sorgu',
            'throttled' => 'Çok fazla sorgu',
        );

        return isset($labels[$status]) ? $labels[$status] : $status;
    }

    public function statusClass($status)
    {
        $classes = array(
            'pending' => 'is-pending',
            'approved' => 'is-approved',
            'rejected' => 'is-rejected',
        );

        return isset($classes[$status]) ? $classes[$status] : '';
    }

    public function legalStatusLabel($status)
    {
        return $status === 'graduate' ? 'Hukuk mezunu' : 'Hukuk öğrencisi';
    }

    public function documentTypeLabel($type)
    {
        return $type === 'diploma' ? 'Diploma' : 'Öğrenci belgesi';
    }

    public function formatDate($timestamp)
    {
        return $timestamp ? date('d.m.Y H:i', (int) $timestamp) : '—';
    }

    public function formatBirthDate($value)
    {
        if (empty($value)) {
            return '—';
        }

        $date = \DateTime::createFromFormat('Y-m-d', $value);

        return $date ? $date->format('d.m.Y') : $value;
    }

    public function humanFileSize($bytes)
    {
        $bytes = (int) $bytes;

        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 1, ',', '.') . ' MB';
        }

        if ($bytes >= 1024) {
            return number_format($bytes / 1024, 0, ',', '.') . ' KB';
        }

        return $bytes . ' B';
    }

    public function assetUrl($path)
    {
        return $this->helper->url->dir() . ltrim($path, '/');
    }
}
