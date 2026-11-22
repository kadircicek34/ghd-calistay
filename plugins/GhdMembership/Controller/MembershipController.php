<?php

namespace Kanboard\Plugin\GhdMembership\Controller;

use Kanboard\Controller\BaseController;
use Kanboard\Core\Controller\AccessForbiddenException;
use Kanboard\Core\Controller\PageNotFoundException;
use Kanboard\Core\Security\Role;

/**
 * Public membership application, status lookup and administrator review.
 */
class MembershipController extends BaseController
{
    const MAX_DOCUMENT_SIZE = 10485760; // 10 MiB

    private function requireAdministrator()
    {
        if (! $this->userSession->isAdmin()) {
            throw new AccessForbiddenException();
        }
    }

    private function publicUrl($action, array $params = array())
    {
        $params['plugin'] = 'GhdMembership';

        return $this->helper->url->to('MembershipController', $action, $params);
    }

    public function index()
    {
        $isAdmin = $this->userSession->isAdmin();

        $this->response->html($this->template->render('GhdMembership:membership/landing', array(
            'is_logged' => $this->userSession->isLogged(),
            'is_admin' => $isAdmin,
            'pending_count' => $isAdmin ? $this->membershipModel->countByStatus('pending') : 0,
        )));
    }

    public function apply()
    {
        if ($this->userSession->isLogged()) {
            $this->response->redirect($this->helper->url->to('DashboardController', 'show'));
            return;
        }

        $this->renderApply(array(), array());
    }

    public function save()
    {
        if (! $this->request->isPost()) {
            $this->response->redirect($this->publicUrl('apply'));
            return;
        }

        $this->checkCSRFForm();
        $values = $this->request->getValues();

        // Honeypot: bots fill hidden fields, humans do not.
        if (! empty($values['website'])) {
            $this->response->redirect($this->publicUrl('success'));
            return;
        }

        list($errors, $clean) = $this->validateApplication($values);

        if (! empty($errors)) {
            $values['password'] = '';
            $values['password_confirmation'] = '';
            $this->renderApply($values, $errors);
            return;
        }

        $document = $this->handleDocumentUpload();

        if (isset($document['error'])) {
            $errors['document'] = $document['error'];
            $values['password'] = '';
            $values['password_confirmation'] = '';
            $this->renderApply($values, $errors);
            return;
        }

        $application = array(
            'full_name' => $clean['full_name'],
            'username' => $clean['username'],
            'email' => $clean['email'],
            'phone' => $clean['phone'],
            'birth_date' => $clean['birth_date'],
            'legal_status' => $clean['legal_status'],
            'school' => $clean['school'],
            'graduation_year' => $clean['graduation_year'],
            'password_hash' => password_hash($values['password'], PASSWORD_BCRYPT),
            'document_type' => $clean['document_type'],
            'document_name' => $document['name'],
            'document_path' => $document['path'],
            'document_mime' => $document['mime'],
            'document_size' => $document['size'],
            'kvkk_consent' => 1,
            'terms_consent' => 1,
            'status' => 'pending',
            'review_note' => null,
            'ip_address' => $this->request->getIpAddress(),
            'user_agent' => mb_substr($this->request->getUserAgent(), 0, 255),
            'user_id' => null,
            'created_at' => time(),
            'reviewed_at' => null,
            'reviewed_by' => null,
        );

        $applicationId = $this->membershipModel->create($application);

        if (! $applicationId) {
            $this->removeDocument($document['path']);
            $this->renderApply($values, array(
                'general' => 'Başvurunuz kaydedilemedi. Lütfen tekrar deneyin.',
            ));
            return;
        }

        session_set('ghd_application_username', $clean['username']);
        $this->response->redirect($this->publicUrl('success'));
    }

    public function success()
    {
        $username = session_get('ghd_application_username');
        session_remove('ghd_application_username');

        $this->response->html($this->template->render('GhdMembership:membership/success', array(
            'username' => $username ?: '',
        )));
    }

