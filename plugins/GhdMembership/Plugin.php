<?php

namespace Kanboard\Plugin\GhdMembership;

use Kanboard\Core\Plugin\Base;
use Kanboard\Core\Security\Role;

/**
 * GH Çalıştay membership and branding plugin.
 *
 * Public surface:
 *   /                     landing page
 *   /basvuru              membership application
 *   /basvuru/durum        application status lookup
 *   /kvkk                 privacy notice
 *   /kullanim-kosullari   terms of use
 *
 * Administrators:
 *   /admin/uyelik-basvurulari
 */
class Plugin extends Base
{
    public function initialize()
    {
        // Public pages.
        $this->route->addRoute('/', 'MembershipController', 'index', 'GhdMembership');
        $this->route->addRoute('/anasayfa', 'MembershipController', 'index', 'GhdMembership');
        $this->route->addRoute('/basvuru', 'MembershipController', 'apply', 'GhdMembership');
        $this->route->addRoute('/basvuru/gonder', 'MembershipController', 'save', 'GhdMembership');
        $this->route->addRoute('/basvuru/basarili', 'MembershipController', 'success', 'GhdMembership');
        $this->route->addRoute('/basvuru/durum', 'MembershipController', 'status', 'GhdMembership');
        $this->route->addRoute('/basvuru/durum/kontrol', 'MembershipController', 'checkStatus', 'GhdMembership');
        $this->route->addRoute('/kvkk', 'MembershipController', 'kvkk', 'GhdMembership');
        $this->route->addRoute('/kullanim-kosullari', 'MembershipController', 'terms', 'GhdMembership');

        // Admin pages.
        $this->route->addRoute('/admin/uyelik-basvurulari', 'MembershipController', 'admin', 'GhdMembership');
        $this->route->addRoute('/admin/uyelik-basvurulari/:id', 'MembershipController', 'show', 'GhdMembership');
        $this->route->addRoute('/admin/uyelik-basvurulari/:id/onayla', 'MembershipController', 'approve', 'GhdMembership');
        $this->route->addRoute('/admin/uyelik-basvurulari/:id/reddet', 'MembershipController', 'reject', 'GhdMembership');
        $this->route->addRoute('/admin/uyelik-basvurulari/:id/belge', 'MembershipController', 'document', 'GhdMembership');

        // Public access rules. Core routes are registered before plugins, so these
        // entries extend the existing ACL map.
        $this->applicationAccessMap->add(
            'MembershipController',
            array('index', 'apply', 'save', 'success', 'status', 'checkStatus', 'kvkk', 'terms'),
            Role::APP_PUBLIC
        );

        // Administrative actions are restricted to application administrators.
        $this->applicationAccessMap->add(
            'MembershipController',
            array('admin', 'show', 'approve', 'reject', 'document'),
            Role::APP_ADMIN
        );

        // Brand the existing application chrome.
        $this->template->setTemplateOverride('layout', 'GhdMembership:layout');
        $this->template->setTemplateOverride('auth/index', 'GhdMembership:auth/index');
        $this->template->setTemplateOverride('header/title', 'GhdMembership:header/title');

        // Theme assets and navigation additions.
        $this->hook->on('template:layout:css', array('template' => 'plugins/GhdMembership/Assets/theme.css'));
        $this->hook->on('template:layout:js', array('template' => 'plugins/GhdMembership/Assets/theme.js'));
        $this->helper->hook->attach('template:header:dropdown', 'GhdMembership:header/membership_link');
        $this->helper->hook->attach('template:dashboard:show:before-filter-box', 'GhdMembership:dashboard/pending_banner');

        // Application helper used by templates.
        $this->helper->register('ghdMembership', '\Kanboard\Plugin\GhdMembership\Helper\MembershipHelper');
    }

    public function onStartup()
    {
        \Kanboard\Core\Translator::load(
            $this->languageModel->getCurrentLanguage(),
            __DIR__ . '/Locale'
        );
    }

    public function getClasses()
    {
        return array(
            'Plugin\GhdMembership\Model' => array(
                'MembershipModel',
            ),
        );
    }

    public function getPluginName()
    {
        return 'GH Çalıştay Üyelik';
    }

    public function getPluginDescription()
    {
        return 'Global Hukukçular Derneği üyelik başvurusu, yönetici onayı ve GH Çalıştay markası.';
    }

    public function getPluginAuthor()
    {
        return 'Global Hukukçular Derneği';
    }

    public function getPluginVersion()
    {
        return '1.0.0';
    }

    public function getPluginHomepage()
    {
        return 'https://calistay.globalhukukcular.com';
    }

    public function getCompatibleVersion()
    {
        return '>=1.2.58';
    }
}
