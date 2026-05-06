<?php
/**
 * Maps Core::* module events to router payloads.
 */
if (!defined('CMS_VERSION')) {
    exit;
}

final class MAS_NC_CoreEvents
{
    /**
     * @param array<string,mixed> $params
     */
    public static function handle(CMSModule $mod, string $originator, string $eventname, array $params): void
    {
        if ($originator !== 'Core') {
            return;
        }

        $payload = null;

        switch ($eventname) {
            case 'AddUserPost':
                if (!empty($params['user']) && is_object($params['user'])) {
                    $u = $params['user'];
                    $uname = isset($u->username) ? (string) $u->username : '?';
                    $payload = array(
                        'severity' => 'info',
                        'category' => 'user',
                        'source' => 'Core::AddUserPost',
                        'title' => $mod->Lang('evt_new_user_title'),
                        'body' => $mod->Lang('evt_new_user_body') . ' ' . $uname,
                        'meta' => array('username' => $uname),
                    );
                }
                break;

            case 'ModuleInstalled':
                $payload = array(
                    'severity' => 'info',
                    'category' => 'module',
                    'source' => 'Core::ModuleInstalled',
                    'title' => $mod->Lang('evt_module_installed_title'),
                    'body' => isset($params['name']) ? (string) $params['name'] : '',
                    'meta' => array(
                        'name' => isset($params['name']) ? (string) $params['name'] : '',
                        'version' => isset($params['version']) ? (string) $params['version'] : '',
                    ),
                );
                break;

            case 'ModuleUpgraded':
                $payload = array(
                    'severity' => 'info',
                    'category' => 'module',
                    'source' => 'Core::ModuleUpgraded',
                    'title' => $mod->Lang('evt_module_upgraded_title'),
                    'body' => (isset($params['name']) ? (string) $params['name'] : '') . ' '
                        . (isset($params['oldversion']) ? (string) $params['oldversion'] : '') . ' → '
                        . (isset($params['newversion']) ? (string) $params['newversion'] : ''),
                    'meta' => array(
                        'name' => isset($params['name']) ? (string) $params['name'] : '',
                        'oldversion' => isset($params['oldversion']) ? (string) $params['oldversion'] : '',
                        'newversion' => isset($params['newversion']) ? (string) $params['newversion'] : '',
                    ),
                );
                break;

            case 'ModuleUninstalled':
                $payload = array(
                    'severity' => 'warning',
                    'category' => 'module',
                    'source' => 'Core::ModuleUninstalled',
                    'title' => $mod->Lang('evt_module_uninstalled_title'),
                    'body' => isset($params['name']) ? (string) $params['name'] : '',
                    'meta' => array('name' => isset($params['name']) ? (string) $params['name'] : ''),
                );
                break;

            case 'LoginFailed':
                if ($mod->GetPreference('mas_nc_evt_login_failed', '0') !== '1') {
                    return;
                }
                $user = isset($params['user']) ? (string) $params['user'] : '?';
                $payload = array(
                    'severity' => 'warning',
                    'category' => 'security',
                    'source' => 'Core::LoginFailed',
                    'title' => $mod->Lang('evt_login_failed_title'),
                    'body' => $mod->Lang('evt_login_failed_body') . ' ' . $user,
                    'meta' => array('user' => $user),
                );
                break;

            case 'DeleteUserPost':
                if ($mod->GetPreference('mas_nc_evt_delete_user', '0') !== '1') {
                    return;
                }
                if (!empty($params['user']) && is_object($params['user'])) {
                    $u = $params['user'];
                    $uname = isset($u->username) ? (string) $u->username : '?';
                    $payload = array(
                        'severity' => 'warning',
                        'category' => 'user',
                        'source' => 'Core::DeleteUserPost',
                        'title' => $mod->Lang('evt_user_deleted_title'),
                        'body' => $uname,
                        'meta' => array('username' => $uname),
                    );
                }
                break;
        }

        if (is_array($payload)) {
            MAS_NC_Router::notify($mod, $payload);
        }
    }
}