    public function status()
    {
        $identifier = mb_strtolower(trim($this->request->getStringParam('identifier')));
        $result = null;

        if ($identifier !== '') {
            $application = $this->membershipModel->getLatestByIdentifier($identifier);
            $result = $this->statusPayload($application);
        }

        $this->response->html($this->template->render('GhdMembership:membership/status', array(
            'identifier' => $identifier,
            'result' => $result,
        )));
    }

    public function checkStatus()
    {
        if (! $this->request->isPost()) {
            $this->response->json(array('status' => 'invalid', 'message' => 'Geçersiz istek.'), 405);
            return;
        }

        // The login page calls this endpoint before submitting the core login form.
        // A light session throttle prevents enumeration attempts without adding
        // CAPTCHA friction to the normal flow.
        $checks = session_get('ghd_status_checks');

        if (! is_array($checks)) {
            $checks = array();
        }

        $now = time();
        $checks = array_values(array_filter($checks, function ($timestamp) use ($now) {
            return $timestamp > $now - 60;
        }));

        if (count($checks) >= 20) {
            $this->response->json(array(
                'status' => 'throttled',
                'message' => 'Çok fazla sorgu yaptınız. Lütfen bir dakika sonra tekrar deneyin.',
            ), 429);
            return;
        }

        $checks[] = $now;
        session_set('ghd_status_checks', $checks);

        $identifier = mb_strtolower(trim((string) $this->request->getRawValue('identifier')));

        if ($identifier === '' || mb_strlen($identifier) > 190) {
            $this->response->json(array('status' => 'invalid', 'message' => 'Lütfen e-posta adresinizi veya kullanıcı adınızı girin.'), 400);
            return;
        }

        $application = $this->membershipModel->getLatestByIdentifier($identifier);
        $this->response->json($this->statusPayload($application));
    }

    public function kvkk()
    {
        $this->response->html($this->template->render('GhdMembership:membership/kvkk'));
    }

    public function terms()
    {
        $this->response->html($this->template->render('GhdMembership:membership/terms'));
    }

    public function admin()
    {
        $this->requireAdministrator();

        $status = $this->request->getStringParam('status', 'pending');

        if (! in_array($status, array('pending', 'approved', 'rejected', 'all'), true)) {
            $status = 'pending';
        }

        $this->response->html($this->helper->layout->app('GhdMembership:membership/admin_list', array(
            'title' => 'Üyelik Başvuruları',
            'applications' => $this->membershipModel->getAll($status),
            'status' => $status,
            'counts' => array(
                'pending' => $this->membershipModel->countByStatus('pending'),
                'approved' => $this->membershipModel->countByStatus('approved'),
                'rejected' => $this->membershipModel->countByStatus('rejected'),
                'all' => $this->membershipModel->countAll(),
            ),
        )));
    }

    public function show()
    {
        $this->requireAdministrator();

        $application = $this->membershipModel->getById($this->request->getIntegerParam('id'));

        if (empty($application)) {
            throw new PageNotFoundException();
        }

        $this->response->html($this->helper->layout->app('GhdMembership:membership/admin_show', array(
            'title' => 'Başvuru #' . $application['id'],
            'application' => $application,
        )));
    }

