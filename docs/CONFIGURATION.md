# Configuration

All configuration lives under `application/config/`. This document lists every config file, calls out the non-default settings, and documents how secrets/credentials are handled.

> ⚠️ Several config files in this repo contain **production secrets in plaintext**. See [`SECURITY.md`](SECURITY.md) for the remediation list.

## File inventory

| File | Purpose | Notes |
|---|---|---|
| `config.php` | Core CI 3 config. | Most settings stock; non-defaults listed below. |
| `database.php` | DB connection. | **Committed plaintext credentials** for the production cPanel DB user `zgruhjabaz_smartschoolbd`. |
| `routes.php` | URL → controller map. | See [`ROUTES.md`](ROUTES.md). |
| `autoload.php` | What CI loads on every request. | Listed below. |
| `migration.php` | CI migration runner config. | Migrations not used; see notes. |
| `hooks.php` | CI hook definitions. | Empty (CI `enable_hooks = FALSE` anyway). |
| `purchase_key.php` | Envato purchase code/username. | Written by the installer; **also committed**. |
| `constants.php` | App-wide PHP constants. | Stock. |
| `doctypes.php`, `mimes.php`, `smileys.php`, `user_agents.php`, `foreign_chars.php`, `profiler.php`, `memcached.php` | Stock CI dictionaries. | Untouched. |
| `safe3.zip` | Snapshot zip of `config.php` + `purchase_key.php`. | **Should not be in version control.** |

## `config.php` — non-default settings

| Setting | Value | Notes |
|---|---|---|
| `base_url` | Built dynamically from `$_SERVER['HTTPS']` + `HTTP_HOST` + `SCRIPT_NAME` | Lets the same code base serve every tenant subdomain without per-host config. |
| `subclass_prefix` | `MY_` | Standard. Drives `MY_Controller` / `MY_Model` discovery. |
| `composer_autoload` | `FALSE` | No Composer at runtime. Third-party libs are pre-vendored. |
| `enable_hooks` | `FALSE` | Confirms `application/hooks/` is unused. |
| `encryption_key` | `34656335323433333164` | **Hard-coded; same value across every deployment shipped from this template.** This needs to be rotated per-environment and removed from the public repo. |
| `sess_driver` | `database` | Sessions live in the `ci_sessions` table — not files. |
| `sess_cookie_name` | `rm_session` | Tenant cookies share this name across subdomains. |
| `sess_expiration` | `7200` (2 hours) | |
| `sess_save_path` | `rm_sessions` | The DB table name. |
| `sess_match_ip` | `FALSE` | |
| `sess_time_to_update` | `300` | |
| `sess_regenerate_destroy` | `FALSE` | |
| `cookie_secure` | `FALSE` | TLS is terminated at the proxy; cookies are not marked Secure. ⚠️ consider flipping to TRUE on production. |
| `cookie_httponly` | `FALSE` | ⚠️ should be TRUE to harden against XSS-driven session theft. |
| `global_xss_filtering` | `TRUE` | |
| `csrf_protection` | `TRUE` | …with carve-outs for `/feespayment/`, `/admissionpayment/`, `/onlineexam_payment/`, `/subscription/`, `/saas_payment/` (added inline in `config.php` because those endpoints receive IPN POSTs from external gateways). |
| `csrf_token_name` | `school_csrf_name` | |
| `csrf_cookie_name` | `school_cookie_name` | |
| `csrf_expire` | `7200` | |
| `csrf_regenerate` | `FALSE` | |
| `installed` | `TRUE` | When `FALSE`, every request redirects to `/install`. |
| `product_name` | `'ramom_school_v6.7'` | Used by the in-app updater (`System_update`). |
| `strict_subdomain_isolation` | `FALSE` | When `TRUE`, `Admin_Controller` refuses requests where the session's branch ≠ the host's branch. Roadmap flips this to TRUE; today it is intentionally FALSE so super-admins can hop between tenants from apex. |

## `autoload.php`

Every request automatically loads:

| Kind | Items |
|---|---|
| libraries | `database`, `session`, `pagination`, `xmlrpc`, `form_validation`, `upload`, `app_lib` |
| helpers | `url`, `file`, `form`, `security`, `directory`, `general` |
| models | `application_model` |
| packages, drivers, config, language | (none) |

