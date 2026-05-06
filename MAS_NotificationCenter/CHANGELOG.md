# MAS_NotificationCenter changelog

## 1.0.2 — 07/05/2026

### Fixed

- Help sub-tabs (Overview, Channels, Interop) no longer reuse `div#page_tabs` markup. CMS admin `cms_initTabs()` binds every `div` under the real module `#page_tabs`, which hid `#help_c` when a sub-tab was clicked. Sub-navigation now uses `<a role="tab">` plus scoped jQuery so Help content stays visible.

## 1.0.1 — 07/05/2026

### Fixed

- Admin banner and manifest icon URLs no longer double the site root when `GetModuleURLPath()` already returns an absolute URL.
- Donations sponsor logo uses fixed dimensions and `object-fit` so large source images do not overflow the layout.

## 1.0.0 — 06/05/2026

### Added

- First release: internal notification log with CMS core event hooks (new user, module install or upgrade or uninstall, optional login failure and user delete).
- Outbound channels: email via `cms_mailer`, Discord Incoming Webhooks, Web Push using `minishlink/web-push` and VAPID keys stored in module preferences.
- System health checks (PHP version floor, free disk percent, DB ping) on cron when enabled.
- Signed cron URL action for heartbeat and health evaluation.
- Operator UI aligned with MAS_OpenStreetMap tabs (Settings, Notifications, Help, About, Changelog, optional Donations).
- Progressive Web App manifest action and optional CGSimplePWA service worker merge support.
- Publishing helper that writes `mas_nc_sw.js` to the CMS document root.
- Interoperability: `HookManager::do_hook('MAS_NotificationCenter::Notify', array($payload))` and documented module event `Notify`.
