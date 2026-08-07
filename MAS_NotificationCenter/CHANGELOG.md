# MAS_NotificationCenter changelog

## 1.0.5 - 07/08/2026

### Security

- Pinned and bumped `guzzlehttp/guzzle` to ^7.15.2 and `guzzlehttp/psr7` to ^2.12.3 (fixes [GHSA-v5mv-p594-2x33](https://github.com/advisories/GHSA-v5mv-p594-2x33) and related Dependabot alerts pulled in via `minishlink/web-push`).

## 1.0.4 - 06/05/2026

### Added

- **Module UI (Settings):** configurable **admin menu section** (`mas_nc_admin_section`) with the same top-level choices as core admin (Main, Content, Layout, Files, Users & Groups, Extensions, Site Admin, E-Commerce, My Preferences). `GetAdminSection()` reads this preference with validation.
- **Show Donations tab** moved into the new **Module UI** block at the top of Settings (with short help). The Donations tab hide control on the Donations tab now posts `save_settings` with the same hidden field pattern as MAS_OpenStreetMap so **Hide Donations tab** works.

## 1.0.3 - 06/05/2026

### Added

- **CGSimplePWA parity:** Admin `GetHeaderHTML` now prefixes `CGSimplePWA::GetHeaderHTML()` when that module is installed (same pattern as `CGWebPush::GetHeaderHTML`), so service worker regeneration and script combiner run on MAS admin pages. When CGSimplePWA is present, the extra module manifest link and theme-color are skipped by default to avoid duplicate manifests; optional preference forces the MAS manifest in admin headers.
- **CGWebPush bridge:** Optional setting to also call `CGWebPush::broadcast_message()` with a `notification_message` built from the same title and body as MAS deliveries. Dispatcher runs Web Push when either internal push or this bridge is enabled. Internal Minishlink sends are gated by the existing Web Push channel checkbox.
- **Interop:** New `lib/mas_nc_cg_interop.php`, Settings section showing runtime detection of CGSimplePWA and CGWebPush, Help Interop tab text for the CG stack.

## 1.0.2 - 07/05/2026

### Fixed

- Help sub-tabs (Overview, Channels, Interop) no longer reuse `div#page_tabs` markup. CMS admin `cms_initTabs()` binds every `div` under the real module `#page_tabs`, which hid `#help_c` when a sub-tab was clicked. Sub-navigation now uses `<a role="tab">` plus scoped jQuery so Help content stays visible.

## 1.0.1 - 07/05/2026

### Fixed

- Admin banner and manifest icon URLs no longer double the site root when `GetModuleURLPath()` already returns an absolute URL.
- Donations sponsor logo uses fixed dimensions and `object-fit` so large source images do not overflow the layout.

## 1.0.0 - 06/05/2026

### Added

- First release: internal notification log with CMS core event hooks (new user, module install or upgrade or uninstall, optional login failure and user delete).
- Outbound channels: email via `cms_mailer`, Discord Incoming Webhooks, Web Push using `minishlink/web-push` and VAPID keys stored in module preferences.
- System health checks (PHP version floor, free disk percent, DB ping) on cron when enabled.
- Signed cron URL action for heartbeat and health evaluation.
- Operator UI aligned with MAS_OpenStreetMap tabs (Settings, Notifications, Help, About, Changelog, optional Donations).
- Progressive Web App manifest action and optional CGSimplePWA service worker merge support.
- Publishing helper that writes `mas_nc_sw.js` to the CMS document root.
- Interoperability: `HookManager::do_hook('MAS_NotificationCenter::Notify', array($payload))` and documented module event `Notify`.
