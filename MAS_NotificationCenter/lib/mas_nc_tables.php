<?php
/**
 * Table name helpers for MAS_NotificationCenter.
 */
if (!defined('CMS_VERSION')) {
    exit;
}

final class MAS_NC_Tables
{
    public static function notifications(): string
    {
        return CMS_DB_PREFIX . 'mod_mas_nc_notifications';
    }

    public static function push_subscriptions(): string
    {
        return CMS_DB_PREFIX . 'mod_mas_nc_push_subscriptions';
    }
}
