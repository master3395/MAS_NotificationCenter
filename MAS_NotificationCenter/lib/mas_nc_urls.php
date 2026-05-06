<?php
/**
 * Resolve module public URL base without doubling smart_root_url when GetModuleURLPath is absolute.
 */
if (!defined('CMS_VERSION')) {
    exit;
}

/**
 * @return string no trailing slash
 */
function mas_nc_module_public_base(CMSModule $mod): string
{
    $path = rtrim((string) $mod->GetModuleURLPath(), '/');
    if ($path !== '' && preg_match('#^https?://#i', $path)) {
        return $path;
    }
    $cfg = cms_config::get_instance();

    return rtrim($cfg->smart_root_url(), '/') . $path;
}
