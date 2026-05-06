<?php
if (!defined('CMS_VERSION')) {
    exit;
}

$db = $this->GetDb();
$dict = NewDataDictionary($db);

$sqlarray = $dict->DropTableSQL(CMS_DB_PREFIX . 'mod_mas_nc_notifications');
$dict->ExecuteSQLArray($sqlarray);
$sqlarray = $dict->DropTableSQL(CMS_DB_PREFIX . 'mod_mas_nc_push_subscriptions');
$dict->ExecuteSQLArray($sqlarray);

$db->DropSequence(CMS_DB_PREFIX . 'mod_mas_nc_notifications_seq');

$this->RemovePreference();

$this->RemovePermission('Manage MAS_NotificationCenter');

$this->RemoveEvent('Notify');

$this->RemoveEventHandler('Core', 'AddUserPost');
$this->RemoveEventHandler('Core', 'ModuleInstalled');
$this->RemoveEventHandler('Core', 'ModuleUpgraded');
$this->RemoveEventHandler('Core', 'ModuleUninstalled');
$this->RemoveEventHandler('Core', 'LoginFailed');
$this->RemoveEventHandler('Core', 'DeleteUserPost');
