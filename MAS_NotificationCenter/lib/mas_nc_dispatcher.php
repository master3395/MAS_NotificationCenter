<?php
/**
 * Email, Discord, Web Push delivery for saved notifications.
 */
if (!defined('CMS_VERSION')) {
    exit;
}

final class MAS_NC_Dispatcher
{
    /**
     * @return int bitmask MAS_NC_Router::DELIVER_*
     */
    public static function deliver(CMSModule $mod, int $id, string $title, string $body, string $severity, string $category): int
    {
        $mask = 0;
        $subject = '[' . $severity . '] ' . $title;

        if ($mod->GetPreference('mas_nc_enable_email', '1') === '1') {
            if (self::sendEmail($mod, $subject, $body)) {
                $mask |= MAS_NC_Router::DELIVER_EMAIL;
            }
        }
        if ($mod->GetPreference('mas_nc_enable_discord', '0') === '1') {
            if (self::sendDiscord($mod, $title, $body, $severity)) {
                $mask |= MAS_NC_Router::DELIVER_DISCORD;
            }
        }
        $wantWebPush = $mod->GetPreference('mas_nc_enable_push', '1') === '1'
            || $mod->GetPreference('mas_nc_cgwebpush_broadcast', '0') === '1';
        if ($wantWebPush) {
            if (MAS_NC_WebPush::sendToAdmins($mod, $title, $body)) {
                $mask |= MAS_NC_Router::DELIVER_PUSH;
            }
        }

        return $mask;
    }

    private static function sendEmail(CMSModule $mod, string $subject, string $body): bool
    {
        $toRaw = trim((string) $mod->GetPreference('mas_nc_email_recipients', ''));
        if ($toRaw === '') {
            return false;
        }
        $parts = preg_split('/[\s,;]+/', $toRaw, -1, PREG_SPLIT_NO_EMPTY);
        if (!is_array($parts) || count($parts) < 1) {
            return false;
        }

        try {
            $mail = new cms_mailer(true);
            $valid = 0;
            foreach ($parts as $addr) {
                $addr = trim((string) $addr);
                if ($addr !== '' && filter_var($addr, FILTER_VALIDATE_EMAIL)) {
                    $mail->AddAddress($addr);
                    $valid++;
                }
            }
            if ($valid < 1) {
                return false;
            }
            $mail->SetSubject($subject);
            $mail->SetBody($body);
            $mail->Send();

            return true;
        } catch (Throwable $e) {
            if (function_exists('audit')) {
                audit(0, 'MAS_NotificationCenter', 'Email delivery failed (details omitted)');
            }

            return false;
        }
    }

    private static function sendDiscord(CMSModule $mod, string $title, string $body, string $severity): bool
    {
        $url = trim((string) $mod->GetPreference('mas_nc_discord_webhook', ''));
        if ($url === '' || !filter_var($url, FILTER_VALIDATE_URL)) {
            return false;
        }
        $host = parse_url($url, PHP_URL_HOST);
        if (!is_string($host)
            || (stripos($host, 'discord') === false && stripos($host, 'discordapp') === false)) {
            return false;
        }

        $color = 3447003;
        if ($severity === 'warning') {
            $color = 16776960;
        }
        if ($severity === 'error') {
            $color = 15158332;
        }

        $payload = array(
            'embeds' => array(
                array(
                    'title' => mb_substr($title, 0, 256),
                    'description' => mb_substr($body, 0, 4090),
                    'color' => $color,
                ),
            ),
        );
        $json = json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        if ($json === false) {
            return false;
        }

        $ch = curl_init($url);
        if ($ch === false) {
            return false;
        }
        curl_setopt_array($ch, array(
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => array('Content-Type: application/json'),
            CURLOPT_POSTFIELDS => $json,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 12,
            CURLOPT_CONNECTTIMEOUT => 8,
        ));
        $res = curl_exec($ch);
        $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return $code >= 200 && $code < 300;
    }
}