`general` is `application/helpers/general_helper.php` — the big helper module with auth, perms, translation, and DB-lookup functions.

`application_model` is the tenant resolver — it must be available before any controller's `__construct()` calls `get_branch_id()`.

## `database.php`

```php
'hostname'  => 'localhost',
'username'  => 'zgruhjabaz_smartschoolbd',
'password'  => 'zgruhjabaz_smartschoolbd',
'database'  => 'zgruhjabaz_smartschoolbd',
'dbdriver'  => 'mysqli',
'dbprefix'  => '',
'pconnect'  => FALSE,
'dbcollat'  => 'utf8_general_ci',
'stricton'  => FALSE,
```

⚠️ This file is committed as-is. The username/password match the production cPanel database user. Recommended:
1. Move these to a per-environment include (e.g. `application/config/db_secrets.php`, gitignored) and `require` it from `database.php`.
2. Rotate the password on the live host.
3. Remove the historic value from git history (`git filter-repo` / equivalent) once rotation is complete.

## Sessions in the DB

Because `sess_driver = database`, you need a `ci_sessions` table on the configured DB:

```sql
CREATE TABLE IF NOT EXISTS ci_sessions (
    id varchar(128) NOT NULL,
    ip_address varchar(45) NOT NULL,
    timestamp int(10) unsigned DEFAULT 0 NOT NULL,
    data blob NOT NULL,
    KEY ci_sessions_timestamp (timestamp),
    PRIMARY KEY (id, ip_address)
);
```

(Standard CI 3 schema; check the live DB has this. The actual table name in this codebase is configurable via `sess_save_path` which is set to `rm_sessions` — verify the live schema matches.)

## Migration runner

`migration.php` configures the CI migration runner. It is **not used in practice** — `application/migrations/` contains only `index.html`. Schema changes ship as raw SQL files and the strategic docs (`PROJECT_DRAFT.md`, `RESUME.md`) reference one-shot SQL migrations.

If you want to start using CI migrations, set `$config['migration_enabled'] = TRUE` in `migration.php`, set `migration_type = 'sequential'` or `timestamp`, and drop migration files into `application/migrations/`.

## Addon registry

A row in the `addon` table activates an addon (matched by `prefix`):

```php
// application/libraries/App_lib.php
function isExistingAddon($prefix = '') {
    $row = $this->CI->db->select('id')->where('prefix', $prefix)->get('addon')->row();
    return !empty($row);
}
```

Prefixes referenced by the active code (`grep`-discovered): `saas`, `qrcode`. Drop-in addon packages live in `uploads/addons/<hash>/`. The `Addons` controller installs them, and the `Modules` controller toggles them on/off.

## Secrets currently in version control

The following files contain secrets / sensitive material at HEAD. Each should be removed from history once a rotation plan is in place — see [`SECURITY.md`](SECURITY.md).

| Path | What it contains |
|---|---|
| `application/config/database.php` | DB hostname/username/password (production cPanel user). |
| `application/config/config.php` | A shared `encryption_key`. |
| `application/config/purchase_key.php` | Envato purchase username + code. |
| `application/config/safe3.zip` | A zipped snapshot of the above two. |
| `application/bupviews.zip` | A snapshot of the `application/views/` tree. |
| `application/views/landing/bak.zip` | A snapshot of older landing variants. |
| `uploads/db_backup/DB-backup_2025-01-11_23-10.zip` | A production DB backup. **High-impact PII risk.** |

## Suggested per-environment overlay

A clean way to handle multi-environment config without `.env`:

```
application/
├── config/
│   ├── config.php              ← committed defaults
│   ├── database.php            ← thin wrapper that requires the secrets file
│   ├── config_local.php        ← gitignored, deployed per-environment
│   └── database_secrets.php    ← gitignored, deployed per-environment
```

```php
// At the bottom of config.php:
if (file_exists(__DIR__ . '/config_local.php')) {
    require_once __DIR__ . '/config_local.php';   // overrides
}
```

Add `application/config/config_local.php` and `application/config/database_secrets.php` to `.gitignore` (the repo currently has no root `.gitignore`).

---

*Source: `application/config/{config,database,routes,autoload,migration,hooks,purchase_key}.php`, `application/libraries/App_lib.php`.*
