# Architecture

## Tech stack

| Layer | Tech | Source of truth |
|---|---|---|
| Framework | **CodeIgniter 3.1.13** | `system/core/CodeIgniter.php` (`CI_VERSION = '3.1.13'`) |
| Language | PHP 8.2 / 8.3 (target) | `docs/PROJECT_DRAFT.md` |
| Database driver | `mysqli` | `application/config/database.php` (`'dbdriver' => 'mysqli'`) |
| Session | CI native session (file-based by default) | `application/config/autoload.php` autoloads `session` |
| Templating | Plain PHP partials in `application/views/` | — |
| Routing | CI regex routes | `application/config/routes.php` |
| Front controller | `index.php` → CI bootstrap | `index.php` |
| URL rewrite | Apache/LiteSpeed mod_rewrite | root `.htaccess` |

## Front controller & URL rewriting

The root `.htaccess` rewrites any URL that does not map to a real file/directory into `index.php?/<path>`:

```
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php?/$1 [L]
```

`index.php` is the stock CI 3 bootstrap (BCIT/EllisLab MIT). It sets `BASEPATH = system/`, `APPPATH = application/`, and `VIEWPATH = application/views/`, then includes `system/core/CodeIgniter.php` to start the request lifecycle.

## Request lifecycle

```
HTTP request
   │
   ▼
.htaccess rewrite  ──►  index.php
   │
   ▼
system/core/CodeIgniter.php
   │
   ├─► Loads application/config/config.php
   ├─► Loads application/config/routes.php
   │      └── Computes $route['default_controller']:
   │             apex host (`smartschool.bd` / `www.smartschool.bd`)  →  'landing'
   │             every other host                                     →  'home'
   ├─► Loads application/config/autoload.php
   │      └── libraries: database, session, pagination, xmlrpc,
   │                     form_validation, upload, app_lib
   │      └── helpers:   url, file, form, security, directory, general
   │      └── models:    application_model  ← URL-host based branch resolver
   │
   ▼
Router resolves URL → controller/method
   │
   ▼
Controller __construct() runs:
   • parent (MY_Controller/Admin_Controller/Frontend_Controller/…)
     resolves $branchID via application_model->get_branch_id()
     and loads $global_config, $theme_config, $cms_setting, etc.
   • If not installed, redirects to /install
   • Admin_Controller also enforces strict-subdomain isolation when on
   • Frontend_Controller also self-heals missing front_cms_setting rows
   │
   ▼
Method body → loads models → renders view
```

## Base controllers (`application/core/MY_Controller.php`)

All controllers extend one of five base classes defined in a single file:

| Base class | Used by | Purpose |
|---|---|---|
| `MY_Controller` | 11 controllers (Addons, Ajax, Cron_api, Popupbox, Saas_billing, Signup, …) | Common bootstrap — applies no-cache headers, redirects to `/install` until installed, loads `global_settings` + `theme_settings` + per-branch currency/timezone overrides, exposes `getBranchDetails()` and the `photoHandleUpload()` form helper. |
| `Admin_Controller extends MY_Controller` | 61 controllers — every back-office surface | Adds login gate, super-admin/staff role check, strict-subdomain-isolation hook (gated by `$config['strict_subdomain_isolation']`). |
| `User_Controller extends MY_Controller` | `Userrole` only | Requires student or parent login; otherwise saves intended URL + redirects to `/authentication`. |
| `Authentication_Controller extends MY_Controller` | `Authentication` only | Same as `MY_Controller` plus autoloads `authentication_model`. |
| `Frontend_Controller extends MY_Controller` | `Home`, `Admissionpayment` family | Resolves the public-site branch via `Home_model::getDefaultBranch()`, loads `front_cms_setting`; if missing on a pinned tenant host, **self-heals** by seeding default CMS settings instead of redirecting to login. |

The five-in-one layout means you can't just `grep "class Admin_Controller"` across `controllers/`; the definitions live in `core/MY_Controller.php`.

## Multi-tenant resolution

The tenant (called a **branch** in the data model) for the current request is resolved by `Application_model::get_branch_id()`:

```php
// application/models/Application_model.php (lines 13–26)
public function get_branch_id() {
    $url_branch = $this->ss_resolve_branch_from_host();
    if ($url_branch !== null) return $url_branch;        // 1) host match
    if (is_superadmin_loggedin()) return $this->input->post('branch_id');  // 2) explicit
    return get_loggedin_branch_id();                     // 3) session
}
```

