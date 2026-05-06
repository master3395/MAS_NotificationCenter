<?php
/**
 * MAS_NotificationCenter: internal notifications for CMS Made Simple (MIT).
 *
 * @author master3395
 */
if (!defined('CMS_VERSION')) {
    exit;
}

require_once __DIR__ . '/lib/mas_nc_tables.php';
require_once __DIR__ . '/lib/mas_nc_urls.php';
require_once __DIR__ . '/lib/mas_nc_changelog.php';
require_once __DIR__ . '/lib/mas_nc_push_repo.php';
require_once __DIR__ . '/lib/mas_nc_webpush.php';
require_once __DIR__ . '/lib/mas_nc_dispatcher.php';
require_once __DIR__ . '/lib/mas_nc_router.php';
require_once __DIR__ . '/lib/mas_nc_health.php';
require_once __DIR__ . '/lib/mas_nc_core_events.php';
require_once __DIR__ . '/lib/mas_nc_cg_interop.php';

final class MAS_NotificationCenter extends CMSModule
{
    public const PERM_MANAGE = 'Manage MAS_NotificationCenter';

    public const EVT_NOTIFY = 'Notify';

    /**
     * Programmatic API for other PHP code (same module request).
     *
     * @param array<string,mixed> $payload
     */
    public static function notify(array $payload): int
    {
        $m = cms_utils::get_module('MAS_NotificationCenter');
        if (!$m instanceof self) {
            return 0;
        }

        return MAS_NC_Router::notify($m, $payload);
    }

    public function GetName()
    {
        return 'MAS_NotificationCenter';
    }

    public function GetFriendlyName()
    {
        return $this->Lang('friendlyname');
    }

    public function GetVersion()
    {
        return '1.0.4';
    }

    public function GetAuthor()
    {
        return 'master3395';
    }

    public function GetAuthorEmail()
    {
        return 'info@newstargeted.com';
    }

    public function GetAdminDescription()
    {
        return $this->Lang('moddescription');
    }

    public function GetHelp()
    {
        $smarty = cms_utils::get_smarty();
        if (!$smarty) {
            return '';
        }
        $tplPath = cms_join_path($this->GetModulePath(), 'templates', 'help.tpl');
        if (!is_file($tplPath)) {
            return '';
        }
        $tpl = $smarty->CreateTemplate($this->GetTemplateResource('help.tpl'));
        $tpl->assign('mod', $this);
        $smarty->assign('mod', $this);

        return (string) $tpl->fetch();
    }

    public function GetAbout()
    {
        $smarty = cms_utils::get_smarty();
        if (!$smarty) {
            return '';
        }
        $tplPath = cms_join_path($this->GetModulePath(), 'templates', 'about.tpl');
        if (!is_file($tplPath)) {
            return '';
        }
        $tpl = $smarty->CreateTemplate($this->GetTemplateResource('about.tpl'));
        $tpl->assign('mod', $this);
        $smarty->assign('mod', $this);

        return (string) $tpl->fetch();
    }

    public function GetChangeLog()
    {
        $baseDir = realpath($this->GetModulePath());
        $file = realpath($this->GetModulePath() . DIRECTORY_SEPARATOR . 'CHANGELOG.md');
        if (!$baseDir || !$file || !is_file($file) || !is_readable($file)) {
            return '';
        }
        if (strpos($file, $baseDir) !== 0 || basename($file) !== 'CHANGELOG.md') {
            return '';
        }
        $markdown = @file_get_contents($file);
        if ($markdown === false || $markdown === '') {
            return '';
        }

        return '<div class="mas_nc_changelog_html">' . MAS_NC_Changelog::markdownToHtml((string) $markdown) . '</div>';
    }

    public function HandlesEvents()
    {
        return true;
    }

    public function RegisterEvents()
    {
        $this->AddEventHandler('Core', 'AddUserPost', false);
        $this->AddEventHandler('Core', 'ModuleInstalled', false);
        $this->AddEventHandler('Core', 'ModuleUpgraded', false);
        $this->AddEventHandler('Core', 'ModuleUninstalled', false);
        $this->AddEventHandler('Core', 'LoginFailed', false);
        $this->AddEventHandler('Core', 'DeleteUserPost', false);
    }

    /**
     * @param array<string,mixed> $params
     */
    public function DoEvent($originator, $eventname, &$params)
    {
        if ($originator === 'Core') {
            MAS_NC_CoreEvents::handle($this, $originator, $eventname, $params);
        }
    }

    public function GetEventHelp($eventname)
    {
        $key = 'eventhelp_' . $eventname;

        return $this->Lang($key);
    }

    public function GetEventDescription($eventname)
    {
        $key = 'eventdesc_' . $eventname;

        return $this->Lang($key);
    }

    public function IsPluginModule()
    {
        return true;
    }

    public function HasAdmin()
    {
        return true;
    }

    public function GetAdminSection()
    {
        static $allowed = null;
        if ($allowed === null) {
            $allowed = array(
                'main',
                'content',
                'layout',
                'files',
                'usersgroups',
                'extensions',
                'siteadmin',
                'ecommerce',
                'myprefs',
            );
        }
        $default = 'extensions';
        $v = trim((string) $this->GetPreference('mas_nc_admin_section', $default));
        if (!in_array($v, $allowed, true)) {
            return $default;
        }

        return $v;
    }

    public function VisibleToAdminUser()
    {
        return $this->CheckPermission(self::PERM_MANAGE)
            || $this->CheckPermission('Modify Site Preferences');
    }

    public function GetDependencies()
    {
        return array();
    }

