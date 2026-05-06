<?php
if (!function_exists('cmsms')) {
    exit;
}

header('Content-Type: application/json; charset=UTF-8');

if (!$this->VisibleToAdminUser()) {
    echo json_encode(array('ok' => false, 'error' => 'denied'), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    exit;
}

$uid = get_userid();
if ($uid < 1) {
    echo json_encode(array('ok' => false, 'error' => 'auth'), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    exit;
}

$raw = (string) file_get_contents('php://input');
$data = json_decode($raw, true);
if (!is_array($data) || empty($data['endpoint'])) {
    echo json_encode(array('ok' => false, 'error' => 'payload'), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    exit;
}

$endpoint = (string) $data['endpoint'];
$id = hash('sha256', $endpoint);

MAS_NC_PushRepo::save($this, $id, $data, (int) $uid);

if (function_exists('audit')) {
    audit((int) $uid, 'MAS_NotificationCenter', 'Push subscription saved');
}

echo json_encode(array('ok' => true), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
exit;
