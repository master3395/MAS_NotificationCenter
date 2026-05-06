<?php
if (!function_exists('cmsms')) {
    exit;
}
if (!$this->VisibleToAdminUser()) {
    return $this->DisplayErrorPage($id, $params, $returnid, $this->Lang('accessdenied'));
}

$activeTab = isset($params['active_tab']) ? (string) $params['active_tab'] : 'settings';
$allowedTabs = array('settings', 'notifications', 'help', 'about', 'changelog', 'donations');
if (!in_array($activeTab, $allowedTabs, true)) {
    $activeTab = 'settings';
}

if (!empty($params['hidedonationssubmit'])) {
    $showDonationsTab = !empty($params['show_donations_tab']);
    $this->SetPreference('hidedonationstab', $showDonationsTab ? '' : $this->GetVersion());
    $this->Redirect($id, 'defaultadmin', $returnid, array('msg' => 'saved', 'active_tab' => 'settings'));

    return;
}

if (!empty($params['mas_nc_publish_sw'])) {
    $src = cms_join_path($this->GetModulePath(), 'js', 'mas-nc-sw-root.js');
    $config = cms_config::get_instance();
    $dest = cms_join_path($config['root_path'], 'mas_nc_sw.js');
    if (!is_readable($src)) {
        $this->Redirect($id, 'defaultadmin', $returnid, array('msg' => 'saved', 'active_tab' => 'settings'));

        return;
    }
    $body = (string) @file_get_contents($src);
    $banner = '/* MAS Notification Center service worker, generated ' . gmdate('c') . " */\n";
    @file_put_contents($dest, $banner . $body);
    if (function_exists('audit')) {
        audit(0, 'MAS_NotificationCenter', 'Published mas_nc_sw.js to site root');
    }
    $this->Redirect($id, 'defaultadmin', $returnid, array('msg' => 'published_sw', 'active_tab' => 'settings'));

    return;
}

if (!empty($params['mas_nc_rotate_vapid'])) {
    $auto = cms_join_path($this->GetModulePath(), 'vendor', 'autoload.php');
    if (is_readable($auto)) {
        require_once $auto;
        try {
            if (class_exists('Minishlink\\WebPush\\VAPID')) {
                $keys = Minishlink\WebPush\VAPID::createVapidKeys();
                $this->SetPreference('mas_nc_vapid_public', $keys['publicKey']);
                $this->SetPreference('mas_nc_vapid_private', $keys['privateKey']);
                $db = $this->GetDb();
                $db->Execute('DELETE FROM ' . MAS_NC_Tables::push_subscriptions());
            }
        } catch (Throwable $e) {
            if (function_exists('audit')) {
                audit(0, 'MAS_NotificationCenter', 'VAPID rotate failed');
            }
        }
    }
    $this->Redirect($id, 'defaultadmin', $returnid, array('msg' => 'keys_rotated', 'active_tab' => 'settings'));

    return;
}

if (!empty($params['mas_nc_send_test_push'])) {
    $title = $this->Lang('push_test_title');
    $body = $this->Lang('push_test_body');
    MAS_NC_WebPush::sendToAdmins($this, $title, $body);
    $this->Redirect($id, 'defaultadmin', $returnid, array('msg' => 'saved', 'active_tab' => 'settings'));

    return;
}

if (!empty($params['mas_nc_delete_selected']) && isset($params['mas_nc_sel']) && is_array($params['mas_nc_sel'])) {
    $db = $this->GetDb();
    foreach ($params['mas_nc_sel'] as $rid) {
        $rid = (int) $rid;
        if ($rid > 0) {
            $db->Execute('DELETE FROM ' . MAS_NC_Tables::notifications() . ' WHERE id = ?', array($rid));
        }
    }
    $this->Redirect($id, 'defaultadmin', $returnid, array('msg' => 'saved', 'active_tab' => 'notifications'));

    return;
}

$this->SetPreference('mas_nc_enable_email', !empty($params['mas_nc_enable_email']) ? '1' : '0');
$this->SetPreference('mas_nc_enable_discord', !empty($params['mas_nc_enable_discord']) ? '1' : '0');
$this->SetPreference('mas_nc_enable_push', !empty($params['mas_nc_enable_push']) ? '1' : '0');
$this->SetPreference('mas_nc_cgwebpush_broadcast', !empty($params['mas_nc_cgwebpush_broadcast']) ? '1' : '0');
$this->SetPreference('mas_nc_admin_own_manifest', !empty($params['mas_nc_admin_own_manifest']) ? '1' : '0');
$this->SetPreference('mas_nc_email_recipients', isset($params['mas_nc_email_recipients']) ? trim((string) $params['mas_nc_email_recipients']) : '');
$this->SetPreference('mas_nc_discord_webhook', isset($params['mas_nc_discord_webhook']) ? trim((string) $params['mas_nc_discord_webhook']) : '');
$this->SetPreference('mas_nc_evt_login_failed', !empty($params['mas_nc_evt_login_failed']) ? '1' : '0');
$this->SetPreference('mas_nc_evt_delete_user', !empty($params['mas_nc_evt_delete_user']) ? '1' : '0');
$this->SetPreference('mas_nc_fatal_shutdown', !empty($params['mas_nc_fatal_shutdown']) ? '1' : '0');
$this->SetPreference('mas_nc_health_min_php', isset($params['mas_nc_health_min_php']) ? trim((string) $params['mas_nc_health_min_php']) : '7.4.0');
$this->SetPreference('mas_nc_health_disk_pct', isset($params['mas_nc_health_disk_pct']) ? (string) max(1, (int) $params['mas_nc_health_disk_pct']) : '10');
$this->SetPreference('mas_nc_health_db_ms', isset($params['mas_nc_health_db_ms']) ? (string) max(0, (int) $params['mas_nc_health_db_ms']) : '2000');
$this->SetPreference('mas_nc_vapid_contact', isset($params['mas_nc_vapid_contact']) ? trim((string) $params['mas_nc_vapid_contact']) : 'mailto:info@newstargeted.com');
$this->SetPreference('mas_nc_rate_per_hour', isset($params['mas_nc_rate_per_hour']) ? (string) max(1, (int) $params['mas_nc_rate_per_hour']) : '120');

$allowedSections = array(
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
$sec = isset($params['mas_nc_admin_section']) ? trim((string) $params['mas_nc_admin_section']) : 'extensions';
if (!in_array($sec, $allowedSections, true)) {
    $sec = 'extensions';
}
$this->SetPreference('mas_nc_admin_section', $sec);

$showDonations = !empty($params['show_donations_tab_settings']);
$this->SetPreference('hidedonationstab', $showDonations ? '' : $this->GetVersion());

if (function_exists('audit')) {
    audit(0, 'MAS_NotificationCenter', 'Settings saved');
}

$this->Redirect($id, 'defaultadmin', $returnid, array('msg' => 'saved', 'active_tab' => $activeTab));
