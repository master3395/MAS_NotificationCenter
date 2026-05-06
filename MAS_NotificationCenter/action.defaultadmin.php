<?php
if (!function_exists('cmsms')) {
    exit;
}
if (!$this->VisibleToAdminUser()) {
    return $this->DisplayErrorPage($id, $params, $returnid, $this->Lang('accessdenied'));
}

$smarty = cmsms()->GetSmarty();
$smarty->assign('mod', $this);
$smarty->assign('mas_nc_version', $this->GetVersion());

$showDonations = $this->ShowDonationsTab();

$activeTab = isset($params['active_tab']) ? (string) $params['active_tab'] : 'settings';
$allowedTabs = array('settings', 'notifications', 'help', 'about', 'changelog', 'donations');
if (!in_array($activeTab, $allowedTabs, true)) {
    $activeTab = 'settings';
}
if ($activeTab === 'donations' && !$showDonations) {
    $activeTab = 'settings';
}

$smarty->assign('show_donations_tab', $showDonations ? '1' : '0');
$smarty->assign('show_donations_tab_checked', $showDonations ? '1' : '0');
$smarty->assign('active_tab', $activeTab);

$smarty->assign('form_start', $this->CreateFormStart($id, 'save_settings', $returnid));
$smarty->assign('form_end', $this->CreateFormEnd());
$smarty->assign('settings_form_start', $this->CreateFormStart($id, 'save_settings', $returnid));
$smarty->assign('settings_form_end', $this->CreateFormEnd());

$adminSectionItems = array(
    lang('main') => 'main',
    lang('content') => 'content',
    lang('layout') => 'layout',
    lang('files') => 'files',
    lang('usersgroups') => 'usersgroups',
    lang('extensions') => 'extensions',
    lang('admin') => 'siteadmin',
    lang('ecommerce') => 'ecommerce',
    lang('myprefs') => 'myprefs',
);
$smarty->assign(
    'mas_nc_admin_section_dropdown',
    $this->CreateInputDropdown($id, 'mas_nc_admin_section', $adminSectionItems, -1, $this->GetAdminSection())
);

$smarty->assign('hidedonationssubmit', $this->CreateInputSubmit($id, 'hidedonationssubmit', $this->Lang('hidedonationssubmit')));
$smarty->assign('donationstext', $this->Lang('donationstext'));
$smarty->assign('sponsorstext', $this->Lang('sponsors'));
$smarty->assign('donations_sponsor_href', 'https://newstargeted.com');
$smarty->assign('donations_sponsor_logo_url', 'https://newstargeted.com/hotlink-ok/logo.png');
$smarty->assign('donations_sponsor_logo_alt', $this->Lang('donations_sponsor_logo_alt'));
$smarty->assign('donations_sponsor_link', $this->Lang('donations_sponsor_link'));

$smarty->assign('mas_nc_changelog_html', $this->GetChangeLog());

$cfg = cms_config::get_instance();
$pageId = (int) cmsms()->GetContentOperations()->GetDefaultPageID();
$manifestUrl = $this->create_url('cntnt01', 'manifest', $pageId, array('showtemplate' => 'false'));
$smarty->assign('mas_nc_manifest_url', $manifestUrl);

$ajaxPushUrl = $this->create_url($id, 'ajax_push_subscribe', $returnid, array('showtemplate' => 'false'));
$smarty->assign('mas_nc_ajax_push_subscribe_url', str_replace('&amp;', '&', $ajaxPushUrl));

$smarty->assign('mas_nc_vapid_public', (string) $this->GetPreference('mas_nc_vapid_public', ''));

$cronToken = (string) $this->GetPreference('mas_nc_cron_token', '');
$cronUrl = $this->create_url('cntnt01', 'cron_ping', $pageId, array('mas_token' => $cronToken, 'showtemplate' => 'false'));
$smarty->assign('mas_nc_cron_url', str_replace('&amp;', '&', $cronUrl));
$smarty->assign('mas_nc_cron_token_display', $cronToken !== '' ? $cronToken : $this->Lang('cron_token_missing'));

$smarty->assign('enable_email_checked', $this->GetPreference('mas_nc_enable_email', '1') === '1' ? '1' : '0');
$smarty->assign('enable_discord_checked', $this->GetPreference('mas_nc_enable_discord', '0') === '1' ? '1' : '0');
$smarty->assign('enable_push_checked', $this->GetPreference('mas_nc_enable_push', '1') === '1' ? '1' : '0');
$smarty->assign('cgwebpush_broadcast_checked', $this->GetPreference('mas_nc_cgwebpush_broadcast', '0') === '1' ? '1' : '0');
$smarty->assign('admin_own_manifest_checked', $this->GetPreference('mas_nc_admin_own_manifest', '0') === '1' ? '1' : '0');
$smarty->assign('has_cg_simple_pwa', class_exists('MAS_NC_CGInterop') && MAS_NC_CGInterop::hasCGSimplePwa() ? '1' : '0');
$smarty->assign('has_cg_webpush', class_exists('MAS_NC_CGInterop') && MAS_NC_CGInterop::hasCGWebPush() ? '1' : '0');
$smarty->assign('email_recipients', (string) $this->GetPreference('mas_nc_email_recipients', ''));
$smarty->assign('discord_webhook', (string) $this->GetPreference('mas_nc_discord_webhook', ''));
$smarty->assign('evt_login_failed_checked', $this->GetPreference('mas_nc_evt_login_failed', '0') === '1' ? '1' : '0');
$smarty->assign('evt_delete_user_checked', $this->GetPreference('mas_nc_evt_delete_user', '0') === '1' ? '1' : '0');
$smarty->assign('fatal_shutdown_checked', $this->GetPreference('mas_nc_fatal_shutdown', '0') === '1' ? '1' : '0');
$smarty->assign('health_min_php', (string) $this->GetPreference('mas_nc_health_min_php', '7.4.0'));
$smarty->assign('health_disk_pct', (string) $this->GetPreference('mas_nc_health_disk_pct', '10'));
$smarty->assign('health_db_ms', (string) $this->GetPreference('mas_nc_health_db_ms', '2000'));
$smarty->assign('vapid_contact', (string) $this->GetPreference('mas_nc_vapid_contact', 'mailto:info@newstargeted.com'));
$smarty->assign('rate_per_hour', (string) $this->GetPreference('mas_nc_rate_per_hour', '120'));

