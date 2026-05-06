<?php
/**
 * Optional CGSimplePWA service worker merge (when CGSimplePWA is installed).
 */
if (!defined('CMS_VERSION')) {
    exit;
}

if (!class_exists('CGSimplePWA\ServiceWorkerAddon')) {
    return;
}

final class MAS_NC_PwaAddon extends CGSimplePWA\ServiceWorkerAddon
{
    /** @var CMSModule */
    private $mod;

    public function __construct(CMSModule $mod)
    {
        $this->mod = $mod;
    }

    public function getLibraryFiles(): array
    {
        return array(
            cms_join_path($this->mod->GetModulePath(), 'js', 'mas-nc-push.js'),
        );
    }

    public function getModifiedDate(): int
    {
        $f = cms_join_path($this->mod->GetModulePath(), 'js', 'mas-nc-sw-root.js');

        return is_file($f) ? (int) filemtime($f) : time();
    }

    public function getServiceWorkerCode(): string
    {
        $path = cms_join_path($this->mod->GetModulePath(), 'js', 'mas-nc-sw-root.js');
        $txt = is_readable($path) ? (string) file_get_contents($path) : '';

        return $txt;
    }

    public function getStartupCode(): string
    {
        return '';
    }
}