    public function approve()
    {
        $this->requireAdministrator();

        if (! $this->request->isPost()) {
            $this->response->redirect($this->publicUrl('admin'));
            return;
        }

        $this->checkCSRFForm();

        $id = $this->request->getIntegerParam('id');
        $application = $this->membershipModel->getById($id);

        if (empty($application)) {
            throw new PageNotFoundException();
        }

        if ($application['status'] !== 'pending') {
            $this->flash->failure('Bu başvuru daha önce işlenmiş.');
            $this->response->redirect($this->publicUrl('admin'));
            return;
        }

        if ($this->usernameOrEmailExists($application['username'], $application['email'])) {
            $this->flash->failure('Bu kullanıcı adı veya e-posta ile zaten bir hesap var. Başvuruyu reddedip kullanıcıyla iletişime geçin.');
            $this->response->redirect($this->publicUrl('show', array('id' => $id)));
            return;
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
            $this->flash->failure('Üye hesabı oluşturulamadı. Lütfen tekrar deneyin.');
            $this->response->redirect($this->publicUrl('show', array('id' => $id)));
            return;
        }

        if (! $this->membershipModel->markApproved($id, $userId, $this->userSession->getId())) {
            $this->db->cancelTransaction();
            $this->flash->failure('Başvuru durumu güncellenemedi. Lütfen tekrar deneyin.');
            $this->response->redirect($this->publicUrl('show', array('id' => $id)));
            return;
        }

        $projectId = (int) $this->configModel->get('ghd_project_id', 0);

        if ($projectId > 0 && $this->projectModel->exists($projectId)) {
            $this->projectUserRoleModel->addUser($projectId, $userId, Role::PROJECT_MEMBER);
        }

        $this->db->closeTransaction();

        $this->flash->success('Başvuru onaylandı. ' . $application['full_name'] . ' artık GH Çalıştay panosuna giriş yapabilir.');
        $this->response->redirect($this->publicUrl('admin'));
    }

    public function reject()
    {
        $this->requireAdministrator();

        if (! $this->request->isPost()) {
            $this->response->redirect($this->publicUrl('admin'));
            return;
        }

        $this->checkCSRFForm();

        $id = $this->request->getIntegerParam('id');
        $application = $this->membershipModel->getById($id);

        if (empty($application)) {
            throw new PageNotFoundException();
        }

        if ($application['status'] !== 'pending') {
            $this->flash->failure('Bu başvuru daha önce işlenmiş.');
            $this->response->redirect($this->publicUrl('admin'));
            return;
        }

        $note = trim((string) $this->request->getValue('review_note'));

        if (mb_strlen($note) < 3) {
            $this->flash->failure('Red gerekçesi en az 3 karakter olmalıdır.');
            $this->response->redirect($this->publicUrl('show', array('id' => $id)));
            return;
        }

        if (! $this->membershipModel->markRejected($id, mb_substr($note, 0, 2000), $this->userSession->getId())) {
            $this->flash->failure('Başvuru reddedilemedi. Lütfen tekrar deneyin.');
            $this->response->redirect($this->publicUrl('show', array('id' => $id)));
            return;
        }

        $this->flash->success('Başvuru reddedildi ve gerekçe kaydedildi.');
        $this->response->redirect($this->publicUrl('admin'));
    }

    public function document()
    {
        $this->requireAdministrator();
        $this->checkCSRFParam();

        $application = $this->membershipModel->getById($this->request->getIntegerParam('id'));

        if (empty($application)) {
            throw new PageNotFoundException();
        }

        $path = $this->documentPath($application['document_path']);

        if (! is_file($path)) {
            throw new PageNotFoundException();
        }

        $downloadName = preg_replace('/[\r\n"]/', '', $application['document_name']);
        $asciiName = preg_replace('/[^\x20-\x7E]/', '_', $downloadName);

        $this->response
            ->withContentType($application['document_mime'])
            ->withHeader('Content-Disposition', 'attachment; filename="' . $asciiName . '"; filename*=UTF-8\'\'' . rawurlencode($downloadName))
            ->withHeader('X-Content-Type-Options', 'nosniff')
            ->withHeader('Cache-Control', 'no-store, private')
            ->withHeader('Pragma', 'no-cache')
            ->withBody(file_get_contents($path))
            ->send();
    }

