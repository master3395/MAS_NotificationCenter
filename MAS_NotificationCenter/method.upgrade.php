<?php
if (!defined('CMS_VERSION')) {
    exit;
}

$old = isset($oldversion) ? (string) $oldversion : '';
$newVersion = $this->GetVersion();
if ($old !== '' && function_exists('audit')) {
    audit('', 'MAS_NotificationCenter', 'Upgraded from ' . $old . ' to ' . $newVersion);
}
