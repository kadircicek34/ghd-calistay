<?php

namespace Kanboard\Plugin\GhdMembership\Model;

use Kanboard\Core\Base;
use Kanboard\Core\Security\Role;

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

    public function countRecentApproved($days = 7)
    {
        return (int) $this->db->table(self::TABLE)
            ->eq('status', self::STATUS_APPROVED)
            ->gt('created_at', time() - ((int) $days * 86400))
            ->count();
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

    /**
     * Create the Kanboard account and mark the application approved.
     *
     * @return int|false New user id, or false on failure.
     */
    public function approve($id, array $application, $adminId = 0)
    {
        $userExists = $this->db->table('users')
            ->beginOr()
            ->eq('username', $application['username'])
            ->eq('email', $application['email'])
            ->closeOr()
            ->exists();

        if ($userExists) {
            return false;
        }

        $this->db->startTransaction();

        $userId = $this->db->table('users')->persist(array(
            'username' => $application['username'],
            'password' => $application['password_hash'],
            'is_active' => 1,
            'role' => Role::APP_USER,
            'name' => $application['full_name'],
            'email' => $application['email'],
            'language' => 'tr_TR',
            'theme' => 'light',
            'timezone' => 'Europe/Istanbul',
            'notifications_enabled' => 0,
            'notifications_filter' => 4,
            'token' => '',
        ));

        if (! $userId) {
            $this->db->cancelTransaction();
            return false;
        }

        if (! $this->markApproved($id, $userId, (int) $adminId)) {
            $this->db->cancelTransaction();
            return false;
        }

        $projectId = (int) $this->configModel->get('ghd_project_id', 0);

        if ($projectId > 0 && $this->projectModel->exists($projectId)) {
            $this->projectUserRoleModel->addUser($projectId, $userId, Role::PROJECT_MEMBER);
        }

        $this->db->closeTransaction();

        return (int) $userId;
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
