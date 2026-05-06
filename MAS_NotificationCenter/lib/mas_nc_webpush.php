<?php
/**
 * Web Push via minishlink/web-push (optional Composer vendor).
 */
if (!defined('CMS_VERSION')) {
    exit;
}

final class MAS_NC_WebPush
{
    private static function vendorAutoload(): bool
    {
        static $done = false;
        if ($done) {
            return true;
        }
        $base = dirname(__DIR__) . '/vendor/autoload.php';
        if (is_file($base)) {
            require_once $base;
            $done = true;

            return true;
        }

        return false;
    }

    /**
     * @return array{publicKey:string,privateKey:string}|null
     */
    public static function getVapidKeys(CMSModule $mod): ?array
    {
        $pub = trim((string) $mod->GetPreference('mas_nc_vapid_public', ''));
        $priv = trim((string) $mod->GetPreference('mas_nc_vapid_private', ''));
        if ($pub === '' || $priv === '') {
            return null;
        }

        return array('publicKey' => $pub, 'privateKey' => $priv);
    }

    public static function sendToAdmins(CMSModule $mod, string $title, string $body): bool
    {
        if (!self::vendorAutoload()) {
            return false;
        }
        $keys = self::getVapidKeys($mod);
        if (!$keys) {
            return false;
        }

        $rows = MAS_NC_PushRepo::loadAll($mod);
        if (count($rows) < 1) {
            return false;
        }

        try {
            $auth = array(
                'VAPID' => array(
                    'subject' => self::mailtoSubject($mod),
                    'publicKey' => $keys['publicKey'],
                    'privateKey' => $keys['privateKey'],
                ),
            );
            $payload = json_encode(
                array(
                    'title' => mb_substr($title, 0, 120),
                    'body' => mb_substr($body, 0, 1024),
                    'tag' => 'mas-nc-' . time(),
                ),
                JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
            );
            if ($payload === false) {
                return false;
            }

            $webPush = new Minishlink\WebPush\WebPush($auth, array('TTL' => 300));
            $any = false;
            foreach ($rows as $row) {
                $subArr = MAS_NC_PushRepo::subscriptionFromRow($row);
                if (!$subArr || empty($subArr['endpoint'])) {
                    continue;
                }
                try {
                    $subscription = Minishlink\WebPush\Subscription::create($subArr);
                } catch (Throwable $e) {
                    continue;
                }
                $webPush->sendNotification($subscription, $payload);
                $any = true;
            }
            if (!$any) {
                return false;
            }
            foreach ($webPush->flush() as $report) {
                if ($report->isSuccess()) {
                    continue;
                }
                $resp = method_exists($report, 'getResponse') ? $report->getResponse() : null;
                $code = ($resp && method_exists($resp, 'getStatusCode')) ? (int) $resp->getStatusCode() : 0;
                if ($code === 410 || $code === 404) {
                    $endpoint = (string) $report->getRequest()->getUri();
                    $id = hash('sha256', $endpoint);
                    MAS_NC_PushRepo::delete($mod, $id);
                }
            }

            return true;
        } catch (Throwable $e) {
            if (function_exists('audit')) {
                audit(0, 'MAS_NotificationCenter', 'Web Push batch failed (details omitted)');
            }

            return false;
        }
    }

    private static function mailtoSubject(CMSModule $mod): string
    {
        $adminEmail = (string) $mod->GetPreference('mas_nc_vapid_contact', 'mailto:info@newstargeted.com');
        if (strpos($adminEmail, 'mailto:') !== 0 && filter_var($adminEmail, FILTER_VALIDATE_EMAIL)) {
            return 'mailto:' . $adminEmail;
        }
        if (strpos($adminEmail, 'mailto:') === 0) {
            return $adminEmail;
        }

        return 'mailto:info@newstargeted.com';
    }
}
