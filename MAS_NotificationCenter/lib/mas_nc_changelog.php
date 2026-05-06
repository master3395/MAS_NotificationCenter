<?php
/**
 * Minimal Markdown to HTML for CHANGELOG.md (MAS_OpenStreetMap-compatible).
 */
if (!defined('CMS_VERSION')) {
    exit;
}

final class MAS_NC_Changelog
{
    public static function markdownToHtml(string $markdown): string
    {
        $lines = preg_split('/\r\n|\r|\n/', $markdown);
        if (!is_array($lines)) {
            return '';
        }
        $out = array();
        $inList = false;
        foreach ($lines as $line) {
            if ($line === '') {
                if ($inList) {
                    $out[] = '</ul>';
                    $inList = false;
                }
                continue;
            }
            if (preg_match('/^###\s+(.+)$/', $line, $m)) {
                if ($inList) {
                    $out[] = '</ul>';
                    $inList = false;
                }
                $out[] = '<h3>' . cms_htmlentities($m[1]) . '</h3>';
                continue;
            }
            if (preg_match('/^##\s+(.+)$/', $line, $m)) {
                if ($inList) {
                    $out[] = '</ul>';
                    $inList = false;
                }
                $out[] = '<h2>' . cms_htmlentities($m[1]) . '</h2>';
                continue;
            }
            if (preg_match('/^#\s+(.+)$/', $line, $m)) {
                if ($inList) {
                    $out[] = '</ul>';
                    $inList = false;
                }
                $out[] = '<h2>' . cms_htmlentities($m[1]) . '</h2>';
                continue;
            }
            if (preg_match('/^[-*]\s+(.+)$/', $line, $m)) {
                if (!$inList) {
                    $out[] = '<ul>';
                    $inList = true;
                }
                $out[] = '<li>' . cms_htmlentities($m[1]) . '</li>';
                continue;
            }
            if ($inList) {
                $out[] = '</ul>';
                $inList = false;
            }
            $out[] = '<p>' . cms_htmlentities($line) . '</p>';
        }
        if ($inList) {
            $out[] = '</ul>';
        }

        return implode("\n", $out);
    }
}
