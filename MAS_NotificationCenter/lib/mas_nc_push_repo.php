<?php
/**
 * Push subscription persistence (JSON blobs).
 */
if (!defined('CMS_VERSION')) {
    exit;
}

final class MAS_NC_PushRepo
{
    /**
     * @return array<int,array<string,mixed>>
     */
    public static function loadAll(CMSModule $mod): array
    {
        $db = $mod->GetDb();
        $sql = 'SELECT id, subscription_json, admin_uid, created, last_seen FROM ' . MAS_NC_Tables::push_subscriptions();
        $rows = $db->GetArray($sql);
        if (!is_array($rows)) {
            return array();
        }

        return $rows;
    }

    /**
     * @param array<string,mixed> $subscription decoded PushSubscription JSON (endpoint, keys)
     */
    public static function save(CMSModule $mod, string $id, array $subscription, int $adminUid): void
    {
        $db = $mod->GetDb();
        $json = json_encode($subscription, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        if ($json === false) {
            return;
        }
        $now = time();
        $exists = $db->GetOne('SELECT id FROM ' . MAS_NC_Tables::push_subscriptions() . ' WHERE id = ?', array($id));
        if ($exists) {
            $sql = 'UPDATE ' . MAS_NC_Tables::push_subscriptions()
                . ' SET subscription_json = ?, admin_uid = ?, last_seen = ? WHERE id = ?';
            $db->Execute($sql, array($json, $adminUid, $now, $id));
        } else {
            $sql = 'INSERT INTO ' . MAS_NC_Tables::push_subscriptions()
                . ' (id, subscription_json, admin_uid, created, last_seen) VALUES (?,?,?,?,?)';
            $db->Execute($sql, array($id, $json, $adminUid, $now, $now));
        }
    }

    public static function delete(CMSModule $mod, string $id): void
    {
        $db = $mod->GetDb();
        $db->Execute('DELETE FROM ' . MAS_NC_Tables::push_subscriptions() . ' WHERE id = ?', array($id));
    }

    /**
     * @return array<string,mixed>|null
     */
    public static function subscriptionFromRow(array $row): ?array
    {
        if (!isset($row['subscription_json'])) {
            return null;
        }
        $dec = json_decode((string) $row['subscription_json'], true);

        return is_array($dec) ? $dec : null;
    }
}