    private function renderApply(array $values, array $errors)
    {
        $defaults = array(
            'full_name' => '',
            'username' => '',
            'email' => '',
            'phone' => '',
            'birth_date' => '',
            'legal_status' => 'student',
            'school' => '',
            'graduation_year' => '',
            'document_type' => 'student_certificate',
            'website' => '',
        );

        $this->response->html($this->template->render('GhdMembership:membership/apply', array(
            'values' => array_merge($defaults, $values),
            'errors' => $errors,
        )));
    }

    private function validateApplication(array $values)
    {
        $errors = array();

        $fullName = trim((string) ($values['full_name'] ?? ''));
        $username = strtolower(trim((string) ($values['username'] ?? '')));
        $email = strtolower(trim((string) ($values['email'] ?? '')));
        $phone = trim((string) ($values['phone'] ?? ''));
        $birthDate = trim((string) ($values['birth_date'] ?? ''));
        $legalStatus = trim((string) ($values['legal_status'] ?? ''));
        $school = trim((string) ($values['school'] ?? ''));
        $graduationYear = trim((string) ($values['graduation_year'] ?? ''));
        $documentType = trim((string) ($values['document_type'] ?? ''));
        $password = (string) ($values['password'] ?? '');
        $passwordConfirmation = (string) ($values['password_confirmation'] ?? '');

        if (mb_strlen($fullName) < 3 || mb_strlen($fullName) > 120) {
            $errors['full_name'] = 'Ad soyad 3 ile 120 karakter arasında olmalıdır.';
        }

        if (! preg_match('/^[a-z0-9][a-z0-9._-]{2,29}$/', $username)) {
            $errors['username'] = 'Kullanıcı adı 3-30 karakter olmalı; küçük harf, rakam, nokta, tire ve alt çizgi kullanın.';
        }

        if (! filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($email) > 190) {
            $errors['email'] = 'Geçerli bir e-posta adresi girin.';
        }

        $phoneDigits = preg_replace('/\D+/', '', $phone);

        if (strlen($phoneDigits) < 10 || strlen($phoneDigits) > 13) {
            $errors['phone'] = 'Geçerli bir telefon numarası girin.';
        }

        $date = \DateTime::createFromFormat('Y-m-d', $birthDate);

        if (! $date || $date->format('Y-m-d') !== $birthDate) {
            $errors['birth_date'] = 'Geçerli bir doğum tarihi girin.';
        } elseif ($date > new \DateTime('today')) {
            $errors['birth_date'] = 'Doğum tarihi gelecekte olamaz.';
        } elseif ((int) $date->format('Y') < 1900) {
            $errors['birth_date'] = 'Geçerli bir doğum tarihi girin.';
        }

        if (! in_array($legalStatus, array('student', 'graduate'), true)) {
            $errors['legal_status'] = 'Hukuk öğrencisi veya hukuk mezunu seçeneğini işaretleyin.';
        }

        if (mb_strlen($school) < 3 || mb_strlen($school) > 180) {
            $errors['school'] = 'Okul veya fakülte bilgisini girin.';
        }

        if ($legalStatus === 'graduate') {
            if (! preg_match('/^(19|20)\d{2}$/', $graduationYear)) {
                $errors['graduation_year'] = 'Mezuniyet yılını dört haneli olarak girin.';
            }
        } elseif ($graduationYear !== '' && ! preg_match('/^(19|20)\d{2}$/', $graduationYear)) {
            $errors['graduation_year'] = 'Mezuniyet yılı dört haneli olmalıdır.';
        }

        if (! in_array($documentType, array('student_certificate', 'diploma'), true)) {
            $errors['document_type'] = 'Belge türünü seçin.';
        }

        if (strlen($password) < 8 || strlen($password) > 72) {
            $errors['password'] = 'Şifre 8 ile 72 karakter arasında olmalıdır.';
        } elseif (! preg_match('/[A-Za-zÇĞİÖŞÜçğıöşü]/u', $password) || ! preg_match('/\d/', $password)) {
            $errors['password'] = 'Şifre en az bir harf ve bir rakam içermelidir.';
        }

        if ($password !== $passwordConfirmation) {
            $errors['password_confirmation'] = 'Şifreler birbiriyle eşleşmiyor.';
        }

        if (empty($values['kvkk_consent'])) {
            $errors['kvkk_consent'] = 'KVKK aydınlatma metnini onaylamanız gerekir.';
        }

        if (empty($values['terms_consent'])) {
            $errors['terms_consent'] = 'Kullanım koşullarını onaylamanız gerekir.';
        }

        if (! isset($_FILES['document']) || $_FILES['document']['error'] === UPLOAD_ERR_NO_FILE) {
            $errors['document'] = 'e-Devlet belgenizi yükleyin.';
        }

        $ip = $this->request->getIpAddress();
        $hourlyLimit = (int) $this->configModel->get('ghd_hourly_application_limit', 10);

        if ($hourlyLimit > 0 && $this->membershipModel->countRecentByIp($ip) >= $hourlyLimit) {
            $errors['general'] = 'Çok fazla başvuru denemesi yapıldı. Lütfen bir saat sonra tekrar deneyin.';
        }

        if (! isset($errors['email']) && ! isset($errors['username'])) {
            $existing = $this->membershipModel->getLatestForEmailOrUsername($email, $username);

            if ($existing && $this->membershipModel->isActiveStatus($existing['status'])) {
                if ($existing['email'] === $email) {
                    $errors['email'] = $existing['status'] === 'approved'
                        ? 'Bu e-posta ile onaylanmış bir üyelik var. Lütfen giriş yapın.'
                        : 'Bu e-posta ile bekleyen bir başvurunuz var. Durumunuzu başvuru durum sayfasından kontrol edebilirsiniz.';
                } else {
                    $errors['username'] = $existing['status'] === 'approved'
                        ? 'Bu kullanıcı adı onaylanmış bir üyeliğe ait. Lütfen giriş yapın.'
                        : 'Bu kullanıcı adıyla bekleyen bir başvuru var.';
                }
            }

            if (! isset($errors['email']) && $this->db->table('users')->eq('email', $email)->exists()) {
                $errors['email'] = 'Bu e-posta ile kayıtlı bir hesap var. Lütfen giriş yapın.';
            }

            if (! isset($errors['username']) && $this->db->table('users')->eq('username', $username)->exists()) {
                $errors['username'] = 'Bu kullanıcı adı zaten kullanılıyor.';
            }
        }

        return array($errors, array(
            'full_name' => $fullName,
            'username' => $username,
            'email' => $email,
            'phone' => $phone,
            'birth_date' => $birthDate,
            'legal_status' => $legalStatus,
            'school' => $school,
            'graduation_year' => $graduationYear,
            'document_type' => $documentType,
        ));
    }

