<?php
/**
 * Optional integration with CGSimplePWA and CGWebPush when those modules are installed.
 */
if (!defined('CMS_VERSION')) {
    exit;
}

final class MAS_NC_CGInterop
{
    public static function hasCGSimplePwa(): bool
    {
        $m = cms_utils::get_module('CGSimplePWA');

        return $m instanceof CMSModule;
    }

    public static function hasCGWebPush(): bool
    {
        $m = cms_utils::get_module('CGWebPush');

        return $m instanceof CMSModule;
    }

    /**
     * Admin header prefix: same pattern as CGWebPush::GetHeaderHTML (CGSimplePWA init + SW combiner).
     */
    public static function adminPwaHeaderPrefix(): string
    {
        $m = cms_utils::get_module('CGSimplePWA');
        if (!$m instanceof CMSModule || !method_exists($m, 'GetHeaderHTML')) {
            return '';
        }
        try {
            return (string) $m->GetHeaderHTML();
        } catch (Throwable $e) {
            if (function_exists('audit')) {
                audit(0, 'MAS_NotificationCenter', 'CGSimplePWA GetHeaderHTML failed (details omitted)');
            }

            return '';
        }
    }

    /**
     * Broadcast the same title and body through CGWebPush to all known subscriptions (optional).
     *
     * @return bool true if broadcast_message ran without throwing
     */
    public static function tryCGWebPushBroadcast(CMSModule $mas, string $title, string $body): bool
    {
        if (!is_object($mas) || $mas->GetPreference('mas_nc_cgwebpush_broadcast', '0') !== '1') {
            return false;
        }
        $cg = cms_utils::get_module('CGWebPush');
        if (!$cg instanceof CMSModule || !method_exists($cg, 'broadcast_message')) {
            return false;
        }
        if (!class_exists('notification_message')) {
            return false;
        }
        try {
            $msg = new notification_message();
            $msg->module = 'MAS_NotificationCenter';
            $msg->subject = mb_substr($title, 0, 250);
            $msg->body = $body;
            $cg->broadcast_message($msg);

            return true;
        } catch (Throwable $e) {
            if (function_exists('audit')) {
                audit(0, 'MAS_NotificationCenter', 'CGWebPush bridge broadcast failed (details omitted)');
            }

            return false;
        }
    }
}
