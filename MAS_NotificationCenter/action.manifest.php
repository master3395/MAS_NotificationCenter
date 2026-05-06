<?php
if (!function_exists('cmsms')) {
    exit;
}

$config = cms_config::get_instance();
$rootUrl = rtrim($config->smart_root_url(), '/');
$base = mas_nc_module_public_base($this);
$icon192 = $base . '/images/icon-192.png';
$icon512 = $base . '/images/icon-512.png';

$manifest = array(
    'name' => 'MAS Notification Center',
    'short_name' => 'MAS NC',
    'description' => $this->Lang('moddescription'),
    'start_url' => $rootUrl . '/',
    'scope' => '/',
    'display' => 'standalone',
    'background_color' => '#ffffff',
    'theme_color' => '#0078d4',
    'icons' => array(
        array(
            'src' => $icon192,
            'sizes' => '192x192',
            'type' => 'image/png',
            'purpose' => 'any maskable',
        ),
        array(
            'src' => $icon512,
            'sizes' => '512x512',
            'type' => 'image/png',
            'purpose' => 'any maskable',
        ),
    ),
);

header('Content-Type: application/manifest+json; charset=UTF-8');
echo json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
exit;
