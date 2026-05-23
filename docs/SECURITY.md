# Security notes

This document calls out items in the repository that materially affect security posture. Everything here is a **finding**, not a prescription — the recommended remediation column is the lowest-effort safe path; an operator may pick a stricter one.

> ⚠️ This is a public GitHub repository. Anything currently committed is reachable by anyone on the internet.

## Critical: secrets committed to git

| Path | What's exposed | Recommended remediation |
|---|---|---|
| `application/config/database.php` | DB hostname (`localhost` — innocuous on its own) + production cPanel DB user/password `zgruhjabaz_smartschoolbd:zgruhjabaz_smartschoolbd` + database name. Anyone who can reach the host on port 3306 (or run code on the cPanel account) can read/write the entire app DB. | 1. **Rotate** the DB password on cPanel.<br>2. Move credentials into `application/config/database_secrets.php` and gitignore it; `database.php` `require_once`s it.<br>3. Rewrite git history to expunge the old password (e.g. `git filter-repo --replace-text`). |
| `application/config/purchase_key.php` | Envato username + purchase code | Treat as a low-impact secret (it only authenticates this license to the Ramom installer's purchase-validation API). Still: do not commit. Move to a gitignored file or environment variable. |
| `application/config/config.php` (`encryption_key`) | The CI 3 encryption key (`'34656335323433333164'`) used by session encryption, the `Encrypt`/`Encryption` libs, and the `MY_Model::hash()` password salt | Rotate per-environment. Setting it identical on every install means a token/cookie issued by one installation is forgeable by anyone who reads this repo. |
| `application/config/safe3.zip` | A zipped backup of the above two files | Delete from the repo and from history. |
| `application/bupviews.zip` | A snapshot of the entire `application/views/` tree from an older revision | Delete. Use git history if you need the old code. |
| `application/views/landing/bak.zip` | Snapshot of older landing-page variants | Delete; older variants are also still in tree as `bak.variant_b.php` etc. |
| `application/views/landing/bak.variant_b.php`, `application/controllers/bak.*.php`, `application/models/bak.Saas_model.php` | Old PHP files left side-by-side with current ones. Not loaded but read by anyone. | Delete from the repo. Use git history. |
| `uploads/db_backup/DB-backup_2025-01-11_23-10.zip` | A 163 KB production database backup committed verbatim. Likely contains personally-identifiable student/parent/staff data. | **Highest priority.** Delete from HEAD **and** rewrite history (`git filter-repo --path uploads/db_backup --invert-paths`). Verify no other DB dumps are anywhere else in the tree. Notify any data-subjects per local data-protection law if PII may have been exposed during the time the repo was public. |

## Important: hardening

| Setting | Current | Recommended |
|---|---|---|
| `application/config/config.php`<br>`$config['cookie_secure']` | `FALSE` | `TRUE` once production is fully on HTTPS. Prevents session cookie from being sent over plain HTTP. |
| `application/config/config.php`<br>`$config['cookie_httponly']` | `FALSE` | `TRUE`. Prevents JS-readable session cookie — protects against session theft via XSS. |
| `application/config/config.php`<br>`$config['sess_regenerate_destroy']` | `FALSE` | Consider `TRUE` so the old session row is dropped on regenerate (defence-in-depth against session fixation). |
| `application/config/config.php`<br>`$config['sess_match_ip']` | `FALSE` | `TRUE` if your users don't change IPs (mobile network roaming will log them out). Trade-off — keep `FALSE` if you accept the risk. |
| `application/config/config.php`<br>`$config['csrf_protection']` | `TRUE` | OK. Note the inline carve-outs for `/feespayment/`, `/admissionpayment/`, `/onlineexam_payment/`, `/subscription/`, `/saas_payment/` — these accept POSTs from external IPN gateways and must NOT enforce CSRF. Audit they each verify the gateway signature on the payload. |
| `$config['global_xss_filtering']` | `TRUE` | OK. (CI 3's filter is regex-based and not exhaustive — output-side escaping is still the correct primary defence.) |
| `application/controllers/Install.php` | Self-disables once `$config['installed'] = TRUE` | OK. Verify the installer route is not reachable in production by hitting `https://smartschool.bd/install` — it should redirect to `/authentication`. |

## SQL injection surface

CodeIgniter 3 Query Builder (`$this->db->where(...)->get(...)`) auto-escapes by default and is used throughout this codebase. However, **a handful of older callers use raw SQL with string concatenation** — for example `application/helpers/general_helper.php::translate()` builds a query as:

```php
$sql = "SELECT `english`,`" . $set_lang . "` FROM `languages` WHERE `word` = '$word'";
$query = $CI->db->query($sql);
```

`$set_lang` comes from `set_global_setting('translation')` or session userdata — operator-controlled, but still ideally bound. `$word` comes from controller calls (constants) — not user input today, but a future caller may pass user input here unaware that the helper concatenates.

Recommendation: replace such calls with `$this->db->query($sql, [params])` or refactor to use the query builder. A linter check (`grep -nE "\$_(POST|GET).*\->query\(" application/`) should be added to CI.

## Authentication / session model

- Passwords are hashed with `hash("sha512", $password . config_item("encryption_key"))` (see `application/core/MY_Model.php::hash()`). This is **not** a recommended password hash — sha512 is fast and there is no per-user salt, only the global `encryption_key`. An attacker who gains DB read access can run efficient offline brute-force attacks against weak passwords.
  - **Recommended:** migrate to `password_hash($password, PASSWORD_DEFAULT)` and `password_verify(...)`. Migration plan: on next successful login, if the stored hash is sha512-format, re-hash with bcrypt and update the row. Keep the legacy verifier behind a feature flag until all users have rotated.
- Session driver is `database` and session id is keyed by `(id, ip_address)`. With `sess_match_ip = FALSE`, the id alone is enough to resume — see hardening table above.

## Multi-tenant isolation

- Tenant isolation is **logical**, not physical: every tenant lives in the same DB and same tables; tenancy is enforced by a `branch_id` WHERE clause on every query. A missing `branch_id` filter on any controller method is a tenant-data-leak bug.
  - The strategic doc P4.4 calls for an automated audit; until that ships, treat every new controller method as needing a manual reviewer check that it scopes by `application_model->get_branch_id()`.
- `$config['strict_subdomain_isolation']` is currently `FALSE`. When `TRUE`, `Admin_Controller` will refuse a request when the host's branch ≠ the session's branch — this is the belt-and-braces backstop. The roadmap (P1-T8 follow-up) is to flip it to TRUE. Do that before the platform onboards meaningful numbers of paying tenants.

## File-upload surface

- `MY_Controller::photoHandleUpload()` validates by file extension and size, not by MIME type sniffing. An attacker can rename `evil.phtml` to `evil.jpg` if the destination directory is PHP-executable.
- `uploads/` is served by the web server with `mod_rewrite` enabled (because `.htaccess` only rewrites *non*-existing files). **There is no `uploads/.htaccess` to forbid `.php` execution inside the uploads tree.** Drop one in if not already present on the live host:

  ```
  # uploads/.htaccess
  <FilesMatch "\.(?i:php|phtml|php3|php4|php5|php7|phar|inc)$">
      Require all denied
  </FilesMatch>
  AddType text/plain .php .phtml .php3 .php4 .php5 .php7 .phar .inc
  Options -ExecCGI
  ```

  Verify on Litespeed equivalents (httpd.conf or per-vhost rules) since `.htaccess` syntax may differ.

## Addon installer

`application/controllers/Addons.php` + `application/helpers/unzip_helper.php` will unpack arbitrary uploaded zips into `uploads/addons/<hash>/` and then `require` files from there. A malicious addon zip = full RCE.

- Gate this controller to super-admin only (it already is) and document that only the super-admin should ever install addons.
- Consider verifying addon zips against a server-side allowlist of SHA256 sums before unpack.

## Backup / database-dump endpoint

`application/controllers/Backup.php` produces a mysqldump of the configured DB. Ensure access is gated to super-admin only — a misconfigured role permission here would leak the entire DB. Audit `application/views/database_backup/*` for the visibility check.

## What is NOT a problem

A few things that look scary but aren't:

- `$config['composer_autoload'] = FALSE` is intentional — Composer isn't used. The vendored libs are loaded explicitly.
- Plaintext `'password' => 'zgruhjabaz_smartschoolbd'` looks like a default placeholder but is the **actual production password** — see Critical table above.
- The `bak.*.php` files are not executed; only the matching non-`bak` files are routed. They are a hygiene issue, not an immediate code-exec risk.

---

*Source: full scan of `application/config/`, `application/core/`, `application/controllers/{Install,Backup,Addons}.php`, `application/helpers/general_helper.php`, root `.htaccess`, `uploads/` listing.*
