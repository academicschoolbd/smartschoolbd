# Setup & Deployment

This codebase is a CodeIgniter 3 app distributed in the CodeCanyon style — drop the source onto a PHP host, hit `/install`, fill in the wizard, and the app writes back its own `application/config/database.php`. There is no `composer install`, no build step, and no asset pipeline.

## Requirements

| Component | Required | Notes |
|---|---|---|
| PHP | **8.2 or 8.3** | Per strategic docs (production is on cPanel's ea-php82). CI 3.1.13 itself runs on 5.6+ but the SaaS code uses PHP 7.4+ syntax. |
| PHP extensions | `mysqli`, `mbstring`, `gd`, `curl`, `zip`, `xml`, `intl`, `openssl`, `fileinfo` | Standard cPanel set. |
| Database | **MariaDB 10.4+ or MySQL 5.7+** | Driver is `mysqli`. Production runs MariaDB 11.4. |
| Web server | Apache **or** LiteSpeed with `mod_rewrite` | Root `.htaccess` rewrites all non-file URLs into `index.php`. |
| SSL | Required for OAuth/IPN callbacks and most payment gateways | Wildcard cert for `*.smartschool.bd` for multi-tenant. |
| Writable dirs (chmod 0775) | `application/cache/`, `application/logs/`, `uploads/`, `assets/` (where uploads land), `application/config/database.php` (during install only) | The web installer needs to write `database.php` once; revert to 0644 afterwards. |
| Shell access | Recommended (for cron + symlink subdomains) | Not strictly required if you can set cron in cPanel and create subdomains via the cPanel UI. |

## Fresh install (greenfield host)

1. Upload the entire project to your web root (e.g. `~/public_html/` or `~/smartschool.bd/`).
2. Create an empty MySQL/MariaDB database + user with all privileges on that DB.
3. Visit `https://your-host/` — `Install` will run because `application/config/config.php` ships with `$config['installed'] = TRUE` already set — **you'll want to flip that to FALSE before deploying to a brand-new host**, otherwise the installer skips itself. (The committed value of `TRUE` is appropriate only for the existing production deployment that has already been installed.)
4. Step 1 — accept terms.
5. Step 2 — Envato purchase username + code (validated against an external API by `Install::purchase_validation`). The code is then saved to `application/config/purchase_key.php`.
6. Step 3 — DB credentials. The installer connects with `mysqli_connect()` then calls `Install_model::write_database_config()` which rewrites `application/config/database.php`.
7. Step 4 — initial seed: school name, super-admin name/email/password, timezone.
8. Done — the installer flips `$config['installed'] = TRUE` and redirects to `/authentication`.

## Existing deployment (this repo)

`application/config/config.php` already has `installed = TRUE` and DB credentials in `database.php`. Pointing this code at the existing production DB will work out of the box; pointing it at an empty DB will silently fail (every request hits an "installed" guard, then explodes on the first table query).

To run this codebase against a fresh DB, you have three options (in increasing difficulty):

| Option | How |
|---|---|
| **A.** Replay the latest schema dump | Get the latest `zgruhjabaz_smartschoolbd-*.sql` from production (referenced in `docs/RESUME.md`), import it into the empty DB, point `database.php` at it. |
| **B.** Re-run the installer | Flip `$config['installed'] = FALSE`, delete `application/config/database.php` contents, visit `/install`, walk the wizard. |
| **C.** Bootstrap from CodeCanyon | Install the original Ramom School Management System v6.8 zip, then layer this repo's diff on top. (Most painful — not recommended.) |

## Cron jobs

Two cron entries are needed in production. Adjust the PHP binary path to your host:

**Daily SaaS renewal (creates invoices + sends pay links):**
```
0 2 * * * /opt/cpanel/ea-php82/root/usr/bin/php-cli /home/zgruhjabaz/smartschool.bd/index.php saas_renewals_cli run >> /home/zgruhjabaz/logs/saas-renew.log 2>&1
```

**Periodic fees / SMS / email reminders (legacy Ramom):**
```
*/15 * * * * /opt/cpanel/ea-php82/root/usr/bin/php-cli /home/zgruhjabaz/smartschool.bd/index.php cron_api/index >> /home/zgruhjabaz/logs/cron-api.log 2>&1
```

(The `cron_api/index` endpoint also accepts an HTTP call from an external scheduler, secured by `cron_secret_key` from `global_settings`.)

The strategic docs list a future per-minute job worker for the `jobs` table; that script (`application/cli/run_jobs.php` or similar) is **not** in the repo yet.

## Multi-tenant subdomain setup

For every tenant, create:

1. A DNS A/AAAA record for `<slug>.smartschool.bd` pointing at the same server.
2. A wildcard SSL cert that covers `*.smartschool.bd` (Let's Encrypt + DNS-01 works; AutoSSL on cPanel works if you've added the subdomain there).
3. A document-root pointer that resolves the subdomain to the same code base. On Spaceship cPanel today:
   ```
   ln -s /home/zgruhjabaz/smartschool.bd /home/zgruhjabaz/<slug>.smartschool.bd
   ```
4. A `custom_domain` row mapping `<slug>.smartschool.bd → school_id = <branch_id>`.

Steps 1–3 are done by the super-admin manually today (P0/P7 of the roadmap aims to automate them). Step 4 is automated by `Saas::approve()` when a signup is approved.

## Local development

Local dev is genuinely awkward in this codebase because:

- No Docker, no Composer, no asset pipeline.
- The installer requires an external Envato API call to validate the purchase code.
- The whole multi-tenant resolver assumes a real DNS + wildcard cert. A bare `localhost` install can only test the single-branch (apex) path.

A practical "good enough" local setup:

```bash
# 1. PHP 8.2 + mariadb (via Homebrew / apt / scoop)
sudo apt install php8.2 php8.2-mysql php8.2-mbstring php8.2-gd php8.2-curl php8.2-zip mariadb-server

# 2. Clone the repo to your webroot
cd /var/www/html && git clone https://github.com/academicschoolbd/smartschoolbd.git
sudo chown -R www-data:www-data smartschoolbd

# 3. Load the latest production schema dump (no PII) into a local DB
mysql -u root -p -e "CREATE DATABASE smartschool"
mysql -u root -p smartschool < /path/to/zgruhjabaz_smartschoolbd-2026-05-19_schema.sql

# 4. Edit application/config/database.php to point at your local DB
#    (or copy database.php → database_secrets.php and require it)

# 5. Visit http://localhost/smartschoolbd/
```

If you need to test subdomain routing locally, add lines to `/etc/hosts` and use a self-signed wildcard cert with Apache+SNI:

```
127.0.0.1   smartschool.local
127.0.0.1   ngps.smartschool.local
127.0.0.1   devine2e.smartschool.local
```

…and seed your local `custom_domain` table with those hostnames pointing at the matching branch ids.

## Deployment

There is no canonical deploy pipeline checked in. Current production deploy is "FTP / cPanel Git Deploy" per the strategic docs. A future PR could add a GitHub Action that rsyncs the repo to the server (or use cPanel's git-version-control feature with `.cpanel.yml`).

If you set up CI later, the lint/test commands worth running are:

```bash
# PHP syntax check across the codebase
find application -name '*.php' -not -path '*/third_party/*' -print0 \
  | xargs -0 -n1 -P4 php -l > /dev/null

# Optional — phpcs for PSR-12 if you add a phpcs.xml
vendor/bin/phpcs --standard=PSR12 application/controllers application/models application/libraries
```

(`phpcs` is not currently in the repo.)

## Post-install hygiene

Once installed, on any host:

1. `chmod 0644 application/config/database.php` (it was 0664 / 0775 during install).
2. Delete or move `application/config/safe3.zip`, `application/bupviews.zip`, `application/views/landing/bak.zip`, `uploads/db_backup/*.zip`, and the `bak.*.php` files — they shouldn't ship to production. See [`SECURITY.md`](SECURITY.md).
3. Rotate `application/config/database.php` credentials.
4. Rotate `$config['encryption_key']` in `application/config/config.php` (per-host random value).
5. Set `$config['cookie_secure'] = TRUE` and `$config['cookie_httponly'] = TRUE` once you're behind HTTPS.
6. If you're using the `saas` addon, insert the row in `addon` with `prefix='saas'` and create the `custom_domain` table.

---

*Source: `index.php`, `.htaccess`, `application/controllers/Install.php`, `application/models/Install_model.php`, `application/controllers/Saas_renewals_cli.php`, `application/controllers/Cron_api.php`, strategic docs `PROJECT_DRAFT.md` / `RESUME.md`.*
