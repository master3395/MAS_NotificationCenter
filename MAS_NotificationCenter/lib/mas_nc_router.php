<?php
/**
 * Central notification router: persist row + outbound dispatch + rate limit.
 */
if (!defined('CMS_VERSION')) {
    exit;
}

final class MAS_NC_Router
{
    /** Bit flags for delivered_mask */
    public const DELIVER_EMAIL = 1;

    public const DELIVER_DISCORD = 2;

    public const DELIVER_PUSH = 4;

    /**
     * @param array<string,mixed> $payload
     */
    public static function notify(CMSModule $mod, array $payload): int
    {
        $severity = isset($payload['severity']) ? (string) $payload['severity'] : 'info';
        if (!in_array($severity, array('info', 'warning', 'error'), true)) {
            $severity = 'info';
        }
        $category = isset($payload['category']) ? preg_replace('/[^a-z0-9_\-]/i', '', (string) $payload['category']) : 'custom';
        if ($category === '') {
            $category = 'custom';
        }
        $source = isset($payload['source']) ? substr((string) $payload['source'], 0, 128) : 'MAS_NotificationCenter';
        $title = isset($payload['title']) ? substr((string) $payload['title'], 0, 255) : $mod->Lang('notify_default_title');
        $body = isset($payload['body']) ? (string) $payload['body'] : '';
        $meta = isset($payload['meta']) && is_array($payload['meta']) ? $payload['meta'] : array();

        if ($category !== 'cron' && $category !== 'health' && !self::rateAllow($mod, $category)) {
            return 0;
        }

        $db = $mod->GetDb();
        $contextJson = json_encode($meta, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        if ($contextJson === false) {
            $contextJson = '{}';
        }

        $seq = $db->GenID(CMS_DB_PREFIX . 'mod_mas_nc_notifications_seq');
        $sql = 'INSERT INTO ' . MAS_NC_Tables::notifications()
            . ' (id, created, severity, category, source, title, body, context_json, delivered_mask, is_read)'
            . ' VALUES (?,?,?,?,?,?,?,?,?,?)';
        $db->Execute($sql, array(
            $seq,
            time(),
            $severity,
            $category,
            $source,
            $title,
            $body,
            $contextJson,
            0,
            0,
        ));

        $mask = (int) MAS_NC_Dispatcher::deliver($mod, $seq, $title, $body, $severity, $category);
        $db->Execute(
            'UPDATE ' . MAS_NC_Tables::notifications() . ' SET delivered_mask = ? WHERE id = ?',
            array($mask, $seq)
        );

        return (int) $seq;
    }

    private static function rateAllow(CMSModule $mod, string $category): bool
    {
        $max = (int) $mod->GetPreference('mas_nc_rate_per_hour', '120');
        if ($max < 1) {
            $max = 120;
        }
        $window = 3600;
        $key = 'mas_nc_rl_' . md5($category);
        $now = time();
        $raw = $mod->GetPreference($key, '');
        $data = ($raw !== '' && is_string($raw)) ? json_decode($raw, true) : null;
        if (!is_array($data) || !isset($data['t'], $data['c'])) {
            $data = array('t' => $now, 'c' => 0);
        }
        if (($now - (int) $data['t']) > $window) {
            $data = array('t' => $now, 'c' => 0);
        }
        $data['c'] = (int) $data['c'] + 1;
        $mod->SetPreference($key, json_encode($data, JSON_UNESCAPED_UNICODE));
        if ($data['c'] > $max) {
            return false;
        }

        return true;
    }
}
