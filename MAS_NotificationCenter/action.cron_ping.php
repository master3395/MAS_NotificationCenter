<?php
if (!function_exists('cmsms')) {
    exit;
}

header('Content-Type: text/plain; charset=UTF-8');

$token = isset($_REQUEST['mas_token']) ? (string) $_REQUEST['mas_token'] : '';
$expected = (string) $this->GetPreference('mas_nc_cron_token', '');
if ($expected === '' || !hash_equals($expected, $token)) {
    http_response_code(403);
    echo 'Forbidden';

    exit;
}

MAS_NC_Router::notify($this, array(
    'severity' => 'info',
    'category' => 'cron',
    'source' => 'cron_ping',
    'title' => $this->Lang('cron_ping_title'),
    'body' => $this->Lang('cron_ping_body'),
    'meta' => array('time' => gmdate('c')),
));

if ($this->GetPreference('mas_nc_health_on_cron', '1') !== '0') {
    $issues = MAS_NC_Health::run($this);
    foreach ($issues as $k => $msg) {
        MAS_NC_Router::notify($this, array(
            'severity' => 'warning',
            'category' => 'health',
            'source' => 'health:' . $k,
            'title' => $this->Lang('health_issue_title'),
            'body' => (string) $msg,
            'meta' => array('key' => $k),
        ));
    }
}

echo 'OK';
exit;