$modAssetsBase = mas_nc_module_public_base($this);
$smarty->assign('mas_nc_module_icon_url', $modAssetsBase . '/images/icon-192.png');
$smarty->assign('mas_nc_banner_url', $modAssetsBase . '/images/banner.png');

$db = $this->GetDb();
$pageNum = isset($params['mas_nc_page']) ? max(1, (int) $params['mas_nc_page']) : 1;
$perPage = 40;
$offset = ($pageNum - 1) * $perPage;
$tab = MAS_NC_Tables::notifications();
$total = (int) $db->GetOne('SELECT COUNT(*) FROM ' . $tab);
$sql = 'SELECT id, created, severity, category, source, title, body, delivered_mask, is_read FROM '
    . $tab . ' ORDER BY id DESC LIMIT ' . (int) $perPage . ' OFFSET ' . (int) $offset;
$rows = $db->GetArray($sql);
if (is_array($rows)) {
    foreach ($rows as &$r0) {
        $r0['created_fmt'] = date('d/m/Y H:i', (int) $r0['created']);
    }
    unset($r0);
}
$smarty->assign('mas_nc_notifications', is_array($rows) ? $rows : array());
$smarty->assign('mas_nc_page', $pageNum);
$smarty->assign('mas_nc_total', $total);
$smarty->assign('mas_nc_per_page', $perPage);
$smarty->assign('mas_nc_has_prev', $pageNum > 1);
$smarty->assign('mas_nc_has_next', ($pageNum * $perPage) < $total);
$smarty->assign('mas_nc_prev_page', max(1, $pageNum - 1));
$smarty->assign('mas_nc_next_page', $pageNum + 1);
$smarty->assign('mas_nc_prev_url', $pageNum > 1 ? $this->create_url($id, 'defaultadmin', $returnid, array('mas_nc_page' => $pageNum - 1, 'active_tab' => 'notifications')) : '');
$smarty->assign('mas_nc_next_url', ($pageNum * $perPage) < $total ? $this->create_url($id, 'defaultadmin', $returnid, array('mas_nc_page' => $pageNum + 1, 'active_tab' => 'notifications')) : '');
$smarty->assign('mas_nc_sw_path', '/mas_nc_sw.js');

$msg = isset($params['msg']) ? (string) $params['msg'] : '';
if ($msg === 'saved') {
    echo $this->ShowMessage($this->Lang('saved'));
}
if ($msg === 'published_sw') {
    echo $this->ShowMessage($this->Lang('published_sw_ok'));
}
if ($msg === 'keys_rotated') {
    echo $this->ShowMessage($this->Lang('keys_rotated'));
}

echo $this->StartTabHeaders();
echo $this->SetTabHeader('settings', $this->Lang('tab_settings'), $activeTab === 'settings');
echo $this->SetTabHeader('notifications', $this->Lang('tab_notifications'), $activeTab === 'notifications');
echo $this->SetTabHeader('help', $this->Lang('tab_help'), $activeTab === 'help');
echo $this->SetTabHeader('about', $this->Lang('tab_about'), $activeTab === 'about');
echo $this->SetTabHeader('changelog', $this->Lang('tab_changelog'), $activeTab === 'changelog');
if ($showDonations) {
    echo $this->SetTabHeader('donations', $this->Lang('donations_tab'), $activeTab === 'donations');
}
echo $this->EndTabHeaders();
echo $this->StartTabContent();

echo $this->StartTab('settings');
echo $this->ProcessTemplate('admin_settings.tpl');
echo $this->EndTab();

echo $this->StartTab('notifications');
echo $this->ProcessTemplate('admin_notifications.tpl');
echo $this->EndTab();

echo $this->StartTab('help');
echo $this->ProcessTemplate('admin_help.tpl');
echo $this->EndTab();

echo $this->StartTab('about');
echo $this->ProcessTemplate('admin_about.tpl');
echo $this->EndTab();

echo $this->StartTab('changelog');
echo $this->ProcessTemplate('admin_changelog.tpl');
echo $this->EndTab();

if ($showDonations) {
    echo $this->StartTab('donations');
    echo $this->ProcessTemplate('donations.tpl');
    echo $this->EndTab();
}

echo $this->EndTabContent();