    public function MinimumCMSVersion()
    {
        return '2.2.10';
    }

    public function GetMinimumPHPVersion()
    {
        return '7.4.0';
    }

    public function InstallPostMessage()
    {
        return $this->Lang('postinstall');
    }

    public function UninstallPostMessage()
    {
        return $this->Lang('postuninstall');
    }

    public function UninstallPreMessage()
    {
        return $this->Lang('really_uninstall');
    }

    public function LazyLoadAdmin()
    {
        return false;
    }

    public function LazyLoadFrontend()
    {
        return false;
    }

    public function SetParameters()
    {
        $pwa = cms_utils::get_module('CGSimplePWA');
        if ($pwa && class_exists('CGSimplePWA\ServiceWorkerAddon')) {
            $addonFile = cms_join_path($this->GetModulePath(), 'lib', 'mas_nc_pwa_addon.php');
            if (is_readable($addonFile)) {
                require_once $addonFile;
                if (class_exists('MAS_NC_PwaAddon')) {
                    $pwa->registerServiceWorkerAddon(new MAS_NC_PwaAddon($this));
                }
            }
        }
    }

    public function GetHeaderHTML($action = '')
    {
        $out = '';
        if (class_exists('MAS_NC_CGInterop')) {
            $out .= MAS_NC_CGInterop::adminPwaHeaderPrefix();
        }
        $useSitePwa = class_exists('MAS_NC_CGInterop') && MAS_NC_CGInterop::hasCGSimplePwa();
        if (!$useSitePwa || $this->GetPreference('mas_nc_admin_own_manifest', '0') === '1') {
            try {
                $pageId = (int) CmsApp::get_instance()->GetContentOperations()->GetDefaultPageID();
                $manifestUrl = $this->create_url('cntnt01', 'manifest', $pageId, array('showtemplate' => 'false'));
            } catch (Throwable $e) {
                $manifestUrl = '';
            }
            $icon192 = $this->GetModuleURLPath() . '/images/icon-192.png';
            if ($manifestUrl !== '') {
                $out .= '<link rel="manifest" href="' . cms_htmlentities($manifestUrl) . '" crossorigin="use-credentials">';
            }
            $out .= '<meta name="theme-color" content="#0078d4">';
            $out .= '<link rel="apple-touch-icon" href="' . cms_htmlentities($icon192) . '">';
        } elseif ($useSitePwa) {
            $icon192 = $this->GetModuleURLPath() . '/images/icon-192.png';
            $out .= '<link rel="apple-touch-icon" href="' . cms_htmlentities($icon192) . '">';
        }
        $js = $this->GetModuleURLPath() . '/js/mas-nc-push.js';
        $out .= '<script defer src="' . cms_htmlentities($js) . '"></script>';

        return $out;
    }

    public function SuppressAdminOutput(&$request)
    {
        if (isset($_REQUEST['mact'])) {
            $tmp = explode(',', (string) $_REQUEST['mact']);
            if (count($tmp) > 2 && $tmp[0] === 'MAS_NotificationCenter' && $tmp[2] === 'ajax_push_subscribe') {
                return true;
            }
        }

        return false;
    }

    public function InitializeFrontend()
    {
        $this->RegisterModulePlugin(true, false);
        $this->RestrictUnknownParams();
        $this->SetParameterType('mas_token', CLEAN_STRING);
        $this->SetParameterType('subscription_json', CLEAN_STRING);
        $this->SetParameterType('showtemplate', CLEAN_STRING);

        static $hookDone = false;
        if (!$hookDone) {
            \CMSMS\HookManager::add_hook('MAS_NotificationCenter::Notify', array(__CLASS__, 'hookNotifyIncoming'));
            $hookDone = true;
        }

        $this->maybeRegisterShutdownProbe();
    }

    /**
     * @param mixed $payload
     */
    public static function hookNotifyIncoming($payload): void
    {
        if (!is_array($payload)) {
            return;
        }
        $m = cms_utils::get_module('MAS_NotificationCenter');
        if (!$m instanceof self) {
            return;
        }
        MAS_NC_Router::notify($m, $payload);
    }

    private function maybeRegisterShutdownProbe(): void
    {
        static $registered = false;
        if ($registered) {
            return;
        }
        if ($this->GetPreference('mas_nc_fatal_shutdown', '0') !== '1') {
            return;
        }
        register_shutdown_function(array(__CLASS__, 'shutdownProbe'));
        $registered = true;
    }

    public static function shutdownProbe(): void
    {
        $err = error_get_last();
        if (!is_array($err) || !isset($err['type'])) {
            return;
        }
        $fatalTypes = array(E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR);
        if (!in_array((int) $err['type'], $fatalTypes, true)) {
            return;
        }
        $m = cms_utils::get_module('MAS_NotificationCenter');
        if (!$m instanceof self) {
            return;
        }
        $msg = isset($err['message']) ? (string) $err['message'] : 'fatal';
        $msg = preg_replace('/\s+/', ' ', strip_tags($msg));
        $msg = mb_substr($msg, 0, 400);
        MAS_NC_Router::notify($m, array(
            'severity' => 'error',
            'category' => 'system',
            'source' => 'shutdown',
            'title' => $m->Lang('fatal_notify_title'),
            'body' => $msg,
            'meta' => array(),
        ));
    }

    public function InitializeAdmin()
    {
        $this->CreateParameter('action', 'default', $this->Lang('help_param_action'));
        $this->maybeRegisterShutdownProbe();
    }

    public function ShowDonationsTab()
    {
        return ($this->GetPreference('hidedonationstab', '') !== $this->GetVersion());
    }
}