    private function handleDocumentUpload()
    {
        if (! isset($_FILES['document']) || ! is_array($_FILES['document'])) {
            return array('error' => 'e-Devlet belgenizi yükleyin.');
        }

        $file = $_FILES['document'];

        if ($file['error'] !== UPLOAD_ERR_OK) {
            $messages = array(
                UPLOAD_ERR_INI_SIZE => 'Belge izin verilen boyuttan büyük.',
                UPLOAD_ERR_FORM_SIZE => 'Belge izin verilen boyuttan büyük.',
                UPLOAD_ERR_PARTIAL => 'Belge yüklenirken bir sorun oluştu.',
                UPLOAD_ERR_NO_FILE => 'e-Devlet belgenizi yükleyin.',
            );

            return array('error' => $messages[$file['error']] ?? 'Belge yüklenemedi.');
        }

        if (! is_uploaded_file($file['tmp_name'])) {
            return array('error' => 'Belge yüklenemedi.');
        }

        if ((int) $file['size'] <= 0 || (int) $file['size'] > self::MAX_DOCUMENT_SIZE) {
            return array('error' => 'Belge en fazla 10 MB olabilir.');
        }

        // The official Kanboard image does not ship the fileinfo extension.
        // Detect the three accepted formats from their magic bytes instead.
        $handle = fopen($file['tmp_name'], 'rb');
        $magic = $handle ? fread($handle, 8) : '';
        if ($handle) {
            fclose($handle);
        }

        if (strncmp($magic, '%PDF-', 5) === 0) {
            $mime = 'application/pdf';
            $extension = 'pdf';
        } elseif (strncmp($magic, "\xFF\xD8\xFF", 3) === 0 && getimagesize($file['tmp_name']) !== false) {
            $mime = 'image/jpeg';
            $extension = 'jpg';
        } elseif (strncmp($magic, "\x89PNG\r\n\x1a\n", 8) === 0 && getimagesize($file['tmp_name']) !== false) {
            $mime = 'image/png';
            $extension = 'png';
        } else {
            return array('error' => 'Yalnızca geçerli bir PDF, JPEG veya PNG belge yükleyebilirsiniz.');
        }

        $directory = rtrim(DATA_DIR, '/') . '/membership_docs';

        if (! is_dir($directory) && ! mkdir($directory, 0700, true) && ! is_dir($directory)) {
            return array('error' => 'Belge dizini oluşturulamadı.');
        }

        @chmod($directory, 0700);

        $storedName = bin2hex(random_bytes(16)) . '.' . $extension;
        $destination = $directory . '/' . $storedName;

        if (! move_uploaded_file($file['tmp_name'], $destination)) {
            return array('error' => 'Belge kaydedilemedi. Lütfen tekrar deneyin.');
        }

        @chmod($destination, 0600);

        $displayName = preg_replace('/[^\p{L}\p{N}._ -]/u', '_', basename($file['name']));
        $displayName = mb_substr($displayName !== '' ? $displayName : 'e-devlet-belgesi.' . $extension, 0, 180);

        return array(
            'path' => $storedName,
            'name' => $displayName,
            'mime' => $mime,
            'size' => (int) $file['size'],
        );
    }

