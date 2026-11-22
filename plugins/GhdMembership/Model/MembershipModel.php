<?php

namespace Kanboard\Plugin\GhdMembership\Model;

use Kanboard\Core\Base;

/**
 * Membership application data access.
 */
class MembershipModel extends Base
{
    const TABLE = 'ghd_membership_applications';

    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

    public function getById($id)
    {
        return $this->db->table(self::TABLE)->eq('id', (int) $id)->findOne();
    }

    public function getByEmail($email)
    {
        return $this->db->table(self::TABLE)
            ->eq('email', $email)
            ->desc('created_at')
            ->findOne();
    }

    public function getByUsername($username)
    {
        return $this->db->table(self::TABLE)
            ->eq('username', $username)
            ->desc('created_at')
            ->findOne();
    }

    public function getLatestByIdentifier($identifier)
    {
        return $this->db->table(self::TABLE)
            ->beginOr()
            ->eq('email', $identifier)
            ->eq('username', $identifier)
            ->closeOr()
            ->desc('created_at')
            ->findOne();
    }

    public function getLatestForEmailOrUsername($email, $username)
    {
        return $this->db->table(self::TABLE)
            ->beginOr()
            ->eq('email', $email)
            ->eq('username', $username)
            ->closeOr()
            ->desc('created_at')
            ->findOne();
    }

    public function getAll($status = '')
    {
        $query = $this->db->table(self::TABLE)->desc('created_at');

        if ($status !== '' && $status !== 'all') {
            $query->eq('status', $status);
        }

        return $query->findAll();
    }

    public function countByStatus($status)
    {
        return (int) $this->db->table(self::TABLE)->eq('status', $status)->count();
    }

    public function countAll()
    {
        return (int) $this->db->table(self::TABLE)->count();
    }

    public function countRecentByIp($ip, $seconds = 3600)
    {
        if ($ip === '') {
            return 0;
        }

        return (int) $this->db->table(self::TABLE)
            ->eq('ip_address', $ip)
            ->gt('created_at', time() - (int) $seconds)
            ->count();
    }

    public function create(array $values)
    {
        return $this->db->table(self::TABLE)->persist($values);
    }

    public function markApproved($id, $userId, $adminId)
    {
        return $this->db->table(self::TABLE)->eq('id', (int) $id)->update(array(
            'status' => self::STATUS_APPROVED,
            'user_id' => (int) $userId,
            'reviewed_at' => time(),
            'reviewed_by' => (int) $adminId,
        ));
    }

    public function markRejected($id, $note, $adminId)
    {
        return $this->db->table(self::TABLE)->eq('id', (int) $id)->update(array(
            'status' => self::STATUS_REJECTED,
            'review_note' => $note,
            'reviewed_at' => time(),
            'reviewed_by' => (int) $adminId,
        ));
    }

    public function isActiveStatus($status)
    {
        return in_array($status, array(self::STATUS_PENDING, self::STATUS_APPROVED), true);
    }
}