Resolution order:

1. **Host header → `custom_domain` table.**
   `ss_resolve_branch_from_host()` (lines 36–75) normalises `HTTP_HOST` (lower-cases, strips `www.` and `:port`), checks the `saas` addon is active (`App_lib::isExistingAddon('saas')`) and the `custom_domain` table exists, then `SELECT school_id FROM custom_domain WHERE url = ? AND status = 1`. Result is cached per request via a `static`.
2. **Super-admin override.** If logged in as super-admin (`role_id = 1`), trust the `branch_id` POST field (so the super-admin can switch tenants from the UI).
3. **Session.** Fall back to the branch_id stored in the user's login session.

The CLI SAPI and empty `HTTP_HOST` short-circuit to `null` (super-admin CLI cron path).

## Subdomain & custom-domain routing

- Subdomains: `<slug>.smartschool.bd` is symlinked at the OS level on the production host (`ln -s /home/zgruhjabaz/smartschool.bd /home/zgruhjabaz/<sub>.smartschool.bd`) so the same code base serves every tenant. CI is reached over the same `index.php` and the `custom_domain` lookup pins the request to the tenant.
- Apex: bare `smartschool.bd` is intentionally **not** in `custom_domain`. `routes.php` checks the host explicitly:
  ```php
  $ss_host = preg_replace('/^www\./', '', strtolower((string)($_SERVER['HTTP_HOST'] ?? '')));
  $ss_host = preg_replace('/:\d+$/', '', $ss_host);
  $route['default_controller'] = ($ss_host === 'smartschool.bd') ? 'landing' : 'home';
  ```
  so apex requests render the marketing `Landing` controller, while every tenant subdomain hits `Home`.

## Addon system

A lightweight feature-flag mechanism keyed by `addon.prefix`:

```php
// application/libraries/App_lib.php
function isExistingAddon($prefix = '') {
    $row = $this->CI->db->select('id')->where('prefix', $prefix)->get('addon')->row();
    return !empty($row);
}
```

Currently referenced prefixes in code: `saas`, `qrcode`. Any DB row in `addon` with the matching prefix flips the corresponding code path on. The SaaS feature gates `custom_domain` lookups, the Saas controllers' visibility, etc. on `isExistingAddon('saas')`.

## SaaS-specific cross-cutting concerns

These pieces are layered on top of the base Ramom architecture; see [`SAAS.md`](SAAS.md) for the full map.

- **Signup → approval flow**: `Signup` controller → `saas_pending_request` row → super-admin reviews in `Saas::pending_request` → `Saas::approve()` creates a `branch` row + `custom_domain` row + `saas_subscriptions` row + `audit_log` entry.
- **Billing**: `Saas_billing` (tenant-facing pay flow), `Subscription` (per-tenant plan/usage view), `Saas_renewals_cli` (cron entrypoint) all share `Saas_renewal_runner` (the library that does the actual invoice + email work) and a `Saas_gateways/` directory of provider adapters (bKash, Nagad, Rocket, SSLCommerz, Stripe, PayKureGhor, manual).
- **Landing page**: apex marketing site reads the singleton `landing_setting` row to drive copy/colour/section flags and picks one of `variant_a.php`..`variant_e.php` under `application/views/landing/`.
- **Custom domains**: `Custom_domain` controller maintains the lookup table; visible in both the super-admin saas section (`/saas/...`) and the tenant `Subscription` UI.

## What this codebase does **not** have

To save future readers some `grep`-time:

- No Composer at the application root. `application/third_party/*` ships pre-vendored.
- No PSR-4 autoloader; CI 3's classic class-name-to-file mapping is in effect.
- No CI 3 **migrations** are present (the `application/migrations/` directory exists only to hold the protective `index.html`). Schema lives in raw SQL dumps and ad-hoc one-shot migration SQL files referenced from the strategic docs.
- No hooks registered in `application/hooks/`.
- No automated tests / fixtures of any kind.
- No `.env`. Configuration is plain PHP arrays in `application/config/*.php`.

---

*Source paths referenced: `index.php`, `.htaccess`, `system/core/CodeIgniter.php`, `application/config/{config,database,routes,autoload}.php`, `application/core/MY_Controller.php`, `application/models/Application_model.php`, `application/libraries/App_lib.php`.*
