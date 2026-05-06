<?php
if (!defined('CMS_VERSION')) {
    exit;
}

if (!function_exists('gmp_abs')) {
    return 'GMP extension is required for Web Push VAPID keys. Enable php-gmp and reinstall.';
}

$this->CreatePermission('Manage MAS_NotificationCenter', 'Configure MAS Notification Center, alerts, and transports');

$db = $this->GetDb();
$dict = NewDataDictionary($db);

$taboptarray = array('mysql' => 'TYPE=MyISAM');

$flds = '
    id I KEY NOTNULL,
    created I NOTNULL,
    severity C(16) NOTNULL,
    category C(64) NOTNULL,
    source C(128) NOTNULL,
    title C(255) NOTNULL,
    body X NOTNULL,
    context_json X2,
    delivered_mask I NOTNULL DEFAULT 0,
    is_read I1 NOTNULL DEFAULT 0
';
$sqlarray = $dict->CreateTableSQL(CMS_DB_PREFIX . 'mod_mas_nc_notifications', $flds, $taboptarray);
$dict->ExecuteSQLArray($sqlarray);
$db->CreateSequence(CMS_DB_PREFIX . 'mod_mas_nc_notifications_seq');

$flds2 = '
    id C(64) KEY NOTNULL,
    subscription_json X NOTNULL,
    admin_uid I NOTNULL DEFAULT 0,
    created I NOTNULL,
    last_seen I NOTNULL
';
$sqlarray2 = $dict->CreateTableSQL(CMS_DB_PREFIX . 'mod_mas_nc_push_subscriptions', $flds2, $taboptarray);
$dict->ExecuteSQLArray($sqlarray2);

$this->CreateEvent('Notify');

$this->SetPreference('mas_nc_enable_email', '1');
$this->SetPreference('mas_nc_enable_discord', '0');
$this->SetPreference('mas_nc_enable_push', '1');
$this->SetPreference('mas_nc_email_recipients', '');
$this->SetPreference('mas_nc_discord_webhook', '');
$this->SetPreference('mas_nc_rate_per_hour', '120');
$this->SetPreference('mas_nc_evt_login_failed', '0');
$this->SetPreference('mas_nc_evt_delete_user', '0');
$this->SetPreference('mas_nc_health_min_php', '7.4.0');
$this->SetPreference('mas_nc_health_disk_pct', '10');
$this->SetPreference('mas_nc_health_db_ms', '2000');
$this->SetPreference('mas_nc_fatal_shutdown', '0');
$this->SetPreference('mas_nc_vapid_contact', 'mailto:info@newstargeted.com');
$this->SetPreference('mas_nc_health_on_cron', '1');
$this->SetPreference('hidedonationstab', '');

if (function_exists('random_bytes')) {
    $this->SetPreference('mas_nc_cron_token', bin2hex(random_bytes(24)));
} else {
    $this->SetPreference('mas_nc_cron_token', sha1(uniqid((string) mt_rand(), true)));
}

$auto = cms_join_path($this->GetModulePath(), 'vendor', 'autoload.php');
if (is_readable($auto)) {
    require_once $auto;
    try {
        if (class_exists('Minishlink\\WebPush\\VAPID')) {
            $keys = Minishlink\WebPush\VAPID::createVapidKeys();
            $this->SetPreference('mas_nc_vapid_public', $keys['publicKey']);
            $this->SetPreference('mas_nc_vapid_private', $keys['privateKey']);
        }
    } catch (Throwable $e) {
        if (function_exists('audit')) {
            audit(0, 'MAS_NotificationCenter', 'VAPID key generation skipped during install.');
        }
    }
}

$this->RegisterEvents();
