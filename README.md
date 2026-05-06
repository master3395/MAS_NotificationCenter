# MAS_NotificationCenter

CMS Made Simple module: internal notifications, email, Discord webhooks, Web Push (VAPID), health checks, and cron URL.

## Install

Copy the `MAS_NotificationCenter/` folder into your site `modules/` directory, then install or upgrade from **Extensions → Module Manager**.

## Composer (Web Push)

After copying files, run from inside `MAS_NotificationCenter/`:

```bash
composer install --no-dev
```

PHP **GMP** must be enabled for VAPID key generation on first install.

## License

MIT. See `LICENSE`.
