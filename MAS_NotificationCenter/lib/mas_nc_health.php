<?php
/**
 * System health probes (disk, PHP version, DB latency).
 */
if (!defined('CMS_VERSION')) {
    exit;
}

final class MAS_NC_Health
{
    /**
     * @return array<string,string|int|float>
     */
    public static function run(CMSModule $mod): array
    {
        $out = array();

        $minPhp = trim((string) $mod->GetPreference('mas_nc_health_min_php', '7.4.0'));
        if ($minPhp === '') {
            $minPhp = '7.4.0';
        }
        $cur = PHP_VERSION;
        if (version_compare($cur, $minPhp, '<')) {
            $out['php'] = 'PHP ' . $cur . ' is below configured minimum ' . $minPhp;
        }

        $diskPct = (int) $mod->GetPreference('mas_nc_health_disk_pct', '10');
        if ($diskPct < 1) {
            $diskPct = 10;
        }
        $config = cms_config::get_instance();
        $root = isset($config['root_path']) ? (string) $config['root_path'] : dirname(dirname(dirname(__DIR__)));
        $free = @disk_free_space($root);
        $total = @disk_total_space($root);
        if ($free !== false && $total !== false && $total > 0) {
            $pctFree = ($free / $total) * 100.0;
            if ($pctFree < (float) $diskPct) {
                $out['disk'] = 'Low disk space on application root: about ' . round($pctFree, 1) . '% free (threshold ' . $diskPct . '%).';
            }
        }

        $maxMs = (int) $mod->GetPreference('mas_nc_health_db_ms', '2000');
        if ($maxMs > 0) {
            $db = $mod->GetDb();
            $t0 = microtime(true);
            $db->Execute('SELECT 1');
            $ms = (microtime(true) - $t0) * 1000.0;
            if ($ms > $maxMs) {
                $out['db'] = 'Database ping slow: about ' . round($ms) . ' ms (threshold ' . $maxMs . ' ms).';
            }
        }

        return $out;
    }
}