    private function documentPath($storedName)
    {
        $base = realpath(rtrim(DATA_DIR, '/') . '/membership_docs');
        $candidate = $base ? realpath($base . '/' . basename($storedName)) : false;

        if (! $base || ! $candidate || strpos($candidate, $base . DIRECTORY_SEPARATOR) !== 0) {
            return '';
        }

        return $candidate;
    }

    private function removeDocument($storedName)
    {
        $path = $this->documentPath($storedName);

        if ($path !== '' && is_file($path)) {
            @unlink($path);
        }
    }

    private function usernameOrEmailExists($username, $email)
    {
        return $this->db->table('users')->beginOr()
            ->eq('username', $username)
            ->eq('email', $email)
            ->closeOr()
            ->exists();
    }

    private function statusPayload($application)
    {
        if (empty($application)) {
            return array(
                'status' => 'none',
                'message' => 'Bu bilgiyle bir başvuru bulunamadı. Bilgilerinizi kontrol edin veya yeni bir başvuru yapın.',
            );
        }

        if ($application['status'] === 'approved') {
            return array(
                'status' => 'approved',
                'message' => 'Başvurunuz onaylandı. Kullanıcı adınız ve şifrenizle giriş yapabilirsiniz.',
            );
        }

        if ($application['status'] === 'rejected') {
            $message = 'Başvurunuz reddedildi.';

            if (! empty($application['review_note'])) {
                $message .= ' Gerekçe: ' . $application['review_note'];
            }

            return array(
                'status' => 'rejected',
                'message' => $message,
            );
        }

        return array(
            'status' => 'pending',
            'message' => 'Başvurunuz alındı ve yönetici onayı bekliyor. Onaylandığında aynı kullanıcı adı ve şifreyle giriş yapabilirsiniz.',
        );
    }
}
